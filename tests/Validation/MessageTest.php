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
        $attribute = 'foo_global_custom';
        $rule = 'integer';
        $validator = Validator::make([$attribute => 1.1], [$attribute => $rule]);

        $this->assertFalse($validator->passes());
        $this->assertEquals(
            str_replace(':attribute', $attribute, $integerRuleMessageInstance->getMessages()['custom_messages'][$attribute][$rule]),
            $validator->getMessage()
        );

        // Rename attribute name in message
        $attribute = 'foo_global_attr';
        $rule = 'integer';
        $validator = Validator::make([$attribute => 1.1], [$attribute => $rule]);

        $this->assertFalse($validator->passes());
        $newAttribute = $integerRuleMessageInstance->getMessages()['attributes'][$attribute];
        $this->assertEquals(
            str_replace(':attribute', $newAttribute, $integerRuleMessageInstance->getMessages()['rule_messages'][$rule]),
            $validator->getMessage()
        );

        // Message from custom_messages
        $attribute = 'foo_custom';
        $rule = 'integer';
        $customMessage = 'The :attribute must be an integer(from custom message)';
        $validator = Validator::make([$attribute => 1.1], [$attribute => $rule], [$attribute => [$rule => $customMessage]]);

        $this->assertFalse($validator->passes());
        $this->assertEquals(
            str_replace(':attribute', $attribute, $customMessage),
            $validator->getMessage()
        );

        // Message from custom_messages
        $attribute = 'foo_custom';
        $rule = 'integer';
        $customMessage = 'The :attribute must be an integer(from custom message)';
        $validator = Validator::make([$attribute => 1.1], [$attribute => $rule], [$attribute => $customMessage]);

        $this->assertFalse($validator->passes());
        $this->assertEquals(
            str_replace(':attribute', $attribute, $customMessage),
            $validator->getMessage()
        );

        // Rename attribute name in custom attributes
        $attribute = 'foo_custom';
        $rule = 'integer';
        $customMessage = 'The :attribute must be an integer(from custom message)';
        $validator = Validator::make([$attribute => 1.1], [$attribute => $rule], [$attribute => $customMessage], [$attribute => $attribute . "foo"]);
        $attribute = $attribute . "foo";
        $this->assertFalse($validator->passes());
        $this->assertEquals(
            str_replace(':attribute', $attribute, $customMessage),
            $validator->getMessage()
        );
    }

    protected function tearDown()
    {
        parent::tearDown();

        Validator::unsetGlobalMessageInstance();
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
                'foo_global_custom' => [
                    'integer' => 'The :attribute must be an integer(from global custom message)'
                ],
                'foo_custom' => [
                    'integer' => 'The :attribute must be an integer(from global custom message)'
                ],
            ],

            'attributes' => [
                'foo_global_attr' => 'foo_attr_beauty'
            ],
        ];
    }
}

