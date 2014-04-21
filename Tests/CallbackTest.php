<?php

use Vda\Validation\ArrayValidator;
use Vda\Validation\Validators\FieldValidationResult;

class CallbackTestClass extends PHPUnit_Framework_TestCase
{
    public function testSimpleCallback()
    {
        ArrayValidator::registerRule('uniqueName', function($value) {
            // here can be a query to database
            return new FieldValidationResult(true, $value);
        });

        $validator = new ArrayValidator();
        $validator->field('name', ['uniqueName']);
        $res = $validator->validate(array(
            'name' => 'vtk',
        ));
        $this->assertTrue($res->isValid());
        $this->assertEquals(array('name' => 'vtk'), $res->getValidData());
    }

    public function testCallbackAsValidatorWithContext()
    {
        ArrayValidator::registerRule('comparePasswords', function($value, $data) {
            return new FieldValidationResult($value == $data['password1'], $value);
        });

        $validator = new ArrayValidator();
        $validator->field('password1')->required();
        $validator->field('password2', ['comparePasswords']);

        $res = $validator->validate(array(
            'password1' => '1234',
            'password2' => '2345',
        ));
        $this->assertFalse($res->isValid());

        $res = $validator->validate(array(
            'password1' => '1234',
            'password2' => '1234',
        ));
        $this->assertTrue($res->isValid());
    }

    /**
     * Callback rule must return instance of FieldValidationResult
     *
     * @expectedException Exception
     */
    public function testCallbackResult()
    {
        ArrayValidator::registerRule('uniqueName', function($value, $data) {
            return true;
        });

        $validator = new ArrayValidator();
        $validator->field('name', ['uniqueName']);
        $validator->validate(array(
            'name' => 'vtk',
        ));
    }
}
