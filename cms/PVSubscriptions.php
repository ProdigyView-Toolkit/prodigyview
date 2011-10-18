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
class PVSubscriptions extends PVStaticObject{
	
	/**
	 * Adds a subscription to the database. Fields set are based upon the passed parameters.
	 * 
	 * @param array $args An array of arguements that is used to define the subscription.
	 * 			-'content_id' _id_ : The id of the content this subscription is associated with
	 * 			-'comment_id' _id_ : The id of the comment this subscription is assoicated with
	 * 			-'user_id' _id_: The id of the user this subscription is associated with
	 * 			-'app_id' _id_: The id of the application this subscription is associated with
	 * 			-'subscription_type' _string_: The type of subscription this is considered
	 * 			-'subscription_approved' _boolean_: If the subscription has been approved or not
	 * 			-'subscription_start_date' _date/time_: The start date of the subscription
	 * 			-'subscription_end_date' _date/time_: The end date of the subscription
	 * 			-'subscription_active' _boolean_: If the subscription is active or not
	 * 
	 * @return id $subscription_id The id of the newly created subscription
	 * @access public
	 */
	public static function addSubscription($args=array()){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::getSubscriptionDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args);
		$args= PVDatabase::makeSafe($args);
		extract($args);
		
		if(empty($subscription_start_date)){
			$subscription_start_date=date("Y-m-d H:i:s", time()) ;
		}
		
		if(empty($subscription_end_date)){
			$subscription_end_date=date("Y-m-d H:i:s", time()) ;
		}
		
		$user_ip=$_SERVER['REMOTE_ADDR'];
		$query="INSERT INTO ".PVDatabase::getSubscriptionTableName()."(content_id, comment_id, user_id , app_id, subscription_type, subscription_approved , subscription_start_date, subscription_end_date, user_ip, subscription_active) VALUES( '$content_id', '$comment_id', '$user_id' , '$app_id', '$subscription_type', '$subscription_approved' , '$subscription_start_date', '$subscription_end_date', '$user_ip', '$subscription_active')";
		$subscription_id=PVDatabase::return_last_insert_query($query, 'subscription_id' , PVDatabase::getSubscriptionTableName());
		self::_notify('PVSubscriptions::addSubscription', $subscription_id, $args);
		
		return $subscription_id;
		
	}//end addContentSubscription
	
	/**
	 * Adds a unique subscription to the database. Checks on the following fields to see if the subscription
	 * exist or not: 'user_id', 'comment_id', 'content_id', 'app_id', 'subscription_type',
	 * 
	 * @param array $args An array of arguements that is used to define the subscription.
	 * 			-'content_id' _id_ : The id of the content this subscription is associated with
	 * 			-'comment_id' _id_ : The id of the comment this subscription is assoicated with
	 * 			-'user_id' _id_: The id of the user this subscription is associated with
	 * 			-'app_id' _id_: The id of the application this subscription is associated with
	 * 			-'subscription_type' _string_: The type of subscription this is considered
	 * 			-'subscription_approved' _boolean_: If the subscription has been approved or not
	 * 			-'subscription_start_date' _date/time_: The start date of the subscription
	 * 			-'subscription_end_date' _date/time_: The end date of the subscription
	 * 			-'subscription_active' _boolean_: If the subscription is active or not
	 * 
	 * @return id $subscription_id The id of the subscription if no matches were found. Otherwise returns false
	 * @access public
	 */
	public static function addUniqueSubscription($args=array()){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::getSubscriptionDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args);
		$args= PVDatabase::makeSafe($args);
		extract($args);
		
		if(empty($subscription_start_date)){
			$subscription_start_date=date("Y-m-d H:i:s", time()) ;
		}
		
		if(empty($subscription_end_date)){
			$subscription_end_date=date("Y-m-d H:i:s", time()) ;
		}
		
		$user_ip=$_SERVER['REMOTE_ADDR'];
		
		$query="SELECT subscription_id FROM ".PVDatabase::getSubscriptionTableName()." WHERE content_id='$content_id' AND comment_id='$comment_id' AND user_id='$user_id' AND app_id='$app_id' AND subscription_type='$subscription_type' ";
		$result=PVDatabase::query($query);
		
		if(PVDatabase::resultRowCount($result)<=0){
		
			$query="INSERT INTO ".PVDatabase::getSubscriptionTableName()."(content_id, comment_id, user_id , app_id, subscription_type, subscription_approved , subscription_start_date, subscription_end_date, user_ip, subscription_active) VALUES( '$content_id', '$comment_id', '$user_id' , '$app_id', '$subscription_type', '$subscription_approved' , '$subscription_start_date', '$subscription_end_date', '$user_ip', '$subscription_active')";
			$subscription_id=PVDatabase::return_last_insert_query($query, 'subscription_id', PVDatabase::getSubscriptionTableName());
			self::_notify('PVSubscriptions::addUniqueSubscription', $subscription_id, $args);
			
			return $subscription_id;
		}
		return false;
	}//end addContentSubscription
	
	
	/**
	 * Searches through the subscriptions to find subscriptions that match the passed arguements. Uses ProdigyView
	 * Standard Search Query.
	 * 
	 * @param array $args An array of arguements that is used to define the subscription.
	 * 			-'content_id' _id_ : The id of the content this subscription is associated with
	 * 			-'comment_id' _id_ : The id of the comment this subscription is assoicated with
	 * 			-'user_id' _id_: The id of the user this subscription is associated with
	 * 			-'app_id' _id_: The id of the application this subscription is associated with
	 * 			-'subscription_type' _string_: The type of subscription this is considered
	 * 			-'subscription_approved' _boolean_: If the subscription has been approved or not
	 * 			-'subscription_start_date' _date/time_: The start date of the subscription
	 * 			-'subscription_end_date' _date/time_: The end date of the subscription
	 * 			-'subscription_active' _boolean_: If the subscription is active or not
	 * 			-'subscription_id' _id_ The id of the subscription
	 * 			-'user_ip' _string_: The ip of the user that created the subscritpion
	 * 			-'join_users' _boolean_: Joins a user's data on ther user id
	 * 			-'join_content' _boolean_: Joins content based the id of the content
	 * 			-'join_comments' _boolean_: Join comments based on the id of the comment
	 * 			-'join_apps' _booleans_: Join applications based on the application's id.
	 * 
	 * @return array $subscriptions Returns an array of subscriptions which are also arrays
	 * @access public
	 */
	public static function getSubscriptionList($args=array()){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::getSubscriptionDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args);
		$custom_where=$args['custom_where'];
		$custom_join=$args['custom_join'];
		$custom_select=$args['custom_select'];
		$args= PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);
		
		$first=1;
		
		$content_array=array();
		$table_name=PVDatabase::getSubscriptionTableName();
		$db_type=PVDatabase::getDatabaseType();
			
		$WHERE_CLAUSE='';
		
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
		
		if(!empty($subscription_type)){
				
			$subscription_type=trim($subscription_type);
			
			if($first==0 && ($subscription_type[0]!='+' && $subscription_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($subscription_type[0]=='+' || $subscription_type[0]==',') && $first==1 ){
				$subscription_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($subscription_type, 'subscription_type');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($subscription_approved)){
				
			$subscription_approved=trim($subscription_approved);
			
			if($first==0 && ($subscription_approved[0]!='+' && $subscription_approved[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($subscription_approved[0]=='+' || $subscription_approved[0]==',') && $first==1 ){
				$subscription_approved[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($subscription_approved, 'subscription_approved');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($subscription_active)){
				
			$subscription_active=trim($subscription_active);
			
			if($first==0 && ($subscription_active[0]!='+' && $subscription_active[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($subscription_active[0]=='+' || $subscription_active[0]==',') && $first==1 ){
				$subscription_active[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($subscription_active, 'subscription_active');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($subscription_id)){
				
			$subscription_id=trim($subscription_id);
			
			if($first==0 && ($subscription_id[0]!='+' && $subscription_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($subscription_id[0]=='+' || $subscription_id[0]==',') && $first==1 ){
				$subscription_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($subscription_id, 'subscription_id');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($user_ip)){
				
			$user_ip=trim($user_ip);
			
			if($first==0 && ($user_ip[0]!='+' && $user_ip[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($user_ip[0]=='+' || $user_ip[0]==',') && $first==1 ){
				$user_ip[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($user_ip, 'user_ip');
			
			$first=0;
		}//end not empty app_id
		
		$JOINS='';
		
		if(!empty($custom_join)){
			$JOINS.=" $custom_join ";
		}
		
		if($join_users == true){
			$JOINS.=" JOIN ".PVDatabase::getLoginTableName()." ON ".PVDatabase::getLoginTableName().".user_id=".PVDatabase::getSubscriptionTableName().".user_id ";	
		}
		
		
		if($join_content == true){
			$JOINS.=" JOIN ".PVDatabase::getContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getSubscriptionTableName().".content_id ";	
		}
		
		if($join_comments == true){
			$JOINS.=" JOIN ".PVDatabase::getContentCommentsTableName()." ON ".PVDatabase::getContentCommentsTableName().".comment_id=".PVDatabase::getSubscriptionTableName().".comment_id ";	
		}
		
		if($join_applications == true){
			$JOINS.=" JOIN ".PVDatabase::getApplicationsTableName()." ON ".PVDatabase::getApplicationsTableName().".app_id=".PVDatabase::getSubscriptionTableName().".app_id ";	
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
		self::_notify('PVSubscriptions::getSubscriptionList', $content_array, $args);
		
    	return $content_array;
	
	}//end getUserSubscriptionList
	
	/**
	 * Retrieves a subscription's data based upon the id of the subscription.
	 * 
	 * @param id $subscription_id The id of the subscription to be retrieved
	 * 
	 * @return array $subscription The subscription's data
	 * @access public
	 */
	public static function getSubscription($subscription_id){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $subscription_id);
		
		$subscription_id = self::_applyFilter( get_class(), __FUNCTION__ , $subscription_id);
		
		if(!empty($subscription_id)){
			$subscription_id=PVDatabase::makeSafe($subscription_id);
			
			$query="SELECT * FROM ".PVDatabase::getSubscriptionTableName()." WHERE subscription_id='$subscription_id' ";
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			self::_notify('PVSubscriptions::getSubscription', $row);
			
			return $row;
		}
	}//end getUserSubscribtion
	
	/**
	 * Updates a subscription based on the subscription's id
	 * 
	 * @param array $args The arguements that will be used to define the subscription being updated.
	 * 			-'content_id' _id_ : The id of the content this subscription is associated with
	 * 			-'comment_id' _id_ : The id of the comment this subscription is assoicated with
	 * 			-'user_id' _id_: The id of the user this subscription is associated with
	 * 			-'app_id' _id_: The id of the application this subscription is associated with
	 * 			-'subscription_type' _string_: The type of subscription this is considered
	 * 			-'subscription_approved' _boolean_: If the subscription has been approved or not
	 * 			-'subscription_start_date' _date/time_: The start date of the subscription
	 * 			-'subscription_end_date' _date/time_: The end date of the subscription
	 * 			-'subscription_active' _boolean_: If the subscription is active or not
	 * 			-'subscription_id' _id_: The id of the subscriptionw which is used to determine which subscription to update.
	 * 
	 * @return void
	 * @access public
	 */
	public static function updateSubscription($args=array()){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::getSubscriptionDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args);
		$args= PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($subscription_id)){
		
			if(empty($subscription_start_date)){
				$subscription_start_date=date("Y-m-d H:i:s", time()) ;
			}
			
			if(empty($subscription_end_date)){
				$subscription_end_date=date("Y-m-d H:i:s", time()) ;
			}
			
			$user_ip=$_SERVER['REMOTE_ADDR'];
			
			$query="UPDATE ".PVDatabase::getSubscriptionTableName()." SET content_id='$content_id', comment_id='$comment_id', user_id='$user_id' , app_id='$app_id' , subscription_type='$subscription_type', subscription_approved='$subscription_approved' , subscription_active='$subscription_active', subscription_start_date='$subscription_start_date', subscription_end_date='$subscription_end_date' WHERE  subscription_id='$subscription_id'";
			PVDatabase::query($query);
			self::_notify('PVSubscriptions::updateSubscription', $args);
		}//end if not empty
	}//end updateUserSubscription
	
	/**
	 * Removes a subscription from the database based upon the subscription's id.
	 * 
	 * @param id $subscription_id The id of the subscription to be deleted
	 * 
	 * @return void
	 * @access public
	 */
	public static function deleteSubscription($subscription_id){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $subscription_id);
		
		$subscription_id = self::_applyFilter( get_class(), __FUNCTION__ , $subscription_id);
		
		if(!empty($subscription_id)){
			$subscription_id=PVDatabase::makeSafe($subscription_id);
			$query="DELETE FROM ".PVDatabase::getSubscriptionTableName()." WHERE subscription_id='$subscription_id'";
			
			PVDatabase::query($query);
			self::_notify('PVSubscriptions::deleteSubscription', $subscription_id);
		}//end if
	}//end deleteUserSubscription
	
	private static function getSubscriptionDefaults() {
		$defaults=array(
			'subscription_id'=>0,
			'content_id'=>0,
			'comment_id'=>0,
			'app_id'=>0,
			'user_id'=>0,
			'subscription_type'=>'',
			'subscription_approved'=>0,
			'subscription_active'=>0,
			'subscription_date'=>'',
			'subscription_start_date'=>'',
			'subscription_end_date'=>'',
			'user_ip'=>''
		);
		
		return $defaults;
	}
	
}//end class
	