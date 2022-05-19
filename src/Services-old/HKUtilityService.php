<?php

namespace App\Services;

class HKUtilityService
{
    #region
    public static function begins_with($str, $sub)
    {
        if ($str && is_string($str) && is_string($sub)) {
            return substr($str, 0, strlen($sub) == $sub);
        }
        return false;
    }
    public static function ends_with($str, $sub)
    {
        if ($str && is_string($str) && is_string($sub)) {
            return (substr($str, strlen($str) - strlen($sub)) == $sub);
        }
        return false;
    }
    #endregion

    #region
    public static function value_from($array, $key, $default = NULL)
    {
        return (is_array($array) && isset($array[$key])) ? $array[$key] : $default;
    }

    public static function number_from($array, $key, $default = NULL)
    {
        return (is_array($array) && isset($array[$key]) && is_numeric($array[$key])) ? +$array[$key] : $default;
    }

    public static function string_from($array, $key, $default = NULL)
    {
        return (is_array($array) && isset($array[$key]) && is_string($array[$key])) ? $array[$key] : $default;
    }

    public static function array_from($array, $key, $default = NULL)
    {
        return (is_array($array) && isset($array[$key]) && is_array($array[$key])) ? $array[$key] : $default;
    }
    #endregion

    #region
    public static function query_params($db_parameters, $is_array = FALSE)
    {
        $parameters = array();
        if (is_array($db_parameters)) {
            foreach ($db_parameters as $key => $value) {
                if ($is_array) array_push($parameters, "'" . $key . "'", $value);
                else array_push($parameters, $value . " AS " . $key);
            }
        }
        return implode(",", $parameters);
    }

    public static function serializeObjects($array, $type = NULL)
    {
        if ($type) {
            $results = array();
            foreach ($array as $key => $object) {
                array_push($results, new $type($object));
            }
            return $results;
        }
        return $array;
    }
    #endregion
}
