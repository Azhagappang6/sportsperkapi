<?php

namespace App\Modals;

use App\Modals\HKUser as User;
use App\Modals\HKModal as Modal;
use App\Modals\HKTeam as Team;
use App\Modals\HKAttendance as Attendance;
use App\Modals\HKUserAttendance as UserAttendance;
use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
//
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKUserAttendance extends Modal {
    const DB_TABLE = "user_attendance";
    const DB_TABLE_ALIAS = "USR_ATD";
    // 
    public $id;
    public $player_id;
    public $status;
    public $practice_date;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function __construct($value = NULL) {
        parent::__construct($value);
        $this->id = UTS::number_from($value, UserAttendance::DB_ID);
        $this->player_id = UTS::string_from($value, UserAttendance::DB_PLAYER);
        $this->status = UTS::string_from($value, UserAttendance::DB_STATUS);
        $this->practice_date = UTS::string_from($value, UserAttendance::DB_PDATE);
        //
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));
    }

    public static function Init($value)
    {
        if ($value) {
            return new Team($value);
        }
    }

    #region CRUD Operations
    public function db_create()
    {
        $key_values = $this->object([UserAttendance::DB_TEAMID,UserAttendance::DB_USERID]);
        $key_values = array_merge($key_values, [Modal::DB_CREATED_BY => CTS::$user_id]);
        $this->id = DBS::execute_insert(UserAttendance::DB_TABLE, $key_values, true);
        if ($this->id) {
            return $this->id;
        }
    }

    public function db_read($filter = NULL)
    {
        $parameters = UserAttendance::db_parameters(UserAttendance::DB_TABLE_ALIAS, $filter);
        $conditions = [UserAttendance::DB_TABLE_ALIAS . "." . UserAttendance::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(UserAttendance::DB_TABLE, UserAttendance::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), UserAttendance::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }

    public function db_update()
    {
        $conditions = $this->object([UserAttendance::DB_ID]);
        $key_values = $this->object([UserAttendance::DB_TEAMID]);
        $key_values = array_merge($key_values, [Modal::DB_MODIFIED_BY => CTS::$user_id]);
        if (DBS::execute_update(UserAttendance::DB_TABLE, $key_values, $conditions) == 1) {
            return true;
        }
    }

    public function db_delete()
    {
        $conditions = $this->object([UserAttendance::DB_ID]);
        if (DBS::execute_delete(UserAttendance::DB_TABLE, $conditions) == 1) {
            return true;
        }
    }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
        $conditions = [];
        $parameters = UserAttendance::db_parameters(UserAttendance::DB_TABLE_ALIAS, $filter);
        #filters for regions and sports
        //
        $sort = Sort::sort_from($filter);
        if ($sort->type == "created") $sort->type = UserAttendance::DB_TABLE_ALIAS . "." . UserAttendance::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = UserAttendance::DB_TABLE_ALIAS . "." . UserAttendance::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, UserAttendance::db_search($filter));
        $result = UserAttendance::db_select($parameters, $conditions, $sort, $page);
        if ($result && count($result) > 0) {
            return $result;
        }
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = UserAttendance::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[UserAttendance::DB_ID] = (($alias) ? $alias . "." : "") . UserAttendance::DB_ID;
        $parameters[UserAttendance::DB_TEAMID] = (($alias) ? $alias . "." : "") . UserAttendance::DB_TEAMID;
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
            foreach ([UserAttendance::DB_TABLE_ALIAS . '.name'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(UserAttendance::DB_TABLE, UserAttendance::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, UserAttendance::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
    #endregion
}