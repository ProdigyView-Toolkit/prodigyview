<?php
namespace prodigyview\util;

use prodigyview\design\StaticObject;

//Define the directory seperator
if (!defined('DS')) {
	define('DS', '/');
}

//Define the sites root directory
if (!defined('PV_ROOT')) {
	define('PV_ROOT', './');
}

/**
 * Cache is a system for caching data and retrieving cached data.
 *
 * The default system uses a file cache for caching data. But the system through the adapter pattern
 * is extendable to use any caching system attached to the application.
 *
 * Example:
 * ```php
 * //Init The Cache
 * Cache::init();
 *
 * $data = array('Apples', 'Oranges', 'Bananas');
 *
 * //Check if cache has expired
 * if(Cache::hasExpired('mycache')):
 * 	 //Store The Cache
 * 	 Cache::writeCache('mycache', $data);
 * endif;
 *
 * $data = Cache::readCache('mycache');
 *
 * print_r($data);
 * ```
 *
 * @package util
 */
class Cache {
	
	use StaticObject;

	/**
	 * File location to store the cache
	 */
	protected static $_cache_location = '/tmp/';

	/**
	 * The date format for storing the cache
	 */
	protected static $_cache_format = 'Y-m-d H:i:s';

	/**
	 * The regular expression for searching for cache
	 */
	protected static $_cache_format_search = '/\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}/';

	/**
	 * The name to preprend to cache
	 */
	protected static $_cache_name = 'cache:';

	/**
	 * How to wrap the the cache
	 */
	protected static $_enclosing_tags = array(
		'{',
		'}'
	);

	/**
	 * Memcache connection
	 */
	protected static $_memcache = null;

	/**
	 * Default time to live for the cache
	 */
	protected static $_cache_expire = 300;
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initalize Cache by setting the location to save cached files and initialize
	 * memchace servers
	 *
	 * @param array $config An array of configuration options. Options will be the defaults for other
	 * options.
	 * 			-'cache_format' _string_: The date/time format used when caching
	 * 			-'cache_format_search' _string_: The preg_match to use when searching for the cache date/time
	 * 			-'enclosing_tags' _array_: Tags that will encase the files to be caches
	 * 			-'cache_location' _string_: The location the cached is saved
	 * 			-'cache_name' _string_: The name to assign to the cache when both writing and reading it
	 * 			-'cache_expire' _int_: Ovveride the default expiration time by setting your own
	 *
	 * @return void
	 * @access public
	 */
	public static function init(array $config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		if(!self::$_initialized) {
			$defaults = array(
				'cache_location' => PV_ROOT . DS . 'tmp' . DS,
				'cache_format' => 'Y-m-d H:i:s',
				'cache_format_search' => '\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}',
				'enclosing_tags' => array(
					'{',
					'}'
				),
				'cache_name' => 'cache:',
				'cache_expire' => 300,
				'memcache_servers' => array()
			);
	
			$config += $defaults;
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$_cache_format = $config['cache_format'];
			self::$_cache_location = $config['cache_location'];
			self::$_cache_format_search = $config['cache_format_search'];
			self::$_cache_name = $config['cache_name'];
			self::$_enclosing_tags = $config['enclosing_tags'];
			self::$_cache_expire = $config['cache_expire'];
	
			if (!empty($config['memcache_servers']))
				self::$_memcache = new Memcache();
	
			foreach ($config['memcache_servers'] as $server) {
				$host = (isset($server['host'])) ? $server['host'] : '127.0.0.1';
				$port = (isset($server['port'])) ? $server['port'] : '11211';
				$persistent = (isset($server['persistent'])) ? $server['persistent'] : true;
				$weight = (isset($server['weight'])) ? $server['weight'] : 1;
				$timeout = (isset($server['timeout'])) ? $server['timeout'] : 30;
				$retry_interval = (isset($server['retry_interval'])) ? $server['retry_interval'] : 15;
				$status = (isset($server['status'])) ? $server['status'] : true;
				$failure_callback = (isset($server['failure_callback'])) ? $server['failure_callback'] : null;
				$timeoutms = (isset($server['timeoutms'])) ? $server['timeoutms'] : null;
	
				self::$_memcache->addServer($host, $port, $persistent, $weight, $timeout, $retry_interval, $status, $failure_callback, $timeoutms);
			}
	
			foreach ($config['memcache_servers'] as $server) {
				$host = (isset($server['host'])) ? $server['host'] : '127.0.0.1';
				$port = (isset($server['port'])) ? $server['port'] : '11211';
				$timeout = (isset($server['timeout'])) ? $server['timeout'] : 30;
				$connect = (isset($server['connect'])) ? $server['connect'] : false;
	
				if ($connect)
					self::$_memcache->connect($host, $port, $timeout);
			}
	
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
			
			self::$_initialized = true;
		}
	}

	/**
	 * Write the content to cache out to file
	 *
	 * @param string $key The key to be used accessing the cache
	 * @param string $content The content to be cached
	 * @param array $options Options that define how the files are cached
	 * 			-'cache_format' _string_: The date/time format used when caching
	 * 			-'cache_format_search' _string_: The preg_match to use when searching for the cache date/time
	 * 			-'enclosing_tags' _array_: Tags that will encase the files to be caches
	 * 			-'cache_location' _string_: The location the cached is saved
	 * 			-'cache_name' _string_: The name to assign to the cache when both writing and reading it
	 * 			-'cache_expire' _int_: Ovveride the default expiration time by setting your own
	 *
	 * @return void
	 * @access public
	 */
	public static function writeCache(string $key, $content, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $content, $options);

		$defaults = array(
			'cache_format' => self::$_cache_format,
			'cache_format_search' => self::$_cache_format_search,
			'enclosing_tags' => self::$_enclosing_tags,
			'cache_location' => self::$_cache_location,
			'cache_name' => self::$_cache_name,
			'cache_expire' => self::$_cache_expire
		);

		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'content' => $content,
			'options' => $options
		), array('event' => 'args'));
		
		$key = $filtered['key'];
		$content = $filtered['content'];
		$options = $filtered['options'];

		extract($options);

		if (is_array($content) || is_object($content)) {
			$content = serialize($content);
		}

		$expiration = date($cache_format, time() + $cache_expire);

		$cache_tag = $enclosing_tags[0] . $cache_name . $expiration . $enclosing_tags[1];
		$content = $cache_tag . $content;

		FileManager::writeFile($cache_location . $key, $content);
		self::_notify(get_class() . '::' . __FUNCTION__, $key, $content, $options);
	}

	/**
	 * Read the content of a cached file
	 *
	 * @param string $key The key to be used to access the cached file
	 * @param array $options Options that define how the files are cached
	 * 			-'cache_format' _string_: The date/time format used when caching
	 * 			-'cache_format_search' _string_: The preg_match to use when searching for the cache date/time
	 * 			-'enclosing_tags' _array_: Tags that will encase the files to be caches
	 * 			-'cache_location' _string_: The location the cached is saved
	 * 			-'cache_name' _string_: The name to assign to the cache when both writing and reading it
	 * 			-'cache_expire' _int_: Ovveride the default expiration time by setting your own
	 *
	 * @return string $content The cached content
	 * @access public
	 */
	public static function readCache(string $key, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $options);

		$defaults = array(
			'cache_format' => self::$_cache_format,
			'cache_format_search' => self::$_cache_format_search,
			'enclosing_tags' => self::$_enclosing_tags,
			'cache_location' => self::$_cache_location,
			'cache_name' => self::$_cache_name,
			'remove_cache_tag' => true
		);

		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'options' => $options
		), array('event' => 'args'));
		$key = $filtered['key'];
		$options = $filtered['options'];
		extract($options);

		$content = FileManager::readFile($cache_location . $key);

		if ($remove_cache_tag) {
			$content = preg_replace('/\\' . $enclosing_tags[0] . $cache_name . $cache_format_search . $enclosing_tags[1] . '/', '', $content);
		}

		$data = @unserialize($content);
		
		if ($data !== false || $content === 'b:0;')
			$content = $data;

		self::_notify(get_class() . '::' . __FUNCTION__, $content, $key, $options);
		$content = self::_applyFilter(get_class(), __FUNCTION__, $content, array('event' => 'return'));

		return $content;
	}

	/**
	 * Check to see if the current cache has expired. Returns true if it has, otherwise false.
	 *
	 * @param string $key The key to search for the cache by
	 * @param array $options Options that define how the cache is be found
	 * 			-'cache_format' _string_: The date/time format used when caching
	 * 			-'cache_format_search' _string_: The preg_match to use when searching for the cache date/time
	 * 			-'enclosing_tags' _array_: Tags that will encase the cached tag
	 * 			-'cache_location' _string_: The location the cached is saved
	 * 			-'cache_name' _string_: The name to assign to the cache when both writing and reading it
	 *
	 * @return boolean $expired Returns true if expired, otherwise false
	 * @access public
	 */
	public static function hasExpired(string $key, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $options);

		$defaults = array(
			'cache_format' => self::$_cache_format,
			'cache_format_search' => self::$_cache_format_search,
			'enclosing_tags' => self::$_enclosing_tags,
			'cache_location' => self::$_cache_location,
			'cache_name' => self::$_cache_name
		);

		$expired = false;
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'options' => $options
		), array('event' => 'args'));
		
		$key = $filtered['key'];
		$options = $filtered['options'];
		extract($options);

		if (!file_exists($cache_location . $key)) {
			$expired = true;
		}

		$content = FileManager::readFile($cache_location . $key);

		if (!empty($content) && preg_match('/\\' . $enclosing_tags[0] . $cache_name . $cache_format_search . $enclosing_tags[1] . '/', $content, $matches)) {

			if (preg_match('/' . $cache_format_search . '/', $matches[0], $date_match)) {

				if (strtotime($date_match[0]) < time())
					$expired = true;
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $expired, $key, $options);
		$expired = self::_applyFilter(get_class(), __FUNCTION__, $expired, array('event' => 'return'));

		return $expired;
	}

	/**
	 * Get the expiration date of the cached file, if it exist.
	 *
	 * @param string $key The key to search for the cache by
	 * @param array $options Options that define how the cache date is be found
	 * 			-'cache_format' _string_: The date/time format used when caching
	 * 			-'cache_format_search' _string_: The preg_match to use when searching for the cache date/time
	 * 			-'enclosing_tags' _array_: Tags that will encase the cached tag
	 * 			-'cache_location' _string_: The location the cached is saved
	 * 			-'cache_name' _string_: The name to assign to the cache when both writing and reading it
	 *
	 * @return string $expired Returns the date to expire, in string format
	 * @access public
	 */
	public static function getExpiration(string $key, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $options);

		$defaults = array(
			'cache_format' => self::$_cache_format,
			'cache_format_search' => self::$_cache_format_search,
			'enclosing_tags' => self::$_enclosing_tags,
			'cache_location' => self::$_cache_location,
			'cache_name' => self::$_cache_name
		);

		$expiration = null;
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'options' => $options
		), array('event' => 'args'));
		
		$key = $filtered['key'];
		$options = $filtered['options'];
		
		extract($options);

		$content = FileManager::readFile($cache_location . $key);

		if (!empty($content) && preg_match('/\\' . $enclosing_tags[0] . $cache_name . $cache_format_search . $enclosing_tags[1] . '/', $content, $matches)) {

			if (preg_match('/' . $cache_format_search . '/', $matches[0], $date_match)) {
				$expiration = $date_match[0];
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $expiration, $key, $options);
		$expiration = self::_applyFilter(get_class(), __FUNCTION__, $expiration, array('event' => 'return'));

		return $expiration;
	}

	/**
	 * Delete the cached file, if it exist
	 *
	 * @param string $key The key to search for the cache by
	 * @param array $options Options that define how the cache data is deleted
	 * 			-'cache_location' _string_: The location the cached is saved
	 *
	 * @return void
	 * @access public
	 */
	public static function deleteCache(string $key, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $options);

		$defaults = array('cache_location' => self::$_cache_location);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'options' => $options
		), array('event' => 'args'));
		
		$key = $filtered['key'];
		$options = $filtered['options'];
		
		extract($options);

		FileManager::deleteFile($cache_location . $key);
		self::_notify(get_class() . '::' . __FUNCTION__, $key, $options);
	}

	/**
	 * Write content to memcache.
	 *
	 * @param string $key The key that will be assoicated with the file
	 * @param mixed $content The content that will be written to memcache
	 * @param array $options Options that are used when defining memcache
	 * 			-'flag' _int_: The flag used when writing to the memcache object
	 * 			-'cache_expire'_int_: Override the default cache expire. Expiration is set in seconds
	 * 			-'add_only' _boolean_: Only add to the cache if the key does not already exist
	 * 			-'replace' _boolean_: Only add to the cache if the key does exit
	 *
	 * @return void
	 * @access public
	 */
	public static function writeMemcache($key, $content, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $content, $options);

		$defaults = array(
			'flag' => MEMCACHE_COMPRESSED,
			'cache_expire' => self::$_cache_expire,
			'add_only' => false,
			'replace' => false
		);

		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'content' => $content,
			'options' => $options
		), array('event' => 'args'));
		$key = $filtered['key'];
		$content = $filtered['content'];
		$options = $filtered['options'];
		extract($options);

		if ($add_only)
			self::$_memcache->add($key, $content, $flag, $cache_expire);
		else if ($replace)
			self::$_memcache->replace($key, $content, $flag, $cache_expire);
		else
			self::$_memcache->set($key, $content, $flag, $cache_expire);

		self::_notify(get_class() . '::' . __FUNCTION__, $key, $content, $options);
	}

	/**
	 * Read content from mime cache
	 *
	 * @param string $key The key that is assoicated with the file
	 * @param array $options Options that are used when defining memcache
	 * 			-'flag' _int_: The flag used when reading from the memcache object
	 *
	 * @return mixed $content Content returned from the memcache object
	 * @access public
	 */
	public static function readMemcache($key, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $options);

		$defaults = array('flags' => MEMCACHE_COMPRESSED, );
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'options' => $options
		), array('event' => 'args'));
		
		$key = $filtered['key'];
		$options = $filtered['options'];
		extract($options);

		$content = self::$_memcache->get($key, $flags);

		self::_notify(get_class() . '::' . __FUNCTION__, $content, $key, $options);
		$content = self::_applyFilter(get_class(), __FUNCTION__, $content, array('event' => 'return'));

		return $content;
	}

	/**
	 * Remove cached data from memcache
	 *
	 * @param string $key The key that is assoicated with the file
	 * @param array $options Options that are used when deleting a file
	 * 			-'flush' _boolean_: Default is false, if set to true will delete all files in cache
	 *
	 * @return void
	 * @access public
	 */
	public static function removeMemcache($key, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $options);

		$defaults = array('flush' => false);

		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'options' => $options
		), array('event' => 'args'));
		
		$key = $filtered['key'];
		$options = $filtered['options'];
		extract($options);

		if ($flush)
			self::$_memcache->flush();
		else
			self::$_memcache->delete($key);

		self::_notify(get_class() . '::' . __FUNCTION__, $key, $options);
	}

	/**
	 * Returns the memcache object
	 *
	 * @return object $memcache The memcache object
	 * @access pulbic
	 */
	public static function getMemcacheObject() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$object = self::$_memcache;

		self::_notify(get_class() . '::' . __FUNCTION__, $object);
		$object = self::_applyFilter(get_class(), __FUNCTION__, $object, array('event' => 'return'));

		return $object;
	}

}
