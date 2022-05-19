<?php

namespace App\Modals;

use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
use App\Modals\HKModal as Modal;
use App\Modals\HKCoach as Coach;
use App\Modals\HKTeam as Team;
use App\Modals\HKAttendance2 as Attendance2;
use App\Modals\HKUser as User;
use App\Modals\HKPlayer as Player;
// 
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKAttendance2 extends Modal 
{
    const DB_TABLE = "Attendance_master";
    const DB_TABLE_ALIAS = "ATE_M";

    public $id;
    public $team;
    public $coach;
    public $date;
    public $players;
    public $status;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function team_id()
    {
        if($this->team && $this->team instanceof Team){
            return $this->team->id;
        }
        return null;
    }

    function coach_id()
    {
        if($this->coach && $this->coach instanceof Coach){
            return $this->coach->id;
        }
        return null;
    }

    function __construct($value = NULL)
    {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Attendance2::DB_ID);
        $this->team = Team::Init(UTS::value_from($value, Modal::DB_TEAM));
        $this->coach = Coach::Init(UTS::value_from($value, Modal::DB_COACH));
        $this->date = UTS::string_from($value, Modal::DB_DATE);
        $this->players = array();
        foreach (UTS::array_from($value, Modal::DB_PLAYERS, []) as $key => $value) {
            array_push($this->players, Player::Init($value));
        }
        
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));

        // echo json_encode($players);
    }

    public static function Init($value)
    {
        if ($value) {
            return new Attendance2($value);
        }
    }

    
    #region CRUD Operations

    public function db_create()
    {
        if (!$this->db_attendance_available())
        {

            $key_values = $this->object([Attendance2::DB_DATE]);
            $key_values = array_merge($key_values, [Attendance2::DB_TEAM => $this->team_id()]);
            $key_values = array_merge($key_values, [Attendance2::DB_COACH => $this->coach_id()]);
            $key_values = array_merge($key_values, [Attendance2::DB_CREATED_BY => CTS::$user_id]);

            $this->id = DBS::execute_insert(Attendance2::DB_TABLE, $key_values, true);
            if ($this->id) {

                foreach ($this->players as $key => $value) 
                {
                    $key_values2["attendance"] = $this->id;
                    $key_values2["player"] = $value->id;
                    $key_values2["status"] = $value->attendance;
                    $key_values2 = array_merge($key_values2, [Attendance2::DB_CREATED_BY => CTS::$user_id]);
					
					if ($value->id != 0)
                    {
						DBS::execute_insert("player_attendance", $key_values2, true);
					}
                }
    			//echo json_encode($this->id);
                return $this->id;
            }
        }else{
            echo json_encode(array("status" => "false", "msg" => "Records Already Exists"));
            die;
        }
    
    }

    public function db_attendance_available()
    {
        $parameters = Attendance2::db_parameters(Attendance2::DB_TABLE_ALIAS, NULL);
        $conditions = [Attendance2::DB_TABLE_ALIAS . "." . Attendance2::DB_TEAM ."=" . $this->team->id];
        $conditions = array_merge($conditions, [Attendance2::DB_TABLE_ALIAS . "." . Attendance2::DB_DATE . "='" . $this->date . "'"]);
        $result = DBS::execute_select(Attendance2::DB_TABLE, Attendance2::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Attendance2::class());
        return ($result && count($result) == 1);
       
    }


    public function db_read($filter = NULL)
    {
        $parameters = Attendance2::db_parameters(Attendance2::DB_TABLE_ALIAS, $filter);
        $conditions = [Attendance2::DB_TABLE_ALIAS . "." . Attendance2::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(Attendance2::DB_TABLE, Attendance2::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Attendance2::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }

    public function db_update()
    {
        $conditions = $this->object([Attendance2::DB_ID]);
        $key_values = array();
        $key_values = array_merge($key_values, [Attendance2::DB_TEAM => $this->team_id()]);
        $key_values = array_merge($key_values, [Attendance2::DB_COACH => $this->coach_id()]);
        $key_values = array_merge($key_values, [Attendance2::DB_MODIFIED_BY => CTS::$user_id]);

        $result = DBS::execute_update(Attendance2::DB_TABLE, $key_values, $conditions);

        foreach ($this->players as $key => $value) {
            $player_conditions = array("id" => $value->id);
            $player_keyvalues = array("status" => $value->attendance);
            $player_keyvalues = array_merge($player_keyvalues, [Player::DB_MODIFIED_BY => CTS::$user_id]);
            $result += DBS::execute_update("player_attendance", $player_keyvalues, $player_conditions);
        }

        if($result > 0)
        {
            return true;
        }else{
            echo json_encode(array("status" => "false", "msg" => "Records Already Exists"));
            die;
        }
    }

    public function db_update2()
    {
        $conditions = $this->object([Attendance2::DB_TEAM]);
        $conditions = $this->object([Attendance2::DB_DATE]);

        // $key_values = array_merge($key_values, [Attendance2::DB_TEAM => $this->team_id()]);
        // $key_values = array_merge($key_values, [Attendance2::DB_COACH => $this->coach_id()]);
        // $key_values = array_merge($key_values, [Attendance2::DB_MODIFIED_BY => CTS::$user_id]);
       
        // echo json_encode($this->players);
        foreach ($this->players as $key => $value) 
        {
            print_r($value);
            // $key_values2["attendance"] = $this->id;
            // $key_values2["player"] = $value->id;
            $key_values2["status"] = $value->attendance_status;
            $key_values2 = array_merge($key_values2, [Attendance2::DB_MODIFIED_BY => CTS::$user_id]);
            DBS::execute_update("player_attendance", $key_values2, true);
        }
        return true;
    }

    public function db_delete()
    {
        $conditions = $this->object([Attendance2::DB_ID]);
        if (DBS::execute_delete(Attendance2::DB_TABLE, $conditions) == 1) {
            return true;
        }
    }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
         $conditions = [];
         $parameters = Attendance2::db_parameters(Attendance2::DB_TABLE_ALIAS, $filter);
         //
         $sort = Sort::sort_from($filter);
         if ($sort->type == "alphabetical") $sort->type = Attendance2::DB_TABLE_ALIAS . "." . Attendance2::DB_NAME;
         else if ($sort->type == "created") $sort->type = Attendance2::DB_TABLE_ALIAS . "." . Attendance2::DB_CREATED_ON;
         else if ($sort->type == "modified") $sort->type = Attendance2::DB_TABLE_ALIAS . "." . Attendance2::DB_MODIFIED_ON;
         else $sort->type = NULL;
         // 
         $page = Page::page_from($filter);
         // 
         $conditions = array_merge($conditions, Attendance2::db_search($filter));
         $result = Attendance2::db_select($parameters, $conditions, $sort, $page);
         if ($result && count($result) > 0) {
             return $result;
         }
     }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion


    #region Helper Methods
    public static function db_parameters($alias = Attendance2::DB_TABLE_ALIAS, $filter = NULL)
    {


        $parameters = [];
        $parameters[Attendance2::DB_ID] = (($alias) ? $alias . "." : "") . Attendance2::DB_ID;
        $parameters[Attendance2::DB_TEAM] = (($alias) ? $alias . "." : "") . Attendance2::DB_TEAM;
        $parameters[Attendance2::DB_DATE] = (($alias) ? $alias . "." : "") . Attendance2::DB_DATE;

        // if ($value == "state") $query_parameters[$value] = "(SELECT JSON_ARRAYAGG(JSON_OBJECT(". UTS::query_params_array(State::db_parameters('')) .")) FROM ". State::db_name() ." AS ". State::db_alias() ." WHERE ".State::db_alias().".id = IN (SELECT player FROM player_attendance WHERE attendance =" . (($alias) ? $alias . "." : "") . Attendance2::DB_TEAM . "))"; 

        // $parameters[Attendance2::DB_TEAM] = "(SELECT JSON_OBJECT(" . UTS::query_params(Attendance2::db_parameters(NULL), true) . ") FROM player_attendance WHERE attendance=" . (($alias) ? $alias . "." : "") . Attendance2::DB_ID . " LIMIT 0,1)";
        // $parameters[Attendance2::DB_COACH] = "(SELECT JSON_OBJECT(" . UTS::query_params(Coach::db_parameters(NULL), true) . ") FROM coach_master WHERE id=" . (($alias) ? $alias . "." : "") . Attendance2::DB_COACH . " LIMIT 0,1)";
        
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Attendance2::DB_EMAIL, Attendance2::DB_MOBILE,Attendance2::DB_TYPE])) $parameters[$value] = (($alias) ? $alias . "." : "") . $value;
            else if (in_array($value, [Attendance2::DB_CREATED_ON, Attendance2::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Attendance2::DB_CREATED_BY, Attendance2::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
            // else if( $value == "players"){
            //     $parameters[$value] = "(SELECT JSON_OBJECT(". UTS::query_params(Player::db_parameters(NULL), true) .") FROM player_master WHERE id=". Attendance2::DB_TABLE_ALIAS .".player)";
            // }
        }
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([Attendance2::DB_TABLE_ALIAS . '.firstname', Attendance2::DB_TABLE_ALIAS . '.lastname'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Attendance2::DB_TABLE, Attendance2::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Attendance2::class());
    }
    #endregion

   #region NOT TO BE MODIFIED
   public static function class()
   {
       return get_called_class();
   }
   #endregion

}