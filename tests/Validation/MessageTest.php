<?php

/**
 * User: coderd
 * Date: 2016/11/21
 * Time: 10:44
 */

use Rapkg\Validation\Validator;
use Rapkg\Validation\MessageInterface;

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testIntegerGlobalMessage()
    {
        $integerRuleMessageInstance = new integerRuleMessage();
        Validator::setGlobalMessageInstance($integerRuleMessageInstance);

        // Message from global rule_messages
        $attribute = 'foo_rule';
        $rule = 'integer';
        $validator = Validator::make([$attribute => 1.1], [$attribute => $rule]);

        $this->assertFalse($validator->passes());
        $this->assertEquals(
            str_replace(':attribute', $attribute, $integerRuleMessageInstance->getMessages()['rule_messages'][$rule]),
            $validator->getMessage()
        );

        // Message from global custom_messages
        $attribute = 'foo_custom';
        $rule = 'integer';
        $validator = Validator::make([$attribute => 1.1], [$attribute => $rule]);

        $this->assertFalse($validator->passes());
        $this->assertEquals(
            str_replace(':attribute', $attribute, $integerRuleMessageInstance->getMessages()['custom_messages'][$attribute][$rule]),
            $validator->getMessage()
        );

        // Rename attribute name in message
        $attribute = 'foo_attr';
        $rule = 'integer';
        $validator = Validator::make([$attribute => 1.1], [$attribute => $rule]);

        $this->assertFalse($validator->passes());
        $newAttribute = $integerRuleMessageInstance->getMessages()['attributes'][$attribute];
        $this->assertEquals(
            str_replace(':attribute', $newAttribute, $integerRuleMessageInstance->getMessages()['rule_messages'][$rule]),
            $validator->getMessage()
        );
    }
}

class integerRuleMessage implements MessageInterface
{
    public function getMessages()
    {
        return [
            'rule_messages' => [
                'integer' => 'The :attribute must be an integer(from global rule message)'
            ],

            'custom_messages' => [
                'foo_custom' => [
                    'integer' => 'The :attribute must be an integer(from global custom message)'
                ]
            ],

            'attributes' => [
                'foo_attr' => 'foo_attr_beauty'
            ],
        ];
    }
}

