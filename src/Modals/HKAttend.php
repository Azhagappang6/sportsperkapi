<?php

namespace App\Modals;

use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
use App\Modals\HKModal as Modal;
use App\Modals\HKUser as User;
use App\Modals\HKPlayer as Player;
use App\Modals\HKAttendance2 as Attendance2;
use App\Modals\HKAttend as Attend;
// 
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKAttend extends Modal 
{
    const DB_TABLE = "player_attendance";
    const DB_TABLE_ALIAS = "PLY_A";
    
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $mobile;
    public $password;
    public $type;
    public $player;
    public $status;
    public $date;
	public $attendance;
	public $active;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;
    //
    //public $attendance_status;

    function __construct($value = NULL)
    {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Attend::DB_ID);
        $this->player = UTS::string_from($value, Attend::DB_PLAYER);
        $this->status = UTS::string_from($value, Attend::DB_STATUS);
		$this->attendance = UTS::string_from($value, Attend::DB_ATTENDANCE);

        $this->type = UTS::string_from($value, Attend::DB_TYPE);
        $this->password = UTS::string_from($value, Attend::DB_PASSWORD);
        $this->date = UTS::string_from($value, Attendance2::DB_DATE);
		$this->firstname = UTS::string_from($value, Player::DB_FIRSTNAME);
        $this->lastname = UTS::string_from($value, Player::DB_LASTNAME);
		$this->active = UTS::string_from($value, Player::DB_ACTIVE);
       
        // 
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));
        //
        //$this->attendance_status = UTS::number_from($value, Modal::DB_ATTENDANCE);
    }

    public static function Init($value)
    {
        if ($value) {
            return new Attend($value);
        }
    }

    #region authenticate Operations

    public function db_authenticate($filter = NULL)
    {
        $parameters = Attend::db_parameters(Attend::DB_TABLE_ALIAS, $filter);
        $conditions = [Attend::DB_TABLE_ALIAS . "." . Attend::DB_EMAIL . "='" . $this->email . "'",
                        Attend::DB_TABLE_ALIAS . "." . Attend::DB_PASSWORD . "='" . $this->password . "'"];
        $result = DBS::execute_select(Attend::DB_TABLE, Attend::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Attend::class());
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
        $this->id = DBS::execute_insert(Player::DAttendB_TABLE, $key_values, true);
        if ($this->id) {
            return $this->id;
        }
        
    }
    public function db_read($filter = NULL)
    {
        $parameters = Attend::db_parameters(Attend::DB_TABLE_ALIAS, $filter);
        $conditions = [Attend::DB_TABLE_ALIAS . "." . Attend::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(Attend::DB_TABLE, Attend::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Attend::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }
    // public function db_update()
    // {
    //     $conditions = $this->object([Player::DB_ID]);
    //     $key_values = $this->object([Player::DB_FIRSTNAME, Player::DB_LASTNAME, Player::DB_EMAIL, Player::DB_MOBILE, Player::DB_TYPE]);
    //     $key_values = array_merge($key_values, [Player::DB_MODIFIED_BY => CTS::$user_id]);
    //     if (DBS::execute_update(Player::DB_TABLE, $key_values, $conditions) == 1) {
    //         return true;
    //     }
    // }
    // public function db_delete()
    // {
    //     $conditions = $this->object([Player::DB_ID]);
    //     if (DBS::execute_delete(Player::DB_TABLE, $conditions) == 1) {
    //         return true;
    //     }
    // }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
        $conditions = [];
        $parameters = Attend::db_parameters(Attend::DB_TABLE_ALIAS, $filter);

        if($type == "for-attendance")
        {
            $team_id = UTS::number_from($filter, "team_id");
            $date = UTS::string_from($filter, "date");
            // $attendance_id = UTS::number_from($filter, "attendance_id");
            // $status = UTS::number_from($filter, "status");
            if($team_id) {
                array_push($conditions, Attend::DB_TABLE_ALIAS . ".attendance IN (SELECT id FROM attendance_master WHERE team = " . $team_id . " AND date = '".$date."') GROUP BY player" );
            }

        }
        //
        $sort = Sort::sort_from($filter);
        if ($sort->type == "alphabetical") $sort->type = Attend::DB_TABLE_ALIAS . "." . Attend::DB_NAME;
        else if ($sort->type == "created") $sort->type = Attend::DB_TABLE_ALIAS . "." . Attend::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = Attend::DB_TABLE_ALIAS . "." . Attend::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, Attend::db_search($filter));
        $result = Attend::db_select($parameters, $conditions, $sort, $page);
        
		$res1 = array();
		if ($result && count($result) > 0) {
      
                foreach($result as $val)
                {
                    if ($val->active == 1) {
                        $res1[] = array( "id" => $val->id, "firstname" => $val->firstname, "lastname" => $val->lastname, "player" => $val->player, "status" => $val->status, "attendance" => $val->attendance);
                    }
				}
				return $res1;
		}
        /* if ($result && count($result) > 0) 
        {	
			return $result;
        }else{
            echo json_encode(array("status" => "false", "msg" => "Records Not Found"));
            die;
        } */
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = Attend::DB_TABLE_ALIAS, $filter = NULL)
    {
		$today = date("Y-m-d");
        $parameters = [];
        $parameters[Attend::DB_ID] = (($alias) ? $alias . "." : "") . Attend::DB_ID;
        $parameters[Attend::DB_PLAYER] = (($alias) ? $alias . "." : "") . Attend::DB_PLAYER;
        $parameters[Attend::DB_STATUS] = (($alias) ? $alias . "." : "") . Attend::DB_STATUS;
		$parameters[Attend::DB_ATTENDANCE] = (($alias) ? $alias . "." : "") . Attend::DB_ATTENDANCE;
		$parameters[Player::DB_FIRSTNAME] = (($alias) ? $alias . "." : "") . Player::DB_FIRSTNAME;
        $parameters[Player::DB_LASTNAME] = (($alias) ? $alias . "." : "") . Player::DB_LASTNAME;
		$parameters[Player::DB_ACTIVE] = (($alias) ? $alias . "." : "") . Player::DB_ACTIVE;
		//$parameters[Attendance2::DB_DATE] = (($alias) ? $alias . "." : "") . Attendance2::DB_DATE;
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Attend::DB_EMAIL, Attend::DB_MOBILE,Attend::DB_TYPE])) $parameters[$value] = (($alias) ? $alias . "." : "") . $value;
            else if (in_array($value, [Attend::DB_CREATED_ON, Attend::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Attend::DB_CREATED_BY, Attend::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
			else if ($value == Player::DB_FIRSTNAME)
            {
                $parameters[$value] = "(SELECT firstname FROM player_master WHERE id = PLY_A.player LIMIT 0,1)";
            }
            else if ($value == Player::DB_LASTNAME)
            {
                $parameters[$value] = "(SELECT lastname FROM player_master WHERE id = PLY_A.player LIMIT 0,1)";
            }
			else if ($value == Player::DB_ACTIVE)
            {
                $parameters[$value] = "(SELECT active FROM player_master WHERE id = PLY_A.player LIMIT 0,1)";
            }
        }
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([Attend::DB_TABLE_ALIAS . '.firstname', Attend::DB_TABLE_ALIAS . '.lastname'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Attend::DB_TABLE, Attend::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Attend::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
}