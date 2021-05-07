<?php

namespace \;

class DBAccess
{
    private $mysqli;

    public function __construct($host, $user, $pass, $table)
    {
        $this->mysqli = new mysqli($host, $user, $pass, $table);
        if ($this->mysqli->connect_error) {
            throw new RuntimeException('mysqli connection error: ' . $this->mysqli->connect_error);
        }
    }

    public function __destruct()
    {
        $this->mysqli->close();
    }

    public function changeDB($new_db)
    {
        return $this->mysqli->select_db($new_db);
    }

    public function dbDie()
    {
        die($this->mysqli->error);
    }

    private function executeQuery($query_str)
    {
        return $this->mysqli->query($query_str);
    }

    public function getFields($table)
    {
        $query_str = "SHOW COLUMNS FROM $table";
        return $this->execute_query($query_str);
    }

    public function enumerateRows($result)
    {
        if ($this->get_num_rows($result) > 0) {
            while ($row = $this->get_assoc_row_data($result)) {
                $dbfields[] = ($row['Field']);
            }
        }
    }

    public function getNumRows($result)
    {
        return $result->num_rows;
    }

    public function getAssocRowData($result)
    {
        return $result->fetch_assoc();
    }

    public function getRowArrayAssoc($result)
    {
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getRowArrayNum($result)
    {
        return $result->fetch_array(MYSQLI_NUM);
    }

    public function getRowArray($result)
    {
        return $result->fetch_array();
    }

    public function addColumn($table, $col_name, $col_type, $nullable, $default_val)
    {
        $query_str = "ALTER TABLE $table ADD $col_name $col_type " .
            ($nullable ? "" : "NOT NULL ") .
            " default '$default_val'";
        return $this->execute_query($query_str);
    }

    public function insertData($table, $cols, $vals)
    {
        $query_str = "INSERT INTO $table (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
        return $this->execute_query($query_str);
    }

    public function getData($table, $cols, $grp_cols = null, $where_str = null, $order_str = null)
    {
        $query_str = "SELECT " . implode(",", $cols) . " FROM $table";

        if (isset($grp_cols)) {
            $query_str .= " GROUP BY " . implode(",", $grp_cols);
        }

        if (isset($where_str)) {
            $query_str .= " WHERE $where_str";
        }

        if (isset($order_str)) {
            $query_str .= " ORDER BY $order_str";
        }

        return $this->execute_query($query_str);
    }

    public function getEscapeString($str)
    {
        return $this->mysqli->real_escape_string($str);
    }

    private function collapseAssignments($assigments)
    {
        $result = array();
        $sep = '=';

        foreach ($assigments as $col => $val) {
            $result[] = $col . $sep . $val;
        }

        return $result;
    }

    public function updateData($table, $assigments, $where_str)
    {
        $assign_str = implode(",", $this->collapse_assignments($assigments));
        $query_str = "UPDATE $table SET $assign_str WHERE $where_str";

        return $this->execute_query($query_str);
    }

    public function getFieldCount($result)
    {
        return $result->field_count;
    }

    public function getFieldName($result, $col_num)
    {
        return $result->fetch_field_direct($col_num)->name;
    }

    public function deleteData($table, $where_str)
    {
        $query_str = "DELETE FROM $table WHERE $where_str";
        return $this->execute_query($query_str);
    }
}
