<?php
namespace prodigyview\network;

use prodigyview\design\InstanceObject;

/**
 * A class for communicating with other services via socket programming.
 * 
 * Example Usage
 * 
 * ```
 * //Send A Message
 * $socket = new Socket('www.example.com', 1000, array('connect' => true));
 * $response = $socket->send('Hello World');
 * 
 * //Socket Server Example
 * $socket = new Socket('127.0.0.1', 5000, array(
 *	'bind' => true,
 *	'listen' => true
 *	));
 * 
 * $socket->startServer('none', function($message) {
 * 		return 'The World Hears You!';
 * }, 'closure');
 * ```
 * 
 * @package network
 */
class Socket {
	
	use InstanceObject;
	
	/**
	 * The main socket to connect with
	 */
	protected $_socket = null;
	
	/**
	 * An optional socket used for clients that connect to the current socket.
	 */
	protected $_client = null;
	
	/**
	 * An indication that the socket is running as a server
	 */
	protected $_serverRunning = 0;
	
	/**
	 * Constructor for creating the socket connection.
	 */
	public function __construct(string $host =null, int $port = 0, array $options = array()) {
		
		$defaults = array(
			'domain' => AF_INET,
			'type' => SOCK_STREAM,
			'protocol' => 0,
			'bind' => false,
			'listen' => false,
			'connect' => false
		);
		
		$options += $defaults;
		
		$this -> create($options['domain'], $options['type'], $options['protocol']);
		
		if($options['bind']) {
			$this -> bind($host, $port);
		}
		
		if($options['connect']) {
			$this -> connect($host, $port);
		}
		
		if($options['listen']) {
			$this -> listen();
		}
		
	}
	
	/**
	 * Creates and returns a socket resource, also referred to as an endpoint of communication. 
	 * 
	 * @param int $domain The domain parameter specifies the protocol family to be used by the socket.
	 * @param int $type The type parameter selects the type of communication to be used by the socket.
	 * @param int $protocol The protocol parameter sets the specific protocol within the specified domain to be used when communicating on the returned socket. 
	 */
	public function create(int $domain = AF_INET , int $type = SOCK_STREAM , ?int $protocol = 0) {
			
		$this ->_socket = socket_create($domain , $type, $protocol);
		
		if(!$this ->_socket) {
			throw new \Exception( sprintf( "Unable to create a socket: %s", socket_strerror( socket_last_error() ) ) ); 
		}
		
	}
	
	/**
	 * Bind the socket to a host and port.
	 * 
	 * @param string $host The host to bind the socket the too
	 * @param string $port The port to bind on.
	 */
	public function bind(string $host, ?int $port = 0) {
		$result = socket_bind($this ->_socket, $host, $port);
		
		if(!$result) {
			throw new \Exception( sprintf( "Unable to connect to server %s:%s %s", $host, $port, socket_strerror( socket_last_error() ) ) ); 
		}
	}
	
	/**
	 * Connect the socket a server that you will eventually send messages too.
	 * 
	 * @param string $host The host name
	 * @param int $port The port id
	 * 
	 * @return void
	 */
	public function connect(string $host, ?int $port = 0) {
		
		$result = socket_connect($this->_socket, $host, $port);
		
		if(!$result) {
			throw new \Exception( sprintf( "Unable to connect to server %s:%s %s", $host, $port, socket_strerror( socket_last_error() ) ) ); 
		}
	}
	
	/**
	 * Listens for a connection made to the socket from another service.
	 * 
	 * @param int $backlog A maximum of backlog incoming connections will be queued for processing.
	 * 
	 * @return void
	 */
	public function listen(int $backlog = 100) {
		$result = socket_listen($this ->_socket, $backlog);
	}
	
	/**
	 * Sets the option for a socket as specified in: http://php.net/manual/en/function.socket-set-option.php
	 * 
	 * @param int $level The level parameter specifies the protocol level at which the option resides.
	 * @param int $optname The available socket options are the same as those for the socket_get_option() function.
	 * @param mixed $value The option value.
	 * @param socket $socket Optional for defining what socket to use.
	 * 
	 * @param boolean Returns true if set correctly
	 */
	public function setOption(int $level , int $optname, $value, $socket = null) {
		
		if($socket == null) {
			$socket = $this -> _socket;
		}
		
		$result = socket_set_option($socket , $level , $optname, $value);
		
		return $result;
	}
	
	/**
	 * Gets an option that has been specified, indiciated in http://php.net/manual/en/function.socket-get-option.php
	 * 
	 * @param int $level The level parameter specifies the protocol level at which the option resides.
	 * @param int $optionname The associated $option name
	 * @param socket $socket Optional for defining what socket to use.
	 * 
	 * @return mixed The vaue of the given option or false.
	 */
	public function getOption(int $level , int $optname, $socket = null) {
		
		if($socket == null) {
			$socket = $this -> _socket;
		}
		
		$result = socket_get_option($socket , $level , $optname);
		
		return $result;
	}
	
	/**
	 * Adds a callback for the socket when a message has been sent. The callback can be a closure, instance of static method.
	 * 
	 * @param mixed $class If an instance, pass the instance. If static, pass the full name of the class. If closure, put anything.
	 * @param mixed $method If an instance or static, pass the name of the method. If a closure, pass the closure here
	 * @param string $type Either pass instance, closure or static, depending on the callback
	 * @param int $max_read_length The maximum number of bytes read. Can use \r, \n, or \0 to end reading as well
	 * @param int $read_type Optional type parameter of PHP_BINARY_READ or PHP_NORMAL_READ
	 * 
	 * @return void
	 */
	public function addCallback($class, $method, string  $type = 'closure', int $max_read_length = 5000, int $read_type = PHP_BINARY_READ) {
		$this -> _client = socket_accept($this -> _socket);
		
		$input = $this -> receive($max_read_length, $read_type, $this -> _client);
		
		if ($type === 'closure')
			$response = call_user_func_array($method, array($input));
		else if ($type === 'instance')
			$response = $this->_invokeMethod($class, $method, $input);
		else
			$response = $this->_invokeStaticMethod($class, $method, $input);
		
		$this->send($response, false, $this -> _client);
	}
	
	/**
	 * Run a socket as a server that continously listens for input.
	 * 
	 * @param mixed $class If an instance, pass the instance. If static, pass the full name of the class. If closure, put anything.
	 * @param mixed $method If an instance or static, pass the name of the method. If a closure, pass the closure here
	 * @param string $type Either pass instance, closure or static, depending on the callback
	 * @param int $max_read_length The maximum number of bytes read. Can use \r, \n, or \0 to end reading as well
	 * @param int $read_type Optional type parameter of PHP_BINARY_READ or PHP_NORMAL_READ
	 * 
	 * @return void
	 */
	public function startServer($class, $method, string  $type = 'closure', int $max_read_length = 5000, int $read_type = PHP_BINARY_READ) {
		
		$this -> _serverRunning = 1;
		
		while($this -> _serverRunning) {
			$this -> addCallback($class, $method, $type, $max_read_length, $read_type);
			socket_close($this -> _client);
		}
	}
	
	/**
	 * Stops the socket server
	 * 
	 * @return void
	 */
	public function stopServer() {
		$this -> _serverRunning = 0;
	}
	
	/**
	 * Send a message to the server listening on the socket.
	 * 
	 * @param string $message A message to send to the server
	 * @param boolean $wait_for_response Will tell the socket to execute a recieve function for waiting
	 * @param socket $socket Optional for defining what socket to use.
	 */
	public function send(?string $message, bool $wait_for_response = true, $socket = null) {
		
		$response = null;
			
		if(!$socket) {
			$socket = $this -> _socket;
		}
		
		$result = socket_write($socket, $message, strlen($message));
		
		if($result === false) {
			throw new \Exception( sprintf( "Unable to write to socket: %s", socket_strerror( socket_last_error() ) ) ); 
		}
		
		if($wait_for_response) {
			$response = $this -> receive(5000, PHP_BINARY_READ);
		}
		
		return $response;
	}
	
	/**
	 * Recieve a message from a socket.
	 * 
	 * @param int $max_read_length The maximum number of bytes read. Can use \r, \n, or \0 to end reading as well
	 * @param int $read_type Optional type parameter of PHP_BINARY_READ or PHP_NORMAL_READ
	 * 
	 * @return string
	 */
	public function receive($max_read_length = 5000, $read_type = PHP_BINARY_READ, $socket = null){
			
		if($socket == null) {
			$socket = $this -> _socket;
		}
		
		$result = '';
		
		while($buffer = socket_read($socket, $max_read_length, $read_type)){
			
			if($buffer === false) {
				throw new \Exception( sprintf( "Unable to read from socket: %s", socket_strerror( socket_last_error() ) ) ); 
			}
			
			$break = false;
			
			if($buffer = trim($buffer)) {
            		$break = true;
			}
			
			$result  .= $buffer;
			
			if($break)
				break;
		}//end while
		
		return $result;
	}
	
	/**
	 * Sets the resource to either be blocking/synchronous, or none-blocking/asynchronous.
	 * 
	 * @param boolean $block If set to true with block. If set to false, will run in none blocking
	 * @param resource $socket An optional $socket to specify, otherwise, will use default socket.
	 * 
	 * @return void
	 */
	public function setBlocking(bool $block, $socket = null) {
		if(!$socket) {
			$socket = $this -> _socket;
		}
		
		if($block) {
			socket_set_block($socket);
		} else {
			socket_set_nonblock($socket);
		}
	}
	
	/**
	 * Close the connection to the socket
	 * 
	 * @return void
	 */
	public function close() {
		if($this -> _client) {
			socket_close($this -> _client);
		}
		
		socket_close($this -> _socket);
	}
	
}
