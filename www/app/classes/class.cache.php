<?php

use MemcachedTags\MemcachedTags;

class Cache {


	private static $mc;
	private static $mcTags;
	private static $default_timeout = 20;


	public function __construct($mc, $mcTags) {

		self::$mc = $mc;
		self::$mcTags = $mcTags;

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

$MemcachedTags = new MemcachedTags($mc);

$cache = new Cache($mc, $MemcachedTags);