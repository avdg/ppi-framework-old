<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Cache
 */

class PPI_Cache_Disk implements PPI_Cache_Interface {

	protected $_cacheDir = null;

	protected $_options = array();

	function __construct(array $p_aOptions = array()) {
	    $this->_options = $p_aOptions;
	    $this->_cacheDir = isset($p_aOptions['cache_dir']) ? $p_aOptions['cache_dir'] : APPFOLDER . 'Cache/Disk/';
	}

	/**
	 * Get a value from cache
	 * @param string $p_sKey The Key
	 * @return mixed
	 */
	function get($p_sKey) {
            $path = $this->getKeyCacheDir($p_sKey);
            list($splitPos, $headerData) = $this->getHeaderData($path);
	    // If we have a positive TTL (must expire some time) check its expire time and do a cleanup if expired
	    if($headerData['ttl'] > 0 && $headerData['expire_time'] < time()) {
	        unlink($path);
	        return null;
	    }
	    $sContent = file_get_contents($path, true, null, $splitPos + 2, filesize($path));
	    return $sContent != '' ? unserialize($sContent) : null;
	}

	/**
	 * Set a value in the cache
	 * @param string $p_sKey The Key
	 * @param mixed $p_mData The Data
	 * @param integer $p_iTTL The Time To Live
	 * @return boolean
	 */
	function set($p_sKey, $p_mData, $p_iTTL = 0) {
            $path = $this->getKeyCacheDir($p_sKey);
	    if(file_exists($path)) {
	        unlink($path);
	    }
	    if(is_writeable(dirname($path)) === false) {
		throw new PPI_Exception('Unable to create cache file: ' . $p_sKey);
	    }
            $header = 'expire_time:' . (int) (time() + $p_iTTL) . '|ttl:' . $p_iTTL . "\r\n";
	    return file_put_contents($path, $header . serialize($p_mData), LOCK_EX) > 0;
	}

	/**
	 * Check if a key exists in the cache
	 * @param string $p_mKey The Key(s)
	 * @return boolean
	 */
	function exists($p_mKey) {
		if(is_string($p_mKey)) {
			$path = $this->getKeyCacheDir($p_mKey);
			if(is_readable($path) === false) {
				return false;
			}
			list(, $headerData) = $this->getHeaderData($path);
			// If ttl is 0, then it's infinitely existing
			// otherwise it is > 0 and the expire time must be in the future from time()
			return $headerData['ttl'] == 0 || $headerData['expire_time'] >= time();
		}
		if(is_array($p_mKey)) {
			$exists = false;
			foreach($p_mKey as $file) {
				// Recursion to itself passing in a string.
				if($this->exists($file) === false) {
					return false;
				}
				$exists = true;
			}
			return $exists;
		}
		throw new PPI_Exception('Invalid input type; expecting string or array');
	}

	/**
	 * Remove a key from the cache
	 * @param string $p_sKey The Key
	 * @return boolean
	 */
	function remove($p_sKey) {
		$path = $this->getKeyCacheDir($p_sKey);
		return file_exists($path) && unlink($path);
	}
	
	/**
	 * Get the header data from a cache item
	 * @param string $p_sPath The Path
	 * @throws PPI_Exception
	 * @return array
	 */
	protected function getHeaderData($p_sPath) {
            if( ($fd = fopen($p_sPath, 'r')) === false) {
                 throw new PPI_Exception('Unable to open: ' . $p_sPath);
            }
	    // lock, read, unlock, free
	    flock($fd, LOCK_SH);
            $data = fread($fd, 40);
	    flock($fd, LOCK_UN);
	    fclose($fd);

            if( ($splitPos = strpos($data, "\r\n")) !== false) {
                list($headerData,) = explode("\r\n", $data);
                return array($splitPos, $this->prepareHeaderData($headerData));
	    }
	    throw new PPI_Exception('Unable to locate header information for: ' . $p_sPath);
	}

        /**
         * Convert the header data into an array
         * @param string $headerData
         * @return array
         */
        protected function prepareHeaderData($headerData) {
            $headers = array();
            foreach(explode('|', $headerData) as $header) {
                list($key, $val) = explode(':', $header, 2);
                $headers[$key] = $val;
            }
            return $headers;
        }

	/**
	 * Get the cache dir
	 * @return string
	 */
	protected function getBaseCacheDir() {
		return $this->_cacheDir;
	}

	/**
	 * Get the full path to a cache item
	 * @return string
         */ 
	protected function getKeyCacheDir($p_sKey) {
		return $this->_cacheDir . 'default--' . $p_sKey;
	}

}
