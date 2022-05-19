<?php

namespace App\Modals;

use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
use App\Modals\HKModal as Modal;
use App\Modals\HKUser as User;
use App\Modals\HKCoach as Coach;
// 
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKCoach extends Modal 
{
    const DB_TABLE = "coach_master";
    const DB_TABLE_ALIAS = "COH_M";

    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $mobile;
    public $password;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function __construct($value = NULL)
    {
        parent::__construct($value);
        $this->id = UTS::number_from($value, Coach::DB_ID);
        $this->firstname = UTS::string_from($value, Coach::DB_FIRSTNAME);
        $this->lastname = UTS::string_from($value, Coach::DB_LASTNAME);
        $this->email = UTS::string_from($value, Coach::DB_EMAIL);
        $this->mobile = UTS::string_from($value, Coach::DB_MOBILE);
        $this->password = UTS::string_from($value, Coach::DB_PASSWORD);
        $this->created_on = UTS::number_from($value, Modal::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Modal::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Modal::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Modal::DB_MODIFIED_BY));
    }

    public static function Init($value)
    {
        if ($value) {
            return new Coach($value);
        }
    }

    #region authenticate Operations

    // public function db_authenticate($filter = NULL)
    // {
    //     $parameters = Coach::db_parameters(Coach::DB_TABLE_ALIAS, $filter);
    //     $conditions = [Coach::DB_TABLE_ALIAS . "." . Coach::DB_EMAIL . "='" . $this->email . "'",
    //                     Coach::DB_TABLE_ALIAS . "." . Coach::DB_PASSWORD . "='" . $this->password . "'"];
    //     $result = DBS::execute_select(Coach::DB_TABLE, Coach::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), Coach::class());
    //     if ($result && count($result) == 1) {
    //         return $result[0];
    //     }
    
    // }
    
    #end of authenticate region 

    #region CRUD Operations

    public function db_create()
    {
        if (!$this->db_coach_available())
        {
            $key_values = $this->object([Coach::DB_FIRSTNAME, Coach::DB_LASTNAME, Coach::DB_EMAIL, Coach::DB_MOBILE,Coach::DB_TYPE,Coach::DB_PASSWORD]);
            $key_values = array_merge($key_values, [Coach::DB_CREATED_BY => CTS::$user_id]);
            $this->id = DBS::execute_insert(Coach::DB_TABLE, $key_values, true);
            if ($this->id) {
                return $this->id;
            }
        }
    }

    public function db_coach_available()
    {
        $parameters = Coach::db_parameters(Coach::DB_TABLE_ALIAS, NULL);
        $conditions = [Coach::DB_TABLE_ALIAS . "." . Coach::DB_EMAIL . "='" . $this->email . "'"];
        $result = DBS::execute_select(Coach::DB_TABLE, Coach::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), User::class());
        return ($result && count($result) == 1);
       
    }

    public function db_authenticate($filter = NULL)
    {
        $parameters = Coach::db_parameters(Coach::DB_TABLE_ALIAS, $filter);
        $conditions = [Coach::DB_TABLE_ALIAS . "." . Coach::DB_EMAIL . "='" . $this->email . "'", 
                    Coach::DB_TABLE_ALIAS . "." . Coach::DB_PASSWORD . "='" . $this->password . "'"];
        $result = DBS::execute_select(Coach::DB_TABLE, Coach::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), User::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }else{
            echo json_encode(array("status" => "false", "msg" => "User Not Exists"));
            die;
        }
    }

    public function db_read($filter = NULL)
    {
        $parameters = Coach::db_parameters(Coach::DB_TABLE_ALIAS, $filter);
        $conditions = [Coach::DB_TABLE_ALIAS . "." . Coach::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(Coach::DB_TABLE, Coach::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), User::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }

    public function db_update()
    {
        $conditions = $this->object([Coach::DB_ID]);
        $key_values = $this->object([Coach::DB_FIRSTNAME, Coach::DB_LASTNAME, Coach::DB_EMAIL, Coach::DB_MOBILE]);
        $key_values = array_merge($key_values, [Coach::DB_MODIFIED_BY => CTS::$user_id]);
        if (DBS::execute_update(Coach::DB_TABLE, $key_values, $conditions) == 1) {
            return true;
        }
    }

    public function db_delete()
    {
        $conditions = $this->object([Coach::DB_ID]);
        if (DBS::execute_delete(Coach::DB_TABLE, $conditions) == 1) {
            return true;
        }
    }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
         $conditions = [];
         $parameters = Coach::db_parameters(Coach::DB_TABLE_ALIAS, $filter);
         //
         $sort = Sort::sort_from($filter);
         if ($sort->type == "alphabetical") $sort->type = Coach::DB_TABLE_ALIAS . "." . Coach::DB_NAME;
         else if ($sort->type == "created") $sort->type = Coach::DB_TABLE_ALIAS . "." . Coach::DB_CREATED_ON;
         else if ($sort->type == "modified") $sort->type = Coach::DB_TABLE_ALIAS . "." . Coach::DB_MODIFIED_ON;
         else $sort->type = NULL;
         // 
         $page = Page::page_from($filter);
         // 
         $conditions = array_merge($conditions, Coach::db_search($filter));
         $result = Coach::db_select($parameters, $conditions, $sort, $page);
         if ($result && count($result) > 0) {
             return $result;
         }
     }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion


    #region Helper Methods
    public static function db_parameters($alias = Coach::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[Coach::DB_ID] = (($alias) ? $alias . "." : "") . Coach::DB_ID;
        $parameters[Coach::DB_FIRSTNAME] = (($alias) ? $alias . "." : "") . Coach::DB_FIRSTNAME;
        $parameters[Coach::DB_LASTNAME] = (($alias) ? $alias . "." : "") . Coach::DB_LASTNAME;
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [Coach::DB_EMAIL, Coach::DB_MOBILE,Coach::DB_TYPE])) $parameters[$value] = (($alias) ? $alias . "." : "") . $value;
            else if (in_array($value, [Coach::DB_CREATED_ON, Coach::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Coach::DB_CREATED_BY, Coach::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
        }
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([Coach::DB_TABLE_ALIAS . '.firstname', Coach::DB_TABLE_ALIAS . '.lastname'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(Coach::DB_TABLE, Coach::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, Coach::class());
    }
    #endregion

   #region NOT TO BE MODIFIED
   public static function class()
   {
       return get_called_class();
   }
   #endregion

}