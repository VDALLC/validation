<?php
namespace Vda\Validation\Validators;

use Exception;

class Callback extends AbstractValidator
{
    protected $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function validate($value, $data)
    {
        $res = call_user_func($this->callback, $value, $data);
        if ($res instanceof FieldValidationResult) {
            return $res;
        } else {
            throw new Exception('Callback validator must return instance of FieldValidationResult');
        }
    }
}
