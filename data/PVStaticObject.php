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

class PVStaticObject extends PVStaticPatterns {

	protected static $_collection = null;

	protected static $_instance;

	/**
	 * Adds a value to the classes Collection. By default the collection is stored
	 * in the public instance data. The stored instance can be retrieved later by called
	 * in it's key value.
	 *
	 * @param string $index The key or index to store the value at
	 * @param mixed $value A mixed value that can be anytype
	 *
	 * @return void
	 * @access public
	 */
	public static function _set($index, $value) {
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}

		self::$_collection -> addWithName($index, $value);
	}

	/**
	 * Retrieves a value that is in the public data collection or was pass through
	 * by the set method.
	 *
	 * @param string $index The index to retrieve a value from
	 *
	 * @return mixed $data The data that was stored in that index
	 */
	public static function _get($index) {
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}
		return self::$_collection -> $index;
	}

	/**
	 * Adds a data to the public collection, index will be assigned. Primary used for adding
	 * launch quanties of data to the collection,
	 *
	 * @param mixed $data Any information type( Object, Array, etc) to add to the public data collection
	 *
	 * @return void
	 * @access public
	 */
	protected static function addToCollection($data) {
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}
		self::$_collection -> add($data);
	}//end

	/**
	 * Adds data to the public collection.
	 * @see seet
	 *
	 * @todo check the relevance of get and set
	 */
	protected static function addToCollectionWithName($name, $data) {
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}
		self::$_collection -> addWithName($name, $data);
	}//end

	public function getIterator() {
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}
		return self::$_collection -> getIterator();
	}

	protected static function _getSqlSearchDefaults() {
			
		$defaults = array(
			'custom_where' => '', 
			'limit' => '', 
			'order_by' => '', 
			'custom_join' => '', 
			'custom_select' => '', 
			'distinct' => '', 
			'group_by' => '', 
			'having' => '', 
			'join_users' => false, 
			'prequery' => '', 
			'current_page' => '', 
			'results_per_page' => '', 
			'paged' => '', 
			'prefix_args' => '', 
			'join_user_roles' => false, 
			'join_content' => false, 
			'join_content' => false, 
			'join_comments' => false, 
			'join_applications' => false, 
			'join_apps' => false, 
			'join_pages' => false, 
			'join_modules' => false, 
			'join_containers' => false
		);

		return $defaults;
	}

	public static function getInstance() {
		if (!self::$_instance) {
			$class = get_class();
			self::$_instance = new $class();
		}

		return self::$_instance;
	}

}//end class
