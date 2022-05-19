<?php

namespace App\Modals;

use App\Modals\Helpers\HKPage as Page;
use App\Modals\Helpers\HKSort as Sort;
use App\Modals\HKModal as Model;
use App\Modals\HKUser as User;
// 
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKUser extends Model 
{
    const DB_TABLE = "user_master";
    const DB_TABLE_ALIAS = "USR_M";
    // 
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $mobile;
    public $password;
    public $type;
    // 
    public $created_by;
    public $created_on;
    public $modified_by;
    public $modified_on;

    function __construct($value = NULL)
    {
        parent::__construct($value);
        $this->id = UTS::number_from($value, User::DB_ID);
        $this->firstname = UTS::string_from($value, User::DB_FIRSTNAME);
        $this->lastname = UTS::string_from($value, User::DB_LASTNAME);
        $this->email = UTS::string_from($value, User::DB_EMAIL);
        $this->mobile = UTS::string_from($value, User::DB_MOBILE);
        $this->type = UTS::string_from($value, User::DB_TYPE);
        $this->password = UTS::string_from($value, User::DB_PASSWORD);
        // print_r("***");
        // print_r($this->mobile);
        // 
        $this->created_on = UTS::number_from($value, Model::DB_CREATED_ON);
        $this->modified_on = UTS::number_from($value, Model::DB_MODIFIED_ON);
        $this->created_by = User::Init(UTS::value_from($value, Model::DB_CREATED_BY));
        $this->modified_by = User::Init(UTS::value_from($value, Model::DB_MODIFIED_BY));

        // print_r(json_encode($this));
    }

    public static function Init($value)
    {
        if ($value) {
            return new User($value);
        }
    }

    #region CRUD Operations
    public function db_create()
    {
        $key_values = $this->object([User::DB_FIRSTNAME, User::DB_LASTNAME, User::DB_EMAIL, User::DB_MOBILE,User::DB_TYPE,User::DB_PASSWORD]);
        $key_values = array_merge($key_values, [User::DB_CREATED_BY => CTS::$user_id]);
        $this->id = DBS::execute_insert(User::DB_TABLE, $key_values, true);
        if ($this->id) {
            return $this->id;
        }
    }

    public function db_read($filter = NULL)
    {
        $parameters = User::db_parameters(User::DB_TABLE_ALIAS, $filter);
        $conditions = [User::DB_TABLE_ALIAS . "." . User::DB_ID . "=" . $this->id];
        $result = DBS::execute_select(User::DB_TABLE, User::DB_TABLE_ALIAS, $parameters, $conditions, NULL, Page::page0_1(), User::class());
        if ($result && count($result) == 1) {
            return $result[0];
        }
    }

    public function db_update()
    {
        $conditions = $this->object([User::DB_ID]);
        $key_values = $this->object([User::DB_FIRSTNAME, User::DB_LASTNAME, User::DB_EMAIL, User::DB_MOBILE, User::DB_TYPE]);
        $key_values = array_merge($key_values, [User::DB_MODIFIED_BY => CTS::$user_id]);
        if (DBS::execute_update(User::DB_TABLE, $key_values, $conditions) == 1) {
            return true;
        }
    }

    public function db_delete()
    {
        $conditions = $this->object([User::DB_ID]);
        if (DBS::execute_delete(User::DB_TABLE, $conditions) == 1) {
            return true;
        }
    }
    #endregion

    #region Listing & List Counts
    public static function db_list($type, $filter)
    {
        $conditions = [];
        $parameters = User::db_parameters(User::DB_TABLE_ALIAS, $filter);
        //
        $sort = Sort::sort_from($filter);
        if ($sort->type == "alphabetical") $sort->type = User::DB_TABLE_ALIAS . "." . User::DB_NAME;
        else if ($sort->type == "created") $sort->type = User::DB_TABLE_ALIAS . "." . User::DB_CREATED_ON;
        else if ($sort->type == "modified") $sort->type = User::DB_TABLE_ALIAS . "." . User::DB_MODIFIED_ON;
        else $sort->type = NULL;
        // 
        $page = Page::page_from($filter);
        // 
        $conditions = array_merge($conditions, User::db_search($filter));
        $result = User::db_select($parameters, $conditions, $sort, $page);
        if ($result && count($result) > 0) {
            return $result;
        }
    }

    public static function db_listcount($type, $filter)
    {
    }
    #endregion

    #region Helper Methods
    public static function db_parameters($alias = User::DB_TABLE_ALIAS, $filter = NULL)
    {
        $parameters = [];
        $parameters[User::DB_ID] = (($alias) ? $alias . "." : "") . User::DB_ID;
        $parameters[User::DB_FIRSTNAME] = (($alias) ? $alias . "." : "") . User::DB_FIRSTNAME;
        $parameters[User::DB_LASTNAME] = (($alias) ? $alias . "." : "") . User::DB_LASTNAME;
        $parameters[User::DB_MOBILE] = (($alias) ? $alias . "." : "") . User::DB_MOBILE;
        foreach (UTS::array_from($filter, "extra_params", []) as $key => $value) {
            if (in_array($value, [User::DB_EMAIL, User::DB_MOBILE,User::DB_TYPE])) $parameters[$value] = (($alias) ? $alias . "." : "") . $value;
            else if (in_array($value, [Coach::DB_CREATED_ON, User::DB_MODIFIED_ON])) $parameters[$value] = "UNIX_TIMESTAMP(" . $alias . "." . $value . ")";
            else if (in_array($value, [Coach::DB_CREATED_BY, User::DB_MODIFIED_BY])) $parameters[$value] = "(SELECT JSON_OBJECT(" . UTS::query_params(User::db_parameters(NULL), true) . ") FROM user_master WHERE id=" . (($alias) ? $alias . "." : "") . $value . " LIMIT 0,1)";
        }
        return $parameters;
    }

    public static function db_search($filter)
    {
        $conditions = [];
        $keyword = addslashes(trim(UTS::string_from($filter, 'keyword')));
        if ($keyword) {
            foreach ([User::DB_TABLE_ALIAS . '.firstname', User::DB_TABLE_ALIAS . '.lastname'] as $key => $value) {
                array_push($conditions, $value  . " LIKE '%" . $keyword . "%'");
            }
        }
        return $conditions;
    }

    public static function db_select($parameters, $conditions, $sort = NULL, $page = NULL)
    {
        return DBS::execute_select(User::DB_TABLE, User::DB_TABLE_ALIAS, $parameters, $conditions, $sort, $page, User::class());
    }
    #endregion

    #region NOT TO BE MODIFIED
    public static function class()
    {
        return get_called_class();
    }
    #endregion
}