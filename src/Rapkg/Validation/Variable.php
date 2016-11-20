<?php
/**
 * User: coderd
 * Date: 2016/11/18
 * Time: 12:55
 */

namespace Rapkg\Validation;


class Variable
{
    private static $defaultRuleMessages = [
        'required' => 'The :attribute field is required.',

        'integer' => 'The :attribute must be an integer.',
        'boolean' => 'The :attribute field must be true or false.',
        'string' => 'The :attribute must be a string.',
        'numeric' => 'The :attribute must be a number.',
        'alpha_num' => 'The :attribute may only contain letters and numbers.',
        'float' => 'The :attribute must be a float.',
        'array' => 'The :attribute must be an array.',

        'max' => [
            'integer' => 'The :attribute may not be greater than :max.',
            'string'  => 'The :attribute may not be greater than :max characters.',
            'array'   => 'The :attribute may not have more than :max items.',
        ],
        'min' => [
            'integer' => 'The :attribute must be at least :min.',
            'string'  => 'The :attribute must be at least :min characters.',
            'array'   => 'The :attribute must have at least :min items.',
        ],
        'between' => [
            'integer' => 'The :attribute must be between :min and :max.',
            'string'  => 'The :attribute must be between :min and :max characters.',
            'array'   => 'The :attribute must have between :min and :max items.',
        ],
        'in' => 'The :attribute field must one of (:values).',
        'contain' => 'The :attribute field must contain phrase \':phrase\'',
        'no_space' => 'The :attribute field must not contain white space',
        'size' => [
            'integer' => 'The :attribute must be :size.',
            'string'  => 'The :attribute must be :size characters.',
            'array'   => 'The :attribute must contain :size items.',
        ],

        'ip' => 'The :attribute must be a valid IP address.',
        'email' => 'The :attribute must be a valid email address.',
        'cn_mobile' => 'The :attribute must be a valid mobile phone number.',
        'cn_id_card' => 'The :attribute must be identity card of the People\'s Republic of China.',

        'date_format' => 'The :attribute field does not match the format: :date_format',
        'regex' => 'The :attribute format is invalid.',
    ];

    public static function getDefaultRuleMessages()
    {
        return self::$defaultRuleMessages;
    }

    public static function ruleExists($rule)
    {
        return array_key_exists($rule, self::$defaultRuleMessages);
    }
}