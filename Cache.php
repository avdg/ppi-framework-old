<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Cache
 * @link      www.ppiframework.com
 */
class PPI_Cache {

    protected $_defaults = array(
        'handler'  => 'disk'
    );

    /**
     * The handler in use
     *
     * @var null|PPI_Cache_Interface
     */
    protected $_handler = null;

	/**
	 * The options to the cache layer. This can be an array of options
	 * or a string of the driver name eg: new PPI_Cache('apc');
	 *
	 * @param array|string $p_aOptions
	 */
	function __construct($p_aOptions = array()) {

		// We now let you specify the handler as a string for quickness.
		if(is_string($p_aOptions)) {
			$p_aOptions = array('handler' => $p_aOptions);
		}

        if(isset($p_aOptions['handler'])) {

            // If it's a pre instantiated cache handler then use that
            if(!is_string($p_aOptions['handler']) && $p_aOptions['handler'] instanceof PPI_Cache_Interface) {
                $this->_handler = $p_aOptions['handler'];
                unset($p_aOptions['handler']);
            }
        }

        $this->_defaults = ($p_aOptions + $this->_defaults);

        // If no handler was passed in, then we setup that handler now by the string name: i.e: 'disk'
        if($this->_handler === null) {
            $this->setupHandler($this->_defaults['handler']);
        }
	}

	/**
	 * Initialise the cache handler
     *
	 * @param array $p_aOptions The options to go into the cache initialisation
	 * @return void
	 * @throws PPI_Exception
	 *
	 */
	function setupHandler($p_sHandler) {
		$p_sHandler = strtolower($p_sHandler);
        $handler = 'PPI_Cache_' . ucfirst($p_sHandler);
		$this->_handler = new $handler($this->_defaults);
        if($this->_handler->enabled() === false) {
            throw new PPI_Exception('The cache driver ' . $handler . ' is currently disabled.');
        }
	}

    /**
     * Get a key value from the cache
     *
     * @param string $p_sKey The Key
     * @return mixed
     */
    function get($p_sKey) {
    	return $this->_handler->get($p_sKey);
    }

    /**
     * Set a value in the cache
     *
     * @param string $p_sKey The Key
     * @param mixed $p_mValue The Value
     * @return boolean
     */
    function set($p_sKey, $p_mValue) {
    	return $this->_handler->set($p_sKey, $p_mValue);
    }

    /**
     * Check if a key exists in the cache
     *
     * @param string $p_sKey The Key
     * @return boolean
     */
    function exists($p_sKey) {
    	return $this->_handler->exists($p_sKey);
    }

    /**
     * Remove a value from the cache by key
     *
     * @param string $p_sKey The Key
     * @return boolean
     */
    function remove($p_sKey) {
    	return $this->_handler->remove($p_sKey);
    }

}
