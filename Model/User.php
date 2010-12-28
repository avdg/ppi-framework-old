<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */

class PPI_Model_User extends APP_Model_Application {
	public $_encryptionAlgorithm = 'sha1';
	// @todo - Make sure this is default but config overridden
	private $_usernameField	=	'email';
	// @todo - Make sure this is default but config overridden
	private $_passwordField = 'password';
	private $_iTableName;
	private $_iTableIndex;

	function __construct($p_iTableName = "", $p_iTableIndex = "", $p_sBdbInfo = "", $p_iUserID = 0) {
		global $oConfig;
		$this->_iTableName 	= $p_iTableName;
		$this->_iTableIndex = $p_iTableIndex;

		parent::__construct($p_iTableName, $p_iTableIndex, $p_sBdbInfo, $p_iUserID);
		// encryption algorithm override
		if(isset($oConfig->system->encryptionAlgorithm)) {
			$this->_encryptionAlgorithm = $oConfig->system->encryptionAlgorith;
		}
	}

	// ----------------------- User Register ---------------------
	function putRecord(array $aData) {
		$oConfig = PPI_Helper::getConfig();
		// If its an insert
		if(!array_key_exists($this->_iTableIndex, $aData)) {
			$plainPass = $aData['password'];
			if(!array_key_exists($oConfig->system->usernameField, $aData)) {
				throw new PPI_Exception('Unable to locate username field when creating user');
			}
			$aData['active']            = isset($oConfig->system->defaultUserActive) && $oConfig->system->defaultUserActive != false ? 1 : 0;
			$aData['created']           = time();
			$aData['activation_code'] 	= base64_encode(time());
			$aData['password'] 			= $this->encryptNewPassword($aData['password']);

			// If no role_id has been set, lets set it from the config.
			if(!isset($aData['role_id'])) {
				if(!isset($oConfig->system->defaultUserRole)) {
					throw new PPI_Exception('Missing config value system.defaultUserRole');
				}
				$aData['role_id'] = PPI_Model_Helper::getRoleIDFromName($oConfig->system->defaultUserRole);
			}
		} else {
			//if password is being passed in for re-set, we need to encrypt it
			if(isset($aData['password'])) {
				$aData['password'] 	= $this->encryptAndSaltPassword($aData[$oConfig->system->usernameField], $aData['password']);
				unset($aData[$oConfig->system->usernameField]);
			}
		}
		return parent::putRecord($aData);
		// set the system log here

		// send acitvation email here
		//$oEmail = new PPI_Model_Email();
		/*$oEmail->setTemplate('User Registration', array(
			'site_name' => $oConfig->site_name,
			'username' 	=> $aData[$oConfig->usernameField],
			'password' 	=> $plainPass
		))->sendMail();*/
	}

	function activate($iUserID, $sActivationCode) {

	}

	/**
	 * Update a users password
	 * @param integer $p_iUserID The User ID we wish to update
	 * @param string $p_sPassword The plaintext password to update
	 * @return boolean
	 */
	function updatePassword($p_iUserID, $p_sPassword) {

		// If we were able to get user details
		if(count($aUser = $this->find($p_iUserID)) > 0) {
			// Using the username field from the config.
			$usernameField = $this->getConfig()->system->usernameField;

			// We update the users record, passing in the encrypted form of their new password.
			$this->putRecord(array(
				$this->getPrimaryKey() => $p_iUserID,
				'password'             => $this->encryptAndSaltPassword($aUser[$usernameField], $p_sPassword)
			));
			return true;
		}

		return false;
	}

	// ----------------- Authentication Functions -----------------
	/**
	 * Login function for the user, passing the username and password.
	 * @param string $username The username (the value of the usernameField from the config)
	 * @param string $password The plaintext password
	 * @return boolean
	 */
	function login($username, $password) {
		$user = $this->fetch($this->getConfig()->system->usernameField . ' = ' . $this->quote($username));
		if(!empty($user)) {
			if($this->encryptPassword(substr($user['password'], 0, 12), $password) === $user['password']) {
				if(array_key_exists('password', $user)) {
					unset($user['password']);
				}
				$user['role_name'] = PPI_Helper_User::getRoleNameFromID($user['role_id']);
				$user['role_name_nice'] = PPI_Helper_User::getRoleNameNice($user['role_name']);
				$this->getSession()->setAuthData($user);
			return true;
			}
		}
		return false;
	}

	/**
	 * Log the user out. Wipe all the session data
	 */
	function logout() {
		$this->getSession()->removeAll();
	}

	/**
	 * Verify if the user is logged in or not
	 * @return boolean
	 */
	function isLoggedIn() {
		return (count($this->getSession()->getAuthData()) > 0);
	}

	function getRoleGroups() {
		global $oConfig;
		if(is_array($oConfig->system->roleMapping)) {
			return $oConfig->system->roleMapping;
		} else {
			return (array) $oConfig->system->roleMapping;
		}
	}

	function getRoleType($p_iRoleID) {
		$aGroups = $this->getRoleGroups();
		foreach ($aGroups as $key => $val) {
			if ($val == $p_iRoleID) {
				return $key;
			}
		}
		return 'unknown';
	}


	function getRoleID($p_sRoleType) {
		$aGroups = $this->getRoleGroups();
		if(in_array($p_sRoleType, $aGroups)) {
			foreach($aGroups as $key => $val) {
				if($aGroups[$key] == $p_sRoleType) {
					return $key;
				}
			}
		}
		return false;
	}

	function getID($where) {
		$select = $this->select()
			->from('users')
			->where($where);
		$rows = $select->getList($select)->fetchAll();
		return (count($rows) > 0) ? $rows[0][$this->_iTableIndex] : 0;
	}

	/**
	 * Generate a new password
	 *
	 * @param string $password The plaintext password
	 * @return string
	 */
	function encryptNewPassword($password) {
		$oReg = $this->getRegistry();
		if(!$oReg->exists('PPI_Model_User::hash_algos')) {
			$algos = hash_algos();
			$oReg->set('PPI_Model_User::hash_algos', $algos);
		}
		if(!isset($algos)) {
			$algos = $oReg->get('PPI_Model_User::hash_algos');
		}
		$algo = $this->_encryptionAlgorithm;
		if(!in_array($algo, $algos)) {
			throw new PPI_Exception('Unable to use algorithm: ' . $algo . 'not supported in list of: ' . implode(', ', $algos));
		}
		$salt = substr(hash($algo, uniqid(rand(), true)), 0, 12);
		return $this->encryptPassword($salt, $password);
	}

	/**
	 * Encrypt + salt the users password.
	 * @param string $salt The salt
	 * @param string $password The plaintext password
	 */
	function encryptPassword($salt, $password) {
		return $salt . hash($this->_encryptionAlgorithm, $salt . $password);
	}

	/**
	 * Verify the username against the password
	 *
	 * @param string $username The username
	 * @param string $password The password
	 * @return boolean
	 */
	function checkPassword($username, $password) {
		$user = $this->fetch($this->getConfig()->system->usernameField . ' = ' . $this->quote($username));
		if(!empty($user)) {
			return ($this->encryptPassword(substr($user['password'], 0, 12), $password) === $user['password']);
		}
		return false;
	}

	/**
	 * Setter for the algorithm
	 * @param string $algorithm The algorithm function
	 */
	function setAlgorithm($algorithm) {
		$this->_encryptionAlgorithm = $algorithm;
	}

	/**
	 * Get the current algorithm set
	 * @return string
	 */
	function getAlgorithm() {
		return $this->_encryptionAlgorithm;
	}

	/**
	 * Send the password recovery email to the user.
	 * @param string $p_sEmail The Email Address
	 * @param string $p_sSubject The Subject
	 * @param string $p_sMessage The Message
	 * @return boolean
	 */
	function sendRecoverEmail($p_aUser, $p_sSubject = '', $p_sMessage = '') {
		$oConfig = $this->getConfig();
		if($p_sSubject === '') {
			$p_sSubject = 'Password recovery';
		}
		$sRecoveryCode = base64_encode(time());
		if($p_sMessage === '') {
			$p_sMessage = "Hi, {$p_aUser['first_name']}\n\nYou have requested a password recovery and your password has now been reset.\nPlease click the following verification link to reset your password.\n";
			$p_sMessage .= $oConfig->system->base_url . 'user/recover/' . urlencode($sRecoveryCode);
		}
		$oEmail = new PPI_Model_Email_Advanced();
		$oEmail->Subject = $p_sSubject;
		$oEmail->SetFrom($oConfig->system->adminEmail, $oConfig->system->adminName);
		$oEmail->AddAddress($p_aUser['email']);
		$oEmail->AltBody = $p_sMessage;
		$oEmail->MsgHTML($p_sMessage);
		// If the email sent successfully,
		if($oEmail->Send()) {
			$oUser       = new APP_Model_User();
			$sPrimaryKey = $oUser->getPrimaryKey();
			// Lets update the users record with their recovery_code
			$oUser->putRecord(array(
				'recovery_code' => $sRecoveryCode,
				$sPrimaryKey    => $p_aUser[$sPrimaryKey]
			));
			return true;
		}
		return false;
	}

	/**
	 * Incomplete
	 * @todo everything
	 * @param string $p_sRecoverCode Recovery code
	 */
	function verifyRecoverCode($p_sRecoverCode) {
		$p_sRecoverCode = base64_decode(urldecode($p_sRecoverCode));
	}

	/**
	 * The formbuilder structure for the Password Recovery module.
	 * @return array The form structure
	 */
	function getRecoverFormStructure() {
		$structure = array(
			'fields' => array(
				'email'      => array('type' => 'text', 'label' => 'Email address')
			),
			'rules' => array(
				'email' => array(
					array('type' => 'required', 'message' => 'You must enter a valid email address'),
					array('type' => 'email', 'message'    => 'You must enter a valid email address')
				)
			)
		);
		return $structure;

	}
}
