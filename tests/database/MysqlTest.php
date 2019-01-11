<?php

use prodigyview\database\Mysql;
use PHPUnit\Framework\TestCase;

class MysqlTest extends TestCase{
	
	private $_db = null;
	
	private $_table = 'users';
	
	private $_host = 'mysql';
	
	private $_port = 3306;
	
	private $_connectionName = 'testmysql';
	
	private $_login = 'prodigyview';
	
	private $_password = 'prodigyview';
	
	private $_database= 'prodigyview';
	
	private $_columns = array(
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
		$this->_db = new Mysql();
		
		$this->_db->setConnection($this->_connectionName, array(
			'host'=> $this->_host,
			'database' => $this->_database,
			'login'=>$this->_login,
			'password'=>$this->_password,
			'port'=>$this->_port
		));
		
		$this->_db->connect();
	}
	
	public function testConnectionVariables() {
		
		$this->assertEquals($this->_login, $this->_db->getLogin());
		$this->assertEquals($this->_host, $this->_db->getHost());	
		$this->assertEquals($this->_port, $this->_db->getPort());	
		$this->assertEquals($this->_database, $this->_db->getDatabase());		
		$this->assertEquals($this->_connectionName, $this->_db->getConnectionName());	
		$this->assertEquals('mysql', $this->_db->getDatabaseType());	
		
	}
	
	public function testTableExistFalse() {
		
		$result = $this->_db ->tableExist('not_found_table');
		$this->assertFalse($result);
	}
	
	public function testCreateTableString() {
		
		$table_name = 'test_table';
		
		$result = $this->_db ->createTable($table_name, array(), array('execute'=>false));
		
		$this->assertEquals('CREATE TABLE ' . $table_name . ' ;', $result);
	}
	
	public function testColumnMapInteger() {
		
		$result = $this->_db->columnTypeMap('int');
		
		$this->assertEquals('INT', $result);
		
	}
	
	public function testColumnMapBigInt() {
		
		$result = $this->_db->columnTypeMap('bigint');
		
		$this->assertEquals('BIGINT', $result);
		
	}
	
	public function testColumnMapDouble() {
		
		$result = $this->_db->columnTypeMap('double');
		
		$this->assertEquals('DOUBLE', $result);
		
	}
	
	public function testColumnMapString() {
		
		$result = $this->_db->columnTypeMap('string');
		
		$this->assertEquals('VARCHAR', $result);
		
	}

	public function testColumnMapText() {
		
		$result = $this->_db->columnTypeMap('text');
		
		$this->assertEquals('TEXT', $result);
		
	}
	
	public function testColumnMapBlob() {
		
		$result = $this->_db->columnTypeMap('blob');
		
		$this->assertEquals('BLOB', $result);
		
	}
	
	public function testColumnMapBoolean() {
		
		$result = $this->_db->columnTypeMap('boolean');
		
		$this->assertEquals('BOOLEAN', $result);
		
	}
	
	public function testColumnMapTinyInt() {
		
		$result = $this->_db->columnTypeMap('tinyint');
		
		$this->assertEquals('TINYINT', $result);
		
	}
	
	public function testColumnMapTimestamp() {
		
		$result = $this->_db->columnTypeMap('timestamp');
		
		$this->assertEquals('TIMESTAMP', $result);
		
	}
	
	public function testColumnMapDate() {
		
		$result = $this->_db->columnTypeMap('datetime');
		
		$this->assertEquals('TIMESTAMP', $result);
		
	}
	
	public function testColumnSerial() {
		
		$result = $this->_db->columnTypeMap('serial');
		
		$this->assertEquals('SERIAL', $result);
		
	}
	
	
	public function testColumnBigSerial() {
		
		$result = $this->_db->columnTypeMap('bigserial');
		
		$this->assertEquals('SERIAL', $result);
		
	}
	
	public function testColumnHstore() {
		
		$result = $this->_db->columnTypeMap('hstore');
		
		$this->assertEquals('unknown', $result);
		
	}
	
	public function testColumnUUID() {
		
		$result = $this->_db->columnTypeMap('uuid');
		
		$this->assertEquals('VARCHAR', $result);
		
	}
	
	public function testColumnIP() {
		
		$result = $this->_db->columnTypeMap('ip');
		
		$this->assertEquals('VARCHAR', $result);
		
	}
	
	public function testColumnInet() {
		
		$result = $this->_db->columnTypeMap('inet');
		
		$this->assertEquals('VARCHAR', $result);
		
	}
	
	public function testColumnJson() {
		
		$result = $this->_db->columnTypeMap('json');
		
		$this->assertEquals('JSON', $result);
		
	}
	
	public function testIDColumn() {
		
		$column = 'id';
		
		$result = $this->_db->formatColumn($column, $this->_columns[$column]);
		
		$this ->assertEquals('id INT NOT NULL  AUTO_INCREMENT', trim($result));
		
	}
	
	public function testEmailColumn() {
		
		$column = 'email';
		
		$result = $this->_db->formatColumn($column, $this->_columns[$column]);
		
		$this ->assertEquals('email VARCHAR(255) NOT NULL   UNIQUE', trim($result));
		
	}
	
	public function testNameColumn() {
		
		$column = 'name';
		
		$result = $this->_db->formatColumn($column, $this->_columns[$column]);
		
		$this ->assertEquals('name VARCHAR(100) NULL', trim($result));
		
	}
	
	public function testBioColumn() {
		
		$column = 'bio';
		
		$result = $this->_db->formatColumn($column, $this->_columns[$column]);
		
		$this ->assertEquals('bio TEXT NOT NULL', trim($result));
		
	}
	
	public function testCreateTableExecute() {
		
		if(!$this->_db ->tableExist($this->_table)) {
			$result = $this->_db ->createTable($this->_table, $this->_columns, array('execute'=>true, 'primary_key' => 'id'));
		}
		
		$this->assertTrue($this->_db ->tableExist($this->_table));
	}
	
	public function testColumnExistFalse() {
		
		$column = 'non_existant';
		
		$result = $this->_db ->columnExist($this->_table, $column);
		
		$this->assertFalse($result);
	}
	
	public function testColumnExistTrue() {
		
		$column = 'name';
		
		$result = $this->_db ->columnExist($this->_table, $column);
		
		$this->assertTrue($result);
	}
	
	public function testAddColumnString() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=$this->_db->addColumn($this->_table, $column, $this->_addColumn[$column], array('execute' => false));
		
		
		$this->assertEquals('ALTER TABLE users ADD is_active TINYINT NOT NULL DEFAULT \'0\'  ;', $result);
		$this->assertFalse($this->_db ->columnExist($this->_table, $column));
	}
	
	public function testAddColumnExecute() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=$this->_db->addColumn($this->_table, $column, $this->_addColumn[$column]);
		
		$this->assertTrue($this->_db ->columnExist($this->_table, $column));
	}
	
	
	public function testDropColumnString() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=$this->_db->dropColumn($this->_table, $column, array('execute'=> false));
		
		$this->assertEquals('ALTER TABLE users DROP COLUMN is_active;', $result);
		$this->assertTrue($this->_db ->columnExist($this->_table, $column));
	}
	
	public function testDropColumnExecute() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=$this->_db->dropColumn($this->_table, $column);
		
		$this->assertEquals('ALTER TABLE users DROP COLUMN is_active;', $result);
		$this->assertFalse($this->_db ->columnExist($this->_table, $column));
	}
	
	public function testInsertJon() {
		$this->_db->clearTableData($this->_table);
		$this->_db->insertStatement($this->_table, $this->_data[0]);
		
		$this->assertTrue(true);
	}
	
	public function testInsertFindJon() {
		$this->_db->clearTableData($this->_table);
		$this->_db->insertStatement($this->_table, $this->_data[0]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = $this->_db->selectStatement($query);
		
		$this->assertTrue($result instanceof \mysqli_result);
	}
	
	public function testSelectJonFetchArray() {
		$this->_db->clearTableData($this->_table);
		$this->_db->insertStatement($this->_table, $this->_data[0]);
		$this->_db->insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = $this->_db->selectStatement($query);
		
		
		$data = $this->_db->fetchArray($result);
		
		$this->assertEquals($data['email'], $this->_data[0]['email']);
	}
	
	public function testSelectJonFetchAssoc() {
		$this->_db->clearTableData($this->_table);
		$this->_db->insertStatement($this->_table, $this->_data[0]);
		$this->_db->insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = $this->_db->selectStatement($query);
		
		$data = $this->_db->fetchFields($result);
		
		$this->assertEquals($data[0]['email'], $this->_data[0]['email']);
	}
	
	public function testChangeJonWithBob() {
		$this->_db->clearTableData($this->_table);
		$this->_db->insertStatement($this->_table, $this->_data[0]);
		$this->_db->insertStatement($this->_table, $this->_data[1]);
		
		$this ->_db->updateStatement($this->_table, array('name'=>'Bob'), array('email'=>$this->_data[0]['email']));
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = $this->_db->selectStatement($query);
		
		$data = $this->_db->fetchFields($result);
		
		$this->assertEquals($data[0]['name'], 'Bob');
	}
	
	public function testDeleteJon() {
		$this->_db->clearTableData($this->_table);
		$this->_db->insertStatement($this->_table, $this->_data[0]);
		$this->_db->insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$this ->_db->deleteStatement($query);
		
		$result  = $this->_db->selectStatement($query);
		
		$data = $this->_db->fetchFields($result);
		
		$this->assertTrue(empty($data));
	}
	
	public function testPreparedInsertFindJon() {
		$this->_db->clearTableData($this->_table);
		$this->_db->preparedInsert($this->_table, $this->_data[0]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = $this->_db->preparedSelectStatement($query);
		
		$this->assertTrue($result instanceof \mysqli_result);
	}
	
	public function testPreparedInsertFindJonWithId() {
		$this->_db->clearTableData($this->_table);
		$result = $this->_db->preparedReturnLastInsert($this->_table, 'id', $this->_table, $this->_data[0]);
		
		$this->assertEquals($result, 1);
	}
	
	public function testPreparedSelectJonFetchArray() {
		$this->_db->clearTableData($this->_table);
		$this->_db->preparedInsert($this->_table, $this->_data[0]);
		$this->_db->preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = $this->_db->preparedSelectStatement($query);
		
		$data = $this->_db->fetchArray($result);
		
		$this->assertEquals($data['email'], $this->_data[0]['email']);
	}
	
	public function testPreparedSelectJonFetchAssoc() {
		$this->_db->clearTableData($this->_table);
		$this->_db->preparedInsert($this->_table, $this->_data[0]);
		$this->_db->preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = $this->_db->preparedSelectStatement($query);
		
		$data = $this->_db->fetchFields($result);
		
		$this->assertEquals($data[0]['email'], $this->_data[0]['email']);
	}
	
	public function testPreparedChangeJonWithBob() {
		$this->_db->clearTableData($this->_table);
		$this->_db->preparedInsert($this->_table, $this->_data[0]);
		$this->_db->preparedInsert($this->_table, $this->_data[1]);
		
		$this ->_db->preparedUpdate($this->_table, array('name'=>'Bob'), array('email'=>$this->_data[0]['email']));
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$result  = $this->_db->preparedSelectStatement($query);
		
		$data = $this->_db->fetchFields($result);
		
		$this->assertEquals($data[0]['name'], 'Bob');
	}
	
	public function testPreparedDeleteJon() {
		$this->_db->clearTableData($this->_table);
		$this->_db->preparedInsert($this->_table, $this->_data[0]);
		$this->_db->preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=>array('email'=>$this->_data[0]['email'])
		);
		
		$this ->_db->preparedDelete($this->_table, array('email'=>$this->_data[0]['email']));
		
		$result  = $this->_db->preparedSelectStatement($query);
		
		$data = $this->_db->fetchFields($result);
		
		$this->assertTrue(empty($data));
	}
	
	public function testDropTable() {
		$this->_db->dropTable($this->_table);
		$this->assertFalse($this->_db ->tableExist($this->_table));
	}
	
	public function testLink() {
		$link = $this->_db->getDatabaseLink();
		
		
		$this->assertTrue($link instanceof \mysqli);
	}
	
	
}
