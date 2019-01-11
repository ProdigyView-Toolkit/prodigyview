<?php

namespace prodigyview\database;

use prodigyview\design\InstanceObject;
use prodigyview\util\Validator;
use \resource;

if(!defined('PGSQL_CONNECT_FORCE_NEW')) {
	define('PGSQL_CONNECT_FORCE_NEW', 1);
}

if(!defined('PGSQL_BOTH')) {
	define('PGSQL_BOTH', 0);
}

if(!defined('PGSQL_ASSOC')) {
	define('PGSQL_ASSOC', 1);
}

class Postgresql implements DBInterface {
	
	use InstanceObject, SQL;
	
	protected $_link = null;
	
	protected $_host = null;
	
	protected $_port = null;
	
	protected $_database = null;
	
	protected $_schema = null;
	
	protected $_tablePrefix = null;
	
	protected $_tableSuffix = null;
	
	protected $_login = null;
	
	protected $_password = null;
	
	protected $_connectionType = null;
	
	protected $_connectionName = null;
	
	protected $_type = 'postgresql';
	
	protected $_fetchDataResultType = PGSQL_ASSOC;
	
	public function getHost() {
		return $this->_host;
	}
	
	public function getPort() {
		return $this->_port;
	}
	
	public function getLogin() {
		return $this->_login;
	}
	
	public function getDatabase() {
		return $this->_database;
	}
	
	public function setConnection(string $name, array $options = array()) {
			
		$defaults = array(
			'port'=> 5432,
			'schema'=> '',
			'password' => '',
			'prefix' => '',
			'suffix' => '',
			'connect_type' => PGSQL_CONNECT_FORCE_NEW
		);
		
		$options += $defaults;
		
		$this->_connectionName = $name;
		$this->_host = $options['host'];
		$this->_port = $options['port'];
		$this->_database = $options['database'];
		$this->_schema = $options['schema'];
		$this->_tablePrefix = $options['prefix'];
		$this->_tableSuffix = $options['suffix'];
		$this->_login = $options['login'];
		$this->_password = $options['password'];
		$this->_connectionType = $options['connect_type'];
		
		//Change the result type
		if(isset($options['fetch_data_result_type'])) {
			$this->_fetchDataResultType=$options['fetch_data_result_type'];
		}
		
		$options += $defaults;
		
	}
	
	public function connect() {
		
		$this->_link = pg_connect('host=' . $this->_host  . ' port=' . $this->_port . ' dbname=' . $this->_database . ' user=' . $this->_login . ' password=' . $this->_password, $this->_connectionType );
		
		return $this->_link;
		
	}
	
	public function isActive() {
		
		if($this->_link) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Execute a query.
	 * 
	 * @param string $query
	 */
	public function query($query) {

		$result = pg_query($this->_link, $query);

		return $result;
	}

	/**
	 * Returns the last interested id of a query.
	 * 
	 * @param string $query The query to be executed
	 * @param string $returnField The returning field
	 * @param string $returnTable Void in this case, not used
	 */
	public function returnLastInsert($query, $returnField = '', $returnTable = '') {

		$result = pg_exec($query . " RETURNING $returnField ");
		$row = $this->fetchArray($result);
		$id = $row[$returnField];

		return $id;

	}

	/**
	 * Gets the number of rows found in a result
	 * 
	 * @param resource $result The result
	 * 
	 * @return int
	 */
	public function resultRowCount($result) {
		$count = pg_num_rows($result);

		return $count;
	}
	
	/**
	 * Fetches the the results of a row in the form of an array
	 * 
	 * @param resource $result A resource from an executed query
	 * 
	 * @return
	 */
	public function fetchArray($result, $row = null, $type = null) {
		
		if(!$type) {
			$type = $this->_fetchDataResultType;
		}
		
		$array = pg_fetch_array($result, $row, $type);

		return $array;
	}

	public function fetchFields($result, $row = null) {

		$fields = pg_fetch_assoc($result, $row);

		return $fields;

	}

	public function makeSafe($string) {
		if (is_array($string)) {
			$return_array = array();

			foreach ($string as $key => $value) {
				$return_array[$key] = $this->makeSafe($value);
			}

		} else {

			$return_array = pg_escape_string($this->_link, $string);
		}

		return $return_array;
	}

	public function closeDB() {

		pg_close($this->_link);

	}

	public function getSchema($append_period = true) {

		if (empty($this->_schema)) {
			$schema = '';
		} else if ($append_period) {
			$schema = $this->_schema . ".";
		} else {
			$schema = $this->_schema;
		}

		return $schema;
	}

	public function clearTableData($tablename, $options = '') {
		$tablename = $this->makeSafe($tablename);

		$query = "TRUNCATE TABLE $tablename $options";
		$this->query($query);
	}

	public function tableExist($tablename, $schema = '') {

		$query = "select * from information_schema.tables where table_name  = '$tablename' ";
		if (!empty($schema))
			$query .= "AND table_schema = '$schema'";

		$result = $this->query($query);
		$count = $this->resultRowCount($result);

		if ($count <= 0 && empty($count)) {
			return FALSE;
		}

		return TRUE;
	}

	public function columnExist($table_name, $field_name, $schema = false) {
		
		$schema_query = '';
		
		if($this->_schema) {
			$schema_query = 'table_schema = \''. $this->getSchema($schema) . '\' AND';
		}

		$query = "SELECT * FROM information_schema.columns WHERE {$schema_query} table_name = '$table_name' AND column_name = '$field_name';";

		$result = $this->query($query);
		$count = $this->resultRowCount($result);
		$this->_notify(get_class() . '::' . __FUNCTION__, $count, $result, $table_name, $field_name);
		
		if ($count <= 0) {
			return FALSE;
		}

		return TRUE;

	}

	public function getSQLRandomOperator() {
		$function = 'RANDOM()';

		return $function;
	}

	public function formatData($string) {

	}

	public function dbAverageFunction($field) {

		$function = ' AVG(' . $field . ') ';

		return $function;
	}

	public function getDatabaseType() {
		return $this->_type;
	}

	public function getConnectionName() {
		return $this->_connectionName;
	}

	public function getPagininationOffset($table, $join_clause = '', $where_clause = '', $current_page = 0, $results_per_page = 20, $order_by = '', $fields = 'COUNT(*) as count') {
		if (!empty($where_clause) && !is_array($where_clause))
			$where_clause .= ' WHERE ' . $args['where'];
		else if (is_array($where_clause) && !empty($where_clause)) {
			$query = ' WHERE ';
			$first = true;
			foreach ($where_clause as $key => $value) {
				if (is_array($value))
					$query .= $this->parseOperators($key, $value, 'AND', '=', $first);
				else {
					if ($first) {
						if (Validator::isInteger($key)) {
							$query .= ' ' . $value . ' ';
						} else {
							$query .= $key . ' = \'' . $this->makeSafe($value) . '\'';
						}
					} else {
						if (Validator::isInteger($key)) {
							$query .= ' AND ' . $value . ' ';
						} else {
							$query .= ' AND ' . $key . ' = \'' . $this->makeSafe($value) . '\'';
						}
					}
				}

				$first = false;
			}

			$where_clause = $query;
		}

		$query = "SELECT $fields FROM $table $join_clause $where_clause";

		$result = $this->query($query);
		$total_pages = $this->fetchArray($result);
		$total_pages = $total_pages['count'];
		$from_clause = '';

		//Get The Start Page
		if ($current_page) {
			$start_location = ($current_page - 1) * $results_per_page;
		} else {
			$start_location = 0;
		}

		$last_page = ceil($total_pages / $results_per_page);

		if ($current_page < 1) {
			$current_page = 1;
		} else if ($current_page > $last_page) {
			$current_page = $last_page;
		}

		$database_type = $this->getDatabaseType();

		$limit_offset = ' LIMIT ' . ($current_page - 1) * $results_per_page . ' OFFSET ' . $results_per_page;

		$return_array = array(
			'limit_offset' => $limit_offset,
			'current_page' => $current_page,
			'last_page' => $last_page,
			'start_location' => $start_location,
			'total_pages' => $total_pages,
			'from_clause' => $from_clause
		);

		return $return_array;

	}

	public function getDatabaseLink() {
		return $this->_link;
	}


	public function preparedQuery($query, $data, $formats = '') {
		$result = pg_prepare($this->_link, '', $query);
		$result = pg_execute($this->_link, '', $data);

		return $result;
	}

	public function preparedInsert($table_name, $data, $formats = array()) {

		$query = 'INSERT INTO ' . $table_name;
		$values = '';
		$placeholders = '';

		if (!empty($data)) {
			$values .= '(';
			$placeholders .= '(';
		}//end if

		$first = 1;
		$count = 0;
		foreach ($data as $key => $value) {
			if ($first) {
				$values .= $key;
				$placeholders .= ' ' . $this->getPreparedPlaceHolder($count + 1) . ' ';
			} else {
				$values .= ' , ' . $key;
				$placeholders .= ', ' . $this->getPreparedPlaceHolder($count + 1) . ' ';
			}

			$first = 0;
			$count++;
		}//end foreach

		if (!empty($data)) {
			$values .= ')';
			$placeholders .= ')';
		}//end if

		$query .= $values . ' VALUES' . $placeholders;

		$template_name = md5($query);

		$result = pg_query_params($this->_link, 'SELECT name FROM pg_prepared_statements WHERE name = $1', array($template_name));

		if (pg_num_rows($result) == 0) {
			$result = pg_prepare($this->_link, $template_name, $query);

		}

		$result = pg_execute($this->_link, $template_name, $data);

		return $result;

	}

	public function preparedReturnLastInsert($table_name, $returnField, $returnTable, $data, $formats = array(), $options = array()) {

		$query = 'INSERT INTO ' . $table_name;
		$values = '';
		$placeholders = ' VALUES';

		if (!empty($data)) {
			$values .= '(';
			$placeholders .= '(';
		}//end if

		$first = 1;
		$params = array();
		$count = 0;
		foreach ($data as $key => $value) {
			if ($first) {
				$values .= $key;
				$placeholders .= ' ' . $this->getPreparedPlaceHolder($count + 1) . ' ';
			} else {
				$values .= ' , ' . $key;
				$placeholders .= ', ' . $this->getPreparedPlaceHolder($count + 1) . ' ';
			}

			$params[$key] = (isset($formats[$count])) ? $formats[$count] : 's';
			$count++;
			$first = 0;
		}//end foreach

		if (!empty($data)) {
			$values .= ')';
			$placeholders .= ')';
		}//end if

		$query .= $values . $placeholders;

		$template_name = md5($query);

		$template_name = md5($query . " RETURNING $returnField");

		$result = pg_query_params($this->_link, 'SELECT name FROM pg_prepared_statements WHERE name = $1', array($template_name));

		if (pg_num_rows($result) == 0) {
			$result = pg_prepare($this->_link, $template_name, $query . " RETURNING $returnField");
		}

		$result = pg_execute($this->_link, $template_name, $data);

		if ($result == false) {
			$this->catchDBError();
		}
		$row = $this->fetchArray($result);

		$id = $row[$returnField];

		return $id;

	}

	public function preparedSelect($query, $data, array $formats = array(), array $options = array()) {

		$params = array();

		$count = 0;

		foreach ($data as $key => $value) {
			$params[$key] = (isset($formats[$count])) ? $formats[$count] : 's';
			$count++;
		}//end foreach

		if (isset($options['prequery']) && !empty($options['prequery'])) {
			$query = $options['prequery'] . $query;
		}

		if (isset($options['postquery']) && !empty($options['postquery'])) {
			$query = $query . $options['postquery'];
		}

		$result = pg_prepare($this->_link, '', $query);
		$result = pg_execute($this->_link, '', $data);

		return $result;

	}

	public function preparedSelectStatement(array $args, array $options = array()) {
		$default = array(
			'fields' => '*',
			'where' => '',
			'into' => '',
			'from' => '',
			'join' => '',
			'group_by' => '',
			'having' => '',
			'order_by' => '',
			'limit' => '',
			'offset' => '',
			'prequery' => '',
			'postquery' => '',
		);

		$args += $default;
		
		$placeheld_variables = array();

		$query = '';

		if (is_array($args['fields'])) {
			$fields = implode(',', $args['fields']);
		} else {
			$fields = $args['fields'];
		}

		$query .= 'SELECT ' . $fields . ' ';

		$query .= ' FROM ' . $args['table'] . ' ';

		if (is_array($args['join'])) {
			foreach ($args['join'] as $key => $value) {
				if (is_array($value)) {
					$type = isset($value['type']) ? $value['type'] : ' NATURAL JOIN ';
					$table = isset($value['table']) ? $value['table'] : $key;

					$query .= $type . ' ' . $table;
				} else {
					$query .= ' JOIN ' . $value;
				}
			}
		} else {
			$query .= ' ' . $args['join'];
		}

		if (!empty($args['where']))
			$query .= ' WHERE ';
		if (is_array($args['where'])) {
			$first = true;
			$count = 0;
			
			foreach ($args['where'] as $key => $value) {
				if (is_array($value))
					$query .= $this->parseOperators($key, $value, 'AND', '=', $first);
				else {
					if ($first) {
						if (Validator::isInteger($key)) {
							$query .= ' ' . $value . ' ';
						} else {
							$placeheld_variables[] = $value;
							$count+=1;
							$query .= $key . ' = ' . $this->getPreparedPlaceHolder($count) . ' ';
						}
					} else {
						if (Validator::isInteger($key)) {
							$query .= ' AND ' . $value . ' ';
						} else {
							$placeheld_variables[] = $value;
							$count+=1;
							$query .= ' AND ' . $key . ' = ' . $this->getPreparedPlaceHolder($count) . ' ';
						}
					}
				}

				$first = false;
			}
		} else {
			$query .= $args['where'];
		}

		if (!empty($args['group_by']) && is_array($args['group_by'])) {
			$query .= ' GROUP BY ' . implode(',', $args['group_by']);
		} else if (!empty($args['group_by'])) {
			$query .= ' GROUP BY ' . $args['group_by'];
		}

		if (!empty($args['order_by']) && is_array($args['order_by'])) {
			$query .= ' ORDER BY ' . implode(',', $args['order_by']);
		} else if (!empty($args['order_by'])) {
			$query .= ' ORDER BY ' . $args['order_by'];
		}

		if (!empty($args['limit'])) {
			$query .= ' LIMIT ' . $args['limit'];
		}

		if (isset($options['prequery']) && !empty($options['prequery'])) {
			$query = $options['prequery'] . $query;
		}

		if (!empty($args['offset'])) {
			$query .= ' OFFSET ' . $args['offset'];
		}

		if (isset($options['postquery']) && !empty($options['postquery'])) {
			$query = $query . $options['postquery'];
		}
	
		$query_name = md5($query);
		
		// Prepare a query for execution
		$result = pg_query_params($this->_link, 'SELECT name FROM pg_prepared_statements WHERE name = $1', array($query_name));

		if (pg_num_rows($result) == 0) {
			$result = pg_prepare($this->_link, $query_name, $query);
		}
		
		// Execute the prepared query. 
		$result = pg_execute($this->_link, $query_name, $placeheld_variables);
		
		//$result = $this->query($query);
		
		return $result;
	}

	public function preparedUpdate($table, $data, $wherelist, $formats = array(), $whereformats = array(), $options = array()) {
		$query = 'UPDATE ' . $table . ' SET ';
		$params = array();
		$params_holder = array();

		$first = 1;
		$count = 0;
		foreach ($data as $key => $value) {
			$params[] = (isset($formats[$count])) ? $formats[$count] : 's';
			$params_holder[] = $value;

			if ($first) {
				$query .= $key . '=' . $this->getPreparedPlaceHolder($count + 1) . ' ';
			} else {
				$query .= ',' . $key . '=' . $this->getPreparedPlaceHolder($count + 1) . ' ';
			}
			$count++;
			$first = 0;
		}//end foreach

		$first = 1;
		if (is_array($wherelist) && !empty($wherelist)) {
			$query .= ' WHERE ';
			$count2 = 0;
			foreach ($wherelist as $key => $value) {
				$params[] = (isset($wherelist[$count2])) ? $formats[$count2] : 's';
				$params_holder[] = $value;

				if ($first) {
					$query .= $key . '=' . $this->getPreparedPlaceHolder($count + 1) . ' ';
				} else {
					$query .= ' AND ' . $key . '=' . $this->getPreparedPlaceHolder($count + 1) . ' ';
				}
				$count++;
				$count2++;
				$first = 0;
			}//end foreach
		}//end if is_array and not emptys

		$template_name = md5($query);

		$result = pg_query_params($this->_link, $query, $params_holder);

		$result = pg_query_params($this->_link, 'SELECT name FROM pg_prepared_statements WHERE name = $1', array($template_name));

		if (pg_num_rows($result) == 0) {
			$result = pg_prepare($this->_link, $template_name, $query);
		}

		$result = pg_execute($this->_link, $template_name, $params_holder);

		return $result;
	}

	public function preparedDelete($table, $wherelist = array(), $whereformats = array(), $options = array()) {
		$query = 'DELETE FROM ' . $table;

		if (is_array($wherelist) && !empty($wherelist)) {
			$params = array();
			$query .= ' WHERE ';
			$count = 0;
			$first = 1;
			foreach ($wherelist as $key => $value) {
				$params[$key] = (isset($wherelist[$count])) ? $formats[$count] : 's';
				if ($first) {
					$query .= $key . '=' . $this->getPreparedPlaceHolder($count + 1) . ' ';
				} else {
					$query .= ' AND ' . $key . '=' . $this->getPreparedPlaceHolder($count + 1) . ' ';
				}

				$first = 0;
				$count++;
			}//end foreach
		}

		$template_name = md5($query);

		$result = pg_query_params($this->_link, 'SELECT name FROM pg_prepared_statements WHERE name = $1', array($template_name));

		if (pg_num_rows($result) == 0) {
			$result = pg_prepare($this->_link, $template_name, $query);
		}

		$result = pg_execute($this->_link, $template_name, $wherelist);

		return $result;
	}

	public function getPreparedPlaceHolder($count = 1) {

		$placeholder = '$' . $count;

		return $placeholder;
	}

	public function formatTableName($table_name, $append_schema = true, $append_prefix = true) {
		$table_name = $this->_tablePrefix . $table_name;

		if ($this->getSchema() && $append_schema) {
			$table_name = $this->getSchema() . $table_name;
		}

		return $table_name;
	}


	public function bindParameters(&$statement, &$params) {

		$args = array();
		$args[] = implode('', array_values($params));
		foreach ($params as $paramName => $paramType) {
			$args[] = &$params[$paramName];
			$params[$paramName] = null;
		}

		call_user_func_array(array(
			&$statement,
			'bind_param'
		), $args);
	}

	public function stmt_bind_assoc(&$stmt, &$out) {

		$data = mysqli_stmt_result_metadata($stmt);
		$fields = array();
		$out = array();

		$fields[0] = $stmt;
		$count = 1;

		while ($field = mysqli_fetch_field($data)) {
			$fields[$count] = &$out[$field->name];
			$count++;
		}
		@call_user_func_array(mysqli_stmt_bind_result, $fields);

	}

	public function createTable($table_name, $columns = array(), $options = array()) {

		$defaults = array(
			'format_table' => false,
			'execute' => true,
			'return_query' => true,
			'primary_key' => '',
		);
		
		$options += $defaults;

		$column_query = '';

		if (!empty($columns) && is_array($columns)) {
			$first = 1;
			foreach ($columns as $column_name => $column) {
				$column_query .= (!$first) ? ',' : '';
				$column_query .= $this->formatColumn($column_name, $column);
				$first = 0;
			}
		}

		if (!empty($options['primary_key']))
			$column_query .= ',PRIMARY KEY(' . $options['primary_key'] . ')';

		if (!empty($column_query))
			$column_query = '(' . $column_query . ')';

		if ($options['format_table'])
			$table_name = $this->formatTableName($table_name);

		$query = 'CREATE TABLE ' . $table_name . ' ' . $column_query . ';';

		if ($options['execute'])
			$this->query($query);

		if ($options['return_query'])
			return $query;
	}

	public function addColumn($table_name, $column_name, $column_data = array(), $options = array()) {

		$defaults = array(
			'format_table' => false,
			'execute' => true,
			'return_query' => true,
		);
		
		$options += $defaults;

		if ($options['format_table'])
			$table_name = $this->formatTableName($table_name);

		$query = 'ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $this->formatColumn($column_name, $column_data) . ';';

		if ($options['execute'])
			$this->query($query);

		if ($options['return_query'])
			return $query;

	}

	public function formatColumn($name, $options = array()) {

		$defaults = array(
			'primary_key' => false,
			'unique' => false,
			'not_null' => true,
			'type' => 'string',
			'precision' => '',
			'default' => null,
			'auto_increment' => false,
			'execute_default' => false
		);

		$options += $defaults;

		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$options = $filtered['options'];

		$precision = (!empty($options['precision'])) ? '(' . $options['precision'] . ')' : '';
		$null = ($options['not_null'] == true) ? 'NOT NULL' : 'NULL';

		if (isset($options['default']) && is_callable($options['default'])) {
			$default = $options['default']();
		} else if ($options['execute_default']) {
			$default = (isset($options['default'])) ? 'DEFAULT ' . $options['default'] . '' : '';
		} else {
			$default = (isset($options['default'])) ? 'DEFAULT \'' . $options['default'] . '\'' : '';
		}
		
		$auto_increment = ($options['auto_increment'] == true) ? $this->getAutoIncrement() : '';
		
		$unique = ($options['unique'] == true) ? 'UNIQUE' : '';

		if ($options['auto_increment'] == true) {
			$options['type'] = 'SERIAL';
		}

		$query = $name . ' ' . $this->columnTypeMap($options['type']) . $precision . ' ' . $null . ' ' . $default . ' ' . $auto_increment . ' ' . $unique;

		return $query;
	}

	public function getAutoIncrement() {
		return '';
	}


	public function dropColumn($table_name, $column_name, $options = array()) {

		$defaults = array(
			'format_table' => false,
			'execute' => true,
			'return_query' => true,
		);
		
		$options += $defaults;

		if ($options['format_table'])
			$table_name = $this->formatTableName($table_name);

		$query = 'ALTER TABLE ' . $table_name . ' DROP COLUMN ' . $column_name . ';';

		if ($options['execute'])
			$this->query($query);

		if ($options['return_query'])
			return $query;
	}

	public function dropTable($table_name, $options = array()) {

		$defaults = array(
			'format_table' => false,
			'execute' => true,
			'return_query' => true,
		);
		$options += $defaults;

		if ($options['format_table'])
			$table_name = $this->formatTableName($table_name);

		$query = 'DROP TABLE ' . $table_name . ';';

		if ($options['execute'])
			$this->query($query);

		if ($options['return_query'])
			return $query;

	}

	public function catchDBError() {

	}

}
