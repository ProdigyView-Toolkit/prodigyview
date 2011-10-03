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
*THIS SOFTWARE IS PROVIDED BY ProdigyView LLC ``AS IS'' AND ANY EXPRESS OR IMPLIED
*WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
*FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL ProdigyView LLC OR
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

class PVModules extends PVStaticObject {
	
	public static function installModule($args){
		
		if(!empty($args) && is_array($args)){
			
			$args=PVDatabase::makeSafe($args);
			
			extract($args);
			
			$is_module_editable=ceil($is_module_editable);
			
			if(!PVValidator::isDouble($module_version) && !PVValidator::isInteger($module_version)){
				$plugin_version=0;
			}
			
			
			$query="SELECT * FROM ".PVDatabase::getModuleAdminTableName()." WHERE module_unique_id='$module_unique_id' AND module_app_identifier='$module_app_identifier'";
			
			$result = PVDatabase::query($query);
			
			if(PVDatabase::resultRowCount($result) <= 0){
				$query="INSERT INTO ".PVDatabase::getModuleAdminTableName()."(module_name, module_unique_id, module_app_identifier , module_directory, module_file, module_function, module_description, module_author, module_site, module_license, module_version, is_module_editable) VALUES('$module_name', '$module_unique_id', '$module_app_identifier' , '$module_directory' , '$module_file', '$module_function', '$module_description', '$module_author', '$module_site', '$module_license', '$module_version', '$is_module_editable')";
				
				PVDatabase::query($query);
				
				
			}//end no result count
			else{
				
				$query="UPDATE ".PVDatabase::getModuleAdminTableName()." SET module_name='$module_name', module_directory='$module_directory', module_file='$module_file', module_function='$module_function', module_description='$module_description', module_author='$module_author', module_site='$module_site', module_license='$module_license', module_version='$module_version', is_module_editable='$is_module_editable' WHERE  module_unique_id='$module_unique_id' AND module_app_identifier='$module_app_identifier' ";
				
				PVDatabase::query($query);
				
			}
			
		}//end if not empty and is array
		
	}//end installModule
	

	
		
	
	
	public static function getModuleAdmin($module_unique_id, $module_app_identifier){
		
		if(!empty($module_unique_id) && !empty($module_app_identifier)){
			
			$module_unique_id=PVDatabase::makeSafe($module_unique_id);
			$module_app_identifier=PVDatabase::makeSafe($module_app_identifier);
			
			$query="SELECT * FROM ".PVDatabase::getModuleAdminTableName()." WHERE module_unique_id='$module_unique_id' AND module_app_identifier='$module_app_identifier'";
			
			$result=PVDatabase::query($query);
			
			$row = PVDatabase::fetchArray($result);
			
			return $row;
			
		}//end ! empty
		
	}//getModuleAdmin
	
	public static function getModuleAdminList($args){
		
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			$args=PVDatabase::makeSafe($args);
			extract($args, EXTR_SKIP);
		
		
			$first=1;
			
			
			
				
			$WHERE_CLAUSE.='';
			
			if(!empty($module_name)){
					
				$module_id=trim($module_name);
				
				if($first==0 && ($module_name[0]!='+' && $module_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_name[0]=='+' || $module_name[0]==',') && $first==1 ){
					$module_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_name, 'module_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_unique_id)){
					
				$module_unique_id=trim($module_unique_id);
				
				if($first==0 && ($module_unique_id[0]!='+' && $module_unique_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_unique_id[0]=='+' || $module_unique_id[0]==',') && $first==1 ){
					$module_unique_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_unique_id, 'module_unique_id');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_app_identifier)){
					
				$module_app_identifier=trim($module_app_identifier);
				
				if($first==0 && ($module_app_identifier[0]!='+' && $module_app_identifier[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_app_identifier[0]=='+' || $module_app_identifier[0]==',') && $first==1 ){
					$module_app_identifier[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_app_identifier, 'module_app_identifier');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_directory)){
					
				$module_directory=trim($module_directory);
				
				if($first==0 && ($module_directory[0]!='+' && $module_directory[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_directory[0]=='+' || $module_directory[0]==',') && $first==1 ){
					$module_directory[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_directory, 'module_directory');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_file)){
					
				$module_file=trim($module_file);
				
				if($first==0 && ($module_file[0]!='+' && $module_file[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_file[0]=='+' || $module_file[0]==',') && $first==1 ){
					$module_file[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_file, 'module_file');
				
				$first=0;
			}//end not empty app_id
			
			
			
			if(!empty($module_function)){
					
				$module_function=trim($module_function);
				
				if($first==0 && ($module_function[0]!='+' && $module_function[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_function[0]=='+' || $module_function[0]==',') && $first==1 ){

					$module_function[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_function, 'module_function');
				
				$first=0;
			}//end not empty app_id
			
			
			
			if(!empty($module_params)){
					
				$module_params=trim($module_params);
				
				if($first==0 && ($module_params[0]!='+' && $module_params[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_params[0]=='+' || $module_params[0]==',') && $first==1 ){
					$module_params[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_params, 'module_params');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_description)){
					
				$module_description=trim($module_description);
				
				if($first==0 && ($module_description[0]!='+' && $module_description[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_description[0]=='+' || $module_description[0]==',') && $first==1 ){
					$module_description[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_description, 'module_description');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_author)){
					
				$module_author=trim($module_author);
				
				if($first==0 && ($module_author[0]!='+' && $module_author[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_author[0]=='+' || $module_author[0]==',') && $first==1 ){
					$module_author[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_author, 'module_author');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_license)){
					
				$module_license=trim($module_license);
				
				if($first==0 && ($module_license[0]!='+' && $module_license[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_license[0]=='+' || $module_license[0]==',') && $first==1 ){
					$module_license[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_license, 'module_license');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_site)){
					
				$module_site=trim($module_site);
				
				if($first==0 && ($module_site[0]!='+' && $module_site[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_site[0]=='+' || $module_site[0]==',') && $first==1 ){
					$module_site_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_site, 'module_site');
				
				$first=0;
			}//end not empty app_id		
			
			
			if(!empty($module_version)){
					
				$module_version=trim($module_version);
				
				if($first==0 && ($module_version[0]!='+' && $module_version[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_version[0]=='+' || $module_version[0]==',') && $first==1 ){
					$module_version[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_version, 'module_version');
				
				$first=0;
			}//end not empty app_id	
			
			
			if(!empty($is_module_editable)){
					
				$is_module_editable=trim($is_module_editable);
				
				if($first==0 && ($is_module_editable[0]!='+' && $is_module_editable[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($is_module_editable[0]=='+' || $is_module_editable[0]==',') && $first==1 ){
					$is_module_editable[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($is_module_editable, 'is_module_editable');
				
				$first=0;
			}//end not empty app_id	
			
		}//end if is array
		
		$JOINS='';
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=' '.$custom_where.' ';
		}
		
		
		if(!empty($custom_join)){
			$JOINS.=' '.$custom_join.' ';
		}
		
		if($join_apps==true){
			$JOINS.=' JOIN '.PVDatabase::getApplicationsTableName().' ON '.PVDatabase::getApplicationsTableName().'.app_unique_id='.PVDatabase::getModuleAdminTableName().'.module_app_identifier ';
		}
		
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
		
		if(!empty($group_by)){
			$WHERE_CLAUSE.="GROUP BY $group_by ";
		}
		
		$ORDER_BY=$args['order_by'];
		
		$LIMIT=$args['limit'];
		
		
		
		if(!empty($LIMIT)){
			$LIMIT=" limit $LIMIT ";
		}
		
		if(!empty($ORDER_BY)){
			$ORDER_BY="ORDER BY $ORDER_BY ";
		}
		
	
		$query="SELECT * FROM ".PVDatabase::getModuleAdminTableName()." $JOINS ".$WHERE_CLAUSE." $ORDER_BY $LIMIT ";
		
		$result=PVDatabase::query($query);
		
		$content_array=array();
	
		while ($row = PVDatabase::fetchArray($result)){
			array_push($content_array, $row);
    	}//end while
    	
    	$content_array=PVDatabase::formatData($content_array);
    	
		return $content_array;	
		
	}//end getModuleList
	
	public static function deleteModuleAdmin($module_unique_id, $module_app_identifier, $remove_modules=FALSE){
		
		$module_info=self::getModuleAdmin($module_unique_id, $module_app_identifier);
		
		if(!empty($module_info) && is_array($module_info)){
			
			extract($module_info);
			
			if(!empty($module_directory)){
				if(file_exists(PV_MODULES.$module_directory)){
					PVFileManager::deleteDirectory(PV_MODULES.$module_directory);	
				}
			}
			
			if(file_exists(PV_MODULES.$module_directory.$module_file)){
					unlink(PV_MODULES.$module_directory.$module_file);	
			}
			
			$query="DELETE FROM ".PVDatabase::getModuleAdminTableName()." WHERE module_unique_id='$module_unique_id' AND module_app_identifier='$module_app_identifier'";
			PVDatabase::query($query);
			
			if($remove_modules){
				$module_list=self::getModuleList(array('module_identifier'=>$module_unique_id));
				
				foreach($module_list as $key=>$value){
					self::deleteModule($value['module_id']);
				}
			}//end if removeModule
		}
		
	}//end 
	
	public static function createModule($args){
		
		if(is_array($args) && !empty($args)){
			
			$args=PVDatabase::makeSafe($args);
			
			$module_app=ceil($module_app);
			$module_ordering=ceil($module_ordering);
			$module_enabled=ceil($module_enabled);
			$show_module_title=ceil($show_module_title);
			$module_wrap=ceil($module_wrap);
			$module_parent=ceil($module_parent);
			$module_site_id=ceil($module_site_id);
			
			extract($args);
			
			$query="INSERT INTO ".PVDatabase::getModulesTableName()." ( module_name, module_alias, module_description, module_app, module_ordering, module_enabled, module_params, module_css, module_title, show_module_title, module_wrap, module_permissions, module_identifier, module_parent, module_site_id) VALUES ( '$module_name', '$module_alias', '$module_description', '$module_app', '$module_ordering', '$module_enabled', '$module_params', '$module_css', '$module_title', '$show_module_title', '$module_wrap', '$module_permissions', '$module_identifier', '$module_parent', '$module_site_id')";
		}//end $args
		
		$module_id=PVDatabase::return_last_insert_query($query, 'module_id', PVDatabase::getModulesTableName() );
		
		return $module_id;
		
	}//end createModule
	
	public static function getModuleList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			$args=PVDatabase::makeSafe($args);
			extract($args, EXTR_SKIP);
		}
		
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getModulesTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
			
			if(!empty($module_id)){
					
				$module_id=trim($module_id);
				
				if($first==0 && ($module_id[0]!='+' && $module_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_id[0]=='+' || $module_id[0]==',') && $first==1 ){
					$module_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_id, 'module_id');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_id)){
					
				$module_id=trim($module_id);
				
				if($first==0 && ($module_id[0]!='+' && $module_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_id[0]=='+' || $module_id[0]==',') && $first==1 ){
					$module_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_id, 'module_id');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_name)){
					
				$module_name=trim($module_name);
				
				if($first==0 && ($module_name[0]!='+' && $module_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_name[0]=='+' || $module_name[0]==',') && $first==1 ){
					$module_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_name, 'module_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_alias)){
					
				$module_alias=trim($module_alias);
				
				if($first==0 && ($module_alias[0]!='+' && $module_alias[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_alias[0]=='+' || $module_alias[0]==',') && $first==1 ){
					$module_alias[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_alias, 'module_alias');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_app)){
					
				$module_app=trim($module_app);
				
				if($first==0 && ($module_app[0]!='+' && $module_app[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_app[0]=='+' || $module_app[0]==',') && $first==1 ){
					$module_app[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_app, 'module_app');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_ordering)){
					
				$module_ordering=trim($module_ordering);
				
				if($first==0 && ($module_ordering[0]!='+' && $module_ordering[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_ordering[0]=='+' || $module_ordering[0]==',') && $first==1 ){
					$module_ordering[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_ordering, 'module_ordering');
				
				$first=0;
			}//end not empty app_id
			
			
			
			if(!empty($module_enabled)){
					
				$module_enabled=trim($module_enabled);
				
				if($first==0 && ($module_enabled[0]!='+' && $module_enabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_enabled[0]=='+' || $module_enabled[0]==',') && $first==1 ){
					$module_enabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_enabled, 'module_enabled');
				
				$first=0;
			}//end not empty app_id
			
			
			
			if(!empty($module_params)){
					
				$module_params=trim($module_params);
				
				if($first==0 && ($module_params[0]!='+' && $module_params[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_params[0]=='+' || $module_params[0]==',') && $first==1 ){
					$module_params[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_params, 'module_params');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_permissions)){
					
				$module_permissions=trim($module_permissions);
				
				if($first==0 && ($module_permissions[0]!='+' && $module_permissions[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_permissions[0]=='+' || $module_permissions[0]==',') && $first==1 ){
					$module_permissions[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_permissions, 'module_permissions');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_identifier)){
					
				$module_identifier=trim($module_identifier);
				
				if($first==0 && ($module_identifier[0]!='+' && $module_identifier[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_identifier[0]=='+' || $module_identifier[0]==',') && $first==1 ){
					$module_identifier[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_identifier, 'module_identifier');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_parent)){
					
				$module_parent=trim($module_parent);
				
				if($first==0 && ($module_parent[0]!='+' && $module_parent[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_parent[0]=='+' || $module_parent[0]==',') && $first==1 ){
					$module_parent[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_parent, 'module_parent');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($module_site_id)){
					
				$module_site_id=trim($module_site_id);
				
				if($first==0 && ($module_site_id[0]!='+' && $module_site_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_site_id[0]=='+' || $module_site_id[0]==',') && $first==1 ){
					$module_site_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_site_id, 'module_site_id');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($container_id)){
					
				$container_id=trim($container_id);
				
				if($first==0 && ($container_id[0]!='+' && $container_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_id[0]=='+' || $container_id[0]==',') && $first==1 ){
					$container_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_id, PVDatabase::getContainerModulesTableName().'.container_id');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($container_name)){
					
				$container_name=trim($container_name);
				
				if($first==0 && ($container_name[0]!='+' && $container_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_name[0]=='+' || $container_name[0]==',') && $first==1 ){
					$container_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_name, 'container_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($container_alias)){
					
				$container_alias=trim($container_alias);
				
				if($first==0 && ($container_alias[0]!='+' && $container_alias[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_alias[0]=='+' || $container_alias[0]==',') && $first==1 ){
					$container_alias[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_alias, 'container_alias');
				
				$first=0;
			}//end not empty app_id
			
			
			
			if(!empty($container_position)){
					
				$container_position=trim($container_position);
				
				if($first==0 && ($container_position[0]!='+' && $container_position[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_position[0]=='+' || $container_position[0]==',') && $first==1 ){
					$container_position[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_position, 'container_position');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($container_enabled)){
					
				$container_enabled=trim($container_enabled);
				
				if($first==0 && ($container_enabled[0]!='+' && $container_enabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_enabled[0]=='+' || $container_enabled[0]==',') && $first==1 ){
					$container_enabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_enabled, 'container_enabled');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($container_params)){
					
				$container_params=trim($container_params);
				
				if($first==0 && ($container_params[0]!='+' && $container_params[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_params[0]=='+' || $container_params[0]==',') && $first==1 ){
					$container_params[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_params, 'container_params');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($container_parent)){
					
				$container_params=trim($container_parent);
				
				if($first==0 && ($container_parent[0]!='+' && $container_parent[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_parent[0]=='+' || $container_parent[0]==',') && $first==1 ){
					$container_parent[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_parent, 'container_parent');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($container_site_id)){
					
				$container_site_id=trim($container_parent);
				
				if($first==0 && ($container_site_id[0]!='+' && $container_site_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_site_id[0]=='+' || $container_site_id[0]==',') && $first==1 ){
					$container_site_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_site_id, 'container_site_id');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($page_id)){
				
				$page_id=trim($page_id);
				
				if($first==0 && ($page_id[0]!='+' && $page_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_id[0]=='+' || $page_id[0]==',') && $first==1 ){
					$page_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_id, 'page_id');
				
				$first=0;
			}//end not empty app_id
			
			
			
			
			if(!empty($page_name)){
				
				$page_name=trim($page_name);
				
				if($first==0 && ($page_name[0]!='+' && $page_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_name[0]=='+' || $page_name[0]==',') && $first==1 ){
					$page_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_name, 'page_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($page_title)){
					
				$page_title=trim($page_title);
				
				if($first==0 && ($page_title[0]!='+' && $page_title[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_title[0]=='+' || $page_title[0]==',') && $first==1 ){
					$page_title[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_title, 'page_title');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($page_description)){
					
				$page_description=trim($page_description);
				
				if($first==0 && ($page_description[0]!='+' && $page_description[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_description[0]=='+' || $page_description[0]==',') && $first==1 ){
					$page_description[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_description, 'page_description');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($page_alias)){
					
				$page_alias=trim($page_alias);
				
				if($first==0 && ($page_alias[0]!='+' && $page_alias[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_alias[0]=='+' || $page_alias[0]==',') && $first==1 ){
					$page_alias[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_alias, 'page_alias');
				
				$first=0;
			}//end not empty app_id
			
			
			
			if(!empty($frontpage)){
					
				$frontpage=trim($frontpage);
				
				if($first==0 && ($frontpage[0]!='+' && $frontpage[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($frontpage[0]=='+' || $frontpage[0]==',') && $first==1 ){
					$frontpage[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($frontpage, 'frontpage');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($page_enabled)){
					
				$page_enabled=trim($page_enabled);
				
				if($first==0 && ($page_enabled[0]!='+' && $page_enabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_enabled[0]=='+' || $page_enabled[0]==',') && $first==1 ){
					$page_enabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_enabled, 'page_enabled');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($page_ordering)){
					
				$page_ordering=trim($page_ordering);
				
				if($first==0 && ($page_ordering[0]!='+' && $page_ordering[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_ordering[0]=='+' || $page_ordering[0]==',') && $first==1 ){
					$page_ordering[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_ordering, 'page_ordering');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($page_short_url)){
					
				$page_short_url=trim($page_short_url);
				
				if($first==0 && ($page_short_url[0]!='+' && $page_short_url[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_short_url[0]=='+' || $page_short_url[0]==',') && $first==1 ){
					$page_short_url[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_short_url, 'page_short_url');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($page_url)){
					
				$page_url=trim($page_url);
				
				if($first==0 && ($page_url[0]!='+' && $page_url[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_url[0]=='+' || $page_url[0]==',') && $first==1 ){
					$page_url[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_url, 'page_url');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($page_site_id)){
					
				$page_site_id=trim($page_site_id);
				
				if($first==0 && ($page_site_id[0]!='+' && $page_site_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_site_id[0]=='+' || $page_site_id[0]==',') && $first==1 ){
					$page_site_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_site_id, 'page_site_id');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($parent_page)){
					
				$parent_page=trim($parent_page);
				
				if($first==0 && ($parent_page[0]!='+' && $parent_page[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($parent_page[0]=='+' || $parent_page[0]==',') && $first==1 ){
					$parent_page[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($parent_page, 'parent_page');
				
				$first=0;
			}//end not empty app_id
			
		$JOINS='';
			
		if($join_container_modules || $join_containers ){
			$JOINS.=" JOIN ".PVDatabase::getContainerModulesTableName()." ON ".PVDatabase::getModulesTableName().".module_id = ".PVDatabase::getContainerModulesTableName().".module_id ";	
		}
		
		if( $join_containers){
			$JOINS.=" JOIN ".PVDatabase::getContainersTableName()." ON ".PVDatabase::getContainerModulesTableName().".container_id = ".PVDatabase::getContainersTableName().".container_id ";	
		}
		
		
		
		
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=' '.$custom_where.' ';
		}
		
		if($join_apps==true){
			$JOINS.=' JOIN '.PVDatabase::getApplicationsTableName().' ON '.PVDatabase::getModulesTableName().'.module_app='.PVDatabase::getApplicationsTableName().'.app_id ';
		}
		
		if($join_module_admin==true){
			$JOINS.=' JOIN '.PVDatabase::getModuleAdminTableName().' ON '.PVDatabase::getModulesTableName().'.module_identifier='.PVDatabase::getModuleAdminTableName().'.module_unique_id ';
		}
		
		if($join_page_modules==true){
			$JOINS.=' JOIN '.PVDatabase::getPageModuleRelationshipTableName().' ON '.PVDatabase::getPageModuleRelationshipTableName().'.module_id='.PVDatabase::getModulesTableName().'.module_id ';
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
		
    	$query="$prequery SELECT $PREFIX_ARGS $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
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
		
	}//end getModuleList
	
	
	public static function getModule($module_id){
		
		if(!empty($module_id)){
			
			$module_id=ceil($module_id);
			
			$query="SELECT * FROM ".PVDatabase::getModulesTableName()." WHERE module_id='$module_id'";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row;
			
		}//end page id
		
	}//end getModule
	
	
	public static function getModuleByAlias($module_id){
		
		if(!empty($module_id)){
			
			$module_id=PVDatabase::makeSafe($module_id);
			
			$query="SELECT * FROM ".PVDatabase::getModulesTableName()." WHERE module_alias='$module_id'";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
			
		}//end page id
		
	}//end getModule
	
	public static function updateModule($args){
		
		if(is_array($args) && !empty($args['module_id']) ){
			
			$args=PVDatabase::makeSafe($args);
			extract($args);
			
			$module_app=ceil($module_app);
			$module_ordering=ceil($module_ordering);
			$module_enabled=ceil($module_enabled);
			$show_module_title=ceil($show_module_title);
			$module_wrap=ceil($module_wrap);
			$module_parent=ceil($module_parent);
			$module_site_id=ceil($module_site_id);
			
			$module_id=ceil($module_id);
			
			$query="UPDATE ".PVDatabase::getModulesTableName()." SET module_name='$module_name', module_alias='$module_alias' , module_description='$module_description' , module_app='$module_app' , module_ordering='$module_ordering' , module_enabled='$module_enabled' , module_params='$module_params' , module_css='$module_css' , module_title='$module_title' , show_module_title='$show_module_title' , module_wrap='$module_wrap' , module_permissions='$module_permissions' , module_identifier='$module_identifier' , module_parent='$module_parent' , module_site_id='$module_site_id' WHERE module_id='$module_id' ";
			PVDatabase::query($query);
			
		}//end if
		
	}//end updateModule
	
	
	public static function deleteModule($module_id, $recursive=FALSE){
		
		if(!empty($module_id)){
			
			$module_id=ceil($module_id);
			
			$query="DELETE FROM ".PVDatabase::getModulesTableName()." WHERE module_id='$module_id'";
			PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getContainerModulesTableName()." WHERE module_id='$module_id'";
			PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getPageModuleRelationshipTableName()." WHERE module_id='$module_id'";
			PVDatabase::query($query);
			
			if($recursive==TRUE){
				$query="SELECT * FROM ".PVDatabase::getModulesTableName()." WHERE module_parent='$module_id'";
				
				
				$result=PVDatabase::query($query);
			
				while ($row = PVDatabase::fetchArray($result)){
					self::deletePage($row['module_id'], $recursive);
				}//end while
			}//recursive true
			
		}//end not empty page id
		
	}//end deletePage
		
}//end class
	