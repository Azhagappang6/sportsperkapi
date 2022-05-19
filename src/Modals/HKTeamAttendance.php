<?php

namespace App\Modals;

use App\Modals\HKUser as User;
use App\Modals\HKModal as Modal;
use App\Modals\HKTeam as Team;
use App\Modals\HKTeamAttendance as TeamAttendance;
use App\Modals\HKUserAttendance as UserAttendance;
use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
//
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKTeamAttendance extends Modal {
    const DB_TABLE = "team_attendance";
    const DB_TABLE_ALIAS = "TEA_A";
    // 
    public $id;
    public $team_id;
    public $coach_id;
    public $data;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function __construct($value = NULL) {
        parent::__construct($value);
        $this->id = UTS::number_from($value, TeamAttendance::DB_ID);
        $this->team_id = UTS::string_from($value, TeamAttendance::DB_TEAMID);
        $this->coach_id = UTS::string_from($value, TeamAttendance::DB_COACH);
        $this->data = UTS::array_from($value, TeamAttendance::DB_DATA);
        //
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));
    }

    public static function Init($value)
    {
        if ($value) {
            return new TeamAttendance($value);
        }
    }

    #region CRUD Operations
    public function db_create()
    {
        $keys = [];
        $values = [];
        $key_values = $this->object([TeamAttendance::DB_TEAMID,TeamAttendance::DB_COACH]);
        $key_values = array_merge($key_values, [Modal::DB_CREATED_BY => CTS::$user_id]);
        $data=$this->object([TeamAttendance::DB_DATA]);
        $data_json=json_encode ($data);
        $key_values = array_merge($key_values, [TeamAttendance::DB_DATA => $data_json]);
        $this->id = DBS::execute_insert(TeamAttendance::DB_TABLE, $key_values, true);
       if ($this->id) {

            return $this->id;

        }
    }

    public function db_read($filter = NULL)
    {
        $parameters = TeamAttendance::db_parameters(TeamAttendance::DB_TABLE_ALIAS, $filter);
        $conditions = [TeamAttendance::DB_TABLE_ALIAS . "." . TeamAttendance::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(TeamAttendance::DB_TABLE, TeamAttendance::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), TeamAttendance::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }

    public function db_update()
    {
        $conditions = $this->object([TeamAttendance::DB_ID]);
        $key_values = $this->object([TeamAttendance::DB_TEAMID]);
        $key_values = array_merge($key_values, [Modal::DB_MODIFIED_BY => CTS::$user_id]);
        if (DBS::execute_update(TeamAttendance::DB_TABLE, $key_values, $conditions) == 1) {
            return true;
        }
    }

    public function db_delete()
    {
        $conditions = $this->object([TeamAttendance::DB_ID]);
        if (DBS::execute_delete(TeamAttendance::DB_TABLE, $conditions) == 1) {
            return true;
        }
    }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
        $conditions = [];
        $parameters = TeamAttendance::db_parameters(TeamAttendance::DB_TABLE_ALIAS, $filter);
        #filters for regions and sports
        //
        $sort = Sort::sort_from($filter);
        if ($sort->type == "created") $sort->type = TeamAttendance::DB_TABLE_ALIAS . "." . TeamAttendance::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = TeamAttendance::DB_TABLE_ALIAS . "." . TeamAttendance::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, TeamAttendance::db_search($filter));
        $result = TeamAttendance::db_select($parameters, $conditions, $sort, $page);
        if ($result && count($result) > 0) {
            return $result;
        }
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = TeamAttendance::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[TeamAttendance::DB_ID] = (($alias) ? $alias . "." : "") . TeamAttendance::DB_ID;
        $parameters[TeamAttendance::DB_TEAMID] = (($alias) ? $alias . "." : "") . TeamAttendance::DB_TEAMID;
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
            foreach ([TeamAttendance::DB_TABLE_ALIAS . '.name'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(TeamAttendance::DB_TABLE, TeamAttendance::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, TeamAttendance::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
    #endregion
}