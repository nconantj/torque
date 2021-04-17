<?php
    require_once ('creds.php');
    
    class DBAccess {
        private $mysqli;
        
        function __construct($host, $user, $pass, $table) {
            $this->mysqli = new mysqli($host, $user, $pass, $table);
            if ($this->mysqli->connect_error) {
                throw new RuntimeException('mysqli connection error: ' . $this->mysqli->connect_error);
            }
        }
        
        function __destruct() {
            $this->mysqli->close();
        }
            
        public function db_die() {
            die ($this->mysqli->error);
        }
        
        private function execute_query($query_str) {
            return $this->mysqli->query($query_str);
        }
        
        public function get_fields($table) {
            $query_str = "SHOW COLUMNS FROM $table";
            return execute_query($query_str);
        }
        
        public function enumerate_rows($result) {    
            if (get_num_rows($result) > 0) {
                while ($row = get_assoc_row_data($result)) {
                    $dbfields[] = ($row['Field']);
                }
            }
        }
        
        public function get_num_rows($result) {
            return $result->num_rows;
        }
        
        private function get_assoc_row_data($result) {
            return $result->fetch_assoc();
        }
        
        public function add_column($table, $col_name, $col_type, $nullable, $default_val) {
            $query_str = "ALTER TABLE $table ADD $col_name $col_type " . ($nullable ? "" : "NOT NULL ") . " default '$default_val'";
            return execute_query($query_str);
        }
        
        public function insert_data ($table, $cols, $vals) {
            $query_str = "INSERT INTO $table (". implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
            return execute_query($query_str);
        }
    }
    
?>