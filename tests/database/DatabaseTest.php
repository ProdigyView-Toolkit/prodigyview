<?php

use prodigyview\database\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase{
	
	private $_db = null;
	
	private $_table = 'users';
	
	private $_host = 'postgres';
	
	private $_port = 5432;
	
	private $_connectionName = 'testdb';
	
	private $_login = 'prodigyview';
	
	private $_password = 'prodigyview';
	
	private $_database= 'prodigyview';
	
	private $_connections = array(
		'postgresql_connection' => array(
			'host'=>'postgres',
			'login' => 'prodigyview',
			'password' => 'prodigyview',
			'database' => 'prodigyview',
			'port' => 5432,
			'type'=> 'postgresql'
		),
		'mysql_connection' => array(
			'host'=>'mysql',
			'login' => 'prodigyview',
			'password' => 'prodigyview',
			'database' => 'prodigyview',
			'port' => 3306,
			'type'=> 'mysql'
		),
	);
	
	private $_postgresqlColumns = array(
		'id'=> array('type' => 'serial', 'auto_increment' => true, 'primary_key'=> true),
		'email'=> array('type' => 'string', 'precision'=>255, 'unique'=> true),
		'name' => array('type' => 'string', 'precision'=> 100, 'not_null' => false),
		'bio' => array('type' => 'string', 'default' => '')
	);
	
	private $_mysqlColumns = array(
		'id'=> array('type' => 'int', 'auto_increment' => true, 'primary_key'=> true),
		'email'=> array('type' => 'string', 'precision'=>255, 'unique'=> true),
		'name' => array('type' => 'string', 'precision'=> 100, 'not_null' => false),
		'bio' => array('type' => 'text')
	);
	
	private $_addColumn = array(
		'is_active' => array('type' => 'tinyint', 'default' =>0)
	);
	
	private $_data = array(
		array('email' => 'jon@example.com', 'name'=> 'Jon Doe', 'bio' => 'Most Common Name Ever'),
		array('email' => 'jane@example.com', 'name'=> 'Jane Doe', 'bio' => '2nd Most Common Name Ever')
	);
	
	protected function setUp() {
		
		Database::init();
		
		if(!Database::hasConnection('postgresql_connection')) {
			Database::addConnection('postgresql_connection',$this->_connections['postgresql_connection']);
		}
		
		if(!Database::hasConnection('mysql_connection')) {
			Database::addConnection('mysql_connection',$this->_connections['mysql_connection']);
		}
	}
	
	public function testPostgresConnectionVariables() {
		//$this->assertEquals($this->_login, Database::getLogin());
		//$this->assertEquals($this->_host, Database::getHost());	
		//$this->assertEquals($this->_port, Database::getPort());	
		//$this->assertEquals($this->_database, Database::getDatabase());
		$this->assertEquals('postgresql_connection', Database::getConnectionName());	
		$this->assertEquals('postgresql', Database::getDatabaseType());	
				
		
	}
	
	public function testPostgresTableExistFalse() {
		
		$result = Database::tableExist('not_found_table');
		
		$this->assertFalse($result);
	}
	
	public function testPostgresCreateTableString() {
		
		$table_name = 'test_table';
		
		$result = Database::createTable($table_name, array(), array('execute'=>false));
		
		$this->assertEquals('CREATE TABLE ' . $table_name . ' ;', $result);
	}
	
	public function testPostgresColumnMapInteger() {
		
		$result = Database::columnTypeMap('int');
		
		$this->assertEquals('INTEGER', $result);
		
	}
	
	public function testPostgresColumnMapBigInt() {
		
		$result = Database::columnTypeMap('bigint');
		
		$this->assertEquals('BIGINT', $result);
		
	}
	
	public function testPostgresColumnMapDouble() {
		
		$result = Database::columnTypeMap('double');
		
		$this->assertEquals('DOUBLE PRECISION', $result);
		
	}
	
	public function testPostgresColumnMapString() {
		
		$result = Database::columnTypeMap('string');
		
		$this->assertEquals('CHARACTER VARYING', $result);
		
	}

	public function testPostgresColumnMapText() {
		
		$result = Database::columnTypeMap('text');
		
		$this->assertEquals('TEXT', $result);
		
	}
	
	public function testPostgresColumnMapBlob() {
		
		$result = Database::columnTypeMap('blob');
		
		$this->assertEquals('BYTEA', $result);
		
	}
	
	public function testPostgresColumnMapBoolean() {
		
		$result = Database::columnTypeMap('boolean');
		
		$this->assertEquals('BOOLEAN', $result);
		
	}
	
	public function testPostgresColumnMapTinyInt() {
		
		$result = Database::columnTypeMap('tinyint');
		
		$this->assertEquals('SMALLINT', $result);
		
	}
	
	public function testPostgresColumnMapTimestamp() {
		
		$result = Database::columnTypeMap('timestamp');
		
		$this->assertEquals('TIMESTAMP', $result);
		
	}
	
	public function testPostgresColumnMapDate() {
		
		$result = Database::columnTypeMap('datetime');
		
		$this->assertEquals('TIMESTAMP', $result);
		
	}
	
	public function testPostgresColumnSerial() {
		
		$result = Database::columnTypeMap('serial');
		
		$this->assertEquals('SERIAL', $result);
		
	}
	
	
	public function testPostgresColumnBigSerial() {
		
		$result = Database::columnTypeMap('bigserial');
		
		$this->assertEquals('BIGSERIAL', $result);
		
	}
	
	public function testPostgresColumnHstore() {
		
		$result = Database::columnTypeMap('hstore');
		
		$this->assertEquals('HSTORE', $result);
		
	}
	
	public function testPostgresColumnUUID() {
		
		$result = Database::columnTypeMap('uuid');
		
		$this->assertEquals('UUID', $result);
		
	}
	
	public function testPostgresColumnIP() {
		
		$result = Database::columnTypeMap('ip');
		
		$this->assertEquals('CIDR', $result);
		
	}
	
	public function testPostgresColumnInet() {
		
		$result = Database::columnTypeMap('inet');
		
		$this->assertEquals('INET', $result);
		
	}
	
	public function testPostgresColumnJson() {
		
		$result = Database::columnTypeMap('json');
		
		$this->assertEquals('JSON', $result);
		
	}
	
	public function testPostgresIDColumn() {
		
		$column = 'id';
		
		$result = Database::formatColumn($column, $this->_postgresqlColumns[$column]);
		
		$this ->assertEquals('id SERIAL NOT NULL', trim($result));
		
	}
	
	public function testPostgresEmailColumn() {
		
		$column = 'email';
		
		$result = Database::formatColumn($column, $this->_postgresqlColumns[$column]);
		
		$this ->assertEquals('email CHARACTER VARYING(255) NOT NULL   UNIQUE', trim($result));
		
	}
	
	public function testPostgresNameColumn() {
		
		$column = 'name';
		
		$result = Database::formatColumn($column, $this->_postgresqlColumns[$column]);
		
		$this ->assertEquals('name CHARACTER VARYING(100) NULL', trim($result));
		
	}
	
	public function testPostgresBioColumn() {
		
		$column = 'bio';
		
		$result = Database::formatColumn($column, $this->_postgresqlColumns[$column]);
		
		$this ->assertEquals('bio CHARACTER VARYING NOT NULL DEFAULT \'\'', trim($result));
		
	}
	
	public function testPostgresCreateTableExecute() {
		
		if(!Database::tableExist($this->_table)) {
			$result = Database::createTable($this->_table, $this->_postgresqlColumns, array('execute'=>true));
		}
		
		$this->assertTrue(Database::tableExist($this->_table));
	}
	
	public function testPostgresColumnExistFalse() {
		
		$column = 'non_existant';
		
		$result = Database::columnExist($this->_table, $column);
		
		$this->assertFalse($result);
	}
	
	public function testPostgresColumnExistTrue() {
		
		$column = 'name';
		
		$result = Database::columnExist($this->_table, $column);
		
		$this->assertTrue($result);
	}
	
	public function testPostgresAddColumnString() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=Database::addColumn($this->_table, $column, $this->_addColumn[$column], array('execute' => false));
		
		
		$this->assertEquals('ALTER TABLE users ADD COLUMN is_active SMALLINT NOT NULL DEFAULT \'0\'  ;', $result);
		$this->assertFalse(Database::columnExist($this->_table, $column));
	}
	
	public function testPostgresAddColumnExecute() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=Database::addColumn($this->_table, $column, $this->_addColumn[$column]);
		
		$this->assertTrue(Database::columnExist($this->_table, $column));
	}
	
	
	public function testPostgresDropColumnString() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=Database::dropColumn($this->_table, $column, array('execute'=> false));
		
		$this->assertEquals('ALTER TABLE users DROP COLUMN is_active;', $result);
		$this->assertTrue(Database::columnExist($this->_table, $column));
	}
	
	public function testPostgresDropColumnExecute() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=Database::dropColumn($this->_table, $column);
		
		$this->assertEquals('ALTER TABLE users DROP COLUMN is_active;', $result);
		$this->assertFalse(Database::columnExist($this->_table, $column));
	}
	
	public function testPostgresInsertJon() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		
		$this->assertTrue(true);
	}
	
	public function testPostgresInsertFindJon() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::selectStatement($query);
		
		$this->assertTrue(is_resource($result));
	}
	
	public function testPostgresSelectJonFetchArray() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		Database::insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::selectStatement($query);
		
		$data = Database::fetchArray($result);
		
		$this->assertEquals($data['email'], $this->_data[0]['email']);
	}
	
	public function testPostgresSelectJonFetchAssoc() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		Database::insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::selectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertEquals($data['email'], $this->_data[0]['email']);
	}
	
	public function testPostgresChangeJonWithBob() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		Database::insertStatement($this->_table, $this->_data[1]);
		
		Database::updateStatement($this->_table, array('name'=>'Bob'), array('email'=>$this->_data[0]['email']));
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::selectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertEquals($data['name'], 'Bob');
	}
	
	public function testPostgresDeleteJon() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		Database::insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		Database::deleteStatement($query);
		
		$result  = Database::selectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertFalse($data);
	}
	
	public function testPostgresPreparedInsertFindJon() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::preparedSelectStatement($query);
		
		
		$this->assertTrue(is_resource($result));
	}
	
	public function testPostgresPreparedInsertFindJonWithId() {
		Database::clearTableData($this->_table);
		$result = Database::preparedReturnLastInsert($this->_table, 'id', $this->_table, $this->_data[0]);
		
		$this->assertEquals($result, 12);
	}
	
	public function testPostgresPreparedSelectJonFetchArray() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		Database::preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::preparedSelectStatement($query);
		
		$data = Database::fetchArray($result);
		
		$this->assertEquals($data['email'], $this->_data[0]['email']);
	}
	
	public function testPostgresPreparedSelectJonFetchAssoc() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		Database::preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::preparedSelectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertEquals($data['email'], $this->_data[0]['email']);
	}
	
	public function testPostgresPreparedChangeJonWithBob() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		Database::preparedInsert($this->_table, $this->_data[1]);
		
		Database::preparedUpdate($this->_table, array('name'=>'Bob'), array('email'=>$this->_data[0]['email']));
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::preparedSelectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertEquals($data['name'], 'Bob');
	}
	
	public function testPostgresPreparedDeleteJon() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		Database::preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		Database::preparedDelete($this->_table, array('email'=>$this->_data[0]['email']));
		
		$result  = Database::preparedSelectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertFalse($data);
	}
	
	public function testPostgresDropTable() {
		Database::dropTable($this->_table);
		$this->assertFalse(Database::tableExist($this->_table));
	}
	
	public function testPostgresLink() {
		$link = Database::getDatabaseLink();
		
		$this->assertTrue(is_resource($link));
	}
	
	public function testMysqlConnectionVariables() {
		
		Database::setDatabase
		('mysql_connection');
		
		//$this->assertEquals($this->_login, Database::getLogin());
		//$this->assertEquals($this->_host, Database::getHost());	
		//$this->assertEquals($this->_port, Database::getPort());	
		//$this->assertEquals($this->_database, Database::getDatabase());		
		$this->assertEquals('mysql_connection', Database::getConnectionName());	
		$this->assertEquals('mysql', Database::getDatabaseType());	
		
	}
	
	public function testMysqlTableExistFalse() {
		
		$result = Database::tableExist('not_found_table');
		$this->assertFalse($result);
	}
	
	public function testMysqlCreateTableString() {
		
		$table_name = 'test_table';
		
		$result = Database::createTable($table_name, array(), array('execute'=>false));
		
		$this->assertEquals('CREATE TABLE ' . $table_name . ' ;', $result);
	}
	
	public function testMysqlColumnMapInteger() {
		
		$result = Database::columnTypeMap('int');
		
		$this->assertEquals('INT', $result);
		
	}
	
	public function testMysqlColumnMapBigInt() {
		
		$result = Database::columnTypeMap('bigint');
		
		$this->assertEquals('BIGINT', $result);
		
	}
	
	public function testMysqlColumnMapDouble() {
		
		$result = Database::columnTypeMap('double');
		
		$this->assertEquals('DOUBLE', $result);
		
	}
	
	public function testMysqlColumnMapString() {
		
		$result = Database::columnTypeMap('string');
		
		$this->assertEquals('VARCHAR', $result);
		
	}

	public function testMysqlMysqlColumnMapText() {
		
		$result = Database::columnTypeMap('text');
		
		$this->assertEquals('TEXT', $result);
		
	}
	
	public function testMysqlColumnMapBlob() {
		
		$result = Database::columnTypeMap('blob');
		
		$this->assertEquals('BLOB', $result);
		
	}
	
	public function testMysqlColumnMapBoolean() {
		
		$result = Database::columnTypeMap('boolean');
		
		$this->assertEquals('BOOLEAN', $result);
		
	}
	
	public function testMysqlColumnMapTinyInt() {
		
		$result = Database::columnTypeMap('tinyint');
		
		$this->assertEquals('TINYINT', $result);
		
	}
	
	public function testMysqlColumnMapTimestamp() {
		
		$result = Database::columnTypeMap('timestamp');
		
		$this->assertEquals('TIMESTAMP', $result);
		
	}
	
	public function testMysqlColumnMapDate() {
		
		$result = Database::columnTypeMap('datetime');
		
		$this->assertEquals('TIMESTAMP', $result);
		
	}
	
	public function testMysqlColumnSerial() {
		
		$result = Database::columnTypeMap('serial');
		
		$this->assertEquals('SERIAL', $result);
		
	}
	
	
	public function testMysqlColumnBigSerial() {
		
		$result = Database::columnTypeMap('bigserial');
		
		$this->assertEquals('SERIAL', $result);
		
	}
	
	public function testMysqlColumnHstore() {
		
		$result = Database::columnTypeMap('hstore');
		
		$this->assertEquals('unknown', $result);
		
	}
	
	public function testMysqlColumnUUID() {
		
		$result = Database::columnTypeMap('uuid');
		
		$this->assertEquals('VARCHAR', $result);
		
	}
	
	public function testMysqlColumnIP() {
		
		$result = Database::columnTypeMap('ip');
		
		$this->assertEquals('VARCHAR', $result);
		
	}
	
	public function testMysqlColumnInet() {
		
		$result = Database::columnTypeMap('inet');
		
		$this->assertEquals('VARCHAR', $result);
		
	}
	
	public function testMysqlColumnJson() {
		
		$result = Database::columnTypeMap('json');
		
		$this->assertEquals('JSON', $result);
		
	}
	
	public function testMysqlIDColumn() {
		
		$column = 'id';
		
		$result = Database::formatColumn($column, $this->_mysqlColumns[$column]);
		
		$this ->assertEquals('id INT NOT NULL  AUTO_INCREMENT', trim($result));
		
	}
	
	public function testMysqlEmailColumn() {
		
		$column = 'email';
		
		$result = Database::formatColumn($column, $this->_mysqlColumns[$column]);
		
		$this ->assertEquals('email VARCHAR(255) NOT NULL   UNIQUE', trim($result));
		
	}
	
	public function testMysqlNameColumn() {
		
		$column = 'name';
		
		$result = Database::formatColumn($column, $this->_mysqlColumns[$column]);
		
		$this ->assertEquals('name VARCHAR(100) NULL', trim($result));
		
	}
	
	public function testMysqlBioColumn() {
		
		$column = 'bio';
		
		$result = Database::formatColumn($column, $this->_mysqlColumns[$column]);
		
		$this ->assertEquals('bio TEXT NOT NULL', trim($result));
		
	}
	
	public function testMysqlCreateTableExecute() {
		
		if(!Database::tableExist($this->_table)) {
			$result = Database::createTable($this->_table, $this->_mysqlColumns, array('execute'=>true, 'primary_key' => 'id'));
		}
		
		$this->assertTrue(Database::tableExist($this->_table));
	}
	
	public function testMysqlColumnExistFalse() {
		
		$column = 'non_existant';
		
		$result = Database::columnExist($this->_table, $column);
		
		$this->assertFalse($result);
	}
	
	public function testMysqlColumnExistTrue() {
		
		$column = 'name';
		
		$result = Database::columnExist($this->_table, $column);
		
		$this->assertTrue($result);
	}
	
	public function testMysqlAddColumnString() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=Database::addColumn($this->_table, $column, $this->_addColumn[$column], array('execute' => false));
		
		
		$this->assertEquals('ALTER TABLE users ADD is_active TINYINT NOT NULL DEFAULT \'0\'  ;', $result);
		$this->assertFalse(Database::columnExist($this->_table, $column));
	}
	
	public function testMysqlAddColumnExecute() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=Database::addColumn($this->_table, $column, $this->_addColumn[$column]);
		
		$this->assertTrue(Database::columnExist($this->_table, $column));
	}
	
	
	public function testMysqlDropColumnString() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=Database::dropColumn($this->_table, $column, array('execute'=> false));
		
		$this->assertEquals('ALTER TABLE users DROP COLUMN is_active;', $result);
		$this->assertTrue(Database::columnExist($this->_table, $column));
	}
	
	public function testMysqlDropColumnExecute() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=Database::dropColumn($this->_table, $column);
		
		$this->assertEquals('ALTER TABLE users DROP COLUMN is_active;', $result);
		$this->assertFalse(Database::columnExist($this->_table, $column));
	}
	
	public function testMysqlInsertJon() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		
		$this->assertTrue(true);
	}
	
	public function testMysqlInsertFindJon() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::selectStatement($query);
		
		$this->assertTrue($result instanceof \mysqli_result);
	}
	
	public function testSelectJonFetchArray() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		Database::insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::selectStatement($query);
		
		
		$data = Database::fetchArray($result);
		
		$this->assertEquals($data['email'], $this->_data[0]['email']);
	}
	
	public function testMysqlSelectJonFetchAssoc() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		Database::insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result = Database::selectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertEquals($data[0]['email'], $this->_data[0]['email']);
	}
	
	public function testMysqlChangeJonWithBob() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		Database::insertStatement($this->_table, $this->_data[1]);
		
		Database::updateStatement($this->_table, array('name'=>'Bob'), array('email'=>$this->_data[0]['email']));
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::selectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertEquals($data[0]['name'], 'Bob');
	}
	
	public function testMysqlDeleteJon() {
		Database::clearTableData($this->_table);
		Database::insertStatement($this->_table, $this->_data[0]);
		Database::insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		Database::deleteStatement($query);
		
		$result  = Database::selectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertTrue(empty($data));
	}
	
	public function testMysqlPreparedInsertFindJon() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::preparedSelectStatement($query);
		
		$this->assertTrue($result instanceof \mysqli_result);
	}
	
	public function testPreparedInsertFindJonWithId() {
		Database::clearTableData($this->_table);
		$result = Database::preparedReturnLastInsert($this->_table, 'id', $this->_table, $this->_data[0]);
		
		$this->assertEquals($result, 1);
	}
	
	public function testMysqlPreparedSelectJonFetchArray() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		Database::preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::preparedSelectStatement($query);
		
		$data = Database::fetchArray($result);
		
		$this->assertEquals($data['email'], $this->_data[0]['email']);
	}
	
	public function testMysqlPreparedSelectJonFetchAssoc() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		Database::preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = Database::preparedSelectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertEquals($data[0]['email'], $this->_data[0]['email']);
	}
	
	public function testMysqlPreparedChangeJonWithBob() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		Database::preparedInsert($this->_table, $this->_data[1]);
		
		Database::preparedUpdate($this->_table, array('name'=>'Bob'), array('email'=>$this->_data[0]['email']));
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result = Database::preparedSelectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertEquals($data[0]['name'], 'Bob');
	}
	
	public function testMysqlPreparedDeleteJon() {
		Database::clearTableData($this->_table);
		Database::preparedInsert($this->_table, $this->_data[0]);
		Database::preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		Database::preparedDelete($this->_table, array('email'=>$this->_data[0]['email']));
		
		$result  = Database::preparedSelectStatement($query);
		
		$data = Database::fetchFields($result);
		
		$this->assertTrue(empty($data));
	}
	
	public function testMysqlDropTable() {
		Database::dropTable($this->_table);
		$this->assertFalse(Database::tableExist($this->_table));
	}
	
	public function testMysqlLink() {
		$link = Database::getDatabaseLink();
		
		
		$this->assertTrue($link instanceof \mysqli);
	}
	
	
}
