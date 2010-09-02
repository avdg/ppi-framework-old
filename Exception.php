<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */

class PPI_Exception extends Exception {


	public $_traceString = '';
	public $_line = '';
	public $_code = '';
	public $_message = '';
	public $_file = '';
	public $_traceArray = array();
	public $_queries = array();
    private static $_instance = null;

    /**
     * Initialize the default registry instance.

     * @return void
     */
    protected static function init() {
        self::setInstance(new PPI_Exception());
    }

    /**
     * Set the default registry instance to a specified instance.
     */
    public static function setInstance(PPI_Exception $instance) {
        self::$_instance = $instance;
    }
    /**
     * Retrieves the default exception instance.
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::init();
        }
        return self::$_instance;
    }

	function __construct($message = '', $sqlQueries = null) {
		parent::__construct($message);
		$this->_traceString = $this->getTraceAsString();
		$this->_traceArray	= $this->getTrace();
		$this->_line 		= $this->getLine();
		$this->_code 		= $this->getCode();
		$this->_message 	= $this->getMessage();
		$this->_file 		= $this->getFile();
		$this->_queries 	= PPI_Helper::getRegistry()->get('PPI_Model_Queries', array());
	}

	/**
     * This function shows a 403 error
     *
     * @access	public
     * @return	void
     */
	function show_403($p_sMessage = "") {
		$heading = "403 Forbidden";
		//header("HTTP/1.1 403 Forbidden");
		$message = (!empty ($p_sMessage) ) ? $p_sMessage : "You are not allowed to access the requested location";
		require SYSTEMPATH.'errors/403.php';
	}


	/**
    * This function shows a 404 error
    *
    * @access	public
    * @todo change this to a new function name
    * @todo make this do 404 on the HTTP status line
    * @param        string argument name
    * @return	void
    */
	static function show_404($p_sLocation = "", $p_bUseImage = false) {
		$oConfig = PPI_Helper::getConfig();
		$heading = "404 Page Not Found";
		$message = (!empty ($p_sLocation) ) ? $p_sLocation : "The page you requested was not found.";
		//header("HTTP/1.1 404 Not Found");
		require SYSTEMPATH.'View/404.php';
        exit;
	}

	/**
	 * PPI_Exception::show_exceptioned_error()
	 * Show this exception
	 * @param string $p_aError Error information from the custom error log
	 * @return void
	 */
	function show_exceptioned_error($p_aError = "") {
		global $siteTypes;
        $oConfig = PPI_Helper::getConfig();

		$p_aError['sql'] = $this->_queries;
		$sHostName = getHTTPHostname();
		if($siteTypes[$sHostName] == 'development') {
			$heading = "PHP Exception";
			$baseUrl = $oConfig->system->base_url;
			require SYSTEMPATH.'View/code_error.php';
			echo $header.$html.$footer;
		} else {
			$oView = new PPI_View();
			$oView->load('error', array('message' => $p_aError['message']));
		}
		exit;
	}

	/**
	 * Show an error message
	 *
	 * @param string $p_sMsg
	 */
	function show_error($p_sMsg) {
        $oConfig = PPI_Helper::getConfig();
		$heading = "PHP Error";
		$baseUrl = $oConfig->system->base_url;
		require SYSTEMPATH.'View/code_error.php';
		echo $header.$html.$footer;
	}

	/**
	 * Same as show error but return the content as a param
	 *
	 * @param array $p_aError
	 * @return string The HTML contents
	 */
	function getErrorForEmail($p_aError = "") {
		$oConfig = PPI_Helper::getConfig();
		$heading = "PHP Error";
		$baseUrl = $oConfig->system->base_url;
		require SYSTEMPATH.'View/code_error.php';
		return $html;
	}
}
