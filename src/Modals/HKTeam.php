<?php

namespace App\Modals;

use App\Modals\HKUser as User;
use App\Modals\HKTeam as Team;
use App\Modals\HKModal as Modal;
use App\Modals\HKAttendance as Attendance;
use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
//
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKTeam extends Modal {
    const DB_TABLE = "team_master";
    const DB_TABLE_ALIAS = "TEM_M";
    // 
    public $id;
    public $name;
	public $year;
    public $region;
	public $player_count;
	public $favourites;
   
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function __construct($value = NULL) {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Team::DB_ID);
        $this->name = UTS::string_from($value, Team::DB_NAME);
		$this->year = UTS::string_from($value, Team::DB_YEAR);
        $this->region = UTS::string_from($value, Team::DB_REGION);
		$this->player_count = UTS::string_from($value, Team::DB_PLAYER_COUNT);
		$this->favourites = UTS::string_from($value, Team::DB_FAVOURITES);
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
        $parameters = Team::db_parameters(Team::DB_TABLE_ALIAS, $filter);
		
		array_push($conditions, Team::DB_TABLE_ALIAS . ".active ='1'");
        #filters for regions and sports
        if(isset($filter['region'])){
            array_push($conditions, Team::DB_TABLE_ALIAS . ".region ='" . UTS::string_from($filter, 'region' ) ."'");
        }
        
        if(isset($filter['sport'])){
            array_push($conditions, Team::DB_TABLE_ALIAS . ".sport ='" . UTS::string_from($filter, 'sport' ) ."'");
        }

        if(isset($filter['year'])){
            array_push($conditions, Team::DB_TABLE_ALIAS . ".year ='" . UTS::string_from($filter, 'year' ) ."'");
        }
        
        if($type== "for-coach" && isset($filter['coach_id'])){
			//array_push($conditions, Team::DB_TABLE_ALIAS . ".id IN (SELECT id FROM team_master WHERE created_by = ".UTS::number_from($filter, 'coach_id') .") ");
			array_push($conditions, Team::DB_TABLE_ALIAS . ".id IN (SELECT team FROM team_coach WHERE coach_id = ".UTS::number_from($filter, 'coach_id') .") ");
        }
        if($type== "state-region" && isset($filter['region_name'])){
            echo "sdsd23";
            // "(SELECT team FROM attendance_master WHERE coach = ".UTS::number_from($filter, 'coach_id') .")";
            array_push($conditions, "(SELECT team FROM attendance_master WHERE coach = ".UTS::string_from($filter, 'region_name') .") ");
        }
        // if($type=='my-teams'){
        //     $conditions=[Team::DB_TABLE_ALIAS . "." . Team::DB_ID . " IN(SELECT team_id from team_attendance)"];
        // }
        //
        $sort = Sort::sort_from($filter);
        if ($sort->type == "alphabetical") $sort->type = Team::DB_TABLE_ALIAS . "." . Team::DB_NAME;
        else if ($sort->type == "created") $sort->type = Team::DB_TABLE_ALIAS . "." . Team::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = Team::DB_TABLE_ALIAS . "." . Team::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, Team::db_search($filter));
        $result = Team::db_select($parameters, $conditions, $sort, $page);
        if ($result && count($result) > 0) {
            return $result;
        }
		else{
            echo json_encode(array("status" => "false", "msg" => "No Records Found"));
            die;
        }
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = Team::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[Team::DB_ID] = (($alias) ? $alias . "." : "") . Team::DB_ID;
        $parameters[Team::DB_NAME] = (($alias) ? $alias . "." : "") . Team::DB_NAME;
		$parameters[Team::DB_YEAR] = (($alias) ? $alias . "." : "") . Team::DB_YEAR;
        $parameters[Team::DB_REGION] = (($alias) ? $alias . "." : "") . Team::DB_REGION;
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Modal::DB_DESCRIPTION, Modal::DB_REGION])) $parameters[$value] = (($alias) ? $alias . "." : "") . $value;
            else if (in_array($value, [Modal::DB_CREATED_ON, Modal::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Modal::DB_CREATED_BY, Modal::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
			else if ($value == "player_count"){
                $parameters[$value] = "(SELECT COUNT(*) FROM player_master WHERE active =1 AND team_id = " . $parameters[Team::DB_ID] . " LIMIT 0,1)";
            }
			else if ($value == "favourites"){
                $parameters[$value] = "(SELECT COUNT(*) FROM team_coach WHERE team = TEM_M.id AND coach_id = ".UTS::number_from($filter, 'coach_id') .")";
            }
        }
            
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([Team::DB_TABLE_ALIAS . '.name'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Team::DB_TABLE, Team::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Team::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
    #endregion
}