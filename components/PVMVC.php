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

class PVMVC extends PVStaticObject{
	
	public static function installMVC($args){
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$is_current_mvc=ceil($is_current_mvc);
		$autoload_mvc=ceil($autoload_mvc);
		
		if(!PVValidator::isDouble($mvc_version) && !PVValidator::isInteger($mvc_version)){
			$mvc_version=0;
		}
		
		if(!empty($mvc_unique_id)){
			
			$mvc_info=self::getMVCInfo($mvc_unique_id);
			
			if(empty($mvc_info)){
			$query="INSERT INTO ".PVDatabase::getMVCTableName()."(mvc_unique_id, mvc_name, mvc_description, mvc_author,  mvc_website, mvc_license, mvc_version, mvc_directory, mvc_file, mvc_object, is_current_mvc, autoload_mvc) VALUES( '$mvc_unique_id', '$mvc_name', '$mvc_description', '$mvc_author', '$mvc_website', '$mvc_license', '$mvc_version', '$mvc_directory', '$mvc_file', '$mvc_object', '$is_current_mvc', '$autoload_mvc')";
			
			}
			else{
				$query="UPDATE ".PVDatabase::getMVCTableName()." SET  mvc_name='$mvc_name', mvc_description='$mvc_description' , mvc_author='$mvc_author',  mvc_website='$mvc_website', mvc_license='$mvc_license', mvc_version='$mvc_version', mvc_directory='$mvc_directory', mvc_file='$mvc_file', mvc_object='$mvc_object', is_current_mvc='$is_current_mvc', autoload_mvc='$autoload_mvc' WHERE mvc_unique_id='$mvc_unique_id' ";
			}
			
			
			PVDatabase::query($query);	
			
		}
	}//end installMVC
	
	
	public static function initiliazeMVC($mvc_unique_id){
		
		if(!empty($mvc_unique_id)){
			$mvc_info=self::getMVCInfo($mvc_unique_id);
			
			$mvc_file=PV_MVC.$mvc_info['mvc_directory'].$mvc_info['mvc_file'];
			
			if(file_exists($mvc_file)){
				include($mvc_file);
			}
		}
		
	}//end initiliazeMVC
	
	public static function getMVCInfo($mvc_unique_id){
		
		if(!empty($mvc_unique_id)){
			
			$mvc_unique_id=PVDatabase::makeSafe($mvc_unique_id);
			
			$query="SELECT * FROM ".PVDatabase::getMVCTableName()." WHERE mvc_unique_id='$mvc_unique_id'";
			$result=PVDatabase::query($query);
			
			$row=PVDatabase::fetchArray($result);
			
			return $row;
		}
		
	}//end getMVCInfo
	
	
	public static function getMVCList($args){
		
		if(is_array($args)){
			$custom_where=$args['custom_where'];
			$custom_join=$args['custom_join'];
			extract($args, EXTR_SKIP);
		}
		
		
		$first=1;
			
		$content_array=array();
		$table_name=PVDatabase::getMVCTableName();
		$db_type=PVDatabase::getDatabaseType();
				
		$WHERE_CLAUSE.='';
			
		if(!empty($mvc_unique_id) || $mvc_unique_id==='0'){
					
			$mvc_unique_id=trim($mvc_unique_id);
				
			if($first==0 && ($mvc_unique_id[0]!='+' && $mvc_unique_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_unique_id[0]=='+' || $mvc_unique_id[0]==',') && $first==1 ){
				$mvc_unique_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_unique_id, 'mvc_unique_id');
				
			$first=0;
		}//end not empty app_id
		
		if(!empty($mvc_name) || $mvc_name==='0' ){
					
			$mvc_name=trim($mvc_name);
				
			if($first==0 && ($mvc_name[0]!='+' && $mvc_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_name[0]=='+' || $mvc_name[0]==',') && $first==1 ){
				$mvc_name[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_name, 'mvc_name');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mvc_description) || $mvc_description==='0' ){
					
			$mvc_description=trim($mvc_description);
				
			if($first==0 && ($mvc_description[0]!='+' && $mvc_description[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_description[0]=='+' || $mvc_description[0]==',') && $first==1 ){
				$mvc_description[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_description, 'mvc_description');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mvc_author)){
					
			$mvc_author=trim($mvc_author);
				
			if($first==0 && ($mvc_author[0]!='+' && $mvc_author[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_author[0]=='+' || $mvc_author[0]==',') && $first==1 ){
				$mvc_author[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_author, 'mvc_author');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mvc_website)){
					
			$mvc_website=trim($mvc_website);
				
			if($first==0 && ($mvc_website[0]!='+' && $mvc_website[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_website[0]=='+' || $mvc_website[0]==',') && $first==1 ){
				$mvc_website[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_website, 'mvc_website');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mvc_license)){
					
			$mvc_license=trim($mvc_license);
				
			if($first==0 && ($mvc_license[0]!='+' && $mvc_license[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_license[0]=='+' || $mvc_license[0]==',') && $first==1 ){
				$mvc_license[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_license, 'mvc_license');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mvc_version)){
					
			$mvc_version=trim($mvc_version);
				
			if($first==0 && ($mvc_version[0]!='+' && $mvc_version[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_version[0]=='+' || $mvc_version[0]==',') && $first==1 ){
				$mvc_version[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_version, 'mvc_version');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mvc_directory)){
					
			$mvc_directory=trim($mvc_directory);
				
			if($first==0 && ($mvc_directory[0]!='+' && $mvc_directory[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_directory[0]=='+' || $mvc_directory[0]==',') && $first==1 ){
				$mvc_directory[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_directory, 'mvc_directory');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mvc_file)){
					
			$mvc_file=trim($mvc_file);
				
			if($first==0 && ($mvc_file[0]!='+' && $mvc_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_file[0]=='+' || $mvc_file[0]==',') && $first==1 ){
				$mvc_file[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_file, 'mvc_file');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mvc_object)){
					
			$mvc_object=trim($mvc_object);
				
			if($first==0 && ($mvc_object[0]!='+' && $mvc_object[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($mvc_object[0]=='+' || $mvc_object[0]==',') && $first==1 ){
				$mvc_object[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mvc_object, 'mvc_object');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($is_current_mvc)){
					
			$is_current_mvc=trim($is_current_mvc);
				
			if($first==0 && ($is_current_mvc[0]!='+' && $is_current_mvc[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($is_current_mvc[0]=='+' || $is_current_mvc[0]==',') && $first==1 ){
				$is_current_mvc[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($is_current_mvc, 'is_current_mvc');
				
			$first=0;
		}//end not empty app_id
		
		if(!empty($autoload_mvc)){
					
			$autoload_mvc=trim($autoload_mvc);
				
			if($first==0 && ($autoload_mvc[0]!='+' && $autoload_mvc[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($autoload_mvc[0]=='+' || $autoload_mvc[0]==',') && $first==1 ){
				$autoload_mvc[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($autoload_mvc, 'autoload_mvc');
				
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
	
	public static function deleteMVC($mvc_unique_id){
		
		if(!empty($mvc_unique_id)){
			$mvc_info=self::getMVCInfo($mvc_unique_id);
			
			PVFileManager::deleteDirectory(PV_MVC.$mvc_info['mvc_directory']);
			
			$mvc_unique_id=PVDatabase::makeSafe($mvc_unique_id);
			
			$query="DELETE FROM ".PVDatabase::getMVCTableName()." WHERE mvc_unique_id='$mvc_unique_id'";
			$result=PVDatabase::query($query);
			
			$row=PVDatabase::fetchArray($result);
			
			return $row;
		}
		
	}//end deleteMVC
	
}//end class
	