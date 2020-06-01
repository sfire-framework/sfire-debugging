<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

namespace sFire\Debugging;


/**
 * Class Debug
 * @package sFire\Debugging
 */
class Debug {
	

	/**
	 * Keep track of the times
	 * @var array
	 */
	private static array $time = [];


	/**
	 * Advanced debugging of variables
	 * @param mixed $data 
	 * @param bool $export
	 * @return mixed
	 */
	public static function dump($data = null, bool $export = false) {

		if($data === null || (true === is_string($data) && trim($data) === '') || true === is_bool($data)) {

			if($export) {
				return var_export($data);
			}

			echo '<pre>' . var_dump($data) . '</pre>';

			return null;
		}
		
		if($export) {
			return print_r($data, true);
		}

		echo '<pre>' . print_r($data, true) . '</pre>';
		return null;
	}


	/**
	 * Calculate process times by adding new times an exporting them
	 * @param string $key
	 * @return array|void
	 */
	public static function time(string $key = null): ?array {

		if(count(static::$time) === 0) {
			static::$time = [];
		}

		if(null !== $key) {
			
			static::$time[] = ['time' => microtime(), 'key' => $key];
			return;
		}

		static::$time[] = ['time' => microtime(), 'key' => 'end'];
		
		$laps = [];
		$iterations = count(static::$time);

		for($i = 1; $i < $iterations; $i++) {

			$start 	= explode(' ', static::$time[$i - 1]['time']);
			$end  	= explode(' ', static::$time[$i]['time']);

			$laps[static::$time[$i - 1]['key'] . ' - ' . static::$time[$i]['key']] = number_format((($end[1] + $end[0]) - ($start[1] + $start[0])), 4, '.', '');
		}

		return $laps;
	}
}