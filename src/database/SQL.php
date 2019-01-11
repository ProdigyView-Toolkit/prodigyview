<?php
namespace prodigyview\database;

use prodigyview\util\Validator;


trait SQL {

	/**
	 * Takes in an array of values that is formated like a query, and parse it to become a SQL query. For example:
	 * 
	 * array('>' = '5') will become column > 5
	 * 
	 * @param string $column The name of column to do the comparison operation
	 * @param array $args The args in key value and subkey value. The keys are conditionals and the value are what te conditio is being compparied too
	 * @param string $key They conditional, either AND or OR for the query
	 * @param string $operator How to compare values
	 * @param boolean $first For recursive operation, is this the first value
	 * 
	 * @return string $query A query to execute
	 * @todo Rewrite this function and description for clarity
	 */
	public function parseOperators($column, $args = array(), $key = 'AND', $operator = '=', $first = true){
		
		$query = '';
		
		if(Validator::isInteger($key)){
			$key = 'AND';
		}
		
		foreach($args as $subkey => $arg) {
			
			if(($subkey == '>=' ||  $subkey ==  '>' || $subkey ==  '<' || $subkey ==  '<=' || $subkey ==  '!=') && !Validator::isInteger($subkey)) 
					$operator = $subkey;
			else if(!Validator::isInteger($subkey))
				$key = $subkey;
			
			if(is_array($arg)) {
				$query.= self::parseOperators($column, $arg, $key, $operator, $first);
			} else {
			
				if($arg == 'IS NULL' || $arg == 'IS NOT NULL' || $arg == 'IS TRUE' || $arg == 'IS NOT TRUE' || $arg == 'IS FALSE' || $arg == 'IS NOT FALSE' || $arg == 'IS UNKNOWN' || $arg == 'IS NOT UNKNOWN' ) {
					$operator = '';
						
					if(!$first) {
						$query .=  ' '.$key . ' '.$column. ' '.$operator.' ' . self::makeSafe($arg).' ';
				 	} else  {
				 	
				 		$query .=  ' '.$column. ' '.$operator.' ' . self::makeSafe($arg).' ';
				 	}
				} else {
				
					if(!$first) {
						$query .=  ' '.$key . ' '.$column. ' '.$operator.' \'' . self::makeSafe($arg).'\' ';
				 	} else  {
				 	
				 		$query .=  ' '.$column. ' '.$operator.' \'' . self::makeSafe($arg).'\' ';
				 	}
				}
			 
			}
			
			$first = false;	
		}//end foreach
		
		return $query;
	}

	public function columnTypeMap($type) {
			
		$type = strtolower(trim($type));

		$types = array(
			'integers' => array(
				'match' => array(
					'int',
					'integer',
					'numeric'
				),
				'database' => array(
					'mysql' => 'INT',
					'mssql' => 'INT',
					'postgresql' => 'INTEGER',
					'sqlite' => 'INTEGER'
				)
			),
			'bigintegers' => array(
				'match' => array('bigint'),
				'database' => array(
					'mysql' => 'BIGINT',
					'mssql' => 'BIGINT',
					'postgresql' => 'BIGINT',
					'sqlite' => 'INTEGER'
				)
			),
			'double' => array(
				'match' => array(
					'double',
					'float',
					'real'
				),
				'database' => array(
					'mysql' => 'DOUBLE',
					'mssql' => 'FLOAT',
					'postgresql' => 'DOUBLE PRECISION',
					'sqlite' => 'REAL'
				)
			),
			'string' => array(
				'match' => array(
					'string',
					'varchar',
					'character varying',
					'nchar',
					'native character',
					'nvarchar'
				),
				'database' => array(
					'mysql' => 'VARCHAR',
					'mssql' => 'VARCHAR',
					'postgresql' => 'CHARACTER VARYING',
					'sqlite' => 'TEXT'
				)
			),
			'text' => array(
				'match' => array(
					'text',
					'clob'
				),
				'database' => array(
					'mysql' => 'TEXT',
					'mssql' => 'TEXT',
					'postgresql' => 'TEXT',
					'sqlite' => 'TEXT'
				)
			),
			'blob' => array(
				'match' => array(
					'blob',
					'bytea'
				),
				'database' => array(
					'mysql' => 'BLOB',
					'mssql' => 'BLOB',
					'postgresql' => 'BYTEA',
					'sqlite' => 'TEXT'
				)
			),
			'boolean' => array(
				'match' => array('boolean'),
				'database' => array(
					'mysql' => 'BOOLEAN',
					'mssql' => 'BIT',
					'postgresql' => 'BOOLEAN',
					'sqlite' => 'INTEGER'
				)
			),
			'tinyint' => array(
				'match' => array(
					'tinyint',
					'smallint'
				),
				'database' => array(
					'mysql' => 'TINYINT',
					'mssql' => 'TINYINT',
					'postgresql' => 'SMALLINT',
					'sqlite' => 'INTEGER'
				)
			),
			'timestamp' => array(
				'match' => array('timestamp'),
				'database' => array(
					'mysql' => 'TIMESTAMP',
					'mssql' => 'datetime',
					'postgresql' => 'TIMESTAMP',
					'sqlite' => 'TEXT'
				)
			),
			'date' => array(
				'match' => array(
					'date',
					'date/time',
					'datetime'
				),
				'database' => array(
					'mysql' => 'TIMESTAMP',
					'mssql' => 'datetime',
					'postgresql' => 'TIMESTAMP',
					'sqlite' => 'TEXT'
				)
			),
			'serial' => array(
				'match' => array('serial'),
				'database' => array(
					'mysql' => 'SERIAL',
					'mssql' => 'unknown',
					'postgresql' => 'SERIAL',
					'sqlite' => 'INTEGER'
				)
			),
			'bigserial' => array(
				'match' => array('bigserial'),
				'database' => array(
					'mysql' => 'SERIAL',
					'mssql' => 'unknown',
					'postgresql' => 'BIGSERIAL',
					'sqlite' => 'INTEGER'
				)
			),
			'hstore' => array(
				'match' => array('hstore'),
				'database' => array(
					'mysql' => 'unknown',
					'mssql' => 'unknown',
					'postgresql' => 'HSTORE',
					'sqlite' => 'unknown'
				)
			),
			'uuid' => array(
				'match' => array(
					'uuid',
					'guid'
				),
				'database' => array(
					'mysql' => 'VARCHAR',
					'mssql' => 'GUID',
					'postgresql' => 'UUID',
					'sqlite' => 'TEXT'
				)
			),
			'ip' => array(
				'match' => array(
					'ip',
					'ipv4',
					'cidr'
				),
				'database' => array(
					'mysql' => 'VARCHAR',
					'mssql' => 'VARCHAR',
					'postgresql' => 'CIDR',
					'sqlite' => 'TEXT'
				)
			),
			'ipv6' => array(
				'match' => array('ipv6','inet', 'address','address/y'),
				'database' => array(
					'mysql' => 'VARCHAR',
					'mssql' => 'VARCHAR',
					'postgresql' => 'INET',
					'sqlite' => 'TEXT'
				)
			),
			'macaddr' => array(
				'match' => array('macaddr','macaddress'),
				'database' => array(
					'mysql' => 'VARCHAR',
					'mssql' => 'VARCHAR',
					'postgresql' => 'MACADDR',
					'sqlite' => 'TEXT'
				)
			),
			'json' => array(
				'match' => array('json'),
				'database' => array(
					'mysql' => 'JSON',
					'mssql' => 'varchar',
					'postgresql' => 'JSON',
					'sqlite' => 'TEXT'
				)
			),
		);

		foreach ($types as $key => $value) {
			if (in_array($type, $value['match'])) {
				$match = $value['database'][$this->_type];
				$match = $this->_applyFilter(get_class(), __FUNCTION__, $match, array('event' => 'return'));
				return $match;
			}//end if
		}//end for each

	}
	
	public function insertStatement($table_name, $data, $options = array()) {

		if (!empty($table_name)) {
			$first = 1;
			foreach ($data as $key => $value) {

				if ($first == 0) {
					$columns .= ' ,' . $key;
					$values .= ' ,\'' . $this->makeSafe($value) . '\' ';
				} else {
					$columns = $key;
					$values = ' \'' . $this->makeSafe($value) . '\' ';
				}

				$first = 0;
			}//end foreach

			$query = 'INSERT INTO ' . $table_name . '(' . $columns . ') VALUES(' . $values . ')';
			
			$result = $this->query($query);
		}

		return $result;
	}

	public function updateStatement($table, $data, $wherelist, $options = array()) {

		$query = 'UPDATE ' . $table . ' SET ';
		$params = array();
		$params_holder = array();

		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$query .= $key . ' = \'' . $this->makeSafe($value) . '\'';
			}//end foreach

		} else {
			$query .= $data;
		}

		if (is_array($wherelist) && !empty($wherelist)) {
			$query .= ' WHERE ';
			$first = true;

			foreach ($wherelist as $key => $value) {
				if (is_array($value))
					$query .= $this->parseOperators($key, $value, 'AND', '=', $first);
				else {
					if ($first)
						$query .= $key . ' = \'' . $this->makeSafe($value) . '\'';
					else {
						$query .= ' AND ' . $key . ' = \'' . $this->makeSafe($value) . '\'';
					}
				}

				$first = false;
			}//end foreach

		} else if (!empty($wherelist)) {
			$query .= ' WHERE ' . $wherelist;
		}

		$result = $this->query($query);

		return $result;
	}

	public function selectStatement(array $args, array $options = array()) {

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
			'paged' => false,
			'results_per_page' => 10,
			'current_page' => 0
		);

		$args += $default;

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
					$on = isset($value['on']) ? $value['on'] : '';

					$query .= $type . ' ' . $table . ' ' . $on;
				} else {
					$query .= ' JOIN ' . $value;
				}
			}
		} else {
			$query .= ' ' . $args['join'];
		}

		if (!empty($args['where']) && !is_array($args['where']))
			$query .= ' WHERE ' . $args['where'];
		else if (is_array($args['where']) && !empty($args['where'])) {
			$query .= ' WHERE ';
			$first = true;
			foreach ($args['where'] as $key => $value) {
				if (is_array($value))
					$query .= $this->parseOperators($key, $value, 'AND', '=', $first);
				else {
					if ($first)
						$query .= $key . ' = \'' . $this->makeSafe($value) . '\'';
					else {
						$query .= ' AND ' . $key . ' = \'' . $this->makeSafe($value) . '\'';
					}
				}

				$first = false;
			}
		}

		if (!empty($args['order_by']) && is_array($args['order_by'])) {
			$query .= ' ORDER BY ' . implode(',', $args['order_by']);
		} else if (!empty($args['order_by'])) {
			$query .= ' ORDER BY ' . $args['order_by'];
		}
		
		$result = $this->query($query);
		
		return $result;

	}

	public function deleteStatement(array $args, array $options = array()) {
		$default = array(
			'where' => '',
			'into' => '',
			'from' => '',
			'join' => '',
			'group_by' => '',
			'having' => '',
			'order_by' => '',
			'limit' => '',
			'offset' => '',
		);

		$args += $default;

		$query = '';

		$query .= 'DELETE FROM ' . $args['table'] . ' ';

		if (is_array($args['join'])) {
			$query .= implode(',', $args['join']);
		} else {
			$query .= ' ' . $args['join'];
		}

		if (!empty($args['where']))
			$query .= ' WHERE ';
		if (is_array($args['where'])) {
			$first = true;
			foreach ($args['where'] as $key => $value) {
				if (is_array($value))
					$query .= $this->parseOperators($key, $value, 'AND', '=', $first);
				else {
					if ($first)
						$query .= $key . ' = \'' . $this->makeSafe($value) . '\'';
					else {
						$query .= ' AND ' . $key . ' = \'' . $this->makeSafe($value) . '\'';
					}
				}

				$first = false;
			}
		} else {
			$query .= $args['where'];
		}

		$result = $this->query($query);

		return $result;
	}
}
