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
	
	private static $stored_app_objects;
	private static $stored_app_objects_admin;
	
	public static function pv_exec($app, $command, $params, $new_object=FALSE){
		
		if($new_object==FALSE && !is_object($app)){
			$temp_object=self::$stored_app_objects[$app];
			if(!empty($temp_object) && is_object($temp_object)){
				$app=$temp_object;
			}
		}
		
		if(is_object ($app)){
		
			return $app->commandInterpreter($command, $params);
		
		}
		else{
			if(PVValidator::isInteger($app)){
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
					PVLibraries::enqueue_jquery($name);
				}
			}//end for
			
			$array=preg_split('/;|,/', $row['javascript_libraries']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueue_javascript($name);
				}
			}//end for
			
			$array=preg_split('/;|,/', $row['prototype_libraries']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueue_prototype($name);
				}
			}//end for
			
			
			$array=preg_split('/;|,/', $row['motools_libraries']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueue_mootools($name);
				}
			}//end for
			$array=preg_split('/;|,/', $row['css_files']);
			$count = count($array);
			for ($i = 0; $i < $count; $i++) {
				$name=trim($array[$i]);
				if(!empty($name)){
					PVLibraries::enqueue_css($name);
				}
			}//end for
			
			include_once(PV_APPLICATIONS.$app_directory.$app_file);
			
			
			if(!empty($app_object)){
				$appObject=new $app_object;
				self::$stored_app_objects[$app]=$appObject;
				return $appObject->commandInterpreter($command, $params);
			}
		}//end else
	
	
	}//end pv_exec
	
	
	public static function pv_exec_admin($app, $command, $params, $new_object=FALSE){
		
		if($new_object==FALSE && !is_object($app)){
			$temp_object=self::$stored_app_objects_admin[$app];
			if(!empty($temp_object) && is_object($temp_object)){
				$app=$temp_object;
			}
		}
		
		
		if(PV_IS_ADMIN==true){
			
			if(is_object ($app)){
			
				return $app->commandInterpreter($command, $params);
			
			}
			else{
				if(PVValidator::isInteger($app)){
					$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app' ";
				}
				else{
					$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app' ";
				}
				
				$result=PVDatabase::query($query);
				$row=PVDatabase::fetchArray($result);
					
				$app_directory=$row['admin_dir'];
				$app_file=$row['admin_file'];
				$app_object=trim($row['admin_object']);
				
				
				$array=preg_split('/;|,/', $row['admin_jquery_libraries']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueue_jquery($name);
					}
				}//end for
				
				$array=preg_split('/;|,/', $row['admin_javascript_libraries']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueue_javascript($name);
					}
				}//end for
				
				$array=preg_split('/;|,/', $row['admin_prototype_libraries']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueue_prototype($name);
					}
				}//end for
				
				
				$array=preg_split('/;|,/', $row['admin_motools_libraries']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueue_mootools($name);
					}
				}//end for
				
				$array=preg_split('/;|,/', $row['admin_css_files']);
				$count = count($array);
				for ($i = 0; $i < $count; $i++) {
					$name=trim($array[$i]);
					if(!empty($name)){
						PVLibraries::enqueue_css($name);
					}
				}//end for
				
				include_once(PV_ADMIN_APPLICATIONS.$app_directory.$app_file);
				
				
				if(!empty($app_object)){
					$appObject=new $app_object;
					self::$stored_app_objects_admin[$app]=$appObject;
					return $appObject->commandInterpreter($command, $params);
				}
		}//end else
	
		}//end is admin
	
	}//end pv_exec
	
	public static function installApplication($args=array()){
		$args += self::getApplicationDefaults();
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
			$query="INSERT INTO ".PVDatabase::getApplicationsTableName()."(app_name , app_file , app_directory , app_unique_id , app_parameters , app_object , jquery_libraries , javascript_libraries , show_admin , admin_dir , admin_file , admin_object , backend_app , admin_jquery_libraries , admin_javascript_libraries , admin_motools_libraries , prototype_libraries , admin_prototype_libraries , app_default_page , app_version , app_description , motools_libraries , css_files , admin_css_files , uninstall_file , has_module, app_icon, app_preferences, is_application_editable, app_license, application_type, application_language, app_site, app_author ) VALUES('$app_name' , '$app_file' , '$app_directory' , '$app_unique_id' , '$app_paramters' , '$app_object' , '$jquery_libraries' , '$javascript_libraries' , '$show_admin' , '$admin_dir' , '$admin_file' , '$admin_object' , '$backend_app' , '$admin_jquery_libraries' , '$admin_javascript_libraries' , '$admin_motools_libraries' , '$prototype_libraries' , '$admin_prototype_libraries' , '$app_default_page' , '$app_version' , '$app_description' , '$motools_libraries' , '$css_files' , '$admin_css_files' , '$uninstall_file' , '$has_module', '$app_icon' , '$app_preferences', '$is_application_editable', '$app_license', '$application_type' , '$application_language', '$app_site', '$app_author') ";
			return $app_id=PVDatabase::return_last_insert_query($query, "app_id", PVDatabase::getApplicationsTableName());
		} else {
			$query="UPDATE ".PVDatabase::getApplicationsTableName()." SET app_name='$app_name' , app_file='$app_file' , app_directory='$app_directory' , app_unique_id='$app_unique_id' , app_object='$app_object' , jquery_libraries='$jquery_libraries' , javascript_libraries='$javascript_libraries' , show_admin='$show_admin' , admin_dir='$admin_dir' , admin_file='$admin_file' , admin_object='$admin_object' , backend_app='$backend_app' , admin_jquery_libraries='$admin_jquery_libraries' , admin_javascript_libraries='$admin_javascript_libraries' , admin_motools_libraries='$admin_motools_libraries' , prototype_libraries='$prototype_libraries' , admin_prototype_libraries='$admin_prototype_libraries' , app_default_page='$app_default_page' , app_version='$app_version' , app_description='$app_description' , motools_libraries='$motools_libraries' , css_files='$css_files' , admin_css_files='$admin_css_files' , uninstall_file='$uninstall_file' , has_module='$has_module', app_icon='$app_icon', app_preferences='$app_preferences', is_application_editable='$is_application_editable', app_license='$app_license', application_type='$application_type', application_language='$application_language', app_site='$app_site', app_author='$app_author' WHERE app_unique_id='$app_unique_id' ";
			PVDatabase::query($query);			
			return true;
		}//end else
	}//end installApplication
	
	public static function getApplication($app_id){
		
		if(!empty($app_id)){
			
			if(PVValidator::isID($app_id)){
				$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app_id'";
			} else {
				$app_id=PVDatabase::makeSafe($app_id);
				$query="SELECT * FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_id'";
			}
			
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row= PVDatabase::formatData($row);
			
			return $row;
		}
	}//end getApplicationInfo
	
	public static function getApplicationList($args=array()){
		$args += self::getApplicationDefaults();
		$args += self::_getSqlSearchDefaults();
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
		
		if(!empty($admin_dir)){
				
			$admin_dir=trim($admin_dir);
			
			if($first==0 && ($admin_dir[0]!='+' && $admin_dir[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_dir[0]=='+' || $admin_dir[0]==',') && $first==1 ){
				$admin_dir[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_dir, 'admin_dir');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($admin_dir)){
				
			$admin_dir=trim($admin_dir);
			
			if($first==0 && ($admin_dir[0]!='+' && $admin_dir[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_dir[0]=='+' || $admin_dir[0]==',') && $first==1 ){
				$admin_dir[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_dir, 'admin_dir');
			
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
		
		if(!empty($admin_motools_libraries)){
				
			$admin_motools_libraries=trim($admin_motools_libraries);
			
			if($first==0 && ($admin_motools_libraries[0]!='+' && $admin_motools_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($admin_motools_libraries[0]=='+' || $admin_motools_libraries[0]==',') && $first==1 ){
				$admin_motools_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_motools_libraries, 'admin_motools_libraries');
			
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
		
		if(!empty($motools_libraries)){
				
			$motools_libraries=trim($motools_libraries);
			
			if($first==0 && ($motools_libraries[0]!='+' && $motools_libraries[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($motools_libraries[0]=='+' || $motools_libraries[0]==',') && $first==1 ){
				$motools_libraries[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($motools_libraries, 'motools_libraries');
			
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
		
    	return $content_array;
	}
	
	public static function removeApplication($app_id, $options=array()){
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
				if(file_exists(PV_APPLICATIONS.$admin_dir)){
					PVFileManager::deleteDirectory(PV_APPLICATIONS.$admin_dir);
				}
			}
			
			if(file_exists(PV_APPLICATIONS.$admin_dir.$admin_file)){
				unlink(PV_APPLICATIONS.$admin_dir.$admin_file);	
			}
			
			$query="DELETE FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app_id' ";
			PVDatabase::query($query);
		}//end not epty
		
	}//end removeApplication
	
	
	public static function getApplicationParameters($app_id){
		$app_id=PVDatabase::makeSafe($app_id);
		
		if(PVValidator::isID($app_id)){
			$query="SELECT app_parameters FROM ".PVDatabase::getApplicationsTableName()." WHERE app_id='$app_id' ";
		}
		else{
			$query="SELECT app_parameters FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_id' ";
		}
		
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		return $row['app_parameters'];
	}//end getApplicationParameters
	
	public static function setApplicationParameters($app_id, $parameters){
		$app_id=PVDatabase::makeSafe($app_id);
		$parameters=PVDatabase::makeSafe($parameters);
		
		if(PVValidator::isID($app_id)){
			$query="UPDATE  ".PVDatabase::getApplicationsTableName()." SET app_parameters'".$parameters."' WHERE app_id='$app_id' ";
		}
		else{
			$query="UPDATE  ".PVDatabase::getApplicationsTableName()." SET app_parameters'".$parameters."' WHERE app_id='$app_id' ";
		}
		
		PVDatabase::query($query);
		
	}//end setApplicationParameters
	
	public static function getAppObject($app_unique_name){
		
		$query="SELECT app_directory, app_file, app_object FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_unique_name' ";	
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		$app_directory=$row['app_directory'];
		$app_file=$row['app_file'];
		$app_object=trim($row['app_object']);
		
		include_once(PV_APPLICATIONS.DS.$app_directory.$app_file);
		
		
		if(!empty($app_object)){
			return $app_object;
			
		}
	}//end getAppObject
	
	
	public static function getAdminAppObject($app_unique_name){
	
		$site_config=PVConfiguration::getSiteGeneralConfiguration();
		
		$query="SELECT admin_dir, admin_file, admin_object, backend_app FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_unique_name' ";	
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		$app_directory=$row['admin_dir'];
		$app_file=$row['admin_file'];
		$app_object=trim($row['admin_object']);
		$backend_app=$row['backend_app'];

		
		if($backend_app==1){
			include_once(ROOT.DS.$site_config['admin_url'].$app_directory.$app_file);
		}
		else {
			include_once(ROOT.DS.$app_directory.$app_file);
		}
		
		if(!empty($app_object)){
			return $app_object;
			
		}
	}//end getAppObject
	
	public static function getApplicationID($app_unique_name){
		
		$query="SELECT app_id FROM ".PVDatabase::getApplicationsTableName()." WHERE app_unique_id='$app_unique_name' ";	
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
	
		return $row['app_id'];
	
	}//end getApplicationID
	
	private static function getApplicationDefaults() {
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
			'admin_dir'=>'',
			'admin_file'=>'',
			'admin_object'=>'',
			'backend_app'=>0,
			'admin_jquery_libraries'=>'',
			'admin_javascript_libraries'=>'',
			'admin_motools_libraries'=>'',
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
		
		return $defaults;
	}
	
}//end class
	