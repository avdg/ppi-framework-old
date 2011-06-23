<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Core
 * @link      www.ppiframework.com
 *
 */
class PPI_Router implements PPI_Router_Interface {

	/**
	 * The matched route
	 *
	 * @var string
	 */
	protected $_matchedRoute = null;
	/**
	 * The file to get the routes from
	 *
	 * @var string $_routingFile
	 */
	protected $_routingFile = null;

	/**
	 * The constructor
	 */
	public function __construct(array $options = array()) {

		if (isset($options['routingFile'])) {
			$this->_routingFile = $options['routingFile'];
		}
	}

	/**
	 * Initialise the router and start grabbing routes
	 *
	 * @return void
	 */
	public function init() {

		include $this->getRoutingFile();
		$uri = str_replace(PPI_Helper::getConfig()->system->base_url, '/', PPI_Helper::getFullUrl());
		$route = $uri;

		// Loop through the route array looking for wild-cards
		foreach ($routes as $key => $val) {
			// Convert wild-cards to RegEx
			$key = str_replace(array(':any', ':num'), array('.+', '[0-9]+'), $key);
			// Does the RegEx match?
			if (preg_match('#^' . $key . '$#', $uri)) {
				// Do we have a back-reference?
				if (false !== strpos($val, '$') && false !== strpos($key, '(')) {
					$val = preg_replace('#^' . $key . '$#', $val, $uri);
				}
				$route = $val;
				break;
			}
		}
		$this->setMatchedRoute($route);
	}

	/**
	 * GEt the route currently matched
	 *
	 * @return string
	 */
	public function getMatchedRoute() {
		return $this->_matchedRoute;
	}

	/**
	 * Set the route currently matched
	 *
	 * @param string $route
	 * @return void
	 */
	public function setMatchedRoute($route) {
		$this->_matchedRoute = $route;
	}

	/**
	 * Get the routing file
	 *
	 * @return string
	 */
	public function getRoutingFile() {

		if ($this->_routingFile === null) {
			$this->_routingFile = APPFOLDER . 'Config/routes.php';
		}
		return $this->_routingFile;
	}
}
