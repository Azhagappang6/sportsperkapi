<?php

namespace App\Modals;

use App\Modals\HKUser as User;
use App\Modals\HKPlayer as Player;
use App\Modals\HKTeam as Team;
use App\Modals\HKModal as Modal;
use App\Modals\HKAttendance as Attendance;
use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
//
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKAttendance extends Modal {
    const DB_TABLE = "player_attendance";
    const DB_TABLE_ALIAS = "TEA_A";
    // 
    public $id;
    public $attendance;
    public $player;
    public $status;
   
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function __construct($value = NULL) {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Modal::DB_ID);
        $this->attendance = Team::Init(UTS::value_from($value, Modal::DB_ATTENDANCE));
        $this->player = array();
        foreach (UTS::array_from($value, Modal::DB_PLAYER, []) as $key => $value) {
            array_push($this->player, Player::Init($value));
        }
        // $this->date = UTS::number_from($value, Modal::DB_DATE);
        //
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));
        // print_r($this);
    }

    public static function Init($value)
    {
        if ($value) {
            return new Attendance($value);
        }
    }

    #region Listing & List Counts

    public function db_create()
    {
        $ids = array();
        foreach ($this->player as $key => $value) {
            // print_r($value);
            $key_values = $this->object([Attendance::DB_DATE]);
            $key_values = array_merge($key_values, [Attendance2::DB_ID]);
            $key_values = array_merge($key_values, [Attendance::DB_PLAYER => $value->id, Attendance::DB_STATUS => $value->attendance_status]);
            $key_values = array_merge($key_values, [Modal::DB_CREATED_BY => CTS::$user_id]);
            $var = DBS::execute_insert(Attendance::DB_TABLE, $key_values, true);
            if ($var) {
                print_r($this->player);
                // print_r($var);
                array_push($ids, $var);
            }
        }
        
        return $ids;
        
        // if ($this->id) {
        //     return $this->id;
        // }
    
    }
    
    public function db_read($filter = NULL)
    {
        $parameters = Attendance::db_parameters(Attendance::DB_TABLE_ALIAS, $filter);
        $conditions = [Attendance::DB_TABLE_ALIAS . "." . Attendance::DB_TEAM . "=" . $this->id];
        $result = DBS::execute_select(Attendance::DB_TABLE, Attendance::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Attendance::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }

    public function db_update()
    {
        $conditions = $this->object([Attendance::DB_TEAM]);
        // $key_values = $this->object([Player::DB_FIRSTNAME, Player::DB_LASTNAME, Player::DB_EMAIL, Player::DB_MOBILE, Player::DB_TYPE]);
        // $key_values = array_merge($key_values, [Player::DB_MODIFIED_BY => CTS::$user_id]);
       
        $ids = array();
        foreach ($this->players as $key => $value) {
            $key_values = $this->object([Attendance::DB_DATE]);
            $key_values = array_merge($key_values, [Attendance::DB_TEAM => $this->team->id]);
            $key_values = array_merge($key_values, [Attendance::DB_PLAYER => $value->id, Attendance::DB_STATUS => $value->attendance_status]);
            $key_values = array_merge($key_values, [Modal::DB_CREATED_BY => CTS::$user_id]);
            if (DBS::execute_update(Player::DB_TABLE, $key_values, $conditions) == 1) {
                return true;
            }
        }
    }
    
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = User::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[Attendance::DB_ID] = (($alias) ? $alias . "." : "") . Attendance::DB_ID;
        $parameters[Attendance::DB_TEAM] = (($alias) ? $alias . "." : "") . Attendance::DB_TEAM;
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Modal::DB_CREATED_ON, Modal::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Modal::DB_CREATED_BY, Modal::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
        }
            
        return $parameters;
    }

    

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Attendance::DB_TABLE, Attendance::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Attendance::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
    #endregion
}