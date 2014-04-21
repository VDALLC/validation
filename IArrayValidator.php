<?php
namespace Vda\Validation;

use \Exception;
use Vda\Validation\Validators\IValidator;

/**
 * Interface IArrayValidator
 *
 *
 *
 * @package Vda\Validation
 */
interface IArrayValidator
{
    /**
     * @param $data
     * @return IValidationResult
     */
    public function validate($data);

    /**
     * Select fields to configure validators.
     *
     * @param $names
     * @return self
     */
    public function field($names);

    /**
     * Register custom validator.
     *
     * @param $name
     * @param IValidator|callable $validator
     * @throws Exception
     */
    public static function registerRule($name, $validator);

    /**
     * @param $filename
     * @return ArrayValidator
     */
    public static function loadJson($filename);

    public function configure(array $config, array $messages = array(), array $labels = array());

//====== build-in validators ====================================================

    /**
     * @return self
     */
    public function required();

    public function range($from, $to);

//====== translation support ====================================================

    /**
     * Set translation message for last selected rule.
     *
     * You can use #field# and other rule depended placeholders in template.
     *
     * @param $template
     * @return self
     */
    public function message($template);

    /**
     * Set label for last active fields.
     *
     * Useful for single field selection.
     *
     * @param $label
     * @return self
     */
    public function label($label);

    /**
     * Set labels for given fields.
     *
     * @param array $labels
     * @return self
     */
    public function labels(array $labels);
}
