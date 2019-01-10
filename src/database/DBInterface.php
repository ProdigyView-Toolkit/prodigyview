<?php

namespace prodigyview\database;

interface DBInterface {
	
	public function getDatabaseType();
	
	public function getConnectionName();
	
	public function getHost();
	
	public function getPort();
	
	public function getLogin();
	
	public function getDatabase();
	
	public function getSchema($append_period = true);
	
	public function setConnection(string $name, array $options = array());
	
	public function connect();
	
	public function isActive();
	
	public function query($query);
	
	public function returnLastInsert($query, $returnField = '', $returnTable = '');
	
	public function resultRowCount($result);
	
	public function fetchArray($result);
	
	public function fetchFields($result);
	
	public function makeSafe($string);
	
	public function closeDB();
	
	public function clearTableData($tablename, $options = '');
	
	public function tableExist($tablename, $schema = '');
	
	public function columnExist($table_name, $field_name);
	
	public function getSQLRandomOperator();
	
	public function formatData($string);
	
	public function dbAverageFunction($field);
	
	public function getPagininationOffset($table, $join_clause = '', $where_clause = '', $current_page = 0, $results_per_page = 20, $order_by = '', $fields = 'COUNT(*) as count');
	
	public function getDatabaseLink();
	
	public function insertStatement($table_name, $data, $options = array());
	
	public function updateStatement($table, $data, $wherelist, $options = array());
	
	public function selectStatement(array $args, array $options = array());
	
	public function deleteStatement(array $args, array $options = array());
	
	public function preparedQuery($query, $data, $formats = '');
	
	public function preparedInsert($table_name, $data, $formats = array());
	
	public function preparedReturnLastInsert($table_name, $returnField, $returnTable, $data, $formats = array(), $options = array() );
	
	public function preparedSelect($query, $data, array $formats = array(), array $options = array());
	
	public function preparedSelectStatement(array $args, array $options = array());
	
	public function preparedUpdate($table, $data, $wherelist, $formats = array(), $whereformats = array(), $options = array());
	
	public function preparedDelete($table, $wherelist = array(), $whereformats = array(), $options = array()) ;
	
	public function getPreparedPlaceHolder($count = 1);
	
	public function formatTableName($table_name, $append_schema = true, $append_prefix = true);
	
	public function parseOperators($column, $args = array(), $key = 'AND', $operator = '=', $first = true);
	
	public function createTable($table_name, $columns = array(), $options = array());
	
	public function addColumn($table_name, $column_name, $column_data = array(), $options = array());
	
	public function formatColumn($name, $options = array());
	
	public function getAutoIncrement();
	
	public function columnTypeMap($type);
	
	public function dropColumn($table_name, $column_name, $options = array());
	
	public function dropTable($table_name, $options = array());
	
	public function catchDBError();
	
	
}
