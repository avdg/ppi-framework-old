<?php
/**
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Core
 * @link      www.ppiframework.com
 */
class PPI_Registry extends ArrayObject  {
    /**
     * Registry object provides storage for shared objects.
     * @var object $_instance
     */
    private static $_instance = null;

    /**
     * Constructs a parent ArrayObject with default to allow acces as an object
     * @param array $array Initial data for the parent
     * @param integer $flags ArrayObject Initial flags for ArrayObject
     */
    public function __construct($array = array(), $flags = parent::ARRAY_AS_PROPS) {
        parent::__construct($array, $flags);
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
     * @param PPI_Registry $registry An object instance of type PPI_Registry
     * @return void
     * @throws PPI_Exception if registry is already initialized.
     */
    public static function setInstance(PPI_Registry $registry) {
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
        self::setInstance(new PPI_Registry());
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
    public static function get($p_sKey, $p_mDefault = null) {
        $instance = self::getInstance();
        $bExists  = $instance->offsetExists($p_sKey);
        if ($p_mDefault === null && !$bExists) {
            throw new PPI_Exception("No entry is registered for key '$p_sKey'");
        }
        return $bExists ? $instance->offsetGet($p_sKey) : $p_mDefault;
    }

    /**
     * setter method, basically same as offsetSet().
     *
     * This method can be called from an object of type PPI_Registry, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index The location in the ArrayObject in which to store
     *   the value.
     * @param mixed $value The object to store in the ArrayObject.
     * @return void
     */
    public static function set($index, $value) {
        self::$_instance->offsetSet($index, $value);
    }

    /**
     * Removes an offset from the registry
     *
     * @param string $index
     * @return void
     */
    public static function remove($index) {
    	self::$_instance->offsetUnset($index);
    }

    /**
     * Checks if an offset exists in the registry
     *
     * @param string $index
     * @return mixed
     */
    public static function exists($index) {
		return self::$_instance->offsetExists($index);
    }

    /**
     * @param string $index
     * @return mixed
     *
     * Workaround for http://bugs.php.net/bug.php?id=40442
     */
    public function offsetExists($index) {
        return array_key_exists($index, $this);
    }

}
