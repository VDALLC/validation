<?php
namespace Vda\Validation;

class ValidationResult implements IValidationResult, \ArrayAccess
{
    protected $isValid;
    protected $data;
    protected $messages;

    public function __construct($isValid, $data, $messages)
    {
        $this->isValid = $isValid;
        $this->data = $data;
        $this->messages = $messages;
    }

    public function isValid()
    {
        return $this->isValid;
    }

    public function getValidData()
    {
        return $this->data;
    }

    public function getFirstMessage($field)
    {
        return reset($this->messages[$field]);
    }

    public function getMessages($field)
    {
        return $this->messages[$field];
    }

    public function getAllMessages()
    {
        return $this->messages;
    }

    public function offsetExists($offset)
    {
        return isset($this->messages[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->messages[$offset])) {
            if (is_array($this->messages[$offset])) {
                return reset($this->messages[$offset]);
            }
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        return null;
    }

    public function offsetUnset($offset)
    {
        return null;
    }
}
