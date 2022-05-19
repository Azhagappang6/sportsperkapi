<?php

namespace App\Modals;

use App\Modals\HKModal as Model;

class HKModal
{
    const DB_ID = "id";
    const DB_HSN = "hsn";
    const DB_GSTN = "gstn";
    const DB_NAME = "name";
    const DB_EMAIL = "email";
    const DB_IMAGE = "image";
    const DB_PHONE = "phone";
    const DB_MOBILE = "mobile";
    const DB_PASSWORD = "password";
    const DB_TYPE = "type";
    const DB_REGION = "region";
    const DB_SPORTS = "sports";
    const DB_PARENT = "parent";
    const DB_BARCODE = "barcode";
    const DB_GST_TYPE = "gst_type";
    const DB_GST_RATE = "gst_rate";
    const DB_ADDRESS = "address";
    const DB_CATEGORY = "category";
    const DB_LASTNAME = "lastname";
    const DB_FIRSTNAME = "firstname";
    const DB_DESCRIPTION = "description";
    // 
    const DB_CREATED_BY = "created_by";
    const DB_CREATED_ON = "created_on";
    const DB_MODIFIED_BY = "modified_by";
    const DB_MODIFIED_ON = "modified_on";

    public function object($keys)
    {
        $object = [];
        $this_object = (array)$this;
        foreach ($keys as $key) {
            if (isset($this_object[$key])) {
                $object[$key] = $this_object[$key];
            }
        }
        return $object;
    }

    function __construct(&$value = NULL)
    {
        if ($value && gettype($value) == 'string') {
            $value = json_decode($value, TRUE);
        }
    }

    public static function Init($value)
    {
        if ($value) {
            return new Model($value);
        }
    }

    #region
    public static function class()
    {
        return get_called_class();
    }
    #endregion
}