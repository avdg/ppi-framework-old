<?php
class PPI_DataSource {

	protected static $_sources = array();
	protected static $_connections = array();
	protected static $_driverTypeMap = array(
		'mysql'  => 'pdo',
		'sqlite' => 'pdo',
		'pgsql'  => 'pdo',
		'oci'    => 'pdo',
		'oci8'   => 'pdo',
		'db2'    => 'pdo',
		'ibm'    => 'pdo',
		'sqlsrv' => 'pdo',
		'mongo'  => 'mongo'
	);

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
		$driverOptions = self::$_sources[$key];
		if(!isset(self::$_driverTypeMap[$driverOptions['type']])) {
			throw new PPI_Exception('Invalid DataSource Type: ' . $driverOptions['type']);
		}

		// Our type of driver tells us where to fetch the driver from
		$driverType = self::$_driverTypeMap[$driverOptions['type']];

		switch($driverType) {

			case 'pdo':
				$suffix = 'PDO';
				break;

			case 'mongo':
				$suffix = 'Mongo';
		}

		$adapterName = 'PPI_DataSource_' . $suffix;
		$adapter = new $adapterName();
		$driver = $adapter->getDriver($driverOptions);
		self::$_connections[$key] = $driver; // Connection Caching
		return $driver;

	}

	static function add($key, array $options) {
		self::$_sources[$key] = $options;
	}

}