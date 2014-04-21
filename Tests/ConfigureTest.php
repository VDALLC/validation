<?php

use Vda\Validation\ArrayValidator;
use Vda\Validation\Validators\FieldValidationResult;

class ConfigureTestClass extends PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayValidator
     */
    protected $validator;

    public function setUp()
    {
        ArrayValidator::registerRule('comparePasswords', function($value, $data) {
            return new FieldValidationResult($value == $data['password1'], $value);
        });

        $this->validator = new ArrayValidator();
        $this->validator->configure(array(
            'name' => array('required'),
            'password1' => array('required'),
            'password2' => array('required', 'comparePasswords'),
            'age' => array(array('range', 18, 100)),
        ), array(
            'required' => 'Please fill #field#',
            'comparePasswords' => 'passwords must be same',
            'range' => '#field# must be in range between #from# and #to#',
        ), array(
            'name' => 'your name',
            'age' => 'age',
        ));
    }

    public function testEmptyArrayAllErrors()
    {
        $res = $this->validator->validate(array());
        $this->assertFalse($res->isValid());
        $this->assertEquals(4, count($res->getAllMessages()));
    }

    public function testAllValid()
    {
        $res = $this->validator->validate(array(
            'name' => 'vtk',
            'password1' => '1234',
            'password2' => '1234',
            'age' => 22
        ));
        $this->assertTrue($res->isValid());
        $this->assertEquals(0, count($res->getAllMessages()));
    }
}
