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
class PVTools extends PVStaticObject {
	
	function __construct(){
		
	}//end constructor
	
	public static function includeDirectory($path_to_directory){
	
		// Loop through directory, strip out . and ..
		if ( $handle = opendir ( $path_to_directory ) ) {
  			 while ( false !== ( $file = readdir ( $handle ) ) ) {
       			if ( $file != "." && $file != ".." ) {
           			// Include or require file here
           			include_once( $path_to_directory.$file );
       		}
   		}
   			closedir($handle);
		}
	}//end includeDirectory
	
	
	/*
	Converts a boolean, such as 1 to the number true
	*/
	public static function convertNumbericBoolean($boolean){
		if($boolean==1){
			return true;	
		}
		else if($boolean==0){
			return false;	
		}
		
	}//end convertNumbericBoolean
	
	
	
	/*
	Converts the text true to 1 and
	false to 0
	*/
	public static function convertTextBoolean($boolean){
		if($boolean=="true"){
			return 1;	
		}
		else if($boolean=="false"){
			return "0";	
		}
		
		return $boolean;
	}//end convertTextBoolean
	
	
	public static function createParameterArray($params){
		$array=split("[:\n]", $params);
		$count = count($array);
		$paramarray=array();
		
		for ($i = 0; $i < $count; $i++) {
			$name=$array[$i];
			$paramarray[$name]=$array[$i+1];
			$i=$i+1;
			
		}//end for
		
		return $paramarray;
	
	}//end createrParamterArray
	
	public static function xmlToArray($params){
		$xml = simplexml_load_string($params);
		return get_object_vars($xml);
		
	}
	
	/* Accepts a string with , and seperates it into an array*/
	public static function splitCommaToArray($delimeter ,$string){
		return split ($delimeter ,$string);
	
	}
	
	
	public static function htmlEntitify($string){
		return htmlentities($string, ENT_QUOTES, 'UTF-8');
	}
	
	public static function urlEncode(){
	
		
	}//end urlEncode
	
	
	
    
    //Change on final release
	public static function generateRandomString($numOfChars = 15, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'){
	   
	    $charLength = (strlen($chars) - 1);
	
	   
	    $returnString = $chars{rand(0, $charLength)};
	    
	   
	    for ($i = 1; $i < $numOfChars; $i = strlen($returnString)){
	        
	        $newchar = $chars{rand(0, $charLength)};

	        if ($newchar != $returnString{$i - 1}) {
	        	$returnString .=  $newchar;
	        }
	    }//end for
	    
	   
	    return $returnString;
	}
	
	
	
	  
    function truncateText ($str, $length=10, $trailing='...', $strip_tags=TRUE, $allowed_tags=''){
 
   			if($strip_tags==TRUE && !empty($str)){
				$str=strip_tags($str);
			}
            // take off chars for the trailing
  
            $length-=mb_strlen($trailing);
  
            if (mb_strlen($str)> $length)
  
            {
  
               // string exceeded length, truncate and add trailing dots
  
               return mb_substr($str,0,$length).$trailing;
  
            }
  
            else {
  
               // string was already short enough, return the string
  
              $res = $str;
  
            }
			
            return $res;
      }//end truncateText
	  
	  
	public static function getCurrentUrl() {
		$current_page_url = 'http';
		 
		if ($_SERVER['HTTPS'] == 'on') { 
		 	$current_page_url .= 's';
		}
		  
		$current_page_url .= '://';
		
		if ($_SERVER['SERVER_PORT'] != '80') {
			$current_page_url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		} else {
			$current_page_url .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		}
		
		return $current_page_url;
 
	}//end getCurrentCurl
	
	  
	public static function getCurrentBaseUrl() {
		$current_page_url = 'http';
		 
		if ($_SERVER['HTTPS'] == 'on') { $current_page_url .= 's';}
			$current_page_url .= '://';
		 
		if ($_SERVER['SERVER_PORT'] != '80') {
			$current_page_url.= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$current_page_url.= $_SERVER['SERVER_NAME'];
		}

		return  $current_page_url;
 
	}//end getCurrentCurl
	  
	public static function getSiteUrl(){
		  return 'http://'.SITE_ADDRESS;
	  }//end get site URL
	  
	  
	public static function formUrlParameters($variables){
		
		$appendix='?';
		
		$first=1;
		foreach($variables as $key=>$value){
			if($first==1){
				$appendix.=$key.'='.urlencode($value);
			}
			else{
				$appendix.='&'.$key.'='.urlencode($value);
			}
			$first=0;
		}//end foreach
		
		return $appendix;
		
	}//end form url
	
	
	public static function formUrlPath($variables){
		
		$appendix='';
		
		$first=1;
		foreach($variables as $key=>$value){
			if($first==1){
				$appendix.=urlencode($value);
			}
			else{
				$appendix.='/'.urlencode($value);
			}
			$first=0;
		}//end foreach
		
		return $appendix;
		
	}//end form url
	
	
	public static function addOption($args=array()){
		$args += self::getOptionDefaults();
		$args = PVDatabase::makeSafe($args);
		extract($args);
			
		$query="INSERT INTO ".pv_getOptionsTableName()."( app_id, user_id , content_id, option_name, option_value , option_type) VALUES(  '$app_id' , '$user_id' , '$content_id' , '$option_name', '$option_value' , '$option_type' )";
		$option_id=PVDatabase::return_last_insert_query($query, "option_id", pv_getOptionsTableName() );
			
		return $option_id;
	}//end addOption
	
	public static function getOptionList($args=array()){
		$args += self::getOptionDefaults();
		$args += self::_getSqlSearchDefaults();
		$custom_where=$args['custom_where'];
		$custom_join=$args['custom_join'];
		$custom_select=$args['custom_select'];
		$args= PVDatabase::makeSafe($args);
		extract($args);
		
		$content_array=array();
		$table_name=pv_getOptionsTableName();
		$db_type=PVDatabase::getDatabaseType();
	
		$first=1;
		
		$WHERE_CLAUSE="";
		
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
		
		if(!empty($option_value)){
				
			$option_value=trim($option_value);
			
			if($first==0 && ($option_value[0]!='+' && $option_value[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($option_value[0]=='+' || $option_value[0]==',') && $first==1 ){
				$option_value[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_value, 'option_value');
			
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
		
		if(!empty($option_type)){
				
			$option_type=trim($option_type);
			
			if($first==0 && ($option_type[0]!='+' && $option_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($option_type[0]=='+' || $option_type[0]==',') && $first==1 ){
				$option_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_type, 'option_type');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($option_name)){
				
			$option_name=trim($option_name);
			
			if($first==0 && ($option_name[0]!='+' && $option_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($option_name[0]=='+' || $option_name[0]==',') && $first==1 ){
				$option_name[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_name, 'option_name');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($option_date)){
				
			$option_date=trim($option_date);
			
			if($first==0 && ($option_date[0]!='+' && $option_date[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($option_date[0]=='+' || $option_date[0]==',') && $first==1 ){
				$option_date[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_date, 'option_date');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($option_id)){
				
			$option_id=trim($option_id);
			
			if($first==0 && ($option_id[0]!='+' && $option_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($option_id[0]=='+' || $option_id[0]==',') && $first==1 ){
				$option_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_id, 'option_id');
			
			$first=0;
		}//end not empty app_id
	    	
		$JOINS='';
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=' '.$custom_where.' ';
		}
		
		if(!empty($custom_join)){
			$JOINS.=' '.$custom_join.' ';
		}
		
		if($join_apps){
			$JOINS.=" JOIN ".pv_getApplicationsTableName()." ON ".pv_getOptionsTableName().".app_id=".pv_getApplicationsTableName().".app_id ";
		}
		
		if($join_content){
			$JOINS.=" JOIN ".pv_getContentTableName()." ON ".pv_getOptionsTableName().".content_id=".pv_getContentTableName().".content_id ";
		}
		
		if($join_users){
			$JOINS.=" JOIN ".pv_getLoginTableName()." ON ".pv_getOptionsTableName().".user_id=".pv_getLoginTableName().".user_id ";
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
    	
    	$query="$prequery SELECT $prefix_args$custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
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
	}//end getOptionList
	
	
	public static function getOptionByID($option_id){
	
		if(!empty($option_id)){
			$query="SELECT option_id, app_id, user_id , content_id, option_name, option_value , option_type FROM ".pv_getOptionsTableName()." WHERE option_id= '$option_id' ";	
			$result = PVDatabase::query($query);
			
			return $row = PVDatabase::fetchArray($result);
		}//end
	
	}//end
	
	public static function updateOption($args=array()){
		$args += self::getOptionDefaults();
		$args = PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($option_id)){
			$query="UPDATE  ".pv_getOptionsTableName()." SET app_id='$app_id', user_id='$user_id' , content_id='$content_id', option_name='$option_name', option_value='$option_value' , option_type='$option_type' WHERE option_id='$option_id'";
			PVDatabase::query($query);
			return 	$option_id;
		}
		
	}//end update updateUpdate
	
	public static function setOption($args=array()){
		$args += self::getOptionDefaults();
		$args = PVDatabase::makeSafe($args);
		extract($args);
		
		$WHERE_CLAUSE="";
	    	
		if(!empty($app_id) || !empty($option_name) || !empty($option_type) || !empty($content_id) || !empty($user_id) || !empty($custom_where) || !empty($option_id)){
			$first=1;
				
			$WHERE_CLAUSE.=" WHERE ";
				
			if(!empty($app_id)){
				$app_id=PVDatabase::makeSafe($app_id);
				$WHERE_CLAUSE.=" app_id='$app_id' ";
				$first=0;
			}
				
			if(!empty($option_name)){
				$option_name=PVDatabase::makeSafe($option_name);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" option_name='$option_name' ";
				$first=0;
			}
				
			if(!empty($option_type)){
				$option_type=PVDatabase::makeSafe($option_type);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" option_type='$option_type' ";
				$first=0;
			}
				
			if(!empty($content_id)){
				$content_id=PVDatabase::makeSafe($content_id);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" content_id='$content_id' ";
				$first=0;
			}
				
			if(!empty($user_id)){
				$user_id=PVDatabase::makeSafe($user_id);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" user_id='$user_id' ";
				$first=0;
			}
				
			if(!empty($option_id)){
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" option_id='$option_id' ' ";
				$first=0;
			}	
		}
			
		$query="SELECT option_id, app_id, user_id , content_id, option_name, option_value , option_type FROM ".pv_getOptionsTableName()." $WHERE_CLAUSE ";	
		$result = PVDatabase::query($query);
		
		if(PVDatabase::resultRowCount($result) > 0){
			$query="UPDATE  ".pv_getOptionsTableName()." SET option_value='$option_value' $WHERE_CLAUSE";
			PVDatabase::query($query);
		}
		else{
			self::addOption($option_array);
		}
	
	}//setOption
	
	public static function getOption($args=array()){
		$args += self::getOptionDefaults();
		$args = PVDatabase::makeSafe($args);
		extract($args);
		
		$WHERE_CLAUSE="";
	    	
		if(!empty($app_id) || !empty($option_name) || !empty($option_type) || !empty($content_id) || !empty($user_id) || !empty($custom_where) || !empty($option_id)){
			$first=1;
				
			$WHERE_CLAUSE.=" WHERE ";
				
			if(!empty($app_id)){
				$app_id=PVDatabase::makeSafe($app_id);
				$WHERE_CLAUSE.=" app_id='$app_id' ";
				$first=0;
			}
				
			if(!empty($option_name)){
				$option_name=PVDatabase::makeSafe($option_name);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" option_name='$option_name' ";
				$first=0;
			}
				
			if(!empty($option_type)){
				$option_type=PVDatabase::makeSafe($option_type);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" option_type='$option_type' ";
				$first=0;
			}
				
			if(!empty($content_id)){
				$content_id=PVDatabase::makeSafe($content_id);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" content_id='$content_id' ";
				$first=0;
			}
				
			if(!empty($user_id)){
				$user_id=PVDatabase::makeSafe($user_id);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" user_id='$user_id' ";
				$first=0;
			}
				
			if(!empty($option_id)){
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" option_id='$option_id' ' ";
				$first=0;
			}		
		}
		
		$query="SELECT option_id, app_id, user_id , content_id, option_name, option_value , option_type FROM ".pv_getOptionsTableName()." $WHERE_CLAUSE ";	
		$result = PVDatabase::query($query);
	
		if(PVDatabase::resultRowCount($result) > 0){
			$row=PVDatabase::fetchArray($result);
			
			return $row;
		}
		
		return array();
	
	}//setOption
	
	public static function getOptionValue($args=array()){
		$row=self::getOption($args);
		
		if(isset($row['option_value'])){
			return $row['option_value'];
		}
	}//end getOption
	
	public static function deleteOption($option_id, $deleteChildrenOptions=false){
		
		$option_id=ceil($option_id);
		
		if(!empty($option_id)){
			
			$query="DELETE FROM ".pv_getOptionsTableName()." WHERE option_id='$option_id' ";
			PVDatabase::query($query);
		}//end if
	
	}//end getOption
	
	private static function parseSQLArrayOperators($args, $content_term){
		$operator=$args['operator'];
		
		if(empty($operator)){
			$operator=' AND ';
		}
		$SQL='';
		$mark='';
		
		foreach($args as  $value){
			if($key!='operator'){
				$SQL.=$mark.' '.$content_term.'=\''.PVDatabase::makeSafe($value).'\' ';
				$mark=$operator;
			}//end operator
		
		}//end foreach
		
		return $SQL;
	}//end parseSQLArrayOperators
	
	public static function parseSQLOperators($string, $content_term, $encapsulate=TRUE){
		$string=trim($string);
		$string=PVDatabase::makeSafe($string);
		
		//$string.=$content_term
		/*
		if( strstr($string, '!') != 1){
			$string=$content_term.'='
		}
		$string=str_replace('+', ' AND '.$content_term.'=', $string );
		$string=str_replace(',', ' OR '.$content_term.'=', $string );*/
		
	
		
		$length=strlen($string);
		
		$ADD_PREFIX=true;
		$output='';
		for($i=0; $i<$length; $i++){
			
		
			if($string[$i]=='!'){
				
				$output.=' '.$content_term.'!=\'';
			
				if($i==0){
					$ADD_PREFIX=false;	
				}
			}
			else if($string[$i]=='+'){
				if( $string[$i+1]!='!'){
					$output.=' AND '.$content_term.'=\'';
				}
				else {
					$output.=' AND ';
				}
				
				
			}
			else if($string[$i]==','){
				if( $string[$i+1]!='!'){
					$output.=' OR '.$content_term.'=\'';
					
				} else {
					$output.=' OR ';
				}
				
			}
			
			
			if($string[$i]!='!' && $string[$i]!='+' && $string[$i]!=',' ){
				
				$output.=$string[$i];
				
				if(@$string[$i+1]==',' || @$string[$i+1]=='+' || @$string[$i+1]=='!' || $i==$length || $i==$length-1){
					$output.='\'';
					}
			}
			
			
		
		}//end for
		
		
		if($ADD_PREFIX==true){
				$output=$content_term.'=\''.$output;
			}
		
		if($encapsulate){
			$output='('.$output.')';
		}
		
		return $output;
		
	}//end parseSQLOperator

	private static function getOptionDefaults() {
		$defaults=array(
			'option_id'=>0,
			'app_id'=>0,
			'user_id'=>'',
			'content_id'=>0,
			'option_name'=>'',
			'option_value'=>'',
			'option_type'=>'',
			'option_date'=>''
		);
		
		return $defaults;
	}	
}//end tools
?>