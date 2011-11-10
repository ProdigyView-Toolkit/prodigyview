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

	/**
	 * Initilize the class and set the variables for the template.
	 *
	 * @param array $config The configuration variables for the template
	 * 			-'site_name' _string_: The name of the site
	 * 			-'meta_keywords' _string_: The meta keywords for the site
	 * 			-'meta_description' _string_: The meta descriptiong for the site
	 *
	 * @return void
	 * @access public
	 */
	function init($config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		$defaults = array('site_name' => '', 'meta_keywords' => '', 'meta_description' => '');

		$config += $defaults;
		$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));

		self::$siteTitle = $config['site_name'];
		self::$siteMetaTags = $config['meta_keywords'];
		self::$siteMetaDescription = $config['meta_description'];

		self::_notify(get_class() . '::' . __FUNCTION__, $config);
	}

	/**
	 * Returns the title set for the site
	 * Modify the tags in <site_name></site_name> to change the site title.
	 *
	 * @return string siteTitle: The sets title.
	 * @access public
	 */
	public static function getSiteTitle() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$siteTitle);
		$value = self::_applyFilter(get_class(), __FUNCTION__, self::$siteTitle, array('event' => 'return'));

		return $value;
	}

	/**
	 * Returns the title set for the site.
	 * Modify the tags in <site_name></site_name> to change the site title.
	 *
	 * @return string siteTitle: The sets title.
	 * @access public
	 */
	public static function getSiteMetaDescription() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$siteMetaDescription);
		$value = self::_applyFilter(get_class(), __FUNCTION__, self::$siteMetaDescription, array('event' => 'return'));

		return $value;
	}

	/**
	 * Returns the meta descroption  set for the site.
	 * Modify the tags in <meta_description></meta_description> to change the meta description.
	 *
	 * @return string meta_tags: The sets title.
	 * @access public
	 */
	public static function getSiteMetaTags() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$siteMetaTags);
		$value = self::_applyFilter(get_class(), __FUNCTION__, self::$siteMetaTags, array('event' => 'return'));

		return $value;
	}

	/**
	 * Ovveride the title of the site.
	 *
	 * @param string title: Site title
	 * @access public
	 */
	public static function setSiteTitle($string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		self::$siteTitle = $string;
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Append to the site title
	 *
	 * @param string title: Site title
	 *
	 * @return void
	 * @access public
	 */
	public static function appendSiteTitle($string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		self::$siteTitle .= $string;
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Ovveride the meta tags of the site.
	 *
	 * @param string meta_tags: Set the meta tags
	 *
	 * @return void
	 * @access public
	 */
	public static function setSiteMetaTags($string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		self::$siteMetaTags = $string;
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	public static function appendSiteMetaTags($string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		self::$siteMetaTags .= $string;
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Ovveride the meta description of the site.
	 *
	 * @param string $text The string that will be set as the site meta description
	 *
	 * @return void
	 * @access public
	 */
	public static function setSiteMetaDescription($string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		self::$siteMetaDescription = $string;
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Append text to the site meta description
	 *
	 * @param string $text The text to be appened to the site meta description
	 *
	 * @return void
	 * @access public
	 */
	public static function appendSiteMetaDescription($string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		self::$siteMetaDescription .= $string;
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Displays a message that alerts the user when a error action has taken place.
	 * @see PVHtml::div()
	 *
	 * @param string $message The message to be displayed
	 * @param array $options Options that define the parameters. The options will form
	 * 				a div and will be the same options used in PVHtml::div()
	 *
	 * @return string $div Returns a div with the error message inside.
	 * @access public
	 */
	public static function errorMessage($message, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $message, $options);

		$defaults = array('class' => 'error-message');
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('message' => $message, 'options' => $options), array('event' => 'args'));
		$message = $filtered['message'];
		$options = $filtered['options'];

		$div = PVHtml::div($message, $options);
		self::_notify(get_class() . '::' . __FUNCTION__, $div, $message, $options);
		$div = self::_applyFilter(get_class(), __FUNCTION__, $div, array('event' => 'return'));

		return $div;
	}

	/**
	 * Displays a message that alerts the user when a succesful action has taken place.
	 * @see PVHtml::div()
	 *
	 * @param string $message The message to be displayed
	 * @param array $options Options that define the parameters. The options will form
	 * 				a div and will be the same options used in PVHtml::div()
	 *
	 * @return string $div Returns a div with the success message inside.
	 * @access public
	 */
	public static function successMessage($message, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $message, $options);

		$defaults = array('class' => 'success-message');
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('message' => $message, 'options' => $options), array('event' => 'args'));
		$message = $filtered['message'];
		$options = $filtered['options'];

		$div = PVHtml::div($message, $options);
		self::_notify(get_class() . '::' . __FUNCTION__, $div, $message, $options);
		$div = self::_applyFilter(get_class(), __FUNCTION__, $div, array('event' => 'return'));

		return $div;
	}

	/**
	 * The function used for installing or updating a template into the templating system.
	 *
	 * @param array $args The arguements that define the template in the database.
	 * 			-'template_name' _string_: The name of the template
	 * 			-'template_version' _double_: The version of the template
	 * 			-'template_author' _string_: The author of the template
	 * 			-'template_license' _string_: The license used wit the template
	 * 			-'is_default' _boolean_: Is this the default template
	 * 			-'main_file' _string_: The location of the main file for the template
	 * 			-'xml_file' _string: The location of the xml file for the template
	 * 			-'template_directory' _string_: The directory the template resides in
	 * 			-'template_unique_id' _string_: A uniquie identier for the template
	 * 			-'template_domain' _string_: A domain the template resides at
	 * 			-'template_page' _id_: A page the template is assoicated wtih
	 * 			-'template_site_id' _id_: A site the template is assoicated with
	 * 			-'template_options' _string: Storage for the template options
	 *
	 * @return id $template_id The id of the template
	 * @access public
	 */
	public static function installTemplate($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getTemplateDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));

		if (!empty($args) && is_array($args)) {

			$args = PVDatabase::makeSafe($args);

			extract($args);

			$is_default = PVTools::convertTextBoolean($is_default);
			$is_default = ceil($is_default);
			$template_page = ceil($template_page);
			$template_site_id = ceil($template_site_id);

			$query = "SELECT template_id FROM " . PVDatabase::getTemplatesTableName() . " WHERE template_unique_id='$template_unique_id'";

			$result = PVDatabase::query($query);

			if (PVDatabase::resultRowCount($result) <= 0) {

				$query = "INSERT INTO " . PVDatabase::getTemplatesTableName() . "(template_name , template_version , template_author , template_license , main_file , xml_file , template_directory , template_image , template_unique_id, template_domain, template_page, template_site_id, template_options ) VALUES( '$template_name' , '$template_version' , '$template_author' , '$template_license' , '$main_file' , '$xml_file' , '$template_directory' , '$template_image' , '$template_unique_id', '$template_domain' , '$template_page' , '$template_site_id', '$template_options' ) ";

				$template_id = PVDatabase::return_last_insert_query($query, "template_id", PVDatabase::getTemplatesTableName());

				foreach ($positions as $value) {

					$query = "INSERT INTO " . PVDatabase::getTemplatePositionsTableName() . "(template_id, position_name) VALUES('$template_id', '$value') ";

					PVDatabase::query($query);
				}

				self::_notify(get_class() . '::' . __FUNCTION__, $template_id, $args);
				$template_id = self::_applyFilter(get_class(), __FUNCTION__, $template_id, array('event' => 'return'));

				return $template_id;
			}//end if result  < 0
			else {

				$query = "UPDATE " . PVDatabase::getTemplatesTableName() . " SET template_name='$template_name' , template_version='$template_version' , template_author='$template_author' , template_license='$template_license' , main_file='$main_file' , xml_file='$xml_file' , template_directory='$template_directory' , template_image='$template_image' , template_unique_id='$template_unique_id' , template_domain='$template_domain', template_page='$template_page', template_site_id='$template_site_id', template_options='$template_options' WHERE template_unique_id='$template_unique_id' ";
				PVDatabase::query($query);
				self::_notify(get_class() . '::' . __FUNCTION__, $template_id, $args);
			}//end else

		}//end if not empty and is array

	}//end installTemplate

	/**
	 * Retrieves the data on the template based on the template's id or the template's unique id.
	 *
	 * @param mixed $template_id Either the id of the template or the unique identifer of the template
	 *
	 * @return array $data The template's data
	 * @access public
	 */
	public static function getTemplate($template_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $template_id);

		$template_id = self::_applyFilter(get_class(), __FUNCTION__, $template_id, array('event' => 'args'));

		if (!empty($template_id)) {

			if (PVValidator::isInteger($template_id)) {
				$template_id = ceil($template_id);
				$query = "SELECT * FROM " . PVDatabase::getTemplatesTableName() . " WHERE template_id='$template_id' ";
			} else {
				$template_id = PVDatabase::makeSafe($template_id);
				$query = "SELECT * FROM " . PVDatabase::getTemplatesTableName() . " WHERE template_unique_id='$template_id' ";
			}

			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row = PVDatabase::formatData($row);

			self::_notify(get_class() . '::' . __FUNCTION__, $row, $template_id);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row;

		}//end not empty

	}//end getTemplate

	/**
	 * Search for templates based on passed parameters. Utilizes the PV Standard Search Query.
	 *
	 * @param array $args Arguements passed that can be used to find a list of templates.
	 * 			-'template_id' _id_: The id of a template being searched for
	 * 			-'template_name' _string_: The name of the template
	 * 			-'template_version' _double_: The version of the template
	 * 			-'template_author' _string_: The author of the template
	 * 			-'template_license' _string_: The license used wit the template
	 * 			-'is_default' _boolean_: Is this the default template
	 * 			-'main_file' _string_: The location of the main file for the template
	 * 			-'xml_file' _string: The location of the xml file for the template
	 * 			-'template_directory' _string_: The directory the template resides in
	 * 			-'template_unique_id' _string_: A uniquie identier for the template
	 * 			-'template_domain' _string_: A domain the template resides at
	 * 			-'template_page' _id_: A page the template is assoicated wtih
	 * 			-'template_site_id' _id_: A site the template is assoicated with
	 * 			-'template_options' _string: Storage for the template options
	 *
	 * @return array $templates Returns an array of templates found
	 * @access public
	 */
	public static function getTemplateList($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getTemplateDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$first = 1;

		$content_array = array();
		$table_name = PVDatabase::getTemplatesTableName();
		$db_type = PVDatabase::getDatabaseType();

		$WHERE_CLAUSE = '';

		if (!empty($template_id)) {

			$template_id = trim($template_id);

			if ($first == 0 && ($template_id[0] != '+' && $template_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_id[0] == '+' || $template_id[0] == ',') && $first == 1) {
				$template_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_id, 'template_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_name)) {

			$template_name = trim($template_name);

			if ($first == 0 && ($template_name[0] != '+' && $template_name[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_name[0] == '+' || $template_name[0] == ',') && $first == 1) {
				$template_name[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_name, 'template_name');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_version)) {

			$template_version = trim($template_version);

			if ($first == 0 && ($template_version[0] != '+' && $template_version[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_version[0] == '+' || $template_version[0] == ',') && $first == 1) {
				$template_version[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_version, 'template_version');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_author)) {

			$template_author = trim($template_author);

			if ($first == 0 && ($template_author[0] != '+' && $template_author[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_author[0] == '+' || $template_author[0] == ',') && $first == 1) {
				$template_author[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_author, 'template_author');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_license)) {

			$template_license = trim($template_license);

			if ($first == 0 && ($template_license[0] != '+' && $template_license[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_license[0] == '+' || $template_license[0] == ',') && $first == 1) {
				$template_license[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_license, 'template_license');

			$first = 0;
		}//end not empty app_id

		if (!empty($is_default)) {

			$is_default = trim($is_default);

			if ($first == 0 && ($is_default[0] != '+' && $is_default[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($is_default[0] == '+' || $is_default[0] == ',') && $first == 1) {
				$is_default[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($is_default, 'is_default');

			$first = 0;
		}//end not empty app_id

		if (!empty($main_file)) {

			$main_file = trim($main_file);

			if ($first == 0 && ($main_file[0] != '+' && $main_file[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($main_file[0] == '+' || $main_file[0] == ',') && $first == 1) {
				$main_file[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($main_file, 'main_file');

			$first = 0;
		}//end not empty app_id

		if (!empty($xml_file)) {

			$xml_file = trim($xml_file);

			if ($first == 0 && ($xml_file[0] != '+' && $xml_file[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($xml_file[0] == '+' || $xml_file[0] == ',') && $first == 1) {
				$xml_file[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($xml_file, 'xml_file');

			$first = 0;
		}//end not empty app_id

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

		if (!empty($template_directory)) {

			$template_directory = trim($template_directory);

			if ($first == 0 && ($template_directory[0] != '+' && $template_directory[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_directory[0] == '+' || $template_directory[0] == ',') && $first == 1) {
				$template_directory[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_directory, 'template_directory');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_image)) {

			$template_image = trim($template_image);

			if ($first == 0 && ($template_image[0] != '+' && $template_image[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_image[0] == '+' || $template_image[0] == ',') && $first == 1) {
				$template_image[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_image, 'template_image');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_unique_id)) {

			$template_unique_id = trim($template_unique_id);

			if ($first == 0 && ($template_unique_id[0] != '+' && $template_unique_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_unique_id[0] == '+' || $template_unique_id[0] == ',') && $first == 1) {
				$template_unique_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_unique_id, 'template_unique_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_page)) {

			$template_page = trim($template_page);

			if ($first == 0 && ($template_page[0] != '+' && $template_page[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_page[0] == '+' || $template_page[0] == ',') && $first == 1) {
				$template_page[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_page, 'template_page');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_site_id)) {

			$template_site_id = trim($template_site_id);

			if ($first == 0 && ($template_site_id[0] != '+' && $template_site_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_site_id[0] == '+' || $template_site_id[0] == ',') && $first == 1) {
				$template_site_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_site_id, 'template_site_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($template_domain)) {

			$template_domain = trim($template_domain);

			if ($first == 0 && ($template_domain[0] != '+' && $template_domain[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_domain[0] == '+' || $template_domain[0] == ',') && $first == 1) {
				$template_domain[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_domain, 'template_domain');

			$first = 0;
		}//end not empty app_id

		$JOINS = '';

		if (!empty($custom_where)) {
			$WHERE_CLAUSE .= ' ' . $custom_where . ' ';
		}

		if (!empty($custom_join)) {
			$JOINS .= ' ' . $custom_join . ' ';
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
		$query = "$prequery SELECT $prefix_args $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";

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
	}//end getTemplateList

	/**
	 * Removes a template based on the id of the template. The files assoicated with the template
	 * will also be deleted.
	 *
	 * @param id $template_id The id of the template to be deleted
	 *
	 * @return void
	 * @access public
	 */
	public static function removeTemplate($template_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $template_id);

		$template_id = self::_applyFilter(get_class(), __FUNCTION__, $template_id, array('event' => 'args'));

		$template_info = self::getTemplate($template_id);

		if (!empty($template_info) && is_array($template_info)) {
			extract($template_info);

			if (!empty($template_directory)) {
				if (file_exists(PV_TEMPLATES . $template_directory)) {
					PVFileManager::deleteDirectory(PV_TEMPLATES . $template_directory);
				}
			}//end if not empty

			$query = "DELETE FROM " . PVDatabase::getTemplatesTableName() . " WHERE template_id='$template_id'";
			PVDatabase::query($query);

			$query = "DELETE FROM " . PVDatabase::getTemplatePositionsTableName() . " WHERE template_id='$template_id'";
			PVDatabase::query($query);

			self::_notify(get_class() . '::' . __FUNCTION__, $template_id);
		}

	}//end removeTemplate

	/**
	 * Selects a template the cloesely matches the arguments passed to it and returns the first
	 * one found.
	 *
	 * @param array $args Arguements passed that can be used to find a single template.
	 * 			-'template_id' _id_: The id of a template being searched for
	 * 			-'template_name' _string_: The name of the template
	 * 			-'template_version' _double_: The version of the template
	 * 			-'template_author' _string_: The author of the template
	 * 			-'template_license' _string_: The license used wit the template
	 * 			-'is_default' _boolean_: Is this the default template
	 * 			-'main_file' _string_: The location of the main file for the template
	 * 			-'xml_file' _string: The location of the xml file for the template
	 * 			-'template_directory' _string_: The directory the template resides in
	 * 			-'template_unique_id' _string_: A uniquie identier for the template
	 * 			-'template_domain' _string_: A domain the template resides at
	 * 			-'template_page' _id_: A page the template is assoicated wtih
	 * 			-'template_site_id' _id_: A site the template is assoicated with
	 * 			-'template_options' _string: Storage for the template options
	 *
	 * @return array $templates Returns the found template, if any
	 * @access public
	 */
	public static function selectTemplate($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getTemplateDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);

		if (!empty($args) && is_array($args)) {

			extract($args);

			$WHERE = '';
			$first = 1;

			if (!empty($is_default)) {
				if ($first == 1) {
					$WHERE .= " is_default='$is_default' ";
				} else {
					$WHERE .= " AND is_default='$is_default' ";
				}

				$first = 0;
			}//end if

			if (!empty($template_unique_id)) {
				if ($first == 1) {
					$WHERE .= " template_unique_id='$template_unique_id' ";
				} else {
					$WHERE .= " AND template_unique_id='$template_unique_id' ";
				}

				$first = 0;
			}//end if

			if (!empty($template_domain)) {
				if ($first == 1) {
					$WHERE .= " template_domain='$template_domain' ";
				} else {
					$WHERE .= " AND template_domain='$template_domain' ";
				}

				$first = 0;
			}//end if

			if (!empty($template_page)) {
				if ($first == 1) {
					$WHERE .= " template_page='$template_page' ";
				} else {
					$WHERE .= " AND template_page='$template_page' ";
				}

				$first = 0;
			}//end if

			if (!empty($template_site_id)) {
				if ($first == 1) {
					$WHERE .= " template_site_id='$template_site_id' ";
				} else {
					$WHERE .= " AND template_site_id='$template_site_id' ";
				}

				$first = 0;
			}//end if

			if (!empty($WHERE)) {
				$WHERE = ' WHERE ' . $WHERE;
			}
			$query = "SELECT * FROM " . PVDatabase::getTemplatesTableName() . " $WHERE LIMIT 1";
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);

			self::_notify(get_class() . '::' . __FUNCTION__, $row, $args);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row;
		}//end

	}//end getDefaultTemplate

	/**
	 * Add a template position to a template.
	 *
	 * @param array $args An array of arguements that define the template position
	 * 				-'template_id' _id_: The id of the template that contains the position
	 * 				-'position_name' _string_: The name of the position
	 * 				-'position_width' _double_: The width the position
	 * 				-'position_height' _double_ The height of the position
	 *
	 * @return void
	 * @access public
	 */
	public static function addTemplatePosition($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getTemplatePositionDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);

		extract($args);

		if (!empty($template_id) && !empty($position)) {

			$query = "SELECT template_id, position_name FROM " . PVDatabase::getTemplatePositionsTableName() . " WHERE template_id='$template_id' AND position_name='$position_name' ";
			$result = PVDatabase::query($query);

			if (PVDatabase::resultRowCount($result) <= 0 && !empty($position)) {
				$query = "INSERT INTO " . PVDatabase::getTemplatePositionsTableName() . "(template_id, position_name) VALUES('$template_id', '$position_name')";
				PVDatabase::query($query);
				self::_notify(get_class() . '::' . __FUNCTION__, $args);
			}

		}//end if !emoty

	}//end addTemplatePosition

	/**
	 * Returns a list of template positions based on passed arguements. Used ProdigyView standard search query.
	 *
	 * @param array $args An array of arguements that define how the template positions are found
	 * 				-'template_id' _id_: The id of the template that contains the position
	 * 				-'position_name' _string_: The name of the position
	 * 				-'position_width' _double_: The width the position
	 * 				-'position_height' _double_ The height of the position
	 *
	 * @return array $positions Returns an array of template positons
	 * @access public
	 */
	public static function getTemplatePositionList($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getTemplatePositionDefaults();
		$args += self::_getSqlSearchDefaults();
		$args += array('join_templates' => false);
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$custom_where = $args['custom_where'];
		$custom_join = $args['custom_join'];
		$args = PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);

		$first = 1;
		$content_array = array();

		$WHERE_CLAUSE = '';

		if (!empty($template_id)) {

			$template_id = trim($template_id);

			if ($first == 0 && ($template_id[0] != '+' && $template_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($template_id[0] == '+' || $template_id[0] == ',') && $first == 1) {
				$template_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($template_id, 'template_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($position_name)) {

			$position_name = trim($position_name);

			if ($first == 0 && ($position_name[0] != '+' && $position_name[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($position_name[0] == '+' || $position_name[0] == ',') && $first == 1) {
				$position_name[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($position_name, 'position_name');

			$first = 0;
		}//end not empty app_id

		$JOINS = '';

		if ($join_templates) {
			$JOINS .= ' JOIN ' . PVDatabase::getTemplatesTableName() . ' ON ' . PVDatabase::getTemplatePositionsTableName() . '.template_id=' . PVDatabase::getTemplatesTableName() . '.template_id ';
		}

		if (!empty($custom_where)) {
			$WHERE_CLAUSE .= ' ' . $custom_where . ' ';
		}

		if (!empty($custom_join)) {
			$JOINS .= ' ' . $custom_join . ' ';
		}

		if (!empty($WHERE_CLAUSE)) {
			$WHERE_CLAUSE = ' WHERE ' . $WHERE_CLAUSE;
		}

		$ORDER_BY = $args['order_by'];
		$LIMIT = $args['limit'];

		if (!empty($LIMIT)) {
			$LIMIT = " limit $LIMIT ";
		}

		if (!empty($group_by)) {
			$WHERE_CLAUSE .= "GROUP BY $group_by ";
		}

		if (!empty($ORDER_BY)) {
			$ORDER_BY = "ORDER BY $ORDER_BY ";
		}

		if (empty($custom_select)) {
			$custom_select = '*';
		}

		$query = "SELECT $custom_select FROM " . PVDatabase::getTemplatePositionsTableName() . " $JOINS " . $WHERE_CLAUSE . " $ORDER_BY $LIMIT ";
		$result = PVDatabase::query($query);

		while ($row = PVDatabase::fetchArray($result)) {
			array_push($content_array, $row);
		}//end while

		$content_array = PVDatabase::formatData($content_array);
		self::_notify(get_class() . '::' . __FUNCTION__, $content_array, $args);
		$content_array = self::_applyFilter(get_class(), __FUNCTION__, $content_array, array('event' => 'return'));

		return $content_array;
	}//end getTemplatePositionList

	/**
	 * Returns the data assoicated with a template position.
	 *
	 * @param id $template_id The id of the template
	 * @param string $position_name The name of the position
	 *
	 * @return array $position Returns an array of data about that position
	 * @access public
	 */
	public static function getTemplatePosition($template_id, $position_name) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $template_id, $position_name);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('template_id' => $template_id, 'position_name' => $position_name), array('event' => 'args'));
		$template_id = $filtered['template_id'];
		$position_name = $filtered['position_name'];

		$template_id = PVDatabase::makeSafe($template_id);
		$position_name = PVDatabase::makeSafe($position_name);

		if (!empty($template_id) && !empty($position_name)) {

			$query = "SELECT * FROM " . PVDatabase::getTemplatePositionsTableName() . " WHERE template_id='$template_id' AND position_name='$position_name' ";
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row = PVDatabase::formatData($row);

			self::_notify(get_class() . '::' . __FUNCTION__, $row, $template_id, $position_name);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row;
		}//end not empty

	}//end getTemplatePosition

	/**
	 * Removes and template position based up on the id of the template and position name.
	 *
	 * @param id $template_id The id of the template
	 * @param string $position_name The name of the position
	 *
	 * @return void
	 * @access public
	 */
	public static function removeTemplatePosition($template_id, $position_name) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $template_id, $position_name);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('template_id' => $template_id, 'position_name' => $position_name), array('event' => 'args'));
		$template_id = $filtered['template_id'];
		$position_name = $filtered['position_name'];

		$template_id = PVDatabase::makeSafe($template_id);
		$position_name = PVDatabase::makeSafe($position_name);

		if (!empty($template_id) && !empty($position_name)) {

			$query = "DELETE FROM " . PVDatabase::getTemplatePositionsTableName() . " WHERE template_id='$template_id' AND position_name='$position_name' ";

			PVDatabase::query($query);
			self::_notify(get_class() . '::' . __FUNCTION__, $template_id, $position_name);
		}//end not empty

	}//end getTemplatePosition

	/**
	 * Used for updating a string/bufer, generally a header that ob_flush returns. Inputs the libraries
	 * and meta description into the header based upon the dates passed
	 *
	 * @param string $buffer The string/buffer that will contain the tags to be replaced
	 * @param array $options The options that define how the header will output
	 * 			-'site_title' _string_: The text will be searched for in the passed string and replaced with the title of the site.
	 * 			-'site_keywords' _string_: The text will be searched for in the passed string and replaced with the keywords of the site.
	 * 			-'site_description' _string_: The text will be searched for in the passed string and replaced with the description of the site.
	 * 			-'header_addition' _string_: The text will be searched for in the passed string and replaced with the libraries found.
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $buffer The buffer with the tags replaced
	 * @access public
	 */
	public static function updateHeader($buffer, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $buffer, $options);

		$defaults = array(
			'site_title' => '{SITE_TITLE}', 
			'site_keywords' => '{SITE_KEYWORDS}', 
			'site_description' => '{SITE_DESCRIPTION}', 
			'header_addition' => '{HEADER_ADDITION}'
		);

		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('buffer' => $buffer, 'options' => $options), array('event' => 'args'));
		$buffer = $filtered['buffer'];
		$options = $filtered['options'];

		$libraries = self::getHeader($options);

		$buffer = str_replace($options['site_title'], pv_getSiteTitle(), $buffer);

		$buffer = str_replace($options['site_keywords'], pv_getSiteMetaTags(), $buffer);

		$buffer = str_replace($options['site_description'], pv_getSiteMetaDescription(), $buffer);

		$buffer = str_replace($options['header_addition'], $libraries, $buffer);

		self::_notify(get_class() . '::' . __FUNCTION__, $buffer, $options);
		$buffer = self::_applyFilter(get_class(), __FUNCTION__, $buffer, array('event' => 'return'));

		return $buffer;
	}//end  updateHeader

	/**
	 * Retrieves information that would typically be present in a header. Includes all the libraries(javascript, prototype, etc)
	 * and css.
	 *
	 * @param array $options Options that can determine how the site files are displayed
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getHeader($options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array('version' => false, 'append_url' => true, 'libraries' => '', 'url' => '');
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		extract($options);

		$siteConfiguration = pv_getSiteCompleteConfiguration();

		$jquery = (PV_IS_ADMIN) ? PV_ADMIN_JQUERY : PV_JQUERY;
		$mootools = (PV_IS_ADMIN) ? PV_ADMIN_MOOTOOLS : PV_MOOTOOLS;
		$prototype = (PV_IS_ADMIN) ? PV_ADMIN_PROTOTYPE : PV_PROTOTYPE;
		$javascript = (PV_IS_ADMIN) ? PV_ADMIN_JAVASCRIPT : PV_JAVASCRIPT;
		$css = (PV_IS_ADMIN) ? PV_ADMIN_CSS : PV_CSS;

		if ($options['append_url']) {
			$url = PVTools::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		if ($siteConfiguration['ajax_enabled'] == 1 && !empty($siteConfiguration['ajax_library'])) {
			$libraries .= '<script type="text/javascript" src="' . $url . $javascript . DS . $siteConfiguration['ajax_library'] . '"></script>';
		}

		if ($siteConfiguration['jquery_enabled'] == 1 && !empty($siteConfiguration['jquery_library'])) {
			$libraries . '<script type="text/javascript" src="' . $url . $jquery . DS . $siteConfiguration['jquery_library'] . '"></script>';
		}

		if ($siteConfiguration['mootools_enabled'] == 1 && !empty($siteConfiguration['mootools_library'])) {
			$libraries . '<script type="text/javascript" src="' . $url . $mootools . DS . $siteConfiguration['mootools_library'] . '"></script>';
		}

		if ($siteConfiguration['prototype_enabled'] == 1 && !empty($siteConfiguration['prototype_library'])) {
			$libraries . '<script type="text/javascript" src="' . $url . $prototype . DS . $siteConfiguration['prototype_library'] . '"></script>';
		}

		foreach (PVLibraries::getJqueryQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . $jquery . DS . trim($value) . $version . '"></script>';
		}

		foreach (PVLibraries::getMootoolsQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . $mootools . DS . trim($value) . $version . '"></script>';
		}

		foreach (PVLibraries::getPrototypeQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . $prototype . DS . trim($value) . $version . '"></script>';
		}

		foreach (PVLibraries::getCssQueue() as $value) {
			$libraries .= '<link rel="stylesheet"  type="text/css" href="' . $url . $css . DS . trim($value) . $version . '">';
		}

		foreach (PVLibraries::getJavascriptQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . DS . $javascript . DS . trim($value) . $version . '"></script>';
		}

		$libraries .= PVLibraries::getOpenscriptQueue();

		self::_notify(get_class() . '::' . __FUNCTION__, $libraries, $options);
		$libraries = self::_applyFilter(get_class(), __FUNCTION__, $libraries, array('event' => 'return'));

		return $libraries;
	}//end  updateHeader

	/**
	 * Retrieves the queued javascript libraries in PVLibaries and adds them to a script take to be placed in
	 * a template.
	 *
	 * @param array $options Options that can determine how the site files are displayed
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getJavaScriptHeader($options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array('version' => false, 'append_url' => true, 'libraries' => '', 'url' => '');
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		extract($options);

		$javascript = (PV_IS_ADMIN) ? PV_ADMIN_JAVASCRIPT : PV_JAVASCRIPT;

		if ($options['append_url']) {
			$url = PVTools::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		foreach (PVLibraries::getJavascriptQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . $javascript . DS . trim($value) . $version . '"></script>';
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $libraries, $options);
		$libraries = self::_applyFilter(get_class(), __FUNCTION__, $libraries, array('event' => 'return'));

		return $libraries;
	}//end printJavaScriptHeader

	/**
	 * Retrieves the queued mootools libraries in PVLibaries and adds them to a script take to be placed in
	 * a template.
	 *
	 * @param array $options Options that can determine how the site files are displayed
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getMootoolsHeader($options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array('version' => false, 'append_url' => true, 'libraries' => '', 'url' => '');
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		extract($options);

		$mootools = (PV_IS_ADMIN) ? PV_ADMIN_MOOTOOLS : PV_MOOTOOLS;

		if ($options['append_url']) {
			$url = PVTools::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		foreach (PVLibraries::getMootoolsQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . $mootools . DS . trim($value) . $version . '"></script>';
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $libraries, $options);
		$libraries = self::_applyFilter(get_class(), __FUNCTION__, $libraries, array('event' => 'return'));

		return $libraries;

	}//end printJavaScriptHeader

	/**
	 * Retrieves the queued prototype libraries in PVLibaries and adds them to a script take to be placed in
	 * a template.
	 *
	 * @param array $options Options that can determine how the site files are displayed
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getPrototypeHeader($options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array('version' => false, 'append_url' => true, 'libraries' => '', 'url' => '');
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		extract($options);

		$prototype = (PV_IS_ADMIN) ? PV_ADMIN_PROTOTYPE : PV_PROTOTYPE;

		if ($options['append_url']) {
			$url = PVTools::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		foreach (PVLibraries::getPrototypeQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . $prototype . DS . trim($value) . $version . '"></script>';
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $libraries, $options);
		$libraries = self::_applyFilter(get_class(), __FUNCTION__, $libraries, array('event' => 'return'));

		return $libraries;
	}//end printJavaScriptHeader

	/**
	 * Retrieves the queued prototype libraries in PVLibaries and adds them to a script take to be placed in
	 * a template.
	 *
	 * @param array $options Options that can determine how the site files are displayed
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getJQueryHeade($options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array('version' => false, 'append_url' => true, 'libraries' => '', 'url' => false);
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		extract($options);

		$jquery = (PV_IS_ADMIN) ? PV_ADMIN_JQUERY : PV_JQUERY;

		if ($options['append_url']) {
			$url = PVTools::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		foreach (PVLibraries::getJqueryQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . $jquery . DS . trim($value) . $version . '"></script>';
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $libraries, $options);
		$libraries = self::_applyFilter(get_class(), __FUNCTION__, $libraries, array('event' => 'return'));

		return $libraries;

	}//end printJavaScriptHeader

	/**
	 * Retrieves the queued css libraries in PVLibaries and adds them to a script take to be placed in
	 * a template.
	 *
	 * @param array $options Options that can determine how the site files are displayed
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getCSSHeader($options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array('version' => false, 'append_url' => true, 'libraries' => '', 'url' => false);
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		extract($options);

		$css = (PV_IS_ADMIN) ? PV_ADMIN_CSS : PV_CSS;

		if ($options['append_url']) {
			$url = PVTools::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		foreach (PVLibraries::getCssQueue() as $value) {
			$libraries .= '<link rel="stylesheet"  type="text/css" href="' . $url . $css . DS . trim($value) . $version . '">';
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $libraries, $options);
		$libraries = self::_applyFilter(get_class(), __FUNCTION__, $libraries, array('event' => 'return'));

		return $libraries;
	}//end printJavaScriptHeader

	private static function getTemplateDefaults() {

		$defaults = array(
			'template_id' => 0, 
			'template_name' => '', 
			'template_version' => '', 
			'template_author' => '', 
			'template_license' => '', 
			'is_default' => 0, 
			'main_file' => '', 
			'xml_file' => '', 
			'template_directory' => '', 
			'template_image' => '', 
			'template_unique_id' => '', 
			'template_domain' => '', 
			'template_page' => 0, 
			'template_site_id' => 0, 
			'template_options' => ''
		);

		return $defaults;
	}

	private static function getTemplatePositionDefaults() {

		$defaults = array(
			'template_id' => 0, 
			'position_name' => '', 
			'position_width' => 0, 
			'position_height' => 0
		);

		return $defaults;
	}

}//end class
