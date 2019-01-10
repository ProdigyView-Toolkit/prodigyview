<?php

namespace prodigyview\system;

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
	 * The current query being executed
	 */
	private static $theQuery;
	
	/**
	 * The connection to the datbase
	 */
	private static $link;
	
	/**
	 * Not sure what this was for
	 */
	private static $version;

	//Assigns the connection types values
	//Important for deciding which database to connect to
	
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
	 * Ability to execute myself connections
	 */
	private static $mysql_error_report = MYSQLI_REPORT_ERROR;

	//Database Implementation
	/**
	 * The host of the current datbase
	 */
	private static $dbhost = '';
	
	/**
	 * The name of the current database
	 */
	private static $dbname = '';
	
	/**
	 * The user to login to the current datbase
	 */
	private static $dbuser = '';
	
	/**
	 * The password to access the current database
	 */
	private static $dbpass = '';
	
	/**
	 * The type of DB being accessed(Postgresql, Mysql, ETC)
	 */
	private static $dbtype = '';
	
	/**
	 * A schema to be accessed, if any
	 */
	private static $dbschema = '';
	
	/**
	 * Optional prefixes for the tables
	 */
	private static $dbprefix = '';
	
	/**
	 * The port for the current database
	 */
	private static $dbport = '';
	
	/**
	 * The current database connection being referenced
	 */
	private static $current_connecton = '';

	/**
	 * A row of data
	 */
	private static $row;
	
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
	public static function init($config = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		if(!self::$_initialized) {
			$defaults = array('mysql_error_report' => MYSQLI_REPORT_ERROR);
			$config += $defaults;
	
			self::$mysql_error_report = $config['mysql_error_report'];
	
			self::$connections = array();
		
			self::$_initialized = true;
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
	public static function addConnection($connection_name, $args) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $connection_name, $args);

		$defaults = array(
			'dbhost' => '', 
			'dbname' => '', 
			'dbuser' => '', 
			'dbpass' => '', 
			'dbtype' => '', 
			'dbschema' => '', 
			'dbprefix' => '', 
			'dbport' => ''
		);
		
		$args += $defaults;

		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));

		self::$connections[$connection_name]['dbhost'] = $args['dbhost'];
		self::$connections[$connection_name]['dbname'] = $args['dbname'];
		self::$connections[$connection_name]['dbuser'] = $args['dbuser'];
		self::$connections[$connection_name]['dbpass'] = $args['dbpass'];
		self::$connections[$connection_name]['dbtype'] = $args['dbtype'];
		self::$connections[$connection_name]['dbschema'] = $args['dbschema'];
		self::$connections[$connection_name]['dbprefix'] = $args['dbprefix'];
		self::$connections[$connection_name]['dbport'] = $args['dbport'];

		self::_notify(get_class() . '::' . __FUNCTION__, $connection_name, $args);
	}

	/**
	 *Set the database to one in the configuration file or to one passed used the
	 * Database::addConnection method(). Will close the other database link if open
	 * and create a new one.
	 *
	 * Example:
	 * Database::setDatabase(0);
	 *
	 * @param int profile_id: The ID of the profile set in the config.php file
	 * @return void
	 * @access public
	 */
	public static function setDatabase($profile_id = 0) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $profile_id);

		self::closeDB();

		$profile_id = self::_applyFilter(get_class(), __FUNCTION__, $profile_id, array('event' => 'args'));
		self:: $current_connecton = $profile_id;
		
		self::$dbhost = self::$connections[$profile_id]['dbhost'];
		self::$dbuser = self::$connections[$profile_id]['dbuser'];
		self::$dbpass = self::$connections[$profile_id]['dbpass'];
		self::$dbtype = self::$connections[$profile_id]['dbtype'];
		self::$dbname = self::$connections[$profile_id]['dbname'];
		self::$dbport = self::$connections[$profile_id]['dbport'];
		self::$dbschema = self::$connections[$profile_id]['dbschema'];
		self::$dbprefix = self::$connections[$profile_id]['dbprefix'];
		self::connect();

		self::_notify(get_class() . '::' . __FUNCTION__, $profile_id);
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

		// Connect to the database
		if (self::$dbtype === self::$mySQLConnection) {
			mysqli_report(self::$mysql_error_report);
			$port = !empty(self::$dbport) ? self::$dbport : 3306;
			self::$link = new \mysqli(self::$dbhost, self::$dbuser, self::$dbpass, self::$dbname, $port);
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$port = !empty(self::$dbport) ? self::$dbport : 5432;
			self::$link = pg_connect('host=' . self::$dbhost . ' port=' . $port . ' dbname=' . self::$dbname . ' user=' . self::$dbuser . ' password=' . self::$dbpass );
		} else if (self::$dbtype === self::$msSQLConnection) {
			self::$link = sqlsrv_connect(self::$dbhost, array("UID" => self::$dbuser, "PWD" => self::$dbpass, "Database" => self::$dbname, 'ReturnDatesAsStrings' => true));
		} else if (self::$dbtype === self::$sqLiteConnection) {
			self::$link = sqlite_open(self::$dbname);
		} else if (self::$dbtype == self::$oracleConnection) {
			self::$link = oci_connect($user, $pass, $host);
			$d = new \PDO('oci:dbname=$dbname', '$dbuser', '$dbpass');
		} else if (self::$dbtype === self::$mongoConnection) {
			
			if(class_exists('\\MongoDB\Driver\Manager')) {	
				//self::$link = new \MongoDB\Driver\Manager('mongodb://'.self::$dbuser.':'.self::$dbpass.'@'.self::$dbhost);
				self::$link = new \MongoDB\Client('mongodb://'.self::$dbuser.':'.self::$dbpass.'@'.self::$dbhost, [], ['typeMap' => ['root' => 'array', 'document' => 'array']]);
				self::$link ->selectDatabase(self::$dbname);
			} else if(class_exists ('MongoClient')) {
				self::$link = new \MongoClient('mongodb://'.self::$dbuser.':'.self::$dbpass.'@'.self::$dbhost);
				$database = self::$dbname;
				self::$link = self::$link ->selectDB($database);
			} else {
				self::$link = new Mongo('mongodb://'.self::$dbuser.':'.self::$dbpass.'@'.self::$dbhost);
				$database = self::$dbname;
				self::$link = self::$link ->selectDB($database);
			}
			
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

		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'args'));
		
		if (self::$dbtype === self::$mySQLConnection) {
			self::$theQuery = $query;
			$result = self::$link -> query($query);
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			self::$theQuery = $query;
			$result = pg_query(self::$link, $query);
		} else if (self::$dbtype === self::$msSQLConnection) {
			self::$theQuery = $query;
			$result = sqlsrv_query(self::$link, $query, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
		} else if (self::$dbtype === self::$sqLiteConnection) {
			self::$theQuery = $query;
			$result = sqlite_query(self::$link, $query);
		} else if (self::$dbtype == self::$oracleConnection) {
			self::$theQuery = $query;
			$stid = oci_parse(self::$link, $query);
			$result = oci_execute($stid);
		}

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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('query' => $query, 'returnField' => $returnField, 'returnTable' => $returnTable), array('event' => 'args'));
		$query = $filtered['query'];
		$returnField = $filtered['returnField'];
		$returnTable = $filtered['returnTable'];

		if (self::$dbtype === self::$mySQLConnection) {
			self::$theQuery = $query;
			self::$link -> query($query);
			$id = self::$link -> insert_id;
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			self::$theQuery = $query . " RETURNING $returnField ";
			$result = pg_exec($query . " RETURNING $returnField ");
			$row = self::fetchArray($result);
			$id = $row[$returnField];
		} else if (self::$dbtype === self::$msSQLConnection) {
			self::$theQuery = $query;
			sqlsrv_query(self::$link, $query);
			$query = "SELECT @@IDENTITY AS $returnField FROM $returnTable;";
			$result = self::query($query);
			$row = self::fetchArray($result);
			$field_value = $row[$returnField];
			$id = $field_value;
		} else if (self::$dbtype == self::$oracleConnection) {
			self::$theQuery = $query;
			$stid = oci_parse(self::$link, $query);
			$id = oci_execute($stid);
		}

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

		if (self::$dbtype === self::$mySQLConnection) {
			$count = self::$link -> affected_rows;
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$count = pg_num_rows($result);
		} else if (self::$dbtype === self::$msSQLConnection) {
			$count = sqlsrv_num_rows($result);
		} else if (self::$dbtype === self::$sqLiteConnection) {
			$count = sqlite_num_rows($result);
		} else if (self::$dbtype == self::$oracleConnection) {
			self::$theQuery = $query;
			$stid = oci_parse(self::$link, $query);
			$count = oci_execute($stid);
		}

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

		if (self::$dbtype === self::$mySQLConnection && get_class($result) == 'mysqli_result') {
			$array = $result -> fetch_array();
		} else if (self::$dbtype === self::$mySQLConnection && get_class($result) == 'mysqli_stmt') {
			$result -> fetch();
			//return self::$row;

			$array = array();
			foreach (self::$row as $key => $value) {
				$array[$key] = $value;
			}
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$array = pg_fetch_array($result);
		} else if (self::$dbtype === self::$msSQLConnection && !empty($result)) {
			$array = sqlsrv_fetch_array($result);
		} else if (self::$dbtype === self::$sqLiteConnection) {
			$array = sqlite_fetch_array($result);
		} else if (self::$dbtype == self::$oracleConnection) {
			$stid = oci_parse(self::$link, $result);
			$array = oci_fetch_array($stid, OCI_ASSOC);
		} else {
			$array = $result;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $array, $result);
		$array = self::_applyFilter(get_class(), __FUNCTION__, $array, array('event' => 'return'));

		return $array;
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

		if (self::$dbtype === self::$mySQLConnection && get_class($result) == 'mysqli_result') {
			if(method_exists($result, 'fetch_all')) {
				$fields = $result -> fetch_all(MYSQLI_BOTH);
			} else {
				$fields = array();
				while($row = $result->fetch_assoc()) {
					$fields[] = $row;
				}
			}
		} else if (self::$dbtype === self::$mySQLConnection && get_class($result) == 'mysqli_stmt') {
			$result_set = new Collection();

			while ($result -> fetch()) {
				$object = new stdclass();
				foreach (self::$row as $key => $value) {
					$object -> $key = $value;
				}
				$data = self::$row;
				$result_set -> add($object);
			}

			$fields = $result_set;
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$fields = pg_fetch_assoc($result);
		} else if (self::$dbtype === self::$msSQLConnection && !empty($result)) {
			$fields = sqlsrv_fetch_array($result);
		} else if (self::$dbtype === self::$sqLiteConnection) {
			$fields = sqlite_fetch_array($result);
		} else if (self::$dbtype == self::$oracleConnection) {
			$stid = oci_parse(self::$link, $result);
			$fields = oci_fetch_array($stid, OCI_ASSOC);
		}

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

		if (is_array($string)) {
			$return_array = array();

			foreach ($string as $key => $value) {
				$return_array[$key] = self::makeSafe($value);
			}

		} else {
			if (self::$dbtype === self::$mySQLConnection) {
				$return_array = self::$link -> real_escape_string($string);
			} else if (self::$dbtype === self::$postgreSQLConnection) {
				$return_array = pg_escape_string(self::$link, $string);
			} else if (self::$dbtype === self::$msSQLConnection) {

				if (!isset($string) or empty($string))
					return '';
				if (is_numeric($string))
					return $string;

				$non_displayables = array('/%0[0-8bcef]/', // url encoded 00-08, 11, 12, 14, 15
					'/%1[0-9a-f]/', // url encoded 16-31
					'/[\x00-\x08]/', // 00-08
					'/\x0b/', // 11
					'/\x0c/', // 12
					'/[\x0e-\x1f]/'             // 14-31
				);
				foreach ($non_displayables as $regex) {
					$string = preg_replace($regex, '', $string);
				}//end for each

				$string = str_replace("'", "''", $string);

				$return_array = $string;
			} else if (self::$dbtype === self::$sqLiteConnection) {
				$return_array = sqlite_escape_string($string);
			} else if (self::$dbtype == self::$oracleConnection) {
				$stid = oci_parse(self::$link, $result);
				$return_array = oci_fetch_array($stid, OCI_ASSOC);
			}
		}//end else

		$return_array = self::_applyFilter(get_class(), __FUNCTION__, $return_array, array('event' => 'return'));

		return $return_array;

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

		if (self::$dbtype === self::$mySQLConnection) {
			self::$link -> close();
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			pg_close(self::$link);
		} else if (self::$dbtype === self::$msSQLConnection) {
			sqlsrv_close(self::$link);
		} else if (self::$dbtype === self::$sqLiteConnection) {
			sqlite_close(self::$link);
		} else if (self::$dbtype == self::$oracleConnection) {
			oci_close(self::$link);
		}

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

		if (empty(self::$dbschema)) {
			$schema = '';
		} else if($append_period) {
			$schema = self::$dbschema . ".";
		} else {
			$schema = self::$dbschema;
		}

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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('tablename' => $tablename, 'options' => $options), array('event' => 'args'));
		$tablename = $filtered['tablename'];
		$options = $filtered['options'];

		$tablename = self::makeSafe($tablename);

		$query = "TRUNCATE TABLE $tablename $options";
		self::query($query);

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

		$query = '';

		if (self::$dbtype === self::$mySQLConnection) {
			$query = "show tables like \"$tablename\";";
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$query = "select * from information_schema.tables where table_name  = '$tablename' ";
			if(!empty($schema))
				$query.= "AND table_schema = '$schema'";
		} else if (self::$dbtype === self::$sqLiteConnection) {
			$query = "SELECT name FROM sqlite_master WHERE type='table' AND name='$tablename'; ";
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = "SELECT * FROM SysObjects WHERE [Name] = '$tablename'; ";
		} else if (self::$dbtype == self::$oracleConnection) {
			//To be Filed in
		}
		
		$result = self::query($query);
		$count = self::resultRowCount($result);
		self::_notify(get_class() . '::' . __FUNCTION__, $count, $result, $tablename);
		
		if ($count <= 0 && empty($count)) {
			return FALSE;
		}

		return TRUE;
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table_name' => $table_name, 'field_name' => $field_name), array('event' => 'args'));
		$table_name = $filtered['table_name'];
		$field_name = $filtered['field_name'];

		if (self::$dbtype === self::$mySQLConnection) {
			$query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = '" . self::$dbname . "' AND table_name = '$table_name' AND column_name = '$field_name' ";
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$query = "SELECT * FROM information_schema.columns WHERE table_schema = '".self::getSchema(false)."' AND table_name = '$table_name' AND column_name = '$field_name';";
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$table_name' AND COLUMN_NAME='$field_name';";
		} else if (self::$dbtype == self::$oracleConnection) {

		}

		$result = self::query($query);
		$count = self::resultRowCount($result);
		self::_notify(get_class() . '::' . __FUNCTION__, $count, $result, $table_name, $field_name);

		if ($count <= 0) {
			return FALSE;
		}

		return TRUE;
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

		$function = '';
		
		if (self::$dbtype === self::$mySQLConnection) {
			$function = 'RAND()';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$function = 'RANDOM()';
		} else if (self::$dbtype == self::$oracleConnection) {
			$function = 'RAND()';
		} else if (self::$dbtype === self::$msSQLConnection) {
			$function = 'RAND()';
		}

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

		if (is_array($string)) {
			$return_array = array();
			foreach ($string as $key => $value) {
				$return_array[$key] = self::formatData($value);
			}
		} else {
			if (self::$dbtype === self::$mySQLConnection) {
				$return_array = stripslashes($string);
			} else {
				$return_array = $string;
			}
		}//end else

		$return_array = self::_applyFilter(get_class(), __FUNCTION__, $return_array, array('event' => 'return'));

		return $return_array;
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
		
		$function = '';

		if (self::$dbtype === self::$mySQLConnection) {
			$function = ' AVG(' . $field . ') ';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$function = ' AVG(' . $field . ') ';
		} else if (self::$dbtype == self::$oracleConnection) {
			$function = ' AVG(' . $field . ') ';
		} else if (self::$dbtype === self::$msSQLConnection) {
			$function = ' AVG(' . $field . ') ';
		}

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

		self::_notify(get_class() . '::' . __FUNCTION__, self::$dbtype);
		$dbtype = self::_applyFilter(get_class(), __FUNCTION__, self::$dbtype, array('event' => 'return'));

		return $dbtype;
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

		self::_notify(get_class() . '::' . __FUNCTION__, self::$dbtype);
		$current_connecton = self::_applyFilter(get_class(), __FUNCTION__, self:: $current_connecton, array('event' => 'return'));

		return $current_connecton;
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table' => $table, 'join_clause' => $join_clause, 'where_clause' => $where_clause, 'current_page' => $current_page, 'results_per_page' => $results_per_page, 'order_by' => $order_by), array('event' => 'args'));
		$table = $filtered['table'];
		$join_clause = $filtered['join_clause'];
		$where_clause = $filtered['where_clause'];
		$current_page = $filtered['current_page'];
		$results_per_page = $filtered['results_per_page'];
		$order_by = $filtered['order_by'];
		
		if(!empty($where_clause) && !is_array($where_clause))
				$where_clause .= ' WHERE '.$args['where'];
			else if(is_array($where_clause) && !empty($where_clause)) {
				$query = ' WHERE ';
				$first = true;
				foreach($where_clause as $key => $value) {
					if(is_array($value))
						$query .= self::parseOperators($key, $value, 'AND', '=', $first);
					else {
						if($first) {
							if(Validator::isInteger($key)) {
								$query .= ' ' .$value.' ';
							} else {
								$query .= $key.' = \''.self::makeSafe($value).'\'';
							}
						} else {
							if(Validator::isInteger($key)) {
								$query .= ' AND ' .$value.' ';
							} else {
								$query .= ' AND '.$key.' = \''.self::makeSafe($value).'\'';
							}
						}
					}
					
					$first = false;
				}
				
				$where_clause = $query;
			}

		$query = "SELECT $fields FROM $table $join_clause $where_clause";
		
		$result = self::query($query);
		$total_pages = self::fetchArray($result);
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

		$database_type = self::getDatabaseType();

		if ($database_type == 'mysql') {
			$limit_offset = ' LIMIT ' . ($current_page - 1) * $results_per_page . ',' . $results_per_page;
		} else if ($database_type == 'postgresql') {
			$limit_offset = ' LIMIT ' . ($current_page - 1) * $results_per_page . ' OFFSET ' . $results_per_page;
		} else if ($database_type == 'mssql') {
			$limit_offset = ' RowNum >= ' . $start_location . ' RowNum < ' . $start_location + $results_per_page;
			$from_clause = " ( SELECT    ROW_NUMBER() OVER ( ORDER BY $order_by ) AS RowNum, * FROM      $table $where_clause ) AS RowConstrainedResult ";
		}

		$return_array = array('limit_offset' => $limit_offset, 'current_page' => $current_page, 'last_page' => $last_page, 'start_location' => $start_location, 'total_pages' => $total_pages, 'from_clause' => $from_clause);

		self::_notify(get_class() . '::' . __FUNCTION__, $return_array, $table, $join_clause, $where_clause, $current_page, $results_per_page, $order_by);
		$return_array = self::_applyFilter(get_class(), __FUNCTION__, $return_array, array('event' => 'return'));

		return $return_array;

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

		self::_notify(get_class() . '::' . __FUNCTION__, self::$link);
		$link = self::_applyFilter(get_class(), __FUNCTION__, self::$link, array('event' => 'return'));

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
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table_name' => $table_name, 'data' => $data, 'options' => $options), array('event' => 'args'));
		$table_name = $filtered['table_name'];
		$data = $filtered['data'];
		$options = $filtered['options'];
		
		if (self::$dbtype === self::$mySQLConnection || self::$dbtype === self::$postgreSQLConnection || self::$dbtype === self::$msSQLConnection) {
			
			$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table_name' => $table_name, 'data' => $data, 'options' => $options), array('event' => 'args'));
			$table_name = $filtered['table_name'];
			$data = $filtered['data'];
			$options = $filtered['options'];
	
			if (!empty($table_name)) {
				$first = 1;
				foreach ($data as $key => $value) {
	
					if ($first == 0) {
						$columns .= ' ,' . $key;
						$values .= ' ,\'' . self::makeSafe( $value ) . '\' ';
					} else {
						$columns = $key;
						$values = ' \'' . self::makeSafe($value) . '\' ';
					}
	
					$first = 0;
				}//end foreach
	
				$query = 'INSERT INTO ' . $table_name . '(' . $columns . ') VALUES(' . $values . ')';
				$result = self::query($query);
			}
		} else if (self::$dbtype === self::$mongoConnection) {
			$result = preparedReturnLastInsert($table_name, '', '', $data, array(), $options );
		}
		
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table' => $table, 'data' => $data, 'wherelist' => $wherelist, 'options' => $options), array('event' => 'args'));
		$table = $filtered['table'];
		$data = $filtered['data'];
		$options = $filtered['options'];
		$wherelist = $filtered['wherelist'];

		if (self::$dbtype === self::$mySQLConnection || self::$dbtype === self::$postgreSQLConnection || self::$dbtype === self::$msSQLConnection) {
			$query = 'UPDATE ' . $table . ' SET ';
			$params = array();
			$params_holder = array();
			
			if(is_array($data)) {
				foreach ($data as $key => $value) {
					$query .= $key. ' = \''.self::makeSafe($value).'\'';
				}//end foreach
			
			} else {
				$query.= $data;
			}
	
			if (is_array($wherelist) && !empty($wherelist)) {
				$query .= ' WHERE ';
				$first = true;
				
				foreach($wherelist as $key => $value) {
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
				}//end foreach
				
			} else if (!empty($wherelist)) {
				$query .= ' WHERE '.$wherelist;
			}
		
			$result = self::query($query);
		
		} else if (self::$dbtype === self::$mongoConnection) {
			$collection = self::_setMongoCollection($table, $options);
			
			if(class_exists('\\MongoDB\Driver\Manager')) {
    			$result = $collection->updateMany($wherelist, array('$set' =>$data), $options);
			} else {
				$result = $collection -> update($wherelist, $data, $options);
			}
			
		}

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
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('args' => $args, 'options' => $options), array('event' => 'args'));
		$args= $filtered['args'];
		$options = $filtered['options'];
		
		if (self::$dbtype === self::$mySQLConnection || self::$dbtype === self::$postgreSQLConnection || self::$dbtype === self::$msSQLConnection) {
			$default = array(
				'fields'=>'*',
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
			
			if(is_array($args['fields'])) {
				$fields = implode(',', $args['fields']);
			} else {
				$fields = $args['fields'];
			}
			
			$query .= 'SELECT ' . $fields . ' ';
			
			$query .= ' FROM '.$args['table'] . ' ';
			
			if(is_array($args['join'])) {
				foreach($args['join'] as $key => $value) {
					if(is_array($value)){
						$type = isset($value['type']) ? $value['type'] : ' NATURAL JOIN ';
						$table = isset($value['table']) ? $value['table'] : $key ;
						$on = isset($value['on']) ? $value['on'] : '' ;
						
						$query .= $type. ' ' . $table. ' ' .$on;
					} else {
						$query .= ' JOIN '.$value;
					}
				}
			} else {
				$query .= ' '.$args['join'];
			}
			
			if(!empty($args['where']) && !is_array($args['where']))
				$query .= ' WHERE '.$args['where'];
			else if(is_array($args['where']) && !empty($args['where'])) {
				$query .= ' WHERE ';
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
			}
			
			
			if(!empty($args['order_by']) && is_array($args['order_by'])) {
				$query .= ' ORDER BY '.implode(',', $args['order_by']);
			} else if(!empty($args['order_by'])) {
				$query .= ' ORDER BY '. $args['order_by'];
			}
			
			$result = Database::query($query);	
		} else if(self::$dbtype === self::$mongoConnection) {
			$where = (!empty($args['where'])) ? $args['where'] : array();
			$fields = (!empty($args['fields']) && $args['fields'] != '*' ) ? $args['fields'] : array();
			
			$collection = self::_setMongoCollection($args['table'], $options);
			
			if(isset($options['findOne']) && $options['findOne'] ){
				
				$result = $collection -> findOne($where, $fields);
			} else {
				$result = $collection -> find($where, $fields);
			
				if(!empty($args['order_by']) && !class_exists('\\MongoDB\Driver\Manager')) {
					$result = $result -> sort($args['order_by']);
				}
				
				if(!empty($args['limit']) && !class_exists('\\MongoDB\Driver\Manager')) {
					$result = $result -> limit($args['limit']);
				}
				
				if(!empty($args['offset']) && !class_exists('\\MongoDB\Driver\Manager')) {
					$result = $result -> skip($args['offset']);
				}
				
			}
		}
		
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
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('args' => $args, 'options' => $options), array('event' => 'args'));
		$args= $filtered['args'];
		$options = $filtered['options'];
		
		if (self::$dbtype === self::$mySQLConnection || self::$dbtype === self::$postgreSQLConnection || self::$dbtype === self::$msSQLConnection) {
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
			
			$result = Database::query($query);	
		} else if(self::$dbtype === self::$mongoConnection) {
			$collection = self::_setMongoCollection($args['table'], $options);
			$where = (!empty($args['where'])) ? $args['where'] : array();
			
			if(class_exists('\\MongoDB\Driver\Manager')) {
				$result = $collection -> deleteMany($where, $options);
			} else {
				$result = $collection -> remove($where, $options);
			}
		}
		
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

		if (self::$dbtype === self::$mySQLConnection) {
			self::$link -> prepare($query);
			$count = 1;

			foreach ($data as $key => $value) {
				self::$link -> bindParam($count, $value);
				$count++;
			}//end foreach

			return self::$link -> execute();
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$result = pg_prepare(self::$link, '', $query);
			$result = pg_execute(self::$link, '', $data);
			return $result;
		} else if (self::$dbtype == self::$oracleConnection) {

		} else if (self::$dbtype === self::$msSQLConnection) {
			$stmt = sqlsrv_prepare(self::$link, $query, $data);
			return sqlsrv_execute($stmt);
		}

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
				$placeholders .= ' ' . self::getPreparedPlaceHolder($count + 1) . ' ';
			} else {
				$values .= ' , ' . $key;
				$placeholders .= ', ' . self::getPreparedPlaceHolder($count + 1) . ' ';
			}

			$first = 0;
			$count++;
		}//end foreach

		if (!empty($data)) {
			$values .= ')';
			$placeholders .= ')';
		}//end if

		$query .= $values . ' VALUES'.$placeholders;
		
		$template_name = md5($query);
		
		if (self::$dbtype === self::$mySQLConnection) {

		 	$stmt = self::$link -> prepare($query);
                        if(!$stmt) {
                                echo 'Error';
                                exit();
                        }

  	                $count = 1;
                        $refs = array();
                        $type = '';
                        foreach($data as $k => $v) {
                                $refs[$k] = &$data[$k];
                                $type .= 's';
         	 	}
                                       
                        call_user_func_array(array($stmt, 'bind_param'), array_merge(array($type), $refs));

                        return $stmt  -> execute();
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			
			$result = pg_query_params(self::$link, 'SELECT name FROM pg_prepared_statements WHERE name = $1' , array($template_name));

			if (pg_num_rows($result) == 0) {
    			$result = pg_prepare(self::$link, $template_name, $query);
				
			}

			$result = pg_execute(self::$link, $template_name, $data);
			return $result;

		} else if (self::$dbtype == self::$oracleConnection) {

		} else if (self::$dbtype === self::$msSQLConnection) {

			$stmt = sqlsrv_prepare(self::$link, $query, $data);

			return sqlsrv_execute($stmt);
		} else if (self::$dbtype === self::$mongoConnection) {
			$collection = self::_setMongoCollection($table);
			
			if(class_exists('\\MongoDB\Driver\Manager')){
				$collection -> insertOne($data);
			} else {
				$collection -> insert($data);
			}
		}

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
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table_name' => $table_name, 'data' => $data, 'returnField' => $returnField, 'returnTable' => $returnTable, 'formats' => $formats), array('event' => 'args'));
		$table_name = $filtered['table_name'];
		$returnField = $filtered['returnField'];
		$returnTable = $filtered['returnTable'];
		$data = $filtered['data'];
		$formats = $filtered['formats'];
		
		if(self::getDatabaseType() == 'mongo' ) {
			$collection = self::_setMongoCollection($table_name, $options);
			
			if(isset($options['batchInsert']) && $options['batchInsert']) {
				$collection -> batchInsert($data, $options);
			} else if(isset($options['gridFS']) && isset($options['file']) && $options['gridFS']) {
				$id = $collection -> storeFile($options['file'] ,$data, $options);
			} else {
				if(class_exists('\\MongoDB\Driver\Manager')){
					$result = $collection -> insertOne($data,$options);
				} else {
					$result = $collection -> insert($data,$options);
				}
				
			}
			
			if(isset($options['batchInsert']) && $options['batchInsert']) {
				$id = $data;
			} else if(isset($options['gridFS']) == false) {
				if(class_exists('\\MongoDB\Driver\Manager')){
					$id = $result->getInsertedId();
				} else {
					$id = $data['_id'];
				}
			}
				
		} else {

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
					$placeholders .= ' ' . self::getPreparedPlaceHolder($count + 1) . ' ';
				} else {
					$values .= ' , ' . $key;
					$placeholders .= ', ' . self::getPreparedPlaceHolder($count + 1) . ' ';
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
	
			if (self::$dbtype === self::$mySQLConnection) {
				$stmt = self::$link -> prepare($query);
				self::bindParameters($stmt, $params);
				foreach ($data as $key => $value) {
					$params[$key] = $value;
				}
				$stmt -> execute();
				$id = self::$link -> insert_id;
	
			} else if (self::$dbtype === self::$postgreSQLConnection) {
				$template_name = md5($query . " RETURNING $returnField");
				
				$result = pg_query_params(self::$link, 'SELECT name FROM pg_prepared_statements WHERE name = $1' , array($template_name));

				if (pg_num_rows($result) == 0) {
	    			$result = pg_prepare(self::$link, $template_name , $query . " RETURNING $returnField");
				}
			
				$result = pg_execute(self::$link, $template_name , $data);
				
				if($result == false) {
					self::catchDBError();
				}
				$row = self::fetchArray($result);
				
				$id = $row[$returnField];
			} else if (self::$dbtype == self::$oracleConnection) {
	
			} else if (self::$dbtype === self::$msSQLConnection) {
	
				$stmt = sqlsrv_prepare(self::$link, $query, $data);
	
				sqlsrv_execute($stmt);
	
				$query = "SELECT @@IDENTITY AS $returnField FROM $returnTable;";
				$result = self::query($query);
				$row = self::fetchArray($result);
				$field_value = $row[$returnField];
				$id = $field_value;
			}
		}

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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('query' => $query, 'data' => $data, 'formats' => $formats,  'options' => $options ), array('event' => 'args'));
		$query = $filtered['query'];
		$data = $filtered['data'];
		$formats = $filtered['formats'];
		$options = $filtered['options'];

		$params = array();

		$count = 0;
		foreach ($data as $key => $value) {
			$params[$key] = (isset($formats[$count])) ? $formats[$count] : 's';
			$count++;
		}//end foreach
		
		if(isset($options['prequery']) && !empty($options['prequery'])) {
			$query = $options['prequery'] . $query;
		}
		
		if(isset($options['postquery']) && !empty($options['postquery'])) {
			$query = $query. $options['postquery'];
		}

		if (self::$dbtype === self::$mySQLConnection) {

			$stmt = self::$link -> prepare($query);

			if (!empty($params)) {
				self::bindParameters($stmt, $params);
				foreach ($data as $key => $value) {
					$params[$key] = $value;
				}
			}

			$stmt -> execute();
			$stmt -> store_result();
			self::$row = array();
			self::stmt_bind_assoc($stmt, self::$row);
			$result = $stmt;
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$result = pg_prepare(self::$link, '', $query);
			$result = pg_execute(self::$link, '', $data);
		} else if (self::$dbtype == self::$oracleConnection) {

		} else if (self::$dbtype === self::$msSQLConnection) {

			$stmt = sqlsrv_prepare(self::$link, $query, $data);
			$result = sqlsrv_execute($stmt);
		}

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
	public static function selectPreparedStatement(array $args, array $options = array()){
		
		if (self::$dbtype === self::$mySQLConnection || self::$dbtype === self::$postgreSQLConnection || self::$dbtype === self::$msSQLConnection) {
			$default = array(
				'fields'=>'*',
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
			
			$query = '';
			
			if(is_array($args['fields'])) {
				$fields = implode(',', $args['fields']);
			} else {
				$fields = $args['fields'];
			}
			
			$query .= 'SELECT ' . $fields . ' ';
			
			$query .= ' FROM '.$args['table'] . ' ';
			
			if(is_array($args['join'])) {
				foreach($args['join'] as $key => $value) {
					if(is_array($value)){
						$type = isset($value['type']) ? $value['type'] : ' NATURAL JOIN ';
						$table = isset($value['table']) ? $value['table'] : $key ;
						
						$query .= $type. ' ' . $table;
					} else {
						$query .= ' JOIN '.$value;
					}
				}
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
						if($first) {
							if(Validator::isInteger($key)) {
								$query .= ' ' .$value.' ';
							} else {
								$query .= $key.' = \''.self::makeSafe($value).'\'';
							}
						} else {
							if(Validator::isInteger($key)) {
								$query .= ' AND ' .$value.' ';
							} else {
								$query .= ' AND '.$key.' = \''.self::makeSafe($value).'\'';
							}
						}
					}
					
					$first = false;
				}
			} else {
				$query .= $args['where'];
			}
			
			if(!empty($args['group_by']) && is_array($args['group_by'])) {
				$query .= ' GROUP BY '.implode(',', $args['group_by']);
			} else if(!empty($args['group_by'])) {
				$query .= ' GROUP BY '. $args['group_by'];
			}
			
			if(!empty($args['order_by']) && is_array($args['order_by'])) {
				$query .= ' ORDER BY '.implode(',', $args['order_by']);
			} else if(!empty($args['order_by'])) {
				$query .= ' ORDER BY '. $args['order_by'];
			}
			
			if(!empty($args['limit'])) {
				$query .= ' LIMIT '. $args['limit'];
			} 
			
			if(isset($options['prequery']) && !empty($options['prequery'])) {
				$query = $options['prequery'] . $query;
			}
			
			if(!empty($args['offset'])) {
				$query .= ' OFFSET '. $args['offset'];
			}
		
			if(isset($options['postquery']) && !empty($options['postquery'])) {
				$query = $query . $options['postquery'];
			}
		
			$result = Database::query($query);	
		} else if(self::$dbtype === self::$mongoConnection) {
			$collection = self::_setMongoCollection(self::$link->$table_name);
			
			$result = $collection -> find($args);
		}
		
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table' => $table, 'data' => $data, 'wherelist' => $wherelist, 'whereformats' => $whereformats, 'formats' => $formats), array('event' => 'args'));
		$table = $filtered['table'];
		$data = $filtered['data'];
		$formats = $filtered['formats'];
		$wherelist = $filtered['wherelist'];
		$whereformats = $filtered['whereformats'];
		
		if(self::getDatabaseType() == 'mongo' ) {
			$collection = self::_setMongoCollection($table, $options);
			if(class_exists('\\MongoDB\Driver\Manager')) {
				$result = $collection -> updateMany($wherelist, array('$set' =>$data), $options);
			} else {
				$result = $collection -> update($wherelist, $data, $options);
			}
		} else {

			$query = 'UPDATE ' . $table . ' SET ';
			$params = array();
			$params_holder = array();
	
			$first = 1;
			$count = 0;
			foreach ($data as $key => $value) {
				$params[] = (isset($formats[$count])) ? $formats[$count] : 's';
				$params_holder[] = $value;
	
				if ($first) {
					$query .= $key . '=' . self::getPreparedPlaceHolder($count + 1) . ' ';
				} else {
					$query .= ',' . $key . '=' . self::getPreparedPlaceHolder($count + 1) . ' ';
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
						$query .= $key . '=' . self::getPreparedPlaceHolder($count + 1) . ' ';
					} else {
						$query .= ' AND ' . $key . '=' . self::getPreparedPlaceHolder($count + 1) . ' ';
					}
					$count++;
					$count2++;
					$first = 0;
				}//end foreach
			}//end if is_array and not emptys
			
			$template_name = md5($query);
			
			if (self::$dbtype === self::$mySQLConnection) {
	
				$stmt = self::$link -> prepare($query);
				self::bindParameters($stmt, $params);
				
				foreach ($params_holder as $key => $value) {
					$params[$key] = $value;
				}
			
				$result = $stmt -> execute();
			} else if (self::$dbtype === self::$postgreSQLConnection) {
				
				$result = pg_query_params(self::$link, $query, $params_holder);

				$result = pg_query_params(self::$link, 'SELECT name FROM pg_prepared_statements WHERE name = $1' , array($template_name));

				if (pg_num_rows($result) == 0) {
    				$result = pg_prepare(self::$link, $template_name, $query);
				}
	
				$result = pg_execute(self::$link, $template_name, $params_holder);
	
			} else if (self::$dbtype == self::$oracleConnection) {
	
			} else if (self::$dbtype === self::$msSQLConnection) {
				$stmt = sqlsrv_prepare(self::$link, $query, $params);
				$result = sqlsrv_execute($stmt);
			} 
		
		}
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table' => $table, 'wherelist' => $wherelist, 'whereformats' => $whereformats, ), array('event' => 'args'));
		$table = $filtered['table'];
		$wherelist = $filtered['wherelist'];
		$whereformats = $filtered['whereformats'];

		if (self::$dbtype === self::$mongoConnection) {
			$collection = self::_setMongoCollection($table, $options);
			if(class_exists('\\MongoDB\Driver\Manager')) {
				$result = $collection -> deleteMany($wherelist, $options);
			} else {
				$result = $collection -> remove($wherelist, $options);
			}
			
		} else {
			$query = 'DELETE FROM ' . $table;
	
			if (is_array($wherelist) && !empty($wherelist)) {
				$params = array();
				$query .= ' WHERE ';
				$count = 0;
				$first = 1;
				foreach ($wherelist as $key => $value) {
					$params[$key] = (isset($wherelist[$count])) ? $formats[$count] : 's';
					if ($first) {
						$query .= $key . '=' . self::getPreparedPlaceHolder($count + 1) . ' ';
					} else {
						$query .= ' AND ' . $key . '=' . self::getPreparedPlaceHolder($count + 1) . ' ';
					}
	
					$first = 0;
					$count++;
				}//end foreach
			}
	
			$template_name = md5($query);
			
			if (self::$dbtype === self::$mySQLConnection) {
	
				$stmt = self::$link -> prepare($query);
				self::bindParameters($stmt, $params);
				foreach ($wherelist as $key => $value) {
					$params[$key] = $value;
				}
	
				$result = $stmt -> execute();
	
			} else if (self::$dbtype === self::$postgreSQLConnection) {
				
				$result = pg_query_params(self::$link, 'SELECT name FROM pg_prepared_statements WHERE name = $1' , array($template_name));

				if (pg_num_rows($result) == 0) {
    				$result = pg_prepare(self::$link, $template_name, $query);
				}
	
				$result = pg_execute(self::$link, $template_name, $wherelist);
	
			} else if (self::$dbtype == self::$oracleConnection) {
	
			} else if (self::$dbtype === self::$msSQLConnection) {
	
				$stmt = sqlsrv_prepare(self::$link, $query, $wherelist);
				$result = sqlsrv_execute($stmt);
			} 
		}

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
		$table_name = self::$dbprefix . $table_name;

		if (!empty(self::$dbschema) && $append_schema ) {
			$table_name = self::$dbschema . '.' . $table_name;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $table_name);
		$table_name = self::_applyFilter(get_class(), __FUNCTION__, $table_name, array('event' => 'return'));

		return $table_name;

	}//end  getApplicationPermissionsTable
	
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
		if(Validator::isInteger($key))
			$key = 'AND';
		
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

	/**
	 * For prepared statements, binds the parameters with placeholders.
	 * 
	 * @param string $statement The sql statement
	 * @param array $params The parameters to bind with
	 */
	private static function bindParameters(&$statement, &$params) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $statement, $params);

		$args = array();
		$args[] = implode('', array_values($params));
		foreach ($params as $paramName => $paramType) {
			$args[] = &$params[$paramName];
			$params[$paramName] = null;
		}

		call_user_func_array(array(&$statement, 'bind_param'), $args);
	}

	/**
	 * For prepared statements, binds an associatve array. Used for myql.
	 * 
	 * @param string $stmt The mysql statement
	 * @param string $out The output
	 */
	private function stmt_bind_assoc(&$stmt, &$out) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $stmt, $out);

		$data = mysqli_stmt_result_metadata($stmt);
		$fields = array();
		$out = array();

		$fields[0] = $stmt;
		$count = 1;

		while ($field = mysqli_fetch_field($data)) {
			$fields[$count] = &$out[$field -> name];
			$count++;
		}
		@call_user_func_array(mysqli_stmt_bind_result, $fields);
	}

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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table_name' => $table_name, 'columns' => $columns, 'options' => $options), array('event' => 'args'));
		$table_name = $filtered['table_name'];
		$columns = $filtered['columns'];
		$options = $filtered['options'];

		$column_query = '';
		if (!empty($columns) && is_array($columns)) {
			$first = 1;
			foreach ($columns as $column_name => $column) {
				$column_query .= (!$first) ? ',' : '';
				$column_query .= self::formatColumn($column_name, $column);
				$first = 0;
			}
		}
		
		if (!empty($options['primary_key']))
			$column_query .= ',PRIMARY KEY(' . $options['primary_key'] . ')';

		if (!empty($column_query))
			$column_query = '(' . $column_query . ')';

		if ($options['format_table'])
			$table_name = self::formatTableName($table_name);

		if (self::$dbtype === self::$mySQLConnection) {
			$query = 'CREATE TABLE ' . $table_name . ' ' . $column_query . ';';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$query = 'CREATE TABLE ' . $table_name . ' ' . $column_query . ';';
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = 'CREATE TABLE ' . $table_name . ' ' . $column_query . ';';
		}
		
		if ($options['execute'])
			Database::query($query);

		self::_notify(get_class() . '::' . __FUNCTION__, $query, $table_name, $columns, $options);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));

		if ($options['return_query'])
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table_name' => $table_name, 'column_name' => $column_name, 'column_data' => $column_data, 'options' => $options), array('event' => 'args'));
		$table_name = $filtered['table_name'];
		$column_name = $filtered['column_name'];
		$column_data = $filtered['column_data'];
		$options = $filtered['options'];

		if ($options['format_table'])
			$table_name = self::formatTableName($table_name);

		if (self::$dbtype === self::$mySQLConnection) {
			$query = 'ALTER TABLE ' . $table_name . ' ADD ' . self::formatColumn($column_name, $column_data) . ';';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$query = 'ALTER TABLE ' . $table_name . ' ADD COLUMN ' . self::formatColumn($column_name, $column_data) . ';';
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = 'ALTER TABLE ' . $table_name . ' ADD ' . self::formatColumn($column_name, $column_data) . ';';
		}
		
		if ($options['execute'])
			Database::query($query);

		self::_notify(get_class() . '::' . __FUNCTION__, $query, $table_name, $column_name, $column_data, $options);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));

		if ($options['return_query'])
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];

		$precision = (!empty($options['precision'])) ? '(' . $options['precision'] . ')' : '';
		$null = ($options['not_null'] == true) ? 'NOT NULL' : 'NULL';
		
		if(isset($options['default']) && is_callable($options['default'])) {
			$default = $options['default']();
		} else if($options['execute_default']){
			$default = (isset($options['default'])) ? 'DEFAULT ' . $options['default'].'' : '';
		} else {
			$default = (isset($options['default'])) ? 'DEFAULT \'' . $options['default'].'\'' : '';
		}
		
		$auto_increment = ($options['auto_increment'] == true) ? self::getAutoIncrement() : '';
		$unique = ($options['unique'] == true) ? 'UNIQUE' : '';

		if ($options['auto_increment'] == true && self::$dbtype === self::$postgreSQLConnection) {
			$options['type'] = 'SERIAL';
		}

		if (self::$dbtype === self::$mySQLConnection) {
			$query = $name . ' ' . self::columnTypeMap($options['type']) . $precision . ' ' . $null . ' ' . $default . ' ' . $auto_increment . ' ' . $unique;
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$query = $name . ' ' . self::columnTypeMap($options['type']) . $precision . ' ' . $null . ' ' . $default . ' ' . $auto_increment . ' ' . $unique;
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = $name . ' ' . self::columnTypeMap($options['type']) . $precision . ' ' . $null . ' ' . $default . ' ' . $auto_increment . ' ' . $unique;
		}
		
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

		if (self::$dbtype === self::$mySQLConnection) {
			$query = 'AUTO_INCREMENT';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$query = '';
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = 'IDENTITY (1,1)';
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $query);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));

		return $query;
	}

	/**
	 * Maps the column type depending on which database is set. For example, is the database is mysql
	 * and the type string is passed through, the return value is varchar. If the database is postgresql,
	 * the return type would be character varying.
	 *
	 * @param string $type The type of variabel to be matched
	 *
	 * @return string $match The matched type found
	 * @access public
	 */
	private static function columnTypeMap($type) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $type);

		$type = strtolower($type);

		$type = self::_applyFilter(get_class(), __FUNCTION__, $type, array('event' => 'args'));

		$types = array(
			'integers' => array(
				'match' => array('int', 'integer', 'numeric'), 
				'database' => array('mysql' => 'INT', 'mssql' => 'INT', 'postgresql' => 'INTEGER', 'sqlite' => 'INTEGER')), 
			'bigintegers' => array(
				'match' => array('bigint'), 
				'database' => array('mysql' => 'BIGINT', 'mssql' => 'BIGINT', 'postgresql' => 'BIGINT', 'sqlite' => 'INTEGER')),
			'double' => array(
				'match' => array('double', 'float', 'real'), 
				'database' => array('mysql' => 'DOUBLE', 'mssql' => 'FLOAT', 'postgresql' => 'DOUBLE PRECISION',  'sqlite' => 'REAL')), 
			'string' => array(
				'match' => array('string', 'varchar', 'character varying', 'nchar', 'native character', 'nvarchar'), 
				'database' => array('mysql' => 'VARCHAR', 'mssql' => 'VARCHAR', 'postgresql' => 'CHARACTER VARYING', 'sqlite' => 'TEXT')), 
			'text' => array(
				'match' => array('text', 'clob'), 
				'database' => array('mysql' => 'TEXT', 'mssql' => 'TEXT', 'postgresql' => 'TEXT', 'sqlite' => 'TEXT')), 
			'blob' => array(
				'match' => array('blob', 'bytea'), 
				'database' => array('mysql' => 'BLOB', 'mssql' => 'BLOB', 'postgresql' => 'BYTEA', 'sqlite' => 'TEXT')),
			'boolean' => array(
				'match' => array('boolean'), 
				'database' => array('mysql' => 'BOOLEAN', 'mssql' => 'BIT', 'postgresql' => 'BOOLEAN', 'sqlite' => 'INTEGER')), 
			'tinyint' => array(
				'match' => array('tinyint', 'smallint'), 
				'database' => array('mysql' => 'tinyint', 'mssql' => 'tinyint', 'postgresql' => 'smallint', 'sqlite' => 'INTEGER')), 
			'timestamp' => array(
				'match' => array('timestamp'), 
				'database' => array('mysql' => 'TIMESTAMP', 'mssql' => 'datetime', 'postgresql' => 'TIMESTAMP', 'sqlite' => 'TEXT')), 
			'date' => array(
				'match' => array('date', 'date/time', 'datetime'), 
				'database' => array('mysql' => 'TIMESTAMP', 'mssql' => 'datetime', 'postgresql' => 'TIMESTAMP', 'sqlite' => 'TEXT')), 
			'serial' => array(
				'match' => array('serial'), 
				'database' => array('mysql' => 'SERIAL', 'mssql' => 'unknown', 'postgresql' => 'serial', 'sqlite' => 'INTEGER')), 
			'bigserial' => array(
				'match' => array('bigserial'), 
				'database' => array('mysql' => 'unknown', 'mssql' => 'unknown', 'postgresql' => 'bigserial', 'sqlite' => 'INTEGER')), 
			'hstore' => array(
				'match' => array('hstore'), 
				'database' => array('mysql' => 'unknown', 'mssql' => 'unknown', 'postgresql' => 'hstore', 'sqlite' => 'unknown')),
			'uuid' => array(
				'match' => array('uuid', 'guid'), 
				'database' => array('mysql' => 'unknown', 'mssql' => 'guid', 'postgresql' => 'uuid', 'sqlite' => 'TEXT')),
			'ip' => array(
				'match' => array('ip', 'ipv4'), 
				'database' => array('mysql' => 'varchar', 'mssql' => 'varchar', 'postgresql' => 'cidr', 'sqlite' => 'TEXT')), 
			'ipv6' => array(
				'match' => array('ipv6'), 
				'database' => array('mysql' => 'varchar', 'mssql' => 'varchar', 'postgresql' => 'inet', 'sqlite' => 'TEXT')),
			'json' => array(
				'match' => array('json'), 
				'database' => array('mysql' => 'TEXT', 'mssql' => 'varchar', 'postgresql' => 'json', 'sqlite' => 'TEXT')),
			);
			

		foreach ($types as $key => $value) {
			if (in_array($type, $value['match'])) {
				$match = $value['database'][self::$dbtype];
				$match = self::_applyFilter(get_class(), __FUNCTION__, $match, array('event' => 'return'));
				return $match;
			}//end if
		}//end for each

	}//end type map
	
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table_name' => $table_name, 'column_name' => $column_name, 'options' => $options), array('event' => 'args'));
		$table_name = $filtered['table_name'];
		$column_name = $filtered['column_name'];
		$options = $filtered['options'];

		if ($options['format_table'])
			$table_name = self::formatTableName($table_name);
		
		if (self::$dbtype === self::$mySQLConnection) {
			$query = 'ALTER TABLE ' . $table_name . ' DROP COLUMN ' . $column_name . ';';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$query = 'ALTER TABLE ' . $table_name . ' DROP COLUMN ' . $column_name . ';';
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = 'ALTER TABLE ' . $table_name . ' DROP COLUMN ' . $column_name . ';';
		}

		if ($options['execute'])
			Database::query($query);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $query, $table_name, $column_name, $options);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));

		if ($options['return_query'])
			return $query;
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

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('table_name' => $table_name, 'options' => $options), array('event' => 'args'));
		$table_name = $filtered['table_name'];
		$options = $filtered['options'];

		if ($options['format_table'])
			$table_name = self::formatTableName($table_name);
		
		if (self::$dbtype === self::$mySQLConnection) {
			$query = 'DROP TABLE ' . $table_name . ';';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			$query = 'DROP TABLE ' . $table_name . ';';
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = 'DROP TABLE ' . $table_name . ';';
		}

		if ($options['execute'])
			Database::query($query);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $query, $table_name, $options);
		$query = self::_applyFilter(get_class(), __FUNCTION__, $query, array('event' => 'return'));

		if ($options['return_query'])
			return $query;
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
	protected static function _setMongoCollection($table_name, $options = array()){
		
		$defaults = array(
			'gridFS' => false
		);
		
		$options += $defaults;
		
		if(class_exists('\\MongoDB\Driver\Manager')) {
			$collection = self::$link -> selectCollection(self::$dbname ,$table_name);
		} else {
		
			if($options['gridFS']) {
				$collection = self::$link -> getGridFS( $table_name );
			} else {
				$collection = self::$link -> $table_name;
			}
		}
		
		return $collection;
	}

	/**
	 * Not sure what this function does
	 * 
	 *
	 * @todo dig into the function and redo
	 */
	public static function catchDBError() {
		if (self::$dbtype === self::$mySQLConnection) {
			$query = 'AUTO_INCREMENT';
		} else if (self::$dbtype === self::$postgreSQLConnection) {
			print pg_last_error(self::$link);
		} else if (self::$dbtype === self::$msSQLConnection) {
			$query = 'IDENTITY (1,1)';
		}
	}
	
}//end class
