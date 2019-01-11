<?php

namespace prodigyview\system;

use prodigyview\design\StaticObject;

/**
 * The Server class is designd to interface with the server to give information on
 * the current hardware and the operating system.
 * 
 * @package system
 */
class Server {
	
	use StaticObject;

	/**
	 * This function returns the system load as a percentage. Use in conjunction with
	 * getSystemCores method.
	 * 
	 * @param int The number of cores.
	 * @param double 
	 * 
	 * @return double
	 */
	public static function getSystemLoad(int $coreCount = 2, double $interval = 1) : double {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $coreCount, $interval);
		
		$rs = sys_getloadavg();
		$interval = $interval >= 1 && 3 <= $interval ? $interval : 1;
		$load = $rs[$interval];
		$calculated_load = round(($load * 100) / $coreCount, 2);
		
		$calculated_load = self::_applyFilter(get_class(), __FUNCTION__, $calculated_load, array('event' => 'return'));
		
		return $calculated_load;
	}

	/**
	 * This function returns the number of cpu cores from the server.
	 * 
	 * @return
	 */
	public static function getSystemCores() : ?int {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$cmd = "uname";
		$OS = strtolower(trim(shell_exec($cmd)));

		switch($OS) {
			case('linux') :
				$cmd = "cat /proc/cpuinfo | grep processor | wc -l";
				break;
			case('freebsd') :
				$cmd = "sysctl -a | grep 'hw.ncpu' | cut -d ':' -f2";
				break;
			default :
				unset($cmd);
		}

		if ($cmd != '') {
			$cpuCoreNo = intval(trim(shell_exec($cmd)));
		}
		
		$cores = ($cpuCoreNo) ? 1 : $cpuCoreNo;

		$cores = self::_applyFilter(get_class(), __FUNCTION__, $cores, array('event' => 'return'));
		
		return empty($cpuCoreNo) ? 1 : $cpuCoreNo;
	}

	/**
	 * This function returns the number of PHP connections.
	 */
	public static function getHttpConnections() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$unique = array();

		if (function_exists('exec')) {

			$www_total_count = 0;
			$www_unique_count = 0;

			@exec('netstat -an | egrep \':80|:443\' | awk \'{print $5}\' | grep -v \':::\*\' |  grep -v \'0.0.0.0\'', $results);

			foreach ($results as $result) {
				$array = explode(':', $result);
				$www_total_count++;

				if (preg_match('/^::/', $result)) {
					$ipaddr = $array[3];
				} else {
					$ipaddr = $array[0];
				}

				if (!in_array($ipaddr, $unique)) {
					$unique[] = $ipaddr;
					$www_unique_count++;
				}
			}

			unset($results);
			
			$connections = count($unique);
			
			$connections = self::_applyFilter(get_class(), __FUNCTION__, $connections, array('event' => 'return'));

			return $connections;

		}

	}

	/**
	 * This function returns the server memory usage as a percentage.
	 */
	public static function getServerMemoryUsage() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$free = shell_exec('free');
		$free = (string)trim($free);
		$free_arr = explode("\n", $free);
		$mem = explode(" ", $free_arr[1]);
		$mem = array_filter($mem);
		$mem = array_merge($mem);
		$memory_usage = $mem[2] / $mem[1] * 100;
		
		$memory_usage = self::_applyFilter(get_class(), __FUNCTION__, $memory_usage, array('event' => 'return'));

		return $memory_usage;

	}

	/**
	 * This function returns the amount of disk usage as a percentage.
	 */
	public static function getDiskUsage() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$disktotal = disk_total_space('/');
		$diskfree = disk_free_space('/');
		$diskuse = round(100 - (($diskfree / $disktotal) * 100)) . '%';
		
		$diskuse = self::_applyFilter(get_class(), __FUNCTION__, $diskuse, array('event' => 'return'));

		return $diskuse;

	}

	/**
	 * This function returns the server uptime.
	 */
	public static function getServerUptime() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$uptime = floor(preg_replace('/\.[0-9]+/', '', file_get_contents('/proc/uptime')) / 86400);
		
		$uptime = self::_applyFilter(get_class(), __FUNCTION__, $uptime, array('event' => 'return'));

		return $uptime;

	}

	/**
	 * This function returns the kernel version.
	 */
	public static function getKernelVersion() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$kernel = explode(' ', file_get_contents('/proc/version'));
		$kernel = $kernel[2];
		
		$kernel = self::_applyFilter(get_class(), __FUNCTION__, $kernel, array('event' => 'return'));

		return $kernel;

	}

	/**
	 * This function returns the number of running processes.
	 */
	public static function getNumberProcesses() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$proc_count = 0;
		$dh = opendir('/proc');

		while ($dir = readdir($dh)) {
			if (is_dir('/proc/' . $dir)) {
				if (preg_match('/^[0-9]+$/', $dir)) {
					$proc_count++;
				}
			}
		}
		
		$proc_count = self::_applyFilter(get_class(), __FUNCTION__, $proc_count, array('event' => 'return'));

		return $proc_count;

	}

	/**
	 * This function returns the current memory usage.
	 */
	public static function getMemoryUsage() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$mem = memory_get_usage(true);

		if ($mem < 1024) {

			$memory = $mem . ' B';

		} elseif ($mem < 1048576) {
			$memory = round($mem / 1024, 2) . ' KB';
		} else {
			$memory = round($mem / 1048576, 2) . ' MB';
		}

		$memory = self::_applyFilter(get_class(), __FUNCTION__, $memory, array('event' => 'return'));
		
		return $memory;
	}

}
