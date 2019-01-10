<?php
namespace prodigyview\util;

use prodigyview\design\StaticObject;

/**
 * Mathematics is a class for doing computations.
 *
 * The class is relatively incomplete and could use more important functions to ease development.
 *
 * @package util
 */
class Mathematics {
	
	use StaticObject;

	/**
	 * Converts a given time into seconds.
	 *
	 * @param int $days The number of days to convert
	 * @param int $hours The number of hours to convert
	 * @param int $minutes The number of minutes to convert
	 * @param int $seconds The number of seconds to converts
	 *
	 * @return int $seconds Returns the total number of seconds based upon passed arguements
	 * @access public
	 */
	public static function convertTimeIntoSeconds($days = 0, $hours = 0, $minutes = 0, $seconds = 0) {

		$total_seconds = 0;

		if (empty($days) || !Validator::isInteger($days)) {
			$days = 0;
		} else {
			$days = 86400 * $days;
		}

		if (empty($hours) || !Validator::isInteger($hours)) {
			$hours = 0;
		} else {
			$hours = 3600 * $hours;
		}

		if (empty($minutes) || !Validator::isInteger($minutes)) {
			$minutes = 0;
		} else {
			$minutes = 60 * $minutes;

		}

		if (empty($seconds) || !Validator::isInteger($seconds)) {
			$seconds = 0;
		}

		return $total_seconds = $days + $hours + $minutes + $seconds;

	}//end convertTimeIntoSeconds

	/**
	 * Converts the number of seconds page into hours
	 *
	 * @param int $seconds The amount of seconds
	 *
	 * @return double $hours The number of hours converted by the past seconds
	 */
	public static function convertSecondsToHours($seconds) {

		return $seconds / 3600;
	}

	/**
	 * Converts Seconds into minutes
	 *
	 * @param int $seconds The amount of seconds
	 *
	 * @return double $minutes The number of minutes
	 */
	public static function convertSecondsToMinutes($seconds) {

		return $seconds / 60;
	}

	/**
	 * Converts the number of seconds into days
	 *
	 * @param int $seconds The amount of seconds
	 *
	 * @return double $days The number of days derived from those seconds
	 */
	public static function convertSecondsToDays($seconds) {

		return $seconds / 60;
	}

	/**
	 * Converts the seconds between two periods in an elasped time
	 *
	 * @param int $seconds The number of seconds
	 *
	 * @return string $timeElasped
	 */
	public static function convertSecondsIntoElapsedTime($seconds) {

		$days = floor($seconds / 86400);
		$hours = floor(($seconds % 86400) / 3600);
		$minutes = floor(($seconds % 3600) / 60);
		$second = floor($seconds % 60);

		if (empty($days)) {
			$days = '00';
		} else if (strlen($days) == 1) {
			$days = '0' . $days;
		}

		if (empty($hours)) {
			$hours = '00';
		} else if (strlen($hours) == 1) {
			$hours = '0' . $hours;
		}

		if (empty($minutes)) {
			$minutes = '00';
		} else if (strlen($minutes) == 1) {
			$minutes = '0' . $minutes;
		}

		if (empty($second)) {
			$second = '00';
		} else if (strlen($second) == 1) {
			$second = '0' . $second;
		}

		return $days . ':' . $hours . ':' . $minutes . ':' . $second;

	}//end convert

}//end class
