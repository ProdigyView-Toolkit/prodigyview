<?php

interface DatabaseInterface {
	
	public function query($query) {
			
		$result = pg_query(self::$link, $query);
		
		return $result;
	}
	
	public function return_last_insert_query($query, $returnField = '', $returnTable = '') {
		
		$result = pg_exec($query . " RETURNING $returnField ");
		$row = self::fetchArray($result);
		$id = $row[$returnField];
			
		return $id;
		
	}
	
	public function resultRowCount($result) {
		$count = pg_num_rows($result);
		
		return $count;
	}
	
	public function fetchArray($result) {
		$array = pg_fetch_array($result);
		
		return $array;
	}
	
	public function fetchFields($result) {
			
		$fields = pg_fetch_assoc($result);
		
		return $fields;
		
	}
	
	public function makeSafe($string) {
		if (is_array($string)) {
			$return_array = array();

			foreach ($string as $key => $value) {
				$return_array[$key] = $this -> makeSafe($value);
			}

		} else {
		
			$return_array = pg_escape_string(self::$link, $string);
		}
		
		return $return_array;
	}
	
	public function closeDB() {
		
		pg_close(self::$link);
		
	}
	
	public function getSchema($append_period = true){
			
		if (empty(self::$dbschema)) {
			$schema = '';
		} else if($append_period) {
			$schema = self::$dbschema . ".";
		} else {
			$schema = self::$dbschema;
		}

		return $schema;
	}
	
	public function clearTableData($tablename, $options = '');
	
	public function tableExist($tablename, $schema = '');
	
	public function columnExist($table_name, $field_name);
	
	public function getSQLRandomOperator();
	
	public function formatData($string);
	
	public function dbAverageFunction($field);
	
	public function getDatabaseType();
	
	public function getConnectionName();
	
	public function getPagininationOffset($table, $join_clause = '', $where_clause = '', $current_page = 0, $results_per_page = 20, $order_by = '', $fields = 'COUNT(*) as count');
	
	public function getDatabaseLink();
	
	public function insertStatement($table_name, $data, $options = array());
	
	public function updateStatement($table, $data, $wherelist, $options = array());
	
	public function selectStatement(array $args, array $options = array());
	
	public function deleteStatement(array $args, array $options = array());
	
	public function preparedQuery($query, $data, $formats = '');
	
	public function preparedInsert($table_name, $data, $formats = array());
	
	public function preparedReturnLastInsert($table_name, $returnField, $returnTable, $data, $formats = array(), $options = array() );
	
	public function preparedSelect($query, $data, array $formats = array(), array $options = array());
	
	public function selectPreparedStatement(array $args, array $options = array());
	
	public function preparedUpdate($table, $data, $wherelist, $formats = array(), $whereformats = array(), $options = array());
	
	public function preparedDelete($table, $wherelist = array(), $whereformats = array(), $options = array()) ;
	
	public function getPreparedPlaceHolder($count = 1);
	
	public function formatTableName($table_name, $append_schema = true, $append_prefix = true);
	
	protected function parseOperators($column, $args = array(), $key = 'AND', $operator = '=', $first = true);
	
	protected function bindParameters(&$statement, &$params);
	
	protected function stmt_bind_assoc(&$stmt, &$out);
	
	public function createTable($table_name, $columns = array(), $options = array());
	
	public function addColumn($table_name, $column_name, $column_data = array(), $options = array());
	
	public function formatColumn($name, $options = array());
	
	protected function getAutoIncrement();
	
	protected function columnTypeMap($type);
	
	public function dropColumn($table_name, $column_name, $options = array());
	
	public function dropTable($table_name, $options = array());
	
	public function catchDBError();
	
	
}
