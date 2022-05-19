<?php

namespace App\Modals;

use App\Modals\HKUser as User;
use App\Modals\HKModal as Modal;
use App\Modals\HKRegion as Region;
use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
//
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKRegion extends Modal {
    const DB_TABLE = "region_master";
    const DB_TABLE_ALIAS = "REG_M";
    // 
    public $id;
    public $name;
   
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function __construct($value = NULL) {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Region::DB_ID);
        $this->name = UTS::string_from($value, Region::DB_NAME);
        //
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));
    }

    public static function Init($value)
    {
        if ($value) {
            return new Region($value);
        }
    }

    #region Listing & List Counts

    public static function db_list($type, $filter)
    {
        $conditions = [];
        $parameters = Region::db_parameters(Region::DB_TABLE_ALIAS, $filter);
        #filters for regions and sports
        if(isset($filter['regions'])){
            
            $conditions=[Region::DB_TABLE_ALIAS . "." . Region::DB_REGION . "='".$filter['regions']."'"];
        }
        if(isset($filter['sports'])){
            $conditions=[Region::DB_TABLE_ALIAS . "." . Region::DB_NAME . "='".$filter['sports']."'"];
        }
        //
        $sort = Sort::sort_from($filter);
        if ($sort->type == "alphabetical") $sort->type = Region::DB_TABLE_ALIAS . "." . Region::DB_NAME;
        else if ($sort->type == "created") $sort->type = Region::DB_TABLE_ALIAS . "." . Region::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = Region::DB_TABLE_ALIAS . "." . Region::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, Region::db_search($filter));
        $result = Region::db_select($parameters, $conditions, $sort, $page);
        if ($result && count($result) > 0) {
            return $result;
        }
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = User::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[Region::DB_ID] = (($alias) ? $alias . "." : "") . Region::DB_ID;
        $parameters[Region::DB_NAME] = (($alias) ? $alias . "." : "") . Region::DB_NAME;
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Modal::DB_CREATED_ON, Modal::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Modal::DB_CREATED_BY, Modal::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
        }
            
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([Region::DB_TABLE_ALIAS . '.name'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Region::DB_TABLE, Region::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Region::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
    #endregion
}