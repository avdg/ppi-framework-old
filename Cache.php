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

	private static $_handler = null;	
    
	/**
	 * Set the current handler class, overridable from config: system.cacheHandler
	 */
    function __construct() {
    	$oConfig = PPI_Helper::getConfig();
    	if(!empty($oConfig->system->cacheHandler)) {
    		switch($oConfig->system->cacheHandler) {
    			case 'apc':
    				$handler = 'PPI_Cache_Apc';
    				break;
    			
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
    	
    	self::$_handler = new $handler();
    	
    }
    
    /**
     * Get a key value from the cache
     * @param string $p_sKey The Key
     * @return mixed
     */
    static function get($p_sKey) {
    	return self::$_handler->get($p_sKey);
    }
    
    /**
     * Set a value in the cache
     * @param string $p_sKey The Key
     * @param mixed $p_mValue The Value
     * @return boolean
     */
    static function set($p_sKey, $p_mValue) {
    	return self::$_handler->set($p_sKey, $p_mValue);
    }
    
    /**
     * Check if a key exists in the cache
     * @param string $p_sKey The Key
     * @return boolean
     */
    static function exists($p_sKey) {
    	return self::$_handler->exists($p_sKey);
    }
    
    /**
     * Remove a value from the cache by key
     * @param string $p_sKey The Key
     * @return boolean
     */
    static function remove($p_sKey) {
    	return self::$_handler->remove($p_sKey);
    }
	
}