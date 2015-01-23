<?php
namespace Vda\Validation;

use Exception;
use InvalidArgumentException;
use Vda\Util\BeanUtil;
use Vda\Validation\Validators\Callback;
use Vda\Validation\Validators\IValidator;
use Vda\Validation\Validators\Range;
use Vda\Validation\Validators\Required;

class ArrayValidator implements IArrayValidator
{
    /**
     * @var array field => rule[]
     */
    protected $validationRules = array();

    protected $activeFields = array();
    protected $activeRules = array();

    /**
     * Translation error messages.
     *
     * #field#
     *
     * @var array
     */
    protected $errorMessages = array(
        'required' => 'Fill #field# field.'
    );

    /**
     * Translation field names.
     *
     * @var array
     */
    protected $labels = array(

    );

    static protected $rules = array(
        'required'  => 'Vda\Validation\Validators\Required',
        'range'     => 'Vda\Validation\Validators\Range',
    );

    public static function registerRule($name, $validator)
    {
        if (is_callable($validator)) {
            self::$rules[$name] = new Callback($validator);
        } elseif ($validator instanceof IValidator || is_string($validator)) {
            self::$rules[$name] = $validator;
        } else {
            throw new Exception("Invalid validator specified #{$validator}");
        }
    }

    public static function loadJson($filename)
    {
        $config = json_decode(file_get_contents($filename), true);
        $validator = new self();
        $validator->configure(
            $config['rules'],
            $config['messages'],
            $config['labels']
        );
        return $validator;
    }

    /**
     * @param $data
     * @return ValidationResult
     */
    public function validate($data)
    {
        $messages = array();
        $bool = true;
        $validData = array();
        foreach ($this->validationRules as $field => $rules) {
            foreach ($rules as $ruleName => $rule) {
                $validator = $this->fetchValidator($rule);
                $res = $this->fetchValidator($rule)->validate($this->fetchValue($data, $field), $data);
                if ($res->isValid()) {
                    $validData[$field] = $res->getValidValue();
                } else {
                    $bool = false;
                    $validData[$field] = null;
                    $messages[$field][] = $this->makeMessage($ruleName, $field, $validator->getMessageParams());
                }
            }
        }
        return new ValidationResult($bool, $validData, $messages);
    }

    protected function fetchValue($data, $spec)
    {
        $parts = explode('.', $spec);
        $res = $data;
        foreach ($parts as $part) {
            $res = BeanUtil::getProperty($res, $part);
        }
        return $res;
    }

    public function field($names, array $rules = array())
    {
        $this->activeFields = (array)$names;
        $this->activeRules = $rules;
        foreach ($this->activeFields as $name) {
            foreach ($rules as $rule) {
                // second key $rule to filter rules doubling
                $this->validationRules[$name][$rule] = $rule;
            }
        }

        return $this;
    }

    public function configure(array $config, array $messages = array(), array $labels = array())
    {
        foreach ($config as $field => $rules) {
            foreach ($rules as $rule) {
                $this->validationRules[$field][] = $this->fetchValidator($rule);
            }
        }

        foreach ($messages as $rule => $template) {
            $this->errorMessages[$rule] = $template;
        }

        foreach ($labels as $field => $label) {
            $this->labels[$field] = $label;
        }

        return $this;
    }

    public function required()
    {
        $this->activeRules = array('required');

        foreach ($this->activeFields as $name) {
            $this->validationRules[$name]['required'] = new Required();
        }

        return $this;
    }

    public function range($from = 0, $to = 100)
    {
        $this->activeRules = array('range');

        foreach ($this->activeFields as $name) {
            // TODO several validators with same name? but rule names need to messages
            $this->validationRules[$name]['range'] = new Range($from, $to);
        }

        return $this;
    }

    public function message($template)
    {
        foreach ($this->activeRules as $rule) {
            $this->errorMessages[$rule] = $template;
        }

        return $this;
    }

    public function label($label)
    {
        foreach ($this->activeFields as $field) {
            $this->labels[$field] = $label;
        }

        return $this;
    }

    public function labels(array $labels)
    {
        foreach ($labels as $field => $label) {
            $this->labels[$field] = $label;
        }

        return $this;
    }

    /**
     * @param $ref
     * @return IValidator
     * @throws InvalidArgumentException
     */
    protected function fetchValidator($ref)
    {
        if (is_string($ref) && isset(self::$rules[$ref])) {
            if (is_string(self::$rules[$ref])) {
                return new self::$rules[$ref];
            } else { // object
                return self::$rules[$ref];
            }
        } elseif (is_array($ref)) {
            // should be used only with self::$rules[$class] as class name, not object
            $class = array_shift($ref);
            if (isset(self::$rules[$class])) {
                $class = self::$rules[$class];
            }
            $reflect = new \ReflectionClass($class);
            return $reflect->newInstanceArgs($ref);
        } elseif ($ref instanceof IValidator) {
            return $ref;
        } else {
            throw new InvalidArgumentException("Invalid validator reference #{$ref}");
        }
    }

    protected function makeMessage($rule, $field, array $params)
    {
        if (isset($this->errorMessages[$rule])) {
            $template = $this->errorMessages[$rule];
        } else {
            $template = "Invalid #field# field.";
        }
        if (isset($this->labels[$field])) {
            $field = $this->labels[$field];
        }
        $params = array_merge(array('field' => $field), $params);
        foreach ($params as $name => $value) {
            $template = str_replace("#{$name}#", $value, $template);
        }
        return $template;
    }
}
