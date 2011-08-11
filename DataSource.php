<?php
use Doctrine\Common\ClassLoader;
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

		if(isset(self::$_connections[$key])) {
			return self::$_connections[$key];
		}

		if(!isset(self::$_sources[$key])) {
			throw new PPI_Exception('Invalid DataSource Key: ' . $key);
		}

		$driverOptions = self::$_sources[$key];
		if(!isset(self::$_driverTypeMap[$driverOptions['type']])) {
			throw new PPI_Exception('Invalid DataSource Type: ' . $driverOptions['type']);
		}

		$driverType = self::$_driverTypeMap[$driverOptions['type']];
		switch($driverType) {

			case 'pdo':
				$conn = $this->getPDO($driverOptions);
				break;

			case 'mongo':
				$conn = $this->getMongo($driverOptions);
		}

		self::$_connections[$key] = $conn; // Connection Caching
		return $conn;

	}

	function getPDO(array $config = array()) {

		require VENDORPATH . 'Doctrine/Doctrine/Common/ClassLoader.php';
		$classLoader = new ClassLoader('Doctrine', VENDORPATH . 'Doctrine');
		$classLoader->register();
		$connObject = new \Doctrine\DBAL\Configuration();

		// We map our config options to Doctrine's naming of them
		$connParamsMap = array(
			'database' => 'dbname',
			'username' => 'user',
			'hostname' => 'host'
		);

		foreach($connParamsMap as $key => $param) {
			if(isset($config[$key])) {
				$config[$param] = $config[$key];
				unset($config[$key]);
			}
		}

		$driverMap = array(
			'mysql'  => 'pdo_mysql',
			'sqlite' => 'pdo_sqlite',
			'pgsql'  => 'pdo_pgsql',
			'oci'    => 'pdo_oci',
			'oci8'   => 'oci8',
			'db2'    => 'ibm_db2',
			'ibm'    => 'pdo_ibm',
			'sqlsrv' => 'pdo_sqlsrv'
		);

		$config['driver'] = $driverMap[$config['type']];
		return \Doctrine\DBAL\DriverManager::getConnection($config, $connObject);
	}

	function getMongo() {
		// TBC..
	}

	static function add($key, array $options) {
		self::$_sources[$key] = $options;
	}

}