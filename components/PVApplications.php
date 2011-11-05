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

class PVApplications extends PVStaticObject {
	
	private static $stored_app_objects = array();
	private static $stored_app_objects_admin = array();
	private static $store_objects = true;
	
	/**
	 * Initliazes the class and settings configuration
	 * 
	 * @param array $config Configuration file of default values
	 * 			-'store_objects' _boolean_: Set the application object to be stored. When application is called
	 * 			more than once, the stored object will be used and a new object will not be initalized.
	 * 
	 * @return void
	 * @access public
	 */
	public static function init($config = array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $config);
		
		$defaults = array(
			'store_objects'=>true
		);
		
		$config + $defaults;
		$config = self::_applyFilter( get_class(), __FUNCTION__ , $config, array('event'=>'args'));
		self::$store_objects = $config['store_objects'];
		
		self::_notify(get_class().'::'.__FUNCTION__, $config);
	}
	
	/**
	 * Executes an application that has been installed. Only exectues that application front end side
	 * 
	 * @param mixed $app The id or the unique identifer of the application
	 * @param string $command The command to be executed
	 * @param args $args An infinite amount of commands that will be passed to the object
	 * 
	 * @return mixed $arg The value the command will returned, if any
	 * @access public
	 */
	public static function pv_exec($app, $command) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app, $command);
		
		$args = func_get_args();
        array_shift($args);
       
        $passable_args = array();
        foreach($args as $key => &$arg){
           $passable_args[$key] = &$arg;
        }
		
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('app'=>$app, 'passable_args'=>$passable_args ), array('event'=>'args'));
		$app = $filtered['app'];
		$passable_args  = $filtered['passable_args'];
		
		if(self::$store_objects==FALSE && !is_object($app)){
			$temp_object=self::$stored_app_objects[$app];
			if(!empty($temp_object) && is_object($temp_object)){
				$app=$temp_object;
			}
		}
		
		if(is_object ($app)) {
			$value = self::_invokeMethod($appObject, 'commandInterpreter', $passable_args);
		} else {
			if(PVValidator::isID($app)){
				$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app' ";
			}
			else{
				$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app' ";
			}
			
			$result=PVDatabase::query($query);
			$row=PVDatabase::fetchArray($result);
				
			$app_directory=$row['app_directory'];
			$app_file=$row['app_file'];
			$app_object=trim($row['app_object']);
			
			$array=preg_split('/;|,/', $row['jquery_libraries']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueueJquery($name);
				}
			}//end for
			
			$array=preg_split('/;|,/', $row['javascript_libraries']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueueJavascript($name);
				}
			}//end for
			
			$array=preg_split('/;|,/', $row['prototype_libraries']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueuePrototype($name);
				}
			}//end for
			
			$array=preg_split('/;|,/', $row['mootools_libraries']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueueMootools($name);
				}
			}//end for
			$array=preg_split('/;|,/', $row['css_files']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueueCss($name);
				}
			}//end for
			
			include_once(PV_APPLICATIONS.$app_directory.$app_file);
			
			if(!empty($app_object)){
				$appObject=new $app_object;
				self::$stored_app_objects[$app]=$appObject;
				
				$value = self::_invokeMethod($appObject, 'commandInterpreter', $passable_args);
			}
			
			self::_notify(get_class().'::'.__FUNCTION__, $value, $app, $passable_args);
			$value = self::_applyFilter( get_class(), __FUNCTION__ , $value , array('event'=>'return'));
			
			return $value;
		}//end else
	}//end pv_exec
	
	/**
	 * Executes an application that has been installed. Only exectues that application backend-end
	 * 
	 * @param mixed $app The id or the unique identifer of the application
	 * @param string $command The command to be executed
	 * @param args $args An infinite amount of commands that will be passed to the object
	 * 
	 * @return mixed $arg The value the command will returned, if any
	 * @access public
	 */
	public static function pv_exec_admin($app, $command) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app, $command);
		
		$args = func_get_args();
        array_shift($args);
       
        $passable_args = array();
        foreach($args as $key => &$arg){
            $passable_args[$key] = &$arg;
        }
		
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('app'=>$app, 'passable_args'=>$passable_args ), array('event'=>'args'));
		$app = $filtered['app'];
		$passable_args  = $filtered['passable_args'];
		
		if(self::$store_objects==FALSE && !is_object($app)){
			$temp_object=self::$stored_app_objects_admin[$app];
			if(!empty($temp_object) && is_object($temp_object)){
				$app=$temp_object;
			}
		}
		
		if(PV_IS_ADMIN==true){
			
			if(is_object ($app)){
				$value = self::_invokeMethod($appObject, 'commandInterpreter', $passable_args);
			} else {
				if(PVValidator::isID($app)){
					$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app' ";
				} else {
					$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app' ";
				}
				
				$result=PVDatabase::query($query);
				$row=PVDatabase::fetchArray($result);
					
				$app_directory=$row['admin_directory'];
				$app_file=$row['admin_file'];
				$app_object=trim($row['admin_object']);
				
				
				$array=preg_split('/;|,/', $row['jquery_libraries']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueueJquery($name);
					}
				}//end for
				
				$array=preg_split('/;|,/', $row['javascript_libraries']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueueJavascript($name);
					}
				}//end for
				
				$array=preg_split('/;|,/', $row['prototype_libraries']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueuePrototype($name);
					}
				}//end for
				
				$array=preg_split('/;|,/', $row['mootools_libraries']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueueMootools($name);
					}
				}//end for
				$array=preg_split('/;|,/', $row['css_files']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueueCss($name);
					}
				}//end for
				
				include_once(PV_ADMIN_APPLICATIONS.$app_directory.$app_file);
				
				if(!empty($app_object)){
					$appObject=new $app_object;
					self::$stored_app_objects_admin[$app]=$appObject;
					$value = self::_invokeMethod($appObject, 'commandInterpreter', $passable_args);
				}
			}//end else
		
			self::_notify(get_class().'::'.__FUNCTION__, $value, $app, $passable_args);
			$value = self::_applyFilter( get_class(), __FUNCTION__ , $value , array('event'=>'return'));
				
			return $value;
		}//end is admin
	
	}//end pv_exec
	
	/**
	 * Install or update an application
	 * 
	 * @param array $args Arguements that define which fields go into an application.
	 * 			-'app_name' _string_: The nameof the application
	 * 			-'app_file' _string_: The location of the files for the app frontend. Should contain the main class
	 * 			-'app_directory' _string_: The directory that contains the file for the applications front end
	 * 			-'app_unique_id' _string-: The unique identifer for the application.
	 * 			-'app_parameters' _string_: A location to store parameters for the application
	 * 			-'enabled' _boolean_: Is the application enabled
	 * 			-'app_object' _string_: The name of the class that will be initialized to object for the frontend when the applicaiton is called
	 * 			-'jquery_libraries' _string_: A string of comman delimented jquery libraries to load when the application is executed
	 * 			-'javacript_libraries' _string_: A string of comman delimented javacript libraries to load when the application is executed
	 * 			-'prototype_libraries' _string_: A string of comman delimented prototype libraries to load when the application is executed
	 * 			-'mootools_libraries' _string_: A string of comman delimented mootools libraries to load when the application is executed
	 * 			-'css_files' _string_: A string of comman delimented css files to load when the application is executed
	 * 			-'admin_jquery_libraries' _string_: A string of comman delimented jquery libraries to load when the backend of the application is executed
	 * 			-'admin_javacript_libraries' _string_: A string of comman delimented javacript libraries to load when the backend of the  application is executed
	 * 			-'admin_prototype_libraries' _string_: A string of comman delimented prototype libraries to load when the backend of the  application is executed
	 * 			-'admin_mootools_libraries' _string_: A string of comman delimented mootools libraries to load when the backend of the  application is executed
	 * 			-'admin_css_files' _string_: A string of comman delimented css files to load when the backend of the  application is executed
	 * 			
	 */
	public static function installApplication($args=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::getApplicationDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args, array('event'=>'args'));
		$args=PVDatabase::makeSafe($args);
		extract($args);
			
		$enabled=ceil($enabled);
		$show_admin=ceil($show_admin);
		$backend_app=ceil($backend_app);
		$has_module=ceil($has_module);
		$show_main_section=ceil($show_main_section);
		$is_application_editable=ceil($is_application_editable);
			
		if(!PVValidator::isDouble($app_version) && !PVValidator::isInteger($app_version)){
			$app_version=0;
		}
			
		$query="SELECT app_version, app_id FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_unique_id' ";
	  	$result = PVDatabase::query($query);
	  		
	  	if(PVDatabase::resultRowCount($result) <= 0){
			$query="INSERT INTO ".PVDatabase::getApplicationsTableName()."(app_name , app_file , app_directory , app_unique_id , app_parameters , app_object , jquery_libraries , javascript_libraries , show_admin , admin_directory , admin_file , admin_object , backend_app , admin_jquery_libraries , admin_javascript_libraries , admin_mootools_libraries , prototype_libraries , admin_prototype_libraries , app_default_page , app_version , app_description , mootools_libraries , css_files , admin_css_files , uninstall_file , has_module, app_icon, app_preferences, is_application_editable, app_license, application_type, application_language, app_site, app_author ) VALUES('$app_name' , '$app_file' , '$app_directory' , '$app_unique_id' , '$app_paramters' , '$app_object' , '$jquery_libraries' , '$javascript_libraries' , '$show_admin' , '$admin_directory' , '$admin_file' , '$admin_object' , '$backend_app' , '$admin_jquery_libraries' , '$admin_javascript_libraries' , '$admin_mootools_libraries' , '$prototype_libraries' , '$admin_prototype_libraries' , '$app_default_page' , '$app_version' , '$app_description' , '$mootools_libraries' , '$css_files' , '$admin_css_files' , '$uninstall_file' , '$has_module', '$app_icon' , '$app_preferences', '$is_application_editable', '$app_license', '$application_type' , '$application_language', '$app_site', '$app_author') ";
			$app_id=PVDatabase::return_last_insert_query($query, "app_id", PVDatabase::getApplicationsTableName());
		} else {
			$query="UPDATE ".PVDatabase::getApplicationsTableName()." SET app_name='$app_name' , app_file='$app_file' , app_directory='$app_directory' , app_unique_id='$app_unique_id' , app_object='$app_object' , jquery_libraries='$jquery_libraries' , javascript_libraries='$javascript_libraries' , show_admin='$show_admin' , admin_directory='$admin_directory' , admin_file='$admin_file' , admin_object='$admin_object' , backend_app='$backend_app' , admin_jquery_libraries='$admin_jquery_libraries' , admin_javascript_libraries='$admin_javascript_libraries' , admin_mootools_libraries='$admin_mootools_libraries' , prototype_libraries='$prototype_libraries' , admin_prototype_libraries='$admin_prototype_libraries' , app_default_page='$app_default_page' , app_version='$app_version' , app_description='$app_description' , mootools_libraries='$mootools_libraries' , css_files='$css_files' , admin_css_files='$admin_css_files' , uninstall_file='$uninstall_file' , has_module='$has_module', app_icon='$app_icon', app_preferences='$app_preferences', is_application_editable='$is_application_editable', app_license='$app_license', application_type='$application_type', application_language='$application_language', app_site='$app_site', app_author='$app_author' WHERE app_unique_id='$app_unique_id' ";
			PVDatabase::query($query);	
			$row = PVDatabase::fetchArray($result);
			$app_id = $row['app_id'];		
		}//end else
		
		self::_notify(get_class().'::'.__FUNCTION__, $app_id , $args);
		$app_id  = self::_applyFilter( get_class(), __FUNCTION__ , $app_id  , array('event'=>'return'));
			
		return $app_id;
	}//end installApplication
	
	/**
	 * Get the application based upon the id of the application
	 * 
	 * @param mixed $app_id The ID of the application. Either is the unique name or the auto generated id
	 * 
	 * @return array $application The data that the application contains
	 * @access public
	 */
	public static function getApplication($app_id) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app_id);
		
		$app_id = self::_applyFilter( get_class(), __FUNCTION__ , $app_id, array('event'=>'args')); 
		$app_id=PVDatabase::makeSafe($app_id);
			
		if(PVValidator::isID($app_id)){
			$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app_id'";
		} else {
			$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_id'";
		}
			
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$row= PVDatabase::formatData($row);
			
		self::_notify(get_class().'::'.__FUNCTION__, $row, $app_id);
		$row  = self::_applyFilter( get_class(), __FUNCTION__ , $row  , array('event'=>'return'));
		
		return $row;
	}//end getApplicationInfo
	
	/**
	 * Returns a list of applications found in the database. Applications can be searched for by using arguements that define
	 * the application. PV Standard Search Query rules apply.
	 * 
	 * @param array @args Arguements that define the application's field and used for searching.
	 * 
	 * @return array $applications An array of applications that are found with the search critera
	 * @access public
	 */
	public static function getApplicationList($args=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::getApplicationDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args, array('event'=>'args'));
		$custom_where=$args['custom_where'];
		$custom_join=$args['custom_join'];
		$custom_select=$args['custom_select'];
		$args= PVDatabase::makeSafe($args);
		extract($args);
		
		$first=1;
		
		$content_array=array();
		$table_name=PVDatabase::getApplicationsTableName();
		$db_type=PVDatabase::getDatabaseType();
			
		$WHERE_CLAUSE='';
		
		if(!empty($app_id)){
				
			$app_id=trim($app_id);
			
			if($first==0 && ($app_id[0]!='+' && $app_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_id[0]=='+' || $app_id[0]==',') && $first==1 ){
				$app_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_id, 'app_id');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_name)){
				
			$app_name=trim($app_name);
			
			if($first==0 && ($app_name[0]!='+' && $app_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_name[0]=='+' || $app_name[0]==',') && $first==1 ){
				$app_name[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_name, 'app_name');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_file)){
				
			$app_file=trim($app_file);
			
			if($first==0 && ($app_file[0]!='+' && $app_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_file[0]=='+' || $app_file[0]==',') && $first==1 ){
				$app_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_file, 'app_file');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_directory)){
				
			$app_directory=trim($app_directory);
			
			if($first==0 && ($app_directory[0]!='+' && $app_directory[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_directory[0]=='+' || $app_directory[0]==',') && $first==1 ){
				$app_directory[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_directory, 'app_directory');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_unique_id)){
				
			$app_unique_id=trim($app_unique_id);
			
			if($first==0 && ($app_unique_id[0]!='+' && $app_unique_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_unique_id[0]=='+' || $app_unique_id[0]==',') && $first==1 ){
				$page_alias[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_unique_id, 'app_unique_id');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_paramters)){
				
			$app_paramters=trim($app_paramters);
			
			if($first==0 && ($app_paramters[0]!='+' && $app_paramters[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_paramters[0]=='+' || $app_paramters[0]==',') && $first==1 ){
				$app_paramters[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_paramters, 'app_paramters');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($enabled)){
				
			$enabled=trim($enabled);
			
			if($first==0 && ($enabled[0]!='+' && $enabled[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($enabled[0]=='+' || $enabled[0]==',') && $first==1 ){
				$enabled[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($enabled, 'enabled');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_object)){
				
			$app_object=trim($app_object);
			
			if($first==0 && ($app_object[0]!='+' && $app_object[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_object[0]=='+' || $app_object[0]==',') && $first==1 ){
				$app_object[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_object, 'app_object');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($jquery_libraries)){
				
			$jquery_libraries=trim($jquery_libraries);
			
			if($first==0 && ($jquery_libraries[0]!='+' && $jquery_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($jquery_libraries[0]=='+' || $jquery_libraries[0]==',') && $first==1 ){
				$jquery_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($jquery_libraries, 'jquery_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($javascript_libraries)){
				
			$page_short_url=trim($javascript_libraries);
			
			if($first==0 && ($javascript_libraries[0]!='+' && $javascript_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($javascript_libraries[0]=='+' || $javascript_libraries[0]==',') && $first==1 ){
				$page_short_url[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($javascript_libraries, 'javascript_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($show_admin)){
				
			$show_admin=trim($show_admin);
			
			if($first==0 && ($show_admin[0]!='+' && $show_admin[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($show_admin[0]=='+' || $show_admin[0]==',') && $first==1 ){
				$show_admin[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($show_admin, 'show_admin');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_directory)){
				
			$admin_directory=trim($admin_directory);
			
			if($first==0 && ($admin_directory[0]!='+' && $admin_directory[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_directory[0]=='+' || $admin_directory[0]==',') && $first==1 ){
				$admin_directory[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_directory, 'admin_directory');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_directory)){
				
			$admin_directory=trim($admin_directory);
			
			if($first==0 && ($admin_directory[0]!='+' && $admin_directory[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_directory[0]=='+' || $admin_directory[0]==',') && $first==1 ){
				$admin_directory[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_directory, 'admin_directory');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_file)){
				
			$admin_file=trim($admin_file);
			
			if($first==0 && ($admin_file[0]!='+' && $admin_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_file[0]=='+' || $admin_file[0]==',') && $first==1 ){
				$admin_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_file, 'admin_file');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_object)){
				
			$admin_object=trim($admin_object);
			
			if($first==0 && ($admin_object[0]!='+' && $admin_object[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_object[0]=='+' || $admin_object[0]==',') && $first==1 ){
				$admin_object[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_object, 'admin_object');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($backend_app)){
				
			$backend_app=trim($backend_app);
			
			if($first==0 && ($backend_app[0]!='+' && $backend_app[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($backend_app[0]=='+' || $backend_app[0]==',') && $first==1 ){
				$backend_app[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($backend_app, 'backend_app');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_jquery_libraries)){
				
			$admin_jquery_libraries=trim($admin_jquery_libraries);
			
			if($first==0 && ($admin_jquery_libraries[0]!='+' && $admin_jquery_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_jquery_libraries[0]=='+' || $admin_jquery_libraries[0]==',') && $first==1 ){
				$admin_jquery_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_jquery_libraries, 'admin_jquery_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_javascript_libraries)){
				
			$admin_javascript_libraries=trim($admin_javascript_libraries);
			
			if($first==0 && ($admin_javascript_libraries[0]!='+' && $admin_javascript_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_javascript_libraries[0]=='+' || $admin_javascript_libraries[0]==',') && $first==1 ){
				$admin_javascript_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_javascript_libraries, 'admin_javascript_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_javascript_libraries)){
				
			$admin_javascript_libraries=trim($admin_javascript_libraries);
			
			if($first==0 && ($admin_javascript_libraries[0]!='+' && $admin_javascript_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_javascript_libraries[0]=='+' || $admin_javascript_libraries[0]==',') && $first==1 ){
				$admin_javascript_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_javascript_libraries, 'admin_javascript_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_mootools_libraries)){
				
			$admin_mootools_libraries=trim($admin_mootools_libraries);
			
			if($first==0 && ($admin_mootools_libraries[0]!='+' && $admin_mootools_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_mootools_libraries[0]=='+' || $admin_mootools_libraries[0]==',') && $first==1 ){
				$admin_mootools_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_mootools_libraries, 'admin_mootools_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($prototype_libraries)){
				
			$prototype_libraries=trim($prototype_libraries);
			
			if($first==0 && ($prototype_libraries[0]!='+' && $prototype_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($prototype_libraries[0]=='+' || $prototype_libraries[0]==',') && $first==1 ){
				$prototype_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($prototype_libraries, 'prototype_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_prototype_libraries)){
				
			$admin_prototype_libraries=trim($admin_prototype_libraries);
			
			if($first==0 && ($admin_prototype_libraries[0]!='+' && $admin_prototype_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_prototype_libraries[0]=='+' || $admin_prototype_libraries[0]==',') && $first==1 ){
				$admin_prototype_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_prototype_libraries, 'admin_prototype_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_default_page)){
				
			$app_default_page=trim($app_default_page);
			
			if($first==0 && ($app_default_page[0]!='+' && $app_default_page[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_default_page[0]=='+' || $app_default_page[0]==',') && $first==1 ){
				$app_default_page[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_default_page, 'app_default_page');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_version)){
				
			$app_version=trim($app_version);
			
			if($first==0 && ($app_version[0]!='+' && $app_version[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_version[0]=='+' || $app_version[0]==',') && $first==1 ){
				$app_version[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_version, 'app_version');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_description)){
				
			$app_description=trim($app_description);
			
			if($first==0 && ($app_description[0]!='+' && $app_description[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_description[0]=='+' || $app_description[0]==',') && $first==1 ){
				$app_description[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_description, 'app_description');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($mootools_libraries)){
				
			$mootools_libraries=trim($mootools_libraries);
			
			if($first==0 && ($mootools_libraries[0]!='+' && $mootools_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($mootools_libraries[0]=='+' || $mootools_libraries[0]==',') && $first==1 ){
				$mootools_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mootools_libraries, 'mootools_libraries');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($css_files)){
				
			$css_files=trim($css_files);
			
			if($first==0 && ($css_files[0]!='+' && $css_files[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($css_files[0]=='+' || $css_files[0]==',') && $first==1 ){
				$css_files[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($css_files, 'css_files');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_css_files)){
				
			$admin_css_files=trim($admin_css_files);
			
			if($first==0 && ($admin_css_files[0]!='+' && $admin_css_files[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_css_files[0]=='+' || $admin_css_files[0]==',') && $first==1 ){
				$admin_css_files[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_css_files, 'admin_css_files');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($uninstall_file)){
				
			$uninstall_file=trim($uninstall_file);
			
			if($first==0 && ($uninstall_file[0]!='+' && $uninstall_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($uninstall_file[0]=='+' || $uninstall_file[0]==',') && $first==1 ){
				$uninstall_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($uninstall_file, 'uninstall_file');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($has_module)){
				
			$has_module=trim($has_module);
			
			if($first==0 && ($has_module[0]!='+' && $has_module[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($has_module[0]=='+' || $has_module[0]==',') && $first==1 ){
				$has_module[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($has_module, 'has_module');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($show_main_section)){
				
			$show_main_section=trim($show_main_section);
			
			if($first==0 && ($show_main_section[0]!='+' && $show_main_section[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($show_main_section[0]=='+' || $show_main_section[0]==',') && $first==1 ){
				$show_main_section[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($show_main_section, 'show_main_section');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_icon)){
				
			$app_icon=trim($app_icon);
			
			if($first==0 && ($app_icon[0]!='+' && $app_icon[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_icon[0]=='+' || $app_icon[0]==',') && $first==1 ){
				$app_icon[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_icon, 'app_icon');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_preferences)){
				
			$app_preferences=trim($app_preferences);
			
			if($first==0 && ($app_preferences[0]!='+' && $app_preferences[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_preferences[0]=='+' || $app_preferences[0]==',') && $first==1 ){
				$app_preferences[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_preferences, 'app_preferences');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($is_application_editable)){
				
			$is_application_editable=trim($is_application_editable);
			
			if($first==0 && ($is_application_editable[0]!='+' && $is_application_editable[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($is_application_editable[0]=='+' || $is_application_editable[0]==',') && $first==1 ){
				$is_application_editable[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($is_application_editable, 'is_application_editable');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($app_license)){
				
			$app_licensee=trim($app_license);
			
			if($first==0 && ($app_license[0]!='+' && $app_license[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($app_license[0]=='+' || $app_license[0]==',') && $first==1 ){
				$app_license[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_license, 'app_license');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($application_type)){
				
			$application_type=trim($application_type);
			
			if($first==0 && ($application_type[0]!='+' && $application_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($application_type[0]=='+' || $application_type[0]==',') && $first==1 ){
				$application_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($application_type, 'application_type');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($application_language)){
				
			$application_language=trim($application_language);
			
			if($first==0 && ($application_language[0]!='+' && $application_language[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($application_language[0]=='+' || $application_language[0]==',') && $first==1 ){
				$application_language[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($application_language, 'application_language');
			
			$first=0;
		}//end not empty app_id
		
		$JOINS='';
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=' '.$custom_where.' ';
		}
		
		if(!empty($custom_join)){
			$JOINS.=' '.$custom_join.' ';
		}
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
		
		if(!empty($distinct)){
			$PREFIX_ARGS.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$PREFIX_ARGS.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset($table_name, $JOINS , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);
			
			if($db_type=='mysql' || $db_type=='postgresql'){
				$limit=' '.$page_results['limit_offset'];
			}
			else if($db_type=='mssql'){
				$WHERE_CLAUSE.=' '.$page_results['limit_offset'];
				$table_name=$page_results['from_clause'];
			}
		}
	
		if(!empty($group_by)){
			$WHERE_CLAUSE.=" GROUP BY $group_by";
		}
		
		if(!empty($having)){
			$WHERE_CLAUSE.=" HAVING $having";
		}
		
		if(!empty($order_by)){
			$WHERE_CLAUSE.=" ORDER BY $order_by";
		}
		
		if(!empty($limit) && !$paged && ($db_type=='mysql' || $db_type=='postgresql') ){
			$WHERE_CLAUSE.=" LIMIT $limit";
		}
		
		if($paged){
			$WHERE_CLAUSE.=" $limit";
		}
		
		if(empty($custom_select)){
			$custom_select='*';
		}
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
    	
		$result = PVDatabase::query($query);
    	
    	while ($row = PVDatabase::fetchArray($result)){
			if($paged){
				$row['current_page']=$page_results['current_page'];
				$row['last_page']=$page_results['last_page'];
				$row['total_pages']=$page_results['total_pages'];
			}
			
    		array_push($content_array, $row);
    	}//end while
    	
    	$content_array=PVDatabase::formatData($content_array);
		self::_notify(get_class().'::'.__FUNCTION__, $content_array, $args);
		$content_array  = self::_applyFilter( get_class(), __FUNCTION__ , $content_array  , array('event'=>'return'));
		
    	return $content_array;
	}
	
	/**
	 * Removes an application from the database and deletes the assoicated files with that application.
	 * 
	 * @param id $app_id The id of the application to be deleted
	 * @param array @options Options that define if to delete assoicated content with the application. Defaults are set to true.
	 * 			-'remove_app_content' _boolean_: Will remove any content associated with the application
	 * 			-'remove_app_options' _boolean_: Will remove any options associated with the application
	 * 			-'remove_app_modules' _boolean_: Will remove any modules associated with the application
	 * 			-'remove_app_points' _boolean_: Will remove any points associated with the application
	 * 			-'remove_app_subscriptions' _boolean_: Will remove any subscriptions associated with the application
	 * 			-'remove_app_categories' _boolean_: Will remove any categories associated with the application
	 * 			-'remove_modules_admin' _boolean_: Will removes any admin to modules that are associated with this application
	 * 
	 * @return void
	 * @access public
	 */
	public static function removeApplication($app_id, $options=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app_id, $options);
		
		$defaults=array(
			'remove_app_content'=>TRUE,
			'remove_app_options'=>TRUE,
			'remove_app_modules'=>TRUE,
			'remove_app_points'=>TRUE,
			'remove_app_subscriptions'=>TRUE,
			'remove_app_categories'=>TRUE,
			'remove_modules_admin'=>TRUE
		);
		
		$options += $defaults;
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('app_id'=>$app_id, 'options'=>$options ), array('event'=>'args'));
		$app_id = $filtered['app_id'];
		$options  = $filtered['options'];
		
		extract($options);
		
		$app_info=self::getApplication($app_id);
		
		if(!empty($app_info) && is_array($app_info) ){
			
			extract($app_info);
			
			if($remove_app_content){
				
				$app_content=PVContent::getContentList(array('app_id'=>$app_id));
				
				foreach($app_content as $key=>$value){
					PVContent::deleteContent($value['content_id']);
				}//end foreach
				
			}//end remove_app_content
			
			if($remove_app_options){
			
				$app_options=PVTools::getOptionList(array('app_id'=>$app_id));
				
				foreach($app_options as $key=>$value){
					PVTools::deleteOption($value['option_id']);
				}//end foreach
			}
			
			if($remove_app_modules){
			
				$app_modules=PVModules::getModuleList(array('module_app'=>$app_id));
				
				foreach($app_modules as $key=>$value){
					PVModules::deleteModule($value['module_id']);
				}//end foreach
			}
			
			if($remove_app_points){
			
				$app_points=PVPoints::getPointsList(array('app_id'=>$app_id));
				
				foreach($app_points as $key=>$value){
					PVPoints::deletePoint($value['point_id']);
				}//end foreach
			}
			
			if($remove_app_subscriptions){
			
				$app_subscriptions=PVSubscriptions::getSubscriptionList(array('app_id'=>$app_id));
				
				foreach($app_subscriptions as $key=>$value){
					PVSubscriptions::deleteSubscription($value['subscription_id']);
				}//end foreach
			}
			
			if($remove_app_categories){
			
				$app_categories=PVContent::getCategoryList(array('app_id'=>$app_id));
				
				foreach($app_categories as $key=>$value){
					PVContent::deleteCategory($value['category_id']);
				}//end foreach
			}
			
			if($remove_modules_admin){
			
				$app_module_admin=PVModules::getModuleAdminList(array('module_app_identifier'=>$app_unique_id));
				
				foreach($app_module_admin as $key=>$value){
					PVModules::deleteModuleAdmin($value['module_unique_id'], $value['module_app_identifier'] );
				}//end foreach
			}
			
			if(!empty($app_directory)){
				if(file_exists(PV_APPLICATIONS.$app_directory)){
					PVFileManager::deleteDirectory(PV_APPLICATIONS.$app_directory);
				}
			}
			
			if(file_exists(PV_APPLICATIONS.$app_directory.$app_file)){
				unlink(PV_APPLICATIONS.$app_directory.$app_file);	
			}
			
			if(!empty($app_directory)){
				if(file_exists(PV_APPLICATIONS.$admin_directory)){
					PVFileManager::deleteDirectory(PV_APPLICATIONS.$admin_directory);
				}
			}
			
			if(file_exists(PV_APPLICATIONS.$admin_directory.$admin_file)){
				unlink(PV_APPLICATIONS.$admin_directory.$admin_file);	
			}
			
			$query="DELETE FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app_id' ";
			PVDatabase::query($query);
			self::_notify(get_class().'::'.__FUNCTION__, $app_id, $options);
		}//end not epty
		
	}//end removeApplication
	
	/**
	 * Retrieve the parameters associated with an application.
	 * 
	 * @param mixed $app_id The id of of the application can either be the id or the application's unique identifer.
	 * 
	 * @return string $parameters Returns the parameters associated with the application
	 * @access public
	 */
	public static function getApplicationParameters($app_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app_id);
		
		$app_id = self::_applyFilter( get_class(), __FUNCTION__ , $app_id, array('event'=>'args'));
		$app_id=PVDatabase::makeSafe($app_id);
		
		if(PVValidator::isID($app_id)){
			$query="SELECT app_parameters FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app_id' ";
		}
		else{
			$query="SELECT app_parameters FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_id' ";
		}
		
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		self::_notify(get_class().'::'.__FUNCTION__, $row, $app_id);
		$row  = self::_applyFilter( get_class(), __FUNCTION__ , $row  , array('event'=>'return'));
		
		return $row['app_parameters'];
	}//end getApplicationParameters
	
	/**
	 * Sets the applications paramter field.
	 * 
	 * @param mixed $app_id The id of of the application can either be the id or the application's unique identifer.
	 * @param string $parameters The data to set in the application's parameters field.
	 * 
	 * @return void
	 * @access public
	 */
	public static function setApplicationParameters($app_id, $parameters) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app_id, $parameters);
		
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('app_id'=>$app_id, 'parameters'=>$parameters ), array('event'=>'args'));
		$app_id = $filtered['app_id'];
		$parameters  = $filtered['parameters'];
		$app_id=PVDatabase::makeSafe($app_id); 
		$parameters=PVDatabase::makeSafe($parameters);
		
		if(PVValidator::isID($app_id)){
			$query="UPDATE  ".PVDatabase::getApplicationsTableName()." SET app_parameters'".$parameters."' WHERE app_id='$app_id' ";
		} else {
			$query="UPDATE  ".PVDatabase::getApplicationsTableName()." SET app_parameters'".$parameters."' WHERE app_id='$app_id' ";
		}
		
		PVDatabase::query($query);
		self::_notify(get_class().'::'.__FUNCTION__, $app_id, $parameters);
	}//end setApplicationParameters
	
	/**
	 * Returns a non-initalized front-end object of an application.
	 * 
	 * @param string $application_unique_identifier The applications unique identifier, NOT THE ID
	 * 
	 * @return object $object The object of the application
	 * @access void
	 */
	public static function getAppObject($app_unique_name) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app_unique_name);
		
		$app_unique_name = self::_applyFilter( get_class(), __FUNCTION__ , $app_unique_name, array('event'=>'args'));
		$app_unique_name = PVDatabase::makeSafe($app_unique_name);
		
		$query="SELECT app_directory, app_file, app_object FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_unique_name' ";	
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		$app_directory=$row['app_directory'];
		$app_file=$row['app_file'];
		$app_object=trim($row['app_object']);
		
		include_once(PV_APPLICATIONS.DS.$app_directory.$app_file);
		
		if(!empty($app_object)) {
			self::_notify(get_class().'::'.__FUNCTION__, $app_object, $app_unique_name);
			$app_object  = self::_applyFilter( get_class(), __FUNCTION__ , $app_object , array('event'=>'return'));
		
			return $app_object;
		}
	}//end getAppObject
	
	/**
	 * Returns a non-initalized back-end object of an application.
	 * 
	 * @param string $application_unique_identifier The applications unique identifier, NOT THE ID
	 * 
	 * @return object $object The object of the application
	 * @access void
	 */
	public static function getAdminAppObject($app_unique_name) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app_unique_name);
	
		$app_unique_name = self::_applyFilter( get_class(), __FUNCTION__ , $app_unique_name, array('event'=>'args'));
		$app_unique_name = PVDatabase::makeSafe($app_unique_name);
		
		$site_config=PVConfiguration::getSiteGeneralConfiguration();
		
		$query="SELECT admin_directory, admin_file, admin_object, backend_app FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_unique_name' ";	
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		$app_directory=$row['admin_directory'];
		$app_file=$row['admin_file'];
		$app_object=trim($row['admin_object']);
		$backend_app=$row['backend_app'];

		if($backend_app==1){
			include_once(ROOT.DS.$site_config['admin_url'].$app_directory.$app_file);
		}
		else {
			include_once(ROOT.DS.$app_directory.$app_file);
		}
		
		if(!empty($app_object)) {
			self::_notify(get_class().'::'.__FUNCTION__, $app_object, $app_unique_name);
			$app_object  = self::_applyFilter( get_class(), __FUNCTION__ , $app_object , array('event'=>'return'));
			
			return $app_object;
		}
	}//end getAppObject
	
	/**
	 * Returns the application's id based up on the application unique identifer.
	 * 
	 * @param string $app_unique_name The unique name of the application
	 * 
	 * @return void
	 * @access public
	 */
	public static function getApplicationID($app_unique_name) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $app_unique_name);
			
		$app_unique_name = self::_applyFilter( get_class(), __FUNCTION__ , $app_unique_name, array('event'=>'args'));
		$app_unique_name = PVDatabase::makeSafe($app_unique_name);
		
		$query="SELECT app_id FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_unique_name' ";	
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		self::_notify(get_class().'::'.__FUNCTION__, $row, $app_unique_name);
		$row  = self::_applyFilter( get_class(), __FUNCTION__ , $row, array('event'=>'return'));
	
		return $row['app_id'];
	}//end getApplicationID
	
	private static function getApplicationDefaults() {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		$defaults=array(
			'app_id'=>0,
			'app_name'=>'',
			'app_file'=>'',
			'app_directory'=>'',
			'app_unique_id'=>'',
			'app_parameters'=>'',
			'enabled'=>0,
			'app_object'=>'',
			'jquery_libraries'=>'',
			'javascript_libraries'=>'',
			'prototype_libraries'=>'',
			'css_files'=>'',
			'show_admin'=>0,
			'admin_directory'=>'',
			'admin_file'=>'',
			'admin_object'=>'',
			'backend_app'=>0,
			'admin_jquery_libraries'=>'',
			'admin_javascript_libraries'=>'',
			'admin_mootools_libraries'=>'',
			'admin_prototype_libraries'=>'',
			'admin_css_files'=>'',
			'uninstall_file'=>'',
			'has_module'=>0,
			'show_main_section'=>0,
			'app_icon'=>'',
			'app_preferences'=>'',
			'is_application_editable'=>0,
			'app_license'=>'',
			'application_type'=>'',
			'application_language'=>'',
			'app_site'=>'',
			'app_author'=>''
		);
		
		$defaults  = self::_applyFilter( get_class(), __FUNCTION__ , $defaults, array('event'=>'return'));
		return $defaults;
	}
	
}//end class
	