<?php
/**
 * User: coderd
 * Date: 2016/11/18
 * Time: 11:04
 */

namespace Rapkg\Validation;


interface MessageInterface
{
    /**
     * Retrieve messages
     * Example:
     * [
     *      // The rule messages
     *      'rule_messages' => [
     *          'rule-name1' => 'message1',
     *          'rule-name2' => 'message2',
     *      ],
     *
     *      // The custom messages
     *      'custom_messages' => [
     *          'attribute-name1' => [
     *              'rule-name1' => 'custom-message1',
     *          ],
     *          'attribute-name2' => 'custom-message2',
     *      ],
     *
     *      // The custom Validation Attributes
     *      // This array is used to swap attribute place-holders
     *      // with something more reader friendly such as E-Mail Address instead
     *      // of "email". This simply helps us make messages a little cleaner.
     *      'attributes' => [],
     * ]
     *
     * @return array
     */
    public function getMessages();
}