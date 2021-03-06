<?php
/**
 * Created by PhpStorm.
 * User: leomonus
 * Date: 23/9/19
 * Time: 2:14 PM
 */

namespace ANDS\Registry\ContentProvider\JSONLD;


class IdentifierProvider
{

    public static function process($json)
    {


    }

    public static function getIdentifier($json){
        $identifiers = [];
        if(isset($json->identifier->value))
            $identifiers[] = (string) $json->identifier->value;
        elseif(isset($json->identifier))
            $identifiers[] = $json->identifier;
        elseif(isset($json->url))
            $identifiers[] = $json->url;
        elseif(isset($json->id))
            $identifiers[] = $json->id;
        elseif(isset($json->{'@id'}))
            $identifiers[] = $json->{'@id'};
        return $identifiers;
    }

}