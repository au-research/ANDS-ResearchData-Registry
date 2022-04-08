<?php

namespace ANDS\Registry\Providers\RIFCS;

use ANDS\Registry\Providers\RIFCSProvider;
use ANDS\RegistryObject;
use ANDS\Util\SpatialUtil;
use ANDS\Util\XMLUtil;

class SpatialProvider implements RIFCSProvider
{

    public static function process(RegistryObject $record)
    {
        // TODO: Implement process() method.
    }

    public static function get(RegistryObject $record)
    {
        return [
            'coverages' => self::getLocationAsLonLats($record)
        ];
    }

    /**
     * Provides an associated array ready to be indexed
     *
     * @param RegistryObject $record
     * @return array
     */
    public static function getIndexableArray(RegistryObject $record)
    {
        $index = [];
        $sumOfAllAreas = 0;

        $spatialLocations = self::getLocationAsLonLats($record);

        foreach ($spatialLocations as $lonLat) {
            $extents = SpatialUtil::calcExtent($lonLat);
            if ($extents['west'] + $extents['east'] < 5 && $extents['east'] > 175) {
                //need to insert zero bypass
                $lonLatPolygonFixed = SpatialUtil::insertZeroBypassCoords($lonLat, $extents['west'], $extents['east']);
            } else {
                $lonLatPolygonFixed = $lonLat;
            }
            $index['spatial_coverage_polygons'][] = $lonLatPolygonFixed;
            $index['spatial_coverage_extents'][] = $extents['extent'];

            $points = explode(' ', $lonLat);
            foreach ($points as $key => &$point) {
                $point = implode(' ', explode(',', $point));
                if (trim($point) == "") {
                    unset($points[$key]);
                }
            }

            //make it smaller if it's too big
            foreach ($points as &$point) {
                $predicate = explode(' ', $point);
                foreach ($predicate as &$pred) {
                    if ((float)$pred >= 179) {
                        $pred = 178;
                    } elseif ((float)$pred <= -179) {
                        $pred = -178;
                    } elseif ((float)$pred == 90) {
                        $pred = 86;
                    } elseif ((float)$pred == -90) {
                        $pred = -86;
                    } else {
                        $pred = round($pred, 5);
                    }
                }
                if (isset($predicate[1]) && (float)$predicate[1] > 90) {
                    $predicate[1] = 86;
                }
                $point = implode(' ', $predicate);
            }

            // Fix straight line, if all Lat or all Lons are the same
            $uniqueLonLat = array('lat' => [], 'lon' => []);
            foreach ($points as &$point) {
                $predicate = explode(' ', $point);
                $uniqueLonLat['lat'][] = $predicate[0] ? $predicate[0] : '';
                $uniqueLonLat['lon'][] = $predicate[1] ? $predicate[1] : '';
            }
            $uniqueLonLat['lat'] = array_unique($uniqueLonLat['lat']);
            $uniqueLonLat['lon'] = array_unique($uniqueLonLat['lon']);

            //Simplify to a straight line
            if (sizeof($uniqueLonLat['lon']) == 1) {
                sort($uniqueLonLat['lat'], SORT_NUMERIC);
                $points = array(
                    $uniqueLonLat['lat'][0] . ' ' . $uniqueLonLat['lon'][0],
                    end($uniqueLonLat['lat']) . ' ' . $uniqueLonLat['lon'][0]
                );
            } elseif (sizeof($uniqueLonLat['lat']) == 1) {
                sort($uniqueLonLat['lon'], SORT_NUMERIC);
                $points = array(
                    $uniqueLonLat['lat'][0] . ' ' . $uniqueLonLat['lon'][0],
                    $uniqueLonLat['lat'][0] . ' ' . end($uniqueLonLat['lon'])
                );
            }

            //final check of points, make sure they have value
            foreach ($points as $key => &$point) {
                $predicate = explode(' ', $point);

                if (!isset($predicate[0])
                    || !isset($predicate[1])
                    || trim($predicate[0]) == ''
                    || trim($predicate[1]) == ''
                ) {
                    unset($points[$key]);
                }
            }

            $uniquePoints = array_unique($points);

            if (sizeof($points) > 0) {
                $points = array_values($points);
                if (sizeof($uniquePoints) < 2) {
                    $index['spatial_coverage_extents_wkt'][] = 'POINT(' . implode(', ', $uniquePoints) . ')';
                } else if (sizeof($uniquePoints) < 3) {

                    $index['spatial_coverage_extents_wkt'][] = 'LINESTRING(' . implode(', ', $uniquePoints) . ')';
                } else if (sizeof($points) > 2 && sizeof($uniquePoints) != 3) {

                    //fix last point
                    if ($points[0] != end($points)) {
                        $index['spatial_coverage_extents_wkt'][] = 'LINESTRING(' . implode(', ', $points) . ')';
                    } else if (!SpatialUtil::isSelfIntersectPolygon($points)) {
                        foreach ($points as &$point) {
                            $point = (is_array($point)) ? implode(' ', $point) : $point;
                        }
                        $index['spatial_coverage_extents_wkt'][] = 'POLYGON((' . implode(', ', $points) . '))';
                    } else if (!SpatialUtil::isSelfIntersectPolygon($uniquePoints)) {
                        foreach ($uniquePoints as &$point) {
                            $point = (is_array($point)) ? implode(' ', $point) : $point;
                        }

                        //putting end point back
                        $uniquePoints = array_values($uniquePoints);
                        $uniquePoints[] = $uniquePoints[0];
                        $index['spatial_coverage_extents_wkt'][] = 'POLYGON((' . implode(', ', $uniquePoints) . '))';
                    }

                } else if (sizeof($points) < 2) {
                    $index['spatial_coverage_extents_wkt'][] = 'POINT(' . implode(', ', $points) . ')';
                }
            }

            $sumOfAllAreas += $extents['area'];
            $index['spatial_coverage_centres'][] = $extents['center'];
        }
        $index['spatial_coverage_area_sum'] = $sumOfAllAreas;
        return $index;
    }

    public static function getLocationAsLonLats(RegistryObject $record)
    {
        $xml = $record->getCurrentData()->data;
        $sxml = XMLUtil::getSimpleXMLFromString($xml);
        $spatial_elts = $sxml->xpath('//ro:spatial');
        $coords = [];
        foreach ($spatial_elts as $spatial) {

            $type = (string)trim($spatial["type"], $mask = " \t\n\r\0\x0B\xE2\x80\x8B");
            $value = preg_replace('!\s+!', ' ', (string)$spatial);

            if (SpatialUtil::isValidKmlPolyCoords($value) && ($type == 'kmlPolyCoords' || $type == 'gmlKmlPolyCoords')) {
                $coords[] = $value;
            } elseif ($type == 'iso19139dcmiBox') {
                $tok = strtok($value, ";");
                $north = null;
                $south = null;
                $west = null;
                $east = null;
                while ($tok !== false) {
                    $keyValue = explode("=", $tok);
                    if (strtolower(trim($keyValue[0])) == 'northlimit' && is_numeric($keyValue[1])) {
                        $north = floatval($keyValue[1]);
                    }
                    if (strtolower(trim($keyValue[0])) == 'southlimit' && is_numeric($keyValue[1])) {
                        $south = floatval($keyValue[1]);
                    }
                    if (strtolower(trim($keyValue[0])) == 'westlimit' && is_numeric($keyValue[1])) {
                        $west = floatval($keyValue[1]);
                    }
                    if (strtolower(trim($keyValue[0])) == 'eastlimit' && is_numeric($keyValue[1])) {
                        $east = floatval($keyValue[1]);
                    }
                    $tok = strtok(";");
                }

                if (SpatialUtil::isValidWSEN($west, $south, $east, $north)) {
                    if ($north == $south && $east == $west) {
                        $coords[] = $east . "," . $north;
                    } else {
                        $coords[] = $east . "," . $north . " " . $east . "," . $south . " " . $west . "," . $south . " " . $west . "," . $north . " " . $east . "," . $north;
                    }
                }
            } elseif ($type == 'iso19139dcmiPoint' || $type == 'dcmiPoint') {
                //"name=Tasman Sea, AU; east=160.0; north=-40.0"

                $tok = strtok($value, ";");
                $north = null;
                $south = null;
                $west = null;
                $east = null;
                while ($tok !== false) {
                    $keyValue = explode("=", $tok);
                    if (strtolower(trim($keyValue[0])) == 'north' && is_numeric($keyValue[1])) {
                        $north = floatval($keyValue[1]);
                    }
                    if (strtolower(trim($keyValue[0])) == 'east' && is_numeric($keyValue[1])) {
                        $east = floatval($keyValue[1]);
                    }
                    $tok = strtok(";");
                }
                if (SpatialUtil::isValidWSEN($east, $north, $east, $north)) {
                    $coords[] = $east . "," . $north;
                }
            } elseif ($type == 'iso31661' || $type == 'iso31662' || $type == 'iso3166') //"name=Tasman Sea, AU; east=160.0; north=-40.0"
            {

                $north = 90;
                $south = -90;
                $west = 180;
                $east = -180;

                $gCoords = SpatialUtil::getExtentFromGoogle(trim($value));

                if ($gCoords) {
                    $north = floatval($gCoords['north']);
                    $south = floatval($gCoords['south']);
                    $west = floatval($gCoords['west']);
                    $east = floatval($gCoords['east']);

                    if ($north == $south && $east == $west) {
                        $coords[] = $east . "," . $north;
                    } else {
                        $coords[] = $east . "," . $north . " " . $east . "," . $south . " " . $west . "," . $south . " " . $west . "," . $north . " " . $east . "," . $north;
                    }
                }
            }
        }
        return $coords;
    }

}