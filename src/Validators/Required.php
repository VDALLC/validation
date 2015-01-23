<?php
namespace Vda\Validation\Validators;

class Required extends AbstractValidator
{
    public function validate($value, $data)
    {
        return new FieldValidationResult(!empty($value), $value);
    }
}
