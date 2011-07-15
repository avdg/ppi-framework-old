<?php

/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Core
 * @link      www.ppiframework.com
 */
class PPI_Registry {

	/**
	 * Registry object provides storage for shared objects.
	 * @var object $_instance
	 */
	private static $_instance = null;

	/**
	 * The registrys internal data
	 *
	 * @var array
	 */
	private static $_vars = array();

	/**
	 * @param array $array Initial data for the registry
	 */
	public function __construct(array $data = array()) {
		if(!empty($data)) {
			self::$_vars = $data;
		}
	}

	/**
	 * Retrieves the default instance of the registry, if it doesn't exist then we create it.
	 *
	 * @return PPI_Registry
	 */
	public static function getInstance() {
		if (self::$_instance === null) {
			self::init();
		}
		return self::$_instance;
	}

	/**
	 * Set the default registry instance to a specified instance.
	 *
	 * @param object $registry An object instance of type PPI_Registry
	 * @return void
	 * @throws PPI_Exception if registry is already initialized.
	 */
	public static function setInstance($registry) {
		if (self::$_instance !== null) {
			throw new PPI_Exception('Registry is already initialized');
		}
		self::$_instance = $registry;
	}

	/**
	 * Initialize the default registry instance.
	 *
	 * @return void
	 */
	protected static function init() {
		self::setInstance(new PPI_Registry_Legacy());
	}

	/**
	 * getter method, basically same as offsetGet().
	 *
	 * This method can be called from an object of type PPI_Registry, or it
	 * can be called statically.  In the latter case, it uses the default
	 * static instance stored in the class.
	 *
	 * @param string $index - get the value associated with $index
	 * @todo Decide wether to default to null if the key is not found, instead of throwing an exception.
	 * @return mixed
	 * @throws PPI_Exception if no entry is registerd for $index.
	 */
	public static function get($key, $default = null) {
		return ifset(self::$_vars[$key], $default);
	}

	/**
	 * Set a value in the registry
	 *
	 * @param string $index
	 * @param mixed $value The object to store in the ArrayObject.
	 * @return void
	 */
	public static function set($index, $value) {
		self::$_vars[$index] = $value;
	}

	/**
	 * Removes an offset from the registry
	 *
	 * @param string $index
	 * @return void
	 */
	public static function remove($index) {
		unset(self::$_vars[$index]);
	}

	/**
	 * Checks if an offset exists in the registry
	 *
	 * @param string $index
	 * @return mixed
	 */
	public static function exists($index) {
		return array_key_exists($index, self::$_vars);
	}
}
