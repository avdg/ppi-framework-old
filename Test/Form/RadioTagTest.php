<?php
class PPI_Test_RadioboxTagTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->_form = new PPI_Form();
	}

	function tearDown() {
		unset($this->_form);
	}

	function testCreate() {
		$output = $this->_form->radio('myradio');
		$this->assertEquals($output, '<input type="radio" name="myradio">');
	}

	function testCreateWithAttrs() {
		$output = $this->_form->radio('myradio', array('id' => 'bar'));
		$this->assertEquals($output, '<input type="radio" name="myradio" id="bar">');
	}

	function testDirectClass() {
		$radio =  new PPI_Form_Tag_Radio(array(
			'value' => 'foo_radio',
			'name'  => 'myradio',
			'id'    => 'bar'
		));
		$output = $radio->render();
		$this->assertEquals($output, '<input type="radio" value="foo_radio" name="myradio" id="bar">');
	}

	function testDirectClass__toString() {
		$radio =  new PPI_Form_Tag_Radio(array(
			'value' => 'foo_radio',
			'name'  => 'myradio',
			'id'    => 'bar'
		));
		$output = (string) $radio;
		$this->assertEquals($output, '<input type="radio" value="foo_radio" name="myradio" id="bar">');
	}

	function testHasAttr() {
		$radio =  new PPI_Form_Tag_Radio(array(
			'value' => 'foo_radio',
			'name'  => 'myradio',
			'id'    => 'bar'
		));
		$this->assertTrue($radio->hasAttr('name'));
		$this->assertFalse($radio->hasAttr('nonexistantattr'));
	}

	function testGetAttr() {
		$radio =  new PPI_Form_Tag_Radio(array(
			'value' => 'foo_radio',
			'name'  => 'myradio',
			'id'    => 'bar'
		));
		$this->assertEquals('foo_radio', $radio->attr('value'));
	}

	function testSetAttr() {
		$radio =  new PPI_Form_Tag_Radio(array(
			'value' => 'foo_radio'
		));
		$radio->attr('foo', 'bar');
		$this->assertEquals('bar', $radio->attr('foo'));
	}

	function testGetValues() {
		$radio =  new PPI_Form_Tag_Radio(array(
			'value' => 'foo_radio'
		));
		$this->assertEquals('foo_radio', $radio->getValue());
		$this->assertEquals('foo_radio', $radio->attr('value'));
	}

	function testSetValue() {
		$radio =  new PPI_Form_Tag_Radio();
		$radio->setValue('foo_radio');
		$this->assertEquals('foo_radio', $radio->getValue());
	}

	function testGetSetRule() {

		$field = new PPI_Form_Tag_Radio();

		$field->setRule('required');
		$this->assertTrue(count($field->getRule('required')) > 0);

		$field->setRule('maxlength', 32);
		$rule = $field->getRule('maxlength');
		$this->assertEquals($rule['value'], 32);
		$this->assertEquals($rule['type'], 'maxlength');
	}

}