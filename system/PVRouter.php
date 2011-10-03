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
	
	/**
	 * Initializes the router and sets up default parameters.
	 * Called of method can be modified in the bootap.
	 * 
	 * @return void
	 */
	public static function init(){
		$config=PVConfiguration::getSiteCompleteConfiguration();
		
		self::$routes=array();
		self::$route_parameters=array();
		
		if($config['seo_urls']==1 || $config['seo_urls']=='true'){
			self::$seo_urls=1;
		} else {
			self::$seo_urls=0;
		}
	}//end init
	
	/**
	 * Checks the HTTPS is currently on. If its not then nothiing
	 * is done, else it will redirect to an SSL connection.
	 * 
	 * @param string url: An Optional parameter to be redirected to.
	 * 
	 * @return void
	 */
	public static function activateSSL($url=''){
		
		if ($_SERVER["HTTPS"] != "on") {
			$url='https://';
				
			if ($_SERVER["SERVER_PORT"] != "80") {
	  			$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 		} else {
	  			$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			
			header('Location: '.$url );
			
		}//end https!=on
		
	}//end activateSSL
	
	/**
	 * If the current url is open on an SSL connection,
	 * the url will be made into a normal connectin.
	 * 
	 * @param string url: An option parameter of defing where to redirect too
	 */
	public static function deactivateSSL($url=''){
		
		if ($_SERVER["HTTPS"] == "on") {
			$url='http://';
			
			if ($_SERVER["SERVER_PORT"] != "80") {
	  			$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 		} else {
	  			$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			
			header("Location: $url ");
		}//end https is on
		
	}//end activateSSL
	
	/**
	 * Adds a rule to the router. The rule determines how the
	 * router will react..
	 * 
	 * @param mixed route
	 * 
	 * Mixed route can either be a string or an array of information.
	 * 
	 * 
	 * Array(
	 * 	rule=>''m		//The rule pased is the syntax of the uri that will cause the router to react
	 *  route=>'',
	 *  access_level=>'', //Set access levels that correspond to the access levels in the user data. Will check to make sure user has access level.
	 * 'access_level_redirect'=> //If a user does not have the required access level, they are redirected to this url
	 * 'user_roles'=>'' //Set the roles required to access the uri. Roles correspond to roles in the roles table and given to a user
	 * 'user_roles_redirect'=>'', Redirect the user to this uri if they do not have the necessary role.		
	 * )
	 * 
	 * @return void
	 */
	public static function addRouteRule($route){
		if(is_array($route)){
			array_push(self::$routes, $route);
		}
		else{
			array_push(self::$routes, array('rule'=>$route));
		}
	}
	
	/**
	 * Sets the current uri. If the uri is empty, the default uri will be used. How the uri
	 * will react depends on how the route rules are specified. If redirects are set and the uri
	 * matches a rule, a redirect will be automatically instantied.
	 * 
	 * @param string uri: The uri to set.
	 * 
	 * @return void
	 */
	public static function setRoute($uri=''){
		if(empty($uri)){
			$uri=$_SERVER['REQUEST_URI'];
		}
		
		if(substr ( $uri, strlen($uri)-1) == '/') {
			$uri=substr_replace($uri, '', strlen($temp)-1);
		}
		
		$routes=array();
		$uri_parts=explode('/', $uri);
	
		$assigned_route=array();
		$default_route=array();
		$assigned_route_options=array();
		$default_route_options=array();
		
		foreach (self::$routes as $route) {
			$reRule = preg_replace('/:([a-z]+)/', '(?P<\1>[^/]+)',$route['rule']);
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
		
		if(isset($route_options['access_level'])){
			if(!PVSecurity::checkUserAccessLevel(PVUsers::getUserID(), $route_options['access_level']) && isset($route_options['access_level_redirect'])){
				self::redirect($route_options['access_level_redirect']);
			}
		}
		
		if(isset($route_options['user_roles'])){
			if(!PVSecurity::checkUserPermission(PVUsers::getUserRole(), $route_options['user_roles']) && isset($route_options['user_roles_redirect'])){
				self::redirect($route_options['user_roles_redirect']);
			}
		}
		
		if(isset($route_options['redirect'])){
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
	 * @param string parameter: a variable to retrieve that is in the url
	 * 
	 * @return string variable: Gets the variable, if it exist.
	 */
	public static function getRouteVariable($parameter){
		return self::$route_parameters[$parameter];
	}
	
	public static function getRouteParameter($parameter){
		return self::$route_parameters[self::$route_options['route'][$paremter]];
	}
	
	/**
	 * Returns the current options associated with the route,
	 * if they have been set.
	 * 
	 * @param array options: The options associated with the set route
	 */
	public static function getRoute(){
		return self::$route_options['route'];
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
	 * @param string url: A url to be parsed
	 * @param array options:
	 * 
	 * @return stinrg url: Returns the url
	 */
	public static function url($url, $options=array()){
		
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
		
		return $url;
	}//end url
	
	/**
	 * If the url is a valid url, such as another site, the url will be point to that site.
	 * Otherwise it is run through the router and actions are taken if needed.
	 * 
	 * @param string url: A url to be redirected too.
	 * 
	 * @return void
	 */
	public static function redirect($url){
		if(PVValidator::isValidUrl()){
			header('Location: '.$url);
		} else {
			self::setRoute($url);
			header('Location: '.self::url($url));
		}
	}
	
}//end class
	