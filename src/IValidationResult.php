<?php
namespace Vda\Validation;

interface IValidationResult
{
    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return array
     */
    public function getValidData();

    /**
     * @param string $field
     * @return string
     */
    public function getFirstMessage($field);

    /**
     * @param string $field
     * @return string[]
     */
    public function getMessages($field);

    /**
     * @return string[][]
     */
    public function getAllMessages();
}
