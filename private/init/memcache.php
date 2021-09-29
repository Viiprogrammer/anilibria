<?php

$redis = $memcached = null;

if($conf['cache'] == 'memcached'){
	$memcached = new Memcached();
	$memcached->addServer($conf['memcache'][0], $conf['memcache'][1]) or die('memcache not work');
}

if($conf['cache'] == 'redis'){
	$redis = new Redis();
	$redis->connect($conf['redis']);
}

class cacheHandler {
	function get($v){
		global $redis, $memcached, $conf;
		if($conf['cache'] == 'redis'){
			return $redis->get($v);
		}
		if($conf['cache'] == 'memcached'){
			return $memcached->get($v);
		}
	}
	function set($key, $val, $time){
		global $redis, $memcached, $conf;
		if($conf['cache'] == 'redis'){ // $key, $time, $val => redis
            if($time > 0){
                return $redis->setex($key, $time, $val);
            } else {
                return $redis->set($key, $val);
            }
		}
		if($conf['cache'] == 'memcached'){ // $key, $val, $time => memcached
			return $memcached->set($key, $val, $time);
		}
	}
}

$cache = new cacheHandler();
