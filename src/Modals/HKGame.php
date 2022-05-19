<?php

namespace App\Modals;

use App\Modals\HKUser as User;
use App\Modals\HKGame as Game;
use App\Modals\HKTeam as Team;
use App\Modals\HKModal as Modal;
use App\Modals\HKAttendance as Attendance;
use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;

//
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKGame extends Modal {
    const DB_TABLE = "team_master";
    const DB_TABLE_ALIAS = "GAM_M";
    // 
    public $id;
    public $name;
    public $sport;
    public $region;
    public $year;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function __construct($value = NULL) {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Game::DB_ID);
        $this->name = UTS::string_from($value, Game::DB_NAME);
        $this->sport = UTS::string_from($value, Game::DB_SPORT);
		$this->region = UTS::string_from($value, Game::DB_REGION);
        $this->year = UTS::string_from($value, Game::DB_YEAR);
        //
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));
    }

    public static function Init($value)
    {
        if ($value) {
            return new Game($value);
        }
    }

    #region CRUD Operations
    public function db_create()
    {
        $key_values = $this->object([Team::DB_NAME]);
        $key_values = array_merge($key_values, [Modal::DB_CREATED_BY => CTS::$user_id]);
        $this->id = DBS::execute_insert(Team::DB_TABLE, $key_values, true);
        if ($this->id) {
            return $this->id;
        }
    }

    public function db_read($filter = NULL)
    {
        $parameters = Team::db_parameters(Team::DB_TABLE_ALIAS, $filter);
        $conditions = [Team::DB_TABLE_ALIAS . "." . Team::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(Team::DB_TABLE, Team::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Team::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }

    public function db_update()
    {
        $conditions = $this->object([Team::DB_ID]);
        $key_values = $this->object([Team::DB_NAME]);
        $key_values = array_merge($key_values, [Modal::DB_MODIFIED_BY => CTS::$user_id]);
        if (DBS::execute_update(Team::DB_TABLE, $key_values, $conditions) == 1) {
            return true;
        }
    }

    public function db_delete()
    {
        $conditions = $this->object([Team::DB_ID]);
        if (DBS::execute_delete(Team::DB_TABLE, $conditions) == 1) {
            return true;
        }
    }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
        $conditions = [];
        $parameters = Game::db_parameters(Game::DB_TABLE_ALIAS, $filter);
        
        $sort = Sort::sort_from($filter);
        if ($sort->type == "alphabetical") $sort->type = Game::DB_TABLE_ALIAS . "." . Game::DB_NAME;
        else if ($sort->type == "created") $sort->type = Game::DB_TABLE_ALIAS . "." . Game::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = Game::DB_TABLE_ALIAS . "." . Game::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, Game::db_search($filter));
        $result = Game::db_select($parameters, $conditions, $sort, $page);
        if ($result && count($result) > 0) {
            $new_res = array();
            foreach($result as $val)
            {
                if (!in_array($val->year, $new_res)) {
                    $res2[] = array("id" => $val->id, "year" => $val->year);
                    $new_res[] = $val->year;
                }
            }
            // echo json_encode($result);exit;
            return $res2;
        }
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = Game::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[Game::DB_ID] = (($alias) ? $alias . "." : "") . Game::DB_ID;
        // $parameters[Game::DB_NAME] = (($alias) ? $alias . "." : "") . Game::DB_NAME;
        //$parameters[Game::DB_SPORT] = (($alias) ? $alias . "." : "") . Game::DB_SPORT;
		//$parameters[Game::DB_REGION] = (($alias) ? $alias . "." : "") . Game::DB_REGION;
        $parameters[Game::DB_YEAR] = (($alias) ? $alias . "." : "") . Game::DB_YEAR;
		
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Modal::DB_DESCRIPTION, Modal::DB_REGION])) $parameters[$value] = (($alias) ? $alias . "." : "") . $value;
            else if (in_array($value, [Modal::DB_CREATED_ON, Modal::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Modal::DB_CREATED_BY, Modal::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
        }
            
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([Game::DB_TABLE_ALIAS . '.name'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Game::DB_TABLE, Game::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Game::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
    #endregion
}