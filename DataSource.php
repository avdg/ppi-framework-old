<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   DataSource
 * @link      www.ppiframework.com
 */
class PPI_DataSource {

	protected static $_sources = array();
	protected static $_connections = array();

	function __construct() {}

	function factory($key) {

		// Connection Caching
		if(isset(self::$_connections[$key])) {
			return self::$_connections[$key];
		}

		// Check that we asked for a valid key
		if(!isset(self::$_sources[$key])) {
			throw new PPI_Exception('Invalid DataSource Key: ' . $key);
		}

		// See if re recognise our data source's type
		$options = self::$_sources[$key];

		if(!isset($options['prefix'])) {
			$options['prefix'] = 'PPI_DataSource_';
		}

		if($options['type'] === 'mongo') {
			$suffix = 'Mongo';
		} elseif(substr($options['type'], 0, 4) === 'pdo_') {
			$suffix = 'PDO';
		} else {
			$suffix = $options['type'];
		}

		$adapterName = $options['prefix'] . $suffix;
		$adapter     = new $adapterName();
		$driver      = $adapter->getDriver($options);
		
		self::$_connections[$key] = $driver; // Connection Caching
		return $driver;

	}

	static function add($key, array $options) {
		self::$_sources[$key] = $options;
	}

}