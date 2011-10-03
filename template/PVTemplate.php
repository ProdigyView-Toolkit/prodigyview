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

class PVTemplate extends PVStaticObject {
	
	private static $siteTitle;
	private static $siteMetaTags;
	private static $siteMetaDescription;
	
	function __construct(){
	
	}
	
	function init() {
		$config=PVConfiguration::getSiteCompleteConfiguration();
		
		self::$siteTitle=$config['site_name'];
		self::$siteMetaTags=$config['meta_keywords'];
		self::$siteMetaDescription=$config['meta_description'];
	}
	
	/**
	 * Prints the site title set in the xml configuration file.
	 * Modify the tags in <site_name></site_name> to change the site title.
	 */
	public static function printSiteTitle(){
		echo self::$siteTitle;
	}
	
	/**
	 * Prints the site meta description set in the xml configuration file.
	 * Modify the tags in <meta_description></meta_description> to change the meta description.
	 */
	public static function printSiteMetaDescription(){
		echo self::$siteMetaDescription;
	}
	
	/**
	 * Prints the site meta tags set in the xml configuration file.
	 * Modify the tags in <meta_keywords></meta_keywords> to change the meta tags.
	 */
	public static function printSiteMetaTags(){
		echo self::$siteMetaTags;
	}
	
	/**
	 * Returns the title set for the site
	 * Modify the tags in <site_name></site_name> to change the site title.
	 *
	 * @return string siteTitle: The sets title.
	 */
	public static function getSiteTitle(){
		return self::$siteTitle;
	}
	
	/**
	 * Returns the title set for the site.
	 * Modify the tags in <site_name></site_name> to change the site title.
	 *
	 * @return string siteTitle: The sets title.
	 */
	public static function getSiteMetaDescription(){
		return self::$siteMetaDescription;
	}
	
	/**
	 * Returns the meta descroption  set for the site.
	 * Modify the tags in <meta_description></meta_description> to change the meta description.
	 *
	 * @return string meta_tags: The sets title.
	 */
	public static function getSiteMetaTags(){
		return self::$siteMetaTags;
		
	}
	
	/**
	 * Ovveride the title of the site.
	 * 
	 * @param string title: Site title
	 */
	public static function setSiteTitle($string){
		self::$siteTitle=$string;
	}
	
	/**
	 * Append to the site title
	 * 
	 * @param string title: Site title
	 */
	public static function appendSiteTitle($string){
		self::$siteTitle.=$string;
	}
	
	/**
	 * Ovveride the meta tags of the site.
	 * 
	 * @param string meta_tags: Set the meta tags
	 */
	public static function setSiteMetaTags($string){
		self::$siteMetaTags=$string;
	}
	
	public static function appendSiteMetaTags($string){
		self::$siteMetaTags.=$string;
	}
	
	/**
	 * Ovveride the meta description of the site.
	 * 
	 * @param string title: Site title
	 */
	public static function setSiteMetaDescription($string){
		self::$siteMetaDescription=$string;
	}
	
	public static function appendSiteMetaDescription($string){
		self::$siteMetaDescription.=$string;
	}
	
	public static function errorMessage($message, $options=array()){
			$defaults=array('class'=>'error-message');
			$options += $defaults;
			
			return PVHtml::div($message, $options);
	}
	
	public static function successMessage($message, $options=array()){
			$defaults=array('class'=>'success-message');
			$options += $defaults;
			
			return PVHtml::div($message, $options);
	}
	
	public static function installTemplate($args){
		
		if(!empty($args) && is_array($args)){
			
			$args=PVDatabase::makeSafe($args);
			
			extract($args);
			
			$is_default=PVTools::convertTextBoolean($is_default);
			$is_default=ceil($is_default);
			$template_page=ceil($template_page);
			$template_site_id=ceil($template_site_id);
			
			$query="SELECT template_id FROM ".PVDatabase::getTemplatesTableName()." WHERE template_unique_id='$template_unique_id'";
			
			$result=PVDatabase::query($query);
			
			
			if(PVDatabase::resultRowCount($result) <= 0){
	  			
	  			$query="INSERT INTO ".PVDatabase::getTemplatesTableName()."(template_name , template_version , template_author , template_license , main_file , xml_file , template_directory , template_image , template_unique_id, template_domain, template_page, template_site_id, template_options ) VALUES( '$template_name' , '$template_version' , '$template_author' , '$template_license' , '$main_file' , '$xml_file' , '$template_directory' , '$template_image' , '$template_unique_id', '$template_domain' , '$template_page' , '$template_site_id', '$template_options' ) " ;
	  			
	  			$template_id=PVDatabase::return_last_insert_query($query,  "template_id", PVDatabase::getTemplatesTableName());
	  			
	  			foreach($positions as $value){
	  			
	  				$query="INSERT INTO ".PVDatabase::getTemplatePositionsTableName()."(template_id, position_name) VALUES('$template_id', '$value') ";
	  				

	  				PVDatabase::query($query);
	  			}
	  			return $template_id;
	  		}//end if result  < 0
			else{
				
				$query="UPDATE ".PVDatabase::getTemplatesTableName()." SET template_name='$template_name' , template_version='$template_version' , template_author='$template_author' , template_license='$template_license' , main_file='$main_file' , xml_file='$xml_file' , template_directory='$template_directory' , template_image='$template_image' , template_unique_id='$template_unique_id' , template_domain='$template_domain', template_page='$template_page', template_site_id='$template_site_id', template_options='$template_options' WHERE template_unique_id='$template_unique_id' ";
					PVDatabase::query($query);
				
			}//end else 
			
		}//end if not empty and is array
		
	}//end installTemplate
	
	public static function getTemplate($template_id){
		
		if(!empty($template_id)){
			
			if(PVValidator::isInteger($template_id)){
				$template_id=ceil($template_id);
				$query="SELECT * FROM ".PVDatabase::getTemplatesTableName()." WHERE template_id='$template_id' ";
			}
			else{
				$template_id=PVDatabase::makeSafe($template_id);
				$query="SELECT * FROM ".PVDatabase::getTemplatesTableName()." WHERE template_unique_id='$template_id' ";
			}
			
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			$row=PVDatabase::formatData($row);
			
			return $row;
									
		}//end not empty
		
	}//end getTemplate
	
	
	
	public static function getTemplateList($args){
		
		if(is_array($args)){
			extract($args);
		}
		
		$first=1;
		
		$content_array=array();
		$table_name=PVDatabase::getTemplatesTableName();
		$db_type=PVDatabase::getDatabaseType();
			
		$WHERE_CLAUSE.='';
		
		if(!empty($template_id)){
				
			$template_id=trim($template_id);
			
			if($first==0 && ($template_id[0]!='+' && $template_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_id[0]=='+' || $template_id[0]==',') && $first==1 ){
				$template_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_id, 'template_id');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($template_name)){
				
			$template_name=trim($template_name);
			
			if($first==0 && ($template_name[0]!='+' && $template_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_name[0]=='+' || $template_name[0]==',') && $first==1 ){
				$template_name[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_name, 'template_name');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($template_version)){
				
			$template_version=trim($template_version);
			
			if($first==0 && ($template_version[0]!='+' && $template_version[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_version[0]=='+' || $template_version[0]==',') && $first==1 ){
				$template_version[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_version, 'template_version');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($template_author)){
				
			$template_author=trim($template_author);
			
			if($first==0 && ($template_author[0]!='+' && $template_author[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_author[0]=='+' || $template_author[0]==',') && $first==1 ){
				$template_author[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_author, 'template_author');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($template_license)){
				
			$template_license=trim($template_license);
			
			if($first==0 && ($template_license[0]!='+' && $template_license[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_license[0]=='+' || $template_license[0]==',') && $first==1 ){
				$template_license[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_license, 'template_license');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($is_default)){
				
			$is_default=trim($is_default);
			
			if($first==0 && ($is_default[0]!='+' && $is_default[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($is_default[0]=='+' || $is_default[0]==',') && $first==1 ){
				$is_default[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($is_default, 'is_default');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($main_file)){
				
			$main_file=trim($main_file);
			
			if($first==0 && ($main_file[0]!='+' && $main_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($main_file[0]=='+' || $main_file[0]==',') && $first==1 ){
				$main_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($main_file, 'main_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($xml_file)){
				
			$xml_file=trim($xml_file);
			
			if($first==0 && ($xml_file[0]!='+' && $xml_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($xml_file[0]=='+' || $xml_file[0]==',') && $first==1 ){
				$xml_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($xml_file, 'xml_file');
			
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
		
		if(!empty($template_directory)){
				
			$template_directory=trim($template_directory);
			
			if($first==0 && ($template_directory[0]!='+' && $template_directory[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_directory[0]=='+' || $template_directory[0]==',') && $first==1 ){
				$template_directory[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_directory, 'template_directory');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($template_image)){
				
			$template_image=trim($template_image);
			
			if($first==0 && ($template_image[0]!='+' && $template_image[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_image[0]=='+' || $template_image[0]==',') && $first==1 ){
				$template_image[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_image, 'template_image');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($template_unique_id)){
				
			$template_unique_id=trim($template_unique_id);
			
			if($first==0 && ($template_unique_id[0]!='+' && $template_unique_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_unique_id[0]=='+' || $template_unique_id[0]==',') && $first==1 ){
				$template_unique_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_unique_id, 'template_unique_id');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($template_page)){
				
			$template_page=trim($template_page);
			
			if($first==0 && ($template_page[0]!='+' && $template_page[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_page[0]=='+' || $template_page[0]==',') && $first==1 ){
				$template_page[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_page, 'template_page');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($template_site_id)){
				
			$template_site_id=trim($template_site_id);
			
			if($first==0 && ($template_site_id[0]!='+' && $template_site_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_site_id[0]=='+' || $template_site_id[0]==',') && $first==1 ){
				$template_site_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_site_id, 'template_site_id');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($template_domain)){
				
			$template_domain=trim($template_domain);
			
			if($first==0 && ($template_domain[0]!='+' && $template_domain[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_domain[0]=='+' || $template_domain[0]==',') && $first==1 ){
				$template_domain[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_domain, 'template_domain');
			
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
		
	}//end getTemplateList
	
	public static function removeTemplate($template_id){
		
		$template_info=self::getTemplate($template_id);
		
		if(!empty($template_info) && is_array($template_info)){
			extract($template_info);
			
			if(!empty($template_directory)){
				if(file_exists(PV_TEMPLATES.$template_directory)){
				PVFileManager::deleteDirectory(PV_TEMPLATES.$template_directory);
				}
			}//end if not empty
			
			$query="DELETE FROM ".PVDatabase::getTemplatesTableName()." WHERE template_id='$template_id'";
			PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getTemplatePositionsTableName()." WHERE template_id='$template_id'";
			PVDatabase::query($query);
		}
		
	}//end removeTemplate
	
	
	public static function selectTemplate($args){
		
		if(!empty($args) && is_array($args)){
			
			extract($args);
			
			$WHERE='';
			$first=1;
			
			if(!empty($is_default)){
				if($first==1){
					$WHERE.=" is_default='$is_default' ";
				}
				else{
					$WHERE.=" AND is_default='$is_default' ";
				}
				
				$first=0;
			}//end if
			
			if(!empty($template_unique_id)){
				if($first==1){
					$WHERE.=" template_unique_id='$template_unique_id' ";
				}
				else{
					$WHERE.=" AND template_unique_id='$template_unique_id' ";
				}
				
				$first=0;
			}//end if
			
			if(!empty($template_domain)){
				if($first==1){
					$WHERE.=" template_domain='$template_domain' ";
				}
				else{
					$WHERE.=" AND template_domain='$template_domain' ";
				}
				
				$first=0;
			}//end if
			
			
			if(!empty($template_page)){
				if($first==1){
					$WHERE.=" template_page='$template_page' ";
				}
				else{
					$WHERE.=" AND template_page='$template_page' ";
				}
				
				$first=0;
			}//end if
			
			
			if(!empty($template_site_id)){
				if($first==1){
					$WHERE.=" template_site_id='$template_site_id' ";
				}
				else{
					$WHERE.=" AND template_site_id='$template_site_id' ";
				}
				
				$first=0;
			}//end if
			
			if(!empty($WHERE)){
				$WHERE=' WHERE '.$WHERE;
			}
			$query="SELECT * FROM ".PVDatabase::getTemplatesTableName() ." $WHERE LIMIT 1";
			$result = PVDatabase::query($query);
			$row= PVDatabase::fetchArray($result);
			
			return $row;
			
		}//end
		
		
	}//end getDefaultTemplate
	
	public static function addTemplatePosition($template_id, $position){
		
		if(!empty($template_id) && !empty($position) ){
			
			$template_id=ceil($template_id);
			$position=PVDatabase::makeSafe($position);
			
			$query="SELECT template_id, position_name FROM ".PVDatabase::getTemplatePositionsTableName()." WHERE template_id='$template_id' AND position_name='$position' ";
	   	 	$result = PVDatabase::query($query);
			
	   	 	if(PVDatabase::resultRowCount($result)<=0 && !empty($position)){
	   	 		$query="INSERT INTO ".PVDatabase::getTemplatePositionsTableName()."(template_id, position_name) VALUES('$template_id', '$position')";
	   	 		PVDatabase::query($query);
			}
			
		}//end if !emoty
		
	}//end addTemplatePosition
	
	public static function getTemplatePositionList($args){
		
		if(is_array($args)){
			extract($args);
		}
		
		$first=1;
		
		$content_array=array();
			
		$WHERE_CLAUSE.='';
		
		if(!empty($template_id)){
				
			$template_id=trim($template_id);
			
			if($first==0 && ($template_id[0]!='+' && $template_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($template_id[0]=='+' || $template_id[0]==',') && $first==1 ){
				$template_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($template_id, 'template_id');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($position_name)){
				
			$position_name=trim($position_name);
			
			if($first==0 && ($position_name[0]!='+' && $position_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($position_name[0]=='+' || $position_name[0]==',') && $first==1 ){
				$position_name[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($position_name, 'position_name');
			
			$first=0;
		}//end not empty app_id
		
		
		$JOINS='';
		
		if($join_templates){
			$JOINS.=' JOIN '.PVDatabase::getTemplatesTableName().' ON '.PVDatabase::getTemplatePositionsTableName().'.template_id='.PVDatabase::getTemplatesTableName().'.template_id ';	
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
		
		$ORDER_BY=$args['order_by'];
		
		$LIMIT=$args['limit'];
		
		
		if(!empty($LIMIT)){
			$LIMIT=" limit $LIMIT ";
		}
		
		if(!empty($group_by)){
			$WHERE_CLAUSE.="GROUP BY $group_by ";
		}
		
		if(!empty($ORDER_BY)){
			$ORDER_BY="ORDER BY $ORDER_BY ";
		}
		
		if(empty($custom_select)){
			$custom_select='*';
		}
		
		
		$query="SELECT $custom_select FROM ".PVDatabase::getTemplatePositionsTableName()." $JOINS ".$WHERE_CLAUSE." $ORDER_BY $LIMIT ";
		
		$result=PVDatabase::query($query);
	
		while ($row = PVDatabase::fetchArray($result)){
			array_push($content_array, $row);
    	}//end while
    	
    	$content_array=PVDatabase::formatData($content_array);
    	
		return $content_array;
		
		
		
	}//end getTemplatePositionList
	
	public static function getTemplatePosition($template_id, $position_name){
		
		if(!empty($template_id) && !empty($position_name )){
			
			$query="SELECT * FROM ".PVDatabase::getTemplatePositionsTableName()." WHERE template_id='$template_id' AND position_name='$position_name' ";
			
	   	 	$result = PVDatabase::query($query);
			
			$row = PVDatabase::fetchArray($result);
			
			$row=PVDatabase::formatData($row);
			
			return $row;
			
		}//end not empty
		
	}//end getTemplatePosition
	
	public static function removeTemplatePosition($template_id, $position_name){
		
		if(!empty($template_id) && !empty($position_name )){
			
			$query="DELETE FROM ".PVDatabase::getTemplatePositionsTableName()." WHERE template_id='$template_id' AND position_name='$position_name' ";
	   	 	
			PVDatabase::query($query);
			
		}//end not empty
		
	}//end getTemplatePosition
	
	
	/**
	 * Display the CSS for the template that is set to default. Uses the xml for the template
	 * and searches the xml for css tags. The files in the <css></css> tags will be displayed
	 * as an <link></link>. Best used in the header.
	 */
	public static function printCurrentTemplateCss(){
		$query="SELECT template_directory, xml_file FROM ".PVDatabase::getTemplatesTableName()." WHERE is_default='1'";
		
		$result=PVDatabase::query($query);
		
		$row=PVDatabase::fetchArray($result);

		if(!empty($row)){
			
			$template_dir=ROOT.DS.'templates'.DS.$row['template_directory'];
			$xmlFile=$template_dir.$row['xml_file'];
			
			if(file_exists($xmlFile) && !empty($xmlFile)){
					$xml = simplexml_load_file($xmlFile);
					
					foreach($xml->children() as $child){
						if($child->getName()=='css'){
							?>
                            <link rel="stylesheet" href="<?php echo PV_TEMPLATES.DS.$row['template_directory'].basename($child);?>" type="text/css" charset="utf-8" />
                            <?php
							
						}
						
					}//end for each
					
			}//xml file not empty
		}
		
	}//end function
	
	/**
	 * Used for updating a string, generally a header that an ob_flushed
	 * has ouputted.
	 * 
	 * @param string buffer
	 * @param array options
	 * 
	 * @return string buffer_demimentated
	 */
	public static function updateHeader($buffer, $options=array()) {
		$defaults=array(
			'site_title'=>'{SITE_TITLE}',
			'site_keywords'=>'{SITE_KEYWORDS}',
			'site_description'=>'{SITE_DESCRIPTION}',
			'header_description' => '{HEADER_ADDITION}'
		);
		
		$options += $defaults;
		$libraries='';
	
	
		foreach (PVLibraries::$jquery_libraries_array as $value) {
			$libraries.='<script type="text/javascript" src="'.PV_JQUERY.trim($value).'"></script>';
		} 
		
		foreach (PVLibraries::$motools_libraries_array as $value) {
			 $libraries.='<script type="text/javascript" src="'.PV_MOOTOOLS.trim($value).'"></script>';
		}
		
		foreach (PVLibraries::$prototype_libraries_array as $value) {
			 $libraries.='<script type="text/javascript" src="'.PV_PROTOTYPE.trim($value).'"></script>';
		}
		
		foreach (PVLibraries::$css_files_array as $value) {
			 $libraries.='<link rel="stylesheet"  type="text/css" href="'.PV_CSS.trim($value).'">';
		}
		
		foreach (PVLibraries::$javascript_libraries_array as $value) {
			 $libraries.='<script type="text/javascript" src="'.PV_JAVASCRIPT.trim($value).'"></script>';
		}
	
		$libraries.=PVLibraries::$open_javascript;
		
		$buffer=str_replace($options['site_title'], pv_getSiteTitle() , $buffer);
		
		$buffer=str_replace($options['site_keywords'], pv_getSiteMetaTags(), $buffer);
		
		$buffer=str_replace($options['site_description'], pv_getSiteMetaDescription() , $buffer);
	
	  	return str_replace($options['header_addition'], $libraries , $buffer);
	}//end  updateHeader

	public static function getHeader($options=array()) {
		$defaults=array(
			'version'=> false,
			'append_url' =>true,
			'libraries' => '',
			'url'=>''
		);
		$options += $defaults;
		extract($options);
		
		$siteConfiguration=pv_getSiteCompleteConfiguration();
			
		$jquery = (PV_IS_ADMIN) ? PV_ADMIN_JQUERY : PV_JQUERY;
		$mootools = (PV_IS_ADMIN) ? PV_ADMIN_MOOTOOLS : PV_MOOTOOLS;
		$prototype = (PV_IS_ADMIN) ? PV_ADMIN_PROTOTYPE: PV_PROTOTYPE;
		$javascript = (PV_IS_ADMIN) ? PV_ADMIN_JAVASCRIPT: PV_JAVASCRIPT;
		$css = (PV_IS_ADMIN) ? PV_ADMIN_CSS : PV_CSS;
			
		if($options['append_url']){
			$url=pv_getSiteUrl();
		}
			
		if($options['version']){
			$version='?pvversion='.$options['version'];	
		}
			
		if($siteConfiguration['ajax_enabled']==1 && !empty($siteConfiguration['ajax_library'])){
			$libraries.='<script type="text/javascript" src="'.$url.$javascript.DS.$siteConfiguration['ajax_library'].'"></script>';
		}
			
		if($siteConfiguration['jquery_enabled']==1 && !empty($siteConfiguration['jquery_library'])){
			$libraries.'<script type="text/javascript" src="'.$url.$jquery.DS.$siteConfiguration['jquery_library'].'"></script>';
		}
			
		if($siteConfiguration['mootools_enabled']==1 && !empty($siteConfiguration['mootools_library'])){
			$libraries.'<script type="text/javascript" src="'.$url.$mootools.DS.$siteConfiguration['mootools_library'].'"></script>';
		}
			
		if($siteConfiguration['prototype_enabled']==1 && !empty($siteConfiguration['prototype_library'])){
			$libraries.'<script type="text/javascript" src="'.$url.$prototype.DS.$siteConfiguration['prototype_library'].'"></script>';
		}
			
		foreach (PVLibraries::$jquery_libraries_array as $value) {
			$libraries.='<script type="text/javascript" src="'.$url.$jquery.DS.trim($value).$version.'"></script>';
		} 
			
		foreach (PVLibraries::$motools_libraries_array as $value) {
			 $libraries.='<script type="text/javascript" src="'.$url.$mootools.DS.trim($value).$version.'"></script>';
		}
			
		foreach (PVLibraries::$prototype_libraries_array as $value) {
			 $libraries.='<script type="text/javascript" src="'.$url.$prototype.DS.trim($value).$version.'"></script>';
		}
			
		foreach (PVLibraries::$css_files_array as $value) {
			 $libraries.='<link rel="stylesheet"  type="text/css" href="'.$url.$css.DS.trim($value).$version.'">';
		}
			
		foreach (PVLibraries::$javascript_libraries_array as $value) {
			 $libraries.='<script type="text/javascript" src="'.$url.DS.$javascript.DS.trim($value).$version.'"></script>';
		}
		
		$libraries.=PVLibraries::$open_javascript;
		return $libraries;
	}//end  updateHeader
	
	public static function getJavaScriptHeader($options=array()){
		$defaults=array(
			'version'=> false,
			'append_url' =>true,
			'libraries' => '',
			'url'=>''
		);
		$options += $defaults;
		extract($options);
		
		$siteConfiguration=pv_getSiteCompleteConfiguration();
			
		$javascript = (PV_IS_ADMIN) ? PV_ADMIN_JAVASCRIPT: PV_JAVASCRIPT;
		
		if($options['append_url']){
			$url=pv_getSiteUrl();
		}
			
		if($options['version']){
			$version='?pvversion='.$options['version'];	
		}
		
		foreach (PVLibraries::$javascript_libraries_array as $value) {
				
			$libraries.='<script type="text/javascript" src="'.$url.$javascript.DS.trim($value).$version.'"></script>';
		}
		
		 return $libraries;
	}//end printJavaScriptHeader
	
	public static function getMooToolsHeader($version='', $use_url=FALSE){
		$defaults=array(
			'version'=> false,
			'append_url' =>true,
			'libraries' => '',
			'url'=>''
		);
		$options += $defaults;
		extract($options);
		
		$siteConfiguration=pv_getSiteCompleteConfiguration();
			
		$mootools = (PV_IS_ADMIN) ? PV_ADMIN_MOOTOOLS : PV_MOOTOOLS;
		
		if($options['append_url']){
			$url=pv_getSiteUrl();
		}
			
		if($options['version']){
			$version='?pvversion='.$options['version'];	
		}
			
		foreach (PVLibraries::$motools_libraries_array as $value) {
			 $libraries.='<script type="text/javascript" src="'.$url.$mootools.DS.trim($value).$version.'"></script>';
		}
			
		return $libraries;
		
	}//end printJavaScriptHeader
	
	
	public static function getPrototypeHeader($version='', $use_url=FALSE){
		$defaults=array(
			'version'=> false,
			'append_url' =>true,
			'libraries' => '',
			'url'=>''
		);
		$options += $defaults;
		extract($options);
		
		$siteConfiguration=pv_getSiteCompleteConfiguration();
			
		$prototype = (PV_IS_ADMIN) ? PV_ADMIN_PROTOTYPE: PV_PROTOTYPE;
		
		if($options['append_url']){
			$url=pv_getSiteUrl();
		}
			
		if($options['version']){
			$version='?pvversion='.$options['version'];	
		}
			
		foreach (PVLibraries::$prototype_libraries_array as $value) {
			 $libraries.='<script type="text/javascript" src="'.$url.$prototype.DS.trim($value).$version.'"></script>';
		}
			
		return $libraries;
	}//end printJavaScriptHeader
	
	
	public static function getJQueryHeader($version='', $use_url=FALSE){
		$defaults=array(
			'version'=> false,
			'append_url' =>true,
			'libraries' => '',
			'url'=>false
		);
		$options += $defaults;
		extract($options);
		
		$siteConfiguration=pv_getSiteCompleteConfiguration();
			
		$jquery = (PV_IS_ADMIN) ? PV_ADMIN_JQUERY : PV_JQUERY;
		
		if($options['append_url']){
			$url=pv_getSiteUrl();
		}
			
		if($options['version']){
			$version='?pvversion='.$options['version'];	
		}
			
		foreach (PVLibraries::$jquery_libraries_array as $value) {
			$libraries.='<script type="text/javascript" src="'.$url.$jquery.DS.trim($value).$version.'"></script>';
		} 
			
		return $libraries;
		
	}//end printJavaScriptHeader
	
	public static function getCSSHeader($version='', $use_url=FALSE){
		$defaults=array(
			'version'=> false,
			'append_url' =>true,
			'libraries' => '',
			'url'=>false
		);
		$options += $defaults;
		extract($options);
		
		$siteConfiguration=pv_getSiteCompleteConfiguration();
			
		$css = (PV_IS_ADMIN) ? PV_ADMIN_CSS : PV_CSS;
		
		if($options['append_url']){
			$url=pv_getSiteUrl();
		}
			
		if($options['version']){
			$version='?pvversion='.$options['version'];	
		}
			
		foreach (PVLibraries::$css_files_array as $value) {
			 $libraries.='<link rel="stylesheet"  type="text/css" href="'.$url.$css.DS.trim($value).$version.'">';
		}
			
		return $libraries;
		
	}//end printJavaScriptHeader
	
	
}//end class
	