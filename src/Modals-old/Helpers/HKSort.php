<?php

namespace App\Modals\Helpers;

use App\Modals\Helpers\HKSort as Sort;
use App\Modals\HKModal as Model;
use App\Services\HKUtilityService as UTS;

class HKSort extends Model
{
    public $type;
    public $order;

    function __construct($value = NULL)
    {
        parent::__construct($value);
        $this->type = UTS::string_from($value, 'type');
        $this->order = UTS::string_from($value, 'order');
    }

    public static function Init($value)
    {
        return new Sort($value);
    }

    public function order_by()
    {
        if ($this->type) {
            return " ORDER BY " . $this->type . " " . strtoupper($this->order);
        }
        return "";
    }

    #region
    public static function sort_from($array, $key = 'sort')
    {
        if (isset($array[$key])) {
            return new Sort($array[$key]);
        }
        return new Sort(array());
    }
}
