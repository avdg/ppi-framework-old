<?php
use Doctrine\Common\ClassLoader;
class PPI_DataSource_PDO {

	function __construct() {

	}

	function getDriver(array $config) {

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

		$config['driver'] = $config['type'];
		unset($config['type']);
		return \Doctrine\DBAL\DriverManager::getConnection($config, $connObject);

	}

}