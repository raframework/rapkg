<?php

/**
 * User: coderd
 * Date: 2016/11/18
 * Time: 10:36
 */

namespace Rapkg\Validation;


class Validator
{
    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;

    /**
     * The instance to retrieve array of global messages.
     *
     * @var MessageInterface
     */
    protected static $globalMessageInstance;

    /**
     * The array of rule messages.
     *
     * @var array
     */
    protected $ruleMessages = [];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    protected $customMessages = [];

    /**
     * The array of custom attribute names.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The last error message
     *
     * @var string
     */
    protected $message;

    /**
     * Create a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     * @param array $customMessages
     * @param array $attributes
     */
    public function __construct(array $data, array $rules, array $customMessages = [], array $attributes = [])
    {
        $this->data = $data;
        $this->rules = $this->explodeRules($rules);
        $this->mergeMessages($customMessages, $attributes);
    }

    /**
     * Merge rule messages & custom messages & attributes
     *
     * @param array $customMessages
     * @param array $attributes
     */
    protected function mergeMessages(array $customMessages = [], array $attributes = [])
    {
        $globalMessages = [];
        if (self::$globalMessageInstance instanceof MessageInterface) {
            $globalMessages = self::$globalMessageInstance->getMessages();
        }

        // Merge rule messages
        $ruleMessages = Variable::getDefaultRuleMessages();
        if (isset($globalMessages['rule_messages']) && is_array($globalMessages['rule_messages'])) {
            $ruleMessages = array_merge($ruleMessages, $globalMessages['rule_messages']);
        }
        $this->ruleMessages = $ruleMessages;

        // Merge custom messages
        if (isset($globalMessages['custom_messages']) && is_array($globalMessages['custom_messages'])) {
            $this->customMessages = array_merge($this->customMessages, $globalMessages['custom_messages']);
        }
        if ($customMessages && is_array($customMessages)) {
            $this->customMessages = array_merge($this->customMessages, $customMessages);
        }

        // Merge attributes
        if (isset($globalMessages['attributes']) && is_array($globalMessages['attributes'])) {
            $this->attributes = array_merge($this->attributes, $globalMessages['attributes']);
        }
        if ($attributes && is_array($attributes)) {
            $this->attributes = array_merge($this->attributes, $attributes);
        }
    }

    /**
     * Create a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     * @param array $customMessages
     * @param array $attributes
     * @return Validator
     */
    public static function make(array $data, array $rules, array $customMessages = [], array $attributes = [])
    {
        return new Validator($data, $rules, $customMessages, $attributes);
    }

    /**
     * Set global message instance
     *
     * @param MessageInterface $globalMessageInstance
     */
    public static function setGlobalMessageInstance(MessageInterface $globalMessageInstance)
    {
        self::$globalMessageInstance = $globalMessageInstance;
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes()
    {
        $this->message = null;
        foreach ($this->rules as $attribute => $rules) {
            foreach ($rules as $rule) {
                if (!$this->validate($attribute, $rule)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Retrieve the last error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Validate a given attribute against a rule.
     *
     * @param $attribute
     * @param $rule
     * @return bool
     */
    protected function validate($attribute, $rule)
    {
        list($rule, $parameters) = $this->parseRule($rule);

        if (!Variable::ruleExists($rule)) {
            throw new \InvalidArgumentException("validation: rule '$rule' does not exists.");
        }

        $value = $this->getValue($attribute);
        $method = 'validate' . self::studly($rule);

        if ($rule != 'required' && $value === null) {
            return true;
        }
        if (!$this->$method($attribute, $value, $parameters)) {
            if (in_array($rule, ['max', 'min', 'size', 'between'])) {
                $message = $this->ruleMessages[$rule][$this->getType($value)];
            } else {
                $message = $this->ruleMessages[$rule];
            }

            if (isset($this->customMessages[$attribute])) {
                $customMessage = $this->customMessages[$attribute];
                if (is_array($customMessage) && isset($customMessage[$rule])) {
                    $message = $customMessage[$rule];
                } else if (is_string($customMessage)){
                    $message = $customMessage;
                }
            }

            if ($rule == 'size') {
                $message = str_replace(':size', $parameters[0], $message);
            } else if ($rule == 'max') {
                $message = str_replace(':max', $parameters[0], $message);
            } else if ($rule == 'min') {
                $message = str_replace(':min', $parameters[0], $message);
            } else if ($rule == 'between') {
                $message = strtr($message, [':min' => $parameters[0], ':max' => $parameters[1]]);
            } else if ($rule == 'in') {
                $message = str_replace(':values', implode(',', $parameters), $message);
            } else if ($rule == 'contain') {
                $message = str_replace(':phrase', $parameters[0], $message);
            } else if ($rule == 'date_format') {
                $message = str_replace(':date_format', $parameters[0], $message);
            }

            if (isset($this->attributes[$attribute])) {
                $attribute = $this->attributes[$attribute];
            }
            $message = str_replace(':attribute', $attribute, $message);
            $this->message = $message;

            return false;
        }

        return true;
    }

    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param string $rule
     * @return array
     */
    private function parseRule($rule)
    {
        $parameters = [];

        if (strpos($rule, ':') !== false) {
            list($rule, $parameter) = explode(':', $rule, 2);
            $parameters = $this->parseParameters($rule, $parameter);
        }

        return [$rule, $parameters];
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param  string|array  $rules
     * @return array
     */
    private function explodeRules($rules)
    {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        return $rules;
    }

    /**
     * Parse a parameter list.
     *
     * @param string $rule
     * @param string $parameter
     * @return array
     */
    protected function parseParameters($rule, $parameter)
    {
        if (strtolower($rule) == 'regex') {
            return [$parameter];
        }

        return str_getcsv($parameter);
    }

    /**
     * Get the value of a given attribute.
     *
     * @param string $attribute
     * @return mixed
     */
    protected function getValue($attribute)
    {
        return $this->data[$attribute];
    }

    protected function validateRequired($attribute, $value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif ((is_array($value) && count($value) < 1)) {
            return false;
        }

        return true;
    }

    protected function validateInteger($attribute, $value)
    {
        return is_integer($value);
    }

    protected function validateBoolean($attribute, $value)
    {
        return is_bool($value);
    }

    protected function validateString($attribute, $value)
    {
        return is_string($value);
    }

    protected function validateNumeric($attribute, $value)
    {
        return is_numeric($value);
    }

    protected function validateAlphaNum($attribute, $value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        return preg_match('/^[0-9a-zA-Z]+$/', $value);
    }

    protected function validateFloat($attribute, $value)
    {
        return is_float($value);
    }

    protected function validateArray($attribute, $value)
    {
        return is_array($value);
    }

    protected function validateMax($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'max');

        return $this->getSize($attribute, $value) <= $parameters[0];
    }

    protected function validateMin($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'min');

        return $this->getSize($attribute, $value) >= $parameters[0];
    }

    protected function validateBetween($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'between');

        $size = $this->getSize($attribute, $value);

        return $size >= $parameters[0] && $size <= $parameters[1];
    }

    protected function validateIn($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'in');

        return in_array($value, $parameters);
    }

    protected function validateContain($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'contain');

        return strpos($value, $parameters[0]) !== false;
    }

    protected function validateNoSpace($attribute, $value)
    {
        return strpos($value, ' ') === false;
    }

    protected function validateSize($attribute, $value, $parameters)
    {
        $size = $this->getSize($attribute, $value);
        return $size == $parameters[0];
    }

    protected function validateIp($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    protected function validateEmail($attribute, $value)
    {
        if (preg_match(
            '/^[a-zA-Z0-9]+([_\-.][a-zA-Z0-9]+)*@[a-zA-Z0-9]+([-.][a-zA-Z0-9]+)*\.[a-zA-Z0-9]+([-.][a-zA-Z0-9]+)*$/',
            $value
        )) {
            return true;
        }

        return false;
    }

    protected function validateCnMobile($attribute, $value)
    {
        if (preg_match('/^1[34578]\d{9}$/', $value)) {
            return true;
        }
        return false;
    }

    protected function validateCnIdCard($attribute, $value)
    {
        $pattern = "/^([1-6][0-9]{5})([1][9]|[2][0])[0-9]{2}([0][1-9]|[1][0-2])([0][1-9]|([1]|[2])[0-9]|[3][0-1])[0-9]{3}[0-9xX]$/";
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
    }

    /**
     * Validate that an attribute matches a date format.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateDateFormat($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'date_format');

        $parsed = date_parse_from_format($parameters[0], $value);

        return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }

    /**
     * Validate that an attribute passes a regular expression check.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateRegex($attribute, $value, $parameters)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        $this->requireParameterCount(1, $parameters, 'regex');

        return preg_match($parameters[0], $value);
    }

    protected function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new \InvalidArgumentException("Validation rule $rule requires at least $count parameters");
        }
    }

    protected function getSize($attribute, $value)
    {
        if (is_integer($value)) {
            return $value;
        } elseif (is_array($value)) {
            return count($value);
        }

        // Take it as a string
        return mb_strlen($value, 'UTF-8');
    }

    protected function getType($value)
    {
        if (is_integer($value)) {
            return 'integer';
        } elseif (is_array($value)) {
            return 'array';
        }

        return 'string';
    }



    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    protected static function studly($value)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }
}