<?php
class PPI_Cache {
	
	private static $_instance = null;
	private static $_handler = null;
    /**
     * Retrieves the default instance of the cache object, if it doesn't exist then we create it.
     *
     * @return PPI_Cache
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::init();
        }
        return self::$_instance;
    }

    /**
     * Set the default PPI_Cache instance to a specified instance.
     *
     * @param PPI_Cache $input An object instance of type PPI_Cache
     * @return void
     * @throws PPI_Exception if PPI_Cache is already initialized.
     */
    public static function setInstance(PPI_Cache $input) {
        if (self::$_instance !== null) {
            throw new PPI_Exception('PPI_Cache is already initialized');
        }
        self::$_instance = $input;
    }

    /**
     * Initialize the default PPI_Cache instance.
     *
     * @return void
     */
    protected static function init() {
        self::setInstance(new PPI_Cache());
    }	
    
    function __construct() {
    	$oConfig = PPI_Helper::getConfig();
    	if(!empty($oConfig->system->cacheHandler)) {
    		switch($oConfig->system->cacheHandler) {
    			case 'apc':
    				self::$_handler = 'PPI_Cache_Apc';
    				break;
    			
    			case 'memcached':
    				self::$_handler = 'PPI_Cache_Memcached';
    				break;
    				
    			default:
    				throw new PPI_Exception('Caching Handler Not Implemented');
    				break;
    				
    		}
    	} else {
    		self::$_handler = 'PPI_Cache_Disk';
    	}
    	
    }
    
    static function get($p_sKey) {
    	return self::$_handler::get($p_sKey);
    }
    
    static function set($p_sKey, $p_mValue) {
    	return self::$_handler::set($p_sKey, $p_mValue);
    }
    
    static function exists() {
    	return self::$_handler::exists($p_sKey);
    }
    
    static function remove() {
    	return self::$_handler::remove($p_sKey);
    }
	
}