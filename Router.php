<?php
/**
 * Routing class for PPI.
 *
 */
class PPI_Router {
	
	/**
	 * The file to get the routes from
	 *
	 * @var string|null
	 */
	static $_routingFile = null;
	
	/**
	 * The filename of the cache file on disk of the routes
	 * @todo if we are using raw PHP then we don't need to cache
	 * @var string|null
	 */
	static $_routingCachingFile = null;
	
	function __construct() {
		
	}
	
	/**
	 * Get the routes, either from the cache or newly from disk
	 *
	 * @return unknown
	 */
	function getRoutes() {
		self::$_routingFile      = PPI_Registry::get('PPI_Base::routing_file', null);
		self::$_routingCacheFile = PPI_Registry::get('PPI_Base::routing_cache_file', null);
		clearstatcache();
		if(!file_exists(self::$_routingCacheFile) || filemtime($this->_routingFile) < $iLastTime) {
			self::parseRoutes();
		}
		return self::$_routesLoaded ? self::$_routes : unserialize(file_get_contents(self::$_routingCachingFile));
	}
	
	/**
	 * Parse through the routes and return the routes
	 * @return array The Routes
	 */
	function parseRoutes() {
		
	}
	
	/**
	 * Cache the routes to disk
	 */
	function saveRoutes() {
		file_put_contents(self::routingCacheFile, serialize(self::$_routes));
	}
	
}