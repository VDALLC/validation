<?php
namespace Vda\Validation\Validators;

/**
 * Single rule single value validator.
 *
 * Assumed there is only one possible error message for validator.
 *
 * @package Vda\Validation\Validators
 */
interface IValidator
{
    /**
     * @param $value
     * @param $data
     * @return FieldValidationResult
     */
    public function validate($value, $data);

    public function getMessageParams();
}
