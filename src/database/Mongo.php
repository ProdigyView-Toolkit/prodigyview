<?php

namespace prodigyview\database;

use prodigyview\design\InstanceObject;
use prodigyview\util\Validator;

class Mongo implements DBInterface {
	
	use InstanceObject, SQL;
	
	protected $_link = null;
	
	protected $_host = null;
	
	protected $_port = null;
	
	protected $_database = null;
	
	protected $_schema = null;
	
	protected $_login = null;
	
	protected $_password = null;
	
	protected $_connectionName = null;
	
	protected $_type = 'mongo';
	
	protected $_row = array();
	
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
			'port'=> '',
			'schema'=> '',
			'password' => '',
		);
		
		$options += $defaults;
		
		$this->_connectionName = $name;
		$this->_host = $options['host'];
		$this->_port = $options['port'];
		$this->_database = $options['database'];
		$this->_schema = $options['schema'];
		$this->_login = $options['login'];
		$this->_password = $options['password'];
		
	}
	
	public function connect() {
		
		if(class_exists('\\MongoDB\Driver\Manager')) {	
			$this->_link = new \MongoDB\Client('mongodb://'.$this->_login.':'.$this->_password.'@'.$this->_host, [], ['typeMap' => ['root' => 'array', 'document' => 'array']]);
			$this->_link->selectDatabase($this->_database);
		} else if(class_exists ('MongoClient')) {
			$this->_link = new \MongoClient('mongodb://'.$this->_login.':'.$this->_password.'@'.$this->_host);
			$this->_link = $this->_link ->selectDB($this->_database);
		} else {
			$this->_link = new Mongo('mongodb://'.$this->_login.':'.$this->_password.'@'.$this->_host);
			$this->_link = $this->_link ->selectDB($this->_database);
		}
		
				
		return $this->_link;
	}
	
	public function isActive() {
		
		if($this->_link) {
			return true;
		}
		
		return false;
	}

	/**
	 * Because MongoDV does not have a true query, this function will act as a find, and pass the details to
	 * selectStatement method. This method is NOT recommended for usage.
	 * 
	 * @param array $query The same args passed into selectStatement
	 * 
	 * @return mixed
	 */
	public function query($query) {
		return $this -> selectStatement($query);

	}

	/**
	 * DO NOT USE. Please use preparedReturnLastInsert instead
	 */
	public function returnLastInsert($query, $returnField = '', $returnTable = ''){
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
		
		$data = array();
		
		if(!$result) {
			return $data;
		}
		
		foreach ($result as $key => $value) {
			$data[] = $value;
		}
		
		return $data;
	}

	/**
	 * DO NOT USE WITH MONGO
	 */
	public function fetchFields($result) {
		return $this->fetchArray($result);
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
	public function clearTableData($table_name, $options = '') {
		
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
		
		$collections = $this->_link->selectDatabase($this->_database)->listCollections();
		
		
		foreach($collections as $collection) {
			if($collection->getName() == $tablename) {
				return true;
			}
		}
		
		return false;
	}

	
	public function columnExist($table_name, $field_name) {
		
	}

	
	public function getSQLRandomOperator() {
		
	}

	/**
	 * DO NOT USE IN MONGO
	 */
	public function formatData($string){
		
	}

	/**
	 * Finds the average of a function. Should be used in conjunction with the
	 * aggregation framework.
	 * 
	 * @param mixed $field Either a single field or an array of fields
	 * 
	 * @return array Returns the queryin the form of an array
	 */
	public function dbAverageFunction($field) {
		
		$query = array('$avg' => $field);
		
		return $query;
		
	}

	/**
	 * Gets the database type
	 * 
	 * @return string
	 */
	public function getDatabaseType() {
		return $this->_type;
	}

	public function getConnectionName() {
		return $this->_connectionName;
	}

	/**
	 * DO NOT USE WITH MONGO
	 */
	public function getPagininationOffset($table, $join_clause = '', $where_clause = '', $current_page = 0, $results_per_page = 20, $order_by = '', $fields = 'COUNT(*) as count') {
		
	}

	/**
	 * Gets the handler for the current connection
	 */
	public function getDatabaseLink() {
		
		return $this->_link;
		
	}

	
	/**
	 * Inserts items into the database.Also can perform batch inserts
	 * 
	 * @param string $table_name The name of the collection to insert into
	 * @param array $data The data to insert into the database. For batch inserting, seperate data at top level keys
	 * @param array $options Options to further define how to insert the data
	 * 					-boolean batchInsert Indicates to do a patch insert of multiple recods. Default is false
	 * 					-boolean gridFS Indicates if the database part of the gridFS system
	 * 
	 * @return boolean Returns booleans based on the success or failure of the insert
	 */
	public function insertStatement($table_name, $data, $options = array()) {
		$collection = $this->_setMongoCollection($table_name, $options);

		$result = null;
		
		if (isset($options['batchInsert']) && $options['batchInsert']) {
			$result = $collection->batchInsert($data, $options);
		} else if (isset($options['gridFS']) && isset($options['file']) && $options['gridFS']) {
			$result = $collection->storeFile($options['file'], $data, $options);
		} else {
			if (class_exists('\\MongoDB\Driver\Manager')) {
				$result = $collection->insertOne($data, $options);
			} else {
				$result = $collection->insert($data, $options);
			}

		}
		
		return $result;
	}

	/**
	 * Updates an existing record in the database.
	 * 
	 * @param string $table The name of collection to insert the data
	 * @param array $data The new data to update the record(s) with
	 * @param array $wherelist The conditionals for deciding what data to update
	 */
	public function updateStatement($table, $data, $wherelist = array(), $options = array()) {
		
		$collection = $this->_setMongoCollection($table, $options);
		
		if(isset($wherelist['_id']) && !($wherelist['_id'] instanceof \MongoDB\BSON\ObjectId)) {
			$wherelist['_id'] = new \MongoDB\BSON\ObjectId($wherelist['_id']);
		}

		$result = null;
		
		if (class_exists('\\MongoDB\Driver\Manager')) {
			$result = $collection->updateMany($wherelist, array('$set' => $data), $options);
		} else {
			$result = $collection->update($wherelist, $data, $options);
		}
		
		return $result;
	}

	/**
	 * Queries MongoDB for records based on criteria
	 * 
	 * @param string $args The arguements that can be used to finding record(s)
	 * 						-array 	'where': Conditionals of what to look for
	 * 						-array 	'fields': Fields to return
	 * 						-boolean	'findOne': Will only find one result, otherwise default will look for multiple records
	 * 						-string	'order_by' How to order the datasets returned
	 * 						-int		'limit': Limit the amount of results returned
	 * 						-int		'offset':Have an offset from the results return
	 * 
	 * @return array $results
	 */
	public function selectStatement(array $args, array $options = array()) {
		
		$where = (!empty($args['where'])) ? $args['where'] : array();
		$fields = (!empty($args['fields']) && $args['fields'] != '*') ? $args['fields'] : array();
		
		if(isset($where['_id']) && !($where['_id'] instanceof \MongoDB\BSON\ObjectId)) {
			$where['_id'] = new \MongoDB\BSON\ObjectId($where['_id']);
		}
		
		$collection = $this->_setMongoCollection($args['table'], $options);

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
		
		return $result;
	}

	public function deleteStatement(array $args, array $options = array()) {
		$collection = $this->_setMongoCollection($args['table'], $options);
		$where = (!empty($args['where'])) ? $args['where'] : array();
		
		if(isset($where['_id']) && !($where['_id'] instanceof \MongoDB\BSON\ObjectId)) {
			$where['_id'] = new \MongoDB\BSON\ObjectId($where['_id']);
		}

		if (class_exists('\\MongoDB\Driver\Manager')) {
			$result = $collection->deleteMany($where, $options);
		} else {
			$result = $collection->remove($where, $options);
		}
	}

	public function preparedQuery($query, $data, $formats = '') {
		
	}

	public function preparedInsert($table_name, $data, $formats = array()) {

		$collection = $this->_setMongoCollection($table_name);

		if (class_exists('\\MongoDB\Driver\Manager')) {
			$collection->insertOne($data);
		} else {
			$collection->insert($data);
		}

	}

	public function preparedReturnLastInsert($table_name, $returnField, $returnTable, $data, $formats = array(), $options = array()) {

		$collection = $this->_setMongoCollection($table_name, $options);
		

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
		
		return $id->__toString();
	}

	public function preparedSelect($query, $data, array $formats = array(), array $options = array()) {
		
	}

	public function preparedSelectStatement(array $args, array $options = array()) {

		return $this->selectStatement($args);

	}

	public function preparedUpdate($table, $data, $wherelist, $formats = array(), $whereformats = array(), $options = array()) {

		$collection = $this->_setMongoCollection($table, $options);
		
		if(isset($wherelist['_id']) && !($wherelist['_id'] instanceof \MongoDB\BSON\ObjectId)) {
			$wherelist['_id'] = new \MongoDB\BSON\ObjectId($wherelist['_id']);
		}
		
		if (class_exists('\\MongoDB\Driver\Manager')) {
			$result = $collection->updateMany($wherelist, array('$set' => $data), $options);
		} else {
			$result = $collection->update($wherelist, $data, $options);
		}

	}

	/**
	 * Delete item(s) in a collection.
	 * 
	 * @param string $table The name of the table
	 * @param array $wherelist A list of conditionals to look for when choosing the items
	 * @param array $whereformats ignored for MongoDB
	 * @param array $options Options used when deleting the items
	 * 
	 * @return void
	 */
	public function preparedDelete($table, $wherelist = array(), $whereformats = array(), $options = array()) {
		$collection = $this->_setMongoCollection($table, $options);
		
		if(isset($wherelist['_id'])) {
			$wherelist['_id'] = new \MongoDB\BSON\ObjectId($wherelist['_id']);
		}
		
		if (class_exists('\\MongoDB\Driver\Manager')) {
			$result = $collection->deleteMany($wherelist, $options);
		} else {
			$result = $collection->remove($wherelist, $options);
		}
	}

	/**
	 * Do Not Use in MongoDB
	 */
	public function getPreparedPlaceHolder($count = 1) {
		
	}

	/**
	 * Do Not Use in MongoDB
	 */
	public function formatTableName($table_name, $append_schema = true, $append_prefix = true) {
		
		return $table_name;
		
	}

	/**
	 * Do Not Use in MongoDB
	 */
	public function parseOperators($column, $args = array(), $key = 'AND', $operator = '=', $first = true){
		return $column;
	}


	/**
	 * Do Not Use in MongoDB
	 * 
	 * @todo figureout how
	 */
	public function createTable($table_name, $columns = array(), $options = array()) {
		
		//$this->command($table_name, $options);
	}
	
	/**
	 * Do Not Use in MongoDB
	 */
	public function addColumn($table_name, $column_name, $column_data = array(), $options = array()) {
		
	}

	public function formatColumn($name, $options = array()) {
		
	}

	public function getAutoIncrement() {
		return '';
	}

	public function columnTypeMap($type) {
		return $type;
	}

	public function dropColumn($table_name, $column_name, $options = array()) {
		$collection = $this->_setMongoCollection($table_name, $options);
		$update = array('$unset' => array($column_name => true), 'table' => $table_name);
		
		$this -> query($update);
	}

	public function dropTable($table_name, $options = array()) {
		$collection = $this->_setMongoCollection($table_name, $options);
		
		$response = $collection->drop();

		return $response;
	}

	public function catchDBError() {
		
	}

	/**
	 * Sets the current MongoDB collection to use
	 *
	 * @param string $table_name Not really a table but a collection in a Mongo Database
	 * @param array $options Options to pass to Mongo collection
	 * 					- boolean gridFS Default is false, but if set to true, will use gridFS
	 *
	 * @return object The collection
	 */
	protected function _setMongoCollection($table_name, $options = array()) {

		$defaults = array('gridFS' => false);

		$options += $defaults;

		if (class_exists('\\MongoDB\Driver\Manager')) {
			$collection = $this->_link->selectCollection($this->_database, $table_name);
		} else {

			if ($options['gridFS']) {
				$collection = $this->_link->getGridFS($table_name);
			} else {
				$collection = $this->_link->$table_name;
			}
		}

		return $collection;
	}

}
