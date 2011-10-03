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

class PVMenus extends PVStaticObject{
	
	public static function createMenu($args){
		
		$args=PVDatabase::makeSafe($args);
		
		if(is_array($args)){
			extract($args);
		}
		
		$menu_order=ceil($menu_order);
		$content_id=ceil($content_id);
		$user_id=ceil($user_id);
		$app_id=ceil($app_id);
		$menu_enabled=ceil($menu_enabled);
		
		if(empty($menu_unique_id)){
			$menu_unique_id=PVTools::generateRandomString(20);
		}
		
		$query="INSERT INTO ".PVDatabase::getMenuTableName()."(menu_name, menu_type, menu_tag_id, menu_css, menu_order, content_id, user_id, app_id, menu_unique_id, menu_class, menu_description, menu_enabled) VALUES( '$menu_name', '$menu_type', '$menu_tag_id', '$menu_css', '$menu_order', '$content_id', '$user_id', '$app_id', '$menu_unique_id', '$menu_class', '$menu_description', '$menu_enabled')";
		
		$menu_id=PVDatabase::return_last_insert_query($query, 'menu_id', PVDatabase::getMenuTableName());
			
			
		return $menu_id;
	
	}//end createMenu
	
	
	public static function updateMenu($args){
	
		$args=PVDatabase::makeSafe($args);
		
		if(is_array($args)){
			extract($args);	
		}
		$menu_id=ceil($menu_id);
		
		
		if(!empty($menu_id)){
			
			$menu_order=ceil($menu_order);
			$content_id=ceil($content_id);
			$user_id=ceil($user_id);
			$app_id=ceil($app_id);
			$menu_enabled=ceil($menu_enabled);
			
			$query="UPDATE ".PVDatabase::getMenuTableName()." SET menu_name='$menu_name', menu_type='$menu_type' , menu_tag_id='$menu_tag_id' , menu_css='$menu_css' , menu_order='$menu_order' , content_id='$content_id', user_id='$user_id', app_id='$app_id', menu_unique_id='$menu_unique_id', menu_class='$menu_class', menu_description='$menu_description', menu_enabled='$menu_enabled' WHERE menu_id='$menu_id' ";
			
			PVDatabase::query($query);
			
		}//end
		
	}//end updateMenu
	
	public static function getMenuList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			extract($args, EXTR_SKIP);
		}
		
		
		$first=1;
		
		$content_array=array();
		$table_name=PVDatabase::getMenuTableName();
		$db_type=PVDatabase::getDatabaseType();
				
		$WHERE_CLAUSE.='';
			
		if(!empty($menu_id) || $menu_id==='0'){
					
			$menu_id=trim($menu_id);
				
			if($first==0 && ($menu_id[0]!='+' && $menu_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_id[0]=='+' || $menu_id[0]==',') && $first==1 ){
				$app_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_id, 'menu_id');
				
			$first=0;
		}//end not empty app_id
		
		if(!empty($menu_name) || $menu_name==='0' ){
					
			$menu_name=trim($menu_name);
				
			if($first==0 && ($menu_name[0]!='+' && $menu_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_name[0]=='+' || $menu_name[0]==',') && $first==1 ){
				$menu_name[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_name, 'menu_name');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($menu_type) || $menu_type==='0' ){
					
			$menu_type=trim($menu_type);
				
			if($first==0 && ($menu_type[0]!='+' && $menu_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_type[0]=='+' || $menu_type[0]==',') && $first==1 ){
				$menu_type[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_type, 'menu_type');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($menu_tag_id)){
					
			$menu_tag_id=trim($menu_tag_id);
				
			if($first==0 && ($menu_tag_id[0]!='+' && $menu_tag_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_tag_id[0]=='+' || $menu_tag_id[0]==',') && $first==1 ){
				$menu_tag_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_tag_id, 'menu_tag_id');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($menu_css)){
					
			$menu_css=trim($menu_css);
				
			if($first==0 && ($menu_css[0]!='+' && $menu_css[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_css[0]=='+' || $menu_css[0]==',') && $first==1 ){
				$menu_css[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_css, 'menu_css');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($menu_order)){
					
			$menu_order=trim($menu_order);
				
			if($first==0 && ($menu_order[0]!='+' && $menu_order[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_order[0]=='+' || $menu_order[0]==',') && $first==1 ){
				$menu_order[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_order, 'menu_order');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($content_id)){
					
			$content_id=trim($content_id);
				
			if($first==0 && ($content_id[0]!='+' && $content_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($content_id[0]=='+' || $content_id[0]==',') && $first==1 ){
				$content_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_id, 'content_id');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($user_id)){
					
			$user_id=trim($user_id);
				
			if($first==0 && ($user_id[0]!='+' && $user_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($user_id[0]=='+' || $user_id[0]==',') && $first==1 ){
				$user_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($user_id, 'user_id');
				
			$first=0;
		}//end not empty app_id
		
		
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
		
		
		if(!empty($menu_unique_id)){
					
			$menu_unique_id=trim($menu_unique_id);
				
			if($first==0 && ($menu_unique_id[0]!='+' && $menu_unique_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_unique_id[0]=='+' || $menu_unique_id[0]==',') && $first==1 ){
				$menu_unique_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_unique_id, 'menu_unique_id');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($menu_class)){
					
			$menu_class=trim($menu_class);
				
			if($first==0 && ($menu_class[0]!='+' && $menu_class[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_class[0]=='+' || $menu_class[0]==',') && $first==1 ){
				$item_title[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_class, 'menu_class');
				
			$first=0;
		}//end not empty app_id
		
		if(!empty($menu_description)){
					
			$menu_description=trim($menu_description);
				
			if($first==0 && ($menu_description[0]!='+' && $menu_description[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_description[0]=='+' || $menu_description[0]==',') && $first==1 ){
				$menu_description[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_description, 'menu_description');
				
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
		
	}//end getMeniUtemList
	
	public static function getMenu($menu_id){
		
		$menu_id=ceil($menu_id);
		
		if(!empty($menu_id)){
			$query="SELECT * FROM ".PVDatabase::getMenuTableName()." WHERE menu_id='$menu_id' ";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			$row=PVDatabase::formatData($row);
			return $row;
		}
		
	}//end
	
	public static function getMenuByUniqueID($menu_unique_id){
		
		$menu_unique_id=PVDatabase::makeSafe($menu_unique_id);
		
		if(!empty($menu_unique_id)){
			$query="SELECT * FROM ".PVDatabase::getMenuTableName()." WHERE menu_unique_id='$menu_unique_id' ";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			$row=PVDatabase::formatData($row);
			return $row;
		}
		
	}//end
	
	public static function deleteMenu($menu_id, $DELETE_MENU_ITEMS=TRUE){
		$menu_id=ceil($menu_id);
		
		if(!empty($menu_id)){
			$query="DELETE FROM ".PVDatabase::getMenuTableName()." WHERE menu_id='$menu_id' ";
			PVDatabase::query($query);
			
			if($DELETE_MENU_ITEMS){
				$query="DELETE FROM ".PVDatabase::getMenuItemsTableName()." WHERE menu_id='$menu_id'";
				PVDatabase::query($query);
			}
			
		}//end !empty(menu_id)
	}//end deleteMenu
	
	
	public static function createMenuItem($args){
		
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args);
			
			
			$menu_id=ceil($menu_id);
			$parent_id=ceil($parent_id);
			$item_ordering=ceil($item_ordering);
			$item_enabled=ceil($item_enabled);
			
			$query="INSERT INTO ".PVDatabase::getMenuItemsTableName()."(menu_id, parent_id,item_name, item_description, item_url, item_params, item_css, item_ordering, item_enabled, item_title, item_permissions, item_id_tag) VALUES( '$menu_id' , '$parent_id' , '$item_name' , '$item_description', '$item_url', '$item_params', '$item_css', '$item_ordering', '$item_enabled', '$item_title', '$item_permissions' , '$item_id_tag' ) ";
			
			$item_id=PVDatabase::return_last_insert_query($query, 'item_id', PVDatabase::getMenuItemsTableName());
			
			
			return $item_id;
		}//end if is_array
		
	}//end createmenuitem
	
	
	public static function updateMenuItem($args){
		
		if( is_array($args) && !empty($args['menu_id']) && !empty($args['item_id']) ){
			$args=PVDatabase::makeSafe($args);
			extract($args);
			
			$menu_id=ceil($menu_id);
			$parent_id=ceil($parent_id);
			$item_ordering=ceil($item_ordering);
			$item_enabled=ceil($item_enabled);
			
			$query="UPDATE ".PVDatabase::getMenuItemsTableName()." SET  parent_id='$parent_id', item_name='$item_name', item_description='$item_description',  item_url='$item_url' , item_params='$item_params' , item_css='$item_css' , item_ordering='$item_ordering' ,  item_enabled='$item_enabled' , item_title='$item_title' , item_permissions='$item_permissions' ,  item_id_tag='$item_id_tag' WHERE menu_id='$menu_id' AND item_id='$item_id' ";
			
			PVDatabase::query($query);
		}//end is_array
		
	}//end updateMenuItem
	
	public static function getMenuItemList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			extract($args, EXTR_SKIP);
		}
		
		
		$first=1;
			
		$content_array=array();
		$table_name=PVDatabase::getMenuItemsTableName();
		$db_type=PVDatabase::getDatabaseType();
				
		$WHERE_CLAUSE.='';
			
		if(!empty($menu_id) || $menu_id==='0'){
					
			$menu_id=trim($menu_id);
				
			if($first==0 && ($menu_id[0]!='+' && $menu_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($menu_id[0]=='+' || $menu_id[0]==',') && $first==1 ){
				$app_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($menu_id, 'menu_id');
				
			$first=0;
		}//end not empty app_id
		
		if(!empty($item_id) || $item_id==='0' ){
					
			$item_id=trim($item_id);
				
			if($first==0 && ($item_id[0]!='+' && $item_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_id[0]=='+' || $item_id[0]==',') && $first==1 ){
				$item_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_id, 'item_id');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($parent_id) || $parent_id==='0' ){
					
			$parent_id=trim($parent_id);
				
			if($first==0 && ($parent_id[0]!='+' && $parent_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($parent_id[0]=='+' || $parent_id[0]==',') && $first==1 ){
				$parent_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($parent_id, 'parent_id');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($item_name)){
					
			$item_name=trim($item_name);
				
			if($first==0 && ($item_name[0]!='+' && $item_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_name[0]=='+' || $item_name[0]==',') && $first==1 ){
				$item_name[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_name, 'item_name');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($item_description)){
					
			$item_description=trim($item_description);
				
			if($first==0 && ($item_description[0]!='+' && $item_description[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_description[0]=='+' || $item_description[0]==',') && $first==1 ){
				$item_description[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_description, 'item_description');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($item_url)){
					
			$item_url=trim($item_url);
				
			if($first==0 && ($item_url[0]!='+' && $item_url[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_url[0]=='+' || $item_url[0]==',') && $first==1 ){
				$item_url[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_url, 'item_url');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($item_params)){
					
			$item_params=trim($item_params);
				
			if($first==0 && ($item_params[0]!='+' && $item_params[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_params[0]=='+' || $item_params[0]==',') && $first==1 ){
				$item_params[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_params, 'item_params');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($item_css)){
					
			$item_css=trim($item_css);
				
			if($first==0 && ($item_css[0]!='+' && $item_css[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_css[0]=='+' || $item_css[0]==',') && $first==1 ){
				$item_css[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_css, 'item_css');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($item_ordering)){
					
			$item_ordering=trim($item_ordering);
				
			if($first==0 && ($item_ordering[0]!='+' && $item_ordering[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_ordering[0]=='+' || $item_ordering[0]==',') && $first==1 ){
				$item_ordering[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_ordering, 'item_ordering');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($item_enabled)){
					
			$item_enabled=trim($item_enabled);
				
			if($first==0 && ($item_enabled[0]!='+' && $item_enabled[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_enabled[0]=='+' || $item_enabled[0]==',') && $first==1 ){
				$item_enabled[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_enabled, 'item_enabled');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($item_title)){
					
			$item_title=trim($item_title);
				
			if($first==0 && ($item_title[0]!='+' && $item_title[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_title[0]=='+' || $item_title[0]==',') && $first==1 ){
				$item_title[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_title, 'item_title');
				
			$first=0;
		}//end not empty app_id
		
		if(!empty($item_permissions)){
					
			$item_permissions=trim($item_permissions);
				
			if($first==0 && ($item_permissions[0]!='+' && $item_permissions[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_permissions[0]=='+' || $item_permissions[0]==',') && $first==1 ){
				$item_permissions[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_permissions, 'item_permissions');
				
			$first=0;
		}//end not empty app_id
		
		if(!empty($item_id_tag)){
					
			$item_id_tag=trim($item_id_tag);
				
			if($first==0 && ($item_id_tag[0]!='+' && $item_id_tag[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($item_id_tag[0]=='+' || $item_id_tag[0]==',') && $first==1 ){
				$item_id_tag[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($item_id_tag, 'item_id_tag');
				
			$first=0;
		}//end not empty app_id
		
		$JOINS='';
		
		if($join_menu){
				$JOINS.=' JOIN '.PVDatabase::getMenuTableName().' ON '.PVDatabase::getMenuTableName().'.menu_id='.PVDatabase::getMenuItemsTableName().'.menu_id';
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
		
	}//end getMeniUtemList
	
	public static function getMenuItem($menu_id, $item_id){
		
		$menu_id=ceil($menu_id);
		$item_id=ceil($item_id);
			
		if( !empty($menu_id) && !empty($item_id) ){
			
			$query="SELECT * FROM ".PVDatabase::getMenuItemsTableName()." WHERE menu_id='$menu_id' AND item_id='$item_id' ";
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			$row=PVDatabase::formatData($row);
			return $row;
			
		}//end is_array
		
	}//end updateMenuItem
	
	
	public static function deleteMenuItem($menu_id, $item_id){
		
		if( !empty($menu_id) && !empty($item_id) ){
			
			$menu_id=ceil($menu_id);
			$item_id=ceil($item_id);
			
			$query="DELETE FROM ".PVDatabase::getMenuItemsTableName()." WHERE menu_id='$menu_id' AND item_id='$item_id' ";
			
			PVDatabase::query($query);
		}//end is_array
		
	}//end updateMenuItem
	
	public static function generateListMenu($menu_unique_id, $args){
		
		if(!empty($menu_unique_id)){
			
			$menu_unique_id=PVDatabase::makeSafe($menu_unique_id);
			
			if(PVValidator::isInteger($menu_unique_id)){
				$query="SELECT * FROM ".PVDatabase::getMenuTableName()." WHERE menu_id='$menu_unique_id' ";	
			}
			else{
				$query="SELECT * FROM ".PVDatabase::getMenuTableName()." WHERE menu_unique_id='$menu_unique_id' ";	
			}
			
			$result=PVDatabase::query($query);
			
			$menu_info=PVDatabase::fetchArray($result);
			
			if(!empty($menu_info)){
				
			}//end !empty($menu_info)
			
			
			
		}//end !empty(menu_unique_id)
		
	}//end displayListMenu
		
}//end class
	