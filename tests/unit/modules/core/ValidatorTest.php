<?php
/**
 * This file is part of OXID Module Skeleton Generator module.
 *
 * OXID Module Skeleton Generator module is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * OXID Module Skeleton Generator module is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Module Skeleton Generator module.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @category      module
 * @package       modulegenerator
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */
 
namespace Oxps\ModuleGenerator\Tests\Unit\Modules\Core;

use OxidEsales\TestingLibrary\UnitTestCase;
use Oxps\ModuleGenerator\Core\Validator;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class oxpsModuleGeneratorValidatorTest
 * UNIT tests for core class oxpsModuleGeneratorValidator.
 *
 * @see oxpsModuleGeneratorValidator
 */
class ValidatorTest extends UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var Validator|PHPUnit_Framework_MockObject_MockObject
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('oxpsModuleGeneratorValidator', array('__call'));
    }

    public function testGetOxModule()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorOxModule', $this->SUT->getOxModule());
    }

    public function testGetModule()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorModule', $this->SUT->getModule());
    }

    /**
     * @dataProvider vendorPrefixDataProvider
     */
    public function testValidateVendorPrefix($mValue, $blExpectedResult)
    {
        $this->assertSame($blExpectedResult, $this->SUT->validateVendorPrefix($mValue));
    }

    public function vendorPrefixDataProvider()
    {
        return array(

            // Invalid values
            array('', false),
            array(' ', false),
            array(0, false),
            array(1, false),
            array('0000', false),
            array('1111', false),
            array('OXPS', false),
            array('oXps', false),
            array('o', false),
            array('oxpss', false),

            // Valid values
            array('oxps', true),
            array('oe', true),
            array('abc', true),
            array('abcd', true),
        );
    }

    /**
     * @dataProvider camelCaseNameDataProvider
     */
    public function testValidateCamelCaseName($mValue, $blExpectedResult)
    {
        $this->assertSame($blExpectedResult, $this->SUT->validateCamelCaseName($mValue));
    }

    public function camelCaseNameDataProvider()
    {
        return array(

            // Invalid values
            array('', false),
            array(' ', false),
            array(0, false),
            array(1, false),
            array('0000', false),
            array('abc', false),
            array('myModule', false),
            array('A', false),
            array('1module', false),
            array('1MyModule', false),
            array('MyModuleVeryVeryVeryLongLongLongNameThatIsLongLongAndEvenLongerTheNameReallyIs', false),
            array(' MyModule', false),
            array('MyModule ', false),

            // Valid values
            array('MyModule', true),
            array('SuperModuleOne', true),
            array('SuperModuleOne2Three', true),
            array('OtherModuleABCName', true),
            array('MyModuleVeryVeryVeryLongLongLongNameThatIsLongLong', true),
            array('Good', true),
            array('One', true),
            array('Ab', true),
            array('Module1', true),
            array('Module1And2', true),
        );
    }


    /**
     * @dataProvider validateSettingsTypeDataProvider
     */
    public function testValidateSettingsType($mValue, $blExpectedResult)
    {
        $this->assertSame($blExpectedResult, $this->SUT->validateSettingsType($mValue));
    }

    public function validateSettingsTypeDataProvider()
    {
        return [
            // Invalid values
            ['notValid', false],
            [null, false],
            ['', false],
            [' ', false],
            [0, false],
            [1, false],
            [[], false],

            // Valid values
            ['bool', true],
            ['str', true],
            ['num', true],
            ['arr', true],
            ['aarr', true],
            ['select', true],
        ];
    }

    /**
     * @dataProvider camelCaseToHumanReadableDataProvider
     */
    public function testCamelCaseToHumanReadable($mValue, $sExpectedResult)
    {
        $this->assertSame($sExpectedResult, $this->SUT->camelCaseToHumanReadable($mValue));
    }

    public function camelCaseToHumanReadableDataProvider()
    {
        return array(
            array('', ''),
            array(' ', ''),
            array(0, '0'),
            array(1, '1'),
            array('0000', '0000'),
            array('abc', 'abc'),
            array('myModule', 'my Module'),
            array('A', 'A'),
            array('1module', '1module'),
            array('1MyModule', '1 My Module'),
            array(
                'MyModuleVeryVeryVeryLongLongLongNameThatIsLongLongAndEvenLongerTheNameReallyIs',
                'My Module Very Very Very Long Long Long Name That Is Long Long And Even Longer The Name Really Is'
            ),
            array('MyModule', 'My Module'),
            array('SuperModuleOne', 'Super Module One'),
            array('SuperModuleOne2Three', 'Super Module One 2 Three'),
            array('OtherModuleABCName', 'Other Module ABC Name'),
            array(
                'MyModuleVeryVeryVeryLongLongLongNameThatIsLongLong',
                'My Module Very Very Very Long Long Long Name That Is Long Long'
            ),
            array('Good', 'Good'),
            array('One', 'One'),
            array('Ab', 'Ab'),
            array('Module1', 'Module 1'),
            array('Module1And2', 'Module 1 And 2'),
        );
    }

    /**
     * @dataProvider arrayValuesDataProvider
     */
    public function testGetArrayValue(array $aData, $mKey, $sType, $mExpectedResult)
    {
        $this->assertSame($mExpectedResult, $this->SUT->getArrayValue($aData, $mKey, $sType));
    }

    public function arrayValuesDataProvider()
    {
        return array(
            array(array(), '', 'string', ''),
            array(array(), 'a', 'string', ''),
            array(array('a'), 'a', 'string', ''),
            array(array('b' => 'a'), 'a', 'string', ''),
            array(array('b' => 'a'), 'a', 'integer', 0),
            array(array('b' => 'a'), 'a', 'double', 0.0),
            array(array('A' => 2), 'a', 'string', ''),
            array(array('a' => 2), 'a', 'string', '2'),
            array(array('b' => 3, 'a' => 2), 'a', 'string', '2'),
            array(array('b' => 3, 'a' => 2), 'a', 'integer', 2),
            array(array('b' => 3, 'a' => 2), 'a', 'double', 2.0),
            array(array('b' => 3, 'a' => 2), 'a', 'array', array(2)),
        );
    }

    /**
     * @dataProvider validateAndLinkClassesDataProvider
     */
    public function testValidateAndLinkClasses($sValue, $aExpectedResult)
    {
        $this->assertSame($aExpectedResult, $this->SUT->validateAndLinkClasses($sValue));
    }

    public function validateAndLinkClassesDataProvider()
    {
        return [
            // Invalid values
            ['oxNull', []],
            ['nocamelcase', []],
            [null, []],
            [0, []],
            [1, []],
            [[], []],

            // Valid values
            ['Article', ['Article' =>
                [
                    'classPath' => 'Application/Controller/Admin/',
                    'v6ClassName' => 'ArticleController',
                    'v6Namespace' => 'OxidEsales\Eshop\Application\Controller\Admin'
                ]
            ]],
            ['oxarticle', ['oxarticle' =>
                [
                    'classPath' => 'Application/Model/',
                    'v6ClassName' => 'Article',
                    'v6Namespace' => 'OxidEsales\Eshop\Application\Model'
                ]
            ]],
            ['oxArticle', ['oxArticle' =>
                [
                    'classPath' => 'Application/Model/',
                    'v6ClassName' => 'Article',
                    'v6Namespace' => 'OxidEsales\Eshop\Application\Model'
                ]
            ]],
            ['oxList', ['oxList' =>
                [
                    'classPath' => 'Core/',
                    'v6ClassName' => 'ListModel',
                    'v6Namespace' => 'OxidEsales\Eshop\Core\Model',
                ]
            ]],
            ['oxBasket', ['oxBasket' =>
                [
                    'classPath' => 'Application/Model/',
                    'v6ClassName' => 'Basket',
                    'v6Namespace' => 'OxidEsales\Eshop\Application\Model'
                ]
            ]],
            ['Basket', ['Basket' =>
                [
                    'classPath' => 'Application/Controller/',
                    'v6ClassName' => 'BasketController',
                    'v6Namespace' => 'OxidEsales\Eshop\Application\Controller'
                ]
            ]],
            ['oxArticle' . PHP_EOL . 'oxBasket',
             [
                 'oxArticle' =>
                 [
                     'classPath' => 'Application/Model/',
                     'v6ClassName' => 'Article',
                     'v6Namespace' => 'OxidEsales\Eshop\Application\Model'
                 ],
                 'oxBasket'  =>
                 [
                     'classPath' => 'Application/Model/',
                     'v6ClassName' => 'Basket',
                     'v6Namespace' => 'OxidEsales\Eshop\Application\Model'
                 ],
             ]
            ],
        ];
    }

    /**
     * @dataProvider parseMultiLineInputDataProvider
     */
    public function testParseMultiLineInput($sValue, $sLineValidation, $aExpectedResult)
    {
        $this->assertSame($aExpectedResult, $this->SUT->parseMultiLineInput($sValue, $sLineValidation));
    }

    public function parseMultiLineInputDataProvider()
    {
        return [
            // not_empty
            ['oneline', 'not_empty', ['oneline']],
            ['oneline' . PHP_EOL . 0, 'not_empty', ['oneline']],
            [0, 'not_empty', []],
            [1, 'not_empty', ['1']],
            [null, 'not_empty', []],
            [[], 'not_empty', ['Array']],

            // camel_case
            ['nocamelcase', 'camel_case', []],
            ['UpperCamelCase', 'camel_case', ['UpperCamelCase']],
            ['camelCase', 'camel_case', []],
            [0, 'camel_case', []],
            [1, 'camel_case', []],
            [null, 'camel_case', []],
            [[], 'camel_case', ['Array']],

            // multiline
            ['firstline' . PHP_EOL . 0, 'not_empty', ['firstline']],
            [0 . PHP_EOL . 'firstline', 'not_empty', ['firstline']],
            [0 . PHP_EOL . 'firstline', 'camel_case', []],
            [1 . PHP_EOL . 'FirstLine', 'camel_case', ['FirstLine']],
            ['firstline' . PHP_EOL . null, 'not_empty', ['firstline']],
            ['firstline' . PHP_EOL . null, 'not_empty', ['firstline']],
            ['firstline' . PHP_EOL . [], 'not_empty', ['firstline', 'Array']],
            ['firstline' . PHP_EOL . 'secondline', 'not_empty', ['firstline', 'secondline']],
            ['firstline' . PHP_EOL . 'S', 'not_empty', ['firstline', 'S']],
            ['F' . PHP_EOL . 'S', 'camel_case', []],
            ['F' . PHP_EOL . 'S', 'not_empty', ['F', 'S']],
            ['First' . PHP_EOL . 'Second' . PHP_EOL . 'Third', 'not_empty', ['First', 'Second', 'Third']],
        ];
    }

    /**
     * @dataProvider parseBlocksDataDataProvider
     */
    public function testParseBlocksData($sBlocks, $sVendorPrefix, $sModuleName, $aExpectedData)
    {
        $this->assertSame(
            $aExpectedData,
            json_decode(json_encode($this->SUT->parseBlocksData($sBlocks, $sVendorPrefix, $sModuleName)), true)
        );
    }

    public function parseBlocksDataDataProvider()
    {
        return [
            // invalid values
            [
                'details_productmain_titlepage/details/inc/productmain.tpl',
                'oxps',
                'ModuleName',
                [],
            ],
            [
                0,
                'oxps',
                'ModuleName',
                [],
            ],
            [
                null,
                'oxps',
                'ModuleName',
                [],
            ],
            [
                1,
                'oxps',
                'ModuleName',
                [],
            ],

            // valid values
            [
                'details_productmain_title@page/details/inc/productmain.tpl',
                'oxps',
                'ModuleName',
                ['_details_productmain_title' =>
                     [
                         'template' => 'page/details/inc/productmain.tpl',
                         'block'    => 'details_productmain_title',
                         'file'     => 'Application/views/blocks/oxpsModuleName_details_productmain_title.tpl',
                     ],
                ],
            ],
            [
                'details_productmain_title@page/details/inc/productmain.tpl',
                'oxps',
                'ModuleName',
                ['_details_productmain_title' =>
                     [
                         'template' => 'page/details/inc/productmain.tpl',
                         'block'    => 'details_productmain_title',
                         'file'     => 'Application/views/blocks/oxpsModuleName_details_productmain_title.tpl',
                     ],
                ],
            ],
            [
                'details_productmain_title@page/details/inc/productmain.tpl',
                '',
                'ModuleName',
                ['_details_productmain_title' =>
                     [
                         'template' => 'page/details/inc/productmain.tpl',
                         'block'    => 'details_productmain_title',
                         'file'     => 'Application/views/blocks/ModuleName_details_productmain_title.tpl',
                     ],
                ],
            ],

        ];
    }

    /**
     * @dataProvider moduleExistsDataProvider
     */
    public function testModuleExists($sModuleName, $blExpectedValue)
    {
        $this->assertSame($blExpectedValue, $this->SUT->moduleExists($sModuleName));
    }

    public function moduleExistsDataProvider()
    {
        return [
            ['oxps/ModuleGenerator', true],
            ['oxps/modulegenerator', false],
            ['NotExistingModule', false],
            ['', false],
            [[], false],
            [0, false],
            [null, false],
        ];
    }
}
