<?php
class PPI_Test_PasswordTagTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->_form = new PPI_Form();
	}

	function tearDown() {
		unset($this->_form);
	}

	function testCreate() {
		$output = $this->_form->password('mypass');
		$this->assertEquals($output, '<input type="password" name="mypass">');
	}

	function testCreateWithAttrs() {
		$output = $this->_form->password('mypass', array('id' => 'bar'));
		$this->assertEquals($output, '<input type="password" name="mypass" id="bar">');
	}

	function testDirectClass() {
		$pass = new PPI_Form_Tag_Password(array(
			'value' => 'foo_pass',
			'name'  => 'mypass',
			'id'    => 'bar'
		));
		$output = $pass->render();
		$this->assertEquals($output, '<input type="password" value="foo_pass" name="mypass" id="bar">');
	}

	function testHasAttr() {
		$pass = new PPI_Form_Tag_Password(array(
			'value' => 'foo_pass',
			'name'  => 'mypass',
			'id'    => 'bar'
		));
		$this->assertTrue($pass->hasAttr('name'));
		$this->assertFalse($pass->hasAttr('nonexistantattr'));
	}

	function testGetAttr() {
		$pass = new PPI_Form_Tag_Password(array(
			'value' => 'foo_pass',
			'name'  => 'mypass',
			'id'    => 'bar'
		));
		$this->assertEquals('foo_pass', $pass->attr('value'));
	}

	function testSetAttr() {
		$pass = new PPI_Form_Tag_Password(array(
			'value' => 'foo_pass'
		));
		$pass->attr('foo', 'bar');
		$this->assertEquals('bar', $pass->attr('foo'));
	}

	function testGetValues() {
		$pass = new PPI_Form_Tag_Password(array(
			'value' => 'password'
		));
		$this->assertEquals('password', $pass->getValue());
		$this->assertEquals('password', $pass->attr('value'));
	}

	function testSetValue() {
		$pass = new PPI_Form_Tag_Password();
		$pass->setValue('password');
		$this->assertEquals('password', $pass->getValue());
	}

}