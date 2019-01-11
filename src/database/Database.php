<?php

namespace prodigyview\database;

use prodigyview\design\StaticObject;
use prodigyview\util\Validator;

 //If Mysql is Not installed, will have to be set to null
 if(!defined('MYSQLI_REPORT_ERROR')) {
 	define('MYSQLI_REPORT_ERROR', null);
 }

/**
 * Database controls the connections to various databases ranging from Mysql to MongoDB.
 * 
 * For future development, the class needs to be written, but it offers a lot of powerful features that including prepared statements, schema manipulation, sanitization, and other features.
 * 
 * Example:
 * ```php
 * //Initialize the class
 * Database::init();
 * 
 * //Two Different Configurations
 * $mysql_options = array(
 * 	'dbhost' => 'localhost',
 * 	'dbuser' => 'jondoe',
 * 	'dbpass'=>'abc123',
 * 	'dbtype'=>'mysql',
 * 	'dbname'=>'example1',
 * 	'dbport'=>3306
 * );
 * 
 * //Add The Connection
 * Database::addConnection('connection1', $mysql_options);
 * 
 * $postgres_options = array(
 *     'dbhost' => 'localhost',
 *     'dbuser' => 'janedoe',
 *     'dbpass'=>'doeraeme',
 *     'dbtype'=>'postgresql',
 *     'dbname'=>'example2',
 *     'dbport'=>5432
 * );
 * 
 * //Add The Connection
 * Database::addConnection('connection2', $postgres_options);
 * 
 * //Connect To the Mysql Database
 * Database::setDatabase('connection1');
 * 
 * //Sanitize input
 * $value = Database::makeSafe('SELECT ItemName, ItemDescription FROM Items WHERE ItemNumber = 999; DROP TABLE USERS ');
 * 
 * //Execute A Query
 * Database::query("INSERT INTO users(name) VALUES(${value})");
 * 
 * //Change the Database connection
 * Database::setDatabase('connection2');
 * ```
 * 
 * @todo break apart class into seperate database handlers
 * @package system
 */ 
class Database  {
			
	use StaticObject;

	
	/**
	 * MYSQL Connection indicator
	 */
	private static $mySQLConnection = 'mysql';
	
	/**
	 * Postgresql Connection Indicator
	 */
	private static $postgreSQLConnection = 'postgresql';
	
	/**
	 * Oracle connection indicator
	 */
	private static $oracleConnection = 'oracle';
	
	/**
	 * MSSQL connection indicator
	 */
	private static $msSQLConnection = 'mssql';
	
	/**
	 * SQL Light connection indicatir
	 */
	private static $sqLiteConnection = 'sqlite';
	
	/**
	 * MongoDB connection dicator
	 */
	private static $mongoConnection = 'mongo';

	/**
	 * An array of possible connections that have been added
	 */
	private static $connections = array();
	
	/**
	 * The current database connection being referenced
	 */
	private static $current_connecton = '';
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initializes the class.
	 * 
	 * @param array $config Configuration options to pass into the class
	 * 
	 * @return void
	 */
	public static function init(array $config = array(), bool $lock = true) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		if(!self::$_initialized) {
			
	
			self::$connections = array();
		
			if($lock) {
				self::$_initialized = true;
			}
		}

	}//end init

	/**
	 * Add a connection to the database class. The connection can be later
	 * used by calling the function Database::setDatbase().
	 *
	 * Example
	 *
	 * $connection=array(
	 * 	'dbhost'=>'localhost',
	 * 	'dbuser'=>'admin',
	 * 	'dbpass'=>'abc123'
	 * 	'dbname'=>'mydb',
	 * 	'dbtype'=>'postgresql'
	 * );
	 *
	 * Database::addConnection('connection_1',$connection);
	 *
	 * @param mixed $connection_name Connection name can either be a string or integer.
	 * @param array $args And array that contains the information for connecting to the database.
	 * 			- 'dbhost' _string_: The host or ip the database is on
	 * 			- 'dbuser' _string_: The username to connected to the database
	 * 			- 'dbpass' _string_: The password the user uses to connect to the database
	 * 			- 'dbtype' _string_: The type of database. Options are mysql - postgresql -mssql
	 * 			- 'dbname' _string_: The name of the database on the host
	 * 			- 'dbport' _string_: Optional. The port that is used to connect to the database
	 * 			- 'dbschema' _string_: Optional. The schema the database is on (generally used in PostgreSQL)
	 * 			- 'dbprefix' _string_: Optional. A prefix that will be placed in front of every table.
	 *
	 * @return void
	 * @access public
	 */
	public static function addConnection(string $connection_name, array $args) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $connection_name, $args);

		$defaults = array(
			'type' => '', 
		);
		
		$args += $defaults;
		$args = self::_fixConnectionStrings($args);

		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		
		if($args['type']== self::$mySQLConnection) {
			$connection = new Mysql();
		} else if($args['type']== self::$postgreSQLConnection) {
			$connection = new Postgresql();
		} else if($args['type']== self::$mongoConnection) {
			$connection = new Mongo();
		} else if(!empty($args['class'])) {
			$connection = $args['class'];
		}
		
		$connection->setConnection($connection_name, $args);
		
		self::_setConnection($connection_name, $connection);

		self::_notify(get_class() . '::' . __FUNCTION__, $connection_name, $args);
	}

	/**
	 * Fixes the legacy connection strings
	 * 
	 * @param array $args cConnection arguements
	 * 
	 * @return array $args The args reformatted if they were incorrect
	 */
	private static function _fixConnectionStrings(array $args) {
		
		if(isset($args['dbhost'])) {
			$args['host']=$args['dbhost'];
		}
		
		if(isset($args['dbname'])) {
			$args['database']=$args['dbname'];
		}
		
		if(isset($args['dbuser'])) {
			$args['login']=$args['dbuser'];
		}
		
		if(isset($args['dbpass'])) {
			$args['password']=$args['dbpass'];
		}
		
		if(isset($args['dbtype'])) {
			$args['type']=$args['dbtype'];
		}

		if(isset($args['dbschema'])) {
			$args['schema']=$args['dbschema'];
		}
		
		if(isset($args['dbport'])) {
			$args['port']=$args['dbport'];
		}
		
		return $args;
	}

	/**
	 * Set the database to one of the configurations passed in the Database::addConnection method.
	 * This method can be used to change in between connections
	 *
	 * Example:
	 * Database::setDatabase('sql');
	 *
	 * @param string $connection_name: The ID of connection to connect with
	 * @param boolean $connect Attemtp to connect to the database
	 * @param boolean $close_connection Close the current connection before the switch
	 * 
	 * @return void
	 * @access public
	 */
	public static function setDatabase(string $connection_name, bool $connect = true, bool $close_connection = false) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $profile_id);

		if($close_connection) {
			self::closeDB();
		}

		$connection_name = self::_applyFilter(get_class(), __FUNCTION__, $connection_name, array('event' => 'args'));
		
		self::$current_connecton = $connection_name;
		
		if($connect) {
			self::connect();
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $connection_name);
	}
	
	public static function hasConnection($connection_name) {
		return !empty(static::$connections[$connection_name]);
	}
	
	private static function _setConnection($key, &$value) {
		static::$connections[$key]= &$value;
		
		if(count(static::$connections) === 1) {
			self::setDatabase($key, false);
		}
	}
	
	private static function &_getConnection($key, $connect = true) {
		
		if(empty(static::$connections[$key])) {
			throw new NoDatabaseException('No database connection has been set.');
		}
		
		if(!static::$connections[$key]->isActive()) {
			static::$connections[$key]->connect();
		}
		return static::$connections[$key];
	}

	/**
	 * Connect the that database based on the creditionals
	 * in the PHP file.
	 *
	 * @return void
	 * @access private
	 */
	private static function connect() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$connection = self::_getConnection(self::$current_connecton);
		
		if(!$connection->isActive()) {
			$connection->connection();
		}

		self::_notify(get_class() . '::' . __FUNCTION__);
	}//end private

	/**
	 * Executes a SQL Query.passed to the function. The query passed
	 * should be sanitized for malicous code before being processed.
	 *
	 * Example:
	 * $query='Select * FROM TABLE';
	 * $result=Database::query($query);
	 *
	 * @param string $query A SQL query
	 * @return object $result Returns an object result related to the query passed
	 * @access public
	 */
	public static function query($query) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $query);

		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection->query($query);

		self::_notify(get_class() . '::' . __FUNCTION__, $query, $result);
		
		$result = self::_applyFilter(get_class(), __FUNCTION__, $result, array('event' => 'return'));

		return $result;
	}//end query

	/**
	 * Returns the id of the last inserted string into the databse.
	 * returnField and returnTable are generally optional but required
	 * for databases such as PostgreSQL and MSSSQL
	 *
	 * Example:
	 * $query="INSERT INTO TABLE('Test Data') VALUES('abc', '123')";
	 * $id=Database::return_last_insert_query($query, 'id', 'TABLE');
	 *
	 * @param string $query The query thing to be executed
	 * @param string $returnField The field that is auto incremented and will be returned
	 * @param string $returnTable The table the auto-incremented value exist in
	 * @return mixed $id The id of the last inserted field
	 * @access public
	 */
	public static function return_last_insert_query($query, $returnField = '', $returnTable = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $query, $returnField, $returnTable);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'query' => $query, 
			'returnField' => $returnField, 
			'returnTable' => $returnTable
		), array('event' => 'args'));
		
		$query = $filtered['query'];
		$returnField = $filtered['returnField'];
		$returnTable = $filtered['returnTable'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$id = $connection->returnLastInsert($query, $returnField, $returnTable);

		self::_notify(get_class() . '::' . __FUNCTION__, $id, $query, $returnField, $returnTable);
		
		$id = self::_applyFilter(get_class(), __FUNCTION__, $id, array('event' => 'return'));

		return $id;
	}//end return_last_insert_query

	/**
	 * Get the number of rows return in a SELECT sql
	 * statement. Function with automatically decide which
	 * database to use.
	 *
	 * Example:
	 * $result=Database::query("SELECT * FROM TABLE");
	 * $count=Database::resultRowCount($result);
	 *
	 * @param object $result A result from a query
	 * 
	 * @return int $count The number of rows in that result.
	 * @access public
	 */
	public static function resultRowCount($result) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $result);

		$result = self::_applyFilter(get_class(), __FUNCTION__, $result, array('event' => 'args'));
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$count = $connection-> resultRowCount($result);

		self::_notify(get_class() . '::' . __FUNCTION__, $count, $result);
		
		$count = self::_applyFilter(get_class(), __FUNCTION__, $count, array('event' => 'return'));

		return $count;
	}//end result row count

	/**
	 * Fetches the data in each row retrieved from a
	 * result. Results are retuned as array
	 *
	 * Example:
	 * $result=Database::query('SELECT title, description FROM TABLE');
	 * while($row=Database::fetchArray($result)){
	 * 		echo $row['title'];
	 * }
	 *
	 * @param object result: A result from a query object
	 * @return array row: An assoctive array of a row from a table
	 */
	public static function fetchArray($result) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $result);

		$result = self::_applyFilter(get_class(), __FUNCTION__, $result, array('event' => 'args'));
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$data = $connection->fetchArray($result);

		self::_notify(get_class() . '::' . __FUNCTION__, $data, $result);
		
		$data = self::_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'return'));

		return $data;
	}//end fetchArray

	/**
	 * Fetches the data in each row retrieved from a
	 * result. The results are compiled into an object
	 * and returned.
	 *
	 * Example:
	 * $result=Database::query('SELECT title, description FROM TABLE');
	 * while($row=Database::fetchArray($result)){
	 * 		echo $row['title'];
	 * }
	 *
	 * @param object $result A result from a query object
	 * @return array $row An assoctive array of a row from a table
	 */
	public static function fetchFields($result) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $result);

		$result = self::_applyFilter(get_class(), __FUNCTION__, $result, array('event' => 'args'));
		
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$fields = $connection->fetchFields($result);

		self::_notify(get_class() . '::' . __FUNCTION__, $fields, $result);
		
		$fields = self::_applyFilter(get_class(), __FUNCTION__, $fields, array('event' => 'return'));

		return $fields;

	}//end fetchArray

	/**
	 * Sanitizes information before it is inserted into the database. Should be
	 * used on all user input to ensure security. Can sanitize a single string or
	 * an array of data.
	 *
	 * Example::
	 * $name=Database::makeSafe($_POST['name']);
	 * $number=Database::makeSafe($_POST['number']);
	 * Database::query("INSERT INTO TABLE(name, number) VALES('$name', '$number');
	 *
	 * @param mixed $string String can either be a string or an array of data
	 * @return mixed $sanitized_data If the input is a string, a string will be return
	 * if the input is an array, an array will be returned/
	 * @access public
	 */
	public static function makeSafe($string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));

		$connection = self::_getConnection(self::$current_connecton);
		
		$string = $connection->makeSafe($string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'return'));

		return $string;

	}//end fetchArray

	/**
	 * Closes a database connection depending on the connection
	 * that has been set
	 *
	 * @return void
	 * @access public
	 */
	public static function closeDB() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$connection = self::_getConnection(self::$current_connecton);
		
		$connection->closeDB();

		self::_notify(get_class() . '::' . __FUNCTION__);

	}//end close()

	/**
	 * Returns the schema that is being used for this database connection. A
	 * '.' will be appended to the name of the schema if one exist. Schemas
	 * are only necessary for postgresql and db2
	 *
	 * Example:
	 * $table_name=Database::getSchema.'contacts';
	 * $query="INSERT INTO $table_name(name, phone) VALUES('John Smith', '999-9999')";
	 * Database::query($query);
	 * 
	 * @param boolean $append_period Will appaned a period to the schema name
	 *
	 * @return string $schema Returns the name of the current schema.
	 * @access public
	 */
	public static function getSchema($append_period = true) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$schema = $connection->getSchema($append_period);

		self::_notify(get_class() . '::' . __FUNCTION__, $schema);
		
		$schema = self::_applyFilter(get_class(), __FUNCTION__, $schema, array('event' => 'return'));

		return $schema;
	}

	/**
	 * Truncates/Removes all information from a table.
	 *
	 * @param string $tablename The name of the table to clear
	 * @param string $options Options to be added at the end of the SQL query
	 *
	 * @return void
	 * @access public
	 */
	public static function clearTableData($tablename, $options = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $tablename, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'tablename' => $tablename, 
			'options' => $options
		), array('event' => 'args'));
		
		$tablename = $filtered['tablename'];
		$options = $filtered['options'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$query = $connection->clearTableData($tablename, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $query, $tablename, $options);

	}//end clearTableData

	/**
	 * Checks to see of a certain table exist within a database.
	 *
	 * Example:
	 * if(!Database::tableExist('contacts')){
	 * 		//Create table code
	 * }
	 *
	 * @param string $tablename The name of the table being checked if it exist
	 * @param string $schema Add a schema to check against
	 * 
	 * @return boolean $exist Will be true if the tabe exist, else false;
	 * @access public
	 */
	public static function tableExist($tablename, $schema = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $tablename);

		$tablename = self::_applyFilter(get_class(), __FUNCTION__, $tablename, array('event' => 'args'));

		$connection = self::_getConnection(self::$current_connecton);
		
		$exist = $connection-> tableExist($tablename, $schema);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $exist ,$tablename);
		
		return $exist;
		
	}//end

	/**
	 * Checkes if a column exist with a table. Make sure to enter
	 * the schema.table_name if needed.
	 *
	 * Example:
	 * if(!Database::columnExist('test.contacts', 'first_name' )){
	 * 		//Code to create table
	 * }
	 *
	 * @param string $table_name The name of the table to be checked
	 * @param string $field_name The name of the column to check if exist
	 *
	 * @return boolean $exist Returns true if exist, otherwise return false
	 */
	public static function columnExist($table_name, $field_name) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name, $field_name);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table_name' => $table_name, 
			'field_name' => $field_name
		), array('event' => 'args'));
		
		$table_name = $filtered['table_name'];
		$field_name = $filtered['field_name'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$exist = $connection->columnExist($table_name, $field_name);

		self::_notify(get_class() . '::' . __FUNCTION__, $exist, $table_name, $field_name);

		return $exist;
		
	}//end fieldexist

	/**
	 * Returns the function for getting a random variable. The
	 * function returned is dependent on the database that
	 * is set.
	 *
	 * Example:
	 * $query="SELECT * TABLE ORDER BY ".PVDATABASE::getSQLRandomOperator;
	 *
	 * @return string $avg_function
	 * @access public
	 */
	public static function getSQLRandomOperator() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$function = $connections->getSQLRandomOperator();

		self::_notify(get_class() . '::' . __FUNCTION__, $function);
		
		$function = self::_applyFilter(get_class(), __FUNCTION__, $function, array('event' => 'return'));

		return $function;
	}//end getSQLRandomOperator

	/**
	 * Data entered into the database sometimes has characters such as
	 * '/' added to it. This function will remove those characters
	 *
	 * Example:
	 * $name=Database::formatData($row['name']);
	 * OR
	 * $row=Database::formatData($row);
	 *
	 * @param mixed string: Either a string or array to format
	 * @return mixed data: Data with database characters removed
	 */
	public static function formatData($string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));

		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection->formatData($string);		

		return $result;
	}//end formatRow

	/**
	 * The average function is a function used to get the averge
	 * of fields in a database. This function returns the AVG function
	 * for the set database.
	 *
	 * Example:
	 * $query="SELECT ".Database:::dbAverageFunction('age')." as average_age FROM Table
	 *
	 * @param string field: The Field whose average value will be returned
	 * @return: string average_function: The function needed to get the average value ina SQL string
	 */
	public static function dbAverageFunction($field) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $field);

		$field = self::_applyFilter(get_class(), __FUNCTION__, $field, array('event' => 'args'));
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$function = $connection->dbAverageFunction($field);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $function, $field);
		
		$function = self::_applyFilter(get_class(), __FUNCTION__, $function, array('event' => 'return'));

		return $function;
	}//end getAverageDB

	/**
	 * Returns the current databse being used.
	 *
	 * @return string database: The database being used
	 */
	public static function getDatabaseType() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$type = $connection->getDatabaseType();

		self::_notify(get_class() . '::' . __FUNCTION__, $type);
		
		$type = self::_applyFilter(get_class(), __FUNCTION__, $type, array('event' => 'return'));

		return $type;
	}
	
	/**
	 * Returns the name of the current connection being used in the database.
	 * 
	 * @return string $connection_name The name of the current connect
	 * @access public
	 */
	public static function getConnectionName() {
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$name = $connection->getConnectionName();

		self::_notify(get_class() . '::' . __FUNCTION__, $name);
		
		$name = self::_applyFilter(get_class(), __FUNCTION__, $name, array('event' => 'return'));

		return $name;
	}

	/**
	 * Returns paginate values. This function handles pagination depending on the database
	 * being used.
	 *
	 * @param string $table The main table to call pagination from
	 * @param string $join_clause Any tables that are joined in this query
	 * @param string $where_clause Where SQL statement
	 * @param int $current_page The current page. All pages or done by pageNumber-1. 0 is the first page
	 * @param int $results_per_page The number of results to return per page
	 * @param string $order_by How to order the results.
	 * @param string $fields How to count the results, default is 'COUNT(*) as count'
	 *
	 * @return array results: Returns the
	 */
	public static function getPagininationOffset($table, $join_clause = '', $where_clause = '', $current_page = 0, $results_per_page = 20, $order_by = '', $fields = 'COUNT(*) as count') {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table, $join_clause, $where_clause, $current_page, $results_per_page, $order_by);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table' => $table, 
			'join_clause' => $join_clause, 
			'where_clause' => $where_clause, 
			'current_page' => $current_page, 
			'results_per_page' => $results_per_page, 
			'order_by' => $order_by
		), array('event' => 'args'));
		
		$table = $filtered['table'];
		$join_clause = $filtered['join_clause'];
		$where_clause = $filtered['where_clause'];
		$current_page = $filtered['current_page'];
		$results_per_page = $filtered['results_per_page'];
		$order_by = $filtered['order_by'];
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$data = $connection->getPagininationOffset($table, $join_clause, $where_clause, $current_page, $results_per_page, $order_by, $fields);

		self::_notify(get_class() . '::' . __FUNCTION__, $return_array, $table, $join_clause, $where_clause, $current_page, $results_per_page, $order_by);
		
		$data = self::_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'return'));

		return $data;

	}//end

	/**
	 * Every connection to a database has what is known as a link to that database.
	 * For external madification  of the link, this method will retun the current link.
	 *
	 * @return dbojbect link: Connection to the set database.
	 */
	public static function getDatabaseLink() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$link = $connection->getDatabaseLink();

		self::_notify(get_class() . '::' . __FUNCTION__, $link);
		
		$link = self::_applyFilter(get_class(), __FUNCTION__, $link , array('event' => 'return'));

		return $link;
	}//end getDatabaseLink

	/**
	 * Insert information into the databas without explicitly writing the
	 * query.Does not use a prepared statement.
	 *
	 * @param string table_name: The name of the information is being inserted into.
	 * @param array data: Information to be inserted into that table.The key is the column and the key's value is the colums value.
	 * @param array options: Options that can be used for altering the connection.
	 *
	 * @return void
	 * @access public
	 */
	public static function insertStatement($table_name, $data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name, $data, $options);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table_name' => $table_name, 
			'data' => $data, 
			'options' => $options
		), array('event' => 'args'));
		
		$table_name = $filtered['table_name'];
		$data = $filtered['data'];
		$options = $filtered['options'];
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection->insertStatement($table_name, $data, $options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $result, $table_name, $data, $options);
		
		$result = self::_applyFilter(get_class(), __FUNCTION__,  $result , array('event' => 'return'));
		
		return $result;
	}//end insertIntoDatabase
	
	/**
	 * Update record(s) in the database depending on the arguements specified in the wherelist
	 * 
	 * @param string $table The name of the table to update
	 * @param array $data The data to update in key => value ( column => value ) format
	 * @param array $wherelist Options defined on where to update the value
	 * @param array $options Extra options when updating a table
	 * 
	 * @return void
	 * @access public
	 */
	public static function updateStatement($table, $data, $wherelist, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table, $data, $wherelist, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table' => $table, 
			'data' => $data, 
			'wherelist' => $wherelist, 
			'options' => $options
		), array('event' => 'args'));
		
		$table = $filtered['table'];
		$data = $filtered['data'];
		$options = $filtered['options'];
		$wherelist = $filtered['wherelist'];
		
		$connection = self::_getConnection(self::$current_connecton);

		$result = $connection->updateStatement($table, $data, $wherelist, $options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $result, $table, $data, $wherelist, $options);
		$result = self::_applyFilter(get_class(), __FUNCTION__, $result, array('event' => 'return'));

		return $result;

	}//edn preparedUpdate
	
	/**
	 * Creates a select statement to query data in the database.
	 * 
	 * @param array $args An array of arguement that define the select statement
	 * @param array $options Options to alter the select statement
	 * 
	 * @return $mixed $results 
	 * @access public
	 */
	public static function selectStatement(array $args, array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args, $options);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'args' => $args, 
			'options' => $options
		), array('event' => 'args'));
		
		$args= $filtered['args'];
		$options = $filtered['options'];
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection->selectStatement($args, 
		$options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $result, $args, $options);
		
		$result = self::_applyFilter(get_class(), __FUNCTION__,  $result , array('event' => 'return'));
		
		return $result;
	}

	/**
	 * Deletes an item from the database
	 * 
	 * @param array $args Arguements that define how the query will be created
	 * @param array $options Options that define how the query will run
	 * 
	 * @return object $result The result of the query
	 */
	public static function deleteStatement(array $args, array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args, $options);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'args' => $args, 
			'options' => $options
		), array('event' => 'args'));
		
		$args= $filtered['args'];
		$options = $filtered['options'];
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection-> deleteStatement($args,$options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $result, $args, $options);
		
		$result = self::_applyFilter(get_class(), __FUNCTION__,  $result , array('event' => 'return'));
		
		return $result;
	}

	/**
	 * Executes a prepared Query that will be inserted into the database. Function still needs
	 * work before being used.
	 * 
	 * @param string $query
	 * @param array $data
	 * @param string $formats
	 *
	 * @todo fix
	 */
	public static function preparedQuery($query, $data, $formats = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $query, $data, $formats);
		
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$connection->preparedQuery($query, $data, $formats);

	}//end preparedQuery

	/**
	 * Function needs improvment.
	 * 
	 * @param string $table_name
	 * @param array $data
	 * @param array $formats
	 * 
	 * @access public
	 * @todo write better code
	 */
	public static function preparedInsert($table_name, $data, $formats = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name, $data, $formats);

		$connection = self::_getConnection(self::$current_connecton);
		
		$connection->preparedInsert($table_name, $data, $formats);

	}//end preparedInsert

	/**
	 * Inserts a query into the database and returns the id of the field that was last inserted.
	 * The query will be a prepared statement.
	 *
	 * @param string table_name The name of the table the information will be inserted into.
	 * @param string returnField The field that will be returned as the ID. Used in postgresql..
	 * @param string returnTable The table the returnField is in. Used for MSSQL.
	 * @param array $data The data to be inserted in the format of the key being the column and the
	 * key's value being the data.
	 * @param array $formats Still in progress. Formats a preparted statemet.
	 * @param array $options Options mainly used for Mongo
	 * 
	 * @access public
	 * @todo write better code
	 */
	public static function preparedReturnLastInsert($table_name, $returnField, $returnTable, $data, $formats = array(), $options = array() ) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name, $returnField, $returnTable, $data, $formats);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table_name' => $table_name, 
			'data' => $data, 
			'returnField' => $returnField, 
			'returnTable' => $returnTable, 
			'formats' => $formats
		), array('event' => 'args'));
		
		$table_name = $filtered['table_name'];
		$returnField = $filtered['returnField'];
		$returnTable = $filtered['returnTable'];
		$data = $filtered['data'];
		$formats = $filtered['formats'];
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$id = $connection->preparedReturnLastInsert($table_name, $returnField, $returnTable, $data, $formats, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $id, $table_name, $returnField, $returnTable, $data, $formats);
		
		$id = self::_applyFilter(get_class(), __FUNCTION__, $id, array('event' => 'return'));
		
		return $id;
	}//end preparedReturnLastInsert

	/**
	 * Executes a prepared select statement. Complex statements are complex enough that the data must be
	 * formated outside. The passed query should already have the ? inserted for values. The data array passed should
	 * correspond to that values.Futures version will have a select statement that handles the data in a better way.
	 *
	 * @param string $query A query of formatted data to be inserted into the database.
	 * @param array $data Data to be inserted into the database. The key should be the column name and the value
	 * should be the column's value.
	 * @param array $formats The formats for a prepared statement
	 * @param array $options Options than can be used to alter the query and its function
	 * 			-prequery	_string_: SQL to add before the query
	 * 			-postquery _string_: Additonal information to add at the end of the normal
	 *
	 * @return data result: Retuns a result that will need to be run through fetch process.
	 * @access public
	 * @todo write better code
	 */
	public static function preparedSelect($query, $data, array $formats = array(), array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $query, $data, $formats);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'query' => $query, 
			'data' => $data, 
			'formats' => $formats,  
			'options' => $options 
		), array('event' => 'args'));
		
		$query = $filtered['query'];
		$data = $filtered['data'];
		$formats = $filtered['formats'];
		$options = $filtered['options'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection->preparedSelect($query, $data, $formats, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $result, $query, $data, $formats);
		
		$result = self::_applyFilter(get_class(), __FUNCTION__, $result, array('event' => 'return'));
		
		return $result;

	}//end preparedSelect
	
	/**
	 * A SELECT query that will run as a prepared statement
	 * 
	 * @param array $args
	 * @param array $options
	 */
	public static function preparedSelectStatement(array $args, array $options = array()){
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection->preparedSelectStatement($args,$options);
		
		return $result;
	}
	
	

	/**
	 *  Updates a tables data using a prepared query.
	 *
	 * @param string $table The name of the table to be updated.
	 * @param array $data
	 * @param array $wherelist
	 * @param array $formats
	 * @param array $whereformats
	 * @param array $options
	 *
	 * @access public
	 * @todo write better code
	 */
	public static function preparedUpdate($table, $data, $wherelist, $formats = array(), $whereformats = array(), $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table, $data, $wherelist, $formats, $whereformats);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table' => $table, 
			'data' => $data, 
			'wherelist' => $wherelist, 
			'whereformats' => $whereformats, 
			'formats' => $formats
		), array('event' => 'args'));
		
		$table = $filtered['table'];
		$data = $filtered['data'];
		$formats = $filtered['formats'];
		$wherelist = $filtered['wherelist'];
		$whereformats = $filtered['whereformats'];
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection->preparedUpdate($table, $data, $wherelist, $formats, $whereformats, $options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $result, $table, $data, $wherelist, $formats, $whereformats);
		
		$result = self::_applyFilter(get_class(), __FUNCTION__, $result, array('event' => 'return'));

		return $result;

	}//edn preparedUpdate

	/**
	 * Deletes a row in the database spcefied by parameters passed. Use this function
	 * with caution.
	 *
	 * @param string $table The table the information will be deleted from.
	 * @param array $wherelist An array of whats fields to use when deleting the data. The key of the array should be the column name and the array's key value should be the value present in the column.
	 * @param array $whereformats Formats for the where.
	 * @param array $options Options mainly for MongoDB
	 *
	 * @return void
	 */
	public static function preparedDelete($table, $wherelist = array(), $whereformats = array(), $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table, $wherelist, $whereformats);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table' => $table, 
			'wherelist' => $wherelist, 
			'whereformats' => $whereformats 
		), array('event' => 'args'));
		
		$table = $filtered['table'];
		$wherelist = $filtered['wherelist'];
		$whereformats = $filtered['whereformats'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$result = $connection->preparedDelete($table, $wherelist, $whereformats, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $result, $table, $wherelist, $whereformats);
		
		$result = self::_applyFilter(get_class(), __FUNCTION__, $result, array('event' => 'return'));

		return $result;
	}//end preparedDelete
	
	
	/**
	 * The placeholder is a value in preared statements that is suppose to represent a value
	 * to replaced at exection. Placeholder change depending on the database.
	 * 
	 * @param int $count The placeholder spot. Used for postgresql
	 * 
	 * @return $string The placeholder for the current database
	 * @access public
	 */
	public static function getPreparedPlaceHolder($count = 1) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $count);

		$count = self::_applyFilter(get_class(), __FUNCTION__, $count, array('event' => 'args'));

		if (self::$dbtype === self::$mySQLConnection) {
			$placeholder = '?';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$placeholder = '$' . $count;
		} else if (self::$dbtype == self::$oracleConnection) {
			$placeholder = '?';
		} else if (self::$dbtype === self::$msSQLConnection) {
			$placeholder = '?';
		}

		$placeholder = self::_applyFilter(get_class(), __FUNCTION__, $placeholder, array('event' => 'return'));
		
		return $placeholder;

	}//end getPreparedPlaceHolder

	/**
	 * Formats a table to the names conventions used by the current database set up. If the table prefix
	 * is set for the current connection, it will be appened to the name of the database. If the schema
	 * is set, that will be appeneded also.
	 *
	 * @param string $table_name The name of the table to be formated
	 * @param boolean $append_schema Will append the schema to the table name
	 * @param boolean $append_prefix Will append a prefix to the tablee, but behind the schema
	 *
	 * @return string $table_name The name of the table with the values appened in front of it
	 * @access public
	 */
	public static function formatTableName($table_name, $append_schema = true, $append_prefix = true) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name);

		$table_name = self::_applyFilter(get_class(), __FUNCTION__, $table_name, array('event' => 'args'));
		
		$connection = self::_getConnection(self::$current_connecton);
		
		$table_name = $connection->formatTableName($table_name, $append_schema, $append_prefix);

		return $table_name;

	}//end  getApplicationPermissionsTable

	/**
	 * Create a table in the database in which the connection is currently set too.
	 *
	 * @param string $table_name The name of the to be created
	 * @param array $columns The columns that are to be created with the table.
	 * 		  The syntax for creating the columns are from @see formatColumn. The
	 * 		  column name is the key and parameters that create the column is the array that
	 * 		  will be passed to formatColumns
	 * @param array $options Options that control the creation of a table.
	 * 			-'format_table' _boolean_: Formats the table by adding the table prefix set in the database configuration. Default is false.
	 * 			-'execute' _boolean_: Execute the query to create the table. Default is true.
	 * 			-'return_query' _boolean_: Returns the query that would create the table. Default is true
	 * 			-'primary_key' _string_: The primary key(s) of the table
	 *
	 * @return string $query The return query to create the table or false
	 * @access public
	 */
	public static function createTable($table_name, $columns = array(), $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name, $columns, $options);

		$defaults = array('format_table' => false, 'execute' => true, 'return_query' => true, 'primary_key' => '', );
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table_name' => $table_name, 
			'columns' => $columns, 
			'options' => $options
		), array('event' => 'args'));
		
		$table_name = $filtered['table_name'];
		$columns = $filtered['columns'];
		$options = $filtered['options'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$query = $connection->createTable($table_name, $columns, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $query, $table_name, $columns, $options);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));

		return $query;
	}

	/**
	 * Adds a columns to a table that already exist.
	 *
	 * @param string $table_name The name of the table that the column will be added too
	 * @param string $column_name The name of the column to be adding to the table
	 * @param array $column_data The data that will define the column to be created. The array should contain
	 * 		  the same information would would be passed too formatColumn (@see formatColumn).
	 * @param array $options Options that define how adding a column operates.
	 * 			-'format_table' _boolean_: Formats the table name by adding the prefix set in the database config. Default is false.
	 * 			-'execute' _boolean_: Execute the query to create the table. Default is true.
	 * 			-'return_query' _bolean_: Return the generated query. Default is true;
	 *
	 * @return string $query Returns the query for creating the table name
	 * @access public
	 */
	public static function addColumn($table_name, $column_name, $column_data = array(), $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name, $column_name, $column_data, $options);

		$defaults = array('format_table' => false, 'execute' => true, 'return_query' => true, );
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table_name' => $table_name, 
			'column_name' => $column_name, 
			'column_data' => $column_data, 
			'options' => $options
		), array('event' => 'args'));
		
		$table_name = $filtered['table_name'];
		$column_name = $filtered['column_name'];
		$column_data = $filtered['column_data'];
		$options = $filtered['options'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$query = $connection->addColumn($table_name, $column_name, $column_data, $options);
		
		return $query;
	}

	/**
	 * Formats a column based up passed parameters. The formated column will be ready to enter in a SQL
	 * database.
	 *
	 * @param string $name The name of the column to be formated
	 * @param options Options that define the column being created
	 * 			-'primary_key' _boolean_ : Is the passed option a primary key. Default is false.
	 * 			-'unique' _boolean_: Is the passed column considered to be unique. Default is false.
	 * 			-'not_null' _boolean_: Does the column have a not null set. Default is true.
	 * 			-'type' _string_: The type of column this is. Default is string but there are many options
	 * 			and the options are database dependent on what will be created. For a list of types that will
	 * 			create a value, see function mapColumnType
	 * 			-'precision' _int_: How price the column will be. For example if the type is varchar and the precision is 10,
	 * 			then varchar(10) will be used.
	 * 			-'default' _string_: The default value for the column
	 * 			-'auto_increment' _boolean_: Is this column auto incremented. Default is false.
	 * 			-'execute_default' _boolean_: If the default is a sql executable function, set to true so that the funciton will be executed
	 *
	 * @return string $format The column will be returned with arguements formatted to the set database.
	 * @access public
	 */
	public static function formatColumn($name, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options);

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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name, 
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$options = $filtered['options'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$query = $connection->formatColumn($name, $options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $query);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));
		
		return $query;
	}

	/**
	 * Returns the method auto incremented based on the database that is set.
	 *
	 * @return string $increment The auto increment method with is database dependent
	 * @access public
	 */
	private static function getAutoIncrement() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$connection = self::_getConnection(self::$current_connecton);
		
		$query = $connection->getAutoIncrement();

		self::_notify(get_class() . '::' . __FUNCTION__, $query);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));

		return $query;
	}

	
	/**
	 * Remove a column from a table in the database.
	 * 
	 * @param string $table_name The name of the table to remove the column from
	 * @param string $column_name The name name of the column to be removed
	 * @param array $options Options that define how removing a column operates.
	 * 			-'format_table' _boolean_: Formats the table name by adding the prefix set in the database config. Default is false.
	 * 			-'execute' _boolean_: Execute the query to remove the column. Default is true.
	 * 			-'return_query' _bolean_: Return the generated query. Default is true;
	 *
	 * @return string $query Returns the query for removing the column
	 * @access public
	 */
	public static function dropColumn($table_name, $column_name, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name, $column_name, $options);

		$defaults = array('format_table' => false, 'execute' => true, 'return_query' => true, );
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table_name' => $table_name, 
			'column_name' => $column_name, 
			'options' => $options
		), array('event' => 'args'));
		
		$table_name = $filtered['table_name'];
		$column_name = $filtered['column_name'];
		$options = $filtered['options'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$query = $connection->dropColumn($table_name, $column_name, $options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $query, $table_name, $column_name, $options);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));

		
		return $query;
	}
	
	public static function columnTypeMap($type) {
		$connection = self::_getConnection(self::$current_connecton);
		
		return $connection->columnTypeMap($type);
	}

	/**
	 * Drops a table in the database
	 * 
	 * @param string $table_name The name of the table to be dropped
	 * @param array $options Options that define how to remove the table
	 * 			-'format_table' _boolean_: Formats the table name by adding the prefix set in the database config. Default is false.
	 * 			-'execute' _boolean_: Execute the query to the table. Default is true.
	 * 			-'return_query' _bolean_: Return the generated query. Default is true;
	 *
	 * @return string $query Returns the query for dropping the stable
	 * @access public
	 */
	public static function dropTable($table_name, $options = array()) {
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $table_name, $options);

		$defaults = array('format_table' => false, 'execute' => true, 'return_query' => true, );
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'table_name' => $table_name, 
			'options' => $options
		), array('event' => 'args'));
		
		$table_name = $filtered['table_name'];
		$options = $filtered['options'];

		$connection = self::_getConnection(self::$current_connecton);
		
		$query = $connection->dropTable($table_name, $options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $query, $table_name, $options);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));
		
		return $query;
	}
	
}//end class

class NoDatabaseException extends \Exception {
	
}
