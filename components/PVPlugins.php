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

class PVPlugins extends PVStaticObject {

	/**
	 * Calling a hook is a user place hook for plugins. Plugins with this hook name will be called.
	 * Hooks are good for easily modifying the system without modifying a code of a specific section.
	 * Remember to set the hookname and function called accordingly in the database
	 *
	 * @param string hookname: The name of hooke associated with a plugin in the database
	 * @param mixed $args An infinite amount of arguements that can be passed to a plugin
	 *
	 * @return Returns
	 * @access public
	 */
	public static function callHook($hookname) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $hookname);

		$args = func_get_args();
		array_shift($args);

		$passable_args = array();
		foreach ($args as $key => &$arg) {
			$passable_args[$key] = &$arg;
		}

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('hookname' => $hookname, 'passable_args' => $passable_args), array('event' => 'args'));
		$hookname = $filtered['hookname'];
		$passable_args = $filtered['passable_args'];

		$hookname = PVDatabase::makeSafe($hookname);
		$ovveride = true;
		$query = "SELECT plugin_function, plugin_file, plugin_override, plugin_directory FROM " . PVDatabase::getPluginsTableName() . " WHERE plugin_hook='$hookname' AND plugin_enabled='1' ORDER BY plugin_order ";
		$result = PVDatabase::query($query);

		while ($row = PVDatabase::fetchArray($result)) {
			if (!empty($row['plugin_function'])) {
				call_user_func_array($row['plugin_function'], $passable_args);
			}

			if ($row['plugin_override'] == 1) {
				$ovveride = false;
			}
		}//end while
		self::_notify(get_class() . '::' . __FUNCTION__, $hookname);

		return $ovveride;
	}//

	public static function callHookOverride($hookname) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $hookname);

		$args = func_get_args();
		array_shift($args);

		$passable_args = array();
		foreach ($args as $key => &$arg) {
			$passable_args[$key] = &$arg;
		}

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('hookname' => $hookname, 'passable_args' => $passable_args), array('event' => 'args'));
		$hookname = $filtered['hookname'];
		$passable_args = $filtered['passable_args'];

		$hookname = PVDatabase::makeSafe($hookname);
		$ovveride = true;
		$query = "SELECT plugin_function, plugin_file, plugin_override, plugin_directory FROM " . PVDatabase::getPluginsTableName() . " WHERE plugin_hook='$hookname' AND plugin_enabled='1' ORDER BY plugin_order ";
		$result = PVDatabase::query($query);

		while ($row = PVDatabase::fetchArray($result)) {
			if (!empty($row['plugin_function'])) {
				call_user_func_array($row['plugin_function'], $passable_args);
			}

			if ($row['plugin_override'] == 1) {
				$ovveride = false;
			}
		}//end while

		self::_notify(get_class() . '::' . __FUNCTION__, $hookname);

		return $ovveride;
	}//

	/**
	 * Install or update a plugin into the system based on the plugin's unique name.
	 *
	 * @param array $args The arguements that define the plugin
	 * 			-'plugin_unique_name' _string_: A custom unique identifer for the plugin
	 * 			-'plugin_name' _string_: The name of plugin
	 * 			-'plugin_function' _string_: The function the plugin calls when loaded
	 * 			-'plugin_command' _string_: The command for the plugin to execute
	 * 			-'plugin_order' _int_: The order the plugin is loaded in
	 * 			-'plugin_ovveride' _boolean_: Is the plugin used to ovveride other plugins
	 * 			-'plugin_type' _string_: The type of plugin
	 * 			-'plugin_version' _double_: The current version of the plugin
	 * 			-'plugin_parameters' _string_: The parameters in the plugin
	 * 			-'plugin_author' _string_: The author of the plugin
	 * 			-'plugin_homepage' _string_: The homepage of the plugin
	 * 			-'plugin_license' _string_: The license of the plugin
	 * 			-'plugin_file' _string_: The main file to include in loading the plug-in
	 * 			-'plugin_uninstall_function' _string_: The function to be called when uninstalling the plugin
	 * 			-'is_plugin_editable' _boolean_: Can the plugin be modified. Default is false.
	 * 			-'plugin_description' _string_: A description of the plug-in
	 * 			-'plugin_preferences' _string_: Preferences for the plugin
	 * 			-'plugin_hook' _string_: The hook name for the pluginn. Calls it when hook in initizalied.
	 * 			-'plugin_enabled' _boolean_: Is the current plugin enabled. Default is set to false
	 * 			-'plugin_directory' _string_: The location the plugin resides in relative to PV_PLUGIN define
	 * 			-'plugin_admin_function' _string_: The function to be called for the plug-ins admin
	 * 			-'plugin_application' _string_: The unique identifer of the application the plugin is associated with
	 * 			-'is_frontend_plugin' _boolean_: Decides if the plugin can be loaded on the front end
	 * 			-'is_admin_plugin' _boolean_: Decides if the plugin can be loaded on the back end
	 * 			-'plugin_object' _string_: The plugin's object, if it has one
	 * 			-'plugin_language' _string: The plugin's language. For PHP, put php or plugin will not loade
	 *
	 * @return void
	 * @access public
	 */
	public static function installPlugin($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getPluginDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$plugin_override = PVTools::convertTextBoolean($plugin_override);
		$plugin_enabled = PVTools::convertTextBoolean($plugin_enabled);
		$is_plugin_editable = PVTools::convertTextBoolean($is_plugin_editable);

		$plugin_order = ceil($plugin_order);
		$plugin_override = ceil($plugin_override);
		$plugin_enabled = ceil($plugin_enabled);
		$is_plugin_editable = ceil($is_plugin_editable);
		$is_frontend_plugin = ceil($is_frontend_plugin);
		$is_admin_plugin = ceil($is_admin_plugin);

		if (!PVValidator::isDouble($plugin_version) && !PVValidator::isInteger($plugin_version)) {
			$plugin_version = 0;
		}

		$query = "SELECT * FROM " . PVDatabase::getPluginsTableName() . " WHERE plugin_unique_name='$plugin_unique_name' ";
		$result = PVDatabase::query($query);

		if (PVDatabase::resultRowCount($result) <= 0) {
			$query = "INSERT INTO " . PVDatabase::getPluginsTableName() . "( plugin_unique_name, plugin_function, plugin_command, plugin_application, plugin_order, plugin_override, plugin_type, plugin_version, plugin_parameters, plugin_author, plugin_homepage, plugin_license, plugin_name, plugin_file, plugin_uninstall_function, plugin_preferences, plugin_hook, plugin_enabled, plugin_description , is_plugin_editable, plugin_directory, plugin_admin_function,  is_frontend_plugin, is_admin_plugin, plugin_object, plugin_language ) VALUES( '$plugin_unique_name', '$plugin_function', '$plugin_command', '$plugin_application', '$plugin_order', '$plugin_override', '$plugin_type', '$plugin_version', '$plugin_parameters', '$plugin_author', '$plugin_homepage', '$plugin_license', '$plugin_name', '$plugin_file', '$plugin_uninstall_function', '$plugin_preferences', '$plugin_hook', '$plugin_enabled', '$plugin_description', '$is_plugin_editable', '$plugin_directory', '$plugin_admin_function' , '$is_frontend_plugin', '$is_admin_plugin', '$plugin_object' , '$plugin_language' )";
			PVDatabase::query($query);
		} else {
			$query = "UPDATE " . PVDatabase::getPluginsTableName() . " SET plugin_function='$plugin_function', plugin_command='$plugin_command', plugin_application='$plugin_application', plugin_order='$plugin_order', plugin_override='$plugin_override', plugin_type='$plugin_type', plugin_version='$plugin_version', plugin_parameters='$plugin_parameters', plugin_author='$plugin_author', plugin_homepage='$plugin_homepage', plugin_license='$plugin_license', plugin_name='$plugin_name', plugin_file='$plugin_file', plugin_uninstall_function='$plugin_uninstall_function', plugin_preferences='$plugin_preferences', plugin_hook='plugin_hook', plugin_enabled='$plugin_enabled', plugin_description='$plugin_description', is_plugin_editable='$is_plugin_editable', plugin_directory='$plugin_directory', plugin_admin_function='$plugin_admin_function', is_frontend_plugin='$is_frontend_plugin', is_admin_plugin='$is_admin_plugin', plugin_object='$plugin_object', plugin_language='$plugin_language' WHERE  plugin_unique_name='$plugin_unique_name'";
			PVDatabase::query($query);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $args);
	}

	/**
	 * Search for plugins currently in the database
	 *
	 * @param array $args The arguements that define the plugin
	 * 			-'plugin_id' _string_: The name of the plugin
	 * 			-'plugin_unique_name' _string_: A custom unique identifer for the plugin
	 * 			-'plugin_name' _string_: The name of plugin
	 * 			-'plugin_function' _string_: The function the plugin calls when loaded
	 * 			-'plugin_command' _string_: The command for the plugin to execute
	 * 			-'plugin_order' _int_: The order the plugin is loaded in
	 * 			-'plugin_ovveride' _boolean_: Is the plugin used to ovveride other plugins
	 * 			-'plugin_type' _string_: The type of plugin
	 * 			-'plugin_version' _double_: The current version of the plugin
	 * 			-'plugin_parameters' _string_: The parameters in the plugin
	 * 			-'plugin_author' _string_: The author of the plugin
	 * 			-'plugin_homepage' _string_: The homepage of the plugin
	 * 			-'plugin_license' _string_: The license of the plugin
	 * 			-'plugin_file' _string_: The main file to include in loading the plug-in
	 * 			-'plugin_uninstall_function' _string_: The function to be called when uninstalling the plugin
	 * 			-'is_plugin_editable' _boolean_: Can the plugin be modified. Default is false.
	 * 			-'plugin_description' _string_: A description of the plug-in
	 * 			-'plugin_preferences' _string_: Preferences for the plugin
	 * 			-'plugin_hook' _string_: The hook name for the pluginn. Calls it when hook in initizalied.
	 * 			-'plugin_enabled' _boolean_: Is the current plugin enabled. Default is set to false
	 * 			-'plugin_directory' _string_: The location the plugin resides in relative to PV_PLUGIN define
	 * 			-'plugin_admin_function' _string_: The function to be called for the plug-ins admin
	 * 			-'plugin_application' _string_: The unique identifer of the application the plugin is associated with
	 * 			-'is_frontend_plugin' _boolean_: Decides if the plugin can be loaded on the front end
	 * 			-'is_admin_plugin' _boolean_: Decides if the plugin can be loaded on the back end
	 * 			-'plugin_object' _string_: The plugin's object, if it has one
	 * 			-'plugin_language' _string: The plugin's language. For PHP, put php or plugin will not loade
	 *
	 * @return array $plugins Returns a list of plugins
	 * @access public
	 */
	public static function getPluginList($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getPluginDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$custom_where = $args['custom_where'];
		$custom_join = $args['custom_join'];
		$custom_select = $args['custom_select'];
		$args = PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);

		$first = 1;
		$table_name = PVDatabase::getPluginsTableName();
		$db_type = PVDatabase::getDatabaseType();
		$content_array = array();
		$WHERE_CLAUSE = '';

		if (!empty($plugin_unique_name)) {

			$plugin_unique_name = trim($plugin_unique_name);

			if ($first == 0 && ($plugin_unique_name[0] != '+' && $plugin_unique_name[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_unique_name[0] == '+' || $plugin_unique_name[0] == ',') && $first == 1) {
				$plugin_unique_name[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_unique_name, 'plugin_unique_name');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_function)) {

			$plugin_function = trim($plugin_function);

			if ($first == 0 && ($plugin_function[0] != '+' && $plugin_function[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_function[0] == '+' || $plugin_function[0] == ',') && $first == 1) {
				$plugin_function[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_function, 'plugin_function');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_command)) {

			$plugin_command = trim($plugin_command);

			if ($first == 0 && ($plugin_command[0] != '+' && $plugin_command[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_command[0] == '+' || $plugin_command[0] == ',') && $first == 1) {
				$plugin_command[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_command, 'plugin_command');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_order)) {

			$plugin_order = trim($plugin_order);

			if ($first == 0 && ($plugin_order[0] != '+' && $plugin_order[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_order[0] == '+' || $plugin_order[0] == ',') && $first == 1) {
				$plugin_order[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_order, 'plugin_order');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_override)) {

			$plugin_override = trim($plugin_override);

			if ($first == 0 && ($plugin_override[0] != '+' && $plugin_override[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_override[0] == '+' || $plugin_override[0] == ',') && $first == 1) {
				$plugin_override[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_override, 'plugin_override');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_type)) {

			$plugin_type = trim($plugin_type);

			if ($first == 0 && ($plugin_type[0] != '+' && $plugin_type[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_type[0] == '+' || $plugin_type[0] == ',') && $first == 1) {
				$plugin_type[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_type, 'plugin_type');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_version)) {

			$plugin_version = trim($plugin_version);

			if ($first == 0 && ($plugin_version[0] != '+' && $plugin_version[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_version[0] == '+' || $plugin_version[0] == ',') && $first == 1) {
				$plugin_version[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_version, 'plugin_version');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_parameters)) {

			$plugin_parameters = trim($plugin_parameters);

			if ($first == 0 && ($plugin_parameters[0] != '+' && $plugin_parameters[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_parameters[0] == '+' || $plugin_parameters[0] == ',') && $first == 1) {
				$plugin_parameters[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_parameters, 'plugin_parameters');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_author)) {

			$plugin_author = trim($plugin_author);

			if ($first == 0 && ($plugin_author[0] != '+' && $plugin_author[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_author[0] == '+' || $plugin_author[0] == ',') && $first == 1) {
				$plugin_author[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_author, 'plugin_author');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_homepage)) {

			$plugin_homepage = trim($plugin_homepage);

			if ($first == 0 && ($plugin_homepage[0] != '+' && $plugin_homepage[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_homepage[0] == '+' || $plugin_homepage[0] == ',') && $first == 1) {
				$plugin_homepage[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_homepage, 'plugin_homepage');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_license)) {

			$plugin_license = trim($plugin_license);

			if ($first == 0 && ($plugin_license[0] != '+' && $plugin_license[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_license[0] == '+' || $plugin_license[0] == ',') && $first == 1) {
				$plugin_license[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_license, 'plugin_license');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_name)) {

			$plugin_name = trim($plugin_name);

			if ($first == 0 && ($plugin_name[0] != '+' && $plugin_name[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_name[0] == '+' || $plugin_name[0] == ',') && $first == 1) {
				$plugin_name[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_name, 'plugin_name');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_file)) {

			$plugin_file = trim($plugin_file);

			if ($first == 0 && ($plugin_file[0] != '+' && $plugin_file[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_file[0] == '+' || $plugin_file[0] == ',') && $first == 1) {
				$plugin_file[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_file, 'plugin_file');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_uninstall_function)) {

			$plugin_uninstall_function = trim($plugin_uninstall_function);

			if ($first == 0 && ($plugin_uninstall_function[0] != '+' && $plugin_uninstall_function[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_uninstall_function[0] == '+' || $plugin_uninstall_function[0] == ',') && $first == 1) {
				$plugin_uninstall_function[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_uninstall_function, 'plugin_uninstall_function');

			$first = 0;
		}//end not empty app_id

		if (!empty($is_plugin_editable)) {

			$is_plugin_editable = trim($is_plugin_editable);

			if ($first == 0 && ($is_plugin_editable[0] != '+' && $is_plugin_editable[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($is_plugin_editable[0] == '+' || $is_plugin_editable[0] == ',') && $first == 1) {
				$is_plugin_editable[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($is_plugin_editable, 'is_plugin_editable');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_description)) {

			$plugin_description = trim($plugin_description);

			if ($first == 0 && ($plugin_description[0] != '+' && $plugin_description[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_description[0] == '+' || $plugin_description[0] == ',') && $first == 1) {
				$plugin_description[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_description, 'plugin_description');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_preferences)) {

			$plugin_preferences = trim($plugin_preferences);

			if ($first == 0 && ($plugin_preferences[0] != '+' && $plugin_preferences[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_preferences[0] == '+' || $plugin_preferences[0] == ',') && $first == 1) {
				$plugin_preferences[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_preferences, 'plugin_preferences');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_hook)) {

			$plugin_hook = trim($plugin_hook);

			if ($first == 0 && ($plugin_hook[0] != '+' && $plugin_hook[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_hook[0] == '+' || $plugin_hook[0] == ',') && $first == 1) {
				$plugin_hook[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_hook, 'plugin_hook');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_enabled)) {

			$plugin_enabled = trim($plugin_enabled);

			if ($first == 0 && ($plugin_enabled[0] != '+' && $plugin_enabled[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_enabled[0] == '+' || $plugin_enabled[0] == ',') && $first == 1) {
				$plugin_enabled[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_enabled, 'plugin_enabled');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_directory)) {

			$plugin_directory = trim($plugin_directory);

			if ($first == 0 && ($plugin_directory[0] != '+' && $plugin_directory[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_directory[0] == '+' || $plugin_directory[0] == ',') && $first == 1) {
				$plugin_enabled[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_directory, 'plugin_directory');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_application)) {

			$plugin_application = trim($plugin_application);

			if ($first == 0 && ($plugin_application[0] != '+' && $plugin_application[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_application[0] == '+' || $plugin_application[0] == ',') && $first == 1) {
				$plugin_application[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_application, 'plugin_application');

			$first = 0;
		}//end not empty app_id

		if (!empty($is_frontend_plugin)) {

			$is_frontend_plugin = trim($is_frontend_plugin);

			if ($first == 0 && ($is_frontend_plugin[0] != '+' && $is_frontend_plugin[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($is_frontend_plugin[0] == '+' || $is_frontend_plugin[0] == ',') && $first == 1) {
				$is_frontend_plugin[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($is_frontend_plugin, 'is_frontend_plugin');

			$first = 0;
		}//end not empty app_id

		if (!empty($is_admin_plugin)) {

			$is_admin_plugin = trim($is_admin_plugin);

			if ($first == 0 && ($is_admin_plugin[0] != '+' && $is_admin_plugin[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($is_admin_plugin[0] == '+' || $is_admin_plugin[0] == ',') && $first == 1) {
				$is_admin_plugin[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($is_admin_plugin, 'is_admin_plugin');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_object)) {

			$plugin_object = trim($plugin_object);

			if ($first == 0 && ($plugin_object[0] != '+' && $plugin_object[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_object[0] == '+' || $plugin_object[0] == ',') && $first == 1) {
				$plugin_object[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_object, 'plugin_object');

			$first = 0;
		}//end not empty app_id

		if (!empty($plugin_language)) {

			$plugin_language = trim($plugin_language);

			if ($first == 0 && ($plugin_language[0] != '+' && $plugin_language[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($plugin_language[0] == '+' || $plugin_language[0] == ',') && $first == 1) {
				$plugin_language[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($plugin_language, 'plugin_language');

			$first = 0;
		}//end not empty app_id

		$JOIN = '';

		if (!empty($custom_where)) {
			$WHERE_CLAUSE .= ' ' . $custom_where . ' ';
		}

		if (!empty($custom_join)) {
			$JOIN .= ' ' . $custom_join . ' ';
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

		$query = "$prequery SELECT $prefix_args $custom_select FROM $table_name $JOIN $WHERE_CLAUSE";
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
	}//end getPluginList

	/**
	 * Returns a plugin's data in the system.
	 *
	 * @param mixed $plugin_id Either the assigned unique identifier or the id of the plugin
	 *
	 * @return array $plugin The data pertaining that plguin
	 * @access public
	 */
	public static function getPlugin($plugin_unique_name) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $plugin_unique_name);

		$plugin_unique_name = self::_applyFilter(get_class(), __FUNCTION__, $plugin_unique_name, array('event' => 'args'));
		$plugin_unique_name = PVDatabase::makeSafe($plugin_unique_name);

		if (PVValidator::isInteger($plugin_unique_name)) {
			$query = "SELECT * FROM " . PVDatabase::getPluginsTableName() . " WHERE plugin_id='$plugin_unique_name' ";
		} else {
			$query = "SELECT * FROM " . PVDatabase::getPluginsTableName() . " WHERE plugin_unique_name='$plugin_unique_name' ";
		}

		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$row = PVDatabase::formatData($row);

		self::_notify(get_class() . '::' . __FUNCTION__, $row, $plugin_unique_name);
		$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

		return $row;
	}//end getPlugin

	/**
	 * Removes a plugin from the database and the plugin's directory.
	 *
	 * @param mixed $plugin_id Either the assigned unique identifier or the id of the plugin
	 *
	 * @return void
	 * @access public
	 */
	public static function removePlugin($plugin_unique_name) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $plugin_unique_name);

		$plugin_unique_name = self::_applyFilter(get_class(), __FUNCTION__, $plugin_unique_name, array('event' => 'args'));

		if (!empty($plugin_unique_name)) {
			$plugin_info = self::getPlugin($plugin_unique_name);

			if (is_array($plugin_info) && !empty($plugin_info)) {

				extract($plugin_info);

				if (!empty($plugin_directory)) {
					if (file_exists(PV_PLUGINS . $plugin_directory)) {
						PVFileManager::deleteDirectory(PV_PLUGINS . $plugin_directory);
					}
				}//end !empty plugin_directory

				if (file_exists(PV_PLUGINS . $plugin_directory . $plugin_file)) {
					unlink(PV_PLUGINS . $plugin_directory . $plugin_file);
				}

				$query = "DELETE FROM " . PVDatabase::getPluginsTableName() . " WHERE plugin_unique_name='plugin_unique_name' ;";
				PVDatabase::query($query);
			}//end if is array
			self::_notify(get_class() . '::' . __FUNCTION__, $plugin_unique_name);
		}
	}//end removePlugin

	private static function getPluginDefaults() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$defaults = array(
			'plugin_id' => 0, 
			'plugin_unique_name' => '', 
			'plugin_function' => '', 
			'plugin_command' => '', 
			'plugin_order' => 0, 
			'plugin_override' => 0, 
			'plugin_type' => '', 
			'plugin_version' => 0, 
			'plugin_parameters' => '', 
			'plugin_author' => '', 
			'plugin_homepage' => '', 
			'plugin_license' => '', 
			'plugin_name' => '', 
			'plugin_file' => '', 
			'plugin_uninstall_function' => '', 
			'is_plugin_editable' => 0, 
			'plugin_description' => '', 
			'plugin_hook' => '', 
			'plugin_enabled' => 0, 
			'plugin_directory' => '',
			'plugin_admin_function' => '', 
			'plugin_application' => '', 
			'is_frontend_plugin' => 0, 
			'is_admin_plugin' => 0, 
			'plugin_object' => '', 
			'plugin_language' => ''
		);

		$defaults = self::_applyFilter(get_class(), __FUNCTION__, $defaults, array('event' => 'return'));

		return $defaults;
	}

}//end PVPlugins
