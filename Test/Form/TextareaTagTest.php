<?php
class PPI_Test_TextareaTagTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->_form = new PPI_Form();
	}

	function tearDown() {
		unset($this->_form);
	}

	function testCreate() {
		$output = $this->_form->textarea('desc');
		$this->assertEquals($output, '<textarea name="desc"></textarea>');
	}

	function testCreateWithAttrs() {
		$output = $this->_form->textarea('desc', array('id' => 'bar'));
		$this->assertEquals($output, '<textarea name="desc" id="bar"></textarea>');
	}

	function testDirectClass() {
		$text = new PPI_Form_Tag_Textarea(array(
			'value' => 'my description',
			'name'  => 'desc',
			'id'    => 'bar'
		));
		$output = $text->render();
		$this->assertEquals($output, '<textarea name="desc" id="bar">my description</textarea>');
	}

	function testDirectClass__toString() {
		$text = new PPI_Form_Tag_Textarea(array(
			'value' => 'my description',
			'name'  => 'desc',
			'id'    => 'bar'
		));
		$output = (string) $text;
		$this->assertEquals($output, '<textarea name="desc" id="bar">my description</textarea>');
	}

	function testHasAttr() {
		$text = new PPI_Form_Tag_Textarea(array(
			'value' => 'my description',
			'name'  => 'desc',
			'id'    => 'bar'
		));
		$this->assertTrue($text->hasAttr('name'));
		$this->assertFalse($text->hasAttr('nonexistantattr'));
	}

	function testGetAttr() {
		$text = new PPI_Form_Tag_Textarea(array(
			'value' => 'my description',
			'name'  => 'desc',
			'id'    => 'bar'
		));
		$this->assertEquals('desc', $text->attr('name'));
	}

	function testSetAttr() {
		$text = new PPI_Form_Tag_Text(array(
			'value' => 'my description'
		));
		$text->attr('foo', 'bar');
		$this->assertEquals('bar', $text->attr('foo'));
	}

	function testGetValues() {
		$text = new PPI_Form_Tag_Text(array(
			'value' => 'textvalue'
		));
		$this->assertEquals('textvalue', $text->getValue());
	}

	function testSetValue() {
		$text = new PPI_Form_Tag_Textarea();
		$text->setValue('my description');
		$this->assertEquals('my description', $text->getValue());
	}

	function testGetSetRule() {

		$field = new PPI_Form_Tag_Textarea();

		$field->setRule('required');
		$this->assertTrue(count($field->getRule('required')) > 0);

		$field->setRule('maxlength', 32);
		$rule = $field->getRule('maxlength');
		$this->assertEquals($rule['value'], 32);
		$this->assertEquals($rule['type'], 'maxlength');
	}

}