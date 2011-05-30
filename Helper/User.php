<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Helper
 * @package   www.ppiframework.com
 */
class PPI_Helper_User {


    /**
     * Convert a users RoleID to their RoleName
     *
     * @static
     * @throws PPI_Exception
     * @param  integer $p_iRoleID
     * @return string
     */
	static function getRoleNameFromID($p_iRoleID) {
		$aRoles = array_flip(self::getRoles());
		if(array_key_exists($p_iRoleID, $aRoles)) {
			return $aRoles[$p_iRoleID];
		}
		throw new PPI_Exception('Unknown Role ID: '.$p_iRoleID);		
	}

    /**
     * Convert a role name to a 'nice' role name which makes it UI friendly.
     *
     * @static
     * @param  string $sRoleName
     * @return string
     */
	static function getRoleNameNice($sRoleName) {
		return ucwords(str_replace('_', ' ', $sRoleName));
	}
		
	/**
	 * Returns an array of role_id => role_type of all the roles defined
	 *
     * @return array
	 */
	static function getRoles() {
		$oConfig = PPI_Helper::getConfig();
		if(isset($oConfig->user->roleMappingService) && $oConfig->user->roleMappingService == 'database') {
			$oUser     = new APP_Model_User_Role();
			$aRoles    = $oUser->getList()->fetchAll();
			$aRetRoles = array();
			foreach($aRoles as $aRole) {
				$aRetRoles[$aRole['name']] = $aRole['id'];
			}
			return $aRetRoles;
		} else {
			return $oConfig->system->roleMapping->toArray();	
		}
	}	
	
}