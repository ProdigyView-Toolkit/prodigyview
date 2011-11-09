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
*THIS SOFTWARE IS PROVIDED BY My-Lan AS IS'' AND ANY EXPRESS OR IMPLIED
*WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
*FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL My-Lan OR
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

class PVRouter extends PVStaticObject{
	
	private static $routes;
	private static $route_parameters;
	private static $route_options;
	private static $seo_urls;
	private static $default_rule_replace='/:([a-z]+)/';
	private static $default_route_replace='(?P<\1>[^/]+)';
	
	/**
	 * Initializes the router and sets up default parameters and default rules
	 * for using the router. Seo urls is the ability to create and use easily
	 * readabable urls.
	 * 
	 * @param array $config The configuration to add to the router.
	 * 			-'seo_urls' _boolean_: Defaulted to true, specifiy to always make the urls appear seo friendly
	 * 			-'default_rule_replace' _string_: Specifies what to look for when defining a parameter in a router
	 * 			-'default_route_replace' _string_ : When a rule is found, speficies what to replace it with before parsing
	 * 
	 * @return void
	 * @access public
	 */
	public static function init($config=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $config);
		
		$defaults=array(
			'seo_urls'=>true,
			'default_rule_replace'=>'/:([a-z]+)/',
			'default_route_replace'=>'(?P<\1>[^/]+)'
		);
		
		$config += $defaults;
		$config = self::_applyFilter( get_class(), __FUNCTION__ , $config, array('event'=>'args'));
		
		self::$routes=array();
		self::$route_parameters=array();
		
		if($config['seo_urls']==1 || $config['seo_urls']=='true'){
			self::$seo_urls=1;
		} else {
			self::$seo_urls=0;
		}
		
		self::$default_rule_replace = $config['default_rule_replace'];
		self::$default_route_replace = $config['default_route_replace'];
		
		self::_notify(get_class().'::'.__FUNCTION__, $config);
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
	public static function activateSSL($url='') {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $url);
		
		$url = self::_applyFilter( get_class(), __FUNCTION__ , $url , array('event'=>'args'));
		
		if ($_SERVER["HTTPS"] != "on") {
			$url='https://';
				
			if ($_SERVER["SERVER_PORT"] != "80") {
	  			$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 		} else {
	  			$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			
			self::_notify(get_class().'::'.__FUNCTION__, $url);
			header('Location: '.$url );
			
		}//end https!=on
		
	}//end activateSSL
	
	/**
	 * If the current url is open on an SSL connection,
	 * the url will be made into a normal connectin.
	 * 
	 * @param string url: An option parameter of defing where to redirect too
	 */
	public static function deactivateSSL($url='') {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $url);
		
		$url = self::_applyFilter( get_class(), __FUNCTION__ , $url, array('event'=>'args'));
		
		if ($_SERVER["HTTPS"] == "on") {
			$url='http://';
			
			if ($_SERVER["SERVER_PORT"] != "80") {
	  			$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 		} else {
	  			$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			
			self::_notify(get_class().'::'.__FUNCTION__, $url);
			header("Location: $url ");
		}//end https is on
		
	}//end activateSSL
	
	/**
	 * Adds a rule to the router. The rule determines how the
	 * router will react..
	 * 
	 * @param mixed $route Can a string that merely sets a rule or an array with configuration information for rule.
	 * 			- 'rule' _string_: A rule to follow that will be matched using a preg_match.
	 * 			- 'redirect' _string_: A location to redirect if the uri matches the rule
	 * 			- 'access_level' _int_: A level of access required for the matching rule.
	 * 			- 'access_level_redirect' _string_: The location to be redirected too if the route matches and
	 * 				the access level was not high enough.
	 * 			- 'user_roles' _array_: An array of user roles that are allowed to access this location
	 * 			- 'user_roles_redirect' _string_: A location to be redirected to if the route matches and the
	 * 				required role was not present.
	 * 
	 * @return void
	 */
	public static function addRouteRule($route) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $route);
			
		$defaults = array(
			'route'=>null,
			'access_level'=>null,
			'access_level_redirect'=>null,
			'user_roles'=>null,
			'user_roles_redirect'=>null,
			'rule'=>null
		);
		
		if(!is_array($route)){
			$route=array('rule'=>$route);
		}
		
		$route += $defaults;
		$route = self::_applyFilter( get_class(), __FUNCTION__ , $route, array('event'=>'args'));
		array_push(self::$routes, $route);
		
		self::_notify(get_class().'::'.__FUNCTION__, $route);
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
	public static function setRoute($uri='') {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $uri);
		
		$uri = self::_applyFilter( get_class(), __FUNCTION__ , $uri, array('event'=>'args'));
		
		if(empty($uri)){
			$uri=$_SERVER['REQUEST_URI'];
		}
		
		if(substr ( $uri, strlen($uri)-1) == '/') {
			$uri=substr_replace($uri, '', strlen($uri)-1);
		}
		
		$routes=array();
		$uri_parts=explode('/', $uri);
	
		$assigned_route=array();
		$default_route=array();
		$assigned_route_options=array();
		$default_route_options=array();
		
		foreach (self::$routes as $route) {
			$reRule = preg_replace(self::$default_rule_replace, self::$default_route_replace ,$route['rule']);
			$reRule = str_replace('/', '\/', $reRule);
			
            if(preg_match('/' . $reRule .'/', $uri, $matches)){
				
				$uri_match='';	
				foreach($matches as $key=>$value){
					if(!PVValidator::isInteger($key)){
						$uri_match.='/'.$value;
					}
				}//end foreach
				
				$match_1=str_replace($uri_match, '', $uri);
				$match_2=str_replace($uri_match, '', $matches[0]);
				
				if($match_1==$match_2 && !empty($match_1)){
					$assigned_route=$matches;
					$assigned_route_options=$route;
					break;
				} else if ($match_1==$match_2 && empty($match_1)){
					$default_route=$matches;
					$default_route_options=$route;
				}
			}//end if prgrack mage
		}//end first for
		
		if(!empty($assigned_route)){
			$final_route=$assigned_route;
			$route_options=$assigned_route_options;
			if(!isset($final_route['route'])){
				$final_route['route']=$default_route;
			}
			if(!isset($route_options['route'])){
				$route_options['route']=$default_route;
			}
		} else {
			$final_route=$default_route;
			$route_options=$default_route_options;
		}
		
		self::$route_parameters=$final_route;
		self::$route_options=$route_options;
		
		self::_notify(get_class().'::'.__FUNCTION__, $final_route, $route_options);
		
		if(!empty($route_options['access_level'])){
			if(!PVSecurity::checkUserAccessLevel(PVUsers::getUserID(), $route_options['access_level']) && !empty($route_options['access_level_redirect'])){
				self::redirect($route_options['access_level_redirect']);
			}
		}
		
		if(!empty($route_options['user_roles'])){
			if(!PVSecurity::checkUserRole(PVUsers::getUserID(), $route_options['user_roles']) && !empty($route_options['user_roles_redirect'])){
				self::redirect($route_options['user_roles_redirect']);
			}
		}
		
		if(!empty($route_options['redirect'])){
			self::redirect($route_options['redirect']);
		}
		
	}//end checkRouteRule
	
	/**
	 * Gets the variable if specified in a rule.
	 * 
	 * Example:
	 * PVRouter::addRouteRule('/:controller/:action/:id');
	 * 
	 * PVRouter::setRoute(/post/view/3124);
	 * 
	 * $id=PVRouter::getRouteVariable('id');
	 * echo $id
	 * //Should print 3124
	 * 
	 * @param string $parameter A variable to retrieve that is in the url
	 * 
	 * @return string $variable Gets the variable, if it exist.
	 * @access public
	 */
	public static function getRouteVariable($parameter){
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $parameter);
		
		$parameter = self::_applyFilter( get_class(), __FUNCTION__ , $parameter, array('event'=>'args'));
		
		if(isset(self::$route_parameters[$parameter])) {
			self::_notify(get_class().'::'.__FUNCTION__, $parameter, self::$route_parameters[$parameter]);
			$found_parameter = self::_applyFilter( get_class(), __FUNCTION__ , self::$route_parameters[$parameter], array('event'=>'return'));
			
			return $found_parameter;
		}
	}
	
	public static function getRouteParameter($parameter){
		return self::$route_parameters[self::$route_options['route'][$paremter]];
	}
	
	/**
	 * Returns the current options associated with the route,
	 * if they have been set.
	 * 
	 * @return array options: The options associated with the set route
	 * @access public
	 */
	public static function getRoute() {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		$route = (isset(self::$route_options['route'])) ? self::$route_options['route'] : null;
		
		self::_notify(get_class().'::'.__FUNCTION__ , $route);
		$route = self::_applyFilter( get_class(), __FUNCTION__ , $route, array('event'=>'return'));
		
		return $route;
	}
	
	/**
	 * If SEO Urls is on and the url has query data present,
	 * the data will be converted to search engine friendly
	 * urls.
	 * 
	 * Example:
	 * $url = PVRouter::url(index.php?id=gohard&option=gohome);
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
	public static function url($url, $options=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $url);
		
		$filtered = self::_applyFilter( get_class(), __FUNCTION__ , array('url'=>$url, 'options'=>$options ), array('event'=>'args'));
		$url = $filtered['url'];
		$options = $filtered['options'];
		
		if(is_array($url)){
			$temp=(self::$seo_urls) ? '' : '?';
			
			foreach($url as $key=>$part) {
				$temp.=(self::$seo_urls) ? '/'.$part : $key.'='.$part.'&';
			}
			
			if(!self::$seo_urls) {
				$temp=substr_replace($temp, '', strlen($temp)-1);
			}
			$url=$temp;
		}
		if(self::$seo_urls){
			$url = str_replace('.php','', $url);
	    	$pattern = "/\?[^=]*=/";
			$replacement = '/';
			$url = preg_replace($pattern, $replacement, $url);
			$pattern = "/\&[^=]*=/";
			$replacement = '/';
			$url = preg_replace($pattern, $replacement, $url);
		}
		if(!PVValidator::isValidUrl($url) && strpos($url, PVTools::getCurrentBaseUrl()) == false){
			//$url=PVTools::getCurrentBaseUrl().$url;
		}
		
		self::_notify(get_class().'::'.__FUNCTION__ , $url);
		$url= self::_applyFilter( get_class(), __FUNCTION__ , $url, array('event'=>'return'));
		
		return $url;
	}//end url
	
	/**
	 * If the url is a valid url, such as another site, the url will be point to that site.
	 * Otherwise it is run through the router and actions are taken if needed.
	 * 
	 * @param string $url A url to be redirected too.
	 * 
	 * @return void
	 * @access public
	 */
	public static function redirect($url) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $url);
		
		self::_notify(get_class().'::'.__FUNCTION__ , $url);
		$url= self::_applyFilter( get_class(), __FUNCTION__ , $url, array('event'=>'args'));
		
		if(PVValidator::isValidUrl($url)){
			header('Location: '.$url);
		} else {
			self::setRoute($url);
			header('Location: '.self::url($url));
		}
	}
	
}//end class
	