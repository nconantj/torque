<?php
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
		
		public function change_db($new_db) {
			return $mysqli->select_db($db_name);
		}
            
        public function db_die() {
            die ($this->mysqli->error);
        }
        
        private function execute_query($query_str) {
            return $this->mysqli->query($query_str);
        }
        
        public function get_fields($table) {
            $query_str = "SHOW COLUMNS FROM $table";
            return $this->execute_query($query_str);
        }
        
        public function enumerate_rows($result) {    
            if ($this->get_num_rows($result) > 0) {
                while ($row = $this->get_assoc_row_data($result)) {
                    $dbfields[] = ($row['Field']);
                }
            }
        }
        
        public function get_num_rows($result) {
            return $result->num_rows;
        }
        
        public function get_assoc_row_data($result) {
            return $result->fetch_assoc();
        }
		
		public function get_row_array_assoc($result) {
			return $result->fetch_array(MYSQLI_ASSOC);
		}
		
		public function get_row_array_num($result) {
			return $result->fetch_array(MYSQLI_NUM);
		}
		
		public function get_row_array($result) {
			return $result->fetch_array();
		}
        
        public function add_column($table, $col_name, $col_type, $nullable, $default_val) {
            $query_str = "ALTER TABLE $table ADD $col_name $col_type " . ($nullable ? "" : "NOT NULL ") . " default '$default_val'";
            return $this->execute_query($query_str);
        }
        
        public function insert_data ($table, $cols, $vals) {
            $query_str = "INSERT INTO $table (". implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
            return $this->execute_query($query_str);
        }
		
		public function get_data ($table, $cols, $grp_cols = null, $where_str = null, $order_str = null) {
			$query_str = "SELECT " . implode(",",$cols) . " FROM $table";
			
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
		
		public function get_escape_string($str) {
			return $this->mysqli->real_escape_string($str);
		}
		
		private function collapse_assignments($assigments) {
			$result = array();
			$sep = '=';
			
			foreach( $assigments as $col => $val ) {
				$result[] = $col . $sep . $val;
			}
			
			return $result;
		}
		
		public function update_data($table, $assigments, $where_str) {
			$assign_str = implode(",", $this->collapse_assignments($assigments));
			$query_str = "UPDATE $table SET $assign_str WHERE $where_str";
			
			return $this->execute_query($query_str);
		}
		
		public function get_field_count($result) {
			return $result->field_count;
		}
		
		public function get_field_name($result, $col_num) {
			return $result->fetch_field_direct($col_num)->name;
		}
		
		public function delete_data ($table, $where_str) {
			$query_str = "DELETE FROM $table WHERE $where_str";
			return $this->execute_query($query_str);
		}
    }
    
?>