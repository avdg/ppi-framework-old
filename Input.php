<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Core
 * @link      www.ppiframework.com/docs/input.html
 */
class PPI_Input {

	protected $_aArguments;

	function __construct() {
		$this->_aArguments = explode('/', $_SERVER['REQUEST_URI']);
	}

	/**
	 * Check if something is a valid email
	 * @todo make this reference filter_var()
	 * @param string $p_sEmail
	 * @return boolean
	 */
	function is_validemail($p_sEmail = '') {
		return preg_match("/^[_A-Za-z0-9.-]+[^.]@[^.][A-Za-z0-9.-]{2,}[.][a-z]{2,4}$/", $p_sEmail);
	}

    /**
     * Set the flash message(s) in the session
     *
     * @todo move to PPI_Response
     * @param string $p_sMessage
     * @param bool $p_bSuccess
     * @return void
     */
    static function setFlashMessage($p_sMessage, $p_bSuccess = true) {
        PPI_Helper::getSession()->set('ppi_flash_message', array(
            'mode'    => $p_bSuccess ? 'success' : 'failure',
            'message' => $p_sMessage
        ));
    }

    /**
     * Get the flash message(s)
     *
     * @todo move to PPI_Response
     * @return array|null If not set, it's null.
     */
    static function getFlashMessage() {
        return PPI_Helper::getSession()->get('ppi_flash_message');
    }

    /**
     * PPI_Input::clearFlashMessage()
     * Wipe the flash message from the session
     * @todo move to PPI_Response
     *
     * @return void
     */
    static function clearFlashMessage() {
        PPI_Helper::getSession()->remove('ppi_flash_message');
    }

}
