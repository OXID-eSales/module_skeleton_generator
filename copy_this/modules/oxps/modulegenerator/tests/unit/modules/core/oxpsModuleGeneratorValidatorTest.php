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

/**
 * Class oxpsModuleGeneratorValidatorTest
 * UNIT tests for core class oxpsModuleGeneratorValidator.
 *
 * @see oxpsModuleGeneratorValidator
 */
class oxpsModuleGeneratorValidatorTest extends OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var oxpsModuleGeneratorValidator|PHPUnit_Framework_MockObject_MockObject
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

    // TODO: finish test
    public function testValidateAndLinkClasses()
    {
    }

    // TODO: finish test
    public function testParseMultiLineInput()
    {
    }

    // TODO: finish test
    public function testParseBlocksData()
    {
    }

    // TODO: finish test
    public function testModuleExists()
    {
    }


}
