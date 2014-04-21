<?php
namespace Vda\Validation\Validators;

class Range extends AbstractValidator
{
    protected $from;
    protected $to;

    public function __construct($from = 0, $to = 100)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param $value
     * @param $data
     * @return FieldValidationResult
     */
    public function validate($value, $data)
    {
        return new FieldValidationResult($value >= $this->from && $value <= $this->to, $value);
    }
}
