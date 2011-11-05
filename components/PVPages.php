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
	
	/**
	 * Retrieve a pages's url by the page's page id.
	 * 
	 * @param id $page_id The id of the page
	 * 
	 * @return string $page_url The url of the page
	 * @access public
	 */
	public static function getPageURLByID($page_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_id);
		
		$page_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_id , array('event'=>'args'));
		
		$query="SELECT page_url FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		$row = PVDatabase::formatData($row);
		self::_notify(get_class().'::'.__FUNCTION__, $row, $page_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ ,  $row, array('event'=>'return'));
			
		return $row['page_url'];
	}//end get pagePageNameByID
	
	/**
	 * Retrieve a pages's short url by the page's page id.
	 * 
	 * @param id $page_id The id of the page
	 * 
	 * @return string $page_url The url of the page
	 * @access public
	 */
	public static function getPageShortURLByID($page_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_id);
		
		$page_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_id , array('event'=>'args'));
		
		$query="SELECT page_short_url FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		$row = PVDatabase::formatData($row);
		self::_notify(get_class().'::'.__FUNCTION__, $row, $page_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ ,  $row, array('event'=>'return'));
			
		return $row['page_short_url'];
	}//end get pagePageNameByID
	
	/**
	 * Get the page's alias by the id of the page.
	 * 
	 * @param id $page_id The id of the page
	 * 
	 * @return string $page_alias The alias of the page
	 * @access public
	 */
	public static function getPageAliasByID($page_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_id);
		
		$page_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_id, array('event'=>'args'));
		
		$query="SELECT page_alias FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		$row = PVDatabase::formatData($row);
		self::_notify(get_class().'::'.__FUNCTION__, $row, $page_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ ,  $row, array('event'=>'return'));
		
		return $row['page_alias'];
	}
	
	/**
	 * Create a page in the database that interacts with the paging system.
	 * 
	 * @param array $args A list of arguements that define a page
	 * 			-'page_name' _string_: The name of the page
	 * 			-'page_title' _string_: page_title
	 * 			-'page_description' _string_: A description of the page. Doubles as the page's text
	 * 			-'page_aias' _string_: An alias of the page
	 * 			-'frontpage' _boolean:Is  page the default/front page. Default is false.
	 * 			-'page_enabled' _boolean: Is the current page enabled. Default is false.
	 * 			-'page_ordering' _int_: The order that the page is in.
	 * 			-'page_short_url' _string_: A shortned url of the page
	 * 			-'page_url' _string_ The url of the page
	 * 			-'page_params' _string_: Parameters in the page
	 * 			-'page_permissions_' _string_ The roles of the users allowed to access this page
	 * 			-'parent_page' _id_: The id of the parent page to the page
	 * 			-'page_site_id' _id_: The site id this page displays on
	 * 			-'page_access_level' _integer_: The access level of the page
	 * 			-'page_text' _string_: The text that page displays
	 * 
	 * @return id $page_id The id of the page
	 * @access public 
	 */
	public static function createPage($args=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::_getPageDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ ,  $args, array('event'=>'args'));
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$page_enabled=ceil($page_enabled);
		$page_ordering=ceil($page_ordering);
		$frontpage=ceil($frontpage);
		
		$query="INSERT INTO ".PVDatabase::getPagesTableName()." (page_name, page_title, page_description, page_alias, frontpage, page_enabled, page_ordering, page_url, page_params, page_permissions, parent_page, page_site_id, page_access_level, page_text) VALUES ('$page_name', '$page_title', '$page_description', '$page_alias', '$frontpage', '$page_enabled', '$page_ordering', '$page_url', '$page_params', '$page_permissions', '$parent_page', '$page_site_id','$page_access_level','$page_text' )";
		$page_id=PVDatabase::return_last_insert_query($query, 'page_id', PVDatabase::getPagesTableName());
		
		self::_notify(get_class().'::'.__FUNCTION__, $page_id, $args);
		$page_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_id, array('event'=>'return'));
		
		return $page_id;
	}//end createPage
	
	/**
	 * Find pages based on the based critera. Uses the PV Standard Search Query when searching.
	 * 
	 * @param array $args A list of arguements that define a page
	 * 			-'page_id' _id_: The id of a page
	 * 			-'page_name' _string_: The name of the page
	 * 			-'page_title' _string_: page_title
	 * 			-'page_description' _string_: A description of the page. Doubles as the page's text
	 * 			-'page_aias' _string_: An alias of the page
	 * 			-'frontpage' _boolean:Is  page the default/front page. Default is false.
	 * 			-'page_enabled' _boolean: Is the current page enabled. Default is false.
	 * 			-'page_ordering' _int_: The order that the page is in.
	 * 			-'page_short_url' _string_: A shortned url of the page
	 * 			-'page_url' _string_ The url of the page
	 * 			-'page_params' _string_: Parameters in the page
	 * 			-'page_permissions_' _string_ The roles of the users allowed to access this page
	 * 			-'parent_page' _id_: The id of the parent page to the page
	 * 			-'page_site_id' _id_: The site id this page displays on
	 * 			-'page_access_level' _integer_: The access level of the page
	 * 			-'page_text' _string_: The text that page displays
	 * 
	 * @return id array $pages Returns an array of pages
	 * @access public 
	 */
	public static function getPageList($args=array()) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::_getPageDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ ,  $args, array('event'=>'args'));
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
    	
    	$content_array = PVDatabase::formatData($content_array);
		self::_notify(get_class().'::'.__FUNCTION__, $content_array, $args);
		$content_array = self::_applyFilter( get_class(), __FUNCTION__ ,  $content_array, array('event'=>'return'));
		
    	return $content_array;
	}//end getPageList
	
	/**
	 * Returns the data on page determined by the id of the page.
	 * 
	 * @param id $page_id The id of the page
	 * 
	 * @return array $page Data pertaining to that page
	 * @access public
	 */
	public static function getPage($page_id) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_id);
		
		$page_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_id, array('event'=>'args'));
			
		$page_id=PVDatabase::makeSafe($page_id);
			
		$query="SELECT * FROM ".PVDatabase::getPagesTableName()." WHERE page_id='$page_id'";
			
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$row=PVDatabase::formatData($row);
		
		self::_notify(get_class().'::'.__FUNCTION__, $row, $page_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ ,  $row , array('event'=>'return'));
			
		return $row;
	}//end get page
	
	/**
	 * Returns the data on page determined by the alias of the page. If multipe pages has the same alias, the
	 * first page found will be returned
	 * 
	 * @param string $page_alias The alias of the page
	 * 
	 * @return array $page Data pertaining to that page
	 * @access public
	 */
	public static function getPageByAlias($page_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_id);
		
		$page_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_id, array('event'=>'args'));
		
		$page_id=PVDatabase::makeSafe($page_id);
			
		$query="SELECT * FROM ".PVDatabase::getPagesTableName()." WHERE page_alias='$page_id'";
			
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$row=PVDatabase::formatData($row);
		
		self::_notify(get_class().'::'.__FUNCTION__, $row, $page_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ ,  $row , array('event'=>'return'));
			
		return $row;
	}//end get page
	
	/**
	 * Returns the data on page determined by the url of the page. If multipe pages have the same url, the
	 * first page found will be returned
	 * 
	 * @param string $page_alias The alias of the page
	 * 
	 * @return array $page Data pertaining to that page
	 * @access public
	 */
	public static function getPageByUrl($url) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $url);
		
		$url = self::_applyFilter( get_class(), __FUNCTION__ ,  $url, array('event'=>'args'));
		$url = PVDatabase::makeSafe($url);
			
		$query="SELECT * FROM ".PVDatabase::getPagesTableName()." WHERE page_url='$url'";
			
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$row=PVDatabase::formatData($row);
		
		self::_notify(get_class().'::'.__FUNCTION__, $row, $url);
		$row = self::_applyFilter( get_class(), __FUNCTION__ ,  $row , array('event'=>'return'));
			
		return $row;
	}//end get page
	
	/**
	 * Update a page's information. Required the id of the page.
	 * 
	 * @param array $args A list of arguements that define a page
	 * 			-'page_id' _id_: Required, the id of the page to update
	 * 			-'page_name' _string_: The name of the page
	 * 			-'page_title' _string_: page_title
	 * 			-'page_description' _string_: A description of the page. Doubles as the page's text
	 * 			-'page_aias' _string_: An alias of the page
	 * 			-'frontpage' _boolean:Is  page the default/front page. Default is false.
	 * 			-'page_enabled' _boolean: Is the current page enabled. Default is false.
	 * 			-'page_ordering' _int_: The order that the page is in.
	 * 			-'page_short_url' _string_: A shortned url of the page
	 * 			-'page_url' _string_ The url of the page
	 * 			-'page_params' _string_: Parameters in the page
	 * 			-'page_permissions_' _string_ The roles of the users allowed to access this page
	 * 			-'parent_page' _id_: The id of the parent page to the page
	 * 			-'page_site_id' _id_: The site id this page displays on
	 * 			-'page_access_level' _integer_: The access level of the page
	 * 			-'page_text' _string_: The text that page displays
	 * 
	 * @return void
	 * @access public 
	 */
	public static function updatePage($args=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::_getPageDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ ,  $args, array('event'=>'args'));
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$page_enabled=ceil($page_enabled);
		$page_ordering=ceil($page_ordering);
		$frontpage=ceil($frontpage);
			
		$query="UPDATE ".PVDatabase::getPagesTableName()." SET page_name='$page_name', page_title='$page_title', page_description='$page_description', page_alias='$page_alias', frontpage='$frontpage', page_enabled='$page_enabled', page_ordering='$page_ordering', page_short_url='$page_short_url', page_params='$page_params', page_permissions='$	page_permissions', page_site_id='$page_site_id', parent_page='$parent_page' WHERE page_id='$page_id'";
		PVDatabase::query($query);
		self::_notify(get_class().'::'.__FUNCTION__, $args);
		
	}//end updatePage
	
	/**
	 * Remove a page from the database and optionally all it's chidren pages.
	 * 
	 * @param id $page_id The id of the page to delete
	 * @param boolean $recursive Will remove children pages. Default is false.
	 * 
	 * @return void
	 * @access public
	 */
	public static function deletePage($page_id, $recursive=FALSE) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_id, $recursive);
		
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('page_id'=>$page_id, 'recursive'=>$recursive), array('event'=>'args'));
		$page_id = $filtered['page_id'];
		$recursive = $filtered['recursive'];
		
		if(!empty($page_id)){
			
			$page_id=PVDatabase::makeSafe($page_id);
			
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
		
			self::_notify(get_class().'::'.__FUNCTION__, $page_id, $recursive);
		}//end not empty page id
		
	}//end deletePage
	
	/**
	 * Adds a relationship between a page and a container.
	 * 
	 * @param id $page_id The id of the page
	 * @param id $container_id The id of the container
	 * @param int $page_container_order The order in which the container is set
	 * @param boolean $page_container_enabled Determines if the relationship is active
	 * 
	 * @return id $page_container_id The id of the relationship
	 * @access public
	 */
	public static function addPageContainerRelationship($page_id, $container_id, $page_container_order=0, $page_container_enabled=0 ) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_id, $container_id, $page_container_order, $page_container_enabled);
		
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('page_id'=>$page_id, 'container_id'=>$container_id, 'page_container_order'=>$page_container_order, 'page_container_enabled'=>$page_container_enabled), array('event'=>'args'));
		$page_id = $filtered['page_id'];
		$container_id = $filtered['container_id'];
		$page_container_order = $filtered['page_container_order'];
		$page_container_enabled = $filtered['page_container_enabled'];
		
		if(!empty($page_id) && !empty($container_id)){
			
			$page_id=PVDatabase::makeSafe($page_id);
			$container_id=PVDatabase::makeSafe($container_id);
			$page_container_order=ceil($page_container_order);
			$page_container_enabled=ceil($page_container_enabled);
			
			$query="INSERT INTO ".PVDatabase::getPageContainersRelationshipTableName()."( page_id , container_id , page_container_ordering , page_container_enabled) VALUES( '$page_id' , '$container_id' , '$page_container_ordering' , '$page_container_enabled') ";
			$page_container_id=PVDatabase::return_last_insert_query($query,'page_container_id', PVDatabase::getPageContainersRelationshipTableName());
			
			self::_notify(get_class().'::'.__FUNCTION__, $page_container_id, $page_id, $container_id, $page_container_order, $page_container_enabled);
			$page_container_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_container_id , array('event'=>'return'));
			
			return $page_container_id;
		}
		
	}//end addPageContainerRelationship
	
	/**
	 * Returns a list of relationships between pages and containers.
	 * 
	 * @param array @args Arguements that define the relationship and are used for searching a relationship
	 * 			-'page_container_id' _id_: The id of the page container relationship
	 * 			-'page_id' _id_: The id of the page
	 * 			-'container_id' _id_: The id of the container
	 * 			-'page_container_order' _int_: The order in which relationship is set
	 * 			-'page_container_enabled' _boolean_: Determines if the relationship is active
	 * 
	 * @return array $page_container_relationships An array of relationships between a page and a container
	 * @access public
	 */
	public static function getPageContainerRelationshipList($args = array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ ,  $args, array('event'=>'args'));
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			$args=PVDatabase::makeSafe($args);
			extract($args, EXTR_SKIP);
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getPageContainersRelationshipTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE='';
			
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
		$args = self::_applyFilter( get_class(), __FUNCTION__ ,  $content_array , array('event'=>'return'));
		
    	return $content_array;
	}//end getPageContainerList
	
	/**
	 * Returns the data associated with a page container relationship.
	 * 
	 * @param id $page_container_id The id of the relationship whose data to return
	 * 
	 * @return array $page_container_relationship The data between the relationship
	 * @access public
	 */
	public static function getPageContainerRelationship($page_container_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_container_id );
			
		$page_container_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_container_id, array('event'=>'args'));
		$page_container_id = PVDatabase::makeSafe($page_container_id);
		$query="SELECT * FROM ".PVDatabase::getPageContainersRelationshipTableName()." WHERE page_container_id='$page_container_id' ";
		
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$row=PVDatabase::formatData($row);
		
		self::_notify(get_class().'::'.__FUNCTION__, $row, $page_container_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ ,  $row , array('event'=>'return'));
		
		return $row;
	}//end getPageContainerRelationship
	
	/**
	 * Updates a relationship between a page and a container. Requires the id of the relationship.
	 * 
	 * @param array @args Arguements that define the relationship and are used for searching a relationship
	 * 			-'page_container_id' _id_: Required.  The id of the page container relationship
	 * 			-'page_id' _id_: The id of the page
	 * 			-'container_id' _id_: The id of the container
	 * 			-'page_container_order' _int_: The order in which relationship is set
	 * 			-'page_container_enabled' _boolean_: Determines if the relationship is active
	 * 
	 * @return array $page_container_relationships An array of relationships between a page and a container
	 * @access public
	 */
	public static function updatePageContainerRelationship($args = array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args = self::_applyFilter( get_class(), __FUNCTION__ ,  $args, array('event'=>'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);
			
		$page_container_ordering=ceil($page_container_ordering);
		$page_container_enabled=ceil($page_container_enabled);
			
		$query="UPDATE ".PVDatabase::getPageContainersRelationshipTableName()." SET page_id='$page_id', container_id='$container_id', page_container_ordering='$page_container_ordering' , page_container_enabled='$page_container_enabled' WHERE page_container_id='$page_container_id' ";
		PVDatabase::query($query);
		self::_notify(get_class().'::'.__FUNCTION__, $args);
	}//end getPageContainerList
	
	/**
	 * Removes a relationship between the page and a container.
	 * 
	 * @param id $page_container_id The id between the page and the container
	 * 
	 * @return void
	 * @access public
	 */
	public static function deletePageContainerRelationship($page_container_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_container_id);
		
		$page_container_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_container_id, array('event'=>'args'));
		
		if(!empty($page_container_id)){
			$query="DELETE FROM ".PVDatabase::getPageContainersRelationshipTableName()." WHERE page_container_id='$page_container_id' ";
			PVDatabase::query($query);
			self::_notify(get_class().'::'.__FUNCTION__, $page_container_id);
		}
		
	}//end deletePageContainerRelationship
	
	/**
	 * Adds a relationship between a page and a module.
	 * 
	 * @param id $page_id The id of the page
	 * @param id $module_id The id of the module
	 * @param int $page_module_order The order the module will be placed in.
	 * @param boolean $page_module_enabled Determines if the page module relationship is active
	 * 
	 * @return id $page_module_id The id of the newly created page module relationship
	 * @access public
	 */
	public static function addPageModuleRelationship($page_id, $module_id, $page_module_order=0, $page_module_enabled=0) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_id, $module_id, $page_module_order, $page_module_enabled );
			
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('page_id'=>$page_id, 'module_id'=>$module_id, 'page_module_order'=>$page_module_order, 'page_module_enabled'=>$page_module_enabled), array('event'=>'args'));
		$page_id = $filtered['page_id'];
		$module_id = $filtered['module_id'];
		$page_module_order = $filtered['page_module_order'];
		$page_module_enabled = $filtered['page_module_enabled'];
		
		if(!empty($page_id) && !empty($module_id)){
			
			$page_id=PVDatabase::makeSafe($page_id);
			$module_id=PVDatabase::makeSafe($module_id);
			$page_module_order=ceil($page_module_order);
			$page_module_enabled=ceil($page_module_enabled);
			
			$query="INSERT INTO ".PVDatabase::getPageModuleRelationshipTableName()."(page_id, module_id, page_module_order , page_module_enabled) VALUES( '$page_id' , '$module_id' , '$page_module_order' , '$page_module_enabled' ) ";
			$page_module_id=PVDatabase::return_last_insert_query($query,'page_module_id', PVDatabase::getPageModuleRelationshipTableName());
			
			self::_notify(get_class().'::'.__FUNCTION__, $page_module_id, $page_id, $module_id, $page_module_order, $page_module_enabled);
			$page_module_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_module_id , array('event'=>'return'));
		
			return $page_module_id;
		}
		
	}//end addPageContainerRelationship
	
	/**
	 * Searches for a page module relationships based uponthe passed arguements.
	 * 
	 * @param array $args Arguements used for searching for a page module relationship
	 * 			-'page_module_id' _id_: The id of the relationship to search for
	 * 			-'page_id' _id_ The id of the page
	 * 			-'module_id' _id_ The id of the module
	 * 			-'page_module_order' _int_: The order the pagemodule relationshp has set
	 * 			-'page_module_enabled' _boolean_: Determines if the page module relatuionship is active
	 * 
	 * @return array $page_module_relationships An array of page module relationships found
	 * @access public
	 */
	public static function getPageModuleRelationshipList($args = array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args = self::_applyFilter( get_class(), __FUNCTION__ ,  $args, array('event'=>'args'));
		
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
		self::_notify(get_class().'::'.__FUNCTION__, $content_array, $args);
		$content_array = self::_applyFilter( get_class(), __FUNCTION__ ,  $content_array , array('event'=>'return'));
		
    	return $content_array;
	}//end getPageContainerList
	
	/**
	 * Retrieve the data associated with a page module relationship.
	 * 
	 * @param id $page_module_id The id of the page module relationship
	 * 
	 * @return array $page_module_relationship The data associated with the page module relationshiop
	 * @access public
	 */
	public static function getPageModuleRelationship($page_module_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_module_id);
		
		$page_module_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_module_id, array('event'=>'args'));
		$page_module_id=PVDatabase::makeSafe($page_module_id);
			
		$query="SELECT * FROM ".PVDatabase::getPageModuleRelationshipTableName()." WHERE page_module_id='$page_module_id' ";
			
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$row=PVDatabase::formatData($row);
		
		self::_notify(get_class().'::'.__FUNCTION__, $row, $page_module_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ ,  $row , array('event'=>'return'));
			
		return $row;
	}//end getPageContainerRelationship
	
	
	/**
	 * Updates a page module relationship
	 * 
	 * @param array $args Arguements used for updating page module relationship
	 * 			-'page_module_id' _id_: The id of the relationship to search for
	 * 			-'page_id' _id_ The id of the page
	 * 			-'module_id' _id_ The id of the module
	 * 			-'page_module_order' _int_: The order the pagemodule relationshp has set
	 * 			-'page_module_enabled' _boolean_: Determines if the page module relatuionship is active
	 * 
	 * @return void
	 * @access public
	 */
	public static function updatePageModuleRelationship($args = array()){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args = self::_applyFilter( get_class(), __FUNCTION__ ,  $args, array('event'=>'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);
		
		$page_module_ordering=ceil($page_module_ordering);
		$page_module_enabled=ceil($page_module_enabled);
			
		$query="UPDATE ".PVDatabase::getPageModuleRelationshipTableName()." SET page_id='$page_id', module_id='$module_id', page_module_ordering='$page_module_ordering' , page_module_enabled='$page_module_enabled' WHERE page_module_id='$page_module_id' ";
		PVDatabase::query($query);
		self::_notify(get_class().'::'.__FUNCTION__, $args);
	}//end getPageContainerList
	
	/**
	 * Remove a page module relationship from the database
	 * 
	 * @param id $page_module_id The id of page module relationship to delete
	 * 
	 * @return void
	 * @access public
	 */
	public static function deletePageModuleRelationship($page_module_id) {
				
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $page_module_id);
		
		$page_module_id = self::_applyFilter( get_class(), __FUNCTION__ ,  $page_module_id, array('event'=>'args'));
		$page_module_id = PVDatabase::makeSafe($page_module_id);
		
		if(!empty($page_module_id)){
			$query="DELETE FROM ".PVDatabase::getPageModuleRelationshipTableName()." WHERE page_module_id='$page_module_id' ";
			PVDatabase::query($query);
			self::_notify(get_class().'::'.__FUNCTION__, $page_module_id);
		}
		
	}//end deletePageContainerRelationship
	
	protected static function _getPageDefaults() {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__);
		
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
		
		$defaults = self::_applyFilter( get_class(), __FUNCTION__ ,  $defaults , array('event'=>'return'));
		
		return $defaults;
	}
	
}//end class
	