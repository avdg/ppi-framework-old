<?php

/**
 * This is the PPI Appliations Configuration class which is used in the Bootstrap
 *
 * @category  PPI
 * @package   PPI_App
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @copyright 2001-2010 Digiflex Development Team
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version   PHP 5.1.0
 * @link      www.ppiframework.com
*/
class PPI_App {
    
    protected $_errorLevel = E_ALL;
    protected $_showErrors = 'On';
    protected $_configBlock = 'development';
    protected $_siteMode = 'development';
    protected $_config = null;
    protected $_dispatcher = null;
    protected $_router = null;
    protected $_session = null;
    
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
     */
    function setEnv(array $p_aOptions) {
        if(isset($p_aOptions['config'])) {
            $this->_config = $p_aOptions['config'];
            unset($p_aOptions['config']);
        }
        // The sites "mode" this is production or development.
        // This is so PPI knows how to handle things like errors and exceptions without implicitly telling it what to do.
        if(isset($p_aOptions['siteMode'])) {
            $this->_siteMode = ($p_aOptions['siteMode'] !== 'development' && $p_aOptions['siteMode'] !== 'production') 
                ? 'development' 
                : $p_aOptions['siteMode'];
            unset($p_aOptions['siteMode']);
        }
        // Any further options passed, it maps; 'errorLevel' to $this->_errorLevel    
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
     */
    function setRouter(PPI_Router_Interface $p_oRouter) {
        $this->_router = $p_oRouter;
    }
    
    /**
     * Set the dispatch object for the app bootup
     *
     * @param PPI_Dispatch_Interface $p_oDispatch The dispatch object
     */
    function setDispatcher(PPI_Dispatch_Interface $p_oDispatch) {
        $this->_dispatcher = $p_oDispatch;
    }
    
    /**
     * Set the session object for the app bootup
     *
     * @param PPI_Session_Interface $p_oSession The session object
     */
    function setSession(PPI_Session_Interface $p_oSession) {
        $this->_session = $p_oSession;
    }
    
    /**
     * Run the boot process, boot up our app. Call the relevant classes such as config, registry, session, dispatch, router and so on
     *
     * @return $this Fluent interface
     */
    function boot() {
        
        error_reporting($this->_errorLevel);
        ini_set('display_errors', $this->_showErrors);
        
        // Fire up the default config handler
        if($this->_config === null) {
            $this->_config = new PPI_Config('general.ini');
        }
        
        // Fire up the default session handler
        if($this->_session === null) {
            
        }
        
        $this->_config = $this->_config->getConfig();
        // Set the config into the registry
        PPI_Registry::getInstance()->set('PPI_Config', $this->_config);

        // ------------- Initialise the session -----------------
        if(!headers_sent()) {
            if(!isset($this->_config->system->sessionNamespace)) {
                die('Required config value not found. system.sessionNamespace');
            } else {
                session_name($this->_config->system->sessionNamespace);
            }
            session_start();
            PPI_Registry::getInstance()->set('PPI_Session', new PPI_Session());
        }
        
        PPI_Registry::getInstance()->set('PPI_App', $this);
        
        return $this;
    }
    
    /**
     * Call the dispatch process. Running the dispatcher and dispatching
     *
     * @return $this Fluent interface
     */
    function dispatch() {
        // Fire up the default dispatcher
        if($this->_dispatcher === null) {
            $this->_dispatcher = new PPI_Dispatch_Standard(array('router' => $this->_router));
        }

        $dispatch = new PPI_Dispatch($this->_dispatcher);
                        
        PPI_Registry::getInstance()->set('PPI_Dispatch', $dispatch);

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