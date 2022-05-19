<?php

namespace App\Modals;

use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
use App\Modals\HKModal as Modal;
use App\Modals\HKUser as User;
use App\Modals\HKPlayer as Player;
use App\Modals\HKTeam as Team;
use App\Modals\HKFavourites as Favourites;
// 
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKFavourites extends Modal 
{
    const DB_TABLE = "team_coach";
    const DB_TABLE_ALIAS = "TEA_C";
    
    public $id;
    public $coach_id;
    public $team;
    public $team_id;
    public $team_name;
    public $year;
    public $region;
    public $active;
	public $player_count;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;
    //
    function __construct($value = NULL)
    {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Favourites::DB_ID);
        $this->coach_id = UTS::number_from($value, Favourites::DB_COACH_ID);
        $this->team = UTS::number_from($value, Favourites::DB_TEAM);
       
        $this->team_id = UTS::number_from($value, Player::DB_TEAMID);
        $this->team_name = UTS::string_from($value, Team::DB_NAME);
        $this->year = UTS::string_from($value, Team::DB_YEAR);
        $this->region = UTS::string_from($value, Team::DB_REGION);
        $this->active = UTS::string_from($value, Team::DB_ACTIVE);
		$this->player_count = UTS::string_from($value, Team::DB_PLAYER_COUNT);
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
            return new Favourites($value);
        }
    }

    #region authenticate Operations

    #end of authenticate region 
    
    #region CRUD Operations

    public function db_create()
    {
        if (!$this->db_favourites_available())
        {
            $key_values = $this->object([Favourites::DB_COACH_ID, Favourites::DB_TEAM]);
            $key_values = array_merge($key_values, [Favourites::DB_CREATED_BY => CTS::$user_id]);
            $this->id = DBS::execute_insert(Favourites::DB_TABLE, $key_values, true);
            if ($this->id) {
                return $this->id;
            }
        }
    }

    public function db_favourites_available()
    {
        $parameters = Favourites::db_parameters(Favourites::DB_TABLE_ALIAS, NULL);
        $conditions = [Favourites::DB_TABLE_ALIAS . "." . Favourites::DB_TEAM ."=" . $this->team];
        $conditions = array_merge($conditions, [Favourites::DB_TABLE_ALIAS . "." . Favourites::DB_COACH_ID . "='" . $this->coach_id . "'"]);
        $result = DBS::execute_select(Favourites::DB_TABLE, Favourites::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Favourites::class());
        return ($result && count($result) == 1);
       
    }

    public function db_read($filter = NULL)
    {
        $parameters = Favourites::db_parameters(Favourites::DB_TABLE_ALIAS, $filter);
        $conditions = [Favourites::DB_TABLE_ALIAS . "." . Favourites::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(Favourites::DB_TABLE, Favourites::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Favourites::class());
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
    public function db_delete()
    {
        $conditions = $this->object([Favourites::DB_ID]);
        if (DBS::execute_delete(Favourites::DB_TABLE, $conditions) == 1) {
            return true;
        }
    }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
        $conditions = [];
        $parameters = Favourites::db_parameters(Favourites::DB_TABLE_ALIAS, $filter);

        if($type == "for-coach") {
            
            $coach_id = UTS::number_from($filter, "coach_id");
            if($coach_id) {
                array_push($conditions, Favourites::DB_TABLE_ALIAS . ".coach_id IN (SELECT id FROM coach_master WHERE id = " . $coach_id . ") " );
            } 
        }

        //
        $sort = Sort::sort_from($filter);
        if ($sort->type == "alphabetical") $sort->type = Favourites::DB_TABLE_ALIAS . "." . Favourites::DB_NAME;
        else if ($sort->type == "created") $sort->type = Favourites::DB_TABLE_ALIAS . "." . Favourites::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = Favourites::DB_TABLE_ALIAS . "." . Favourites::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, Favourites::db_search($filter));
        $result = Favourites::db_select($parameters, $conditions, $sort, $page);
        
        $res1 = array();
        if ($result && count($result) > 0) {
      
                foreach($result as $val)
                {
                    if ($val->active == 1) {
                        $res1[] = array( "id" => $val->id, "team_id" => $val->team, "team_name" => $val->team_name, "year" => $val->year, "region" => $val->region, "player_count" => $val->player_count);
                    }
                }
                return $res1;
        }
        else{
            echo json_encode(array("status" => "false", "msg" => "No Records Found"));
            die;
        }
        // if ($result && count($result) > 0) {
        //     return $result;
        // }
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = Favourites::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[Favourites::DB_ID] = (($alias) ? $alias . "." : "") . Favourites::DB_ID;
        $parameters[Favourites::DB_TEAM] = (($alias) ? $alias . "." : "") . Favourites::DB_TEAM;
        $parameters[Favourites::DB_ID] = (($alias) ? $alias . "." : "") . Favourites::DB_ID;
        // $parameters[Team::DB_NAME] = (($alias) ? $alias . "." : "") . Team::DB_NAME;
        // $parameters[Player::DB_FIRSTNAME] = (($alias) ? $alias . "." : "") . Player::DB_FIRSTNAME;
        // $parameters[Player::DB_LASTNAME] = (($alias) ? $alias . "." : "") . Player::DB_LASTNAME;
        // $parameters[Player::DB_TEAMID] = (($alias) ? $alias . "." : "") . Player::DB_TEAMID;
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Player::DB_EMAIL, Player::DB_MOBILE,Player::DB_TYPE])) $parameters[$value] = (($alias) ? $alias . "." : "") . $value;
            else if (in_array($value, [Player::DB_CREATED_ON, Player::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Player::DB_CREATED_BY, Player::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
           
            else if ($value == Team::DB_NAME)
            {
                $parameters[$value] = "(SELECT name FROM team_master WHERE id = TEA_C.team LIMIT 0,1)";
            }
            else if ($value == Team::DB_YEAR)
            {
                $parameters[$value] = "(SELECT year FROM team_master WHERE id = TEA_C.team LIMIT 0,1)";
            }
            else if ($value == Team::DB_REGION)
            {
                $parameters[$value] = "(SELECT region FROM team_master WHERE id = TEA_C.team LIMIT 0,1)";
            }
            else if ($value == Team::DB_ACTIVE)
            {
                $parameters[$value] = "(SELECT active FROM team_master WHERE id = TEA_C.team LIMIT 0,1)";
            }
			else if ($value == "player_count"){
                $parameters[$value] = "(SELECT COUNT(*) FROM player_master WHERE active =1 AND team_id = " . $parameters[Favourites::DB_TEAM] . " LIMIT 0,1)";
            }
        }
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([Favourites::DB_TABLE_ALIAS . '.firstname', Favourites::DB_TABLE_ALIAS . '.lastname'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Favourites::DB_TABLE, Favourites::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Favourites::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
}