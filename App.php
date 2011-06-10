<?php
/**
  * This is the PPI Appliations Configuration class which is used in the Bootstrap
 */

/**
 *
 * @package   Core
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @copyright 2001-2010 Digiflex Development Team
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @link      www.ppiframework.com
*/
class PPI_App {

    /**
     * The error level to throw via error_reporting()
     *
     * @var int
     */
    protected $_errorLevel = E_ALL;

    /**
     * Whether to display errors or not. This gets fired into ini_set('display_errors')
     *
     * @var string
     */
    protected $_showErrors = 'On';

    /**
     * The block in the config file to get the config data from
     *
     * @var string
     */
    protected $_configBlock = 'development';

    /**
     * The site mode. Default is 'development'. This determines how PPI handles exceptions
     *
     * @var string
     */
    protected $_siteMode = 'development';

    /**
     * The config object
     *
     * @var null|PPI_Config
     */
    protected $_config = null;

    /**
     * The PPI_Dispatch object
     *
     * @var null|PPI_Dispatch
     */
    protected $_dispatcher = null;

    /**
     * The PPI_Router object
     *
     * @var null|PPI_Router
     */
    protected $_router = null;

    /**
     * The PPI_Session object
     *
     * @var null|PPI_Session
     */
    protected $_session = null;


	/**
	 *
	 *
	 * @var null
	 */
	protected $_request = null;

	/**
	 * @param array $p_aParams
	 */

    function __construct($p_aParams = array()) {
        if(!empty($p_aParams)) {
			foreach ($p_aParams as $key => $value) {
				if (method_exists($this, ($sMethod = 'set' . ucfirst($key)))) {
					$this->$sMethod($value);
				}
			}
        }
    }

    /**
     * Setter for the environment, passing in options determining how the app will behave
     *
     * @param array $p_aOptions The options
     * @return void
     */
    function setEnv(array $p_aOptions) {
        if(isset($p_aOptions['config'])) {
            $this->_config = $p_aOptions['config'];
            unset($p_aOptions['config']);
        }
        // The sites "mode" this is production or development.
        // This is so PPI knows how to handle things like errors and exceptions without implicitly telling it what to do.
        if(isset($p_aOptions['siteMode'])) {
            if(in_array($p_aOptions['siteMode'], array('development', 'production'))) {
            	$this->_siteMode = $p_aOptions['siteMode'];
            }
            unset($p_aOptions['siteMode']);
        }
        // Any further options passed, eg: it maps; 'errorLevel' to $this->_errorLevel
        foreach($p_aOptions as $option) {
            $option = '_' . $option;
            if(isset($this->$option)) {
                $this->$option = $option;
            }
        }
    }

    /**
     * Set the router object for the app bootup
     *
     * @param PPI_Router_Interface $p_oRouter The router object
     * @return void
     */
    function setRouter(PPI_Router_Interface $p_oRouter) {
        $this->_router = $p_oRouter;
    }

    /**
     * Set the dispatch object for the app bootup
     *
     * @param PPI_Dispatch_Interface $p_oDispatch The dispatch object
     * @return void
     */
    function setDispatcher(PPI_Dispatch_Interface $p_oDispatch) {
        $this->_dispatcher = $p_oDispatch;
    }

		/**
	 * Set the request object for the app bootup
	 *
	 * @param object $p_oRequest
	 * @return void
	 */
	function setRequest($p_oRequest) {
		$this->_request = $p_oRequest;
	}

    /**
     * Set the session object for the app bootup
     *
     * @param PPI_Session_Interface $p_oSession The session object
     * @return void
     */
    function setSession(PPI_Session_Interface $p_oSession) {
        $this->_session = $p_oSession;
    }

    /**
     * Run the boot process, boot up our app. Call the relevant classes such as:
     * config, registry, session, dispatch, router.
     *
     * @return $this Fluent interface
     */
    function boot() {

        error_reporting($this->_errorLevel);
        ini_set('display_errors', $this->_showErrors);

        // Fire up the default config handler
        if($this->_config === null) {
            $this->_config = new PPI_Config('general.ini', array('block' => $this->_siteMode));
        }
        $this->_config = $this->_config->getConfig();

        $registry = PPI_Registry::getInstance();

	    // -- Set the config into the registry for quick read/write --
        $registry->set('PPI_Config', $this->_config);

        // ------------- Initialise the session -----------------
        if(!headers_sent()) {
	        // Fire up the default session handler
	        if($this->_session === null) {
	            $this->_session = new PPI_Session();
	        }
            $registry->set('PPI_Session', $this->_session);
        }

        // Fire up the default dispatcher
        if($this->_dispatcher === null) {
            $this->_dispatcher = new PPI_Dispatch_Standard(array('router' => $this->_router));
        }
        $dispatch = new PPI_Dispatch($this->_dispatcher);
        PPI_Registry::getInstance()->set('PPI_Dispatch', $dispatch);

	    // -- Set the PPI_Request object --
	    if($this->_request === null) {
		    $this->_request = new PPI_Request();
	    }
	    $registry->set('PPI_Request', $this->_request);
        // -------------- Library Autoloading Process --------------
        if(!empty($this->_config->system->autoloadLibs)) {
            foreach(explode(',', $this->_config->system->autoloadLibs) as $sLib) {
                switch(strtolower(trim($sLib))) {
                    case 'zf':
                        PPI_Autoload::add('Zend', array(
                            'path'   => SYSTEMPATH . 'Vendor/',
                            'prefix' => 'Zend_'
                        ));
                        break;

	                case 'github':
		                $githubAutoloader = SYSTEMPATH . 'Vendor/Github/Autoloader.php';
		                if(!file_exists($githubAutoloader)) {
			                throw new PPI_Exception('Unable to autoload github, the github autoloader was no found.');
		                }
						include_once(SYSTEMPATH . 'Vendor/Github/Autoloader.php');
			            Github_Autoloader::register();
		                break;

	                // @todo - test this.
	                case 'swift':
		                include_once(SYSTEMPATH . 'Vendor/Swift/swift_required_pear.php');
		                break;
/*
                    case 'solar':
                        PPI_Autoload::add('Solar', array(
                            'path'   => SYSTEMPATH . 'Vendor/',
                            'prefix' => 'Solar_'
                        ));
                        Solar::start();
                        break;
 */
                }

            }
        }

        $registry->set('PPI_App', $this);

        return $this; // Fluent Interface
    }

    /**
     * Call the dispatch process. Running the dispatcher and dispatching
     *
     * @return $this Fluent interface
     */
    function dispatch() {
	    
		$dispatch = PPI_Registry::getInstance()->get('PPI_Dispatch');
        $dispatch->dispatch();
        return $this;
    }

    /**
     * Get the current site mode set, such as 'development' or 'production'
     *
     * @return string
     */
    function getSiteMode() {
        return $this->_siteMode;
    }

}