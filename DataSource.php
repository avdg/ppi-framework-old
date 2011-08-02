<?php
use Doctrine\Common\ClassLoader;
class PPI_DataSource {

	protected static $_sources = array();
	protected static $_connections = array();

	function __construct() {}

	function get($dataSourceKey) {

		if(isset(self::$_connections[$dataSourceKey])) {
			return self::$_connections[$dataSourceKey];
		}

		// @todo perform an isset() here
		$driverOptions = self::$_sources[$dataSourceKey];
		switch($driverOptions['type']) {

			case 'mysql':
			case 'sqlite':
			case 'pgsql':
				$conn = $this->getPDO($driverOptions);
				break;

			case 'mongo':
				$conn = $this->getMongo($driverOptions);
		}

		self::$_connections[$dataSourceKey] = $conn;
		return $conn;

	}

	function getPDO(array $config = array()) {

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

		$connParamsMap = array(
			'database' => 'dbname',
			'username' => 'user',
			'hostname' => 'host'
		);

		require VENDORPATH . 'Doctrine/Doctrine/Common/ClassLoader.php';
		$classLoader = new ClassLoader('Doctrine', VENDORPATH . 'Doctrine');
		$classLoader->register();
		$connObject = new \Doctrine\DBAL\Configuration();

		foreach($connParamsMap as $key => $param) {
			if(isset($config[$key])) {
				$config[$param] = $config[$key];
				unset($config[$key]);
			}
		}

		// @todo perform an isset() here
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