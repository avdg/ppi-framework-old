<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 * @subpackage core
 */

class PPI_Model_Form_Field extends PPI_Model {
	
	private $_fieldName;
	private $_fieldID;
	private $_primary = 'id';
	public $_name = 'ppi_fb_field';
	
	function __construct() {
		parent::__construct($this->_name, $this->_primary);
	}
	
	function getRules() {
		
	}
	
	function getAttributes($p_iFieldID) {
		$select 		= $this->select()
							->from('ppi_fb_field_attribute')
							->where('field_id = '.$this->quote($p_iFieldID));
		$aAttrs 		= $select->getList($select);
		$aAttributes 	= array();
		foreach($aAttrs as $aAttr) {
			$aAttributes[$aAttr['key']] = $aAttr['value'];
		}
		return $aAttributes;
	}
	
	function getFieldTypes() {
		$select 		= $this->select()->from('ppi_fb_field_type');
		$aFieldTypes 	= $select->getList($select);
		$aFields		= array();
		foreach($aFieldTypes as $aField) {
			$aFields[$aField['id']] = $aField['type'];
		}
		return (count($aFieldTypes) > 0) ? $aFields : array();
	}	
	
	function getField($p_iFieldID) {
		$select = $this->select()
						->from($oField->_name)
						->where("id = '".$p_iFieldID."'");
		return $select->getList($select);
	}
	
	// 
	function checkExists($p_iFieldID) {	
			
	}
	
}	