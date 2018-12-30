<?php
use prodigyview\system\Database;

use PHPUnit\Framework\TestCase;

class MysqlTests extends TestCase {
	
	protected function setUp() {
		Database::init();
		
		Database::addConnection('mysql', array(
			'dbhost' => 'mysql', 
			'dbname' => 'prodigyview', 
			'dbuser' => 'prodigyview', 
			'dbpass' => 'prodigyview', 
			'dbtype' => 'mysql', 
			'dbschema' => '', 
			'dbprefix' => '', 
			'dbport' => '3306'
		));
		
		Database::setDatabase('mysql');
	}
	
	public function testCreateTable() {
		
		
		
	}
	
}
	