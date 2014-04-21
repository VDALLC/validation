<?php

use Vda\Validation\ArrayValidator;

class ValidationTestClass extends PHPUnit_Framework_TestCase
{
    public function testRequiredField()
    {
        $validator = new ArrayValidator();
        $validator->field('name')->required();
        $result = $validator->validate(array(
            'name' => ''
        ));
        $this->assertFalse($result->isValid());
    }

    public function testRangeField()
    {
        $validator = new ArrayValidator();
        $validator->field('age')->range(18, 100);
        $result = $validator->validate(array(
            'age' => 17
        ));
        $this->assertFalse($result->isValid());

        $result = $validator->validate(array(
            'age' => 33
        ));
        $this->assertTrue($result->isValid());

        $result = $validator->validate(array(
            'age' => 101
        ));
        $this->assertFalse($result->isValid());
    }

    public function testValidData()
    {
        $validator = new ArrayValidator();
        $validator->field('name')->required();

        $result = $validator->validate(array(
            'name' => 'John',
            'extra' => 'junk',
        ));
        $this->assertTrue($result->isValid());
        $this->assertEquals(array('name' => 'John'), $result->getValidData());
    }

    public function testDefaultErrorMessage()
    {
        $validator = new ArrayValidator();
        $validator->field('name')->required();
        $result = $validator->validate(array(
            'name' => ''
        ));
        $this->assertFalse($result->isValid());
        $this->assertEquals('Fill name field.', $result->getFirstMessage('name'));
    }

    public function testCustomErrorMessage()
    {
        $validator = new ArrayValidator();
        $validator->field('name')->required()->message('wow, #field# is empty');
        $result = $validator->validate(array(
            'name' => ''
        ));
        $this->assertFalse($result->isValid());
        $this->assertEquals('wow, name is empty', $result->getFirstMessage('name'));
    }

    public function testCustomLabelInErrorMessage()
    {
        $validator = new ArrayValidator();
        $validator->field('name')->required()->label('Your Name');
        $result = $validator->validate(array(
            'name' => ''
        ));
        $this->assertFalse($result->isValid());
        $this->assertEquals('Fill Your Name field.', $result->getFirstMessage('name'));
    }

    public function testMultidimensionalArray()
    {
        $validator = new ArrayValidator();
        $validator->field('user.name')->required();

        $data = array(
            'user' => array(
                'name' => 'vtk',
            ),
        );
        $result = $validator->validate($data);
        $this->assertTrue($result->isValid());

        $result = $validator->validate(array());
        $this->assertFalse($result->isValid());
    }

    public function testMultidimensionalObject()
    {
        $validator = new ArrayValidator();
        $validator->field('user.name')->required();

        $data = new stdClass();
        $data->user = new stdClass();
        $data->user->name = 'vtk';
        $result = $validator->validate($data);
        $this->assertTrue($result->isValid());

        $result = $validator->validate(new stdClass());
        $this->assertFalse($result->isValid());
    }
}
