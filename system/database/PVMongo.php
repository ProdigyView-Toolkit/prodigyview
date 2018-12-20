<?php

interface DatabaseInterface {

	/**
	 * Because MongoDV does not have a true query, this function will act as a find, and pass the details to
	 * selectStatement method. This method is NOT recommended for usage.
	 * 
	 * @param array $query The same args passed into selectStatement
	 * 
	 * @return mixed
	 */
	public function query(array $query) {
		return $this -> selectStatement($query);

	}

	/**
	 * DO NOT USE. Please use preparedReturnLastInsert instead
	 */
	public function return_last_insert_query($query, $returnField = '', $returnTable = ''){
		return false;
	}

	/**
	 * DO NOT USE WITH MONGO
	 */
	public function resultRowCount($table) {
		return false;
	}

	/**
	 * DO NOT USE WITH MONGO
	 */
	public function fetchArray($result) {
		
	}

	/**
	 * DO NOT USE WITH MONGO
	 */
	public function fetchFields($result) {
		
	}

	/**
	 * DO NOT USE WITH MONGO, but if you do, it just returns the string. MongoDB
	 * does not really have sanization requirements.
	 */
	public function makeSafe($string) {
		return $string;
	}

	/**
	 * With the lastest version of the drive, MongoDB does not close connectons
	 */
	public function closeDB() {
		
	}

	/**
	 * DO NOT USE WITH MONGO
	 */
	public function getSchema($append_period = true) {
		
	}

	/**
	 * Dropping a collection is the same as truncating in Mongo,and this will call the drop method
	 * 
	 * @param string $tablename The name of the collection to drop
	 * @param array $options
	 * 
	 * @return mixed
	 */
	public function clearTableData($tablename, $options = '') {
		
		return $this->dropTable($table_name);
		
	}

	/**
	 * Detects if a collection exist
	 * 
	 * @param string $tablename The name of the collection
	 * @param string $schema Ignore, not used
	 * 
	 * @return boolean
	 */
	public function tableExist($tablename, $schema = '') {
		$collections = self::$link->getCollectionNames();
		
		$found = false;
		
		foreach($collections as $collection) {
			if($collection == $tablename) {
				$found = true;
			}
		}
		
		return found;
	}

	public function columnExist($table_name, $field_name);

	public function getSQLRandomOperator();

	public function formatData($string);

	public function dbAverageFunction($field);

	public function getDatabaseType();

	public function getConnectionName();

	public function getPagininationOffset($table, $join_clause = '', $where_clause = '', $current_page = 0, $results_per_page = 20, $order_by = '', $fields = 'COUNT(*) as count');

	public function getDatabaseLink();

	public function insertStatement($table_name, $data, $options = array()) {
		$result = preparedReturnLastInsert($table_name, '', '', $data, array(), $options);
	}

	public function updateStatement($table, $data, $wherelist, $options = array()) {
		$collection = self::_setMongoCollection($table, $options);

		if (class_exists('\\MongoDB\Driver\Manager')) {
			$result = $collection->updateMany($wherelist, array('$set' => $data), $options);
		} else {
			$result = $collection->update($wherelist, $data, $options);
		}
	}

	public function selectStatement(array $args, array $options = array()) {
		$where = (!empty($args['where'])) ? $args['where'] : array();
		$fields = (!empty($args['fields']) && $args['fields'] != '*') ? $args['fields'] : array();

		$collection = self::_setMongoCollection($args['table'], $options);

		if (isset($options['findOne']) && $options['findOne']) {

			$result = $collection->findOne($where, $fields);
		} else {
			$result = $collection->find($where, $fields);

			if (!empty($args['order_by']) && !class_exists('\\MongoDB\Driver\Manager')) {
				$result = $result->sort($args['order_by']);
			}

			if (!empty($args['limit']) && !class_exists('\\MongoDB\Driver\Manager')) {
				$result = $result->limit($args['limit']);
			}

			if (!empty($args['offset']) && !class_exists('\\MongoDB\Driver\Manager')) {
				$result = $result->skip($args['offset']);
			}

		}
	}

	public function deleteStatement(array $args, array $options = array()) {
		$collection = self::_setMongoCollection($args['table'], $options);
		$where = (!empty($args['where'])) ? $args['where'] : array();

		if (class_exists('\\MongoDB\Driver\Manager')) {
			$result = $collection->deleteMany($where, $options);
		} else {
			$result = $collection->remove($where, $options);
		}
	}

	public function preparedQuery($query, $data, $formats = '');

	public function preparedInsert($table_name, $data, $formats = array()) {

		$collection = self::_setMongoCollection($table);

		if (class_exists('\\MongoDB\Driver\Manager')) {
			$collection->insertOne($data);
		} else {
			$collection->insert($data);
		}

	}

	public function preparedReturnLastInsert($table_name, $returnField, $returnTable, $data, $formats = array(), $options = array()) {

		$collection = self::_setMongoCollection($table_name, $options);

		if (isset($options['batchInsert']) && $options['batchInsert']) {
			$collection->batchInsert($data, $options);
		} else if (isset($options['gridFS']) && isset($options['file']) && $options['gridFS']) {
			$id = $collection->storeFile($options['file'], $data, $options);
		} else {
			if (class_exists('\\MongoDB\Driver\Manager')) {
				$result = $collection->insertOne($data, $options);
			} else {
				$result = $collection->insert($data, $options);
			}

		}

		if (isset($options['batchInsert']) && $options['batchInsert']) {
			$id = $data;
		} else if (isset($options['gridFS']) == false) {
			if (class_exists('\\MongoDB\Driver\Manager')) {
				$id = $result->getInsertedId();
			} else {
				$id = $data['_id'];
			}
		}
	}

	public function preparedSelect($query, $data, array $formats = array(), array $options = array());

	public function selectPreparedStatement(array $args, array $options = array()) {

		$collection = self::_setMongoCollection(self::$link->$table_name);

		$result = $collection->find($args);

	}

	public function preparedUpdate($table, $data, $wherelist, $formats = array(), $whereformats = array(), $options = array()) {

		$collection = self::_setMongoCollection($table, $options);
		if (class_exists('\\MongoDB\Driver\Manager')) {
			$result = $collection->updateMany($wherelist, array('$set' => $data), $options);
		} else {
			$result = $collection->update($wherelist, $data, $options);
		}

	}

	public function preparedDelete($table, $wherelist = array(), $whereformats = array(), $options = array()) {
		$collection = self::_setMongoCollection($table, $options);
		if (class_exists('\\MongoDB\Driver\Manager')) {
			$result = $collection->deleteMany($wherelist, $options);
		} else {
			$result = $collection->remove($wherelist, $options);
		}
	}

	public function getPreparedPlaceHolder($count = 1);

	public function formatTableName($table_name, $append_schema = true, $append_prefix = true) {
		
		return $table_name;
		
	}

	protected function parseOperators($column, $args = array(), $key = 'AND', $operator = '=', $first = true){
		return $column;
	}

	protected function bindParameters(&$statement, &$params);

	protected function stmt_bind_assoc(&$stmt, &$out);

	public function createTable($table_name, $columns = array(), $options = array());

	public function addColumn($table_name, $column_name, $column_data = array(), $options = array()) {
		
	}

	public function formatColumn($name, $options = array()) {
		
	}

	protected function getAutoIncrement() {
		
	}

	protected function columnTypeMap($type) {
	}

	public function dropColumn($table_name, $column_name, $options = array()) {
		$collection = self::_setMongoCollection($table, $options);
		$update = array('$unset' => array($column_name => true));
		
		$this -> query($update);
	}

	public function dropTable($table_name, $options = array()) {
		$collection = self::_setMongoCollection($table, $options);

		$response = $collection->drop();

		return $response;
	}

	public function catchDBError();

	/**
	 * Sets the current MongoDB collection to use
	 *
	 * @param string $table_name Not really a table but a collection in a Mongo Database
	 * @param array $options Options to pass to Mongo collection
	 * 					- boolean gridFS Default is false, but if set to true, will use gridFS
	 *
	 * @return object The collection
	 */
	protected static function _setMongoCollection($table_name, $options = array()) {

		$defaults = array('gridFS' => false);

		$options += $defaults;

		if (class_exists('\\MongoDB\Driver\Manager')) {
			$collection = self::$link->selectCollection(self::$dbname, $table_name);
		} else {

			if ($options['gridFS']) {
				$collection = self::$link->getGridFS($table_name);
			} else {
				$collection = self::$link->$table_name;
			}
		}

		return $collection;
	}

}
