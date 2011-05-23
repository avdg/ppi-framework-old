<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Core
 * @link      www.ppiframework.com
 *
 */
class PPI_Router implements PPI_Router_Interface {

    /**
     * The array of routes
     *
     * @var null|array
     */
    static $_routes = null;

	/**
	 * The file to get the routes from
	 *
	 * @var string $_routingFile
	 */
	static $_routingFile = null;

	/**
	 * The filename of the cache file on disk of the routes
     * 
	 * @todo if we are using raw PHP then we don't need to cache
	 * @var string $_routingCachingFile
	 */
	static $_routingCachingFile = null;

    /**
     * The constructor
     */
	function __construct() {

	}

    /**
     * Initialise the router and start grabbing routes
     *
     * @return void
     */
	function init() {
		ppi_dump('heheheh', true);
		$this->_routes = $this->getRoutes();
	}

	/**
	 * Get the routes, either from the cache or newly from disk
	 *
	 * @return array
	 */
	function getRoute() {

		$this->routes = file_get_contents(APPFOLDER . 'Config/routes.php');
		ppi_dump($this->routes, true);
		// Loop through the route array looking for wild-cards
		foreach($this->routes as $key => $val) {
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));
			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri)) {
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE) {
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				$this->_set_request(explode('/', $val));
				return;
			}
		}

		PPI_Helper::getRegistry()->set();

		return self::$_routes;
	}

	/**
	 * Parse through the routes and return the routes
     *
	 * @return array The Routes
	 */
	function parseRoutes() {

	}

	/**
	 * Cache the routes to disk
     * 
     * @return int
	 */
	function saveRoutes() {
		return file_put_contents(self::routingCacheFile, serialize(self::$_routes));
	}

}
