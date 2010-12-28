<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Cache
 */
class PPI_Cache {

	private $_handler = null;	
   
	function __construct($handler = null, array $p_aOptions = array()) {
		if($handler !== null && $handler instanceof PPI_Cache_Interface) {
			$this->_handler = $handler;
		} else {
			$this->init();
		}
	}
	
	/**
	 * Initialise the cache handler
	 * 
	 * @throws PPI_Exception
	 *
	 */
	function init(array $p_aOptions = array()) {
		$oConfig = PPI_Helper::getConfig();
		if(!empty($oConfig->system->cacheHandler)) {
			switch($oConfig->system->cacheHandler) {
				case 'apc':
					if(!extension_loaded('apc')) {
						throw new PPI_Exception('Unable to use APC for caching. Extension not loaded');
					}
					$handler = 'PPI_Cache_Apc';
					break;

				case 'disk':
					$handler = 'PPI_Cache_Disk';
					break;

				case 'memcache':
				case 'memcached':  				
					$handler = 'PPI_Cache_Memcached';
					break;

				default:
					throw new PPI_Exception('Caching Handler Not Implemented');
					break;
			}
		} else {
			$handler = 'PPI_Cache_Disk';
		}
		$this->_handler = new $handler($p_aOptions);		
	}
	
    /**
     * Get a key value from the cache
     * @param string $p_sKey The Key
     * @return mixed
     */
    function get($p_sKey) {
    	if($this->_handler === null) {
    		$this->init();
    	}
    	return $this->_handler->get($p_sKey);
    }
    
    /**
     * Set a value in the cache
     * @param string $p_sKey The Key
     * @param mixed $p_mValue The Value
     * @return boolean
     */
    function set($p_sKey, $p_mValue) {
    	if($this->_handler === null) {
    		$this->init();
    	}    	
    	return $this->_handler->set($p_sKey, $p_mValue);
    }
    
    /**
     * Check if a key exists in the cache
     * @param string $p_sKey The Key
     * @return boolean
     */
    function exists($p_sKey) {
    	if($this->_handler === null) {
    		$this->init();
    	}    	
    	return $this->_handler->exists($p_sKey);
    }
    
    /**
     * Remove a value from the cache by key
     * @param string $p_sKey The Key
     * @return boolean
     */
    function remove($p_sKey) {
    	if($this->_handler === null) {
    		$this->init();
    	}    	
    	return $this->_handler->remove($p_sKey);
    }
	
}
