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

class PVContainers extends PVStaticObject {
	
	public static function createContainer($args){
		
		if(is_array($args) && !empty($args)){
			
			$args=PVDatabase::makeSafe($args);
			extract($args);
			
			$container_parent=ceil($container_parent);
			$container_enabled=ceil($container_enabled);
			$show_header=ceil($show_header);
			$container_wrap=ceil($container_wrap);
			$container_site_id=ceil($container_site_id);
			
			$query="INSERT INTO ".PVDatabase::getContainersTableName()."(container_name, container_description, container_alias, container_position, container_header, show_header, container_enabled, container_params, container_css_params, container_wrap, container_permissions, container_parent, container_site_id) VALUES ( '$container_name', '$container_description', '$container_alias', '$container_position', '$container_header', '$show_header', '$container_enabled', '$container_params' , '$container_css_params', '$container_wrap', '$container_permissions', '$container_parent', '$container_site_id') ";
			
			$container_id=PVDatabase::return_last_insert_query($query, 'container_id', PVDatabase::getContainersTableName());
			
			return $container_id;
			
		}//end if
	}//end createContainer
	
	public static function getContainerList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			$args=PVDatabase::makeSafe($args);
			extract($args, EXTR_SKIP);
		}
		
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getContainersTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
			
			if(!empty($container_id)){
					
				$container_id=trim($container_id);
				
				if($first==0 && ($container_id[0]!='+' && $container_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_id[0]=='+' || $container_id[0]==',') && $first==1 ){
					$container_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_id, 'container_id');
				
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
		
		if($join_page_containers || $join_pages){
			$JOINS.=" JOIN ".PVDatabase::getPageContainersRelationshipTableName()." ON ".PVDatabase::getPageContainersRelationshipTableName().".container_id = ".PVDatabase::getContainersTableName().".container_id ";	
		}
		
		if( $join_pages){
			$JOINS.=" JOIN ".PVDatabase::getPagesTableName()." ON ".PVDatabase::getPageContainersRelationshipTableName().".page_id = ".PVDatabase::getPagesTableName().".page_id ";	
		}
		
		if($join_container_modules || $join_modules ){
			$JOINS.=" JOIN ".PVDatabase::getContainerModulesTableName()." ON ".PVDatabase::getContainersTableName().".container_id = ".PVDatabase::getContainerModulesTableName().".container_id ";	
		}
		
		if( $join_modules){
			$JOINS.=" JOIN ".PVDatabase::getModulesTableName()." ON ".PVDatabase::getContainerModulesTableName().".module_id = ".PVDatabase::getModulesTableName().".module_id ";	
		}
		
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
			$page_results=PVDatabase::getPagininationOffset($table_name, $JOINS, $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);
			
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

		
	}//end getContainerList
	
	
	public static function getContainer($container_id){
		if(!empty($container_id)){
			
			$container_id=ceil($container_id);
			
			$query="SELECT * FROM ".PVDatabase::getContainersTableName()." WHERE container_id='$container_id'";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row;
			
		}//end page id
	}//end get page
	
	public static function getContainerByAlias($container_id){
		if(!empty($container_id)){
			
			$container_id=PVDatabase::makeSafe($container_id);
			
			$query="SELECT * FROM ".PVDatabase::getContainersTableName()." WHERE container_alias='$container_id'";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row;
			
		}//end page id
	}//end get page
	
	public static function updateContainer($args){
	
		if( is_array($args) && !empty($args['container_id'])){
				$args=PVDatabase::makeSafe($args);
				extract($args);
				
				$show_header=ceil($show_header);
				$container_enabled=ceil($container_enabled);
				$container_enabled=ceil($container_enabled);
				$container_parent=ceil($container_parent);
				$container_site_id=ceil($container_site_id);
				
				$query="UPDATE ".PVDatabase::getContainersTableName()." SET container_name='$container_name', container_description='$container_description', container_alias='$container_alias', container_position='$container_position', container_header='$container_header', show_header='$show_header', container_enabled='$container_enabled', container_params='$container_params', container_css_params='$container_css_params', container_wrap='$container_wrap', container_permissions='$container_permissions', container_parent='$container_parent', container_site_id='$container_site_id' WHERE container_id='$container_id' ";
				
				PVDatabase::query($query);
		}//end if is array
	}//end updateContainer
	
	
	public static function deleteContainer($container_id, $recursive=FALSE){
		
		if(!empty($container_id)){
			
			$container_id=ceil($container_id);
			
			$query="DELETE FROM ".PVDatabase::getContainerModulesTableName()." WHERE container_id='$container_id' ";
			PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getPageContainersRelationshipTableName()." WHERE container_id='$container_id' ";
			PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getContainersTableName()." WHERE container_id='$container_id' ";
			PVDatabase::query($query);
			
			if($recursive==TRUE){
				$query="SELECT * FROM ".PVDatabase::getContainersTableName()." WHERE container_parent='$container_id'";
				
				
				$result=PVDatabase::query($query);
			
				while ($row = PVDatabase::fetchArray($result)){
					self::deletePage($row['container_id'], $recursive);
				}//end while
			}//recursive true
			
		}//end not empty page id
		
	}//end deletePage
	
	public static function addContainerModuleRelationship($container_id, $module_id, $container_module_ordering=0, $container_module_enabled=0){
		
		if(!empty($container_id) && !empty($module_id)){
			
			$container_id=ceil($container_id);
			$module_id=ceil($module_id);
			$container_module_ordering=ceil($container_module_ordering);
			$container_module_enabled=ceil($container_module_enabled);
			
			$query="INSERT INTO ".PVDatabase::getContainerModulesTableName()."( container_id , module_id , container_module_ordering , container_module_enabled ) VALUES( '$container_id' , '$module_id' , '$container_module_ordering' , '$container_module_enabled' ) ";
			
			$container_module_id=PVDatabase::return_last_insert_query($query, 'container_module_id', PVDatabase::getContainerModulesTableName());
			
			return $container_module_id;
		}//end if not empty
		
	}//end addContainerModuleRelationship
	
	public static function getContainerModuleRelationshipList($args){
		
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args);
		}
		
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getContainerModulesTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
			
			if(!empty($container_id)){
				
				$container_id=trim($container_id);
				
				if($first==0 && ($container_id[0]!='+' && $container_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_id[0]=='+' || $container_id[0]==',') && $first==1 ){
					$container_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_id, 'container_id');
				
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
			
			
			if(!empty($container_module_ordering)){
					
				$container_module_ordering=trim($container_module_ordering);
				
				if($first==0 && ($container_module_ordering[0]!='+' && $container_module_ordering[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_module_ordering[0]=='+' || $container_module_ordering[0]==',') && $first==1 ){
					$container_module_ordering[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_module_ordering, 'container_module_ordering');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($container_module_enabled)){
					
				$container_module_enabled=trim($container_module_enabled);
				
				if($first==0 && ($container_module_enabled[0]!='+' && $container_module_enabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_module_enabled[0]=='+' || $container_module_enabled[0]==',') && $first==1 ){
					$container_module_enabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_module_enabled, 'container_module_enabled');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($container_module_id)){
					
				$container_module_id=trim($container_module_id);
				
				if($first==0 && ($container_module_id[0]!='+' && $container_module_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($container_module_id[0]=='+' || $container_module_id[0]==',') && $first==1 ){
					$container_module_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($container_module_id, 'container_module_id');
				
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
		
	}//end get relationship
	
	public static function getContainerModuleRelationship($container_module_id){
	
		if(!empty($container_module_id)){
			
			$container_module_id=ceil($container_module_id);
			
			$query="SELECT * FROM ".PVDatabase::getContainerModulesTableName()." WHERE  container_module_id='$container_module_id' ";
			
			$result=PVDatabase::query($query);
			
			$row = PVDatabase::fetchArray($result);
			
			return $row;
		}//end if
		
	}//end getContainerModuleRelationship
	
	
	public static function updateContainerModuleRelationship($args){
		
		if(is_array($args) && !empty($args['container_module_id'])){
			
			extract($args);
			
			$container_id=ceil($container_id);
			$module_id=ceil($module_id);
			$container_module_ordering=ceil($container_module_ordering);
			$container_module_enabled=ceil($container_module_enabled);
			$container_module_id=ceil($container_module_id);
			
			$query="UPDATE ".PVDatabase::getContainerModulesTableName()." SET container_id='$container_id', module_id='$module_id', container_module_ordering='$container_module_ordering' , container_module_enabled='$container_module_enabled' WHERE container_module_id='$container_module_id'";
			
			PVDatabase::query($query);
			
		}
	}//end updateContainerModuleRelations
	
	
	public static function deleteContainerModuleRelationship($container_module_id){
		
		if(!empty($container_module_id)){
			$container_module_id=ceil($container_module_id);
			
			$query="DELETE FROM ".PVDatabase::getContainerModulesTableName()." WHERE container_module_id='$container_module_id'";
			
			PVDatabase::query($query);
		}
	}//end deleteContainerModuleRelationship
		
}//end class
	