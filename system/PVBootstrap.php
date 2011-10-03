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
	
	function __construct(){
		
	}
	
	
	public static function getSettings() {
		
		if(file_exists(PV_DB_CONFIG)){
			include(PV_DB_CONFIG);
		}

		// Database variables
		$settings['dbhost'] = $config_db_hostname;
		$settings['dbusername'] = $config_db_user;
		$settings['dbpassword'] = $config_db_pass;
		$settings['dbtype'] = $config_db_type;
		$settings['dbname'] = $config_db_name;
		$settings['dbport'] = $config_db_port;
		$settings['dbschema'] = $config_db_schema;
		$settings['dbprefix'] = $config_db_prefix;
		
		//User Variabes
		$settings['username'] = $pv_username;
		$settings['password'] = $pv_password;
		$settings['email'] = $pv_email;
		$settings['user_unique_id'] = $user_unique_id;
		$settings['pv_version'] = $pv_version;
		$settings['license'] = $pv_license;
		
		return $settings;
	
	}//end getSettings
	
	/**
	 * Booth the ProdigyView system. Initilize variables, set logging,
	 * sessions, etc.
	 * 
	 * return void
	 */
	public static function bootSystem() {
		$config=PVConfiguration::getSiteCompleteConfiguration();
		self::setErrorReporting($config['report_errors'],$config['log_errors'], $config['error_report_level'] );
		PVDatabase::init();
		PVDatabase::setDatabase(0);
		self::loadPlugins();
		
		PVLibraries::init();
		PVTemplate::init();
		PVRouter::init();
		PVValidator::init();
		
		self::removeMagicQuotes();
		
		if(empty($config['default_time_zone'])){
			date_default_timezone_set('America/New_York');
		} else {
			date_default_timezone_set($config['default_time_zone']);
		}
		
		if($config['enable_session_handling']==1 || $config['enable_session_handling']=='true'){
			PVSession::setSessionConfig($config);
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
		
	}//end bootSystem
	
	
	/**
	 * Sets the rror errporting in ProdigyView. The levels are numberic.
	 * -0. Normal errors reparing
	 * 1. Report major and minor errors
	 * 2. Report fatal, notices and warnings
	 * 3. Report everything except notices
	 * 4. Report everything
	 * 
	 * @param int/boolean report_errors: Set to true, errors will be displayed
	 * @param int/boolean log_errors: Set to true, errors will be log in the defined log
	 * @param int error_reporting level: Set the level errors will b shown
	 * 
	 */
	private static function setErrorReporting($report_errors, $log_errors ,$error_report_level) {
	
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
		
		if ($report_errors) {
			
			ini_set('display_errors','On');
		} else {
			ini_set('display_errors','Off');
		}
		
		if($log_errors){
			ini_set('log_errors', 'On');
			ini_set('error_log', PV_ERROR_LOG);
		}
	}//end setReporting
	
	/**
	 * Loads the plugins set in the database and initiliazes
	 * them for site wide usage.
	 */
	function loadPlugins(){
		
		$query="SELECT plugin_function, plugin_file, plugin_directory FROM ".PVDatabase::getPluginsTableName()." WHERE plugin_enabled='1' AND plugin_language='php' ORDER BY plugin_order ";
		$result = PVDatabase::query($query);
    	
    	while ($row = PVDatabase::fetchArray($result)){
			$plugin_file=$row['plugin_directory'].$row['plugin_file'];
    		if(file_exists(PV_PLUGINS.$plugin_file)){
    			include_once(PV_PLUGINS.$plugin_file);
    		}
    	}//end while
	}//end load plugins
	
	
	/**
	 * Unets a global at launch. Use for removing data from $_GET, $_SESSION
	 * $_POST, $_COOKIE, $_REQUEST, $_ENV.
	 */
	private static function unsetGlobalVariable($global){
		
		foreach ($GLOBALS[$global] as $key => $var) {
			if ($var === $GLOBALS[$key]) {
				unset($GLOBALS[$key]);
			}
		}//end for
	
	}//end unsetGlobalVariable
	
	private static function stripSlashesRecursive($array) {
		$array = is_array($array) ? array_map(NULL, $array) : self::stripSlashesRecursive($array);
		return $array;
	}
	
 	
	/**
	 * Magic Quoutes should be disabled on your system. But if it is on, this function
	 * will remove from any varabiles.
	 */
	private static function removeMagicQuotes() {
		if ( get_magic_quotes_gpc() ) {
			array_walk_recursive($_GET, 'stripslashes_gpc');
    		array_walk_recursive($_POST, 'stripslashes_gpc');
            array_walk_recursive($_COOKIE, 'stripslashes_gpc');
            array_walk_recursive($_REQUEST, 'stripslashes_gpc');
		}
	}//end 
	
	/**
	 * A boot, set in what amount of time the header will expire in. Should be
	 * set in munutes.
	 */
	private static function setHeaderExpires($expirationMinutes) {  
 		header(  'Expires: '.gmdate('D, d M Y H:i:s', time()+$expirationMinutes).'GMT');  
 	}//end setHeaderExpires
	
}//end class 
