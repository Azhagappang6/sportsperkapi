<?php

namespace App\Modals;

use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
use App\Modals\HKModal as Modal;
use App\Modals\HKUser as User;
use App\Modals\HKPlayer as Player;
use App\Modals\HKTeam as Team;
// 
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKPlayer extends Modal 
{
    const DB_TABLE = "player_master";
    const DB_TABLE_ALIAS = "PLY_M";
    
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $mobile;
    public $password;
    public $type;
    public $status;
	public $team_id;
	public $team_name;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;
    //
    public $attendance;

    function __construct($value = NULL)
    {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Player::DB_ID);
        $this->firstname = UTS::string_from($value, Player::DB_FIRSTNAME);
        $this->lastname = UTS::string_from($value, Player::DB_LASTNAME);
        $this->email = UTS::string_from($value, Player::DB_EMAIL);
        $this->mobile = UTS::string_from($value, Player::DB_MOBILE);
        $this->type = UTS::string_from($value, Player::DB_TYPE);
        $this->password = UTS::string_from($value, Player::DB_PASSWORD);
		$this->status = UTS::number_from($value, Player::DB_STATUS);
		$this->team_id = UTS::number_from($value, Player::DB_TEAMID);
		$this->team_name = UTS::string_from($value, Team::DB_NAME);
        // print_r("***");
        // print_r($this->mobile);
        // 
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));
        //
        $this->attendance = UTS::number_from($value, Modal::DB_ATTENDANCE);
    }

    public static function Init($value)
    {
        if ($value) {
            return new Player($value);
        }
    }

    #region authenticate Operations

    public function db_authenticate($filter = NULL)
    {
        $parameters = Player::db_parameters(Player::DB_TABLE_ALIAS, $filter);
        $conditions = [Player::DB_TABLE_ALIAS . "." . Player::DB_EMAIL . "='" . $this->email . "'",
                        Player::DB_TABLE_ALIAS . "." . Player::DB_PASSWORD . "='" . $this->password . "'"];
        $result = DBS::execute_select(Player::DB_TABLE, Player::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Player::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    
    }

    #end of authenticate region 
    
    #region CRUD Operations

    public function db_create()
    {
        $key_values = $this->object([Player::DB_FIRSTNAME, Player::DB_LASTNAME, Player::DB_EMAIL, Player::DB_MOBILE,Player::DB_TYPE,Player::DB_PASSWORD]);
        $key_values = array_merge($key_values, [Player::DB_CREATED_BY => CTS::$user_id]);
        $this->id = DBS::execute_insert(Player::DB_TABLE, $key_values, true);
        if ($this->id) {
            return $this->id;
        }
        
    }
    public function db_read($filter = NULL)
    {
        $parameters = Player::db_parameters(Player::DB_TABLE_ALIAS, $filter);
        $conditions = [Player::DB_TABLE_ALIAS . "." . Player::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(Player::DB_TABLE, Player::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Player::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }
    public function db_update()
    {
        $conditions = $this->object([Player::DB_ID]);
        $key_values = $this->object([Player::DB_FIRSTNAME, Player::DB_LASTNAME, Player::DB_EMAIL, Player::DB_MOBILE, Player::DB_TYPE]);
        $key_values = array_merge($key_values, [Player::DB_MODIFIED_BY => CTS::$user_id]);
        if (DBS::execute_update(Player::DB_TABLE, $key_values, $conditions) == 1) {
            return true;
        }
    }
    public function db_delete()
    {
        $conditions = $this->object([Player::DB_ID]);
        if (DBS::execute_delete(Player::DB_TABLE, $conditions) == 1) {
            return true;
        }
    }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
        $conditions = [];
        $parameters = Player::db_parameters(Player::DB_TABLE_ALIAS, $filter);



        if($type == "for-team") {
            
            $team_id = UTS::number_from($filter, "team_id");
            if($team_id) {
                array_push($conditions, Player::DB_TABLE_ALIAS . ".team_id IN (SELECT id FROM team_master WHERE id = " . $team_id . ") AND ".Player::DB_TABLE_ALIAS .".active = '1' ORDER BY firstname ASC" );
            } 
        }
		
		else if($type == "for-attendance")
        {
            $status = UTS::number_from($filter, "status", 0);
            $attendance_id = UTS::number_from($filter, "attendance_id");
            
            if($attendance_id) {
                array_push($conditions, Player::DB_TABLE_ALIAS . ".id IN (SELECT player FROM player_attendance WHERE attendance = " . $attendance_id . " AND status = " . $status . ")" );
            }

        }
        //
        $sort = Sort::sort_from($filter);
        if ($sort->type == "alphabetical") $sort->type = Player::DB_TABLE_ALIAS . "." . Player::DB_NAME;
        else if ($sort->type == "created") $sort->type = Player::DB_TABLE_ALIAS . "." . Player::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = Player::DB_TABLE_ALIAS . "." . Player::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, Player::db_search($filter));
        $result = Player::db_select($parameters, $conditions, $sort, $page);
		
		if($type == "for-attendance" && UTS::number_from($filter, "status",0) == 0)
        {
            $filter["status"] = 1;
            
            $result = array_merge($result, Player::db_list($type, $filter));
        }
		
        if ($result && count($result) > 0) {
            return $result;
        }
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = Player::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[Player::DB_ID] = (($alias) ? $alias . "." : "") . Player::DB_ID;
        $parameters[Player::DB_FIRSTNAME] = (($alias) ? $alias . "." : "") . Player::DB_FIRSTNAME;
        $parameters[Player::DB_LASTNAME] = (($alias) ? $alias . "." : "") . Player::DB_LASTNAME;
		$parameters[Player::DB_TEAMID] = (($alias) ? $alias . "." : "") . Player::DB_TEAMID;
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Player::DB_EMAIL, Player::DB_MOBILE,Player::DB_TYPE])) $parameters[$value] = (($alias) ? $alias . "." : "") . $value;
            else if (in_array($value, [Player::DB_CREATED_ON, Player::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Player::DB_CREATED_BY, Player::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
			else if ($value == Player::DB_STATUS)
            {
                $parameters[$value] = "(SELECT status FROM player_attendance WHERE attendance = ". UTS::number_from($filter, "attendance_id") ." AND player = PLY_M.id LIMIT 0,1)";
            }
			else if ($value == Team::DB_NAME)
            {
                $parameters[$value] = "(SELECT name FROM team_master WHERE id = PLY_M.team_id LIMIT 0,1)";
            }
        }
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([Player::DB_TABLE_ALIAS . '.firstname', Player::DB_TABLE_ALIAS . '.lastname'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Player::DB_TABLE, Player::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Player::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
}