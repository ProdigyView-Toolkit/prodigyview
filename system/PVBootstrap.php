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
*THIS SOFTWARE IS PROVIDED BY My-Lan AS IS'' AND ANY EXPRESS OR IMPLIED
*WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
*FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL My-Lan OR
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

class PVBootstrap extends PVStaticObject{
	
	/**
	 * Booth the ProdigyView system. Initilize variables, set logging,
	 * sessions, etc. Many of the configuration settings are located in the xml
	 * config file.
	 * 
	 * @param array $args Arguments to pass that affect how ProdigyView will boot
	 * 			-'initialize_database' _boolean_: Initialize the database and set the database to the default config
	 * 			-'initialize_libraries' _boolean_: Initializes the libraries
	 * 			-'initialize_router' _boolean_: Initializes the router
	 * 			-'initialize_template' _boolean_:Initializes the template
	 * 			-'initalize_validator' _boolean_: Initializes the validator
	 * 			-'load_plugins' _booleans_: Loads the plug-ins at boot.
	 *			-'load_libraries' _booleans_: Loads the libraries that have been added
	 * 
	 * @return void
	 * @access public
	 * @todo Add ability to initialize other classes
	 */
	public static function bootSystem($args=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$defaults=array(
			'initialize_database'=>true,
			'initialize_libraries'=>true,
			'initialize_router'=>true,
			'initialize_template'=>true,
			'initialize_validator'=>true,
			'initialize_security'=>true,
			'initialize_session'=>true,
			'load_plugins'=>true,
			'load_libraries'=>true,
			'load_configuration'=>true,
			'config'=>array( 'report_errors'=>false, 'log_errors'=>true, 'error_report_level'=>E_ALL, 'enable_cache'=>true, 'unset_cookie'=>false,'unset_session'=>false,'unset_post'=>false,'unset_get'=>false,'unset_request'=>false,'unset_env'=>false,'unset_files'=>false,'unset_server'=>false,'cache_time'=>15)
		);
		
		$args +=  $defaults;
		if($args['load_configuration'])
			$config=PVConfiguration::getSiteCompleteConfiguration() + $defaults['config'];
		else
			$config=$args['config']+$defaults['config'];
		
		$config = self::_applyFilter( get_class(), __FUNCTION__ , $config, array('event'=>'args'));
			
		self::setErrorReporting($config['report_errors'],$config['log_errors'], $config['error_report_level'] );
		
		if($args['initialize_database']) {
			PVDatabase::init($config);
			PVDatabase::setDatabase(0);
		}
		
		if($args['load_plugins'])
			self::loadPlugins();
		
		if($args['initialize_libraries'])
			PVLibraries::init();
			
		if($args['load_libraries'])
			PVLibraries::loadLibraries();
		
		if($args['initialize_template'])
			PVTemplate::init($config);
		
		if($args['initialize_router'])
			PVRouter::init($config);
		
		if($args['initialize_validator'])
			PVValidator::init();
		
		if($args['initialize_security'])
			PVSecurity::init($config);
		
		if($args['initialize_session'])
			PVSession::init($config);
		
		self::removeMagicQuotes();
		
		if(empty($config['default_time_zone'])){
			date_default_timezone_set('America/New_York');
		} else {
			date_default_timezone_set($config['default_time_zone']);
		}
		
		if($config['enable_cache']==1){
			self::setHeaderExpires($config['cache_time']);
		} else {
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header('Cache-Control: no-store, no-cache, must-revalidate'); 
			header('Cache-Control: post-check=0, pre-check=0', FALSE); 
			header('Pragma: no-cache');
		}

		if($config['unset_cookie']==1){
			self::unsetGlobalVariable("_COOKIE");
		}
		
		if($config['unset_session']==1){
			self::unsetGlobalVariable("_SESSION");
		}
		
		if($config['unset_post']==1){
			self::unsetGlobalVariable("_POST");
		}
		
		if($config['unset_get']==1){
			self::unsetGlobalVariable("_GET");
		}
		
		if($config['unset_request']==1){
			self::unsetGlobalVariable("_REQUEST");
		}
		
		if($config['unset_env']==1){
			self::unsetGlobalVariable("_ENV");
		}
		
		if($config['unset_files']==1){
			self::unsetGlobalVariable("_FILES");
		}
		
		if($config['unset_server']==1){
			self::unsetGlobalVariable("_SERVER");
		}
		
		self::_notify(get_class().'::'.__FUNCTION__, $config);
	}//end bootSystem
	
	
	/**
	 * Sets the rror errporting in ProdigyView. The levels are numberic.
	 * -0. Normal errors reparing
	 * 1. Report major and minor errors
	 * 2. Report fatal, notices and warnings
	 * 3. Report everything except notices
	 * 4. Report everything
	 * 
	 *In your xml configuration, look for these tags. 
	 * <report_errors>1</report_errors> 1 for displaying errors, 0 for not displaying errors
	 * <log_errors>1</log_errors> 1 for loggin errors to file, 0 for not logging errors to file
	 * <error_report_level>4</error_report_level> Setting the error repporting level
	 * 
	 * @param int/boolean $report_errors Set to true, errors will be displayed
	 * @param int/boolean $log_errors Set to true, errors will be log in the defined log
	 * @param int/string $error_reporting level Set the level errors will b shown
	 * 
	 * @return void
	 * @access private
	 */
	private static function setErrorReporting($report_errors=FALSE, $log_errors=TRUE ,$error_report_level=E_ALL) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $report_errors, $log_errors ,$error_report_level);
		
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('report_errors'=>$report_errors, 'log_errors'=>$log_errors, 'error_report_level'=>$error_report_level ), array('event'=>'args'));
		$report_errors = $filtered['report_errors'];
		$log_errors = $filtered['log_errors'];
		$error_report_level = $filtered['error_report_level'];
		
		if($error_report_level==0){
			error_reporting(E_ERROR | E_WARNING | E_PARSE);
		}
		else if($error_report_level==1){
			error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
		}
		else if($error_report_level==2){
			error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
		}
		else if($error_report_level==3){
			error_reporting(E_ALL ^ E_NOTICE);
		}
		else if($error_report_level==4){
			error_reporting(E_ALL);
		} 
		else if(!empty($error_report_level)){
			error_reporting($error_report_level);
		}
		
		if ($report_errors) {
			ini_set('display_errors','On');
		} else {
			ini_set('display_errors','Off');
		}
		
		if($log_errors){
			ini_set('log_errors', 'On');
			ini_set('error_log', PV_ERROR_LOG);
		}
		
		self::_notify(get_class().'::'.__FUNCTION__, $report_errors, $log_errors ,$error_report_level);
	}//end setReporting
	
	/**
	 * Loads the plugins set in the database and initiliazes
	 * them for site wide usage.The folder that the plugins are in
	 * can be set by using the PV_PLUGINS define.
	 * 
	 * @return void
	 * @access public
	 * @todo Add prepared query eventually
	 */
	public static function loadPlugins(){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		$query="SELECT plugin_function, plugin_file, plugin_directory FROM ".PVDatabase::getPluginsTableName()." WHERE plugin_enabled='1' AND plugin_language='php' ORDER BY plugin_order ";
		$result = PVDatabase::query($query);
    	
    	while ($row = PVDatabase::fetchArray($result)){
			$plugin_file=$row['plugin_directory'].$row['plugin_file'];
    		if(file_exists(PV_PLUGINS.$plugin_file)){
    			include_once(PV_PLUGINS.$plugin_file);
    		}
    	}//end while
    	
    	self::_notify(get_class().'::'.__FUNCTION__);
	}//end load plugins
	
	
	/**
	 * Unets a global at launch. Use for removing data from $_GET, $_SESSION
	 * $_POST, $_COOKIE, $_REQUEST, $_ENV.
	 * 
	 * @return void
	 * @access private
	 */
	private static function unsetGlobalVariable($global){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $global);
		
		$global = self::_applyFilter( get_class(), __FUNCTION__ , $global, array('event'=>'args'));
		
		foreach ($GLOBALS[$global] as $key => $var) {
			if ($var === $GLOBALS[$key]) {
				unset($GLOBALS[$key]);
			}
		}//end for
	
		self::_notify(get_class().'::'.__FUNCTION__, $global);
	}//end unsetGlobalVariable
	
	private static function stripSlashesRecursive($array) {
		
		$array = is_array($array) ? array_map(NULL, $array) : self::stripSlashesRecursive($array);
		return $array;
	}
	
	/**
	 * Magic Quoutes should be disabled on your system. But if it is on, this function
	 * will remove from any varabiles.
	 * 
	 * @return void
	 * @access void
	 */
	private static function removeMagicQuotes() {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		if ( get_magic_quotes_gpc() ) {
			array_walk_recursive($_GET, 'stripslashes_gpc');
    		array_walk_recursive($_POST, 'stripslashes_gpc');
            array_walk_recursive($_COOKIE, 'stripslashes_gpc');
            array_walk_recursive($_REQUEST, 'stripslashes_gpc');
		}
		
		self::_notify(get_class().'::'.__FUNCTION__);
	}//end 
	
	/**
	 * A boot, set in what amount of time the header will expire in. Should be
	 * set in munutes. The configuration for this file can be changed in the xml
	 * configuration file in the <cache_time>x</cache_time> tags.
	 * 
	 * @return void
	 * @access private
	 */
	private static function setHeaderExpires($expirationMinutes) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $expirationMinutes);
		
		$expirationMinutes = self::_applyFilter( get_class(), __FUNCTION__ , $expirationMinutes, array('event'=>'args'));
		  
 		header(  'Expires: '.gmdate('D, d M Y H:i:s', time()+$expirationMinutes).'GMT'); 
		
		self::_notify(get_class().'::'.__FUNCTION__, $expirationMinutes); 
 	}//end setHeaderExpires
	
}//end class 