<?php

class Cache {


	private static $mc;
	private static $default_timeout = 5;


	public function __construct($mc) {

		self::$mc = $mc;

    }


	public function get($key) {
		
		return self::$mc->get($key);

	}


	public function set($key, $value) {

		return self::$mc->set($key, $value, self::$default_timeout);
		
	}


	public function getAllKeys() {

		return self::$mc->getAllKeys();
		
	}


	public function delete($key) {

		return self::$mc->delete($key);
		
	}


	public function deleteMulti(array $keys) {

		return self::$mc->deleteMulti($keys);
		
	}


	public function flush() {

		return self::$mc->flush();
		
	}


}


// MEMCACHED
$mc = new Memcached();
$mc->addServer("memcached", 11211);

$cache = new Cache($mc);