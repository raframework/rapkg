<?php

/**
 * User: coderd
 * Date: 2016/11/18
 * Time: 下午9:15
 */

use Rapkg\Validation\Variable;
use Rapkg\Validation\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    const TYPE_INTEGER = 'integer';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';

    protected function setUp()
    {
        parent::setUp();

        Validator::unsetGlobalMessageInstance();
    }

    /**
     * @dataProvider dataProvider
     * @param $data
     * @param $rules
     * @param $passes
     * @param $message
     */
    public function testRule($data, $rules, $passes, $message = "")
    {
        $validator = Validator::make($data, $rules);
        if ($passes) {
            $this->assertTrue($validator->passes());
        } else {
            $this->assertFalse($validator->passes());
            $this->assertEquals($message, $validator->getMessage());
        }
    }

    public function dataProvider()
    {
        return array_merge(
            $this->requiredProvider(),
            $this->integerProvider(),
            $this->booleanProvider(),
            $this->stringProvider(),
            $this->numericProvider(),
            $this->alphaNumProvider(),
            $this->floatProvider(),
            $this->arrayProvider(),

            $this->maxProvider(),
            $this->minProvider(),
            $this->betweenProvider(),
            $this->sizeProvider(),

            $this->inProvider(),
            $this->containProvider(),
            $this->noSpaceProvider(),

            $this->ipProvider(),
            $this->emailProvider(),
            $this->cnMobileProvider(),
            $this->cnIdCardProvider(),

            $this->dateFormatProvider(),
            $this->regexProvider()
        );
    }

    private function failsMessage($rule, $attribute)
    {
        $message = Variable::getDefaultRuleMessages()[$rule];
        $message = str_replace(':attribute', $attribute, $message);

        return $message;
    }

    private function requiredProvider()
    {
        $attribute = 'foo';
        $rule = 'required';
        $failsMessage = $this->failsMessage($rule, $attribute);

        return [
            // passes
            [
                [$attribute => true],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => false],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 0],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 1.1],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 'bar'],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => [1]],
                [$attribute => $rule],
                true
            ],

            // fails
            [
                [$attribute => null],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => "  "],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => []],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
        ];
    }

    private function integerProvider()
    {
        $attribute = 'foo';
        $rule = 'integer';
        $failsMessage = $this->failsMessage($rule, $attribute);

        return [
            // passes
            [
                [$attribute => null],
                [$attribute => $rule],
                true,
                $failsMessage
            ],
            [
                [$attribute => 0],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => -1],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 1],
                [$attribute => $rule],
                true
            ],


            // fails
            [
                [$attribute => true],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => false],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => -1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => ""],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => [1]],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function booleanProvider()
    {
        $attribute = 'foo';
        $rule = 'boolean';
        $failsMessage = $this->failsMessage($rule, $attribute);

        return [
            // passes
            [
                [$attribute => null],
                [$attribute => $rule],
                true,
                $failsMessage
            ],
            [
                [$attribute => true],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => false],
                [$attribute => $rule],
                true
            ],


            // fails
            [
                [$attribute => 1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => -1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => ""],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => [1]],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function stringProvider()
    {
        $attribute = 'foo';
        $rule = 'string';
        $failsMessage = $this->failsMessage($rule, $attribute);

        return [
            // passes
            [
                [$attribute => null],
                [$attribute => $rule],
                true,
                $failsMessage
            ],
            [
                [$attribute => ""],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => "  "],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => "  s  "],
                [$attribute => $rule],
                true
            ],


            // fails
            [
                [$attribute => true],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => false],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => -1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => [1]],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function numericProvider()
    {
        $attribute = 'foo';
        $rule = 'numeric';
        $failsMessage = $this->failsMessage($rule, $attribute);

        return [
            // passes
            [
                [$attribute => null],
                [$attribute => $rule],
                true,
                $failsMessage
            ],
            [
                [$attribute => 0],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => -1],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 1],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 0.0],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => -1.1],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 1.1],
                [$attribute => $rule],
                true
            ],


            // fails
            [
                [$attribute => true],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => false],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => ""],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => [1]],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function alphaNumProvider()
    {
        $attribute = 'foo';
        $rule = 'alpha_num';
        $failsMessage = $this->failsMessage($rule, $attribute);

        return [
            // passes
            [
                [$attribute => null],
                [$attribute => $rule],
                true,
                $failsMessage
            ],
            [
                [$attribute => 0],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 1],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => "a"],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => "A"],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => "1a"],
                [$attribute => $rule],
                true
            ],


            // fails
            [
                [$attribute => true],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => false],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => -1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => -1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => ""],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => [1]],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function floatProvider()
    {
        $attribute = 'foo';
        $rule = 'float';
        $failsMessage = $this->failsMessage($rule, $attribute);

        return [
            // passes
            [
                [$attribute => null],
                [$attribute => $rule],
                true,
                $failsMessage
            ],
            [
                [$attribute => 0.0],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => -1.0],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => 1.0],
                [$attribute => $rule],
                true
            ],


            // fails
            [
                [$attribute => true],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => false],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 0],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => -1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => ""],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => [1]],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function arrayProvider()
    {
        $attribute = 'foo';
        $rule = 'array';
        $failsMessage = $this->failsMessage($rule, $attribute);

        return [
            // passes
            [
                [$attribute => null],
                [$attribute => $rule],
                true,
                $failsMessage
            ],
            [
                [$attribute => []],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => [1]],
                [$attribute => $rule],
                true
            ],
            [
                [$attribute => ['s']],
                [$attribute => $rule],
                true
            ],


            // fails
            [
                [$attribute => true],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => false],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => -1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => 1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => -1.1],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
            [
                [$attribute => ""],
                [$attribute => $rule],
                false,
                $failsMessage
            ],
        ];
    }

    private function maxFailsMessage($attribute, $valueType, $max)
    {
        $message = Variable::getDefaultRuleMessages()["max"][$valueType];
        $message = str_replace(':attribute', $attribute, $message);
        $message = str_replace(':max', $max, $message);

        return $message;
    }

    private function maxProvider()
    {
        $attribute = 'foo';
        $rule = 'max';
        $max = 3;

        return [
            // passes
            [
                [$attribute => 2],
                [$attribute => "$rule:$max"],
                true,
            ],
            [
                [$attribute => "ab"],
                [$attribute => "$rule:$max"],
                true,
            ],
            [
                [$attribute => [1]],
                [$attribute => "$rule:$max"],
                true,
            ],

            // fails
            [
                [$attribute => 4],
                [$attribute => "$rule:$max"],
                false,
                $this->maxFailsMessage($attribute, self::TYPE_INTEGER, $max)
            ],
            [
                [$attribute => "abcd"],
                [$attribute => "$rule:$max"],
                false,
                $this->maxFailsMessage($attribute, self::TYPE_STRING, $max)
            ],
            [
                [$attribute => [1, 2, 3, 4]],
                [$attribute => "$rule:$max"],
                false,
                $this->maxFailsMessage($attribute, self::TYPE_ARRAY, $max)
            ],
        ];
    }

    private function minFailsMessage($attribute, $valueType, $min)
    {
        $message = Variable::getDefaultRuleMessages()["min"][$valueType];
        $message = str_replace(':attribute', $attribute, $message);
        $message = str_replace(':min', $min, $message);

        return $message;
    }

    private function minProvider()
    {
        $attribute = 'foo';
        $rule = 'min';
        $min = 3;

        return [
            // passes
            [
                [$attribute => 4],
                [$attribute => "$rule:$min"],
                true,
            ],
            [
                [$attribute => "abcd"],
                [$attribute => "$rule:$min"],
                true,
            ],
            [
                [$attribute => [1, 2, 3, 4]],
                [$attribute => "$rule:$min"],
                true,
            ],

            // fails
            [
                [$attribute => 1],
                [$attribute => "$rule:$min"],
                false,
                $this->minFailsMessage($attribute, self::TYPE_INTEGER, $min)
            ],
            [
                [$attribute => "a"],
                [$attribute => "$rule:$min"],
                false,
                $this->minFailsMessage($attribute, self::TYPE_STRING, $min)
            ],
            [
                [$attribute => [1]],
                [$attribute => "$rule:$min"],
                false,
                $this->minFailsMessage($attribute, self::TYPE_ARRAY, $min)
            ],
        ];
    }

    private function betweenFailsMessage($attribute, $valueType, $min, $max)
    {
        $message = Variable::getDefaultRuleMessages()["between"][$valueType];
        $message = str_replace(':attribute', $attribute, $message);
        $message = str_replace(':min', $min, $message);
        $message = str_replace(':max', $max, $message);

        return $message;
    }

    private function betweenProvider()
    {
        $attribute = 'foo';
        $rule = 'between';
        $min = 3;
        $max = 5;

        return [
            // passes
            [
                [$attribute => 3],
                [$attribute => "$rule:$min,$max"],
                true,
            ],
            [
                [$attribute => 4],
                [$attribute => "$rule:$min,$max"],
                true,
            ],
            [
                [$attribute => 5],
                [$attribute => "$rule:$min,$max"],
                true,
            ],
            [
                [$attribute => "abc"],
                [$attribute => "$rule:$min,$max"],
                true,
            ],
            [
                [$attribute => "abcd"],
                [$attribute => "$rule:$min,$max"],
                true,
            ],
            [
                [$attribute => "abcde"],
                [$attribute => "$rule:$min,$max"],
                true,
            ],
            [
                [$attribute => [1, 2, 3]],
                [$attribute => "$rule:$min,$max"],
                true,
            ],
            [
                [$attribute => [1, 2, 3, 4]],
                [$attribute => "$rule:$min,$max"],
                true,
            ],
            [
                [$attribute => [1, 2, 3, 4, 5]],
                [$attribute => "$rule:$min,$max"],
                true,
            ],

            // fails
            [
                [$attribute => 2],
                [$attribute => "$rule:$min,$max"],
                false,
                $this->betweenFailsMessage($attribute, self::TYPE_INTEGER, $min, $max)
            ],
            [
                [$attribute => 6],
                [$attribute => "$rule:$min,$max"],
                false,
                $this->betweenFailsMessage($attribute, self::TYPE_INTEGER, $min, $max)
            ],
            [
                [$attribute => "ab"],
                [$attribute => "$rule:$min,$max"],
                false,
                $this->betweenFailsMessage($attribute, self::TYPE_STRING, $min, $max)
            ],
            [
                [$attribute => "abcdef"],
                [$attribute => "$rule:$min,$max"],
                false,
                $this->betweenFailsMessage($attribute, self::TYPE_STRING, $min, $max)
            ],
            [
                [$attribute => [1, 2]],
                [$attribute => "$rule:$min,$max"],
                false,
                $this->betweenFailsMessage($attribute, self::TYPE_ARRAY, $min, $max)
            ],
            [
                [$attribute => [1, 2, 3, 4, 5, 6]],
                [$attribute => "$rule:$min,$max"],
                false,
                $this->betweenFailsMessage($attribute, self::TYPE_ARRAY, $min, $max)
            ],
        ];
    }

    private function sizeFailsMessage($attribute, $valueType, $size)
    {
        $message = Variable::getDefaultRuleMessages()["size"][$valueType];
        $message = str_replace(':attribute', $attribute, $message);
        $message = str_replace(':size', $size, $message);

        return $message;
    }

    private function sizeProvider()
    {
        $attribute = 'foo';
        $rule = 'size';
        $size = 3;

        return [
            // passes
            [
                [$attribute => 3],
                [$attribute => "$rule:$size"],
                true,
            ],
            [
                [$attribute => "abc"],
                [$attribute => "$rule:$size"],
                true,
            ],
            [
                [$attribute => [1, 2, 3]],
                [$attribute => "$rule:$size"],
                true,
            ],

            // fails
            [
                [$attribute => 1],
                [$attribute => "$rule:$size"],
                false,
                $this->sizeFailsMessage($attribute, self::TYPE_INTEGER, $size)
            ],
            [
                [$attribute => "a"],
                [$attribute => "$rule:$size"],
                false,
                $this->sizeFailsMessage($attribute, self::TYPE_STRING, $size)
            ],
            [
                [$attribute => [1]],
                [$attribute => "$rule:$size"],
                false,
                $this->sizeFailsMessage($attribute, self::TYPE_ARRAY, $size)
            ],
        ];
    }

    private function inFailsMessage($attribute, $inValues)
    {
        $message = Variable::getDefaultRuleMessages()["in"];
        $message = str_replace(':attribute', $attribute, $message);
        $message = str_replace(':values', $inValues, $message);

        return $message;
    }

    private function inProvider()
    {
        $attribute = 'foo';
        $rule = 'in';

        return [
            // passes
            [
                [$attribute => 1],
                [$attribute => "$rule:1,2"],
                true,
            ],
            [
                [$attribute => "a"],
                [$attribute => "$rule:a,b"],
                true,
            ],


            // fails
            [
                [$attribute => 0],
                [$attribute => "$rule:1,2"],
                false,
                $this->inFailsMessage($attribute, "1,2")
            ],
            [
                [$attribute => "c"],
                [$attribute => "$rule:a,b"],
                false,
                $this->inFailsMessage($attribute, "a,b")
            ],
        ];
    }

    private function containFailsMessage($attribute, $phrase)
    {
        $message = Variable::getDefaultRuleMessages()["contain"];
        $message = str_replace(':attribute', $attribute, $message);
        $message = str_replace(':phrase', $phrase, $message);

        return $message;
    }

    private function containProvider()
    {
        $attribute = 'foo';
        $rule = 'contain';
        $phrase = '{%code}';

        return [
            // passes
            [
                [$attribute => '{%code}'],
                [$attribute => "$rule:$phrase"],
                true,
            ],
            [
                [$attribute => '1 {%code} '],
                [$attribute => "$rule:$phrase"],
                true,
            ],


            // fails
            [
                [$attribute => '{%code'],
                [$attribute => "$rule:$phrase"],
                false,
                $this->containFailsMessage($attribute, $phrase)
            ],
        ];
    }

    private function noSpaceProvider()
    {
        $attribute = 'foo';
        $rule = 'no_space';

        return [
            // passes
            [
                [$attribute => 'abc'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => 123],
                [$attribute => $rule],
                true,
            ],


            // fails
            [
                [$attribute => ' abc'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function ipProvider()
    {
        $attribute = 'foo';
        $rule = 'ip';

        return [
            // passes
            [
                [$attribute => '127.0.0.1'],
                [$attribute => $rule],
                true,
            ],


            // fails
            [
                [$attribute => '127.0.0'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function emailProvider()
    {
        $attribute = 'foo';
        $rule = 'email';

        return [
            // passes
            [
                [$attribute => 'abc@gmail.com'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => 'ab.c@gmail.com'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => 'ab-c@gmail.com'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => 'ab_c@gmail.com'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => 'ab_c@gmail.com.cn'],
                [$attribute => $rule],
                true,
            ],



            // fails
            [
                [$attribute => 'abcgmail.com'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
            [
                [$attribute => '-abc@gmail.com'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function cnMobileProvider()
    {
        $attribute = 'foo';
        $rule = 'cn_mobile';

        return [
            // passes
            [
                [$attribute => '13488888888'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => '14488888888'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => '15488888888'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => '17488888888'],
                [$attribute => $rule],
                true,
            ],
            [
                [$attribute => '18488888888'],
                [$attribute => $rule],
                true,
            ],


            // fails
            [
                [$attribute => '134888888889'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
            [
                [$attribute => '1348888887'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
            [
                [$attribute => '12488888888'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function cnIdCardProvider()
    {
        $attribute = 'foo';
        $rule = 'cn_id_card';

        return [
            // passes
            [
                [$attribute => '469005197511178247'],
                [$attribute => $rule],
                true,
            ],

            // fails
            [
                [$attribute => '46900519751117824'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
            [
                [$attribute => '4690051975111782478'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
            [
                [$attribute => '469005297511178247'],
                [$attribute => $rule],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    private function dateFormatFailsMessage($attribute, $format)
    {
        $message = Variable::getDefaultRuleMessages()["date_format"];
        $message = str_replace(':attribute', $attribute, $message);
        $message = str_replace(':date_format', $format, $message);

        return $message;
    }

    private function dateFormatProvider()
    {
        $attribute = 'foo';
        $rule = 'date_format';
        $format = 'Y-m-d H:i:s';

        return [
            // passes
            [
                [$attribute => '2016-11-21 10:29:00'],
                [$attribute => "$rule:$format"],
                true,
            ],

            // fails
            [
                [$attribute => '2016-11-21 10:29:0000'],
                [$attribute => "$rule:$format"],
                false,
                $this->dateFormatFailsMessage($attribute, $format),
            ],
            [
                [$attribute => '2016 11-21 10:29:00'],
                [$attribute => "$rule:$format"],
                false,
                $this->dateFormatFailsMessage($attribute, $format),
            ],
        ];
    }

    private function regexProvider()
    {
        $attribute = 'foo';
        $rule = 'regex';
        $regex = '/[0-9]+/';

        return [
            // passes
            [
                [$attribute => '123'],
                [$attribute => "$rule:$regex"],
                true,
            ],
            [
                [$attribute => 'a1c'],
                [$attribute => "$rule:$regex"],
                true,
            ],

            // fails
            [
                [$attribute => 'abc'],
                [$attribute => "$rule:$regex"],
                false,
                $this->failsMessage($rule, $attribute)
            ],
        ];
    }

    public function testMultiRules()
    {
        // passes
        $validator = Validator::make(['foo' => 'bar'], ['foo' => 'required|string|max:5']);
        $this->assertTrue($validator->passes());

        // fails
        $validator = Validator::make(['foo' => 'bar'], ['foo' => 'required|string|max:2']);
        $this->assertTrue($validator->fails());
    }
}