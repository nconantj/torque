<?php
    require_once ('creds.php');
    
    class DBAccess {
        private $con = false
        
        function __construct() {
            $this->con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            if (mysqli_connect_errno()) {
                throw new RuntimeException('mysqli connection error: ' . mysqli_connect_error());
            }
        }
        
        function __destruct() {
            mysql_close($this->con);
        }
            
        function db_die() {
            die ($mysqli_error($this->con));
        }
        
        function execute_query($query_str) {
            return mysqli_query($this->con, $query_str);
        }
        
        function get_fields($table) {
            $query_str = "SHOW COLUMNS FROM $table";
            return execute_query($query_str);
        }
        
        function enumerate_rows($result) {    
            if (get_num_rows($result) > 0) {
                while ($row = get_row_data($result)) {
                    $dbfields[] = ($row['Field']);
                }
            }
        }
        
        function get_num_rows($result) {
            return mysqli_num_rows($result);
        }
        
        function get_row_data($result) {
            return mysqli_fetch_assoc($result);
        }
        
        function add_column($table, $col_name, $col_type, $nullable, $default_val) {
            $query_str = "ALTER TABLE $table ADD $col_name $col_type " . ($nullable ? "" : "NOT NULL ") . " default '$default_val'";
            return execute_query($query_str);
        }
        
        function insert_data ($table, $cols, $vals) {
            $query_str = "INSERT INT $table (". implode("," $cols) . ") VALUES (" . implode(",", $vals) . ")";
            return execute_query($query_str);
        }
    }
?>