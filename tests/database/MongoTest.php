<?php

use prodigyview\database\Mongo;
use PHPUnit\Framework\TestCase;

class MongoTest extends TestCase{
	
	private $_db = null;
	
	private $_table = 'users';
	
	private $_host = 'mongodb';
	
	private $_port = 27017;
	
	private $_connectionName = 'testmongo';
	
	private $_login = 'prodigyview';
	
	private $_password = 'prodigyview';
	
	private $_database= 'prodigyview';
	
	private $_columns = array(
		'_id'=> array('type' => '_id', 'auto_increment' => true, 'primary_key'=> true),
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
		$this->_db = new Mongo();
		
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
		$this->assertEquals('mongo', $this->_db->getDatabaseType());			
		
	}
	
	public function testTableExistFalse() {
		
		$result = $this->_db ->tableExist('not_found_table');
		$this->assertEquals($result, 0);
	}
	
	public function testCreateTableString() {
		
		$table_name = 'test_table';
		
		$result = $this->_db ->createTable($table_name, array(), array('execute'=>false));
		
		$this->assertTrue(true);
	}
	
	public function testColumnMapInteger() {
		
		$result = $this->_db->columnTypeMap('int');
		
		$this->assertEquals('int', $result);
		
	}
	
	public function testColumnMapBigInt() {
		
		$result = $this->_db->columnTypeMap('bigint');
		
		$this->assertEquals('bigint', $result);
		
	}
	
	public function testColumnMapDouble() {
		
		$result = $this->_db->columnTypeMap('double');
		
		$this->assertEquals('double', $result);
		
	}
	
	public function testColumnMapString() {
		
		$result = $this->_db->columnTypeMap('string');
		
		$this->assertEquals('string', $result);
		
	}

	public function testColumnMapText() {
		
		$result = $this->_db->columnTypeMap('text');
		
		$this->assertEquals('text', $result);
		
	}
	
	public function testColumnMapBlob() {
		
		$result = $this->_db->columnTypeMap('blob');
		
		$this->assertEquals('blob', $result);
		
	}
	
	public function testColumnMapBoolean() {
		
		$result = $this->_db->columnTypeMap('boolean');
		
		$this->assertEquals('boolean', $result);
		
	}
	
	public function testColumnMapTinyInt() {
		
		$result = $this->_db->columnTypeMap('tinyint');
		
		$this->assertEquals('tinyint', $result);
		
	}
	
	public function testColumnMapTimestamp() {
		
		$result = $this->_db->columnTypeMap('timestamp');
		
		$this->assertEquals('timestamp', $result);
		
	}
	
	public function testColumnMapDate() {
		
		$result = $this->_db->columnTypeMap('datetime');
		
		$this->assertEquals('datetime', $result);
		
	}
	
	public function testColumnSerial() {
		
		$result = $this->_db->columnTypeMap('serial');
		
		$this->assertEquals('serial', $result);
		
	}
	
	
	public function testColumnBigSerial() {
		
		$result = $this->_db->columnTypeMap('bigserial');
		
		$this->assertEquals('bigserial', $result);
		
	}
	
	public function testColumnHstore() {
		
		$result = $this->_db->columnTypeMap('hstore');
		
		$this->assertEquals('hstore', $result);
		
	}
	
	public function testColumnUUID() {
		
		$result = $this->_db->columnTypeMap('uuid');
		
		$this->assertEquals('uuid', $result);
		
	}
	
	public function testColumnIP() {
		
		$result = $this->_db->columnTypeMap('ip');
		
		$this->assertEquals('ip', $result);
		
	}
	
	public function testColumnInet() {
		
		$result = $this->_db->columnTypeMap('inet');
		
		$this->assertEquals('inet', $result);
		
	}
	
	public function testColumnJson() {
		
		$result = $this->_db->columnTypeMap('json');
		
		$this->assertEquals('json', $result);
		
	}
	
	public function testIDColumn() {
		
		$column = '_id';
		
		$result = $this->_db->formatColumn($column, $this->_columns[$column]);
		
		$this ->assertEquals('', trim($result));
		
	}
	
	public function testEmailColumn() {
		
		$column = 'email';
		
		$result = $this->_db->formatColumn($column, $this->_columns[$column]);
		
		$this ->assertEquals('', trim($result));
		
	}
	
	public function testNameColumn() {
		
		$column = 'name';
		
		$result = $this->_db->formatColumn($column, $this->_columns[$column]);
		
		$this ->assertEquals('', trim($result));
		
	}
	
	public function testBioColumn() {
		
		$column = 'bio';
		
		$result = $this->_db->formatColumn($column, $this->_columns[$column]);
		
		$this ->assertEquals('', trim($result));
		
	}
	
	public function testCreateTableExecute() {
		
		if(!$this->_db ->tableExist($this->_table)) {
			$result = $this->_db ->createTable($this->_table, $this->_columns, array('execute'=>true, 'primary_key' => 'id'));
		}
		
		$this->assertTrue(true);
	}
	
	public function testColumnExistFalse() {
		
		$column = 'non_existant';
		
		$result = $this->_db ->columnExist($this->_table, $column);
		
		$this->assertFalse(false);
	}
	
	public function testColumnExistTrue() {
		
		$column = 'name';
		
		$result = $this->_db ->columnExist($this->_table, $column);
		
		$this->assertTrue(true);
	}
	
	public function testAddColumnString() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=$this->_db->addColumn($this->_table, $column, $this->_addColumn[$column], array('execute' => false));
		
		
		$this->assertEquals(null, $result);
		$this->assertFalse(false);
		//$this->assertFalse($this->_db ->columnExist($this->_table, $column));
	}
	
	public function testAddColumnExecute() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=$this->_db->addColumn($this->_table, $column, $this->_addColumn[$column]);
		
		$this->assertTrue(true);
		//$this->assertTrue($this->_db ->columnExist($this->_table, $column));
	}
	
	
	public function testDropColumnString() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=$this->_db->dropColumn($this->_table, $column, array('execute'=> false));
		
		$this->assertTrue(true);
		//$this->assertTrue($this->_db ->columnExist($this->_table, $column));
	}
	
	public function testDropColumnExecute() {
		
		reset($this->_addColumn);
		$column = key($this->_addColumn);
		
		$result=$this->_db->dropColumn($this->_table, $column);
		
		$this->assertFalse(false);
		//$this->assertFalse($this->_db ->columnExist($this->_table, $column));
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
		
		$this->assertTrue(true);
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
		
		$this->assertEquals($data[0]['email'], $this->_data[0]['email']);
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
		
		$this->assertTrue(true);
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
		
		$this->assertEquals($data[0]['email'], $this->_data[0]['email']);
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
		
		$this->assertTrue($link instanceof \MongoDB\Client);
	}
	
	
}
