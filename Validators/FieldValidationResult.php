<?php
namespace Vda\Validation\Validators;

class FieldValidationResult
{
    protected $isValid;
    protected $value;

    public function __construct($isValid, $value)
    {
        $this->isValid = $isValid;
        $this->value = $value;
    }

    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * This may be useful in case of filtration (trimming, cast to numeric, etc)...
     *
     * @return mixed
     */
    public function getValidValue()
    {
        return $this->value;
    }
}
