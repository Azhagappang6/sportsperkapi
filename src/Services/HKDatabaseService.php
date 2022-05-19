<?php

namespace App\Services;

use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;

class HKDatabaseService
{
    public static $db_connection;

    #region ***** Helper Methods *****
    private static function execute($query)
    {
        return DBS::$db_connection->query($query);
    }

    private static function insert_id()
    {
        return DBS::$db_connection->LastInsertId();
    }

    private static function count($statement)
    {
        return ($statement) ? $statement->rowCount() : null;
    }

    private static function fetch($statement)
    {
        return ($statement) ? $statement->fetchAll() : null;
    }

    #endregion

    #region
    public static function execute_scalar($query)
    {
        if (DBS::execute($query)) {
            return DBS::insert_id();
        }
    }

    public static function execute_nonquery($query)
    {
        return DBS::count(DBS::execute($query));
    }
    #endregion

    #region
    public static function execute_insert($table, $key_values, $is_scalar = FALSE)
    {
        $keys = [];
        $values = [];
        foreach ($key_values as $key => $value) {
            if ($value) {
                if (gettype($value) == 'string') {
                    if (UTS::begins_with($value, "__k")) {
                        $value = str_replace("__k", "", $value);
                    }
                    $key = addslashes(trim($key));
                    $value = "'" . addslashes(trim($value)) . "'";
                }
                array_push($keys, $key);
                array_push($values, $value);
            }
        }
        $query = "INSERT INTO " . $table . " (" . implode(", ", $keys) . ") VALUES (" . implode(", ", $values) . ")";
        return ($is_scalar) ? +DBS::execute_scalar($query) : DBS::execute_nonquery($query);
    }

    public static function execute_update($table, $key_values, $condition_key_values)
    {
        $values = [];
        foreach ($key_values as $key => $value) {
            if ($value) {
                if (gettype($value) == "string") {
                    $value = (UTS::begins_with($value, "__k"))
                        ? str_replace("__k", "", $value)
                        : "'" . addslashes(trim($value)) . "'";
                }
            } else {
                $value = 'NULL';
            }
            array_push($values, $key . "=" . $value);
        }
        $conditions = [];
        foreach ($condition_key_values as $key => $value) {
            if ($value || (gettype($value) == 'integer' && $value >= 0)) {
                if (gettype($value) == 'string') $value = "'" . addslashes(trim($value)) . "'";
                array_push($conditions, $key . "=" . $value);
            }
        }
        $query = "UPDATE " . $table . " SET " . implode(", ", $values) . ((is_array($condition_key_values)) ? " WHERE " . implode(' AND ', $conditions) : '');
        return DBS::execute_nonquery($query);
    }

    public static function execute_delete($table, $condition_key_values)
    {
        $conditions = [];
        foreach ($condition_key_values as $key => $value) {
            if ($value || (gettype($value) == 'integer' && $value >= 0)) {
                if (gettype($value) == 'string') $value = "'" . addslashes(trim($value)) . "'";
                array_push($conditions, $key . "=" . $value);
            }
        }
        $query = "DELETE FROM " . $table . " WHERE "  . implode(" AND ", $conditions);
        return DBS::execute_nonquery($query);
    }

    public static function execute_select($table, $alias, $parameters, $conditions, $sort = NULL, $page = NULL, $type = NULL)
    {
        $query = "SELECT " . UTS::query_params($parameters)
            . " FROM " . $table . " AS " . $alias
            . ((count($conditions) > 0) ? " WHERE " . implode(" AND ", $conditions) : "")
            . (($sort) ? $sort->order_by() : "")
            . (($page) ? $page->db_limit() : "");
        //print_r($query);
        $results = (DBS::execute($query))->fetchAll();
        return (count($results) > 0) ? UTS::serializeObjects($results, $type) : null;
    }
    #endregion
}
