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

$PV_VERSION=1.12;
function pv_exec($app, $command, $params, $new_object=FALSE){
	
	return PVApplications::pv_exec($app, $command, $params, $new_object);
}//end pv_exec


function pv_exec_admin($app, $command, $params, $new_object=FALSE){
	
	return PVApplications::pv_exec_admin($app, $command, $params, $new_object);
	
}//end pv_exec

/**
 * Database Functions
 * These functions are global functions for interacting with the date
 */

	function pv_query($query){
		return PVDatabase::query($query);
	}
	
	function pv_return_last_insert_query($query, $returnField='', $tableName='') {
		return PVDatabase::return_last_insert_query($query, $returnField, $tableName);
	}
	
	function pv_resultRowCount($result){
		return PVDatabase::resultRowCount($result);
	}
	
	function pv_fetchArray($result){
		return PVDatabase::fetchArray($result);
	}
	
	function pv_makeSafe($string) {
		return PVDatabase::makeSafe($string);
	}
	
	function pv_closeDB(){
		return PVDatabase::closeDB();
	}
	
	function pv_getSchema(){
		return PVDatabase::getSchema();
	}
	
	function pv_clearTableData($tablename, $extra_options=""){
		return PVDatabase::clearTableData($tablename, $extra_options="");
	}
	
	function pv_tableExist($tablename){
		return PVDatabase::tableExist($tablename);
	}
	
	function pv_columnExist($table_name, $field_name){
		return PVDatabase::columnExist($table_name, $field_name);
	}
	
	function pv_formatData($string){
		return PVDatabase::formatData($string);
	}
	
	function pv_getDatabaseType(){
		return PVDatabase::getDatabaseType();
	}
	
	
	function pv_getSQLRandomOperator(){
		return PVDatabase::getSQLRandomOperator();
	}
	
	function pv_dbAverageFunction($field){
		return PVDatabase::dbAverageFunction($field);
	}
	
	function pv_getPagininationOffset($table, $join_clause='', $where_clause='', $current_page=0, $results_per_page=20,$order_by=''){
		return PVDatabase::getPagininationOffset($table, $join_clause, $where_clause, $current_page, $results_per_page, $order_by);
	}
	
	function pv_getDatabaseLink(){
		return PVDatabase::getDatabaseLink();
	}
	
	function pv_preparedQuery($query, $data, $formats=''){
		return PVDatabase::preparedQuery($query, $data, $formats);
	}
	
	function pv_preparedInsert($table_name, $data, $formats=''){
		return PVDatabase::preparedInsert($table_name, $data, $formats);
	}
	
	function pv_preparedReturnLastInsert($table_name, $returnField, $returnTable,  $data, $formats=''){
		return PVDatabase::preparedReturnLastInsert($table_name, $returnField, $returnTable,  $data, $formats);
	}
	
	function pv_preparedSelect($query, $data, $formats=''){
		return PVDatabase::preparedSelect($query, $data, $formats);
	}
	
	function pv_preparedUpdate($table, $data, $wherelist, $formats='', $whereformats=''){
		return PVDatabase::preparedUpdate($table, $data, $wherelist, $formats, $whereformats);
	}
	
	function pv_preparedDelete($table, $wherelist, $whereformats=''){
		return PVDatabase::preparedDelete($table, $wherelist, $whereformat);
	}
	
	function pv_getApplicationPermissionsTableName(){
		return PVDatabase::getApplicationPermissionsTableName();
	}
	
	function pv_getAccessLevelsTableName(){
		return PVDatabase::getAccessLevelsTableName();
	}
	
	function pv_getApplicationsTableName(){
		return PVDatabase::getApplicationsTableName();
	}
	
	function pv_getContainersTableName(){
		return PVDatabase::getContainersTableName();
	}
	
	
	function pv_getContainerModulesTableName(){
		return PVDatabase::getContainerModulesTableName();
	}
	
	function pv_getContentTableName(){
		return PVDatabase::getContentTableName();
	}
	
	function pv_getAudioContentTableName(){
		return PVDatabase::getAudioContentTableName();
	}
	
	function pv_getContentCategoriesTableName(){
		return PVDatabase::getContentCategoriesTableName();
	}
	
	function pv_getContentRelationsTableName(){
		return PVDatabase::getContentRelationsTableName();
	}
	
	function pv_getContentCategoryRelationsTableName(){
		return PVDatabase::getContentCategoryRelationsTableName();
	}
	
	function pv_getContentCommentsTableName(){
		return PVDatabase::getContentCommentsTableName();
	}
	
	
	function pv_getEventContentTableName(){
		return PVDatabase::getEventContentTableName();
	}
	
	function pv_getContentFieldRelationsTableName(){
		return PVDatabase::getContentFieldRelationsTableName();
	}
	
	function pv_getFileContentTableName(){
		return PVDatabase::getFileContentTableName();
	}
	
	function pv_getImageContentTableName(){
		return PVDatabase::getImageContentTableName();
	}
	
	function pv_getContentModifiersTableName(){
		return PVDatabase::getContentModifiersTableName();
	}
	
	function pv_getProductContentTableName(){
		return PVDatabase::getProductContentTableName();
	}
	
	function pv_getContentRatingTableName(){
		return PVDatabase::getContentRatingTableName();
	}
	
	function pv_getContentTaxonomyTableName(){
		return PVDatabase::getContentTaxonomyTableName();
	}
	
	function pv_getTextContentTableName(){
		return PVDatabase::getTextContentTableName();
	}
	
	function pv_getContentTypeTableName(){
		return PVDatabase::getContentTypeTableName();
	}
	
	function pv_getContentMultiAuthorTableName(){
		return PVDatabase::getContentMultiAuthorTableName();
	}
	
	function pv_getVideoContentTableName(){
		return PVDatabase::getVideoContentTableName();
	}
	
	function pv_getContentViewsTableName(){
		return PVDatabase::getContentViewsTableName();
	}
	
	function pv_getFieldsTableName(){
		return PVDatabase::getFieldsTableName();
	}
	
	function pv_getFieldsOptionsTableName(){
		return PVDatabase::getFieldsOptionsTableName();
	}
	
	function pv_getFieldValuesTableName(){
		return PVDatabase::getFieldValuesTableName();
	}
	
	function pv_getFieldTypesTableName(){
		return PVDatabase::getFieldTypesTableName();
	}
	
	function pv_getFieldOutputTableName(){
		return PVDatabase::getFieldOutputTableName();
	}
	
	function pv_getLoginTableName(){
		return PVDatabase::getLoginTableName();
	}
	
	function pv_getMenuTableName(){
		return PVDatabase::getMenuTableName();
	}
	
	function pv_getMenuItemsTableName(){
		return PVDatabase::getMenuItemsTableName();
	}
	
	function pv_getModuleAdminTableName(){
		return PVDatabase::getModuleAdminTableName();
	}
	
	function pv_getModulesTableName(){
		return PVDatabase::getModulesTableName();
	}
	
	function pv_getModulePermissionsTableName(){
		return PVDatabase::getModulePermissionsTableName();
	}
	
	function pv_getMVCTableName(){
		return PVDatabase::getMVCTableName();
	}
	
	function pv_getOptionsTableName(){
		return PVDatabase::getOptionsTableName();
	}
	
	function pv_getPageContainersRelationshipTableName(){
		return PVDatabase::getPageContainersRelationshipTableName();
	}
	
	function pv_getPagesTableName(){
		return PVDatabase::getPagesTableName();
	}
	
	function pv_getPageModuleRelationshipTableName(){
		return PVDatabase::getPageModuleRelationshipTableName();
	}
	
	function pv_getPluginsTableName(){
		return PVDatabase::getPluginsTableName();
	}
	
	function pv_getPluginPermissionsTableName(){
		return PVDatabase::getPluginPermissionsTableName();
	}
	
	function pv_getSessionTableName(){
		return PVDatabase::getSessionTableName();
	}
	
	function pv_getSessionTrackerTableName(){
		return PVDatabase::getSessionTrackerTableName();
	}
	
	function pv_getTemplatesTableName(){
		return PVDatabase::getTemplatesTableName();
	}
	
	function pv_getTemplatePositionsTableName(){
		return PVDatabase::getTemplatePositionsTableName();
	}
	
	function pv_getUserActivationTableName(){
		return PVDatabase::getUserActivationTableName();
	}
	
	function pv_getPointsTableName(){
		return PVDatabase::getPointsTableName();
	}
	
	function pv_getUserRelationsTableName(){
		return PVDatabase::getUserRelationsTableName();
	}
	
	function pv_getUserRolesRelationsTableName(){
		return PVDatabase::getUserRolesRelationsTableName();
	}
	
	function pv_getSubscriptionTableName(){
		return PVDatabase::getSubscriptionTableName();
	}
	
	function pv_getUserTaxonomyTableName(){
		return PVDatabase::getUserTaxonomyTableName();
	}
	
	function pv_getUserRolesTableName(){
		return PVDatabase::getUserRolesTableName();
	}
	
	
	/**
	 * User Sessions Functions
	 */
	
	function pv_checkLogin(){
		return PVUsers::checkLogin();
	}//end pv_checkLogin
	
	function pv_getUserID(){
		return PVUsers::getUserID();
	}//end pv_checkLogin
	
	function pv_getCurrentUserID(){
		return PVUsers::getUserID();
	}//end pv_checkLogin
	
	function pv_getUserName(){
		return PVUsers::getUserName();
	}//end pv_getUserName()
	
	function pv_getUserEmail(){
		return PVUsers::getUserEmail();
	}//end pv_getUserEmail()
	
	function pv_getCurrentUserEmail(){
		return PVUsers::getUserEmail();
	}//end pv_getUserEmail()
	
	function pv_getUserRole(){
		return PVUsers::getUserRole();
	}//end pv_getUserRole()
	
	function pv_getCurrentUserRole(){
		return PVUsers::getUserRole();
	}//end pv_getUserRole()
	
	function pv_getCurrentUserAccessLevel(){
		return PVUsers::getUserAccessLevel();
	}//end pv_getUserRole()
	
	function pv_getAssignedUserRoles($user_id){
		return PVUsers::getAssignedUserRoles($user_id);
	}//end pv_getUserRole()
	
	function pv_getUserRoleType(){
		return PVUsers::getUserRoleType();
	}//end pv_getUserRole()
	
	function pv_attemptLogin($username, $password, $save_cookie=1, $password_encoded=false){
		return PVUsers::attemptLogin($username, $password, $save_cookie, $password_encoded);
	}//end attemptLogin($username, $password, $save_cookie)
	
	function pv_loginUser($username){
		return PVUsers::loginUser($username);
	}//end loginUser($username)
	
	function pv_getOnlineUsersSession(){
		return PVUsers::attemptLogin($username, $password, $save_cookie);
	}//end getOnlineUsersSession()
	
	function pv_logout(){
		return PVUsers::logout();
	}//end logout()
	
	function pv_getUserList($args=''){
		return PVUsers::getUserList($args);
	}//
	
	function pv_generateResetCode($user_id){
		return PVUsers::generateResetCode($user_id);
	}//
	
	
	function pv_getUserListWithRoles($args=''){
		return PVUsers::getUserListWithRoles($args);
	}//
	
	function pv_getUserIDByName($username){
		return PVUsers::getUserIDByName($username);
	}//end getUserIDByName($username)
	
	function pv_getUserIDByEmail($useremail){
		return PVUsers::getUserIDByEmail($useremail);
	}//end getUserIDByName($username)
	
	function pv_getUserInfo($userid, $join_activation=FALSE, $join_roles=FALSE){
		return PVUsers::getUserInfo($userid, $join_activation , $join_roles);
	}
	
	function pv_getUserInfoWithRoles($userid, $userfield=''){
		return PVUsers::getUserInfoWithRoles($userid, $userfield);
	}
	
	function pv_getUserPageUrl($user_id){
		return PVUsers::getUserPageUrl($user_id);
	}
	
	function pv_getUserImageUrl($user_id){
		return PVUsers::getUserImageUrl($user_id);
	}
	
	function pv_getUserImageThumbUrl($user_id){
		return PVUsers::getUserImageThumbUrl($user_id);
	}
	
	function pv_printUserImage($user_id, $img_class='', $img_id=''){
		return PVUsers::printUserImage($user_id, $img_class='', $img_id='');
	}
	
	function pv_printUserImageThumb($user_id, $img_class='', $img_id=''){
		return PVUsers::printUserImageThumb($user_id, $img_class='', $img_id='');
	}
	
	function pv_insertNewUser($args, $password_encoded=false){
		return PVUsers::addUser($args, $password_encoded);
	}
	
	function pv_addUser($args, $password_encoded=false){
		return PVUsers::addUser($args, $password_encoded);
	}
	
	function pv_updateUser($args){
		return PVUsers::updateUser($args);
	}
	
	function pv_updateUserPassword($args, $encrypted=FALSE){
		return PVUsers::updateUserPassword($args, $encrypted);
	}
	
	function pv_addUserToRole($user_id, $role_id){
		return PVUsers::addUserToRole($user_id, $role_id);
	}
	
	function pv_deleteUser($user_id, $remove_user_content=TRUE, $remove_user_comments=TRUE, $remove_user_subscriptions=TRUE, $remove_user_points=TRUE, $remove_user_categories=TRUE, $remove_user_options=TRUE, $remove_user_multi_author=TRUE){
		return PVUsers::deleteUser($user_id, $remove_user_content, $remove_user_comments, $remove_user_subscriptions, $remove_user_points, $remove_user_categories, $remove_user_options, $remove_user_multi_author);
	}
	
	function pv_updateUserFields($args){
		return PVUsers::updateUserFields($args);
	}
	
	function pv_addUserRole($args){
		return PVUsers::addUserRole($args);
	}
	
	function pv_getUserRolesList($args=''){
		return PVUsers::getUserRolesList($args);
	}
	
	function pv_getUserRoleByID($role_id){
		return PVUsers::getUserRoleByID($role_id);
	}
	
	function pv_getUserRoleByName($role_id){
		return PVUsers::getUserRoleByName($role_id);
	}
	
	function pv_updateUserRole($args){
		return PVUsers::updateUserRole($args);
	}
	
	function pv_removeUserFromeRole($user_id, $role_id){
		return PVUsers::removeUserFromeRole($user_id, $role_id);
	}
	
	
	function pv_deleteUserRole($role_id){
		return PVUsers::deleteUserRole($role_id);
	}

	
	function pv_addUserSubscription($content_id=0, $comment_id=0, $user_id=0, $app_id=0, $subscription_type='', $subscription_approved=0, $subscription_active=0, $subscription_start_date='', $subscription_end_date='' ){
		return PVSubscriptions::addSubscription($content_id, $comment_id, $user_id, $app_id, $subscription_type, $subscription_approved, $subscription_active, $subscription_start_date, $subscription_end_date);
	}
	

	
	function pv_addUserUniqueSubscription($content_id=0, $comment_id=0, $user_id=0, $app_id=0, $subscription_type='', $subscription_approved=0, $subscription_active=0, $subscription_start_date='', $subscription_end_date='' ){
		return PVSubscriptions::addUniqueSubscription($content_id, $comment_id, $user_id, $app_id, $subscription_type, $subscription_approved, $subscription_active, $subscription_start_date, $subscription_end_date );
	}
	
	function pv_getUserSubscriptionList($content_id='', $comment_id='', $user_id='', $app_id='', $subscription_type='', $subscription_approved='', $subscription_active='', $subscription_start_date='', $subscription_end_date=''){
		return PVSubscriptions::getSubscriptionList($content_id, $comment_id, $user_id, $app_id, $subscription_type, $subscription_approved, $subscription_active, $subscription_start_date, $subscription_end_date);
	}
	
	function pv_getUserSubscription($subscription_id){
		return PVSubscriptions::getSubscription($subscription_id);
	}
	
	function pv_updateUserSubscription($subscription_id, $content_id=0, $comment_id=0, $user_id=0, $app_id=0, $subscription_type='', $subscription_approved=0, $subscription_active=0, $subscription_start_date='', $subscription_end_date=''){
		return PVSubscriptions::updateSubscription($subscription_id, $content_id, $comment_id, $user_id, $app_id, $subscription_type, $subscription_approved, $subscription_active, $subscription_start_date, $subscription_end_date);
	}
	
	function pv_deleteUserSubscription($subscription_id){
		return PVSubscriptions::deleteSubscription($subscription_id);
	}
	
	
	function pv_addSubscription($content_id=0, $comment_id=0, $user_id=0, $app_id=0, $subscription_type='', $subscription_approved=0, $subscription_active=0, $subscription_start_date='', $subscription_end_date='' ){
		return PVSubscriptions::addSubscription($content_id, $comment_id, $user_id, $app_id, $subscription_type, $subscription_approved, $subscription_active, $subscription_start_date, $subscription_end_date);
	}
	
	function pv_addUniqueSubscription($content_id=0, $comment_id=0, $user_id=0, $app_id=0, $subscription_type='', $subscription_approved=0, $subscription_active=0, $subscription_start_date='', $subscription_end_date='' ){
		return PVSubscriptions::addUniqueSubscription($content_id, $comment_id, $user_id, $app_id, $subscription_type, $subscription_approved, $subscription_active, $subscription_start_date, $subscription_end_date );
	}
	
	function pv_getSubscriptionList($content_id='', $comment_id='', $user_id='', $app_id='', $subscription_type='', $subscription_approved='', $subscription_active='', $subscription_start_date='', $subscription_end_date=''){
		return PVSubscriptions::getSubscriptionList($content_id, $comment_id, $user_id, $app_id, $subscription_type, $subscription_approved, $subscription_active, $subscription_start_date, $subscription_end_date);
	}
	
	function pv_getSubscription($subscription_id){
		return PVSubscriptions::getSubscription($subscription_id);
	}
	
	function pv_updateSubscription($subscription_id, $content_id=0, $comment_id=0, $user_id=0, $app_id=0, $subscription_type='', $subscription_approved=0, $subscription_active=0, $subscription_start_date='', $subscription_end_date=''){
		return PVSubscriptions::updateSubscription($subscription_id, $content_id, $comment_id, $user_id, $app_id, $subscription_type, $subscription_approved, $subscription_active, $subscription_start_date, $subscription_end_date);
	}
	
	function pv_deleteSubscription($subscription_id){
		return PVSubscriptions::deleteSubscription($subscription_id);
	}

	function pv_addUserPoint($user_id, $point_value=0, $content_id=0, $comment_id=0, $app_id=0, $point_type='' ){
		return PVPoints::addPoint($user_id, $point_value, $content_id, $comment_id, $app_id, $point_type );
	}
	
	function pv_addUserUniquePoint($user_id, $point_value=0, $content_id=0, $comment_id=0, $app_id=0, $point_type='' ){
		return PVPoints::addUniquePoint($user_id, $point_value, $content_id, $comment_id, $app_id, $point_type );
	}
	
	function pv_getUserPointsList($user_id=0, $point_value=0, $content_id=0, $comment_id=0, $app_id=0, $point_type=''){
		return PVPoints::getPointsList($user_id, $point_value, $content_id, $comment_id, $app_id, $point_type);
	}
	
	function pv_getUserPoint($point_id){
		return PVPoints::getPoint($point_id);
	}
	
	function pv_updateUserPoint($point_id, $content_id=0, $comment_id=0, $app_id=0, $point_type='', $user_id=0){
		return PVPoints::updatePoint($point_id, $content_id, $comment_id, $app_id, $point_type, $user_id);
	}
	
	function pv_deleteUserPoint($point_id){
		return PVPoints::deletePoint($point_id);
	}
	
	function pv_addPoint($user_id, $point_value=0, $content_id=0, $comment_id=0, $app_id=0, $point_type='' ){
		return PVPoints::addPoint($user_id, $point_value, $content_id, $comment_id, $app_id, $point_type );
	}
	
	function pv_addUniquePoint($user_id, $point_value=0, $content_id=0, $comment_id=0, $app_id=0, $point_type='' ){
		return PVPoints::addUniquePoint($user_id, $point_value, $content_id, $comment_id, $app_id, $point_type );
	}
	
	function pv_getPointsList($user_id=0, $point_value=0, $content_id=0, $comment_id=0, $app_id=0, $point_type=''){
		return PVPoints::getPointsList($user_id, $point_value, $content_id, $comment_id, $app_id, $point_type);
	}
	
	function pv_getPoint($point_id){
		return PVPoints::getPoint($point_id);
	}
	
	function pv_updatePoint($point_id, $content_id=0, $comment_id=0, $app_id=0, $point_type='', $user_id=0){
		return PVPoints::updatePoint($point_id, $content_id, $comment_id, $app_id, $point_type, $user_id);
	}
	
	function pv_deletePoint($point_id){
		return PVPoints::deletePoint($point_id);
	}
	
	function pv_addUserRelationship($requesting_user_id, $requested_user_id, $relationship_type='', $relationship_status=0 ){
		return PVUsers::addUserRelationship($requesting_user_id, $requested_user_id, $relationship_type, $relationship_status );
	}
	
	function pv_addUniqueUserRelationship($requesting_user_id, $requested_user_id, $relationship_type='', $relationship_status=0 ){
		return PVUsers::addUniqueUserRelationship($requesting_user_id, $requested_user_id, $relationship_type, $relationship_status );
	}
	
	function pv_getUserRelationshipList($requesting_user_id, $requested_user_id='', $relationship_type='', $relationship_status=0){
		return PVUsers::getUserRelationshipList($requesting_user_id, $requested_user_id, $relationship_type, $relationship_status);
	}
	
	function pv_updateUserRelationship($relationship_id, $requesting_user_id=0, $requested_user_id=0, $relationship_type='', $relationship_status=0 ){
		return PVUsers::updateUserRelationship($relationship_id, $requesting_user_id, $requested_user_id, $relationship_type, $relationship_status );
	}
	
	function pv_getUserRelationship($relationship_id){
		return PVUsers::getUserRelationship($relationship_id);
	}
	
	function pv_checkUserRelationship($first_user, $second_user, $relationship_type='', $relationship_status=''){
		return PVUsers::checkUserRelationship($first_user, $second_user, $relationship_type, $relationship_status);
	}
	
	function pv_getUserRelationshipByConnection($first_user, $second_user, $relationship_type='', $relationship_status=''){
		return PVUsers::getUserRelationshipByConnection($first_user, $second_user, $relationship_type, $relationship_status);
	}
	
	function pv_deleteUserRelationship($relationship_id){
		return PVUsers::deleteUserRelationship($relationship_id);
	}
	
	
	
	
	
	
	
	
	/**
	 * Tools
	 */
	
	function pv_createParameterArray($params){
		return PVTools::createParameterArray($params);
	}//end createParameterArray($params)
	
	function pv_xmlToArray($params){
		return PVTools::xmlToArray($params);
	}//end xmlToArray($params)
	
	function pv_convertNumbericBoolean($boolean){
		return PVTools::convertNumbericBoolean($boolean);
	}//end convertNumbericBoolean($boolean)
	
	function pv_convertTextBoolean($boolean){
		return PVTools::convertTextBoolean($boolean);
	}//end convertTextBoolean($boolean)
	
	
	function pv_getCurrentUrl(){
		return PVTools::getCurrentUrl();
	}//end getPageURLByID($page_id)
	
	function pv_generateRandomString($numOfChars = 15, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'){
		return PVTools::generateRandomString($numOfChars, $chars);
	}//end convertTextBoolean($boolean)
	
	function pv_truncateText($string, $length=50, $trailing='...', $strip_tags=TRUE, $allowed_tags=''){
		return PVTools::truncateText ($string, $length, $trailing, $strip_tags, $allowed_tags);
	}//end getPageURLByID($page_id)
	
	function pv_getSiteUrl(){
		return PVTools::getSiteUrl();
	}//end getPageURLByID($page_id)
	
	function pv_getCurrentBaseUrl(){
		return PVTools::getCurrentBaseUrl();
	}//end getPageURLByID($page_id)
	
	function pv_formUrlAppendixPHP($variables){
		return PVTools::formUrlParameters($variables);
	}
	
	function pv_formUrlParameters($variables){
		return PVTools::formUrlParameters($variables);
	}
	
	function pv_formUrlPath($variables){
		return PVTools::formUrlPath($variables);
	}
	function pv_addOption($option_name, $option_value='', $app_id='', $user_id='', $content_id='', $option_type=''){
		return PVTools::addOption($option_name, $option_value, $app_id, $user_id, $content_id, $option_type);
	}//end getApplicationID($app_unique_name)
	
	function pv_getOptionList($option_name='', $option_value='', $app_id='', $user_id='', $content_id='', $option_type='', $custom_where=''){
		return PVTools::getOptionList($option_name, $option_value, $app_id, $user_id, $content_id, $option_type, $custom_where);
	}//end getApplicationID($app_unique_name)
	
	function pv_getOption($option_id, $option_value='', $app_id='', $user_id='', $content_id='', $option_type=''){
		return PVTools::getOption($option_id, $option_value, $app_id, $user_id, $content_id, $option_type);
	}//end getApplicationID($app_unique_name)
	
	function pv_getOptionValue($option_id, $option_value='', $app_id='', $user_id='', $content_id='', $option_type=''){
		return PVTools::getOptionValue($option_id, $option_value, $app_id, $user_id, $content_id, $option_type);
	}//end getApplicationID($app_unique_name)
	
	function pv_updateOption($option_id, $option_name='', $option_value='', $app_id='', $user_id='', $content_id='', $option_type=''){
		return PVTools::updateOption($option_id, $option_name, $option_value, $app_id, $user_id, $content_id, $option_type);
	}//end getApplicationID($app_unique_name)
	
	function pv_setOption($option_array, $option_value='', $app_id='', $user_id='', $content_id='', $option_type='', $option_id=''){
		return PVTools::setOption($option_array, $option_value, $app_id, $user_id, $content_id, $option_type, $option_id);
	}//end getApplicationID($app_unique_name)
	
	function pv_deleteOption($option_id, $deleteChildrenOptions=false){
		return PVTools::deleteOption($option_id, $deleteChildrenOptions);
	}//end getApplicationID($app_unique_name)
	
	
	/*******************
	*PVComponents
	*********************/
	
	function pv_installTemplate($args){
		return PVTemplate::installTemplate($args);
	}
	
	function pv_getTemplate($template_id){
		return PVTemplate::getTemplate($template_id);
	}
	
	function pv_getTemplateList($args){
		return PVTemplate::getTemplateList($args);
	}
	
	function pv_removeTemplate($template_id){
		return PVTemplate::removeTemplate($template_id);
	}
	
	function pv_addTemplatePosition($template_id, $position){
		return PVTemplate::addTemplatePosition($template_id, $position);
	}
	
	function pv_getTemplatePositionList($args){
		return PVTemplate::getTemplatePositionList($args);
	}
	
	function pv_getTemplatePosition($template_id, $position_name){
		return PVTemplate::getTemplatePosition($template_id, $position_name);
	}

	function pv_removeTemplatePosition($template_id, $position_name){
		return PVTemplate::removeTemplatePosition($template_id, $position_name);
	}
	
	function pv_selectTemplate($args){
		return PVTemplate::selectTemplate($args);
	}
	
	function pv_installApplication($args){
		return PVApplications::installApplication($args);
	}
	
	function pv_getApplication($args){
		return PVApplications::getApplication($args);
	}
	
	function pv_getApplicationList($app_id, $remove_app_content=FALSE, $remove_app_options=FALSE, $remove_app_modules=FALSE, $remove_app_points=FALSE, $remove_app_subscriptions=FALSE, $remove_app_categories=FALSE, $remove_modules_admin=FALSE){
		return PVApplications::getApplicationList($app_id, $remove_app_content, $remove_app_options, $remove_app_modules, $remove_app_points, $remove_app_subscriptions, $remove_app_categories, $remove_modules_admin);
	}
	
	function pv_removeApplication($args){
		return PVApplications::removeApplication($args);
	}
	
	function pv_getAppObject($app_unique_name){
		return PVApplications::getAppObject($app_unique_name);
	}//end getPageAliasByID($page)
	
	function pv_getApplicationParameters($app_id){
		return PVApplications::getApplicationParameters($app_id);
	}//end getPageAliasByID($page)
	
	function pv_setApplicationParameters($app_id, $parameters){
		return PVApplications::setApplicationParameters($app_id, $parameters);
	}//end getPageAliasByID($page)

	function pv_getAdminAppObject($app_unique_name){
		return PVApplications::getAdminAppObject($app_unique_name);
	}//end getPageAliasByID($page)
	
	function pv_getApplicationID($app_unique_name){
		return PVApplications::getApplicationID($app_unique_name);
	}//end getApplicationID($app_unique_name)
	
	function pv_getPageURLByID($page_id){
		return PVPages::getPageURLByID($page_id);
	}//end getPageURLByID($page_id)
	
	function pv_getPageShortURLByID($page_id){
		return PVPages::getPageShortURLByID($page_id);
	}//end getPageURLByID($page_id)
	
	function pv_getPages(){
		return PVPages::getPages();
	}//end getPageURLByID($page_id)
	
	function pv_getPageAliasByID($page){
		return PVPages::getPageAliasByID($page);
	}//end getPageAliasByID($page)
	
	function pv_installModule($args){
		return PVModules::installModule($args);
	}
	
	function pv_getModuleAdmin($module_unique_id, $module_app_identifier){
		return PVModules::getModuleAdmin($module_unique_id, $module_app_identifier);
	}
	
	function pv_getModuleAdminList($args){
		return PVModules::getModuleAdminList($args);
	}
	
	function pv_deleteModuleAdmin($module_unique_id, $module_app_identifier, $remove_modules=FALSE){
		return PVModules::deleteModuleAdmin($module_unique_id, $module_app_identifier, $remove_modules);
	}
	
	function pv_installPlugin($args){
		return PVPlugins::installPlugin($args);
	}
	
	function pv_getPluginList($args){
		return PVPlugins::getPluginList($args);
	}
	
	function pv_getPlugin($plugin_unique_name){
		return PVPlugins::getPlugin($plugin_unique_name);
	}
	
	function pv_createPage($args){
		return PVPages::createPage($args);
	}
	
	function pv_getPageList($args){
		return PVPages::getPageList($args);
	}
	
	function pv_getPage($page_id){
		return PVPages::getPage($page_id);
	}
	
	function pv_getPageByAlias($page_id){
		return PVPages::getPageByAlias($page_id);
	}
	
	function pv_getPageByUrl($page_id){
		return PVPages::getPageByUrl($page_id);
	}
	
	function pv_updatePage($args){
		return PVPages::updatePage($args);
	}
	
	function pv_deletePage($page_id, $recursive=FALSE){
		return PVPages::deletePage($page_id, $recursive);
	}
	
	function pv_createContainer($args){
		return PVContainers::createContainer($args);
	}
	
	function pv_getContainerList($args){
		return PVContainers::getContainerList($args);
	}
	
	function pv_getContainer($container_id){
		return PVContainers::getContainer($container_id);
	}
	
	function pv_updateContainer($args){
		return PVContainers::updateContainer($args);
	}
	
	function pv_getContainerByAlias($container_id){
		return PVContainers::getContainerByAlias($container_id);
	}
	
	function pv_deleteContainer($container_id, $recursive=FALSE){
		return PVContainers::deleteContainer($container_id, $recursive);
	}
	
	function pv_createModule($args){
		return PVModules::createModule($args);
	}
	
	function pv_updateModule($args){
		return PVModules::updateModule($args);
	}
	
	function pv_getModuleList($args){
		return PVModules::getModuleList($args);
	}
	
	function pv_getModule($module_id){
		return PVModules::getModule($module_id);
	}
	
	function pv_getModuleByAlias($module_id){
		return PVModules::getModuleByAlias($module_id);
	}
	
	function pv_deleteModule($module_id, $recursive=FALSE){
		return PVModules::deleteModule($module_id, $recursive);
	}
	
	function pv_addPageContainerRelationship($page_id, $container_id, $page_container_order=0, $page_container_enabled=0  ){
		return PVPages::addPageContainerRelationship($page_id, $container_id, $page_container_order, $page_container_enabled  );
	}
	
	function pv_addContainerModuleRelationship($container_id, $module_id, $container_module_ordering=0, $container_module_enabled=0){
		return PVContainers::addContainerModuleRelationship($container_id, $module_id, $container_module_ordering, $container_module_enabled);
	}
	
	function pv_getPageContainerRelationshipList($args){
		return PVPages::getPageContainerRelationshipList($args);
	}
	
	function pv_getContainerModuleRelationshipList($args){
		return PVContainers::getContainerModuleRelationshipList($args);
	}
	
	function pv_deletePageContainerRelationship($page_container_id){
		return PVPages::deletePageContainerRelationship($page_container_id);
	}
	
	function pv_deleteContainerModuleRelationship($container_module_id){
		return PVContainer::deleteContainerModuleRelationship($container_module_id);
	}
	
	function pv_createUserPermission($args){
		return PVSecurity::createUserPermission($args);
	}
	
	function pv_getUserPermissionList($args){
		return PVSecurity::getUserPermissionList($args);
	}
	
	function pv_getUserPermission($permission_unique_name, $app_id=0){
		return PVSecurity::getUserPermission($permission_unique_name, $app_id);
	}
	
	function pv_updateUserPermission($args){
		return PVSecurity::updateUserPermission($args);
	}
	
	function pv_updatePermissionByApplication($args){
		return PVSecurity::updatePermissionByApplication($args);
	}
	
	function pv_updatePermissionRoleByApplication($args){
		return PVSecurity::updatePermissionRoleByApplication($args);
	}
	
	function pv_deleteUserPermission($permission_unique_name, $app_id=0){
		return PVSecurity::deleteUserPermission($permission_unique_name, $app_id);
	}
	
	function pv_createMenu($args){
		return PVMenus::createMenu($args);
	}
	
	function pv_updateMenu($args){
		return PVMenus::updateMenu($args);
	}


	function pv_getMenuList($args){
		return PVMenus::getMenuList($args);
	}
	
	
	function pv_getMenu($menu_id){
		return PVMenus::getMenu($menu_id);
	}
	
	
	function pv_getMenuByUniqueID($menu_unique_id){
		return PVMenus::getMenuByUniqueID($menu_unique_id);
	}
	
	function pv_deleteMenu($menu_id, $DELETE_MENU_ITEMS=TRUE){
		return PVMenus::deleteMenu($menu_id, $DELETE_MENU_ITEMS);
	}


	
	function pv_createMenuItem($args){
		return PVMenus::createMenuItem($args);
	}
	
	function pv_updateMenuItem($args){
		return PVMenus::updateMenuItem($args);
	}
	
	function pv_getMenuItemList($args){
		return PVMenus::getMenuItemList($args);
	}
	
	function pv_getMenuItem($menu_id, $item_id){
		return PVMenus::getMenuItem($menu_id, $item_id);
	}
	
	function pv_deleteMenuItem($menu_id, $item_id){
		return PVMenus::deleteMenuItem($menu_id, $item_id);
	}
	
	function pv_initiliazeMVC($mvc_unique_id){
		return PVMVC::initiliazeMVC($mvc_unique_id);
	}
	
	function pv_installMVC($args){
		return PVMVC::installMVC($args);
	}
	
	function pv_getMVCList($args){
		return PVMVC::getMVCList($args);
	}
	
	function pv_deleteMVC($mvc_unique_id){
		return PVMVC::deleteMVC($mvc_unique_id);
	}
	
	function pv_getMVCInfo($mvc_unique_id){
		return PVMVC::getMVCInfo($mvc_unique_id);
	}
	
	
	
	
	/*********
	**Math
	**********/
	
	function pv_convertTimeIntoSeconds($days=0, $hours=0, $minutes=0, $seconds=0){
		return PVMathematics::convertTimeIntoSeconds($days, $hours, $minutes, $seconds);
	}//end getPageURLByID($page_id)
	
	function pv_convertSecondsToHours($seconds){
		return PVMathematics::convertSecondsToHours($seconds);
	}//end getPageURLByID($page_id)
	
	function pv_convertSecondsToMinutes($seconds){
		return PVMathematics::convertSecondsToMinutes($seconds);
	}//end getPageURLByID($page_id)
	
	
	function pv_convertSecondsToDays($seconds){
		return PVMathematics::convertSecondsToDays($seconds);
	}//end getPageURLByID($page_id)
	
	function pv_convertSecondsIntoElapsedTime($seconds){
		return PVMathematics::convertSecondsIntoElapsedTime($seconds);
	}//end getPageURLByID($page_id)
	
	
	
	

	/**
	 * Security
	 */
	function pv_getCaptcha(){
		return PVSecurity::getCaptcha();
	}//end getCaptcha()

	function pv_checkCaptcha(){
		return PVSecurity::checkCaptcha();
	}//end checkCaptcha()
	
	
	function pv_checkUserAccessLevel($user_id, $required_level){
		return PVSecurity::checkUserAccessLevel($user_id, $required_level);
	}
	
	function pv_getUserRoles(){
		return PVSecurity::getUserRoles();
	}//end getUserRoles()
	
	function pv_checkUserPermission($user_role, $allowed_roles){
		return PVSecurity::checkUserPermission($user_role, $allowed_roles);
	}//end checkUserPermission($user_role, $allowed_roles)
	
	function pv_checkApplicationUserPermission($app_id, $permission_name, $user_role=''){
		return PVSecurity::checkApplicationUserPermission($app_id, $permission_name, $user_role);
	}//end checkUserPermission($user_role, $allowed_roles)
	
	function pv_getApplicationPermissions($app_id, $permission_name){
		return PVSecurity::getApplicationPermissions($app_id, $permission_name);
	}//end pv_getApplicationPermissions
	
	
	function pv_checkApplicationUserAccessLevel($app_id, $permission_name, $user_access_level=0){
		return PVSecurity::checkApplicationUserAccessLevel($app_id, $permission_name, $user_access_level);
	}//end pv_getApplicationPermissions
	
	
	function pv_checkPluginUserPermission($app_id, $permission_name, $user_role=''){
		return PVSecurity::checkPluginUserPermission($app_id, $permission_name, $user_role);
	}//end checkUserPermission($user_role, $allowed_roles)
	
	function pv_getPluginPermissions($plugin_unique_id, $permission_name){
		return PVSecurity::getPluginPermissions($plugin_unique_id, $permission_name);
	}//end pv_getApplicationPermissions
	
	
	function pv_checkPluginUserAccessLevel($plugin_unique_id, $permission_name, $user_access_level=0){
		return PVSecurity::checkPluginUserAccessLevel($plugin_unique_id, $permission_name, $user_access_level);
	}//end pv_getApplicationPermissions
	
	function pv_checkModuleUserPermission($module_unique_id, $app_unique_id, $user_role=''){
		return PVSecurity::checkModuleUserPermission($module_unique_id, $app_unique_id, $user_role);
	}//end checkUserPermission($user_role, $allowed_roles)
	
	function pv_getModulePermissions($module_unique_id, $app_unique_id, $permission_name){
		return PVSecurity::getModulePermissions($module_unique_id, $app_unique_id, $permission_name);
	}//end pv_getApplicationPermissions
	
	
	function pv_checkModuleUserAccessLevel($module_unique_id, $app_unique_id, $permission_name, $user_access_level=0){
		return PVSecurity::checkModuleUserAccessLevel($module_unique_id, $app_unique_id, $permission_name, $user_access_level);
	}//end pv_getApplicationPermissions
	
	
	function pv_createApplicationPermission($args){
		return PVSecurity::createApplicationPermission($args);
	}
	
	function pv_updateApplicationPermission($args){
		return PVSecurity::updateApplicationPermission($args);
	}
	
	function pv_clearApplicationPermission($args){
		return PVSecurity::clearApplicationPermission($args);
	}
	
	function pv_deleteApplicationPermission($application_permission_id){
		return PVSecurity::deleteApplicationPermission($application_permission_id);
	}
	
	function pv_setApplicationPermission($args){
		return PVSecurity::setApplicationPermission($args);
	}
	
	function pv_getApplicationPermissionList($args){
		return PVSecurity::getApplicationPermissionList($args);
	}//end getApplicationPermissionList($args)
	
	function pv_createModulePermission($args){
		return PVSecurity::createModulePermission($args);
	}
	
	function pv_updateModulePermission($args){
		return PVSecurity::updateModulePermission($args);
	}
	
	function pv_clearModulePermission($args){
		return PVSecurity::clearModulePermission($args);
	}
	
	function pv_deleteModulePermission($module_permission_id){
		return PVSecurity::deleteModulePermission($module_permission_id);
	}
	
	function pv_setModulePermission($args){
		return PVSecurity::setModulePermission($args);
	}
	
	function pv_getModulePermissionList($args){
		return PVSecurity::getModulePermissionList($args);
	}//end getModulePermissionList($args)
	
	function pv_createPluginPermission($args){
		return PVSecurity::createPluginPermission($args);
	}
	
	function pv_updatePluginPermission($args){
		return PVSecurity::updatePluginPermission($args);
	}
	
	function pv_clearPluginPermission($args){
		return PVSecurity::clearPluginPermission($args);
	}
	
	function pv_deletePluginPermission($plugin_permission_id){
		return PVSecurity::deletePluginPermission($plugin_permission_id);
	}
	
	function pv_setPluginPermission($args){
		return PVSecurity::setPluginPermission($args);
	}
	
	function pv_getPluginPermissionList($args){
		return PVSecurity::getPluginPermissionList($args);
	}//end getApplicationPermissionList($args)
	
	
	/**
	 * System Component
	 */
	function pv_getSettings(){
		return PVConfiguration::getSettings();
	}//end getSettings()
	
	function pv_getLicense(){
		return PVConfiguration::getLicense();
	}//end getLicense()
	
	
	function pv_bootSystem(){
		return PVBootstrap::bootSystem();
	}//end getLicense()
	
	function pv_getSiteEmailConfiguration(){
		return PVConfiguration::getSiteEmailConfiguration();
	}//end getSiteEmailConfiguration()
	
	function pv_getSiteSessionConfiguration(){
		return PVConfiguration::getSiteSessionConfiguration();
	}//end getSiteSessionConfiguration()
	
	function pv_getSiteCompleteConfiguration(){
		return PVConfiguration::getSiteCompleteConfiguration();
	}//end getSiteCompleteConfiguration()
	
	function pv_getSiteGeneralConfiguration(){
		return PVConfiguration::getSiteGeneralConfiguration();
	}//end getSiteGeneralConfiguration()
	
	function pv_getSystemConfiguration(){
		return PVConfiguration::getSystemConfiguration();
	}//end getSystemConfiguration()
	
	function pv_getSiteConfiguration(){
		return PVConfiguration::getSiteConfiguration();
	}//end getSiteConfiguration()
	
	function pv_getServerConfiguration(){
		return PVConfiguration::getServerConfiguration();
	}//end getServerConfiguration()
	
	function pv_callHook($hookname, $params=''){
		return PVPlugins::callHook($hookname, $params);
	}//end getServerConfiguration()
	
	function pv_callHookOverride($hookname, $params=''){
		return PVPlugins::callHookOverride($hookname, $params);
	}//end getServerConfiguration()
	
	function pv_printSiteTitle(){
		return PVTemplate::printSiteTitle();
	}//end getServerConfiguration()
	
	function pv_printSiteMetaDescription(){
		return PVTemplate::printSiteMetaDescription();
	}//end getServerConfiguration()
	
	function pv_getSiteTitle(){
		return PVTemplate::getSiteTitle();
	}//end getServerConfiguration()
	
	function pv_getSiteMetaDescription(){
		return PVTemplate::getSiteMetaDescription();
	}//end getServerConfiguration()
	
	function pv_getSiteMetaTags(){
		return PVTemplate::getSiteMetaTags();
	}//end getServerConfiguration()
	
	function pv_setSiteTitle($string){
		return PVTemplate::setSiteTitle($string);
	}//e
	
	function pv_appendSiteTitle($string){
		return PVTemplate::appendSiteTitle($string);
	}//end getServerConfiguration()
	
	function pv_setSiteMetaTags($string){
		return PVTemplate::setSiteMetaTags($string);
	}//end getServerConfiguration()
	
	function pv_appendSiteMetaTags($string){
		return PVTemplate::appendSiteMetaTags($string);
	}//end getServerConfiguration()
	
	function pv_setSiteMetaDescription($string){
		return PVTemplate::setSiteMetaDescription($string);
	}//end getServerConfiguration()
	
	function pv_appendSiteMetaDescription($string){
		return PVTemplate::appendSiteMetaDescription($string);
	}//end getServerConfiguration()
	
	function pv_printCurrentTemplateCss(){
		return PVTemplate::printCurrentTemplateCss();
	}//end getServerConfiguration()
	
	function pv_enqueue_javascript($script){
		PVLibraries::enqueue_javascript($script);
	}
	
	function pv_enqueue_jquery($script){
		PVLibraries::enqueue_jquery($script);
	}
	
	function pv_enqueue_prototype($script){
		PVLibraries::enqueue_prototype($script);
	}
	
	function pv_enqueue_mootools($script){
		PVLibraries::enqueue_mootools($script);
	}
	
	function pv_enqueue_css($script){
		PVLibraries::enqueue_css($script);
	}
	
	function pv_enqueue_javascript_header($script){
		$open_javascript.=$script;
	}
	
	function pv_enqueue_openscript($script){
		PVLibraries::enqueue_openscript($script);
	}
	
	function pv_get_enqueue_javascript(){
		return PVLibraries::get_enqueue_javascript();
	}
	
	function pv_get_enqueue_jquery(){
		return PVLibraries::get_enqueue_jquery();
	}
	
	function pv_get_enqueue_prototype(){
		return PVLibraries::get_enqueue_prototype();
	}
	
	function pv_get_enqueue_mootools(){
		return PVLibraries::get_enqueue_mootools();
	}
	
	function pv_get_enqueue_css(){
		return PVLibraries::get_enqueue_css();
	}


	function pv_get_enqueue_openscript(){
		return PVLibraries::get_enqueue_openscript();
	}
	
	function pv_printHeaderWithVersions($version='', $use_url=FALSE){
		PVLibraries::printHeaderWithVersions($version, $use_url);
	}
	
	function pv_printHeader($version='', $use_url=FALSE){
		PVLibraries::printHeader($version, $use_url);
	}
	
	function pv_getHeader($version='', $use_url=FALSE){
		PVLibraries::getHeader($version, $use_url);
	}
	
	function pv_printJavaScriptHeader($version='', $use_url=FALSE){
		PVLibraries::printJavaScriptHeader($version, $use_url);
	}
	
	function pv_printMooToolsHeader($version='', $use_url=FALSE){
		PVLibraries::printMooToolsHeader($version, $use_url);
	}
	
	function pv_printPrototypeHeader($version='', $use_url=FALSE){
		PVLibraries::printPrototypeHeader($version, $use_url);
	}
	
	function pv_printJQueryHeader($version='', $use_url=FALSE){
		PVLibraries::printJQueryHeader($version, $use_url);
	}
	
	function pv_printCSSHeader($version='', $use_url=FALSE){
		PVLibraries::printCSSHeader($version, $use_url);
	}
	
	function pv_activateSSL($url){
		return PVRouter::activateSSL($url);
	}
	
	function pv_deactivateSSL($url){
		return PVRouter::deactivateSSL($url);
	}
	
	function pv_url($url){
    	return PVRouter::url($url);
    }
	
	/**
	 * Controller.php class
	 */
	
	function pv_createContent($parameters){
		return PVContent::createContent($parameters);
	}//end createContente()
	
	function pv_createTextContent($parameters ){
		$parameters['adjacent_table']='pv_content_text';
		return PVContent::createTextContent($parameters);
	}// pv_createCreateTextContent
	
	function pv_createEventContent($parameters){
		$parameters['adjacent_table']='pv_content_events';
		return PVContent::createEventContent($parameters);
	}// pv_createCreateTextContent
	
	function pv_createVideoContent($parameters){
		$parameters['adjacent_table']='pv_content_video';
		return PVContent::createVideoContent($parameters);
	}// pv_createCreateTextContent
	
	function pv_createImageContent($parameters){
		$parameters['adjacent_table']='pv_content_images';
		return PVContent::createImageContent($parameters);
	}//end pv_createImageContent
	
	function pv_createImageContentWithFile($parameters){
		$parameters['adjacent_table']='pv_content_images';
		return PVContent::createImageContentWithFile($parameters);
	}//end pv_createImageContentWithFile
	
	function pv_createAudioContent($parameters){
		$parameters['adjacent_table']='pv_content_audio';
		return PVContent::createAudioContent($parameters);
	}// pv_createCreateTextContent
	
	function pv_createAudioContentWithFile($parameters){
		$parameters['adjacent_table']='pv_content_audio';
		return PVContent::createAudioContentWithFile($parameters);
	}// pv_createCreateTextContent
	
	function pv_createFileContent($parameters){
		$parameters['adjacent_table']='pv_content_files';
		return PVContent::createFileContent($parameters);
	}// pv_createCreateTextContent
	
	function pv_createFileContentWithFile($parameters){
		$parameters['adjacent_table']='pv_content_files';
		return PVContent::createFileContentWithFile($parameters);
	}//pv_createFileContentWithFileFromArray($args)
	
	function pv_createProductContent($parameters){
		$parameters['adjacent_table']='pv_content_product';
		return PVContent::createProductContent($parameters);
	}//pv_createFileContentWithFileFromArray($args)
	
	function pv_createCategory($parameters){
		return PVContent::createCategory($parameters);
	}//end pv_createCategory
	
	function pv_getContentList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type=''){
		return PVContent::getContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type);
	}//end pv_getContentList
	
	function pv_getContentImageList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type=''){
		return PVContent::getImageContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type);
	}//end pv_getContentList
	
	function pv_getContentVideoList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type=''){
		return PVContent::getVideoContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type);
	}//end pv_getContentList
	
	function pv_getContentEventList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type=''){
		return PVContent::getEventContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type);
	}//end pv_getContentList
	
	function pv_getContentTextList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type=''){
		return PVContent::getTextContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type);
	}//end pv_getContentList
	
	function pv_getContentAudioList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type='', $parent_id='', $category_id=''){
		return PVContent::getAudioContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type, $parent_id, $category_id);
	}//end pv_getContentList
	
	function pv_getContentProductList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type='', $parent_id='', $category_id=''){
		return PVContent::getProductContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type, $parent_id, $category_id);
	}//end pv_getContentList
	
	function pv_getUniversalContentList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type='', $parent_id='', $category_id=''){
		return PVContent::getUniversalContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type, $parent_id, $category_id);
	}//end pv_getContentList
	
	
	function pv_getContentFileList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type='',  $parent_id='', $category_id=''){
		return PVContent::getFileContentList($order_by_clause, $limit, $app_id, $owner_id, $content_type,  $parent_id, $category_id);
	}//end pv_getContentList
	
	
	
	function pv_getCategoryList($app_id='', $parent_category='', $category_unique_name='' , $category_name='' ){
		if(is_array($app_id)){
			return PVContent::getCategoryList($app_id);
		}
		else{
			return PVContent::getCategoryList($app_id, $parent_category, $category_unique_name , $category_name );
		}
	}//end pv_getCategoryList($app_id='', $parent_category='', $category_unique_name='' , $category_name='' )
	
	function pv_getContentCategories($content_id){
		return PVContent::getContentCategories($content_id);
	}//end getContentCategories($content_id)
	
	function pv_getContent($content_id){
		return PVContent::getContent($content_id);
	}//end getContent($content_id)
	
	function pv_getTextContent($content_id){
		return PVContent::getTextContent($content_id);
	}//end getTextContent($content_id)
	
	function pv_getImageContent($content_id){
		return PVContent::getImageContent($content_id);
	}//end getImageContent($content_id)
	
	function pv_getVideoContent($content_id){
		return PVContent::getVideoContent($content_id);
	}//end getVideoContent($content_id)
	
	function pv_getEventContent($content_id){
		return PVContent::getEventContent($content_id);
	}//end getVideoContent($content_id)
	
	function pv_getAudioContent($content_id){
		return PVContent::getAudioContent($content_id);
	}//end getAudioContent($content_id)
	
	function pv_getFileContent($content_id){
		return PVContent::getFileContent($content_id);
	}//end getFileContent($content_id)
	
	function pv_getProductContent($content_id){
		return PVContent::getProductContent($content_id);
	}//end getFileContent($content_id)
	
	function pv_getUniversalContent($content_id){
		return PVContent::getUniversalContent($content_id);
	}//end getFileContent($content_id)

	
	function pv_getCategory($category_id){
		return PVContent::getCategory($category_id);
	}//end pv_getCategory($category_id)

	
	function pv_updateContent($fields){
		return PVContent::updateContent($fields);
	}//end pv_updateContent()
	
	function pv_updateTextContent($fields){
		return PVContent::updateTextContent($fields);
	}//end pv_updateContent()
	
	function pv_updateEventContent($fields){
		return PVContent::updateEventContent($fields);
		
	}//end pv_updateContent()
	
	function pv_updateVideoContent($fields){
		return PVContent::updateVideoContent($fields);
	}//end pv_updateContent()
	
	function pv_updateAudioContent($fields){
		return PVContent::updateAudioContent($fields);
	}//end pv_updateContent()
	
	function pv_updateFileContent($fields){
		return PVContent::updateFileContent($fields);
	}//end pv_updateContent()
	
	function pv_updateFileContentWithFile($fields){
		return PVContent::updateFileContentWithFile($fields);
	}//end pv_updateFileContentWithFileFromArray($args)
	
	function pv_updateImageContent($fields){
		return PVContent::updateImageContent($fields);
	}//end pv_updateContent()
	
	function pv_updateProductContent($args){
		return PVContent::updateProductContent($args);
	}
	
	function pv_updateImageContentWithFile($fields){
		return PVContent::updateImageContentWithFile($fields);
	}//end pv_updateContent()
	
	
	function pv_updateCategory($fields){
		return PVContent::updateCategory($fields);
	}//end pv_updateCategory
	
	function pv_deleteContent($content_id, $recursive=FALSE){
		return PVContent::deleteContent($content_id, $recursive);
	}//end deleteContent($content_id)
	
	function pv_deleteTextContent($content_id, $recursive=FALSE){
		return PVContent::deleteContent($content_id, $recursive);
	}//end deleteContent($content_id)
	
	function pv_deleteImageContent($content_id, $recursive=FALSE){
		return PVContent::deleteContent($content_id, $recursive);
	}//end deleteContent($content_id)
	
	function pv_deleteVideoContent($content_id, $recursive=FALSE){
		return PVContent::dePVContentontent($content_id, $recursive);
	}//end deleteContent($content_id)
	
	function pv_deleteEventContent($content_id, $recursive=FALSE){
		return PVContent::deleteContent($content_id, $recursive);
	}//end deleteContent($content_id)
	
	function pv_deleteAudioContent($content_id, $recursive=FALSE){
		return PVContent::deleteContent($content_id, $recursive);
	}//end deleteContent($content_id)
	
	function pv_deleteCategory($category_id, $recursive=FALSE){
		return PVContent::deleteCategory($category_id, $recursive=FALSE);
	}//end deleteContent($content_id)

	
	function pv_deleteFileContent($content_id, $recursive=FALSE){
		return PVContent::deleteContent($content_id, $recursive);
	}//end deleteContent($content_id)
	
	
	function pv_addContentTaxonomy($content_id, $taxonomy_term, $taxonomy_term_parent=''){
		return PVContent::addContentTaxonomy($content_id, $taxonomy_term, $taxonomy_term_parent);
	}//end getApplicationID($app_unique_name)
	
	function pv_updateContentTaxonomy($content_id, $taxonomy_term, $taxonomy_term_parent=''){
		return PVContent::updateContentTaxonomy($content_id, $taxonomy_term, $taxonomy_term_parent);
	}//end getApplicationID($app_unique_name)
	
	function pv_getContentTaxonomy($content_id,$taxonomy_term_parent='' ){
		return PVContent::getContentTaxonomy($content_id,$taxonomy_term_parent );
	}//end getApplicationID($app_unique_name)
	
	function pv_clearContentTaxonomy($content_id,  $taxonomy_term_parent=''){
		return PVContent::clearContentTaxonomy($content_id,  $taxonomy_term_parent);
	}
	
	function pv_getCategoryIDByAlias($category_alias, $app_id='', $category_type='', $parent_category=''){
		return PVContent::getCategoryIDByAlias($category_alias, $app_id, $category_unique_name, $parent_category);
	}//end getApplicationID($app_unique_name)
	
	function pv_addComment($content_id, $comment_text='', $owner_id='', $comment_date='', $comment_approved='', $comment_title='', $comment_parent='', $comment_author='', $comment_author_email='', $comment_author_website='', $comment_type=''	){
		return PVComments::addComment($content_id, $comment_text, $owner_id, $comment_date, $comment_approved, $comment_title, $comment_parent, $comment_author, $comment_author_email, $comment_author_website, $comment_type	);
	}//end getApplicationID($app_unique_name)
	
	function pv_getCommentList($content_id, $owner_id='', $comment_parent='', $comment_type=''){
		return PVComments::getCommentList($content_id, $owner_id, $comment_parent, $comment_type);
	}//end getApplicationID($app_unique_name)
	
	function pv_updateComment($comment_id, $content_id='', $comment_text='', $owner_id='', $comment_date='', $comment_approved='', $comment_title='', $comment_parent='', $comment_author='', $comment_author_email='', $comment_author_website='', $comment_type=''){
		return PVComments::updateComment($comment_id, $content_id, $comment_text, $owner_id, $comment_date, $comment_approved, $comment_title, $comment_parent, $comment_author, $comment_author_email, $comment_author_website, $comment_type);
	}//end getApplicationID($app_unique_name)
	
	
	function pv_getComment($comment_id){
		return PVComments::getComment($comment_id);
	}//end getApplicationID($app_unique_name)
	
	function pv_deleteComment($comment_id, $deleteChildrenComments=false){
		return PVComments::deleteComment($comment_id, $deleteChildrenComments);
	}//end getApplicationID($app_unique_name)
	
	
	function pv_getContentIDByAlias($content_alias, $app_id='', $owner_id='', $content_type=''){
		return PVContent::getContentIDByAlias($content_alias, $app_id, $owner_id, $content_type);
	}
	
	function pv_createUniqueContentAlias($content_alias, $content_id='', $count=''){
		return PVContent::createUniqueContentAlias($content_alias, $content_id, $count);
	}
	
	function pv_createUniqueCategoryAlias($category_alias, $category_id='', $count=''){
		return PVContent::createUniqueCategoryAlias($category_alias, $category_id, $count);
	}
	
	function pv_getContentIDByContentAlias($content_alias, $app_id='', $content_type=''){
		return PVContent::getContentIDByContentAlias($content_alias, $app_id, $content_type);
	}
	
	function pv_addContentView($content_id, $user_id=''){
		return PVContent::addContentView($content_id, $user_id);
	}
	
	function pv_addContentViewUnique($content_id, $user_id=''){
		return PVContent::addContentViewUnique($content_id, $user_id);
	}
	
	function pv_getContentViews($content_id){
		return PVContent::getContentViews($content_id);
	}
	
	
	function pv_getContentViewsList($args=''){
		return PVContent::getContentViewsList($args);
	}
	
	
	function pv_getContentViewsByUserID($content_id, $user_id=''){
		return PVContent::getContentViewsByUserID($content_id, $user_id);
	}
	
	function pv_getContentViewsByIP($content_id, $ip){
		return PVContent::getContentViewsByIP($content_id, $ip);
	}
	
	function pv_parseSQLOperators($string, $content_term, $encapsulate=TRUE){
		return PVTools::parseSQLOperators($string, $content_term, $encapsulate);
	}//end parseSQLOperators($string, $content_term)
	
	function pv_addContentMultiAuthor($user_id, $content_id, $owner_status=''){
		return PVContent::addContentMultiAuthor($user_id, $content_id, $owner_status);
	}//end parseSQLOperators($string, $content_term)
	
	
	function pv_isContentMultiAuthor($user_id, $content_id, $owner_status=''){
		return PVContent::isContentMultiAuthor($user_id, $content_id, $owner_status);
	}//end parseSQLOperators($string, $content_term)
	
	function pv_getContentMutliAuthorList($args){
		return PVContent::getContentMutliAuthorList($args);
	}//end parseSQLOperators($string, $content_term)
	
	function pv_removeContentMultiAuthor($user_id, $content_id, $owner_status=''){
		return PVContent::removeContentMultiAuthor($user_id, $content_id, $owner_status);
	}//end parseSQLOperators($string, $content_term)
	
	
	function pv_addContentRelationship($content_id, $related_content_id, $content_relationship_type=''){
		return PVContent::addContentRelationship($content_id, $related_content_id, $content_relationship_type);
	}//end parseSQLOperators($string, $content_term)
	
	function pv_getContentRelationshipList($args){
		return PVContent::getContentRelationshipList($args);
	}//end parseSQLOperators($string, $content_term)
	
	function pv_checkContentRelationship($content_id, $related_content_id, $content_relationship_type=''){
		return PVContent::checkContentRelationship($content_id, $related_content_id, $content_relationship_type);
	}//end parseSQLOperators($string, $content_term)
	
	function pv_removeContentRelationship($content_id, $related_content_id=0, $content_relationship_type=''){
		return PVContent::removeContentRelationship($content_id, $related_content_id, $content_relationship_type);
	}//end parseSQLOperators($string, $content_term)
	
	/**
	 * File Manager
	 */
	
	function pv_phpFileUpload($params){
		return PVFileManager::phpFileUpload($params);
	}//end phpFileUpload($params)
	
	function pv_deleteDirectory($directory){
		return PVFileManager::deleteDirectory($directory);
	}//end deleteDirectory($directory)
	
	function pv_getFileSize_NTFS($file_name){
		return PVFileManager::getFileSize_NTFS($file_name);
	}//end deleteDirectory($directory)
	
	function pv_getFileSize_PERL($filename){
		return PVFileManager::getFileSize_PERL($filename);
	}//end getFileSize_PERL($filename)
	
	function pv_getFilesInDirectory($directory){
		return PVFileManager::getFilesInDirectory($directory);
	}//end getFilesInDirectory($directory)
	
	function pv_loadFile($filePath, $charSet, $mode){
		return PVFileManager::loadFile($filePath, $charSet, $mode);
	}//end loadFile($filePath, $charSet, $mode)
	
	function pv_writeFile($filePath, $mode, $content, $encoding){
		return PVFileManager::writeFile($filePath, $mode, $content, $encoding);
	}//end writeFile($filePath, $mode, $content, $encoding)
	
	function pv_writeNewFile($filePath, $mode, $content, $encoding){
		return PVFileManager::writeNewFile($filePath, $mode, $content, $encoding);
	}//end writeFile($filePath, $mode, $content, $encoding)
	
	function pv_rewriteNewFile($filePath, $mode, $content, $encoding){
		return PVFileManager::rewriteNewFile($filePath, $mode, $content, $encoding);
	}//end rewriteNewFile($filePath, $mode, $content, $encoding)
	
	function pv_copyFile($currentFile, $newFile){
		return PVFileManager::copyFile($currentFile, $newFile);
	}//end copyFile($currentFile, $newFile)
	
	function pv_copyNewFile($currentFile, $newFile){
		return PVFileManager::copyNewFile($currentFile, $newFile);
	}//end copyNewFile($currentFile, $newFile)
	
	function pv_copyDirectory($currentDirectory, $newDirectory){
		return PVFileManager::copyDirectory($currentDirectory, $newDirectory);
	}//end copyDirectory($currentDirectory, $newDirectory)
	
	function pv_copyNewDirectory($currentDirectory, $newDirectory){
		return PVFileManager::copyNewDirectory($currentDirectory, $newDirectory);
	}//end copyDirectory($currentDirectory, $newDirectory)
	
	function pv_copyEntity($source, $target, $chmod=0777, $recursive=false){
		return PVFileManager::copyEntity($source, $target, $chmod=0777, $recursive=false);
	}//end copyDirectory($currentDirectory, $newDirectory)
	
	function pv_copyNewEntity($source, $target, $chmod=0777, $recursive=false){
		return PVFileManager::copyNewEntity($source, $target, $chmod=0777, $recursive=false);
	}//end copyDirectory($currentDirectory, $newDirectory)
	
	function pv_copyFileFromUrl($url, $destination, $filename=''){
		return PVFileManager::copyFileFromUrl($url, $destination, $filename);
	}//end copyDirectory($currentDirectory, $newDirectory)
	
	function pv_uploadFileFromContent($content_id,  $file_name, $tmp_name, $file_size, $file_type){
		return PVFileManager::uploadFileFromContent($content_id,  $file_name, $tmp_name, $file_size, $file_type);
	}//end copyDirectory($currentDirectory, $newDirectory)
	
	
	function pv_getLastestFileInDirectory($dir){
		return PVFileManager::getLastestFileInDirectory($dir);
	}//end copyDirectory($currentDirectory, $newDirectory)
	
	function pv_deleteFile($file){
		return PVFileManager::deleteFile($file);
	}//end copyDirectory($currentDirectory, $newDirectory)
	
	/*
	 * Validator.php
	 * 
	 */
	
	function pv_isInteger($int){
		return PVValidator::isInteger($int);
	}//end pv_isInteger($int)
	
	function pv_isDouble($double){
		return PVValidator::isDouble($double);
	}//end pv_isDouble($double)
	
	function pv_isAudioFile($mimetype){
		return PVValidator::isAudioFile($mimetype);
	}//end isAudioFile
	
	function pv_isMidiFile($mimetype){
		return PVValidator::isMidiFile($mimetype);
	}//end isAudioFile
	
	function pv_isMpegAudioFile($mimetype){
		return PVValidator::isMpegAudioFile($mimetype);
	}//end isAudioFile
	
	function pv_isAiffFile($mimetype){
		return PVValidator::isAiffFile($mimetype);
	}//end isAudioFile
	
	function pv_isWavFile($mimetype){
		return PVValidator::isWavFile($mimetype);
	}//end isAudioFile
	
	function pv_isRealAudioFile($mimetype){
		return PVValidator::isRealAudioFile($mimetype);
	}//end isAudioFile
	
	function pv_isVideoFile($mimetype){
		return PVValidator::isVideoFile($mimetype);
	}//end isAudioFile
	
	function pv_isMpegVideoFile($mimetype){
		return PVValidator::isMpegVideoFile($mimetype);
	}//end isAudioFile
	
	function pv_isQuickTimeFile($mimetype){
		return PVValidator::isQuickTimeFile($mimetype);
	}//end isAudioFile
	
	function pv_isMovFile($mimetype){
		return PVValidator::isMovFile($mimetype);
	}//end isAudioFile
	
	function pv_isMxuFile($mimetype){
		return PVValidator::isMxuFile($mimetype);
	}//end isAudioFile
	
	function pv_isAviFile($mimetype){
		return PVValidator::isAviFile($mimetype);
	}//end isAudioFile
	
	function pv_isImageFile($mimetype){
		return PVValidator::isImageFile($mimetype);
	}//end isAudioFile
	
	function pv_isBmpFile($mimetype){
		return PVValidator::isBmpFile($mimetype);
	}//end isAudioFile
	
	function pv_isGifFile($mimetype){
		return PVValidator::isGifFile($mimetype);
	}//end isAudioFile
	
	function pv_isIefFile($mimetype){
		return PVValidator::isIefFile($mimetype);
	}//end isAudioFile
	
	function pv_isJpegFile($mimetype){
		return PVValidator::isJpegFile($mimetype);
	}//end isAudioFile
	
	function pv_isPngFile($mimetype){
		return PVValidator::isPngFile($mimetype);
	}//end isAudioFile
	
	function pv_isTiffFile($mimetype){
		return PVValidator::isTiffFile($mimetype);
	}//end isAudioFile
	
	function pv_isCompressedFile($mimetype){
		return PVValidator::isCompressedFile($mimetype);
	}//end isAudioFile
	
	function pv_isZipFile($mimetype){
		return PVValidator::isZipFile($mimetype);
	}//end isAudioFile
	
	function pv_isGTarFile($mimetype){
		return PVValidator::isGTarFile($mimetype);
	}//end isAudioFile
	
	function pv_isTarFile($mimetype){
		return PVValidator::isTarFile($mimetype);
	}//end isAudioFile
	
	function pv_isCssFile($mimetype){
		return PVValidator::isCssFile($mimetype);
	}//end isAudioFile
	
	function pv_isHtmlFile($mimetype){
		return PVValidator::isHtmlFile($mimetype);
	}//end isAudioFile
	
	function pv_isHtmFile($mimetype){
		return PVValidator::isHtmFile($mimetype);
	}//end isAudioFile
	
	function pv_isAscFile($mimetype){
		return PVValidator::isAscFile($mimetype);
	}//end isAudioFile
	
	function pv_isTxtFile($mimetype){
		return PVValidator::isTxtFile($mimetype);
	}//end isAudioFile
	
	function pv_isRtxFile($mimetype){
		return PVValidator::isRtxFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftWordFile($mimetype){
		return PVValidator::isMicrosoftWordFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftWordDocFile($mimetype){
		return PVValidator::isMicrosoftWordDocFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftWordDocxFile($mimetype){
		return PVValidator::isMicrosoftWordDocxFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftExcelFile($mimetype){
		return PVValidator::isMicrosoftExcelFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftExcelXLSFile($mimetype){
		return PVValidator::isMicrosoftExcelXLSFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftExcelXLSXFile($mimetype){
		return PVValidator::isMicrosoftExcelXLSXFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftPowerPointFile($mimetype){
		return PVValidator::isMicrosoftPowerPointFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftPPTFile($mimetype){
		return PVValidator::isMicrosoftPPTFile($mimetype);
	}//end isAudioFile
	
	function pv_isMicrosoftPPTXFile($mimetype){
		return PVValidator::isMicrosoftPPTXFile($mimetype);
	}//end isAudioFile
	
	function pv_isPdfFile($mimetype){
		return PVValidator::isPdfFile($mimetype);
	}//end isAudioFile
	
	function pv_isActiveUrl($url){
		return PVValidator::isActiveUrl($url);
	}//end isAudioFile
	
	function pv_isValidUrl($url){
		return PVValidator::isValidUrl($url);
	}//end isAudioFile
	
	function pv_isValidEmail($email){
		return PVValidator::isValidEmail($email);
	}//end isValidEmail
	
	function pv_isApplicationInstalled($app_unique_id){
		return PVValidator::isApplicationInstalled($app_unique_id);
	}//end isValidEmail
	
	function pv_isApplicationEnabled($app_unique_id){
		return PVValidator::isApplicationEnabled($app_unique_id);
	}//end isValidEmail
	
	function pv_checkFileMimeType($file_location, $mime_text, $search_method='STRING_POSITION'){
		return PVValidator::checkFileMimeType($file_location, $mime_text, $search_method);
	}//end isValidEmail
	
	
	
	/**
	 * ImageRenderer.php
	 */
	
	function pv_uploadImage($file_name, $tmp_name, $file_size, $file_type, $image_width=300 , $image_height=300 , $thumbnailwidth=150, $thumbnailheight=150){
		return PVImageRenderer::uploadImage($file_name, $tmp_name, $file_size, $file_type, $image_width , $image_height , $thumbnailwidth, $thumbnailheight);
	}//end pv_isInteger($int)
	
	function pv_updateImage($content_id,  $content_type, $file_name, $tmp_name, $file_size, $file_type, $image_width=300 , $image_height=300 , $thumbnailwidth=150, $thumbnailheight=150){
		return PVImageRenderer::updateImage($content_id,  $content_type, $file_name, $tmp_name, $file_size, $file_type, $image_width=300 , $image_height=300 , $thumbnailwidth=150, $thumbnailheight=150);
	}//end pv_isInteger($int)
	
	function pv_resizeImageGD($name,$filename,$new_w=150,$new_h=150){
		return PVImageRenderer::resizeImageGD($name,$filename,$new_w=150,$new_h=150);
	}//end pv_isInteger($int)
	
	/**
	 * MailSystem.php
	 * 
	 */
	
	function pv_sendEmail($reciever, $sender='',$subject='', $message='', $carboncopy='', $blindcopy='', $html_email='', $file='' ){
		return PVMail::sendEmail($reciever, $sender,$subject, $message, $carboncopy, $blindcopy, $html_email, $file='' );
	}//end pv_sendEmail
	
	function pv_mailSingleAddress($reciever, $sender='',$subject='', $message='', $carboncopy='', $blindcopy='', $html_email='', $file=''){
		return PVMail::sendEmailPHP($reciever, $sender,$subject, $message, $carboncopy, $blindcopy, $html_email, $file='');
	}//end pv_sendEmail
	
	function pv_mailSingleAddressSMTP($reciever, $sender,$subject, $message, $carboncopy, $blindcopy, $html_email, $file=''){
		return PVMail::sendEMailSMTP($reciever, $sender,$subject, $message, $carboncopy, $blindcopy, $html_email, $file='');
	}//end pv_sendEmail
	
	
	function pv_sendEmailPHP($reciever, $sender='',$subject='', $message='', $carboncopy='', $blindcopy='', $html_email='', $file=''){
		return PVMail::sendEmailPHP($reciever, $sender,$subject, $message, $carboncopy, $blindcopy, $html_email, $file='');
	}//end pv_sendEmail
	
	function pv_sendEmailSMTP($reciever, $sender,$subject, $message, $carboncopy, $blindcopy, $html_email, $file=''){
		return PVMail::sendEMailSMTP($reciever, $sender,$subject, $message, $carboncopy, $blindcopy, $html_email, $file='');
	}//end pv_sendEmail
	
	
	/*
	 * FieldGenerator
	 */
	
	function pv_createField( $app_id=0, $owner_id=0 ,  $field_name="" ,$field_type="" ,$field_description="" , $field_title="" , $max_length=0, $max_size=0, $columns=0,$rows=0, $value="", $searchable=1 , $readonly=0 , $show_title=0 , $is_required=0 , $on_blur="" , $id="" , $on_change="", $on_click="" , $on_doubelclick="" , $on_focus="" , $on_keydown="" , $on_keyup="" , $on_keypress="" , $on_mousedown="" , $on_mouseup="" , $on_mousemove="" , $on_mouseover="" , $on_mouseout="" , $instructions="" , $show_instructions="" , $checked=0, $disabled=0 , $lang="", $align="", $accept="" , $field_class="" , $size="" , $field_prefix="" , $field_suffix="" , $field_css="" , $field_prefix="" , $field_suffix="" , $field_css="", $field_unique_name="" ){
		if(is_array($app_id)){
			return PVFields::createFieldFromArray($app_id);
		}
		else{
			return PVFields::createField( $app_id, $owner_id ,  $field_name ,$field_type ,$field_description , $field_title , $max_length, $max_size, $columns,$rows, $value, $searchable , $readonly , $show_title , $is_required , $on_blur , $id , $on_change, $on_click , $on_doubelclick , $on_focus , $on_keydown , $on_keyup , $on_keypress , $on_mousedown , $on_mouseup , $on_mousemove , $on_mouseover , $on_mouseout , $instructions , $show_instructions , $checked, $disabled , $lang, $align, $accept , $field_class , $size , $field_prefix , $field_suffix , $field_css , $field_prefix , $field_suffix , $field_css, $field_unique_name );
		}
	}//end pv_createField
	
	
	function pv_getFieldsList( $app_id='', $owner_id='', $field_unique_name='', $order_by_clause='', $limit=''){
		return PVFields::getFieldsList( $app_id, $owner_id, $field_unique_name, $order_by_clause, $limit);
	}//end pv_getContentList
	
	function pv_getField($field_id=0){
		return PVFields::getField($field_id);
	}//end getFileContent($content_id)
	
	
	function pv_updateField($args){
		return  PVFields::updateField($args);	
	}
	
	
	function pv_deleteField($field_id, $DELETE_OPTIONS=true, $DELETE_VALUES=true){
		return  PVFields::deleteField($field_id, $DELETE_OPTIONS, $DELETE_VALUES);	
	}
	
	
	function pv_createFieldOption($args){
		return  PVFields::createFieldOption($args);	
	}
	
	function pv_getFieldOptionsList($args){
		return  PVFields::getFieldOptionsList($args);	
	}
	
	function pv_getFieldOption($field_id, $option_id){
		return  PVFields::getFieldOption($field_id, $option_id);	
	}
	
	function pv_updateFieldOption($args){
		return  PVFields::updateFieldOption($args);	
	}
	
	function pv_deleteFieldOption($field_id, $option_id){
		return  PVFields::deleteFieldOption($field_id, $option_id);	
	}
	
	function pv_createFieldValue($args){
		return  PVFields::createFieldValue($args);	
	}
	
	function pv_getFieldValueList( $args ){
		return  PVFields::getFieldValueList( $args );	
	}
	
	function pv_getFieldValue($field_value_id){
		return  PVFields::getFieldValue($field_value_id);	
	}
	function pv_updateFieldValue($args){
		return  PVFields::updateFieldValue($args);	
	}
	
	function pv_deleteFieldValue($field_value_id){
		return  PVFields::deleteFieldValue($field_value_id);	
	}
	
	function pv_addContentFieldRelationship($content_id, $field_id){
		return  PVFields::addContentFieldRelationship($content_id, $field_id);	
	}
	
	function pv_getContentFieldRelationshipList($args){
		return  PVFields::getContentFieldRelationshipList($args);	
	}
	
	function pv_deleteContentFieldRelationship($content_id=0, $field_id=0){
		return  PVFields::deleteContentFieldRelationship($content_id, $field_id);	
	}
	
	
	
	
	
	
	function pv_setContentVariables($args){
		return  PVFields::setContentVariables($args);	
	}
	
	function pv_setCommentVariables($args){
		return  PVFields::setCommentVariables($args);	
	}
	
	function pv_setPointVariables($args){
		return  PVFields::setPointVariables($args);	
	}
	
	function pv_setRatingVariables($args){
		return  PVFields::setRatingVariables($args);	
	}
	
	function pv_addContentFilter($content_type, $content_section, $command, $function){
		return PVFields::addContentFilter($content_type, $content_section, $command, $function);	
	}
	
	function pv_addCommentFilter($comment_type, $comment_section, $command, $function){
		return PVFields::addCommentFilter($comment_type, $comment_section, $command, $function);	
	}
	
	function pv_addPointFilter($point_type, $point_section, $command, $function){
		return PVFields::addPointFilter($point_type, $point_section, $command, $function);	
	}
	
	function pv_addRatingFilter($rating_type, $rating_section, $command, $function){
		return PVFields::addRatingFilter($rating_type, $rating_section, $command, $function);	
	}
	
	function pv_executeContentFilter($content_section, $content){
		return PVFields::executeContentFilter($content_section, $content);	
	}
	
	function pv_printTheContentID($apply_filter=true){
			return PVFields::printContentID($apply_filter);	
	}
	
	function pv_getTheContentID($apply_filter=true){
		return PVFields::getContentID($apply_filter);	
	}
	
	function pv_printTheContentAppID($apply_filter=true){
		return PVFields::printContentAppID($apply_filter);	
	}
	
	function pv_getTheContentAppID($apply_filter=true){
		return PVFields::getContentAppID($apply_filter);	
	}
	
	function pv_printTheContentTitle($apply_filter=true){
		return PVFields::printContentTitle($apply_filter);	
	}
	
	
	function pv_getTheContentTitle($apply_filter=true){
		return PVFields::getContentTitle($apply_filter);	
	}
	
	function pv_printTheContentOwnerID($apply_filter=true){
		return PVFields::printContentOwnerID($apply_filter);	
	}
	
	
	function pv_getTheContentOwnerID($apply_filter=true){
		return PVFields::getContentOwnerID($apply_filter);	
	}
	
	function pv_printTheContentParentID($apply_filter=true){
		return PVFields::printContentParentID($apply_filter);	
	}
	
	
	function pv_getTheContentParentID($apply_filter=true){
		return PVFields::getContentParentID($apply_filter);	
	}
	
	function pv_printTheContentAlias($apply_filter=true){
		return PVFields::printContentAlias($apply_filter);	
	}
	
	
	function pv_getTheContentAlias($apply_filter=true){
		return PVFields::getContentAlias($apply_filter);	
	}
	
	function pv_printTheContentDescription($apply_filter=true){
		return PVFields::printContentDescription($apply_filter);	
	}
	
	
	function pv_getTheContentDescription($apply_filter=true){
		return PVFields::getContentDesciption($apply_filter);	
	}
	
	function pv_printTheContentMetaTags($apply_filter=true){
		return PVFields::printContentMetaTags($apply_filter);	
	}
	
	
	function pv_getTheContentMetaTags($apply_filter=true){
		return PVFields::getContentMetaTags($apply_filter);	
	}
	
	function pv_printTheContentMetaDescription($apply_filter=true){
		return PVFields::printContentMetaDescription($apply_filter);	
	}
	
	
	function pv_getTheContentMetaDescription($apply_filter=true){
		return PVFields::getContentMetaDescription($apply_filter);	
	}
	
	function pv_printTheContentThumbnailLocation($apply_filter=true){
		return PVFields::printContentThumbnailLocation($apply_filter);	
	}
	
	
	function pv_getTheContentThumbnailLocation($apply_filter=true){
		return PVFields::getContentThumbnailLocation($apply_filter);	
	}
	
	function pv_printTheContentThumbnail($apply_filter=true){
		return PVFields::printContentThumbnail($apply_filter);	
	}
	
	
	function pv_getTheContentThumbnail($apply_filter=true){
		return PVFields::getContentThumbnail($apply_filter);	
	}
	
	function pv_printTheContentDateCreated($apply_filter=true){
		return PVFields::printContentDateCreated($apply_filter);	
	}
	
	
	function pv_getTheContentDateCreated($apply_filter=true){
		return PVFields::getContentDateCreated($apply_filter);	
	}
	
	function pv_printTheContentDateModified($apply_filter=true){
		return PVFields::printContentDateModified($apply_filter);	
	}
	
	
	function pv_getTheContentDateModified($apply_filter=true){
		return PVFields::getContentDateModified($apply_filter);	
	}
	
	
	function pv_printTheContentDateActive($apply_filter=true){
		return PVFields::printContentDateActive($apply_filter);	
	}
	
	function pv_getTheContentDateActive($apply_filter=true){
		return PVFields::getContentDateActive($apply_filter);	
	}
	
	function pv_printTheContentDateInactive($apply_filter=true){
		return PVFields::printContentDateInactive($apply_filter);	
	}
	
	function pv_getTheContentDateInactive($apply_filter=true){
		return PVFields::getContentDateInactive($apply_filter);	
	}
	
	
	function pv_printTheContentIsSearchable($apply_filter=true){
		return PVFields::printContentIsSearchable($apply_filter);	
	}
	
	function pv_getTheContentIsSearchable($apply_filter=true){
		return PVFields::getContentIsSearchable($apply_filter);	
	}
	
	
	function pv_printContentAllowComments($apply_filter=true){
		return PVFields::printContentAllowComments($apply_filter);	
	}
	
	function pv_getTheContentAllowComments($apply_filter=true){
		return PVFields::getContentAllowComments($apply_filter);	
	}
	
	
	function pv_printTheContentAllowRating($apply_filter=true){
		return PVFields::printContentAllowRating($apply_filter);	
	}
	
	function pv_getTheContentAllowRating($apply_filter=true){
		return PVFields::getContentAllowRating($apply_filter);	
	}
	
	
	function pv_printTheContentActive($apply_filter=true){
		return PVFields::printContentActive($apply_filter);	
	}
	
	function pv_getTheContentActive($apply_filter=true){
		return PVFields::getContentActive($apply_filter);	
	}
	
	function pv_printTheContentPromoted($apply_filter=true){
		return PVFields::printContentPromoted($apply_filter);	
	}
	
	function pv_getTheContentPromoted($apply_filter=true){
		return PVFields::getContentPromoted($apply_filter);	
	}
	
	
	function pv_printTheContentPermissions($apply_filter=true){
		return PVFields::printContentPermissions($apply_filter);	
	}
	
	function pv_getTheContentPermissions($apply_filter=true){
		return PVFields::getContentPermissions($apply_filter);	
	}
	
	
	function pv_printTheContentType($apply_filter=true){
		return PVFields::printContentType($apply_filter);	
	}
	
	function pv_getTheContentType($apply_filter=true){
		return PVFields::getContentType($apply_filter);	
	}
	
	function pv_printTheContentLanguage($apply_filter=true){
		return PVFields::printContentLanguage($apply_filter);	
	}
	
	function pv_getTheContentLanguage($apply_filter=true){
		return PVFields::getContentLanguage($apply_filter);	
	}
	
	function pv_printTheContentTranslate($apply_filter=true){
		return PVFields::printContentTranslate($apply_filter);	
	}
	
	function pv_getTheContentTranslate($apply_filter=true){
		return PVFields::getContentTranslate($apply_filter);	
	}
	
	function pv_printTheContentApproved($apply_filter=true){
		return PVFields::printContentApproved($apply_filter);	
	}
	
	function pv_getTheContentApproved($apply_filter=true){
		return PVFields::getContentApproved($apply_filter);	
	}
	
	function pv_printTheContentCategory($apply_filter=true){
		return PVFields::printContentCategory($apply_filter);	
	}
	
	function pv_getTheContentCategory($apply_filter=true){
		return PVFields::getContentCategorye($apply_filter);	
	}
	
	function pv_printTheContentParameters($apply_filter=true){
		return PVFields::printContentParameters($apply_filter);	
	}
	
	function pv_getTheContentParameters($apply_filter=true){
		return PVFields::getContentParemeters($apply_filter);	
	}
	
	function pv_printTheContentOrder($apply_filter=true){
		return PVFields::printContentOrder($apply_filter);	
	}
	
	function pv_getTheContentOrder($apply_filter=true){
		return PVFields::getContentOrder($apply_filter);	
	}
	
	function pv_printTheContentSymLink($apply_filter=true){
		return PVFields::printContentSymLink($apply_filter);	
	}
	
	function pv_getTheContentSymLink($apply_filter=true){
		return PVFields::getContentSymLink($apply_filter);	
	}
	
	function pv_printTheContentTaxonomy($apply_filter=true){
		return PVFields::printContentTaxonomy($apply_filter);	
	}
	
	function pv_getTheContentTaxonomy($apply_filter=true){
		return PVFields::getContentTaxonomy($apply_filter);	
	}
	
	function pv_printTheContentImageType($apply_filter=true){
		return PVFields::printContentImageType($apply_filter);	
	}
	
	function pv_getTheContentImageType($apply_filter=true){
		return PVFields::getContentImageType($apply_filter);	
	}
	
	function pv_printTheContentImageSize($apply_filter=true){
		return PVFields::printContentImageSize($apply_filter);	
	}
	
	function pv_getTheContentImageSize($apply_filter=true){
		return PVFields::getContentImageSize($apply_filter);	
	}
	
	function pv_printTheContentImageUrl($apply_filter=true){
		return PVFields::printContentImageUrl($apply_filter);	
	}
	
	function pv_getTheContentImageUrl($apply_filter=true){
		return PVFields::getContentImageUrl($apply_filter);	
	}
	
	function pv_printTheContentThumbUrl($apply_filter=true){
		
		return PVFields::printContentThumbUrl($apply_filter);	
	}
	
	function pv_getTheContentThumbUrl($apply_filter=true){
		return PVFields::getContentThumbUrl($apply_filter);	
	}
	
	function pv_printTheContentImageHeight($apply_filter=true){
		return PVFields::printContentImageHeight($apply_filter);	
	}
	
	function pv_getTheContentImageHeight($apply_filter=true){
		return PVFields::getContentImageHeight($apply_filter);	
	}
	
	function pv_printTheContentImageWidth($apply_filter=true){
		return PVFields::printContentImageWidth($apply_filter);	
	}
	
	function pv_getTheContentImageWidth($apply_filter=true){
		return PVFields::getContentImageWidth($apply_filter);	
	}
	function pv_printTheContentImageThumbWidth($apply_filter=true){
		return PVFields::printContentImageThumbWidth($apply_filter);	
	}
	
	function pv_getTheContentImageThumbWidth($apply_filter=true){
		return PVFields::getContentImageThumbWidth($apply_filter);	
	}
	
	function pv_printTheContentImageThumbHeight($apply_filter=true){
		return PVFields::printContentImageThumbHeight($apply_filter);	
	}
	
	function pv_getTheContentImageThumbHeight($apply_filter=true){
		return PVFields::getContentImageThumbHeight($apply_filter);	
	}
	
	function pv_printTheContentImageSrc($apply_filter=true){
		return PVFields::printContentImageSrc($apply_filter);	
	}
	
	function pv_getTheContentImageSrc($apply_filter=true){
		return PVFields::getContentImageSrc($apply_filter);	
	}
	
	function pv_prinThetContentText($apply_filter=true){
		return PVFields::printContentText($apply_filter);	
	}
	
	function pv_getTheContentText($apply_filter=true){
		return PVFields::getContentText($apply_filter);	
	}
	
	
	function pv_printTheContentTextPageGroup($apply_filter=true){
		return PVFields::printContentTextPageGroup($apply_filter);	
	}
	
	function pv_getTheContentTextPageGroup($apply_filter=true){
		return PVFields::getContentTextPageGroup($apply_filter);	
	}
	
	
	function pv_printTheContentTextPageNumber($apply_filter=true){
		return PVFields::printContentTextPageNumber($apply_filter);	
	}
	
	function pv_getTheContentTextPageNumber($apply_filter=true){
		return PVFields::getContentTextPageNumber($apply_filter);	
	}
	
	function pv_printTheContentTextSrc($apply_filter=true){
		return PVFields::printContentTextSrc($apply_filter);	
	}
	
	function pv_getTheContentTextSrc($apply_filter=true){
		return PVFields::getContentTextSrc($apply_filter);	
	}
	
	
	function pv_printTheContentVideoType($apply_filter=true){
		return PVFields::printContentVideoType($apply_filter);	
	}
	
	function pv_getContentVideoType($apply_filter=true){
		return PVFields::getContentVideoType($apply_filter);	
	}
	
	
	function pv_printTheContentVideoLength($apply_filter=true){
		return PVFields::printContentVideoLength($apply_filter);	
	}
	
	function pv_getTheContentVideoLength($apply_filter=true){
		return PVFields::getContentVideoLength($apply_filter);	
	}
	
	
	function pv_printTheContentVideoAllowEmbedding($apply_filter=true){
		return PVFields::printContentVideoAllowEmbedding($apply_filter);	
	}
	
	function pv_getTheContentVideoAllowEmbedding($apply_filter=true){
		return PVFields::getContentVideoAllowEmbedding($apply_filter);	
	}
	
	
	function pv_printTheContentVideoFlvFile($apply_filter=true){
		return PVFields::printContentVideoFlvFile($apply_filter);	
	}
	
	function pv_getTheContentVideoFlvFile($apply_filter=true){
		return PVFields::getContentVideoFlvFile($apply_filter);	
	}
	
	
	
	function pv_printTheContentVideoMp4File($apply_filter=true){
		return PVFields::printContentVideoMp4File($apply_filter);	
	}
	
	function pv_getTheContentVideoMp4File($apply_filter=true){
		return PVFields::getContentVideoMp4File($apply_filter);	
	}
	
	
	function pv_printTheContentVideoWmvFile($apply_filter=true){
		return PVFields::printContentVideoWmvFile($apply_filter);	
	}
	
	function pv_getTheContentVideoWmvFile($apply_filter=true){
		return PVFields::getContentVideoWmvFile($apply_filter);	
	}
	
	
	function pv_printTheContentVideoMpegvFile($apply_filter=true){
		return PVFields::printContentVideoMpegvFile($apply_filter);	
	}
	
	function pv_getTheContentVideoMpegFile($apply_filter=true){
		return PVFields::getContentVideoMpegFile($apply_filter);	
	}
	
	
	function pv_printTheContentVideoRmFile($apply_filter=true){
		return PVFields::printContentVideoRmFile($apply_filter);	
	}
	
	function pv_getTheContentVideoRmFile($apply_filter=true){
		return PVFields::getContentVideoRmFile($apply_filter);	
	}
	
	function pv_printTheContentVideoAviFile($apply_filter=true){
		return PVFields::printContentVideoAviFile($apply_filter);	
	}
	
	function pv_getTheContentVideoAviFile($apply_filter=true){
		return PVFields::getContentVideoAviFile($apply_filter);	
	}
	
	
	function pv_printTheContentVideoMovFile($apply_filter=true){
		return PVFields::printContentVideoMovFile($apply_filter);	
	}
	
	function pv_getTheContentVideoMovFile($apply_filter=true){
		return PVFields::getContentVideoMovFile($apply_filter);	
	}
	
	function pv_printTheContentVideoAsfFile($apply_filter=true){
		return PVFields::printContentVideoAsfFile($apply_filter);	
	}
	
	function pv_getTheContentVideoAsfFile($apply_filter=true){
		return PVFields::getContentVideoAsfFile($apply_filter);	
	}
	
	function pv_printTheContentVideoEnableHQ($apply_filter=true){
		return PVFields::printContentVideoEnableHQ($apply_filter);	
	}
	
	function pv_getTheContentVideoEnableHQ($apply_filter=true){
		return PVFields::getContentVideoEnableHQ($apply_filter);	
	}
	
	
	function pv_printTheContentVideoAutoHQ($apply_filter=true){
		return PVFields::printContentVideoAutoHQ($apply_filter);	
	}
	
	function pv_getTheContentVideoAutoHQ($apply_filter=true){
		return PVFields::getContentVideoAutoHQ($apply_filter);	
	}
	
	
	function pv_printTheContentVideoSrc($apply_filter=true){
		return PVFields::printContentVideoSrc($apply_filter);	
	}
	
	function pv_getTheContentVideoSrc($apply_filter=true){
		return PVFields::getContentVideoSrc($apply_filter);	
	}
	
	
	function pv_printTheContentVideoEmbed($apply_filter=true){
		return PVFields::printContentVideoEmbed($apply_filter);	
	}
	
	function pv_getTheContentVideoEmbed($apply_filter=true){
		return PVFields::getContentVideoEmbed($apply_filter);	
	}
	
	
	function pv_printTheContentEventLocation($apply_filter=true){
		return PVFields::printContentEventLocation($apply_filter);	
	}
	
	function pv_getTheContentEventLocation($apply_filter=true){
		return PVFields::getContentEventLocation($apply_filter);	
	}
	
	
	function pv_printTheContentEventStartDate($apply_filter=true){
		return PVFields::printContentEventStartDate($apply_filter);	
	}
	
	function pv_getTheContentEventStartDate($apply_filter=true){
		return PVFields::getContentEventStartDate($apply_filter);	
	}
	
	
	function pv_printTheContentEventEndDate($apply_filter=true){
		return PVFields::printContentEventEndDate($apply_filter);	
	}
	
	function pv_getTheContentEventEndDate($apply_filter=true){
		return PVFields::getContentEventEndDate($apply_filter);	
	}
	
	
	function pv_printTheContentEventCountry($apply_filter=true){
		return PVFields::printContentEventCountry($apply_filter);	
	}
	
	function pv_getTheContentEventCountry($apply_filter=true){
		return PVFields::getContentEventCountry($apply_filter);	
	}
	
	
	
	function pv_printTheContentEventAddress($apply_filter=true){
		return PVFields::printContentEventAddress($apply_filter);	
	}
	
	function pv_getTheContentEventAddress($apply_filter=true){
		return PVFields::getContentEventAddress($apply_filter);	
	}
	
	
	function pv_printTheContentEventCity($apply_filter=true){
		return PVFields::printContentEventCity($apply_filter);	
	}
	
	function pv_getTheContentEventCity($apply_filter=true){
		return PVFields::getContentEventCity($apply_filter);	
	}
	
	
	function pv_printTheContentEventState($apply_filter=true){
		return PVFields::printContentEventState($apply_filter);	
	}
	
	function pv_getTheContentEventState($apply_filter=true){
		return PVFields::getContentEventState($apply_filter);	
	}
	
	
	function pv_printTheContentEventZip($apply_filter=true){
		return PVFields::printContentEventZip($apply_filter);	
	}
	
	function pv_getTheContentEventZip($apply_filter=true){
		return PVFields::getContentEventZip($apply_filter);	
	}
	
	
	function pv_printTheContentEventMap($apply_filter=true){
		return PVFields::printContentEventMap($apply_filter);	
	}
	
	function pv_getTheContentEventMap($apply_filter=true){
		return PVFields::getContentEventMap($apply_filter);	
	}
	
	
	function pv_printTheContentEventSrc($apply_filter=true){
		return PVFields::printContentEventSrc($apply_filter);	
	}
	
	function pv_getTheContentEventSrc($apply_filter=true){
		return PVFields::getContentEventSrc($apply_filter);	
	}
	
	
	function pv_printTheContentEventUndefinedEndtime($apply_filter=true){
		return PVFields::printContentEventUndefinedEndtime($apply_filter);	
	}
	
	function pv_getTheContentEventUndefinedEndtime($apply_filter=true){
		return PVFields::getContentEventUndefinedEndtime($apply_filter);	
	}
	
	function pv_printTheContentAudioLength($apply_filter=true){
		return PVFields::printContentAudioLength($apply_filter);	
	}
	
	function pv_getTheContentAudioLength($apply_filter=true){
		return PVFields::getContentAudioLength($apply_filter);	
	}
	
	function pv_printTheContentAudioMidFile($apply_filter=true){
		return PVFields::printContentAudioMidFile($apply_filter);	
	}
	
	function pv_getTheContentAudioMidFile($apply_filter=true){

		return PVFields::getContentAudioMidFile($apply_filter);	
	}
	
	function pv_printTheContentAudioWavFile($apply_filter=true){
		return PVFields::printContentAudioWavFile($apply_filter);	
	}
	
	function pv_getTheContentAudioWavFile($apply_filter=true){
		return PVFields::getContentAudioWavFile($apply_filter);	
	}
	
	function pv_printTheContentAudioAifFile($apply_filter=true){
		return PVFields::printContentAudioAifFile($apply_filter);	
	}
	
	function pv_getTheContentAudioAifFile($apply_filter=true){
		return PVFields::getContentAudioAifFile($apply_filter);	
	}
	
	function pv_printTheContentAudioMp3File($apply_filter=true){
		return PVFields::printContentAudioMp3File($apply_filter);	
	}
	
	function pv_getTheContentAudioMp3File($apply_filter=true){
		return PVFields::getContentAudioMp3File($apply_filter);	
	}
	
	function pv_printTheContentAudioRaFile($apply_filter=true){
		return PVFields::printContentAudioRaFile($apply_filter);	
	}
	
	function pv_getTheContentEventAudioRaFile($apply_filter=true){
		return PVFields::getContentEventAudioRaFile($apply_filter);	
	}
	
	function pv_printTheContentAudioSampleLength($apply_filter=true){
		return PVFields::printContentAudioSampleLength($apply_filter);	
	}
	
	function pv_getTheContentAudioSampleLength($apply_filter=true){
		return PVFields::getContentAudioSampleLength($apply_filter);	
	}
	
	function pv_printTheContentAudioSrc($apply_filter=true){
		return PVFields::printContentAudioSrc($apply_filter);	
	}
	
	function pv_getTheContentAudioSrc($apply_filter=true){
		return PVFields::getContentAudioSrc($apply_filter);	
	}
	
	function pv_printTheContentFileType($apply_filter=true){
		return PVFields::printContentFileType($apply_filter);	
	}
	
	function pv_getTheContentFileType($apply_filter=true){
		return PVFields::getContentFileType($apply_filter);	
	}
	
	function pv_printTheContentFileSize($apply_filter=true){
		return PVFields::printContentFileSize($apply_filter);	
	}
	
	function pv_getTheContentFileSize($apply_filter=true){
		return PVFields::getContentFileSize($apply_filter);	
	}
	
	function pv_printTheContentFileLocation($apply_filter=true){
		return PVFields::printContentFileLocation($apply_filter);	
	}
	
	function pv_getTheContentFileLocation($apply_filter=true){
		return PVFields::getContentFileLocation($apply_filter);	
	}
	
	function pv_printTheContentFileName($apply_filter=true){
		return PVFields::printContentFileName($apply_filter);	
	}
	
	function pv_getTheContentFileName($apply_filter=true){
		return PVFields::getContentFileName($apply_filter);	
	}
	
	function pv_printTheContentFileSrc($apply_filter=true){
		return PVFields::printContentFileSrc($apply_filter);	
	}
	
	function pv_getTheContentFileSrc($apply_filter=true){
		return PVFields::getContentFileSrc($apply_filter);	
	}
	
	function pv_printTheContentFileDownloadable($apply_filter=true){
		return PVFields::printContentFileDownloadable($apply_filter);	
	}
	
	function pv_getTheContentFileDownloadable($apply_filter=true){
		return PVFields::getContentFileDownloadable($apply_filter);	
	}
	
	function pv_printTheContentFileMaxDownloads($apply_filter=true){
		return PVFields::printContentFileMaxDownloads($apply_filter);	
	}
	
	function pv_getTheContentFileMaxDownloads($apply_filter=true){
		return PVFields::getContentFileMaxDownloads($apply_filter);	
	}
	
	
	function pv_printTheContentFileVersion($apply_filter=true){
		return PVFields::printContentFileVersion($apply_filter);	
	}
	
	function pv_getTheContentFileVersion($apply_filter=true){
		return PVFields::getContentFileVersion($apply_filter);	
	}
	
	function pv_printTheContentFileLicense($apply_filter=true){
		return PVFields::printContentFileLicense($apply_filter);	
	}
	
	function pv_getTheContentFileLicense($apply_filter=true){
		return PVFields::getContentFileLicense($apply_filter);	
	}
	
	function pv_printTheContentProductID($apply_filter=true){
		return PVFields::printContentProductID($apply_filter);	
	}
	
	function pv_getTheContentProductID($apply_filter=true){
		return PVFields::getContentProductID($apply_filter);	
	}
	
	function pv_printTheContentProductSKU($apply_filter=true){
		return PVFields::printContentProductSKU($apply_filter);	
	}
	
	function pv_getTheContentProductSKU($apply_filter=true){
		return PVFields::getContentProductSKU($apply_filter);	
	}
	
	function pv_printTheContentProductIDSKU($apply_filter=true){
		return PVFields::printContentProductIDSKU($apply_filter);	
	}
	
	function pv_getTheContentProductIDSKU($apply_filter=true){
		return PVFields::getContentProductIDSKU($apply_filter);	
	}
	
	function pv_printTheContentProductVendorID($apply_filter=true){
		return PVFields::printContentProductVendorID($apply_filter);	
	}
	
	function pv_getTheContentProductVendorID($apply_filter=true){
		return PVFields::getContentProductVendorID($apply_filter);	
	}
	
	function pv_printTheContentProductQuantity($apply_filter=true){
		return PVFields::printContentProductQuantity($apply_filter);	
	}
	
	function pv_getTheContentProductQuantity($apply_filter=true){
		return PVFields::getContentProductQuantity($apply_filter);	
	}
	
	function pv_printTheContentProductPrice($apply_filter=true){
		return PVFields::printContentProductPrice($apply_filter);	
	}
	
	function pv_getTheContentProductPrice($apply_filter=true){
		return PVFields::getContentProductPrice($apply_filter);	
	}
	
	function pv_printTheContentProductDiscountPrice($apply_filter=true){
		return PVFields::printContentProductDiscountPrice($apply_filter);	
	}
	
	function pv_getTheContentProductDiscountPrice($apply_filter=true){
		return PVFields::getContentProductDiscountPrice($apply_filter);	
	}
	
	function pv_printTheContentProductSize($apply_filter=true){
		return PVFields::printContentProductSize($apply_filter);	
	}
	
	function pv_getTheContentProductSize($apply_filter=true){
		return PVFields::getContentProductSize($apply_filter);	
	}
	
	function pv_printTheContentProductColor($apply_filter=true){
		return PVFields::printContentProductColor($apply_filter);	
	}
	
	function pv_getTheContentProductColor($apply_filter=true){
		return PVFields::getContentProductColor($apply_filter);	
	}
	
	function pv_printTheContentProductWeight($apply_filter=true){
		return PVFields::printContentProductWeight($apply_filter);	
	}
	
	function pv_getTheContentProductWeight($apply_filter=true){
		return PVFields::getContentProductWeight($apply_filter);	
	}
	
	function pv_printTheContentProductHeight($apply_filter=true){
		return PVFields::printContentProductHeight($apply_filter);	
	}
	
	function pv_getTheContentProductHeight($apply_filter=true){
		return PVFields::getContentProductHeight($apply_filter);	
	}
	
	function pv_printTheContentProductLength($apply_filter=true){
		return PVFields::printContentProductLength($apply_filter);	
	}
	
	function pv_getTheContentProductLength($apply_filter=true){
		return PVFields::getContentProductLength($apply_filter);	
	}
	
	function pv_printTheContentProductCurrency($apply_filter=true){
		return PVFields::printContentProductCurrency($apply_filter);	
	}
	
	function pv_getTheContentProductCurrency($apply_filter=true){
		return PVFields::getContentProductCurrency($apply_filter);	
	}
	
	function pv_printTheContentProductInStock($apply_filter=true){
		return PVFields::printContentProductInStock($apply_filter);	
	}
	
	function pv_getTheContentProductInStock($apply_filter=true){
		return PVFields::getContentProductInStock($apply_filter);	
	}
	
	function pv_printTheContentProductType($apply_filter=true){
		return PVFields::printContentProductType($apply_filter);	
	}
	
	function pv_getTheContentProductType($apply_filter=true){
		return PVFields::getContentProductType($apply_filter);	
	}
	
	function pv_printTheContentProductTaxID($apply_filter=true){
		return PVFields::printContentProductTaxID($apply_filter);	
	}
	
	function pv_getTheContentProductTaxID($apply_filter=true){
		return PVFields::getContentProductTaxID($apply_filter);	
	}
	
	function pv_printTheContentProductAttribute($apply_filter=true){
		return PVFields::printContentProductAttribute($apply_filter);	
	}
	
	function pv_getTheContentProductAttribute($apply_filter=true){
		return PVFields::getContentProductAttribute($apply_filter);	
	}
	
	function pv_printTheContentProductVersion($apply_filter=true){
		return PVFields::printContentProductVersion($apply_filter);	
	}
	
	function pv_getTheContentProductVersion($apply_filter=true){
		return PVFields::getContentProductVersion($apply_filter);	
	}
	
	
	
	function pv_printCommentOwnerID($apply_filter=true){
		return PVFields::printCommentOwnerID($apply_filter);	
	}
	
	function pv_getCommentOwnerID($apply_filter=true){
		return PVFields::getCommentOwnerIDc($apply_filter);	
	}
	
	function pv_printCommentDate($apply_filter=true){
		return PVFields::printCommentDate($apply_filter);	
	}
	
	function pv_getCommentDate($apply_filter=true){
		return PVFields::getCommentDate($apply_filter);	
	}
	
	function pv_printCommentApproved($apply_filter=true){
		return PVFields::printCommentApproved($apply_filter);	
	}
	
	
	function pv_getCommentApproved($apply_filter=true){
		return PVFields::getCommentApproved($apply_filter);	
	}
	
	function pv_printCommentTitle($apply_filter=true){
		return PVFields::printCommentTitle($apply_filter);	
	}
	
	function pv_getCommentTitle($apply_filter=true){
		return PVFields::getCommentTitle($apply_filter);	
	}
	
	function pv_printCommentText($apply_filter=true){
		return PVFields::printCommentText($apply_filter);	
	}
	
	function pv_getCommentText($apply_filter=true){
		return PVFields::getCommentText($apply_filter);	
	}
	
	function pv_printCommentParent($apply_filter=true){
		return PVFields::printCommentParent($apply_filter);	
	}
	
	function pv_getCommentParent($apply_filter=true){
		return PVFields::getCommentParent($apply_filter);	
	}
	
	
	function pv_printCommentAuthor($apply_filter=true){
		return PVFields::printCommentAuthor($apply_filter);	
	}
	
	function pv_getCommentAuthor($apply_filter=true){
		return PVFields::getCommentAuthor($apply_filter);	
	}
	
	function pv_printCommentAuthorWebsite($apply_filter=true){
		return PVFields::printCommentAuthorWebsite($apply_filter);	
	}
	
	function pv_getCommentAuthorWebsite($apply_filter=true){
		return PVFields::getCommentAuthorWebsite($apply_filter);	
	}
	
	function pv_printCommentContentID($apply_filter=true){
		return PVFields::printCommentContentID($apply_filter);	
	}
	
	function pv_getCommentContentID($apply_filter=true){
		return PVFields::getCommentContentID($apply_filter);	
	}
	

	function pv_printCommentID($apply_filter=true){
		return PVFields::printCommentID($apply_filter);	
	}
	
	function pv_getCommentID($apply_filter=true){
		return PVFields::getCommentID($apply_filter);	
	}
	
	function pv_printCommentOwnerIP($apply_filter=true){
		return PVFields::printCommentOwnerIP($apply_filter);	
	}
	
	function pv_getCommentOwnerIP($apply_filter=true){
		return PVFields::getCommentOwnerIP($apply_filter);	
	}
	
	function pv_printPointValue($apply_filter=true){
		return PVFields::printPointValue($apply_filter);	
	}
	
	function pv_getPointValue($apply_filter=true){
		return PVFields::getPointValue($apply_filter);	
	}
	
	function pv_printPointContentID($apply_filter=true){
		return PVFields::printPointContentID($apply_filter);	
	}
	
	function pv_getPointContentID($apply_filter=true){
		return PVFields::getPointContentID($apply_filter);	
	}
	
	function pv_printPointCommentID($apply_filter=true){
		return PVFields::printPointCommentID($apply_filter);	
	}
	
	function pv_getPointCommentID($apply_filter=true){
		return PVFields::getPointCommentID($apply_filter);	
	}
	
	function pv_printPointAppID($apply_filter=true){
		return PVFields::printPointAppID($apply_filter);	
	}
	
	function pv_getPointAppID($apply_filter=true){
		return PVFields::getPointAppID($apply_filter);	
	}
	
	function pv_printPointType($apply_filter=true){
		return PVFields::printPointType($apply_filter);	
	}
	
	function pv_getPointType($apply_filter=true){
		return PVFields::getPointType($apply_filter);	
	}
	
	function pv_printPointUserID($apply_filter=true){
		return PVFields::printPointUserID($apply_filter);	
	}
	
	function pv_getPointUserID($apply_filter=true){
		return PVFields::getPointUserID($apply_filter);	
	}
	
	function pv_printPointID($apply_filter=true){
		return PVFields::printPointID($apply_filter);	
	}
	
	function pv_getPointID($apply_filter=true){
		return PVFields::getPointID($apply_filter);	
	}
	
	function pv_printPointUserIP($apply_filter=true){
		return PVFields::printPointUserIP($apply_filter);	
	}
	
	function pv_getPointUserIP($apply_filter=true){
		return PVFields::getPointUserIP($apply_filter);	
	}
	
	function pv_printPointDate($apply_filter=true){
		return PVFields::printPointDate($apply_filter);	
	}
	
	function pv_getPointDate($apply_filter=true){
		return PVFields::getPointDate($apply_filter);	
	}
	
	function pv_printRatingContentID($apply_filter=true){
		return PVFields::printRatingContentID($apply_filter);	
	}
	
	function pv_getRatingContentID($apply_filter=true){
		return PVFields::getRatingContentID($apply_filter);	
	}
	
	function pv_printRatingCommentID($apply_filter=true){
		return PVFields::printRatingCommentID($apply_filter);	
	}
	
	function pv_getRatingCommentID($apply_filter=true){
		return PVFields::getRatingCommentID($apply_filter);	
	}
	
	function pv_printRating($apply_filter=true){
		return PVFields::printRating($apply_filter);	
	}
	
	function pv_getRating($apply_filter=true){
		return PVFields::getRating($apply_filter);	
	}
	
	function pv_printRatingUserID($apply_filter=true){
		return PVFields::printRatingUserID($apply_filter);	
	}
	
	function pv_getRatingUserID($apply_filter=true){
		return PVFields::getRatingUserID($apply_filter);	
	}
	
	function pv_printRatingID($apply_filter=true){
		return PVFields::printRatingID($apply_filter);	
	}
	
	function pv_getRatingID($apply_filter=true){
		return PVFields::getRatingID($apply_filter);	
	}
	
	function pv_printRatingDate($apply_filter=true){
		return PVFields::printRatingDate($apply_filter);	
	}
	
	function pv_getRatingDate($apply_filter=true){
		return PVFields::getRatingDate($apply_filter);	
	}
	
	function pv_printRatingDateRerated($apply_filter=true){
		return PVFields::printRatingDateRerated($apply_filter);	
	}
	
	function pv_getRatingDateRerated($apply_filter=true){
		return PVFields::getRatingDateRerated($apply_filter);	
	}
	
	function pv_printRatingUserIP($apply_filter=true){
		return PVFields::printRatingUserIP($apply_filter);	
	}
	
	function pv_getRatingUserIP($apply_filter=true){
		return PVFields::getRatingUserIP($apply_filter);	
	}
	
	
	
	/**
	 * Supplemental Functions
	 */
	
	
	
	function stripslashes_gpc(&$value){
        $value = stripslashes($value);
    }
    
    function pv_successMessage($text){
    	?>
    	<div class="success-message" >
    	<p><?php echo $text;?></p>
    	</div><!-- success-message -->
    	<?php
    
    }//end pv
    
    function pv_errorMessage($text){
    	?>
    	<div class="error-message" >
    	<p><?php echo $text;?></p>
    	</div><!-- success-message -->
    	<?php
    
    }//end pv
    
    function pv_retrieveGET($variable){
    	if(isset($_GET[$variable])){
    		return $_GET[$variable];
    	}
    }//end 
	
	 function pv_retrievePOST($variable){
    	if(isset($_POST[$variable])){
    		return $_POST[$variable];
    	}
    }//end 
	
    /**
     * 
     */
	
	
	

?>