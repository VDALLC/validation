<?php
namespace Vda\Validation\Validators;

abstract class AbstractValidator implements IValidator
{
    public function getMessageParams()
    {
        return array();
    }
}
