<?php
/*
*Copyright 2011 ProdigyView LLC. All rights reserved.
*
*Redistribution and use in source and binary forms, with or without modification, are
*permitted provided that the following conditions are met:
*
*   1. Redistributions of source code must retain the above copyright notice, this list of
*      conditions and the following disclaimer.
*
*   2. Redistributions in binary form must reproduce the above copyright notice, this list
*      of conditions and the following disclaimer in the documentation and/or other materials
*      provided with the distribution.
*
*THIS SOFTWARE IS PROVIDED BY ProdigyView LLC ``AS IS'' AND ANY EXPRESS OR IMPLIED
*WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
*FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL ProdigyView LLC OR
*CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
*CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
*SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
*ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
*NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
*ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*The views and conclusions contained in the software and documentation are those of the
*authors and should not be interpreted as representing official policies, either expressed
*or implied, of ProdigyView LLC.
*/


class PVDatabase extends PVStaticObject {

	private static $theQuery;
	private static $link;
	private static $version;
	
	//Assigns the connection types values
	//Important for deciding which database to connect to
	private static $mySQLConnection="mysql";
	private static $postgreSQLConnection="postgresql";
	private static $oracleConnection="oracle";
	private static $msSQLConnection="mssql";
	private static $sqLiteConnection="sqlite";
	private static $uniqueName="pv_dbconnector";
	
	private static $connections=array();
	
	//Database Implementation
	private static $dbhost = "";
	private static $dbname = "";
	private static $dbuser = "";
	private static $dbpass = "";
	private static $dbtype = "";
	private static $dbschema = "";
	private static $dbprefix = "";
	private static $dbport = "";
	
	//Variables
	private static $row;
	
	public static function init(){
		self::$connections=array();
		
		if(file_exists(PV_DB_CONFIG)){
			include(PV_DB_CONFIG);
			if(is_array($connections))
				self::$connections += $connections;
		}
		
	}//end init
	
	/**
	 * Add a connection to the database class. The connection can be later
	 * used by calling the function PVDatabase::setDatbase().
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
	 * PVDatabase::addConnection('connection_1',$connection);
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
			$defaults=array(
				'dbhost'=>'',
				'dbname'=>'',
				'dbuser'=>'',
				'dbpass'=>'',
				'dbtype'=>'',
				'dbschema'=>'',
				'dbprefix'=>'',
				'dbport'=>''
			);
			$args += $defaults;
			
			self::$connections[$connection_name]['dbhost']=$args['dbhost'];
			self::$connections[$connection_name]['dbname']=$args['dbname'];
			self::$connections[$connection_name]['dbuser']=$args['dbuser'];
			self::$connections[$connection_name]['dbpass']=$args['dbpass'];
			self::$connections[$connection_name]['dbtype']=$args['dbtype'];
			self::$connections[$connection_name]['dbschema']=$args['dbschema'];
			self::$connections[$connection_name]['dbprefix']=$args['dbprefix'];
			self::$connections[$connection_name]['dbport']=$args['dbport'];
	}
	
	/**
	 *Set the database to one in the configuration file or to one passed used the
	 * PVDatabase::addConnection method(). Will close the other database link if open
	 * and create a new one. 
	 * 
	 * Example:
	 * PVDatabase::setDatabase(0);
	 *
	 * @param int profile_id: The ID of the profile set in the config.php file
	 * @return void
	 * @access public
	 */
	public static function setDatabase($profile_id=0){
		self::closeDB();
		
		self::$dbhost = self::$connections[$profile_id]['dbhost'];
		self::$dbuser = self::$connections[$profile_id]['dbuser'];
		self::$dbpass = self::$connections[$profile_id]['dbpass'];
		self::$dbtype = self::$connections[$profile_id]['dbtype'];
		self::$dbname = self::$connections[$profile_id]['dbname'];
		self::$dbport = self::$connections[$profile_id]['dbport'];
		self::$dbschema = self::$connections[$profile_id]['dbschema'];
		self::$dbprefix = self::$connections[$profile_id]['dbprefix'];
		self::connect();
	}
	
	/**
	 * Connect the that database based on the creditionals
	 * in the PHP file.
	 * 
	 * @return void
	 * @access private
	 */
	private static function connect(){
		// Connect to the database
		if(self::$dbtype==self::$mySQLConnection){
			mysqli_report (MYSQLI_REPORT_OFF);
			self::$link = new mysqli(self::$dbhost, self::$dbuser, self::$dbpass,self::$dbname);
		} else if ( self::$dbtype==self::$postgreSQLConnection ){
			self::$link = pg_connect("host=".self::$dbhost." port=".self::$dbport." dbname=".self::$dbname." user=".self::$dbuser." password=".self::$dbpass." ");
		} else if (self::$dbtype==self::$msSQLConnection){
			self::$link = sqlsrv_connect(self::$dbhost, array("UID" => self::$dbuser, "PWD" => self::$dbpass, "Database" => self::$dbname, 'ReturnDatesAsStrings'=>true ));
		} else if (self::$dbtype==self::$sqLiteConnection){
			self::$link = sqlite_open(self::$dbname);
		} else if (self::$dbtype==self::$oracleConnection){
			self::$link = oci_connect($user, $pass, $host);
			$d = new PDO('oci:dbname=$dbname', '$dbuser', '$dbpass');
		}
	}//end private
	
	/**
	 * Executes a SQL Query.passed to the function. The query passed
	 * should be sanitized for malicous code before being processed.
	 * 
	 * Example:
	 * $query='Select * FROM TABLE';
	 * $result=PVDatabase::query($query);
	 * 
	 * @param string $query A SQL query
	 * @return object $result Returns an object result related to the query passed
	 * @access public
	 */
	public static function query($query) {
		
		if(self::$dbtype==self::$mySQLConnection){
			self::$theQuery = $query;
			return self::$link->query($query);
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			self::$theQuery = $query;
			return pg_exec($query);
		}
		else if(self::$dbtype==self::$msSQLConnection){
			self::$theQuery = $query;
			return sqlsrv_query(self::$link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ) );
		}
		else if(self::$dbtype==self::$sqLiteConnection){
			self::$theQuery = $query;
			return sqlite_query(self::$link , $query);
		}
		else if(self::$dbtype==self::$oracleConnection){
			self::$theQuery = $query;
			$stid = oci_parse(self::$link, $query);
			return oci_execute($stid);
		}
		
	}//end query
	
	
	/**
	 * Returns the id of the last inserted string into the databse.
	 * returnField and returnTable are generally optional but required
	 * for databases such as PostgreSQL and MSSSQL
	 * 
	 * Example:
	 * $query="INSERT INTO TABLE('Test Data') VALUES('abc', '123')";
	 * $id=PVDatabase::return_last_insert_query($query, 'id', 'TABLE');
	 * 
	 * @param string $query The query thing to be executed
	 * @param string $returnField The field that is auto incremented and will be returned
	 * @param string $returnTable The table the auto-incremented value exist in
	 * @return mixed $id The id of the last inserted field
	 * @access public
	 */
	public static function return_last_insert_query($query, $returnField='', $returnTable='') {
			
		if(self::$dbtype==self::$mySQLConnection){
			self::$theQuery = $query;
			self::$link->query($query);
			return self::$link->insert_id;
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			self::$theQuery = $query." RETURNING $returnField ";
			$result=pg_exec($query." RETURNING $returnField ");
			
			while($row =self::fetchArray($result)){
				return $row[$returnField];
			}
		}
		else if(self::$dbtype==self::$msSQLConnection){
			self::$theQuery = $query;
			sqlsrv_query(self::$link, $query);
			$query="SELECT @@IDENTITY AS $returnField FROM $returnTable;";
			$result = self::query($query);
			$row = self::fetchArray($result);
			$field_value=$row[$returnField];
			return $field_value;
		}
		else if(self::$dbtype==self::$oracleConnection){
			self::$theQuery = $query;
			$stid = oci_parse(self::$link, $query);
			return oci_execute($stid);
		}
		
	}//end return_last_insert_query
	
	/**
	 * Get the number of rows return in a SELECT sql
	 * statement. Function with automatically decide which
	 * database to use.
	 * 
	 * Example:
	 * $result=PVDatabase::query("SELECT * FROM TABLE");
	 * $count=PVDatabase::resultRowCount($result);
	 * 
	 * @param object $resut A result from a query
	 * @return int $count The number of rows in that result.
	 * @access public
	 */
	public static function resultRowCount($result) {
		
		if(self::$dbtype==self::$mySQLConnection){
			return self::$link->affected_rows;
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			return pg_num_rows($result);
		}
		else if(self::$dbtype==self::$msSQLConnection){
			return sqlsrv_num_rows($result);
		}
		else if(self::$dbtype==self::$sqLiteConnection){
			return sqlite_num_rows($result);
		}
		else if(self::$dbtype==self::$oracleConnection){
			self::$theQuery = $query;
			$stid = oci_parse(self::$link, $query);
			return oci_execute($stid);
		}
	}//end result row count
	
	/**
	 * Fetches the data in each row retrieved from a
	 * result. Results are retuned as array
	 * 
	 * Example:
	 * $result=PVDatabase::query('SELECT title, description FROM TABLE');
	 * while($row=PVDatabase::fetchArray($result)){
	 * 		echo $row['title'];
	 * }
	 * 
	 * @param object result: A result from a query object
	 * @return array row: An assoctive array of a row from a table
	 */
	public static function fetchArray($result) {
		
		if(self::$dbtype==self::$mySQLConnection && get_class($result)=='mysqli_result'){
			return $result->fetch_array();
		}
		else if(self::$dbtype==self::$mySQLConnection && get_class($result)=='mysqli_stmt'){
			$result->fetch();
			return self::$row;
			
			$array=array();
			foreach(self::$row as $key=>$value) {
				$array[$key]=$value;
			}
			
			return $array;
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			return pg_fetch_array($result);
		}
		else if(self::$dbtype==self::$msSQLConnection && !empty($result)){
			return sqlsrv_fetch_array($result);
		}
		else if(self::$dbtype==self::$sqLiteConnection){
			return sqlite_fetch_array($result);
		}
		else if(self::$dbtype==self::$oracleConnection){
			$stid = oci_parse(self::$link, $result);
			return oci_fetch_array($stid, OCI_ASSOC);
		}
	
	}//end fetchArray
	
	/**
	 * Fetches the data in each row retrieved from a
	 * result. The results are compiled into an object
	 * and returned.
	 * 
	 * Example:
	 * $result=PVDatabase::query('SELECT title, description FROM TABLE');
	 * while($row=PVDatabase::fetchArray($result)){
	 * 		echo $row['title'];
	 * }
	 * 
	 * @param object $result A result from a query object
	 * @return array $row An assoctive array of a row from a table
	 */
	public static function fetchFields($result) {
		
		if(self::$dbtype==self::$mySQLConnection && get_class($result)=='mysqli_result'){
			return $result->fetch_fields();
		}
		else if(self::$dbtype==self::$mySQLConnection && get_class($result)=='mysqli_stmt'){
			$result_set=new PVCollection();
			
			while ($result->fetch() ){
				$object = new stdclass();
				foreach(self::$row as $key=>$value) {
					$object->$key=$value; 
				}
				$data=self::$row;
				$result_set->add($object);
			}
			
			return $result_set;
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			return pg_fetch_array($result);
		}
		else if(self::$dbtype==self::$msSQLConnection && !empty($result)){
			return sqlsrv_fetch_array($result);
		}
		else if(self::$dbtype==self::$sqLiteConnection){
			return sqlite_fetch_array($result);
		}
		else if(self::$dbtype==self::$oracleConnection){
			$stid = oci_parse(self::$link, $result);
			return oci_fetch_array($stid, OCI_ASSOC);
		}
	
	}//end fetchArray

	
	/**
	 * Sanitizes information before it is inserted into the database. Should be
	 * used on all user input to ensure security. Can sanitize a single string or
	 * an array of data.
	 * 
	 * Example::
	 * $name=PVDatabase::makeSafe($_POST['name']);
	 * $number=PVDatabase::makeSafe($_POST['number']);
	 * PVDatabase::query("INSERT INTO TABLE(name, number) VALES('$name', '$number');
	 * 
	 * @param mixed $string String can either be a string or an array of data
	 * @return mixed $sanitized_data If the input is a string, a string will be return
	 * if the input is an array, an array will be returned/
	 * @access public
	 */
	public static function makeSafe($string) {
		
		if(is_array($string)){
			$return_array=array();
			
			foreach($string as $key => $value){
				$return_array[$key]=self::makeSafe($value);
			}
			return $return_array;
			
		} else {
			if(self::$dbtype==self::$mySQLConnection){
				return self::$link->real_escape_string($string);
			}
			else if(self::$dbtype==self::$postgreSQLConnection){
				return pg_escape_string($string);
			}
			else if(self::$dbtype==self::$msSQLConnection){
				
				if ( !isset($string) or empty($string) ) return '';
					if ( is_numeric($string) ) return $string;
			
					$non_displayables = array(
						'/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
						'/%1[0-9a-f]/',             // url encoded 16-31
						'/[\x00-\x08]/',            // 00-08
						'/\x0b/',                   // 11
						'/\x0c/',                   // 12
						'/[\x0e-\x1f]/'             // 14-31
					);
					foreach ( $non_displayables as $regex ){
						$string = preg_replace( $regex, '', $string );
					}//end for each
					
					$string = str_replace("'", "''", $string );
					
					return $string;
			}
			else if(self::$dbtype==self::$sqLiteConnection){
				return sqlite_escape_string($string);
			}
			else if(self::$dbtype==self::$oracleConnection){
				$stid = oci_parse(self::$link, $result);
				return oci_fetch_array($stid, OCI_ASSOC);
			}
		}//end else
	
	}//end fetchArray
	
	/**
	 * Closes a database connection depending on the connection
	 * that has been set
	 * 
	 * @return void
	 * @access public
	 */
	public static function closeDB() {
		
		if(self::$dbtype==self::$mySQLConnection){
			self::$link->close();
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			pg_close(self::$link);
		}
		else if(self::$dbtype==self::$msSQLConnection){
			sqlsrv_close(self::$link);
		}
		else if(self::$dbtype==self::$sqLiteConnection){
			sqlite_close(self::$link);
		}
		else if(self::$dbtype==self::$oracleConnection){
			oci_close(self::$link);
		}
	
	}//end close()
	
	/**
	 * Returns the schema that is being used for this database connection. A
	 * '.' will be appended to the name of the schema if one exist. Schemas 
	 * are only necessary for postgresql and db2
	 * 
	 * Example:
	 * $table_name=PVDatabase::getSchema.'contacts';
	 * $query="INSERT INTO $table_name(name, phone) VALUES('John Smith', '999-9999')";
	 * PVDatabase::query($query);
	 * 
	 * @return string $schema Returns the name of the current schema. 
	 * @access public
	 */
	public static function getSchema(){
		if(empty(self::$dbschema)){
			return "";	
		}
		else{
			return 	self::$dbschema.".";
		}
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
	public static function clearTableData($tablename, $options=''){
		$query="TRUNCATE TABLE $tablename $options";
		self::query($query);
	}//end clearTableData
	
	
	/**
	 * Checks to see of a certain table exist within a database.
	 * 
	 * Example:
	 * if(!PVDatabase::tableExist('conacts')){
	 * 		//Create table code
	 * }
	 * 
	 * @param string $tablename The name of the table being checked if it exist
	 * @return boolean $exist Will be true if the tabe exist, else false;
	 * @access public
	 */ 
	public static function tableExist($tablename){
		$query="";
		
		if(self::$dbtype==self::$mySQLConnection){
			$query="show tables like \"$tablename\";";
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			$query="SELECT relname FROM pg_class  WHERE relname = '$tablename';";
		}
		else if(self::$dbtype==self::$sqLiteConnection){
			$query="SELECT name FROM sqlite_master WHERE type='table' AND name='$tablename'; ";
		}
		else if(self::$dbtype==self::$msSQLConnection){
			$query="SELECT * FROM SysObjects WHERE [Name] = '$tablename'; ";
		}	
		else if(self::$dbtype==self::$oracleConnection){
			//To be Filed in
		}
		
		$result=self::query($query);
		
		if(self::resultRowCount($result)<= 0){
			return 0;
		}
		else{
			return 1;
		}
	
	}//end
	
	/**
	 * Checkes if a column exist with a table. Make sure to enter
	 * the schema.table_name if needed.
	 * 
	 * Example:
	 * if(!PVDatabase::columnExist('test.conacts', 'first_name' )){
	 * 		//Code to create table
	 * }
	 * 
	 * @param string $table_name The name of the table to be checked
	 * @param string $filed_name:The name of the column to check if iexit
	 * 
	 * @return boolean $exist Returns true if exist, otherwise return false
	 */
	public static function columnExist($table_name, $field_name){
	
		if(self::$dbtype==self::$mySQLConnection){
			$query="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = '".self::$dbname."' AND table_name = '$table_name' AND column_name = '$field_name' ";
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			$query="SELECT attname FROM pg_attribute WHERE attrelid = (SELECT oid FROM pg_class WHERE relname = '$table_name') AND attname = '$field_name';";
		}
		else if(self::$dbtype==self::$msSQLConnection){
			$query="SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$table_name' AND COLUMN_NAME='$field_name';";
		}
		else if(self::$dbtype==self::$oracleConnection){
			
		}
		
		$result=self::query($query);
		
		if(self::resultRowCount($result)<= 0){
			return 0;
		}
		else{
			return 1;
		}
	
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
	public static function getSQLRandomOperator(){
		
		if(self::$dbtype==self::$mySQLConnection){
			return 'RAND()';	
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			return 'RANDOM()';	
		}
		else if(self::$dbtype==self::$oracleConnection){
			return 'RAND()';	
		}
		else if(self::$dbtype==self::$msSQLConnection){
			return 'RAND()';	
		}
		
	}//end getSQLRandomOperator
	
	/**
	 * Data entered into the database sometimes has characters such as
	 * '/' added to it. This function will remove those characters
	 * 
	 * Example:
	 * $name=PVDatabase::formatData($row['name']);
	 * OR
	 * $row=PVDatabase::formatData($row);
	 * 
	 * @param mixed string: Either a string or array to format
	 * @return mixed data: Data with database characters removed 
	 */
	public static function formatData($string){
		
		if(is_array($string)){
			$return_array=array();
			foreach($string as $key => $value){
				$return_array[$key]=self::formatData($value);
			}
			return $return_array;
		}
		else{
			if(self::$dbtype==self::$mySQLConnection){
				return stripslashes($string);
			}
			else{
				return $string;	
			}
		}//end else
	}//end formatRow
	
	/**
	 * The average function is a function used to get the averge
	 * of fields in a database. This function returns the AVG function
	 * for the set database.
	 * 
	 * Example:
	 * $query="SELECT ".PVDatabase:::dbAverageFunction('age')." as average_age FROM Table
	 * 
	 * @param string field: The Field whose average value will be returned
	 * @return: string average_function: The function needed to get the average value ina SQL string
	 */
	public static function dbAverageFunction($field){
		
		if(self::$dbtype==self::$mySQLConnection){
			return ' AVG('.$field.') ';	
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			return ' AVG('.$field.') ';	
		}
		else if(self::$dbtype==self::$oracleConnection){
			return ' AVG('.$field.') ';	
		}
		else if(self::$dbtype==self::$msSQLConnection){
			return ' AVG('.$field.') ';	
		}
		
	}//end getAverageDB
	
	/**
	 * Returns the current databse being used.
	 * 
	 * @return string database: The database being used
	 */
	public static function getDatabaseType(){
		return self::$dbtype;
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
	 * 
	 * @return array results: Returns the
	 */
	public static function getPagininationOffset($table, $join_clause='', $where_clause='', $current_page=0, $results_per_page=20, $order_by=''){
		
		$query="SELECT COUNT(*) FROM $table $join_clause $where_clause";
		
		$result= self::query($query);
		$total_pages = self::fetchArray($result);
		$total_pages = $total_pages['COUNT(*)'];
		
		//Get The Start Page
		if($current_page){ 
			$start_location = ($current_page - 1) * $results_per_page;
		}
		else {
			$start_location = 0;
		}
		
		$last_page = ceil($total_pages/$results_per_page);
		
		if ($current_page < 1)  { 
		 	$current_page = 1; 
		 } 
		 else if ($current_page > $last_page) { 
		 	$current_page = $last_page; 
		 } 
		 
		 $database_type=self::getDatabaseType();
		 
		 if($database_type=='mysql'){
			$limit_offset=' LIMIT ' .($current_page - 1) * $results_per_page .',' .$results_per_page; 
		 }
		 else if($database_type=='postgresql'){
			$limit_offset=' LIMIT ' .($current_page - 1) * $results_per_page .' OFFSET ' .$results_per_page;  
		 }
		 else if($database_type=='mssql'){
			$limit_offset=' RowNum >= ' .$start_location.' RowNum < ' .$start_location+$results_per_page;
			$from_clause=" ( SELECT    ROW_NUMBER() OVER ( ORDER BY $order_by ) AS RowNum, * FROM      $table $where_clause ) AS RowConstrainedResult ";
		 }
		
		$return_array=array(
			'limit_offset'=>$limit_offset,
			'current_page'=>$current_page,
			'last_page'=>$last_page,
			'start_location'=>$start_location,
			'total_pages'=>$total_pages,
			'from_clause'=>$from_clause
		);
		
		return $return_array;
		
	}//end
	
	/**
	 * Every connection to a database has what is known as a link to that database.
	 * For external madification  of the link, this method will retun the current link.
	 * 
	 * @return dbojbect link: Connection to the set database.
	 */
	public static function getDatabaseLink(){
		return self::$link;
	}//end getDatabaseLink
	
	/**
	 * Insert information into the databas without explicitly writing the
	 * query.Does not use a prepared statement.
	 * 
	 * @param string table_name: The name of the information is being inserted into.
	 * @param array data: Information to be inserted into that table.The key is the column and the key's value is the colums value.
	 * @param array data_types.: Still being worked on.
	 * 
	 * @return void
	 */
	public static function insertIntoDatabase($table_name, $data, $data_types=''){
		
		if(!empty($table_name)){
			$data=self::makeSafe($data);
			$first=1;
			foreach($data as $key=>$value){
				
				if($first==0){
					$columns.=' ,'.$key;
					$values.=' ,\''.$value.'\' ';
				}
				else{
					$columns=$key;
					$values=' \''.$value.'\' ';
				}
				
				$first=0;
			}//end foreach
			
			$query='INSERT INTO '.$table_name.'('.$columns.') VALUES('.$values.')';
			self::query($query);
		}
		
	}//end insertIntoDatabase
	
	/**
	 * Executes a prepared Query that will be inserted into the database. Function still needs
	 * work before being used.
	 */
	public static function  preparedQuery($query, $data, $formats=''){
		
		if(self::$dbtype==self::$mySQLConnection){
			self::$link->prepare($query);
			$count=1;
			
			foreach($data as $key=>$value){
				self::$link->bindParam($count, $value);
				$count++;
			}//end foreach
			
			return self::$link->execute();
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			$result=pg_prepare(self::$link, '', $query);
			$result = pg_execute(self::$link , '', $data); 
			return $result;
		}
		else if(self::$dbtype==self::$oracleConnection){
			
		}
		else if(self::$dbtype==self::$msSQLConnection){
			$stmt = sqlsrv_prepare(self::$link, $query, $data);
			return sqlsrv_execute( $stmt);
		}
		
	}//end preparedQuery
	
	/**
	 *  Function needs improvment.
	 * @access public
	 * @todo write better code
	 */
	public static function preparedInsert($table_name, $data, $formats=array()){
		
		$query='INSERT INTO '.$table_name;
		$values='';
		$placeholders='';
		
		if(!empty($data)){
			$values.='(';
			$placeholders.='(';
		}//end if
		
		$first=1;
		foreach($data as $key=>$value){
			if($first){
				$values.=$key;
				$placeholders.=' ? ';
			}
			else{
				$values.=' , '.$key;
				$placeholders.=', ? ';
			}
			
			$first=0;
		}//end foreach
		
		
		if(!empty($data)){
			$values.=')';
			$placeholders.=')';
		}//end if
		
		$query.=$values.$placeholders;
		
		
		if(self::$dbtype==self::$mySQLConnection){
			
			self::$link->prepare($query);
			$count=1;
			foreach($data as $key=>$value){
				self::$link->bindParam($count, $value);
				$count++;
			}//end foreach
			
			return self::$link->execute();		
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			
			$result=pg_prepare(self::$link, '', $query);
			
			$result = pg_execute(self::$link , '', $data); 
			
			return $result;
			
		}
		else if(self::$dbtype==self::$oracleConnection){
			
		}
		else if(self::$dbtype==self::$msSQLConnection){
			
			$stmt = sqlsrv_prepare(self::$link, $query, $data);
			
			return sqlsrv_execute( $stmt);
		}
		
	}//end preparedInsert
	
	/**
	 * Inserts a query into the database and returns the id of the field that was last inserted.
	 * The query will be a prepared statement.
	 * 
	 * @param string table_name: The name of the table the information will be inserted into.
	 * @param string returnField: The field that will be returned as the ID. Used in postgresql..
	 * @param string returnTable: The table the returnField is in. Used for MSSQL.
	 * @param awrray data: The data to be inserted in the format of the key being the column and the 
	 * key's value being the data.
	 * @param array formats: Still in progress. Formats a preparted statemet.
	 * @access public
	 * @todo write better code
	 */
	public static function preparedReturnLastInsert($table_name, $returnField, $returnTable,  $data, $formats=array()) {
		
		$query='INSERT INTO '.$table_name;
		
		$values='';
		$placeholders=' VALUES';
		
		if(!empty($data)){
			$values.='(';
			$placeholders.='(';
		}//end if
		
		$first=1;
		$params=array();
		$count=0;
		foreach($data as $key=>$value){
			if($first){
				$values.=$key;
				$placeholders.=' '.self::getPreparedPlaceHolder($count+1).' ';
			} else {
				$values.=' , '.$key;
				$placeholders.=', '.self::getPreparedPlaceHolder($count+1).' ';
			}
			
			$params[$key]=(isset($formats[$count])) ? $formats[$count] :'s';
			$count ++;
			$first=0;
		}//end foreach
		
		if(!empty($data)){
			$values.=')';
			$placeholders.=')';
		}//end if
		
		$query.=$values.$placeholders;
		
		if(self::$dbtype==self::$mySQLConnection){
			$stmt=self::$link->prepare($query);
			self::bindParameters($stmt, $params);
			foreach($data as $key=>$value){
				$params[$key]=$value;
			}
			
			$stmt->execute();
			return self::$link->insert_id;
		} else if (self::$dbtype==self::$postgreSQLConnection){
			$result=pg_prepare(self::$link, '', $query." RETURNING $returnField ");
			$result = pg_execute(self::$link , '', $data); 
			
			while($row =self::fetchArray($result)){
				return $row[$returnField];
			}//end while
			
		}
		else if(self::$dbtype==self::$oracleConnection){
			
		}
		else if(self::$dbtype==self::$msSQLConnection){
			
			$stmt = sqlsrv_prepare(self::$link, $query, $data);
			
			sqlsrv_execute( $stmt);
			
			$query="SELECT @@IDENTITY AS $returnField FROM $returnTable;";
			$result = self::query($query);
			$row = self::fetchArray($result);
			$field_value=$row[$returnField];
			return $field_value;
		}
	}//end preparedReturnLastInsert
	
	/**
	 * Executes a prepared select statement. Complex statements are complex enough that the data must be
	 * formated outside. The passed query should already have the ? inserted for values. The data array passed should
	 * correspond to that values.Futures version will have a select statement that handles the data in a better way.
	 * 
	 * @param string $query A query of formatted data to be inserted into the database.
	 * @param array $data: Data to be inserted into the database. The key should be the column name and the value
	 * should be the column's value.
	 * 
	 * @return data result: Retuns a result that will need to be run through fetch process.
	 * @access public
	 * @todo write better code
	 */
	public static function preparedSelect($query, $data, $formats=array()){
		$params=array();
		
		$count=0;
		foreach($data as $key=>$value){
			$params[$key]=(isset($formats[$count])) ? $formats[$count] :'s';
			$count++;
		}//end foreach

		if(self::$dbtype==self::$mySQLConnection){
			
			$stmt=self::$link->prepare($query);
			
			if(!empty($params)) {
				self::bindParameters($stmt, $params);
				foreach($data as $key=>$value){
					$params[$key]=$value;
				}
			}
			
			$stmt->execute();
			$stmt->store_result();
			self::$row = array();
			self::stmt_bind_assoc($stmt, self::$row);
			
			return $stmt;	
		}
		else if(self::$dbtype==self::$postgreSQLConnection){
			
			$result=pg_prepare(self::$link, '', $query);
			
			$result = pg_execute(self::$link , '', $data); 
			
			return $result;
		}
		else if(self::$dbtype==self::$oracleConnection){
			
		}
		else if(self::$dbtype==self::$msSQLConnection){
			
			$stmt = sqlsrv_prepare(self::$link, $query, $data);
			
			return sqlsrv_execute( $stmt);
		}
		
	}//end preparedSelect
	
	
	/**
	 *  Updates a tables data using a prepared query.
	 * 
	 * @param string $table The name of the table to be updated.
	 * @param array $data
	 * @param array $wherelist
	 * 
	 * @access public
	 * @todo write better code
	 */
	public static function preparedUpdate($table, $data, $wherelist, $formats=array(), $whereformats=array()){
		
		$query='UPDATE '.$table.' SET ';
		$params=array();
		$params_holder=array();
		
		$first=1;
		$count=0;
		foreach($data as $key=>$value){
			$params[$key]=(isset($formats[$count])) ? $formats[$count] :'s';	
			$params_holder[$key]=$value;	
			
			if($first){
				$query.=$key.'='.self::getPreparedPlaceHolder($count+1).' ';	
			}
			else{
				$query.=','.$key.'='.self::getPreparedPlaceHolder($count+1).' ';	
			}
			$count++;
			$first=0;
		}//end foreach
		
		$first=1;
		if(is_array($wherelist) && !empty($wherelist)){
			$query.=' WHERE ';
			$count2=0;
			foreach($wherelist as $key=>$value){
				$params[$key]=(isset($wherelist[$count2])) ? $formats[$count2] :'s';
				$params_holder[$key]=$value;
				
				if($first){
					$query.=$key.'='.self::getPreparedPlaceHolder($count+1).' ';	
				} else {
					$query.=' AND '.$key.'='.self::getPreparedPlaceHolder($count+1).' ';		
				}
				$count++;
				$count2++;
				$first=0;
			}//end foreach
		}//end if is_array and not emptys
		
		if(self::$dbtype==self::$mySQLConnection){
			
			$stmt=self::$link->prepare($query);
			self::bindParameters($stmt, $params);
			
			foreach($params_holder as $key=>$value){
				$params[$key]=$value;
			}
			
			return $stmt->execute();
		} else if (self::$dbtype==self::$postgreSQLConnection){
			
			$result=pg_prepare(self::$link, '', $query);
			
			$result = pg_execute(self::$link , '', $params_holder); 
			
			return $result;
		}
		else if(self::$dbtype==self::$oracleConnection){
			
		}
		else if(self::$dbtype==self::$msSQLConnection){
			
			$stmt = sqlsrv_prepare(self::$link, $query, $params);
			
			return sqlsrv_execute( $stmt);
		}
		
	}//edn preparedUpdate
	
	/**
	 * Deletes a row in the database spcefied by parameters passed. Use this function
	 * with caution.
	 * 
	 * @param string table: The table the information will be deleted from.
	 * @param array wherelist: An array of whats fields to use when deleting the data. The key of the array
	 * should be the column name and the array's key value should be the value present in the column.
	 * @param array whereformats; Formats for the where.
	 * 
	 * @return void
	 */
	public static function preparedDelete($table, $wherelist=array(), $whereformats=array()){
		
		$query='DELETE FROM '.$table;
		
		if(is_array($wherelist) && !empty($wherelist)){
			$params=array();
			$query.=' WHERE ';
			$count=0;
			$first=1;
			foreach($wherelist as $key=>$value){
				$params[$key]=(isset($wherelist[$count])) ? $formats[$count] :'s';
				if($first){
					$query.=$key.'='.self::getPreparedPlaceHolder($count+1).' ';	
				} else {
					$query.=' AND '.$key.'='.self::getPreparedPlaceHolder($count+1).' ';	
				}
				
				$first=0;
				$count++;
			}//end foreach
		}
		
		if(self::$dbtype==self::$mySQLConnection){
			
			$stmt=self::$link->prepare($query);
			self::bindParameters($stmt, $params);
			foreach($wherelist as $key=>$value){
				$params[$key]=$value;
			}
			
			$stmt->execute();
			
		} else if (self::$dbtype==self::$postgreSQLConnection){
			
			$result=pg_prepare(self::$link, '', $query);
			$result = pg_execute(self::$link , '', $wherelist); 
			return $result;
		} else if (self::$dbtype==self::$oracleConnection){
			
		} else if (self::$dbtype==self::$msSQLConnection){
			
			$stmt = sqlsrv_prepare(self::$link, $query, $wherelist);
			return sqlsrv_execute( $stmt);
		}
		
	}//end preparedDelete
	
	public static function getPreparedPlaceHolder($count=1) {
		if(self::$dbtype==self::$mySQLConnection){
			return '?';
		} else if (self::$dbtype==self::$postgreSQLConnection){
			return '$'.$count;
		} else if (self::$dbtype==self::$oracleConnection){
			return '?';
		} else if (self::$dbtype==self::$msSQLConnection){
			return '?';
		} 
		
		return '?';
	}//end getPreparedPlaceHolder
	
	public static function formatTableName($table_name){
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	private static function bindParameters(&$statement, &$params){
	  $args   = array();
	  $args[] = implode('', array_values($params));
	  foreach ($params as $paramName => $paramType)
	  {
		$args[] = &$params[$paramName];
		$params[$paramName] = null;
	  }
	
	  call_user_func_array(array(&$statement, 'bind_param'), $args);
	}
	
	private function stmt_bind_assoc (&$stmt, &$out) {
	    $data = mysqli_stmt_result_metadata($stmt);
	    $fields = array();
	    $out = array();
	
	    $fields[0] = $stmt;
	    $count = 1;
	
	    while($field = mysqli_fetch_field($data)) {
	        $fields[$count] = &$out[$field->name];
	        $count++;
	    }    
	   @call_user_func_array(mysqli_stmt_bind_result, $fields);
	}
	
	public static function createTable($table_name, $columns=array(), $options=array()) {
		$defaults=array(
			'format_table'=>false,
			'execute'=>true,
			'return_query'=>true,
			'primary_key'=>'',	
		);
		$options += $defaults;
		
		$column_query='';
		if(!empty($columns) && is_array($columns)) {
			$first=1;
			foreach($columns as $column_name=>$column) {
				$column_query.=(!$first) ? ',' : '';
				$column_query.=self::formatColumn($column_name, $column);
				$first=0;
			}
		}
		
		if(!empty($options['primary_key']))
			$column_query.=',PRIMARY KEY('.$options['primary_key'].')';
			
		if(!empty($column_query) )
			$column_query='('.$column_query.')';
		
		if($options['format_table']) 
			$table_name=self::formatTableName($table_name);
		
		if(self::$dbtype==self::$mySQLConnection){
			$query='CREATE TABLE '.$table_name.' '.$column_query.';';	
		} else if(self::$dbtype==self::$postgreSQLConnection){
			$query='CREATE TABLE '.$table_name.' ;';
		} else if(self::$dbtype==self::$msSQLConnection){
			$query='CREATE TABLE '.$table_name.' ;';
		}
		
		if($options['execute'])
			PVDatabase::query($query);
		if($options['return_query'])
			return $query;
	}

	public static function addColumn($table_name, $column_name, $column_data=array(), $options=array()) {
		$defaults=array(
			'format_table'=>false,
			'execute'=>true,
			'return_query'=>true,	
		);
		$options += $defaults;
		
		if($options['format_table']) 
			$table_name=self::formatTableName($table_name);
		
		if(self::$dbtype==self::$mySQLConnection){
			$query='ALTER TABLE '.$table_name.' ADD '.self::formatColumn($column_name,$column_data).';';	
		} else if(self::$dbtype==self::$postgreSQLConnection){
			$query='ALTER TABLE '.$table_name.' ADD COLUMN '.self::formatColumn($column_name,$column_data).';';
		} else if(self::$dbtype==self::$msSQLConnection){
			$query='ALTER TABLE '.$table_name.' ADD '.self::formatColumn($column_name,$column_data).';';
		}
		
		if($options['execute'])
			PVDatabase::query($query);
		if($options['return_query'])
			return $query;
	}
	
	public static function formatColumn($name, $options=array()) {
		$defaults=array(
			'primary_key'=>false,
			'unique'=>false,
			'not_null'=>true,
			'type'=>'string',
			'precision'=>'',
			'default'=>null,
			'auto_increment'=>false
		);
		
		$options += $defaults;
		
		$precision = (!empty($options['precision'])) ? '('.$options['precision'].')' : '';
		$null = ($options['not_null']==true) ? 'NOT NULL' : 'NULL';
		$default = (isset($options['default'])) ? 'DEFAULT '.$options['default'] : '';
		$auto_increment = ($options['auto_increment']==true) ? self::getAutoIncrement() : '';
		$unique = ($options['unique']==true) ? 'UNIQUE' : '';
		
		if($options['auto_increment']==true && self::$dbtype==self::$postgreSQLConnection) {
			$options['type']='SERIAL';
		}
		
		if(self::$dbtype==self::$mySQLConnection){
			$query=$name.' '.self::columnTypeMap($options['type']).$precision.' '.$null. ' '.$default.' '.$auto_increment.' '.$unique;
		} else if(self::$dbtype==self::$postgreSQLConnection){
			$query=$name.' '.self::columnTypeMap($options['type']).$precision.' '.$null. ' '.$default.' '.$auto_increment.' '.$unique;
		} else if(self::$dbtype==self::$msSQLConnection){
			$query=$name.' '.self::columnTypeMap($options['type']).$precision.' '.$null. ' '.$default.' '.$auto_increment.' '.$unique;
		}

		return $query;
	}
	
	private static function getAutoIncrement(){
		if(self::$dbtype==self::$mySQLConnection){
			return 'AUTO_INCREMENT';
		} else if(self::$dbtype==self::$postgreSQLConnection){
			$query='';
		} else if(self::$dbtype==self::$msSQLConnection){
			$query='IDENTITY (1,1)';
		}
	}
	
	private static function columnTypeMap($type) {
		$type=strtolower($type);
		
		$types=array(
			'integers'=>array(
				'match'=>array('int', 'integer'),
				'database'=>array('mysql'=>'INT', 'mssql'=>'INT', 'postgresql'=>'INTEGER')
			),
			'double'=>array(
				'match'=>array('double', 'float'),
				'database'=>array('mysql'=>'DOUBLE', 'mssql'=>'FLOAT', 'postgresql'=>'DOUBLE PRECISION')
			),
			'string'=>array(
				'match'=>array('string', 'varchar','character varying'),
				'database'=>array('mysql'=>'VARCHAR', 'mssql'=>'VARCHAR', 'postgresql'=>'CHARACTER VARYING')
			),
			'text'=>array(
				'match'=>array('text'),
				'database'=>array('mysql'=>'TEXT', 'mssql'=>'TEXT', 'postgresql'=>'TEXT')
			),
			'boolean'=>array(
				'match'=>array('boolean'),
				'database'=>array('mysql'=>'BOOLEAN', 'mssql'=>'BIT', 'postgresql'=>'BOOLEAN')
			),
			'timestamp'=>array(
				'match'=>array('timestamp'),
				'database'=>array('mysql'=>'TIMESTAMP', 'mssql'=>'datetime', 'postgresql'=>'TIMESTAMP')
			),
			'date'=>array(
				'match'=>array('date', 'date/time'),
				'database'=>array('mysql'=>'TIMESTAMP', 'mssql'=>'datetime', 'postgresql'=>'TIMESTAMP')
			),
			'serial'=>array(
				'match'=>array('serial'),
				'database'=>array('mysql'=>'SERIAL', 'mssql'=>'unknown', 'postgresql'=>'serial')
			),
			'bigserial'=>array(
				'match'=>array('bigserial'),
				'database'=>array('mysql'=>'unknown', 'mssql'=>'unknown', 'postgresql'=>'bigserial')
			),
		);
		
		foreach($types as $key=>$value){
			if(in_array($type, $value['match'])){
				return $value['database'][self::$dbtype];
			}//end if
		}//end for each
		
	}//end type map
	
	/**
	 * Returns the table name for the application's permissions.
	 * The name will be formated to match the current database
	 * schema. So if a schema is defined. the table name will have
	 * the schama name, period, then the table name. Likewise for if a
	 * table prefix is defined.
	 * 
	 * Example:
	 * Config file:
	 * $schema = MySchema
	 * $prefix = 'pv_
	 * 
	 * Function call
	 * echo PVDatabase::getTableName();
	 * 
	 * Result
	 * MySchema.pv_tableName
	 */
	public static function getApplicationPermissionsTableName(){
		
		$table_name='application_permissions';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	public static function getAccessLevelsTableName(){
		
		$table_name='access_levels';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getApplicationsTableName(){
		
		$table_name='applications';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContainersTableName(){
		
		$table_name='containers';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContainerModulesTableName(){
		
		$table_name='container_modules';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentTableName(){
		
		$table_name='content';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getAudioContentTableName(){
		
		$table_name='content_audio';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentCategoriesTableName(){
		
		$table_name='content_categories';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentCategoryRelationsTableName(){
		
		$table_name='content_category_relations';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentRelationsTableName(){
		
		$table_name='content_relations';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentCommentsTableName(){
		
		$table_name='content_comments';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getEventContentTableName(){
		
		$table_name='content_events';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentFieldRelationsTableName(){
		
		$table_name='content_field_relations';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getFileContentTableName(){
		
		$table_name='content_files';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getImageContentTableName(){
		
		$table_name='content_images';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentModifiersTableName(){
		
		$table_name='content_modifiers';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getProductContentTableName(){
		
		$table_name='content_product';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentRatingTableName(){
		
		$table_name='content_rating';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentTaxonomyTableName(){
		
		$table_name='content_taxonomy';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getTextContentTableName(){
		
		$table_name='content_text';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentTypeTableName(){
		
		$table_name='content_type';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	public static function getContentMultiAuthorTableName(){
		
		$table_name='content_multi_author';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getVideoContentTableName(){
		
		$table_name='content_video';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getContentViewsTableName(){
		
		$table_name='content_views';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getFieldsTableName(){
		
		$table_name='fields';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getFieldsOptionsTableName(){
		
		$table_name='fields_options';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getFieldValuesTableName(){
		
		$table_name='fields_values';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getFieldTypesTableName(){
		
		$table_name='fieldtypes';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getFieldOutputTableName(){
		
		$table_name='field_output';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getLoginTableName(){
		
		$table_name='users';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	public static function getUsersTableName(){
		
		$table_name='users';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getMenuTableName(){
		
		$table_name='menu';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getMenuItemsTableName(){
		
		$table_name='menu_items';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getModuleAdminTableName(){
		
		$table_name='module_admin';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getModulesTableName(){
		
		$table_name='modules';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getModulePermissionsTableName(){
		
		$table_name='module_permissions';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	
	public static function getMVCTableName(){
		
		$table_name='mvc';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	
	public static function getOptionsTableName(){
		
		$table_name='options';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	
	public static function getPageContainersRelationshipTableName(){
		
		$table_name='page_containers';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	
	public static function getPagesTableName(){
		
		$table_name='pages';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getPageModuleRelationshipTableName(){
		
		$table_name='page_module_relationship';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getPluginsTableName(){
		
		$table_name='plugins';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getPluginPermissionsTableName(){
		
		$table_name='plugin_permissions';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getSessionTableName(){
		
		$table_name='session';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getSessionTrackerTableName(){
		
		$table_name='session_tracker';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getTemplatesTableName(){
		
		$table_name='templates';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getTemplatePositionsTableName(){
		
		$table_name='template_positions';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getUserActivationTableName(){
		
		$table_name='user_activation';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getPointsTableName(){
		
		$table_name='points';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getUserRelationsTableName(){
		
		$table_name='user_relations';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	public static function getUserRolesRelationsTableName(){
		
		$table_name='user_roles_relations';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	public static function getSubscriptionTableName(){
		
		$table_name='subscriptions';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	public static function getUserTaxonomyTableName(){
		
		$table_name='user_taxonomy';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
	
	public static function getUserRolesTableName(){
		
		$table_name='user_roles';
		
		$table_name=self::$dbprefix.$table_name;
		
		if(!empty(self::$dbschema)){
			$table_name=self::$dbschema.'.'.$table_name;
		}
		
		return $table_name;
		
	}//end  getApplicationPermissionsTable
	
}//end class
?>