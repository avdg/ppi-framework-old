<?php
class PPI_Helper_User {

	static function getRoleNameFromID($p_iRoleID) {
		$oConfig = PPI_Helper::getConfig();
		$aRoles = array_flip(self::getRoles());
		if(array_key_exists($p_iRoleID, $aRoles)) {
			return $aRoles[$p_iRoleID];
		}
		throw new PPI_Exception('Unknown Role ID: '.$p_iRoleID);		
	}
	
	static function getRoleNameNice($sRoleName) {
		return ucwords(str_replace('_', ' ', $sRoleName));
	}
		
	/**
	 * Returns an array of role_id => role_type of all the roles defined
	 *
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