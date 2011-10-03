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

class PVPages extends PVStaticObject{
	
	public static function getPageURLByID($page_id){
		
		if(!empty($page_id)){
			
			$query="SELECT page_url FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row['page_short_url'];
		
		}
	
	}//end get pagePageNameByID
	
	
	public static function getPageShortURLByID($page_id){
		
		if(!empty($page_id)){
			
			$query="SELECT page_short_url FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row['page_short_url'];
		
		}
	
	}//end get pagePageNameByID
	
	public static function getPages(){
	
		$page_array=array();
		
		$query="SELECT page_name, page_id FROM ".PVDatabase::getPagesTableName()." ORDER BY page_name";
		$result=PVDatabase::query($query);
		
		while($row = PVDatabase::fetchArray($result)){
			$page_array[$row['page_id']]=$row['page_name'];
		}//end while
		
		return $page_array;
	
	}//end
	
	
	public static function getPageAliasByID($page_id){
		
		$query="SELECT page_alias FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		return $row['page_alias'];
	}
	
	public static function createPage($args=array()){
		$args += self::getPageDefaults();
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$page_enabled=ceil($page_enabled);
		$page_ordering=ceil($page_ordering);
		$frontpage=ceil($frontpage);
		
		$query="INSERT INTO ".PVDatabase::getPagesTableName()." (page_name, page_title, page_description, page_alias, frontpage, page_enabled, page_ordering, page_short_url, page_params, page_permissions, parent_page, page_site_id) VALUES ('$page_name', '$page_title', '$page_description', '$page_alias', '$frontpage', '$page_enabled', '$page_ordering', '$page_short_url', '$page_params', '$page_permissions', '$parent_page', '$page_site_id' )";
		$page_id=PVDatabase::return_last_insert_query($query, 'page_id', PVDatabase::getPagesTableName());
		return $page_id;
	}//end createPage
	
	public static function getPageList($args=array()){
		$args += self::getPageDefaults();
		$args += self::_getSqlSearchDefaults();
		$custom_where=$args['custom_where'];
		$custom_join=$args['custom_join'];
		$custom_select=$args['custom_select'];
		$args= PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);
		
		$first=1;
		
		$content_array=array();
		$table_name=PVDatabase::getPagesTableName();
		$db_type=PVDatabase::getDatabaseType();
			
		$WHERE_CLAUSE='';
		
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
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=" $custom_where ";
		}
		
		if(!empty($custom_join)){
			$JOINS.=" $custom_join ";
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
		
	}//end getPageList
	
	public static function getPage($page_id){
		if(!empty($page_id)){
			
			$page_id=ceil($page_id);
			
			$query="SELECT * FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			return $row;
			
		}//end page id
	}//end get page
	
	public static function getPageByAlias($page_id){
		if(!empty($page_id)){
			
			$page_id=PVDatabase::makeSafe($page_id);
			
			$query="SELECT * FROM ".PVDatabase::getPagesTableName()." WHERE page_alias='$page_id'";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row;
			
		}//end page id
	}//end get page
	
	
	public static function getPageByUrl($page_id){
		if(!empty($page_id)){
			
			$page_id=PVDatabase::makeSafe($page_id);
			
			$query="SELECT * FROM ".PVDatabase::getPagesTableName()." WHERE page_short_url='$page_id'";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row;
			
		}//end page id
	}//end get page
	
	public static function updatePage($args=array()) {
		$args += self::getPageDefaults();
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$page_enabled=ceil($page_enabled);
		$page_ordering=ceil($page_ordering);
		$frontpage=ceil($frontpage);
			
		$query="UPDATE ".PVDatabase::getPagesTableName()." SET page_name='$page_name', page_title='$page_title', page_description='$page_description', page_alias='$page_alias', frontpage='$frontpage', page_enabled='$page_enabled', page_ordering='$page_ordering', page_short_url='$page_short_url', page_params='$page_params', page_permissions='$	page_permissions', page_site_id='$page_site_id', parent_page='$parent_page' WHERE page_id='$page_id'";
		PVDatabase::query($query);
	}//end updatePage
	
	public static function deletePage($page_id, $recursive=FALSE){
		
		if(!empty($page_id)){
			
			$page_id=ceil($page_id);
			
			$query="DELETE FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
			PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getPageContainersRelationshipTableName()." WHERE page_id='$page_id'";
			PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getPageModuleRelationshipTableName()." WHERE page_id='$page_id'";
			PVDatabase::query($query);
			
			if($recursive==TRUE){
				$query="SELECT * FROM ".PVDatabase::getPagesTableName()." WHERE parent_page='$page_id'";
				$result=PVDatabase::query($query);
			
				while ($row = PVDatabase::fetchArray($result)){
					self::deletePage($row['page_id'], $recursive);
				}//end while
			}//recursive true
			
		}//end not empty page id
		
	}//end deletePage
	
	
	public static function addPageContainerRelationship($page_id, $container_id, $page_container_order=0, $page_container_enabled=0  ){
		
		if(!empty($page_id) && !empty($container_id)){
			
			$page_id=ceil($page_id);
			$container_id=ceil($container_id);
			$page_container_order=ceil($page_container_order);
			$page_container_enabled=ceil($page_container_enabled);
			
			$query="INSERT INTO ".PVDatabase::getPageContainersRelationshipTableName()."( page_id , container_id , page_container_ordering , page_container_enabled) VALUES( '$page_id' , '$container_id' , '$page_container_ordering' , '$page_container_enabled') ";
			$page_container_id=PVDatabase::return_last_insert_query($query,'page_container_id', PVDatabase::getPageContainersRelationshipTableName());
			
			return $page_container_id;
		}
		
	}//end addPageContainerRelationship
	
	public static function getPageContainerRelationshipList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			$args=PVDatabase::makeSafe($args);
			extract($args, EXTR_SKIP);
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getPageContainersRelationshipTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
			
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
			
			if(!empty($page_container_ordering)){
					
				$page_container_ordering=trim($page_container_ordering);
				
				if($first==0 && ($page_container_ordering[0]!='+' && $page_container_ordering[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_container_ordering[0]=='+' || $page_container_ordering[0]==',') && $first==1 ){
					$page_container_ordering[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_container_ordering, 'page_container_ordering');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($page_container_enabled)){
					
				$page_container_enabled=trim($page_container_enabled);
				
				if($first==0 && ($page_container_enabled[0]!='+' && $page_container_enabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_container_enabled[0]=='+' || $page_container_enabled[0]==',') && $first==1 ){
					$page_container_enabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_container_enabled, 'page_container_enabled');
				
				$first=0;
			}//end not empty app_id
			
			if(!empty($page_container_id)){
					
				$page_container_id=trim($page_container_id);
				
				if($first==0 && ($page_container_id[0]!='+' && $page_container_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_container_id[0]=='+' || $page_container_id[0]==',') && $first==1 ){
					$page_container_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_container_id, 'page_container_id');
				
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
		
		if($join_containers==true){
			$JOINS.=' JOIN '.PVDatabase::getContainersTableName().' ON '.PVDatabase::getContainersTableName().'.container_id='.PVDatabase::getPageContainersRelationshipTableName().'.container_id ';
		}
		
		if($join_pages==true){
			$JOINS.=' JOIN '.PVDatabase::getPagesTableName().' ON '.PVDatabase::getPagesTableName().'.container_id='.PVDatabase::getPageContainersRelationshipTableName().'.page_id ';
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
	}//end getPageContainerList
	
	public static function getPageContainerRelationship($page_container_id){
		if(!empty($page_container_id)){
			$page_container_id=ceil($page_container_id);
			$query="SELECT * FROM ".PVDatabase::getPageContainersRelationshipTableName()." WHERE page_container_id='$page_container_id' ";
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row;
		}
	}//end getPageContainerRelationship
	
	public static function updatePageContainerRelationship($args){
		
		if(is_array($args) && !empty($args['page_container_id'])){
			extract($args);
			
			$page_id=ceil($page_id);
			$container_id=ceil($container_id);
			$page_container_ordering=ceil($page_container_ordering);
			$page_container_enabled=ceil($page_container_enabled);
			$page_container_id=ceil($page_container_id);
			
			$query="UPDATE ".PVDatabase::getPageContainersRelationshipTableName()." SET page_id='$page_id', container_id='$container_id', page_container_ordering='$page_container_ordering' , page_container_enabled='$page_container_enabled' WHERE page_container_id='$page_container_id' ";
			PVDatabase::query($query);
		}//end is arrau
		
	}//end getPageContainerList
	
	public static function deletePageContainerRelationship($page_container_id){
		
		if(!empty($page_container_id)){
			
			$query="DELETE FROM ".PVDatabase::getPageContainersRelationshipTableName()." WHERE page_container_id='$page_container_id' ";
			PVDatabase::query($query);
		}
		
	}//end deletePageContainerRelationship
	
	
	
	public static function addPageModuleRelationship($page_id, $module_id, $page_module_order=0, $page_module_enabled=0  ){
		
		if(!empty($page_id) && !empty($module_id)){
			
			$page_id=ceil($page_id);
			$module_id=ceil($module_id);
			$page_module_order=ceil($page_module_order);
			$page_module_enabled=ceil($page_module_enabled);
			
			$query="INSERT INTO ".PVDatabase::getPageModuleRelationshipTableName()."(page_id, module_id, page_module_order , page_module_enabled) VALUES( '$page_id' , '$module_id' , '$page_module_order' , '$page_module_enabled' ) ";
			$page_module_id=PVDatabase::return_last_insert_query($query,'page_module_id', PVDatabase::getPageModuleRelationshipTableName());
			
			return $page_module_id;
		}
		
	}//end addPageContainerRelationship
	
	public static function getPageModuleRelationshipList($args){
		
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args);
		
			$first=1;
			$content_array=array();
			$table_name=PVDatabase::getPageModuleRelationshipTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
			
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
			
			
			if(!empty($page_module_ordering)){
					
				$page_module_ordering=trim($page_module_ordering);
				
				if($first==0 && ($page_module_ordering[0]!='+' && $page_module_ordering[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_module_ordering[0]=='+' || $page_module_ordering[0]==',') && $first==1 ){
					$page_module_ordering[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_module_ordering, 'page_module_ordering');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($page_module_enabled)){
					
				$page_module_enabled=trim($page_module_enabled);
				
				if($first==0 && ($page_module_enabled[0]!='+' && $page_module_enabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_module_enabled[0]=='+' || $page_module_enabled[0]==',') && $first==1 ){
					$page_module_enabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_module_enabled, 'page_module_enabled');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($page_module_id)){
					
				$page_module_id=trim($page_module_id);
				
				if($first==0 && ($page_module_id[0]!='+' && $page_module_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($page_module_id[0]=='+' || $page_module_id[0]==',') && $first==1 ){
					$page_module_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($page_module_id, 'page_module_id');
				
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
		
	}//end getPageContainerList
	
	public static function getPageModuleRelationship($page_container_id){
		if(!empty($page_module_id)){
			
			$page_module_id=ceil($page_module_id);
			
			$query="SELECT * FROM ".PVDatabase::getPageModuleRelationshipTableName()." WHERE page_module_id='$page_module_id' ";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row;
		}
	}//end getPageContainerRelationship
	
	public static function updatePageModuleRelationship($args){
		
		if(is_array($args) && !empty($args['page_module_id'])){
			extract($args);
			
			$page_id=ceil($page_id);
			$module_id=ceil($module_id);
			$page_module_ordering=ceil($page_module_ordering);
			$page_module_enabled=ceil($page_module_enabled);
			$page_module_id=ceil($page_module_id);
			
			$query="UPDATE ".PVDatabase::getPageModuleRelationshipTableName()." SET page_id='$page_id', module_id='$module_id', page_module_ordering='$page_module_ordering' , page_module_enabled='$page_module_enabled' WHERE page_module_id='$page_module_id' ";
			PVDatabase::query($query);
			
		}//end is arrau
		
	}//end getPageContainerList
	
	public static function deletePageModuleRelationship($page_module_id){
		
		if(!empty($page_module_id)){
			
			$query="DELETE FROM ".PVDatabase::getPageModuleRelationshipTableName()." WHERE page_module_id='$page_module_id' ";
			PVDatabase::query($query);
		}
		
	}//end deletePageContainerRelationship
	
	private static function getPageDefaults() {
		$defaults=array(
			'page_id'=>0,
			'page_name'=>'',
			'page_title'=>'',
			'page_description'=>'',
			'page_alias'=>'',
			'frontpage'=>0,
			'page_enabled'=>0,
			'page_ordering'=>0,
			'page_short_url'=>'',
			'page_url'=>'',
			'page_params'=>'',
			'page_permissions'=>'',
			'page_site_id'=>0,
			'parent_page'=>0,
			'page_access_level'=>0,
			'page_text'=>''
		);
		
		return $defaults;
	}
	
}//end class
	