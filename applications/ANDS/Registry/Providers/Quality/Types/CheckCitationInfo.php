<?php


namespace ANDS\Registry\Providers\Quality\Types;


class CheckCitationInfo extends CheckType
{
    public static $name = 'citationInfo';

    /**
     * Returns the status of the check
     *
     * @return boolean
     */
    public function check()
    {
        return count($this->simpleXML->xpath('//ro:citationInfo')) > 0;
    }
}