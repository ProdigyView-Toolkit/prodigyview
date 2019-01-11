<?php

namespace prodigyview\template;

use prodigyview\design\StaticObject;
use prodigyview\network\Router;
use prodigyview\system\Configuration;
use prodigyview\system\Libraries;
use prodigyview\util\Tools;

if (!defined('DS')) {
	define('DS', '/');
}

if (!defined('PV_CSS')) {
	define('PV_CSS', '');
}

if (!defined('PV_JAVASCRIPT')) {
	define('PV_JAVASCRIPT', '');
}

/**
 * Template is a generic template wrapper class that is used as a basis for creating a templating
 * system.
 *
 * @package template
 * @todo Remove Mootools, JQuery etc method. Update to do better parse and potentialy integrate with
 * templating system
 */
class Template {
	
	use StaticObject;

	/**
	 * The title of the site
	 */
	private static $siteTitle;

	/**
	 * Meta tags to go in the header
	 */
	private static $siteMetaTags;

	/**
	 * Description of the site
	 */
	private static $siteMetaDescription;

	/**
	 * Site keywords
	 */
	private static $siteKeywords;
	
	/**
	 * Store flash messages to be displayed
	 */
	 private static $flashMessages = array();
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

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
	public static function init(array $config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		if(!self::$_initialized) {
			$defaults = array(
				'site_name' => '',
				'meta_keywords' => '',
				'meta_description' => ''
			);
	
			$config += $defaults;
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$siteTitle = $config['site_name'];
			self::$siteMetaTags = $config['meta_keywords'];
			self::$siteMetaDescription = $config['meta_description'];
	
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
			
			self::$_initialized = true;
		}
	}

	/**
	 * Returns the title set for the site
	 * Modify the tags in <site_name></site_name> to change the site title.
	 *
	 * @return string siteTitle: The sets title.
	 * @access public
	 */
	public static function getSiteTitle() : ?string {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$siteTitle);
		$value = self::_applyFilter(get_class(), __FUNCTION__, self::$siteTitle, array('event' => 'return'));

		return $value;
	}

	/**
	 * Returns the site keywords.
	 * Modify the keywords attribute
	 *
	 * @return string
	 * @access public
	 */
	public static function getSiteKeywords() : ?string {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$siteKeywords);
		$value = self::_applyFilter(get_class(), __FUNCTION__, self::$siteKeywords, array('event' => 'return'));

		return $value;
	}

	/**
	 * Returns the title set for the site.
	 * Modify the tags in <site_name></site_name> to change the site title.
	 *
	 * @return string siteTitle: The sets title.
	 * @access public
	 */
	public static function getSiteMetaDescription() : ?string {

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
	public static function getSiteMetaTags() : ?string {

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
	public static function setSiteTitle(string $string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		
		self::$siteTitle = $string;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Sets the site keywords
	 *
	 * @param string keywords: Site keywords
	 * @access public
	 */
	public static function setSiteKeywords(string $string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		
		self::$siteKeywords = $string;
		
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
	public static function appendSiteTitle(string $string) {

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
	public static function setSiteMetaTags(string $string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		
		self::$siteMetaTags = $string;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Adds to the sites Meta Tags to be displayed
	 *
	 * @param string $string The string to append
	 *
	 * @return void
	 */
	public static function appendSiteMetaTags(string $string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		
		self::$siteMetaTags .= $string;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Ovveride the meta description of the site.
	 *
	 * @param string $string The string that will be set as the site meta description
	 *
	 * @return void
	 * @access public
	 */
	public static function setSiteMetaDescription(string $string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		
		self::$siteMetaDescription = $string;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Append text to the site meta description
	 *
	 * @param string $string The text to be appened to the site meta description
	 *
	 * @return void
	 * @access public
	 */
	public static function appendSiteMetaDescription(string $string) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string);

		$string = self::_applyFilter(get_class(), __FUNCTION__, $string, array('event' => 'args'));
		
		self::$siteMetaDescription .= $string;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $string);
	}

	/**
	 * Displays a message that alerts the user when a error action has taken place.
	 * @see Html::div()
	 *
	 * @param string $message The message to be displayed
	 * @param array $options Options that define the parameters. The options will form
	 * 				a div and will be the same options used in Html::div()
	 *
	 * @return string $div Returns a div with the error message inside.
	 * @access public
	 */
	public static function errorMessage(string $message, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $message, $options);

		$defaults = array('class' => 'error-message');
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'message' => $message,
			'options' => $options
		), array('event' => 'args'));
		
		$message = $filtered['message'];
		$options = $filtered['options'];

		$div = Html::div($message, $options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $div, $message, $options);
		
		$div = self::_applyFilter(get_class(), __FUNCTION__, $div, array('event' => 'return'));

		return $div;
	}

	/**
	 * Displays a message that alerts the user when a succesful action has taken place.
	 * @see Html::div()
	 *
	 * @param string $message The message to be displayed
	 * @param array $options Options that define the parameters. The options will form
	 * 				a div and will be the same options used in Html::div()
	 *
	 * @return string $div Returns a div with the success message inside.
	 * @access public
	 */
	public static function successMessage(string $message, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $message, $options);

		$defaults = array('class' => 'success-message');
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'message' => $message,
			'options' => $options
		), array('event' => 'args'));
		
		$message = $filtered['message'];
		$options = $filtered['options'];

		$div = Html::div($message, $options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $div, $message, $options);
		
		$div = self::_applyFilter(get_class(), __FUNCTION__, $div, array('event' => 'return'));

		return $div;
	}
	
	/**
	 * Adds a flash message to a queue to be displayed later
	 *
	 * @param string $type The type of message, ie success, error, etc.
	 * @param string $message The message to be displayed
	 * @param array $options Options that define the parameters.
	 * 				-'session' string The name of session
	 *
	 * @return string $div Returns a div with the success message inside.
	 * @access public
	 */
	public static function addFlashMessage(string $type, string $message, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $type, $message, $options);

		$defaults = array('session' => 'default');
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'type' => $type,
			'message' => $message,
			'options' => $options
		), array('event' => 'args'));
		
		$type = $filtered['type'];
		$message = $filtered['message'];
		$options = $filtered['options'];

		self::$flashMessages[$options['session']][$type][] = $message;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $type, $message, $options);
	}
	
	/**
	 * Get the flash messages stored from previous sessions.
	 * 
	 * @param string $type The type of message to retrieves
	 * @param array $options Options used
	 */
	public static function getFlashMessages(string $type, array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $type);
		
		$defaults = array('unset_messages' => true, 'session' => 'default');
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'type' => $type,
			'options' => $options
		), array('event' => 'args'));
		
		$type = $filtered['type'];
		$options = $filtered['options'];
		
		$messages = array();
		
		if(isset(self::$flashMessages[$options['session']][$type])) {
			$messages = self::$flashMessages[$options['session']][$type];
			
			//Delete the messages
			if($options['unset_messages']) {
				unset(self::$flashMessages[$options['session']][$type]);
			}
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $messages, $type, $options);
		
		$messages = self::_applyFilter(get_class(), __FUNCTION__, $messages, array('event' => 'return'));
		
		return $messages;
	}

	/**
	 * Used for updating a string/buffer, generally a header that ob_flush returns. Inputs the libraries
	 * and meta description into the header based upon the dates passed
	 *
	 * @param string $buffer The string/buffer that will contain the tags to be replaced
	 * @param array $options The options that define how the header will output
	 * 			-'site_title' _string_: The text will be searched for in the passed string and replaced with
	 * the title of the site.
	 * 			-'site_keywords' _string_: The text will be searched for in the passed string and replaced with
	 * the keywords of the site.
	 * 			-'site_description' _string_: The text will be searched for in the passed string and replaced
	 * with the description of the site.
	 * 			-'header_addition' _string_: The text will be searched for in the passed string and replaced
	 * with the libraries found.
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and
	 * returned
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
			'site_meta' => '{SITE_META}',
			'header_addition' => '{HEADER_ADDITION}'
		);

		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'buffer' => $buffer,
			'options' => $options
		), array('event' => 'args'));
		
		$buffer = $filtered['buffer'];
		$options = $filtered['options'];

		$libraries = self::getHeader($options);

		$buffer = str_replace($options['site_title'], Template::getSiteTitle(), $buffer);

		$buffer = str_replace($options['site_keywords'], Template::getSiteKeywords(), $buffer);

		$buffer = str_replace($options['site_meta'], Template::getSiteMetaTags(), $buffer);

		$buffer = str_replace($options['site_description'], Template::getSiteMetaDescription(), $buffer);

		$buffer = str_replace($options['header_addition'], $libraries, $buffer);

		self::_notify(get_class() . '::' . __FUNCTION__, $buffer, $options);
		
		$buffer = self::_applyFilter(get_class(), __FUNCTION__, $buffer, array('event' => 'return'));

		return $buffer;
	}//end  updateHeader

	/**
	 * Retrieves information that would typically be present in a header. Includes all the
	 * libraries(javascript, prototype, etc)
	 * and css.
	 *
	 * @param array $options Options that can determine how the site files are displayed
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and
	 * returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getHeader(array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array(
			'version' => false,
			'append_url' => true,
			'libraries' => '',
			'url' => ''
		);
		
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		
		extract($options);

		if ($options['append_url']) {
			$url = Router::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		foreach (Libraries::getCssQueue() as $value) {
			$libraries .= '<link rel="stylesheet"  type="text/css" href="' . $url . $css . DS . trim($value) . $version . '">';
		}

		foreach (Libraries::getJavascriptQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . DS . $javascript . DS . trim($value) . $version . '"></script>';
		}

		$libraries .= Libraries::getOpenscriptQueue();

		self::_notify(get_class() . '::' . __FUNCTION__, $libraries, $options);
		$libraries = self::_applyFilter(get_class(), __FUNCTION__, $libraries, array('event' => 'return'));

		return $libraries;
	}//end  updateHeader

	/**
	 * Retrieves the queued javascript libraries in PVLibaries and adds them to a script take to be
	 * placed in
	 * a template.
	 *
	 * @param array $options Options that can determine how the site files are displayed
	 * 			-'version' _double_: A version for the file to differinate versions of the same file
	 * 			-'append_url' _boolean_: Append the sites url the location of the script
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and
	 * returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getJavaScriptHeader($options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array(
			'version' => false,
			'append_url' => true,
			'libraries' => '',
			'url' => ''
		);
		
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		
		extract($options);

		$javascript = PV_JAVASCRIPT;

		if ($options['append_url']) {
			$url = Router::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		foreach (Libraries::getJavascriptQueue() as $value) {
			$libraries .= '<script type="text/javascript" src="' . $url . $javascript . DS . trim($value) . $version . '"></script>';
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
	 * 			-'libraries' _string_: A string of other libraries that these libraries will be added to and
	 * returned
	 * 			-'url' _string_: A url to speficy the location of the libraries
	 *
	 * @return string $libraries <script /> string with the libraries found
	 * @access public
	 */
	public static function getCSSHeader(array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array(
			'version' => false,
			'append_url' => true,
			'libraries' => '',
			'url' => false
		);
		
		$options += $defaults;
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		extract($options);

		$css = PV_CSS;

		if ($options['append_url']) {
			$url = Router::getCurrentBaseUrl();
		}

		if ($options['version']) {
			$version = '?pvversion=' . $options['version'];
		}

		foreach (Libraries::getCssQueue() as $value) {
			$libraries .= '<link rel="stylesheet"  type="text/css" href="' . $url . $css . DS . trim($value) . $version . '">';
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $libraries, $options);
		$libraries = self::_applyFilter(get_class(), __FUNCTION__, $libraries, array('event' => 'return'));

		return $libraries;
	}//end printJavaScriptHeader

}//end class
