<?php

namespace App\Modals\Helpers;

use App\Modals\Helpers\HKPage as Page;
use App\Modals\HKModal as Model;
use App\Services\HKUtilityService as UTS;

const PAGE_LIMIT_0_1 = ["number" => 0, "size" => 1];

class HKPage extends Model
{
    public $size;
    public $number;
    public $timestamp;

    function __construct($value = NULL)
    {
        parent::__construct($value);
        $this->size = UTS::number_from($value, 'size', 0);
        $this->number = UTS::number_from($value, 'number', 0);
        $this->timestamp = UTS::number_from($value, 'timestamp', 0);
    }

    public static function Init($value)
    {
        return new Page($value);
    }

    public function db_limit()
    {
        if ($this->size > 0) {
            $start = $this->number * $this->size;
            return " LIMIT " . $start . "," . $this->size;
        }
        return "";
    }

    #region
    public static function page_from($array, $key = 'page')
    {
        if (isset($array[$key])) {
            return new Page($array[$key]);
        }
        return new Page(array());
    }

    public static function page0_1()
    {
        return new Page(PAGE_LIMIT_0_1);
    }

    #endregion
}
