<?php

class ValidationResultTestClass extends PHPUnit_Framework_TestCase
{
    // test that array access returns first error message
    public function testArrayAccess()
    {
        $res = new \Vda\Validation\ValidationResult(false, array(), array(
            'name' => array(
                'required' => 'Fill your name',
                'length' => 'Name must be less than 100 chars',
            ),
            'email' => array(
                'email' => 'Invalid email format',
                'length' => 'Email must be less than 100 chars',
            ),
        ));

        $this->assertEquals('Fill your name', $res['name']);
        $this->assertEquals('Invalid email format', $res['email']);
    }
}
