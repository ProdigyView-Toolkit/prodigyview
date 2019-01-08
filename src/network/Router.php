<?php
namespace prodigyview\network;

use prodigyview\util\Tools;
use prodigyview\util\Validator;
use prodigyview\design\StaticObject;

/**
 * Router is responsible for parsing the URL setting up the ability for routing within your
 * application.
 *
 * Applications, especially with Frontend Controller Design Pattern, may require routing to get a
 * user to their destination correctly. This class can take rules, route and correctly navigate the
 * user to their destination.
 *
 * @package network
 */
class Router {
	
	use StaticObject;

	/**
	 * A list of routes that have been added
	 */
	private static $routes;

	/**
	 * Items within a route, after is has been parsed
	 */
	private static $route_parameters;

	/**
	 * Configurationoptions around a route
	 */
	private static $route_options;

	/**
	 * Attempt to make Seo friendy urls
	 */
	private static $seo_urls;

	/**
	 * Specifies what to look for when defining a parameter in a router
	 */
	private static $default_rule_replace = '/:([a-z]+)/';

	/**
	 * When a rule is found, speficies what to replace it with before parsing
	 */
	private static $default_route_replace = '(?P<\1>[^/]+)';
	
	/**
	 * Set if the allow REQUEST METHOD is set forthe current route
	 */
	private static $correct_request_method = null;
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initializes the router and sets up default parameters and default rules
	 * for using the router. Seo urls is the ability to create and use easily
	 * readabable urls.
	 *
	 * @param array $config The configuration to add to the router.
	 * 			-'seo_urls' _boolean_: Defaulted to true, specifiy to always make the urls appear seo friendly
	 * 			-'default_rule_replace' _string_: Specifies what to look for when defining a parameter in a
	 * router
	 * 			-'default_route_replace' _string_ : When a rule is found, speficies what to replace it with
	 * before parsing
	 *
	 * @return void
	 * @access public
	 */
	public static function init(array $config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		if(!self::$_initialized) {
			$defaults = array(
				'seo_urls' => true,
				'default_rule_replace' => '/:([a-z]+)/',
				'default_route_replace' => '(?P<\1>[^/]+)'
			);
	
			$config += $defaults;
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$routes = array();
			self::$route_parameters = array();
	
			if ($config['seo_urls'] === 1 || $config['seo_urls'] === 'true') {
				self::$seo_urls = 1;
			} else {
				self::$seo_urls = 0;
			}
	
			self::$default_rule_replace = $config['default_rule_replace'];
			self::$default_route_replace = $config['default_route_replace'];
	
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
			
			self::$_initialized = true;
		}
	}//end init

	/**
	 * Checks the HTTPS is currently on. If its not then nothiing
	 * is done, else it will redirect to an SSL connection.
	 *
	 * @param string $url An Optional parameter to be redirected to.
	 *
	 * @return void
	 * @access public
	 */
	public static function activateSSL($url = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url);

		$url = self::_applyFilter(get_class(), __FUNCTION__, $url, array('event' => 'args'));

		if ($_SERVER['HTTPS'] != 'on') {
			$url = 'https://';

			if ($_SERVER['SERVER_PORT'] != '80') {
				$url .= $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
			} else {
				$url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			}

			self::_notify(get_class() . '::' . __FUNCTION__, $url);
			header('Location: ' . $url);

		}//end https!=on

	}//end activateSSL

	/**
	 * If the current url is open on an SSL connection,
	 * the url will be made into a normal connectin.
	 *
	 * @param string url: An option parameter of defing where to redirect too
	 */
	public static function deactivateSSL($url = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url);

		$url = self::_applyFilter(get_class(), __FUNCTION__, $url, array('event' => 'args'));

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
			$url = 'http://';

			if ($_SERVER['SERVER_PORT'] != '443') {
				$url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
			} else {
				$url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			}

			self::_notify(get_class() . '::' . __FUNCTION__, $url);
			header('Location: ' . $url);
		}//end https is on

	}//end activateSSL

	/**
	 * Adds a rule to the router. The rule determines how the
	 * router will react..
	 *
	 * @param mixed $route Can be a string that merely sets a rule or an array with configuration
	 * information for rule.
	 * 			- 'rule' _string_: A rule to follow that will be matched using a preg_match.
	 * 			- 'redirect' _string_: A location to redirect if the uri matches the rule
	 * 			- 'access_level' _int_: A level of access required for the matching rule.
	 * 			- 'access_level_redirect' _string_: The location to be redirected too if the route matches and
	 * 				the access level was not high enough.
	 * 			- 'user_roles' _array_: An array of user roles that are allowed to access this location
	 * 			- 'user_roles_redirect' _string_: A location to be redirected to if the route matches and the
	 * 				required role was not present.
	 * 			- 'listen' _string_: Restrict routes to certian request. Default is *, other values can be GET, POST, PUT, DELETE
	 *
	 * @return void
	 */
	public static function addRouteRule($route) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $route);

		$defaults = array(
			'route' => null,
			'access_level' => null,
			'access_level_redirect' => null,
			'user_roles' => null,
			'user_roles_redirect' => null,
			'rule' => null,
			'activate_ssl' => false,
			'deactivate_ssl' => false,
			'callback'=> null,
			'listen'=>'*'
		);

		if (!is_array($route)) {
			$route = array('rule' => $route);
		}

		$route += $defaults;
		$route = self::_applyFilter(get_class(), __FUNCTION__, $route, array('event' => 'args'));
		array_push(self::$routes, $route);

		self::_notify(get_class() . '::' . __FUNCTION__, $route);
	}

	/**
	 * Creates a route that only listens for POST requests.
	 * 
	 * @param string $route The route either as a url or with regular expressions to listen too
	 * @param string An array of options that define the route.
	 * 
	 * @see addRouteRule to see the option
	 * @return void
	 */
	public static function post(string $route, array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $route, $options);
		
		$defaults = array(
			'listen'=>'POST',
			'route' => $route,
			'rule'=> $route
		);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'route' => $route,
			'options' => $options
		), array('event' => 'args'));
		
		$route = $filtered['route'];
		$options = $filtered['options'];
		
		$options += $defaults;
		
		self::addRouteRule($options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $route, $options);
	}
	
	/**
	 * Creates a route that only listens for GET requests.
	 * 
	 * @param string $route The route either as a url or with regular expressions to listen too
	 * @param string An array of options that define the route.
	 * 
	 * @see addRouteRule to see the option
	 * @return void
	 */
	public static function get(string $route, array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $route, $options);
		
		$defaults = array(
			'listen'=>'GET',
			'route' => $route,
			'rule'=> $route
		);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'route' => $route,
			'options' => $options
		), array('event' => 'args'));
		
		$route = $filtered['route'];
		$options = $filtered['options'];
		
		$options += $defaults;
		
		self::addRouteRule($options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $route, $options);
	}
	
	/**
	 * Creates a route that only listens for PUT requests.
	 * 
	 * @param string $route The route either as a url or with regular expressions to listen too
	 * @param string An array of options that define the route.
	 * 
	 * @see addRouteRule to see the option
	 * @return void
	 */
	public static function put(string $route, array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $route, $options);
		
		$defaults = array(
			'listen'=>'POST',
			'route' => $route,
			'rule'=> $route
		);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'route' => $route,
			'options' => $options
		), array('event' => 'args'));
		
		$route = $filtered['route'];
		$options = $filtered['options'];
		
		$options += $defaults;
		
		self::addRouteRule($options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $route, $options);
	}
	
	/**
	 * Creates a route that only listens for DELETE requests.
	 * 
	 * @param string $route The route either as a url or with regular expressions to listen too
	 * @param string An array of options that define the route.
	 * 
	 * @see addRouteRule to see the option
	 * @return void
	 */
	public static function delete(string $route, array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $route, $options);
		
		$defaults = array(
			'listen'=>'POST',
			'route' => $route,
			'rule'=> $route
		);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'route' => $route,
			'options' => $options
		), array('event' => 'args'));
		
		$route = $filtered['route'];
		$options = $filtered['options'];
		
		$options += $defaults;
		
		self::addRouteRule($options);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $route, $options);
	}

	/**
	 * Sets the current uri. If the uri is empty, the default uri will be used. How the uri
	 * will react depends on how the route rules are specified. If redirects are set and the uri
	 * matches a rule, a redirect will be automatically instantied.
	 *
	 * @param string uri: The uri to set.
	 *
	 * @return void
	 * @access public
	 */
	public static function setRoute(string $uri = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $uri);

		$uri = self::_applyFilter(get_class(), __FUNCTION__, $uri, array('event' => 'args'));
		
		if (empty($uri)) {
			$uri = $_SERVER['REQUEST_URI'];
		}

		if (substr($uri, strlen($uri) - 1) === '/') {
			$uri = substr_replace($uri, '', strlen($uri) - 1);
		}

		$routes = array();

		$pos = @strpos($uri, '?');

		if ($pos !== false) {
			$uri = substr_replace($uri, '', $pos, strlen($uri));
		}

		$uri_parts = explode('/', $uri);

		foreach ($uri_parts as $key => $value) {
			if (empty($uri_parts[$key])) {
				unset($uri_parts[$key]);
			}
		}

		$uri_parts = array_values($uri_parts);
		$uri = '/' . implode('/', $uri_parts);
		
		$assigned_route = array();
		$default_route = array();
		$assigned_route_options = array();
		$default_route_options = array();
		
		$method = (isset($_SERVER['REQUEST_METHOD'])) ? $method = $_SERVER['REQUEST_METHOD'] : null;
		
		foreach (self::$routes as $route) {
			
			if(self::_isAllowRequest($route['listen'], $method)) {
				
				$reRule = preg_replace(self::$default_rule_replace, self::$default_route_replace, $route['rule']);
				$reRule = str_replace('/', '\/', $reRule);
	
				if (preg_match('/' . $reRule . '/', $uri, $matches)) {
	
					$uri_match = '';
					foreach ($matches as $key => $value) {
						if (!Validator::isInteger($key)) {
							$uri_match .= '/' . $value;
						}
					}//end foreach
	
					$match_1 = str_replace($uri_match, '', $uri);
					$match_2 = str_replace($uri_match, '', $matches[0]);
	
					if ($match_1 === $match_2 && !empty($match_1)) {
						$assigned_route = $matches;
						$assigned_route_options = $route;
						break;
					} else if ($match_1 === $match_2 && empty($match_1)) {
						$default_route = $matches;
						$default_route_options = $route;
					}
				}//end if prgrack mage
			}
		}//end first for

		if (!empty($assigned_route)) {
			$final_route = $assigned_route;
			$route_options = $assigned_route_options;
			if (!isset($final_route['route'])) {
				$final_route['route'] = $default_route;
			}
			if (!isset($route_options['route'])) {
				$route_options['route'] = $default_route;
			}
		} else {
			$final_route = $default_route;
			$route_options = $default_route_options;
		}

		self::$route_parameters = $final_route;
		self::$route_options = $route_options;
		
		$method = (isset($_SERVER['REQUEST_METHOD'])) ? $method = $_SERVER['REQUEST_METHOD'] : null;
		
		if($route_options['listen'] == '*') {
			self::$correct_request_method = true;
		} else {
			$listen = strtoupper($route_options['listen']);
			
			if(strpos($listen, $method )!== false) {
				self::$correct_request_method = true;
			} else {
				self::$correct_request_method = false;
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $final_route, $route_options);

		if(self::$correct_request_method) {
			
			if (!empty($route_options['activate_ssl']) && $route_options['activate_ssl'] && !self::isSecureConnection()) {
				self::activateSSL();
			} else if (!empty($route_options['deactivate_ssl']) && $route_options['deactivate_ssl'] && self::isSecureConnection()) {
				self::deactivateSSL();
			}
	
			if (!empty($route_options['redirect'])) {
				self::redirect($route_options['redirect']);
			}
			
			if (!empty($route_options['callback'])) {
				return call_user_func_array($route_options['callback'], array(new Request()));
			}
		}

	}//end checkRouteRule
	
	/**
	 * Based on the route options that is listened to, check to see if this route is allowed with the request
	 * method. Meaning does this route allow GET, PUT, DELETE, POST or other.
	 * 
	 * @return boolean true or false if the route is allowed
	 */
	public static function isAllowedRouteRequest() {
		return self::$correct_request_method;
	}

	/**
	 * Gets the variable if specified in a rule.
	 *
	 * Example:
	 * Router::addRouteRule('/:controller/:action/:id');
	 *
	 * Router::setRoute(/post/view/3124);
	 *
	 * $id=Router::getRouteVariable('id');
	 * echo $id
	 * //Should print 3124
	 *
	 * @param string $parameter A variable to retrieve that is in the url
	 *
	 * @return string $variable Gets the variable, if it exist.
	 * @access public
	 */
	public static function getRouteVariable($parameter) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $parameter);

		$parameter = self::_applyFilter(get_class(), __FUNCTION__, $parameter, array('event' => 'args'));

		if (isset(self::$route_parameters[$parameter])) {
			self::_notify(get_class() . '::' . __FUNCTION__, $parameter, self::$route_parameters[$parameter]);
			$found_parameter = self::_applyFilter(get_class(), __FUNCTION__, self::$route_parameters[$parameter], array('event' => 'return'));

			return $found_parameter;
		}
	}

	/**
	 * Returns all the variables that are specified in a route.
	 *
	 * @return array
	 * @access public
	 */
	public static function getRouteVariables() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$data = self::$route_parameters;

		self::_notify(get_class() . '::' . __FUNCTION__, $data);
		$data = self::_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'return'));

		return $data;

	}

	/**
	 * Returns a parameter that is embeed within the route.
	 *
	 * @param string $parameter The parameter being looked for
	 *
	 * @return string The found value, if any
	 */
	public static function getRouteParameter($parameter) {
		return self::$route_parameters[self::$route_options['route'][$parameter]];
	}

	/**
	 * Returns the current options associated with the route,
	 * if they have been set.
	 *
	 * @return array options: The options associated with the set route
	 * @access public
	 */
	public static function getRoute() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$route = (isset(self::$route_options['route'])) ? self::$route_options['route'] : null;

		self::_notify(get_class() . '::' . __FUNCTION__, $route);
		$route = self::_applyFilter(get_class(), __FUNCTION__, $route, array('event' => 'return'));

		return $route;
	}

	/**
	 * If SEO Urls is on and the url has query data present,
	 * the data will be converted to search engine friendly
	 * urls.
	 *
	 * Example:
	 * $url = Router::url(index.php?id=gohard&option=gohome);
	 * echo $url
	 *
	 * //Will print out /gohard/gohome
	 *
	 * @param string $url A url to be parsed
	 * @param array $options
	 *
	 * @return string $url Returns the url
	 * @access public
	 */
	public static function url($url, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'url' => $url,
			'options' => $options
		), array('event' => 'args'));
		
		$url = $filtered['url'];
		$options = $filtered['options'];

		if (is_array($url)) {
			$temp = (self::$seo_urls) ? '' : '?';

			foreach ($url as $key => $part) {
				$temp .= (self::$seo_urls) ? '/' . $part : $key . '=' . $part . '&';
			}

			if (!self::$seo_urls) {
				$temp = substr_replace($temp, '', strlen($temp) - 1);
			}
			$url = $temp;
		}
		if (self::$seo_urls) {
			$url = str_replace('.php', '', $url);
			$pattern = "/\?[^=]*=/";
			$replacement = '/';
			$url = preg_replace($pattern, $replacement, $url);
			$pattern = "/\&[^=]*=/";
			$replacement = '/';
			$url = preg_replace($pattern, $replacement, $url);
		}
		if (!Validator::isValidUrl($url) && strpos($url, Router::getCurrentBaseUrl()) === false) {
			//$url=Router::getCurrentBaseUrl().$url;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $url);
		$url = self::_applyFilter(get_class(), __FUNCTION__, $url, array('event' => 'return'));

		return $url;
	}//end url

	/**
	 * If the url is a valid url, such as another site, the url will be point to that site.
	 * Otherwise it is run through the router and actions are taken if needed.
	 *
	 * @param string $url A url to be redirected too.
	 * @param boolean $exit Exit script after header is set
	 *
	 * @return void
	 * @access public
	 */
	public static function redirect($url, $exit = true) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url);

		self::_notify(get_class() . '::' . __FUNCTION__, $url);
		$url = self::_applyFilter(get_class(), __FUNCTION__, $url, array('event' => 'args'));

		if (Validator::isValidUrl($url)) {
			header('Location: ' . $url);
		} else {
			self::setRoute($url);
			header('Location: ' . self::url($url));
		}

		if ($exit)
			exit();
	}

	/**
	 * Determines of the connection is secure behind SSL to TLS. Default
	 * functionality utilizes the server.
	 *
	 * @return boolean Return true is secure, otherwise false
	 */
	public static function isSecureConnection() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		if ($_SERVER['HTTPS'] != 'on')
			return false;

		return true;
	}
	
	/**
	 * Returns the full url of the current page. Inclded in the return will be if the page is being https
	 * connect,
	 * a port if any, and the uri.
	 *
	 * @return string $url Url of the current page.
	 * @access public
	 */
	public static function getCurrentUrl() : string {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$current_page_url = 'http';

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
			$current_page_url .= 's';
		}

		$current_page_url .= '://';

		if ($_SERVER['SERVER_PORT'] != '80') {
			$current_page_url .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$current_page_url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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
	public static function getCurrentBaseUrl() : string {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$current_page_url = 'http';

		if (@$_SERVER['HTTPS'] === 'on') { $current_page_url .= 's';
		}
		$current_page_url .= '://';

		if ($_SERVER['SERVER_PORT'] != '80') {
			$current_page_url .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'];
		} else {
			$current_page_url .= $_SERVER['HTTP_HOST'];
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
	public static function formUrlParameters(array $variables) : string {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $variables);

		$variables = self::_applyFilter(get_class(), __FUNCTION__, $variables, array('event' => 'args'));

		$appendix = '?';

		$first = 1;
		foreach ($variables as $key => $value) {
			if ($first === 1) {
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
	public static function formUrlPath(array $variables) : string {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $variables);

		$variables = self::_applyFilter(get_class(), __FUNCTION__, $variables, array('event' => 'args'));

		$appendix = '';

		$first = 1;
		foreach ($variables as $key => $value) {
			if ($first === 1) {
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
	
	private static function _isAllowRequest($allowed_request_types, $current_request) {
		
		$allowed = false;
			
		if($allowed_request_types == '*') {
			$allowed = true;
		} else {
			$allowed_request_types = strtoupper($allowed_request_types);
			
			if(strpos($allowed_request_types, $current_request)!== false) {
				$allowed = true;
			}
		}
		
		return $allowed;
	}

}//end class
