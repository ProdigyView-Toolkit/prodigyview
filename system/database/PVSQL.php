<?php

trait PVSQL {
	
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
					$query .= self::parseOperators($key, $value, 'AND', '=', $first);
				else {
					if ($first)
						$query .= $key . ' = \'' . self::makeSafe($value) . '\'';
					else {
						$query .= ' AND ' . $key . ' = \'' . self::makeSafe($value) . '\'';
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

		$result = $this -> query($query);
		
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
			
			$query .= 'DELETE FROM FROM '.$args['table'] . ' ';
			
			if(is_array($args['join'])) {
				$query .= implode(',', $args['join']);
			} else {
				$query .= ' '.$args['join'];
			}
			
			if(!empty($args['where']))
				$query .= ' WHERE ';
			if(is_array($args['where'])) {
				$first = true;
				foreach($args['where'] as $key => $value) {
					if(is_array($value))
						$query .= self::parseOperators($key, $value, 'AND', '=', $first);
					else {
						if($first)
							$query .= $key.' = \''.self::makeSafe($value).'\'';
						else {
							$query .= ' AND '.$key.' = \''.self::makeSafe($value).'\'';
						}
					}
					
					$first = false;
				}
			} else {
				$query .= $args['where'];
			}
			
			$result = PVDatabase::query($query);	
	}

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
	protected static function parseOperators($column, $args = array(), $key = 'AND', $operator = '=', $first = true){
		
		$query = '';
		
		if(PVValidator::isInteger($key)){
			$key = 'AND';
		}
		
		foreach($args as $subkey => $arg) {
			
			if(($subkey == '>=' ||  $subkey ==  '>' || $subkey ==  '<' || $subkey ==  '<=' || $subkey ==  '!=') && !PVValidator::isInteger($subkey)) 
					$operator = $subkey;
			else if(!PVValidator::isInteger($subkey))
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
	
	
}
