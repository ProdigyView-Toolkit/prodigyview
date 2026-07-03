<?php

use prodigyview\database\Database;
use prodigyview\database\SQLite;
use PHPUnit\Framework\TestCase;

class SQLiteTest extends TestCase {
	
	private $_db = null;
	
	private $_table = 'pv_sqlite_users';
	
	private $_database = ':memory:';
	
	private $_columns = array(
		'id'=> array('type' => 'int', 'auto_increment' => true, 'primary_key'=> true),
		'email'=> array('type' => 'string', 'precision'=>255, 'unique'=> true),
		'name' => array('type' => 'string', 'precision'=> 100, 'not_null' => false),
		'bio' => array('type' => 'text'),
		'login_count' => array('type' => 'int', 'default' => 0),
		'rating' => array('type' => 'double', 'default' => 0),
		'deleted_at' => array('type' => 'string', 'not_null' => false),
	);
	
	private $_data = array(
		array('email' => 'jon.sqlite@example.com', 'name'=> 'Jon SQLite', 'bio' => 'SQLite row one'),
		array('email' => 'jane.sqlite@example.com', 'name'=> 'Jane SQLite', 'bio' => 'SQLite row two')
	);
	
	protected function setUp(): void {
		$this->_db = new SQLite();
		$this->_db->setConnection('testsqlite', array(
			'database' => $this->_database
		));
		$this->_db->connect();
		$this->_db->createTable($this->_table, $this->_columns, array('primary_key' => 'id'));
	}
	
	protected function tearDown(): void {
		$this->_db->closeDB();
		Database::init(array(), false);
	}
	
	public function testConnectionVariables() {
		$this->assertEquals('testsqlite', $this->_db->getConnectionName());
		$this->assertEquals('sqlite', $this->_db->getDatabaseType());
		$this->assertEquals($this->_database, $this->_db->getDatabase());
		$this->assertTrue($this->_db->isActive());
	}
	
	public function testTableAndColumnExist() {
		$this->assertTrue($this->_db->tableExist($this->_table));
		$this->assertTrue($this->_db->columnExist($this->_table, 'email'));
		$this->assertFalse($this->_db->columnExist($this->_table, 'not_found'));
	}
	
	public function testInsertSelectUpdateAndDelete() {
		$this->_db->insertStatement($this->_table, $this->_data[0]);
		$this->_db->insertStatement($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=> array('email' => $this->_data[0]['email'])
		);
		
		$result = $this->_db->selectStatement($query);
		$data = $this->_db->fetchArray($result);
		$this->assertEquals($this->_data[0]['name'], $data['name']);
		
		$this->_db->updateStatement($this->_table, array('name' => 'Updated SQLite'), array('email' => $this->_data[0]['email']));
		$result = $this->_db->selectStatement($query);
		$data = $this->_db->fetchArray($result);
		$this->assertEquals('Updated SQLite', $data['name']);
		
		$this->_db->deleteStatement($query);
		$result = $this->_db->selectStatement($query);
		$this->assertFalse($this->_db->fetchArray($result));
	}
	
	public function testPreparedInsertSelectUpdateAndDelete() {
		$this->_db->preparedInsert($this->_table, $this->_data[0]);
		$this->_db->preparedInsert($this->_table, $this->_data[1]);
		
		$query = array(
			'table' => $this->_table,
			'where'=> array('email' => $this->_data[0]['email'])
		);
		
		$result = $this->_db->preparedSelectStatement($query);
		$data = $this->_db->fetchArray($result);
		$this->assertEquals($this->_data[0]['email'], $data['email']);
		
		$this->_db->preparedUpdate($this->_table, array('name' => 'Prepared SQLite'), array('email' => $this->_data[0]['email']));
		$result = $this->_db->preparedSelectStatement($query);
		$data = $this->_db->fetchArray($result);
		$this->assertEquals('Prepared SQLite', $data['name']);
		
		$this->_db->preparedDelete($this->_table, array('email' => $this->_data[0]['email']));
		$result = $this->_db->preparedSelectStatement($query);
		$this->assertFalse($this->_db->fetchArray($result));
	}

	public function testPreparedInsertPreservesNullIntegerAndFloatValues() {
		$this->_db->preparedInsert($this->_table, array(
			'email' => 'typed.sqlite@example.com',
			'name' => null,
			'bio' => 'Typed values',
			'login_count' => 7,
			'rating' => 4.5,
			'deleted_at' => null,
		));

		$result = $this->_db->preparedSelectStatement(array(
			'table' => $this->_table,
			'where'=> array('email' => 'typed.sqlite@example.com')
		));
		$data = $this->_db->fetchArray($result);

		$this->assertNull($data['name']);
		$this->assertSame(7, (int) $data['login_count']);
		$this->assertEqualsWithDelta(4.5, (float) $data['rating'], 0.0001);
		$this->assertNull($data['deleted_at']);
	}

	public function testSelectStatementSupportsIsNullOperator() {
		$this->_db->preparedInsert($this->_table, array(
			'email' => 'null.sqlite@example.com',
			'name' => null,
			'bio' => 'Null operator',
			'deleted_at' => null,
		));

		$result = $this->_db->selectStatement(array(
			'table' => $this->_table,
			'where'=> array('deleted_at' => array('IS NULL'))
		));
		$data = $this->_db->fetchArray($result);

		$this->assertEquals('null.sqlite@example.com', $data['email']);
	}

	public function testInlinePrimaryKeyDoesNotDuplicateTablePrimaryKey() {
		$query = $this->_db->createTable('pv_sqlite_edge_schema', $this->_columns, array(
			'primary_key' => 'id',
			'execute' => false
		));

		$this->assertStringContainsString('id INTEGER PRIMARY KEY AUTOINCREMENT', $query);
		$this->assertStringNotContainsString(',PRIMARY KEY(id)', $query);
	}
	
	public function testDatabaseFacadeCanUseSQLiteConnection() {
		Database::init(array(), false);
			Database::addConnection('sqlite_connection', array(
				'type' => 'sqlite',
				'database' => ':memory:'
			));
			Database::setDatabase('sqlite_connection');
			
			$this->assertEquals('sqlite', Database::getDatabaseType());
		$this->assertEquals('sqlite_connection', Database::getConnectionName());
	}
}
