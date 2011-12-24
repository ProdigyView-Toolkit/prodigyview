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

	/**
	 * Generates a random string of lettters and numbers. String can be customized on the length
	 * and the characters used to generate the string.
	 *
	 * @param int $char_count The length of characters the string will be. Default is 15 chars
	 * @param string $chars The characters that will be used to make up the string. Default is 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'
	 *
	 * @return string $string The auto generated string
	 * @access public
	 */
	public static function generateRandomString($char_count = 15, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $char_count, $chars);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('char_count' => $char_count, 'chars' => $chars), array('event' => 'args'));
		$char_count = $filtered['char_count'];
		$chars = $filtered['chars'];

		$charLength = (strlen($chars) - 1);
		$returnString = $chars{rand(0, $charLength)};

		for ($i = 1; $i < $char_count; $i = strlen($returnString)) {

			$newchar = $chars{rand(0, $charLength)};

			if ($newchar != $returnString{$i - 1}) {
				$returnString .= $newchar;
			}
		}//end for

		self::_notify(get_class() . '::' . __FUNCTION__, $returnString, $char_count, $chars);
		$returnString = self::_applyFilter(get_class(), __FUNCTION__, $returnString, array('event' => 'return'));

		return $returnString;
	}

	/**
	 * Truncates a strings of text to a certain length and applies trailing characters. Generally used for
	 * creating 'Read More...' text descrptions.
	 *
	 * @param string $str The string to truncate
	 * @param int $length The length to truncate the string too. Default is 10 characters.
	 * @param string $trailing Trailing text to add at the end of string once it is truncated. Default text is '...'
	 * @param boolean $strip_tags Strips out any html tags. Default is true.
	 * @param string $allowed_tags Tags to allow if strip_tags is set to true.
	 *
	 * @return string $truncated A The string when truncated
	 * @access public
	 */
	function truncateText($string, $length = 10, $trailing = '...', $strip_tags = TRUE, $allowed_tags = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string, $length, $trailing, $strip_tags, $allowed_tags);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('string' => $string, 'length' => $length, 'trailing' => $trailing, 'strip_tags' => $strip_tags, 'allowed_tags' => $allowed_tags), array('event' => 'args'));
		$string = $filtered['string'];
		$length = $filtered['length'];
		$trailing = $filtered['trailing'];
		$strip_tags = $filtered['strip_tags'];
		$allowed_tags = $filtered['allowed_tags'];

		if ($strip_tags == TRUE && !empty($string)) {
			$string = strip_tags($string, $allowed_tags);
		}

		$truncated = '';

		if (mb_strlen($string) > $length) {
			$truncated = mb_substr($string, 0, $length) . $trailing;
		} else {
			$truncated = $string . $trailing;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $truncated, $string, $length, $trailing, $strip_tags, $allowed_tags);
		$truncated = self::_applyFilter(get_class(), __FUNCTION__, $truncated, array('event' => 'return'));

		return $truncated;
	}//end truncateText

	/**
	 * Returns the full url of the current page. Inclded in the return will be if the page is being https connect,
	 * a port if any, and the uri.
	 *
	 * @return string $url Url of the current page.
	 * @access public
	 */
	public static function getCurrentUrl() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$current_page_url = 'http';

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$current_page_url .= 's';
		}

		$current_page_url .= '://';

		if ($_SERVER['SERVER_PORT'] != '80') {
			$current_page_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$current_page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $current_page_url);
		$current_page_url = self::_applyFilter(get_class(), __FUNCTION__, $current_page_url, array('event' => 'return'));

		return $current_page_url;
	}//end getCurrentCurl

	/**
	 * Returns the current url with the uri. The url at max will only be
	 * www.example.com
	 *
	 * @return string $url The current url without the uri
	 * @access public
	 */
	public static function getCurrentBaseUrl() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$current_page_url = 'http';

		if (@$_SERVER['HTTPS'] == 'on') { $current_page_url .= 's';
		}
		$current_page_url .= '://';

		if ($_SERVER['SERVER_PORT'] != '80') {
			$current_page_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
		} else {
			$current_page_url .= $_SERVER['SERVER_NAME'];
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $current_page_url);
		$current_page_url = self::_applyFilter(get_class(), __FUNCTION__, $current_page_url, array('event' => 'return'));

		return $current_page_url;
	}//end getCurrentCurl

	/**
	 * Takes in an array and forms that array into a query string with ? & =. Passing in array such as
	 * array('arg1'='doo', 'arg2'=>'sec''rae', 'arg3'=>'me') with return '?$arg1=doo&arg2=rae&arg3=me'
	 *
	 * @param array variables A string of variables to turn into a query string
	 *
	 * @return string The array uri into string format
	 * @access public
	 */
	public static function formUrlParameters($variables) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $variables);

		$variables = self::_applyFilter(get_class(), __FUNCTION__, $variables, array('event' => 'args'));

		$appendix = '?';

		$first = 1;
		foreach ($variables as $key => $value) {
			if ($first == 1) {
				$appendix .= $key . '=' . urlencode($value);
			} else {
				$appendix .= '&' . $key . '=' . urlencode($value);
			}
			$first = 0;
		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $appendix, $variables);
		$appendix = self::_applyFilter(get_class(), __FUNCTION__, $appendix, array('event' => 'return'));

		return $appendix;

	}//end form url

	/**
	 * Takes in an array and forms that array into a query string with /'s. Passing in array such as
	 * array('arg1'='doo', 'arg2'=>'sec''rae', 'arg3'=>'me') with return 'doo/rae/me'
	 *
	 * @param array variables A string of variables to turn into a query string
	 *
	 * @return string The array uri into string format
	 * @access public
	 */
	public static function formUrlPath($variables) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $variables);

		$variables = self::_applyFilter(get_class(), __FUNCTION__, $variables, array('event' => 'args'));

		$appendix = '';

		$first = 1;
		foreach ($variables as $key => $value) {
			if ($first == 1) {
				$appendix .= urlencode($value);
			} else {
				$appendix .= '/' . urlencode($value);
			}
			$first = 0;
		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $appendix, $variables);
		$appendix = self::_applyFilter(get_class(), __FUNCTION__, $appendix, array('event' => 'return'));

		return $appendix;

	}//end form url

	/**
	 * Searched for an value within another array recursively.
	 *
	 * @param mixed $needle The needle can either be a value or an array of values to be searched for
	 * @param array $haystack The array to be search in
	 * @param boolean $strict Sets if comparison is performed loosely or tightly
	 * @param array $path The path in the array in which the needle was found
	 *
	 * @return mixed $path Returns a path if the array was found, otherwise returns false
	 * @access public
	 */
	function arraySearchRecursive($needle, $haystack, $strict = false, $path = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $needle, $haystack, $strict, $path);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('needle' => $needle, 'haystack' => $haystack, 'strict' => $strict, 'path' => $path), array('event' => 'args'));
		$needle = $filtered['needle'];
		$haystack = $filtered['haystack'];
		$strict = $filtered['strict'];
		$path = $filtered['path'];

		if (!is_array($haystack)) {
			return false;
		}

		if (is_array($needle)) {
			foreach ($needle as $point) {
				$return = self::arraySearchRecursive($point, $haystack, $strict, $path);
				if (!empty($return))
					$path[] = $return;
			}//dnc vpfdz h

			if (!empty($path))
				return $path;

			return false;
		}

		foreach ($haystack as $key => $value) {
			if (is_array($value) && $sub_path = self::arraySearchRecursive($needle, $value, $strict, $path)) {
				$path = array_merge($path, array($key), $sub_path);
				return $path;
			} else if ((!$strict && $value == $needle) || ($strict && $value === $needle)) {
				$path[] = $key;
				return $path;
			}
		}
		return false;
	}

	/**
	 * Adds an options to the options collection to be retrieved later. Options is any data that is stored by a multiple
	 * set of keys.
	 *
	 * @param array $args The Arguements that make up the option
	 * 			-'app_id' _id_: The id of the application this option is associated with
	 * 			-'user_id' _id_: The id of the user this option is associated with
	 * 			-'content_id' _id_: The id of the content this option is associated with
	 * 			-'option_name' _string_: The name of the option
	 * 			-'option_value' _mixed_: The information to be stored in this option.
	 * 			-'option_type' _string_: The type of option this option is considered to be, if any
	 *
	 * @return id $option_id The id of the new option
	 * @access public
	 */
	public static function addOption($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getOptionDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$query = "INSERT INTO " . pv_getOptionsTableName() . "( app_id, user_id , content_id, option_name, option_value , option_type) VALUES(  '$app_id' , '$user_id' , '$content_id' , '$option_name', '$option_value' , '$option_type' )";
		$option_id = PVDatabase::return_last_insert_query($query, "option_id", pv_getOptionsTableName());

		self::_notify(get_class() . '::' . __FUNCTION__, $option_id, $args);
		$option_id = self::_applyFilter(get_class(), __FUNCTION__, $option_id, array('event' => 'return'));

		return $option_id;
	}//end addOption

	/**
	 * Retrieved a list of the options stored. Follows the ProdigyView Standard Search.
	 *
	 * @param array $args The Arguements that make up the option that can be search for
	 * 			-'app_id' _id_: The id of the application this option is associated with
	 * 			-'user_id' _id_: The id of the user this option is associated with
	 * 			-'content_id' _id_: The id of the content this option is associated with
	 * 			-'option_name' _string_: The name of the option
	 * 			-'option_value' _mixed_: The information to be stored in this option.
	 * 			-'option_type' _string_: The type of option this option is considered to be, if any
	 * 			-'option_date'_date_: The date of the object
	 *
	 * @return array $options A list of options retrieved based on the passed parameters
	 * @access public
	 */
	public static function getOptionList($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getOptionDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));

		$custom_where = $args['custom_where'];
		$custom_join = $args['custom_join'];
		$custom_select = $args['custom_select'];
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$content_array = array();
		$table_name = pv_getOptionsTableName();
		$db_type = PVDatabase::getDatabaseType();

		$first = 1;

		$WHERE_CLAUSE = "";

		if (!empty($app_id)) {

			$app_id = trim($app_id);

			if ($first == 0 && ($app_id[0] != '+' && $app_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($app_id[0] == '+' || $app_id[0] == ',') && $first == 1) {
				$app_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($app_id, 'app_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($option_value)) {

			$option_value = trim($option_value);

			if ($first == 0 && ($option_value[0] != '+' && $option_value[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($option_value[0] == '+' || $option_value[0] == ',') && $first == 1) {
				$option_value[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($option_value, 'option_value');

			$first = 0;
		}//end not empty app_id

		if (!empty($user_id)) {

			$user_id = trim($user_id);

			if ($first == 0 && ($user_id[0] != '+' && $user_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($user_id[0] == '+' || $user_id[0] == ',') && $first == 1) {
				$user_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($user_id, 'user_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($content_id)) {

			$content_id = trim($content_id);

			if ($first == 0 && ($content_id[0] != '+' && $content_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($content_id[0] == '+' || $content_id[0] == ',') && $first == 1) {
				$content_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($content_id, 'content_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($option_type)) {

			$option_type = trim($option_type);

			if ($first == 0 && ($option_type[0] != '+' && $option_type[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($option_type[0] == '+' || $option_type[0] == ',') && $first == 1) {
				$option_type[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($option_type, 'option_type');

			$first = 0;
		}//end not empty app_id

		if (!empty($option_name)) {

			$option_name = trim($option_name);

			if ($first == 0 && ($option_name[0] != '+' && $option_name[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($option_name[0] == '+' || $option_name[0] == ',') && $first == 1) {
				$option_name[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($option_name, 'option_name');

			$first = 0;
		}//end not empty app_id

		if (!empty($option_date)) {

			$option_date = trim($option_date);

			if ($first == 0 && ($option_date[0] != '+' && $option_date[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($option_date[0] == '+' || $option_date[0] == ',') && $first == 1) {
				$option_date[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($option_date, 'option_date');

			$first = 0;
		}//end not empty app_id

		if (!empty($option_id)) {

			$option_id = trim($option_id);

			if ($first == 0 && ($option_id[0] != '+' && $option_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($option_id[0] == '+' || $option_id[0] == ',') && $first == 1) {
				$option_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($option_id, 'option_id');

			$first = 0;
		}//end not empty app_id

		$JOINS = '';

		if (!empty($custom_where)) {
			$WHERE_CLAUSE .= ' ' . $custom_where . ' ';
		}

		if (!empty($custom_join)) {
			$JOINS .= ' ' . $custom_join . ' ';
		}

		if ($join_apps) {
			$JOINS .= " JOIN " . pv_getApplicationsTableName() . " ON " . pv_getOptionsTableName() . ".app_id=" . pv_getApplicationsTableName() . ".app_id ";
		}

		if ($join_content) {
			$JOINS .= " JOIN " . pv_getContentTableName() . " ON " . pv_getOptionsTableName() . ".content_id=" . pv_getContentTableName() . ".content_id ";
		}

		if ($join_users) {
			$JOINS .= " JOIN " . pv_getLoginTableName() . " ON " . pv_getOptionsTableName() . ".user_id=" . pv_getLoginTableName() . ".user_id ";
		}

		if (!empty($WHERE_CLAUSE)) {
			$WHERE_CLAUSE = ' WHERE ' . $WHERE_CLAUSE;
		}

		if (!empty($distinct)) {
			$PREFIX_ARGS .= " DISTINCT $distinct, ";
		}

		if (!empty($limit) && $db_type == 'mssql' && !$paged) {
			$PREFIX_ARGS .= " TOP $limit ";
		}

		if ($paged) {
			$page_results = PVDatabase::getPagininationOffset($table_name, $JOINS, $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);

			if ($db_type == 'mysql' || $db_type == 'postgresql') {
				$limit = ' ' . $page_results['limit_offset'];
			} else if ($db_type == 'mssql') {
				$WHERE_CLAUSE .= ' ' . $page_results['limit_offset'];
				$table_name = $page_results['from_clause'];
			}
		}

		if (!empty($group_by)) {
			$WHERE_CLAUSE .= " GROUP BY $group_by";
		}

		if (!empty($having)) {
			$WHERE_CLAUSE .= " HAVING $having";
		}

		if (!empty($order_by)) {
			$WHERE_CLAUSE .= " ORDER BY $order_by";
		}

		if (!empty($limit) && !$paged && ($db_type == 'mysql' || $db_type == 'postgresql')) {
			$WHERE_CLAUSE .= " LIMIT $limit";
		}

		if ($paged) {
			$WHERE_CLAUSE .= " $limit";
		}

		if (empty($custom_select)) {
			$custom_select = '*';
		}

		$query = "$prequery SELECT $prefix_args$custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
		$result = PVDatabase::query($query);

		while ($row = PVDatabase::fetchArray($result)) {
			if ($paged) {
				$row['current_page'] = $page_results['current_page'];
				$row['last_page'] = $page_results['last_page'];
				$row['total_pages'] = $page_results['total_pages'];
			}

			array_push($content_array, $row);
		}//end while

		$content_array = PVDatabase::formatData($content_array);
		self::_notify(get_class() . '::' . __FUNCTION__, $content_array, $args);
		$content_array = self::_applyFilter(get_class(), __FUNCTION__, $content_array, array('event' => 'return'));

		return $content_array;
	}//end getOptionList

	/**
	 * Retrieves an option by the ID of the option.
	 *
	 * @param id $option_id The id of the option to be retrieved
	 *
	 * @return array $option The data associated with the option
	 * @access public
	 */
	public static function getOptionByID($option_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $option_id);

		$option_id = self::_applyFilter(get_class(), __FUNCTION__, $option_id, array('event' => 'args'));

		if (!empty($option_id)) {
			$query = "SELECT option_id, app_id, user_id , content_id, option_name, option_value , option_type FROM " . pv_getOptionsTableName() . " WHERE option_id= '$option_id' ";
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row = PVDatabase::formatData($row);

			self::_notify(get_class() . '::' . __FUNCTION__, $row, $option_id);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row;
		}//end

	}//end

	/**
	 * Update an option based upon the option's ID.
	 *
	 *  @param array $args The Arguements that can be updated
	 * 			-'app_id' _id_: The id of the application this option is associated with
	 * 			-'user_id' _id_: The id of the user this option is associated with
	 * 			-'content_id' _id_: The id of the content this option is associated with
	 * 			-'option_name' _string_: The name of the option
	 * 			-'option_value' _mixed_: The information to be stored in this option.
	 * 			-'option_type' _string_: The type of option this option is considered to be, if any
	 * 			-'option_id' _id_: Cannot be updated by used as the key for identying which row to update
	 *
	 * @return void
	 * @access public
	 */
	public static function updateOption($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getOptionDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		if (!empty($option_id)) {
			$query = "UPDATE  " . pv_getOptionsTableName() . " SET app_id='$app_id', user_id='$user_id' , content_id='$content_id', option_name='$option_name', option_value='$option_value' , option_type='$option_type' WHERE option_id='$option_id'";
			PVDatabase::query($query);
			self::_notify(get_class() . '::' . __FUNCTION__, $args);
			return $option_id;
		}

	}//end update updateUpdate

	/**
	 * Set an option based upon the values passed. If the update exist, the update will be updated. Otherwise the option wil created. A new vs old
	 * option is decided on if the following fields can be matched 'option_name', 'option_type', 'user_id', 'app_id', 'content_id'. Changing any
	 * one of these will be considered a new option.
	 *
	 * @param array $args The Arguements that can be used to set the option
	 * 			-'app_id' _id_: The id of the application this option is associated with
	 * 			-'user_id' _id_: The id of the user this option is associated with
	 * 			-'content_id' _id_: The id of the content this option is associated with
	 * 			-'option_name' _string_: The name of the option
	 * 			-'option_value' _mixed_: The information to be stored in this option.
	 * 			-'option_type' _string_: The type of option this option is considered to be, if any
	 *
	 * @return void
	 * @access public
	 */
	public static function setOption($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getOptionDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$search_args = PVDatabase::makeSafe($args);
		extract($search_args);

		$WHERE_CLAUSE = "";

		if (!empty($app_id) || !empty($option_name) || !empty($option_type) || !empty($content_id) || !empty($user_id) || !empty($custom_where) || !empty($option_id)) {
			$first = 1;

			$WHERE_CLAUSE .= " WHERE ";

			if (!empty($app_id)) {
				$app_id = PVDatabase::makeSafe($app_id);
				$WHERE_CLAUSE .= " app_id='$app_id' ";
				$first = 0;
			}

			if (!empty($option_name)) {
				$option_name = PVDatabase::makeSafe($option_name);
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " option_name='$option_name' ";
				$first = 0;
			}

			if (!empty($option_type)) {
				$option_type = PVDatabase::makeSafe($option_type);
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " option_type='$option_type' ";
				$first = 0;
			}

			if (!empty($content_id)) {
				$content_id = PVDatabase::makeSafe($content_id);
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " content_id='$content_id' ";
				$first = 0;
			}

			if (!empty($user_id)) {
				$user_id = PVDatabase::makeSafe($user_id);
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " user_id='$user_id' ";
				$first = 0;
			}

			if (!empty($option_id)) {
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " option_id='$option_id' ' ";
				$first = 0;
			}
		}

		$query = "SELECT option_id, app_id, user_id , content_id, option_name, option_value , option_type FROM " . pv_getOptionsTableName() . " $WHERE_CLAUSE ";
		$result = PVDatabase::query($query);

		if (PVDatabase::resultRowCount($result) > 0) {
			$query = "UPDATE  " . pv_getOptionsTableName() . " SET option_value='$option_value' $WHERE_CLAUSE";
			PVDatabase::query($query);
		} else {
			self::addOption($args);
		}

	}//setOption

	/**
	 * Retrieves the data of an option that has been stored based on the value parameters:
	 * 'option_name', 'option_type', 'user_id', 'app_id', 'content_id'
	 *
	 * @param array $args The Arguements that can be used to get the options value
	 * 			-'app_id' _id_: The id of the application this option is associated with
	 * 			-'user_id' _id_: The id of the user this option is associated with
	 * 			-'content_id' _id_: The id of the content this option is associated with
	 * 			-'option_name' _string_: The name of the option
	 * 			-'option_value' _mixed_: The information to be stored in this option.
	 * 			-'option_type' _string_: The type of option this option is considered to be, if any
	 *
	 * @return array $data The data associated with the search parameters
	 * @access public
	 */
	public static function getOption($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getOptionDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$WHERE_CLAUSE = "";

		if (!empty($app_id) || !empty($option_name) || !empty($option_type) || !empty($content_id) || !empty($user_id) || !empty($custom_where) || !empty($option_id)) {
			$first = 1;

			$WHERE_CLAUSE .= " WHERE ";

			if (!empty($app_id)) {
				$app_id = PVDatabase::makeSafe($app_id);
				$WHERE_CLAUSE .= " app_id='$app_id' ";
				$first = 0;
			}

			if (!empty($option_name)) {
				$option_name = PVDatabase::makeSafe($option_name);
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " option_name='$option_name' ";
				$first = 0;
			}

			if (!empty($option_type)) {
				$option_type = PVDatabase::makeSafe($option_type);
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " option_type='$option_type' ";
				$first = 0;
			}

			if (!empty($content_id)) {
				$content_id = PVDatabase::makeSafe($content_id);
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " content_id='$content_id' ";
				$first = 0;
			}

			if (!empty($user_id)) {
				$user_id = PVDatabase::makeSafe($user_id);
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " user_id='$user_id' ";
				$first = 0;
			}

			if (!empty($option_id)) {
				if ($first == 0) {
					$WHERE_CLAUSE .= " AND ";
				}
				$WHERE_CLAUSE .= " option_id='$option_id' ' ";
				$first = 0;
			}
		}

		$query = "SELECT option_id, app_id, user_id , content_id, option_name, option_value , option_type FROM " . pv_getOptionsTableName() . " $WHERE_CLAUSE ";
		$result = PVDatabase::query($query);

		if (PVDatabase::resultRowCount($result) > 0) {
			$row = PVDatabase::fetchArray($result);
			$row = PVDatabase::formatData($row);

			self::_notify(get_class() . '::' . __FUNCTION__, $row, $args);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row;
		}

		return array();
	}//setOption

	/**
	 * Retrieves the value of an option that has been stored based on the value parameters:
	 * 'option_name', 'option_type', 'user_id', 'app_id', 'content_id'
	 *
	 * @param array $args The Arguements that can be used to get the options value
	 * 			-'app_id' _id_: The id of the application this option is associated with
	 * 			-'user_id' _id_: The id of the user this option is associated with
	 * 			-'content_id' _id_: The id of the content this option is associated with
	 * 			-'option_name' _string_: The name of the option
	 * 			-'option_type' _string_: The type of option this option is considered to be, if any
	 *
	 * @return mixed $value The value of the retrieved option
	 * @access public
	 */
	public static function getOptionValue($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$row = self::getOption($args);

		if (isset($row['option_value'])) {

			self::_notify(get_class() . '::' . __FUNCTION__, $row, $args);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row['option_value'];
		}
	}//end getOption

	/**
	 * Delete an option based upon the id of the option.
	 *
	 * @param id $option_id The id of the option to be deleted
	 *
	 * @return void
	 * @access public
	 */
	public static function deleteOption($option_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $option_id);

		$option_id = self::_applyFilter(get_class(), __FUNCTION__, $option_id, array('event' => 'args'));
		$option_id = PVDatabase::makeSafe($option_id);

		if (!empty($option_id)) {
			$query = "DELETE FROM " . pv_getOptionsTableName() . " WHERE option_id='$option_id' ";
			PVDatabase::query($query);
			self::_notify(get_class() . '::' . __FUNCTION__, $option_id);
		}//end if

	}//end getOption

	private static function parseSQLArrayOperators($args, $content_term) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args, $content_term);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('args' => $args, 'content_term' => $content_term), array('event' => 'args'));
		$args = $filtered['args'];
		$content_term = $filtered['content_term'];

		$operator = $args['operator'];

		if (empty($operator)) {
			$operator = ' AND ';
		}
		$SQL = '';
		$mark = '';

		foreach ($args as $value) {
			if ($key != 'operator') {
				$SQL .= $mark . ' ' . $content_term . '=\'' . PVDatabase::makeSafe($value) . '\' ';
				$mark = $operator;
			}//end operator

		}//end foreach

		return $SQL;
	}//end parseSQLArrayOperators

	public static function parseSQLOperators($string, $content_term, $encapsulate = TRUE) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string, $content_term, $encapsulate);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('string' => $string, 'content_term' => $content_term, 'encapsulate' => $encapsulate), array('event' => 'args'));
		$string = $filtered['string'];
		$content_term = $filtered['content_term'];
		$encapsulate = $filtered['encapsulate'];

		$string = trim($string);
		$string = PVDatabase::makeSafe($string);

		//$string.=$content_term
		/*
		 if( strstr($string, '!') != 1){
		 $string=$content_term.'='
		 }
		 $string=str_replace('+', ' AND '.$content_term.'=', $string );
		 $string=str_replace(',', ' OR '.$content_term.'=', $string );*/

		$length = strlen($string);

		$ADD_PREFIX = true;
		$output = '';
		for ($i = 0; $i < $length; $i++) {

			if ($string[$i] == '!') {

				$output .= ' ' . $content_term . '!=\'';

				if ($i == 0) {
					$ADD_PREFIX = false;
				}
			} else if ($string[$i] == '+') {
				if (@$string[$i + 1] != '!') {
					$output .= ' AND ' . $content_term . '=\'';
				} else {
					$output .= ' AND ';
				}
			} else if ($string[$i] == ',') {
				if (@$string[$i + 1] != '!') {
					$output .= ' OR ' . $content_term . '=\'';

				} else {
					$output .= ' OR ';
				}

			}

			if ($string[$i] != '!' && $string[$i] != '+' && $string[$i] != ',') {

				$output .= $string[$i];

				if (@$string[$i + 1] == ',' || @$string[$i + 1] == '+' || @$string[$i + 1] == '!' || $i == $length || $i == $length - 1) {
					$output .= '\'';
				}
			}
		}//end for

		if ($ADD_PREFIX == true) {
			$output = $content_term . '=\'' . $output;
		}

		if ($encapsulate) {
			$output = '(' . $output . ')';
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $output, $string, $content_term, $encapsulate);
		$output = self::_applyFilter(get_class(), __FUNCTION__, $output, array('event' => 'return'));

		return $output;
	}//end parseSQLOperator

	/**
	 * @todo Get rid of this function
	 * @deprecated now
	 */
	public static function convertNumbericBoolean($boolean) {
		if ($boolean == 1) {
			return true;
		} else if ($boolean == 0) {
			return false;
		}

	}//end convertNumbericBoolean

	/**
	 * Converts a boolean that is passed a string to the boolean type true or false.
	 */
	public static function convertTextBoolean($boolean) {
		if ($boolean == 'true') {
			return true;
		} else if ($boolean == 'false') {
			return false;
		}

		return $boolean;
	}//end convertTextBoolean

	/**
	 * @todo most likely get ride of
	 */
	public static function createParameterArray($params) {
		$array = split("[:\n]", $params);
		$count = count($array);
		$paramarray = array();

		for ($i = 0; $i < $count; $i++) {
			$name = $array[$i];
			$paramarray[$name] = $array[$i + 1];
			$i = $i + 1;

		}//end for

		return $paramarray;

	}//end createrParamterArray

	private static function getOptionDefaults() {
			
		$defaults = array(
			'option_id' => 0, 
			'app_id' => 0, 
			'user_id' => '', 
			'content_id' => 0, 
			'option_name' => '', 
			'option_value' => '', 
			'option_type' => '', 
			'option_date' => ''
		);

		return $defaults;
	}

}//end tools
?>