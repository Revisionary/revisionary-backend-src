<?php

// MEMCACHED
$cache = new Memcached();
$cache->addServer("memcached", 11211);