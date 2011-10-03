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
class PVComments extends PVStaticObject {
	
	public static function addComment($args=array()){
		$args += self::getCommentDefaults();
		$args= PVDatabase::makeSafe($args);
		extract($args);
			
		if(empty($comment_date)){
			$comment_date=date("Y-m-d H:i:s", time()) ;
		}
			
		$owner_ip=$_SERVER['REMOTE_ADDR'];
			
		
		$query="INSERT INTO ".PVDatabase::getContentCommentsTableName()."( content_id, owner_id , owner_ip, comment_date , comment_approved , comment_title, comment_text, comment_parent, comment_author, comment_author_email, comment_author_website, comment_type) VALUES( '$content_id' , '$owner_id' , '$owner_ip', '$comment_date' , '$comment_approved' , '$comment_title', '$comment_text', '$comment_parent', '$comment_author', '$comment_author_email', '$comment_author_website', '$comment_type' )";
		$comment_id=PVDatabase::return_last_insert_query($query, "comment_id", PVDatabase::getContentCommentsTableName() );
		
		return $comment_id;
	}//end addComment
	
	
	public static function getComment($comment_id){
		$comment_id=PVDatabase::makeSafe($comment_id);
		
		if(!empty($comment_id)){
			$query="SELECT * FROM ".PVDatabase::getContentCommentsTableName()." WHERE comment_id='$comment_id' ";
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}
	}//end getComment
	
	public static function getCommentList($args=array()){
		$args += self::getCommentDefaults();
		$args += self::_getSqlSearchDefaults();
		$custom_where=$args['custom_where'];
		$custom_join=$args['custom_join'];
		$custom_select=$args['custom_select'];
		$args= PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);
		
		$content_array=array();
		$table_name=PVDatabase::getContentCommentsTableName();
		$db_type=PVDatabase::getDatabaseType();
		
		$first=1;
			
		$WHERE_CLAUSE="";
		
		if(!empty($comment_id)){
				
			$comment_id=trim($comment_id);
			
			if($first==0 && ($comment_id[0]!='+' && $comment_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_id[0]=='+' || $comment_id[0]==',') && $first==1 ){
				$comment_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_id, 'comment_id');
			
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
		
		if(!empty($owner_id)){
				
			$owner_id=trim($owner_id);
			
			if($first==0 && ($owner_id[0]!='+' && $owner_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($owner_id[0]=='+' || $owner_id[0]==',') && $first==1 ){
				$owner_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($owner_id, 'owner_id');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($owner_ip)){
				
			$owner_ip=trim($owner_ip);
			
			if($first==0 && ($owner_ip[0]!='+' && $owner_ip[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($owner_ip[0]=='+' || $owner_ip[0]==',') && $first==1 ){
				$owner_ip[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($owner_ip, 'owner_ip');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($comment_date)){
				
			$comment_date=trim($comment_date);
			
			if($first==0 && ($comment_date[0]!='+' && $comment_date[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_date[0]=='+' || $comment_date[0]==',') && $first==1 ){
				$comment_date[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_date, 'comment_date');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($comment_approved)){
				
			$comment_approved=trim($comment_approved);
			
			if($first==0 && ($comment_approved[0]!='+' && $comment_approved[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_approved[0]=='+' || $comment_approved[0]==',') && $first==1 ){
				$comment_approved[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_approved, 'comment_approved');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($comment_title)){
				
			$comment_title=trim($comment_title);
			
			if($first==0 && ($comment_title[0]!='+' && $comment_title[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_title[0]=='+' || $comment_title[0]==',') && $first==1 ){
				$comment_title[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_title, 'comment_title');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($comment_text)){
				
			$comment_text=trim($comment_text);
			
			if($first==0 && ($comment_text[0]!='+' && $comment_text[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_text[0]=='+' || $comment_text[0]==',') && $first==1 ){
				$comment_text[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_text, 'comment_text');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($comment_parent) ){
				
			$comment_parent=trim($comment_parent);
			
			if($first==0 && ($comment_parent[0]!='+' && $comment_parent[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_parent[0]=='+' || $comment_parent[0]==',') && $first==1 ){
				$comment_parent[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_parent, 'comment_parent');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($comment_author)){
				
			$comment_author=trim($comment_author);
			
			if($first==0 && ($comment_author[0]!='+' && $comment_author[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_author[0]=='+' || $comment_author[0]==',') && $first==1 ){
				$comment_author[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_author, 'comment_author');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($comment_author_email)){
				
			$comment_author_email=trim($comment_author_email);
			
			if($first==0 && ($comment_author_email[0]!='+' && $comment_author_email[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_author_email[0]=='+' || $comment_author_email[0]==',') && $first==1 ){
				$comment_author_email[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_author_email, 'comment_author_email');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($comment_author_website)){
				
			$comment_author_website=trim($comment_author_website);
			
			if($first==0 && ($comment_author_website[0]!='+' && $comment_author_website[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_author_website[0]=='+' || $comment_author_website[0]==',') && $first==1 ){
				$comment_author_website[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_author_website, 'comment_author_website');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($comment_type)){
				
			$comment_type=trim($comment_type);
			
			if($first==0 && ($comment_type[0]!='+' && $comment_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($comment_type[0]=='+' || $comment_type[0]==',') && $first==1 ){
				$comment_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($comment_type, 'comment_type');
			
			$first=0;
		}//end not empty app_id
		
		$JOINS='';
		
		if(!empty($custom_join)){
			$JOINS.=" $custom_join ";
		}
		
		if($join_users){
			$JOINS.=" JOIN ".PVDatabase::getUsersTableName()." ON ".PVDatabase::getUsersTableName().".user_id=".PVDatabase::getContentCommentsTableName().".owner_id ";	
		}
		
		if($join_content){
			$JOINS.=" JOIN ".PVDatabase::getContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getContentCommentsTableName().".content_id ";	
		}
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=" $custom_where ";
		}
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
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
	
	}//end
	
	public static function updateComment($args=array()){
		$args += self::getCommentDefaults();
		$args= PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($comment_id)){
			
			$query="UPDATE ".PVDatabase::getContentCommentsTableName()." SET owner_id='$owner_id', comment_date='$comment_date', comment_approved='$comment_approved', comment_title='$comment_title', comment_parent='$comment_parent', comment_author='$comment_author', comment_author_email='$comment_author_email', comment_author_website='$comment_author_website', content_id='$content_id', comment_text='$comment_text' WHERE comment_id='$comment_id'  ";
			PVDatabase::query($query);
		}//end updateComment
	
	}//end updateComment
	
	public static function deleteComment($comment_id, $deleteChildrenComments=false){
		
		if(!empty($comment_id)){
			$query="DELETE FROM ".PVDatabase::getContentCommentsTableName()." WHERE comment_id='$comment_id'";
			PVDatabase::query($query);
			
			if($deleteChildrenComments==true){
				$query="SELECT comment_id FROM ".PVDatabase::getContentCommentsTableName()." WHERE comment_parent='$comment_id' ";
				
				$result = PVDatabase::query($query);
	
				while ($row = PVDatabase::fetchArray($result)){
					self::deleteComment($row['comment_id'], $deleteChildrenComments);
				}//end while
			}//endif deleteChildren
		
		}//end if not empty
	
	}//end deleteComenet
	
	private static function getCommentDefaults() {
		$defaults=array(
			'comment_id'=>0,
			'content_id'=>0,
			'owner_id'=>0,
			'owner_ip'=>'',
			'comment_date'=>'',
			'comment_approved'=>0,
			'comment_title'=>'',
			'comment_text'=>'',
			'comment_parent'=>0,
			'comment_author'=>'',
			'comment_author_email'=>'',
			'comment_author_website'=>'',
			'comment_type'=>''
		);
		
		return $defaults;
	}
	
}//end class