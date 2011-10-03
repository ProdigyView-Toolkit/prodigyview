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
class PVSecurity extends PVStaticObject {
	
		static $version=0.8;
		static $uniqueName="pv_security";
		
	function PVSecurity() {
			
	}//end constructor
		
	/**
	 * Retrives the roles for users/
	 * 
	 * @return array user roles
	 */			
	public static function getUserRoles() {
			
		$role_array=array();
			
		$query="SELECT role_id, role_name FROM ".PVDatabase::getUserRolesTableName()." ORDER BY role_name";
		$result = PVDatabase::query($query);
			while ($row = PVDatabase::fetchArray($result)){
				$role_array[$row['role_id']]=$row['role_name'];
			}//end while

		return $role_array;
		
	}//end getUserRoles
		
	/**
	 * Checks the user access level of the user based occepeted roles.
	 * 
	 * @param id user_id: The is od the user whose roles will be cheked
	 * @param int access_levvel: The access level to check eagaint.
	 */
	public static function checkUserAccessLevel($user_id, $required_level) {
		
		if(!empty($user_id) ){
			$user_info=PVUsers::getUserInfo($user_id);
			
			if($user_info['user_access_level']>= $required_level){
				return 1;
			}
		}
		return 0;
	}//end checkUserAccessLevel
		
	/**
	 * Checks the user's permission based on the user roles. The roles should be
	 * present in the user_roles table. A user belong to mutiple rules so the function 
	 * will check the user roles.
	 * 
	 * @param array user_role: An array os user roles. Either IDs of the role or name
	 * the name of the role should be passed
	 * @param array allow_roles. Id
	 */	
	public static function checkUserPermission($user_role, $allowed_roles){
		
		if(empty($allowed_roles)){
				return 1;
		}
		$roles=PVUsers::getUserRolesList();
			
		//Put Roles Into Array for checking
		if(!is_array($allowed_roles)){
			$role_array=explode(',', $allowed_roles);
				
			//Convert nOn numeric roles to IDS if they exist
			foreach($role_array as $key=>$role){
				if(!PVValidator::isInteger($role)){
					$role_array[$key]=self::findRoleID($role, $roles);
				}
			}//end foreach
		}
			
		if(is_array($user_role)){
			//Make Sure each passed role is an ID
			foreach($user_role as $key=>$value) {
				if(!PVValidator::isInteger($value)){
					$user_role [$key]=self::findRoleID($value, $roles);
				}	
			}
				
			foreach($user_role as $key=>$value){
				$found=0;
				if(is_array($value)){
					if(in_array($value['role_id'],$role_array ))
						$found=1;
				} else {
					if(in_array($value,$role_array ))
						$found=1;
				}
			}//end foreach 
				
			return $found;
		} else {
			
			if(!PVValidator::isInteger($user_role ))
				$user_role=self::findRoleID($user_role , $roles);
				
			return in_array($user_role,$role_array );
				
		}//end else
		
	}//end checkUserPermissions
	
	/**
	 * Finds the first role by passing in an array of roles with IDs
	 * and the NAME of the role to be found.
	 * 
	 * @param stirng role: The name of the role to be passed
	 * @param array role_array: An arrray of roles with the role anme and ID
	 * 
	 * @return int role_id: The id of the role
	 */	
	private static function findRoleID($role, $role_array) {
			
		foreach($role_array as $roles) {
			if(in_array($role, $roles)){
				return $roles['role_id'];
			}
		}//end foreach
			return 0;
	}//end findRole
		
	/**
	 * Check the users allowed to have access to an application based upon their role.
	 * 
	 * @param int app id
	 * @param permission_name
	 * @param user_role
	 * 
	 * @return boolean allowed
	 */
	public static function checkApplicationUserPermission($app_id, $permission_name, $user_role=''){
			
		if(empty($user_role)){
			$user_role=PVUsers::getAssignedUserRoles(PVUsers::getUserID());
		}
		$allowed_roles=PVApplications::getApplicationPermissions($app_id, $permission_name);
		
		return self::checkUserPermission($user_role, $allowed_roles);
	}//end checkUserApplicationPermission
		
	/**
	 * Returns the allowed roles to an application that.
	 * 
	 * @param id app_id
	 * @param stirng permission_name
	 * 
	 * @return string permission_role
	 */
	public static function getApplicationPermissions($app_id, $permission_name){
			
		if(PVValidator::isID($app_id)){
			$app_id=PVDatabase::makeSafe($app_id);
			$query="SELECT permission_roles FROM ".PVDatabase::getApplicationPermissionsTableName()." WHERE app_id='$app_id' AND permission_unique_name='$permission_name'";
		} else {
			$app_info=PVApplication::getApplication($app_id);
			$app_info_id=$app_info['app_id'];
			$query="SELECT permission_roles FROM ".PVDatabase::getApplicationPermissionsTableName()." WHERE app_id='$app_info_id' AND permission_unique_name='$permission_name'";
		}
		$result = PVDatabase::query($query);
		$row=PVDatabase::fetchArray($result);
			
		return $row['permission_roles'];
				
	}//end get ApplicationPermissions
	
	/**
	 * Checks if a user has access to an application based upon their level of
	 * access.
	 * 
	 * @param id app_id
	 * @param string permission_name
	 * @param int user_access level
	 * 
	 * @return boolean allowed
	 */	
	public static function checkApplicationUserAccessLevel($app_id, $permission_name, $user_access_level=0){
			
		if(PVValidator::isID($app_id)){
			$app_id=ceil($app_id);
			$query="SELECT access_level FROM ".PVDatabase::getApplicationPermissionsTableName()." WHERE app_id='$app_id' AND permission_unique_name='$permission_name'";
		} else {
			$app_info=PVApplication::getApplication($app_id);
			$app_info_id=$app_info['app_id'];
			$query="SELECT access_level FROM ".PVDatabase::getApplicationPermissionsTableName()." WHERE app_id='$app_info_id' AND permission_unique_name='$permission_name'";
		}
			
		$result = PVDatabase::query($query);
		$row=PVDatabase::fetchArray($result);
			
		if($row['access_level']>=$user_access_level){
			return 1;	
		}
		
		return 0;		
	}//end checkApplicationUserAccessLevel
		
	/**
	 * Check if a user has access to a plugion based off the permission name and user role.
	 * 
	 * @param id plugin_id
	 * @param string permission_name
	 * @param string user_role
	 * 
	 * @return boolean allowed
	 */		
	public static function checkPluginUserPermission($plugin_id, $permission_name, $user_role=''){
			
		if(empty($user_role)){
			$user_role=PVUsers::getAssignedUserRoles(PVUsers::getUserID());
		}
			
		$allowed_roles=PVPlugins::getPluginPermissions($plugin_id, $permission_name);
			
		return self::checkUserPermission($user_role, $allowed_roles);
	}//end checkUserApplicationPermission
		
	/**
	 * Returns the user roles that a plugin will allow access too.
	 * 
	 * @param string  plugin_unique_id
	 * @param string permission_name
	 * 
	 * @return string allow_roles
	 */
	public static function getPluginPermissions($plugin_unique_id, $permission_name){
			
		if(PVValidator::isID($plugin_unique_id)){
			$plugin_info=PVPlugins::getPlugin($plugin_unique_id);
			$plugin_info_id=$plugin_info['plugin_unique_id'];
			$query="SELECT permission_roles FROM ".PVDatabase::getPluginPermissionsTableName()." WHERE plugin_unique_id='$plugin_info_id' AND permission_unique_name='$permission_name'";
		} else {
			$plugin_unique_id=PVDatabase::makeSafe($plugin_unique_id);
			$query="SELECT permission_roles FROM ".PVDatabase::getPluginPermissionsTableName()." WHERE plugin_unique_id='$plugin_unique_id' AND permission_unique_name='$permission_name'";	
		}
		$result = PVDatabase::query($query);
		$row=PVDatabase::fetchArray($result);
			
		return $row['permission_roles'];
	}//end get ApplicationPermissions
	
	/**
	 * Checks if a user is allowed to access this plugin based upon their permission role.
	 * 
	 * @param string plugin_unique_id
	 * @param string permission_name
	 * @param int user_access_level
	 * 
	 * @return boolean allowed
	 */	
	public static function checkPluginUserAccessLevel($plugin_unique_id, $permission_name, $user_access_level=0){
			
		if(PVValidator::isID($app_id)){
			$plugin_info=PVPlugins::getPlugin($plugin_unique_id);
			$plugin_info_id=$plugin_info['plugin_unique_id'];
			$query="SELECT permission_access_level FROM ".PVDatabase::getPluginPermissionsTableName()." WHERE plugin_unique_id='$plugin_info_id' AND permission_unique_name='$permission_name'";		
		} else {
			$plugin_unique_id=PVDatabase::makeSafe($plugin_unique_id);
			$query="SELECT permission_access_level FROM ".PVDatabase::getPluginPermissionsTableName()." WHERE plugin_unique_id='$plugin_unique_id' AND permission_unique_name='$permission_name'";		
		}
			
		$result = PVDatabase::query($query);
		$row=PVDatabase::fetchArray($result);
			
		if($row['permission_access_level']>=$user_access_level){
			return 1;	
		} else {
			return 0;	
		}
			
	}//end checkApplicationUserAccessLevel
		
	/**
	 * Checks a module
	 */	
	public static function checkModuleUserPermission($module_unique_id, $app_unique_id, $user_role=''){
			
		if(empty($user_role)){
			$user_role=PVUsers::getAssignedUserRoles(PVUsers::getUserID());
		}
			
		$allowed_roles=self::getModulePermissions($module_unique_id, $app_unique_id);
			
		return self::checkUserPermission($user_role, $allowed_roles);
	}//end checkUserApplicationPermission
		
	/**
	 * 
	 */	
	public static function getModulePermissions($module_unique_id, $app_unique_id, $permission_name){
			
		if(PVValidator::isID($module_unique_id)){
			$module_info=PVModules::getModuleAdmin($module_unique_id);
			$module_info_id=$plugin_info['module_unique_id'];
			$app_unique_id=PVDatabase::makeSafe($app_unique_id);
			$query="SELECT permission_roles FROM ".PVDatabase::getModulePermissionsTableName()." WHERE module_unique_id='$module_info_id' AND app_unique_id='$app_unique_id' AND permission_unique_name='$permission_name'";
		} else {
			$module_unique_id=PVDatabase::makeSafe($module_unique_id);
			$app_unique_id=PVDatabase::makeSafe($app_unique_id);
			$query="SELECT permission_roles FROM ".PVDatabase::getModulePermissionsTableName()." WHERE module_unique_id='$module_unique_id' AND app_unique_id='$app_unique_id' AND permission_unique_name='$permission_name'";		
		}
		$result = PVDatabase::query($query);
		$row=PVDatabase::fetchArray($result);
			
		return $row['permission_roles'];		
	}//end get ApplicationPermissions
	
		
	public static function checkModuleUserAccessLevel($module_unique_id, $app_unique_id, $permission_name, $user_access_level=0){
			
		if(PVValidator::isID($module_unique_id)){
			$module_info=PVModules::getModuleAdmin($module_unique_id, $app_unique_id);
			$app_unique_id=PVDatabase::makeSafe($app_unique_id);
			$module_info_id=$plugin_info['module_unique_id'];
			$query="SELECT permission_access_level FROM ".PVDatabase::getModulePermissionsTableName()." WHERE module_unique_id='$module_info_id' AND app_unique_id='$app_unique_id' AND permission_unique_name='$permission_name'";	
		} else {
			$module_unique_id=PVDatabase::makeSafe($module_unique_id);
			$app_unique_id=PVDatabase::makeSafe($app_unique_id);
			$query="SELECT permission_access_level FROM ".PVDatabase::getModulePermissionsTableName()." WHERE module_unique_id='$module_unique_id' AND  app_unique_id='$app_unique_id' AND permission_unique_name='$permission_name'";	
		}
			
		$result = PVDatabase::query($query);
		$row=PVDatabase::fetchArray($result);
			
		if($row['permission_access_level']>=$user_access_level){
			return 1;	
		}

		return 0;		
	}//end checkApplicationUserAccessLevel
		
		
	function createApplicationPermission($args){
			
		if(is_array($args) && !empty($args['app_id']) && !empty($args['permission_unique_name'])){
			$args=PVDatabase::makeSafe($args);
			extract($args);
				
			$permission_access_level=ceil($permission_access_level);
			$app_id=ceil($app_id);
				
			$query="SELECT * FROM ".PVDatabase::getApplicationPermissionsTableName()." WHERE app_id='$app_id' AND permission_unique_name='$permission_unique_name' ";
			$result=PVDatabase::query($query);
			$row=PVDatabase::fetchArray($result);
				
			if(empty($row)){
				$query="INSERT INTO ".PVDatabase::getApplicationPermissionsTableName()."(app_id, permission_unique_name, permission_display_name, permission_description, permission_roles, permission_access_level) VALUES('$app_id' , '$permission_unique_name' , '$permission_display_name', '$permission_description' , '$permission_roles' , '$permission_access_level') ";		
				return PVDatabase::return_last_insert_query($query, 'application_permission_id', PVDatabase::getApplicationPermissionsTableName());
			}
				
		}//end if !empty
	}//end addApplicationPermission
		
		
	function updateApplicationPermission($args){
			
		$application_permission_id=ceil($args['application_permission_id']);
			
		if(!empty($application_permission_id)){
			$args=PVDatabase::makeSafe($args);
			extract($args);
				
			$permission_access_level=ceil($permission_access_level);
			$app_id=ceil($app_id);
				
			$query="UPDATE ".PVDatabase::getApplicationPermissionsTableName()." SET app_id='$app_id', permission_unique_name='$permission_unique_name', permission_roles='$permission_roles', permission_access_level='$permission_access_level', permission_display_name='$permission_display_name', permission_description='$permission_description' WHERE application_permission_id='$application_permission_id' ";
			PVDatabase::query($query);		
		}
			
	}//end updateApplicationPermission
		
		
	function clearApplicationPermission($args){
			
		if(is_array($args) && !empty($args['app_id']) && !empty($args['permission_unique_name'])){
			$args=PVDatabase::makeSafe($args);
			extract($args);
				
			$permission_access_level=ceil($permission_access_level);
			$app_id=ceil($app_id);
				
			$query="UPDATE ".PVDatabase::getApplicationPermissionsTableName()." SET  permission_roles='', permission_access_level='' WHERE app_id='$app_id' AND permission_unique_name='$permission_unique_name' ";
			PVDatabase::query($query);			
		}//end if !empty
	}//end addApplicationPermission
		
		
	function deleteApplicationPermission($application_permission_id){
			
		$application_permission_id=ceil($application_permission_id);
			
		if(!empty($application_permission_id)){
			$query="DELETE FROM ".PVDatabase::getApplicationPermissionsTableName()." WHERE application_permission_id='$application_permission_id' ";
			PVDatabase::query($query);
		}		
	}//end updateApplicationPermission
		
		
	function setApplicationPermission($args){
			
		if(is_array($args) && !empty($args['app_id']) && !empty($args['permission_unique_name'])){
			$args=PVDatabase::makeSafe($args);
			extract($args);
				
			$permission_access_level=ceil($permission_access_level);
			$app_id=ceil($app_id);
				
			if(empty($args['application_permission_id'])){
				$query="SELECT * FROM ".PVDatabase::getApplicationPermissionsTableName()." WHERE app_id='$app_id' AND permission_unique_name='$permission_unique_name' ";
			} else {
				$application_permission_id=ceil($args['application_permission_id']);
				$query="SELECT * FROM ".PVDatabase::getApplicationPermissionsTableName()." WHERE application_permission_id='$application_permission_id' ";
			}
				
			$result=PVDatabase::query($query);
			$row=PVDatabase::fetchArray($result);
				
			if(empty($row)){
				$query="INSERT INTO ".PVDatabase::getApplicationPermissionsTableName()."(app_id, permission_unique_name, permission_display_name, permission_description, permission_roles, permission_access_level) VALUES('$app_id' , '$permission_unique_name' , '$permission_display_name', '$permission_description' , '$permission_roles' , '$permission_access_level') ";
				return PVDatabase::return_last_insert_query($query, 'application_permission_id', PVDatabase::getApplicationPermissionsTableName());
			} else {
					
				if(empty($application_permission_id)){
					$application_permission_id=ceil($row['application_permission_id']);
				}
					
				$query="UPDATE ".PVDatabase::getApplicationPermissionsTableName()." SET app_id='$app_id', permission_unique_name='$permission_unique_name', permission_roles='$permission_roles', permission_access_level='$permission_access_level' WHERE application_permission_id='$application_permission_id' ";
				PVDatabase::query($query);
				return $application_permission_id;
			}
		}//end if !empty
	}//end addApplicationPermission
		
	function getApplicationPermissionList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			extract($args, EXTR_SKIP);
		}
		
		$first=1;
			
		$content_array=array();
		$table_name=PVDatabase::getApplicationPermissionsTableName();
		$db_type=PVDatabase::getDatabaseType();
				
		$WHERE_CLAUSE.='';
			
		if(!empty($app_id)){
					
			$app_id=trim($app_id);
				
			if($first==0 && ($app_id[0]!='+' && $app_id[0]!=',' ) ){
				$WHERE_CLAUSE.=" AND ";
			} else if( ($app_id[0]=='+' || $app_id[0]==',') && $first==1 ){
				$app_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_id, 'app_id');
				
			$first=0;
		}//end not empty app_id
			
		if(!empty($permission_unique_name)){
					
			$permission_unique_name=trim($permission_unique_name);
				
			if($first==0 && ($mpermission_unique_name[0]!='+' && $permission_unique_name[0]!=',' ) ){
				$WHERE_CLAUSE.=" AND ";
			} else if( ($permission_unique_name[0]=='+' || $permission_unique_name[0]==',') && $first==1 ){
				$permission_unique_name[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_unique_name, 'permission_unique_name');
				
			$first=0;
		}//end not empty app_id
			
		if(!empty($permission_display_name)){
					
			$permission_display_name=trim($permission_display_name);
				
			if($first==0 && ($permission_display_name[0]!='+' && $permission_display_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
			} else if( ($permission_display_name[0]=='+' || $permission_display_name[0]==',') && $first==1 ){
				$permission_display_name[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_display_name, 'permission_display_name');
				
			$first=0;
		}//end not empty app_id
			
		if(!empty($permission_access_level)){
					
			$permission_access_level=trim($permission_access_level);
				
			if($first==0 && ($permission_access_level[0]!='+' && $permission_access_level[0]!=',' ) ){
				$WHERE_CLAUSE.=" AND ";
			} else if( ($permission_access_level[0]=='+' || $permission_access_level[0]==',') && $first==1 ){
				$permission_access_level[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_access_level, 'permission_access_level');
				
			$first=0;
		}//end not empty app_id
			
			
		if(!empty($permission_description)){
					
			$permission_description=trim($permission_description);
				
			if($first==0 && ($permission_description[0]!='+' && $permission_description[0]!=',' ) ){
				$WHERE_CLAUSE.=" AND ";
			} else if( ($permission_description[0]=='+' || $permission_description[0]==',') && $first==1 ){
				$permission_description[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_description, 'permission_description');
				
			$first=0;
		}//end not empty app_id
			
			
			if(!empty($permission_roles)){
					
				$permission_roles=trim($permission_roles);
				
				if($first==0 && ($permission_roles[0]!='+' && $permission_roles[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_roles[0]=='+' || $permission_roles[0]==',') && $first==1 ){
					$permission_roles[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_roles, 'permission_roles');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($permission_access_level)){
					
				$permission_access_level=trim($permission_access_level);
				
				if($first==0 && ($permission_access_level[0]!='+' && $permission_access_level[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_access_level[0]=='+' || $permission_access_level[0]==',') && $first==1 ){
					$permission_access_level[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_access_level, 'permission_access_level');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($application_permission_id)){
					
				$application_permission_id=trim($application_permission_id);
				
				if($first==0 && ($application_permission_id[0]!='+' && $application_permission_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($application_permission_id[0]=='+' || $application_permission_id[0]==',') && $first==1 ){
					$application_permission_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($application_permission_id, 'application_permission_id');
				
				$first=0;
			}//end not empty app_id
			
			
			
		$JOINS='';
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=' '.$custom_where.' ';
		}
		
		if($join_apps==true){
			$JOINS.=' JOIN '.PVDatabase::getApplicationsTableName().' ON '.PVDatabase::getApplicationPermissionsTableName().'.app_id='.PVDatabase::getApplicationsTableName().'.app_id ';
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
		
    	$query="$prequery SELECT $PREFIX_ARGS * FROM $table_name $JOINS $WHERE_CLAUSE";
    	
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
	
	}//end getPermissionList
	

	function createModulePermission($args){
			
		if(is_array($args) && !empty($args['module_unique_id']) && !empty($args['permission_unique_name'])){
			$args=PVDatabase::makeSafe($args);
			extract($args);
				
			$permission_access_level=ceil($permission_access_level);
				
			$query="SELECT * FROM ".PVDatabase::getModulePermissionsTableName()." WHERE 	app_unique_id='$app_unique_id' AND module_unique_id='$module_unique_id' AND permission_unique_name='$permission_unique_name' ";
			$result=PVDatabase::query($query);
			$row=PVDatabase::fetchArray($result);
				
			if(empty($row)){
				$query="INSERT INTO ".PVDatabase::getModulePermissionsTableName()."(module_unique_id,app_unique_id, permission_unique_name, permission_display_name, permission_description, permission_roles, permission_access_level) VALUES( '$module_unique_id' , '$app_unique_id' , '$permission_unique_name' , '$permission_display_name', '$permission_description' , '$permission_roles' , '$permission_access_level') ";
				return PVDatabase::return_last_insert_query($query, 'module_permission_id', PVDatabase::getModulePermissionsTableName());
			}
		}//end if !empty
	}//end addApplicationPermission
		
		
	function updateModulePermission($args){
			
		$module_permission_id=ceil($args['module_permission_id']);
			
		if(!empty($module_permission_id)){
			$args=PVDatabase::makeSafe($args);
			extract($args);
				
			$permission_access_level=ceil($permission_access_level);
				
			$query="UPDATE ".PVDatabase::getModulePermissionsTableName()." SET module_unique_id='$module_unique_id', permission_unique_name='$permission_unique_name', permission_roles='$permission_roles', permission_access_level='$permission_access_level', permission_display_name='$permission_display_name', permission_description='$permission_description', app_unique_id='$app_unique_id' WHERE module_permission_id='$module_permission_id' ";
			PVDatabase::query($query);		
		}	
	}//end updateApplicationPermission
		
		
	function clearModulePermission($args){
			
		if(is_array($args) && !empty($args['module_unique_id']) && !empty($args['permission_unique_name'])){
			$args=PVDatabase::makeSafe($args);
			extract($args);
				
			$permission_access_level=ceil($permission_access_level);
				
			$query="UPDATE ".PVDatabase::getModulePermissionsTableName()." SET  permission_roles='', permission_access_level='' WHERE module_unique_id='$module_unique_id' AND permission_unique_name='$permission_unique_name' ";
			PVDatabase::query($query);		
		}//end if !empty
	}//end addApplicationPermission
		
		
	function deleteModulePermission($module_permission_id){
			
		$module_permission_id=ceil($module_permission_id);
			
		if(!empty($module_permission_id)){
			$query="DELETE FROM ".PVDatabase::getModulePermissionsTableName()." WHERE module_permission_id='$module_permission_id' ";
			PVDatabase::query($query);
		}	
	}//end updateApplicationPermission
		
	function setModulePermission($args){
			
		
			if(is_array($args) && !empty($args['module_unique_id']) && !empty($args['permission_unique_name'])){
				$args=PVDatabase::makeSafe($args);
				extract($args);
				
				$permission_access_level=ceil($permission_access_level);
				$app_id=ceil($app_id);
				
				if(empty($args['module_permission_id'])){
					$query="SELECT * FROM ".PVDatabase::getModulePermissionsTableName()." WHERE 	app_unique_id='$app_unique_id' AND module_unique_id='$module_unique_id' AND permission_unique_name='$permission_unique_name' ";
				}
				else{
					$module_permission_id=ceil($args['module_permission_id']);
					$query="SELECT * FROM ".PVDatabase::getModulePermissionsTableName()." WHERE module_permission_id'$module_permission_id' ";
				}
				
				$result=PVDatabase::query($query);
				$row=PVDatabase::fetchArray($result);
				
				
				
				if(empty($row)){
					$query="INSERT INTO ".PVDatabase::getModulePermissionsTableName()."(module_unique_id,app_unique_id, permission_unique_name, permission_display_name, permission_description, permission_roles, permission_access_level) VALUES( '$module_unique_id' , '$app_unique_id' , '$permission_unique_name' , '$permission_display_name', '$permission_description' , '$permission_roles' , '$permission_access_level') ";
					return PVDatabase::return_last_insert_query($query, 'module_permission_id', PVDatabase::getModulePermissionsTableName());
				}
				else{
					
					if(empty($module_permission_id)){
						$module_permission_id=ceil($row['module_permission_id']);
					}
					
					$query="UPDATE ".PVDatabase::getModulePermissionsTableName()." SET  permission_unique_name='$permission_unique_name', permission_roles='$permission_roles', permission_access_level='$permission_access_level' WHERE module_permission_id='$module_permission_id' ";
					PVDatabase::query($query);
					return $application_permission_id;

				}
			}//end if !empty
		}//end addApplicationPermission
		
		function getModulePermissionList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			extract($args, EXTR_SKIP);
		}
		
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getModulePermissionsTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
			
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
			
			
			if(!empty($permission_unique_name)){
					
				$permission_unique_name=trim($permission_unique_name);
				
				if($first==0 && ($mpermission_unique_name[0]!='+' && $permission_unique_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_unique_name[0]=='+' || $permission_unique_name[0]==',') && $first==1 ){
					$permission_unique_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_unique_name, 'permission_unique_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_display_name)){
					
				$permission_display_name=trim($permission_display_name);
				
				if($first==0 && ($permission_display_name[0]!='+' && $permission_display_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_display_name[0]=='+' || $permission_display_name[0]==',') && $first==1 ){
					$permission_display_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_display_name, 'permission_display_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_access_level)){
					
				$permission_access_level=trim($permission_access_level);
				
				if($first==0 && ($permission_access_level[0]!='+' && $permission_access_level[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_access_level[0]=='+' || $permission_access_level[0]==',') && $first==1 ){
					$permission_access_level[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_access_level, 'permission_access_level');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_description)){
					
				$permission_description=trim($permission_description);
				
				if($first==0 && ($permission_description[0]!='+' && $permission_description[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_description[0]=='+' || $permission_description[0]==',') && $first==1 ){
					$permission_description[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_description, 'permission_description');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_roles)){
					
				$permission_roles=trim($permission_roles);
				
				if($first==0 && ($permission_roles[0]!='+' && $permission_roles[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_roles[0]=='+' || $permission_roles[0]==',') && $first==1 ){
					$permission_roles[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_roles, 'permission_roles');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($permission_access_level)){
					
				$permission_access_level=trim($permission_access_level);
				
				if($first==0 && ($permission_access_level[0]!='+' && $permission_access_level[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_access_level[0]=='+' || $permission_access_level[0]==',') && $first==1 ){
					$permission_access_level[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_access_level, 'permission_access_level');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($app_unique_id)){
					
				$app_unique_id=trim($app_unique_id);
				
				if($first==0 && ($app_unique_id[0]!='+' && $app_unique_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($app_unique_id[0]=='+' || $app_unique_id[0]==',') && $first==1 ){
					$app_unique_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_unique_id, 'app_unique_id');
				
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
			
			
			if(!empty($module_permission_id)){
					
				$module_permission_id=trim($module_permission_id);
				
				if($first==0 && ($module_permission_id[0]!='+' && $module_permission_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($module_permission_id[0]=='+' || $module_permission_id[0]==',') && $first==1 ){
					$module_permission_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($module_permission_id, 'module_permission_id');
				
				$first=0;
			}//end not empty app_id
			
			
			
		$JOINS='';
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=' '.$custom_where.' ';
		}
		
		if($join_apps==true){
			$JOINS.=' JOIN '.PVDatabase::getApplicationsTableName().' ON '.PVDatabase::getModulePermissionsTableName().'.app_unique_id='.PVDatabase::getApplicationsTableName().'.app_unique_id ';
		}
		
		if($join_modules==true){
			$JOINS.=' JOIN '.PVDatabase::getModulesTableName().' ON '.PVDatabase::getModulePermissionsTableName().'.module_unique_id='.PVDatabase::getModulesTableName().'.module_identifier ';
		}
		
		if($join_admin==true){
			$JOINS.=' JOIN '.PVDatabase::getModuleAdminTableName().' ON '.PVDatabase::getModulePermissionsTableName().'.module_unique_id='.PVDatabase::getModuleAdminTableName().'.module_unique_id ';
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
		
    	$query="$prequery SELECT $PREFIX_ARGS * FROM $table_name $JOINS $WHERE_CLAUSE";
    	
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
	
	}//end getPermissionList
	
	
	
	
	function createPluginPermission($args){
			
		
			if(is_array($args) && !empty($args['plugin_unique_id']) && !empty($args['permission_unique_name'])){
				$args=PVDatabase::makeSafe($args);
				extract($args);
				
				$permission_access_level=ceil($permission_access_level);
				
				$query="SELECT * FROM ".PVDatabase::getPluginPermissionsTableName()." WHERE plugin_unique_id='$plugin_unique_id' AND permission_unique_name='$permission_unique_name' ";
				$result=PVDatabase::query($query);
				$row=PVDatabase::fetchArray($result);
				
				if(empty($row)){
					$query="INSERT INTO ".PVDatabase::getPluginPermissionsTableName()."(plugin_unique_id , permission_unique_name, permission_display_name, permission_description, permission_roles, permission_access_level) VALUES('$plugin_unique_id' , '$permission_unique_name' , '$permission_display_name', '$permission_description' , '$permission_roles' , '$permission_access_level') ";
					return PVDatabase::return_last_insert_query($query, 'plugin_permission_id', PVDatabase::getPluginPermissionsTableName());
				}
				
				
			}//end if !empty
		}//end addApplicationPermission
		
		
		function updatePluginPermission($args){
			
			$plugin_permission_id=ceil($args['plugin_permission_id']);
			
			if(!empty($plugin_permission_id)){
				$args=PVDatabase::makeSafe($args);
				extract($args);
				
				$permission_access_level=ceil($permission_access_level);
				
				$query="UPDATE ".PVDatabase::getPluginPermissionsTableName()." SET plugin_unique_id='$plugin_unique_id', permission_unique_name='$permission_unique_name', permission_roles='$permission_roles', permission_access_level='$permission_access_level', permission_display_name='$permission_display_name', permission_description='$permission_description' WHERE plugin_permission_id='$plugin_permission_id' ";
				PVDatabase::query($query);
				
			}
			
		}//end updateApplicationPermission
		
		
		function clearPluginPermission($args){
			
			if(is_array($args) && !empty($args['plugin_unique_id']) && !empty($args['permission_unique_name'])){
				$args=PVDatabase::makeSafe($args);
				extract($args);
				
				$permission_access_level=ceil($permission_access_level);
				
				$query="UPDATE ".PVDatabase::getPluginPermissionsTableName()." SET  permission_roles='', permission_access_level='' WHERE plugin_unique_id='$plugin_unique_id' AND permission_unique_name='$permission_unique_name' ";
				PVDatabase::query($query);
				
			}//end if !empty
		}//end addApplicationPermission
		
		
		function deletePluginPermission($plugin_permission_id){
			
			$plugin_permission_id=ceil($plugin_permission_id);
			
			if(!empty($plugin_permission_id)){
				$query="DELETE FROM ".PVDatabase::getPluginPermissionsTableName()." WHERE plugin_permission_id='$plugin_permission_id' ";
				PVDatabase::query($query);
			}
			
		}//end updateApplicationPermission
		




	function setPluginPermission($args){
			
		
			if(is_array($args) && !empty($args['plugin_unique_id']) && !empty($args['permission_unique_name'])){
				$args=PVDatabase::makeSafe($args);
				extract($args);
				
				$permission_access_level=ceil($permission_access_level);
				$app_id=ceil($app_id);
				
				if(empty($args['plugin_permission_id'])){
					$query="SELECT * FROM ".PVDatabase::getPluginPermissionsTableName()." WHERE plugin_unique_id='$plugin_unique_id' AND permission_unique_name='$permission_unique_name' ";
				}
				else{
					$plugin_permission_id=ceil($args['plugin_permission_id']);
					$query="SELECT * FROM ".PVDatabase::getPluginPermissionsTableName()." WHERE plugin_permission_id='$plugin_permission_id' ";
				}
				
				$result=PVDatabase::query($query);
				$row=PVDatabase::fetchArray($result);
				
				if(empty($row)){
					$query="INSERT INTO ".PVDatabase::getPluginPermissionsTableName()."(plugin_unique_id , permission_unique_name, permission_display_name, permission_description, permission_roles, permission_access_level) VALUES('$plugin_unique_id' , '$permission_unique_name' , '$permission_display_name', '$permission_description' , '$permission_roles' , '$permission_access_level') ";
					return PVDatabase::return_last_insert_query($query, 'plugin_permission_id', PVDatabase::getPluginPermissionsTableName());
				}
				else{
					
					if(empty($plugin_permission_id)){
						$plugin_permission_id=ceil($row['plugin_permission_id']);
					}
					
					$query="UPDATE ".PVDatabase::getPluginPermissionsTableName()." SET plugin_unique_id='$plugin_unique_id', permission_unique_name='$permission_unique_name', permission_roles='$permission_roles', permission_access_level='$permission_access_level' WHERE plugin_permission_id='$plugin_permission_id' ";
					PVDatabase::query($query);
					return $plugin_permission_id;

				}
			}//end if !empty
		}//end addApplicationPermission
		
		function getPluginPermissionList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			extract($args, EXTR_SKIP);
		}
		
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getPluginPermissionsTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
			
			if(!empty($plugin_unique_id)){
					
				$plugin_unique_id=trim($plugin_unique_id);
				
				if($first==0 && ($plugin_unique_id[0]!='+' && $plugin_unique_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($plugin_unique_id[0]=='+' || $plugin_unique_id[0]==',') && $first==1 ){
					$plugin_unique_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($plugin_unique_id, 'plugin_unique_id');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_unique_name)){
					
				$permission_unique_name=trim($permission_unique_name);
				
				if($first==0 && ($mpermission_unique_name[0]!='+' && $permission_unique_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_unique_name[0]=='+' || $permission_unique_name[0]==',') && $first==1 ){
					$permission_unique_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_unique_name, 'permission_unique_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_display_name)){
					
				$permission_display_name=trim($permission_display_name);
				
				if($first==0 && ($permission_display_name[0]!='+' && $permission_display_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_display_name[0]=='+' || $permission_display_name[0]==',') && $first==1 ){
					$permission_display_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_display_name, 'permission_display_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_access_level)){
					
				$permission_access_level=trim($permission_access_level);
				
				if($first==0 && ($permission_access_level[0]!='+' && $permission_access_level[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_access_level[0]=='+' || $permission_access_level[0]==',') && $first==1 ){
					$permission_access_level[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_access_level, 'permission_access_level');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_description)){
					
				$permission_description=trim($permission_description);
				
				if($first==0 && ($permission_description[0]!='+' && $permission_description[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_description[0]=='+' || $permission_description[0]==',') && $first==1 ){
					$permission_description[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_description, 'permission_description');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_roles)){
					
				$permission_roles=trim($permission_roles);
				
				if($first==0 && ($permission_roles[0]!='+' && $permission_roles[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_roles[0]=='+' || $permission_roles[0]==',') && $first==1 ){
					$permission_roles[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_roles, 'permission_roles');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($permission_access_level)){
					
				$permission_access_level=trim($permission_access_level);
				
				if($first==0 && ($permission_access_level[0]!='+' && $permission_access_level[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_access_level[0]=='+' || $permission_access_level[0]==',') && $first==1 ){
					$permission_access_level[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_access_level, 'permission_access_level');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($plugin_permission_id)){
					
				$plugin_permission_id=trim($plugin_permission_id);
				
				if($first==0 && ($plugin_permission_id[0]!='+' && $plugin_permission_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($plugin_permission_id[0]=='+' || $plugin_permission_id[0]==',') && $first==1 ){
					$plugin_permission_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($plugin_permission_id, 'plugin_permission_id');
				
				$first=0;
			}//end not empty app_id
			
			
			
		$JOINS='';
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=' '.$custom_where.' ';
		}
		
		if($join_plugins==true){
			$JOINS.=' JOIN '.PVDatabase::getPluginsTableName().' ON '.PVDatabase::getPluginPermissionsTableName().'.plugin_unique_id='.PVDatabase::getPluginsTableName().'.plugin_unique_name  ';
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
		
    	$query="$prequery SELECT $PREFIX_ARGS * FROM $table_name $JOINS $WHERE_CLAUSE";
    	
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
	
	}//end getPermissionList
	
	
	function createUserPermission($args){
		if(is_array($args) && !empty($args['permission_unique_name'])){
			
			extract($args);
			
			$app_id=ceil($app_id);
			
			$check=self::getUserPermissionList(array('permission_unique_name'=>$permission_unique_name, 'app_id'=>$app_id));
			
			if(empty($check)){
			
			$query="INSERT INTO ".PVDatabase::getApplicationPermissionsTableName()."( app_id, permission_unique_name, permission_display_name , permission_description , permission_roles) VALUES( '$app_id' , '$permission_unique_name' , '$permission_display_name' , '$permission_description' , '$permission_roles' )";
			
			PVDatabase::query($query);
			
			}
		}
	}//end permission
	
	function getUserPermissionList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			extract($args, EXTR_SKIP);
		}
		
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getApplicationPermissionsTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
			
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
			
			
			if(!empty($permission_unique_name)){
					
				$permission_unique_name=trim($permission_unique_name);
				
				if($first==0 && ($mpermission_unique_name[0]!='+' && $permission_unique_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_unique_name[0]=='+' || $permission_unique_name[0]==',') && $first==1 ){
					$permission_unique_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_unique_name, 'permission_unique_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_display_name)){
					
				$permission_display_name=trim($permission_display_name);
				
				if($first==0 && ($permission_display_name[0]!='+' && $permission_display_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_display_name[0]=='+' || $permission_display_name[0]==',') && $first==1 ){
					$permission_display_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_display_name, 'permission_display_name');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_description)){
					
				$permission_description=trim($permission_description);
				
				if($first==0 && ($permission_description[0]!='+' && $permission_description[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_description[0]=='+' || $permission_description[0]==',') && $first==1 ){
					$permission_description[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_description, 'permission_description');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($permission_roles)){
					
				$permission_roles=trim($permission_roles);
				
				if($first==0 && ($permission_roles[0]!='+' && $permission_roles[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($permission_roles[0]=='+' || $permission_roles[0]==',') && $first==1 ){
					$permission_roles[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($permission_roles, 'permission_roles');
				
				$first=0;
			}//end not empty app_id
			
			
			
		$JOINS='';
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=' '.$custom_where.' ';
		}
		
		if($join_apps==true){
			$JOINS.=' JOIN '.PVDatabase::getApplicationsTableName().' ON '.PVDatabase::getApplicationPermissionsTableName().'.app_id='.PVDatabase::getApplicationsTableName().'.app_id ';
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
		
    	$query="$prequery SELECT $PREFIX_ARGS * FROM $table_name $JOINS $WHERE_CLAUSE";
    	
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
	
	}//end getPermissionList
	
	function getUserPermission($permission_unique_name, $app_id=0){
		
	}
	
	function updateUserPermission($args){
		
		if(is_array($args)){
			
		}
		
	}
	
	function updatePermissionByApplication($args){
		
		if(is_array($args) && !empty($args['app_id']) && !empty($args['permission_unique_name']) ){
			
		}
		
	}//end updatePermissionByApplication
	
	function updatePermissionRoleByApplication($args){
		
		if(is_array($args) && !empty($args['app_id']) && !empty($args['permission_unique_name']) ){
			
			$args=PVDatabase::makeSafe($args);
			extract($args);
			$access_level=ceil($access_level);
			
			$query="UPDATE ".PVDatabase::getApplicationPermissionsTableName()." SET permission_roles='$permission_roles', access_level='$access_level' WHERE app_id='$app_id' AND permission_unique_name='$permission_unique_name' ";
			PVDatabase::query($query);
			
		}//end if
		
	}//end updatePermissionByApplication

	
	
	function deleteUserPermission($permission_unique_name, $app_id=0){
		
	}

	
}//end class

?>
