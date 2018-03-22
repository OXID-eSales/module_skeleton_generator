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
 * Class oxpsModuleGeneratorSettingsTest
 * UNIT tests for core class oxpsModuleGeneratorSettings.
 *
 * @see settings
 */
class oxpsModuleGeneratorSettingsTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var settings
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('settings', array('__call'));
    }


    /**
     * @dataProvider moduleSettingsDataProvider
     */
    public function testGetModuleSettings($sCondition, array $aRawSettings, array $aParsedReturn)
    {
        $this->assertEquals($aParsedReturn, $this->SUT->getModuleSettings($aRawSettings), $sCondition);
    }

    public function moduleSettingsDataProvider()
    {
        return array(

            // Invalid or empty data
            array('Empty input', array(), array()),
            array('Empty setting name', array(array('name' => '', 'type' => 'str', 'value' => 'TEST')), array()),
            array('Invalid setting name', array(array('name' => 'my_setting', 'type' => 'str', 'value' => 'TEST')), array()),
            array('Empty setting type', array(array('name' => 'MySetting', 'type' => '', 'value' => 'TEST')), array()),
            array('Invalid setting type', array(array('name' => 'MySetting', 'type' => 'obj', 'value' => 'TEST')), array()),
            array('Corrupt data structure', array('name' => 'MySetting', 'type' => 'str', 'value' => 'TEST'), array()),

            // Valid data
            array(
                'Boolean setting type - empty value',
                array(array('name' => 'MySetting', 'type' => 'bool', 'value' => '')),
                array((object) array('name' => 'MySetting', 'type' => 'bool', 'value' => "false"))
            ),
            array(
                'Boolean setting type - "false" value',
                array(array('name' => 'MySetting', 'type' => 'bool', 'value' => 'false')),
                array((object) array('name' => 'MySetting', 'type' => 'bool', 'value' => "false"))
            ),
            array(
                'Boolean setting type - "False" value',
                array(array('name' => 'MySetting', 'type' => 'bool', 'value' => 'False')),
                array((object) array('name' => 'MySetting', 'type' => 'bool', 'value' => "false"))
            ),
            array(
                'Boolean setting type - "FALSE" value',
                array(array('name' => 'MySetting', 'type' => 'bool', 'value' => 'FALSE')),
                array((object) array('name' => 'MySetting', 'type' => 'bool', 'value' => "false"))
            ),
            array(
                'Boolean setting type - true value',
                array(array('name' => 'MySetting', 'type' => 'bool', 'value' => 'true')),
                array((object) array('name' => 'MySetting', 'type' => 'bool', 'value' => "true"))
            ),
            array(
                'Boolean setting type - value 1',
                array(array('name' => 'MySetting', 'type' => 'bool', 'value' => '1')),
                array((object) array('name' => 'MySetting', 'type' => 'bool', 'value' => "true"))
            ),
            array(
                'Boolean setting type - value true-like',
                array(array('name' => 'MySetting', 'type' => 'bool', 'value' => 'TEST')),
                array((object) array('name' => 'MySetting', 'type' => 'bool', 'value' => "true"))
            ),

            array(
                'String setting type - empty value',
                array(array('name' => 'MySetting', 'type' => 'str', 'value' => '')),
                array((object) array('name' => 'MySetting', 'type' => 'str', 'value' => "''"))
            ),
            array(
                'String setting type - string value',
                array(array('name' => 'MySetting', 'type' => 'str', 'value' => 'TEST')),
                array((object) array('name' => 'MySetting', 'type' => 'str', 'value' => "'TEST'"))
            ),
            array(
                'String setting type - name with trash',
                array(array('name' => ' MySetting2' . PHP_EOL, 'type' => 'str', 'value' => " TEST\t")),
                array((object) array('name' => 'MySetting2', 'type' => 'str', 'value' => "' TEST\t'"))
            ),

            array(
                'Numeric setting type - empty value',
                array(array('name' => 'MySetting', 'type' => 'num', 'value' => '')),
                array((object) array('name' => 'MySetting', 'type' => 'num', 'value' => 0.0))
            ),
            array(
                'Numeric setting type - value is zero',
                array(array('name' => 'MySetting', 'type' => 'num', 'value' => '0')),
                array((object) array('name' => 'MySetting', 'type' => 'num', 'value' => 0.0))
            ),
            array(
                'Numeric setting type - value is integer',
                array(array('name' => 'MySetting', 'type' => 'num', 'value' => '10')),
                array((object) array('name' => 'MySetting', 'type' => 'num', 'value' => 10.0))
            ),
            array(
                'Numeric setting type - value is float',
                array(array('name' => 'MySetting', 'type' => 'num', 'value' => '10.88')),
                array((object) array('name' => 'MySetting', 'type' => 'num', 'value' => 10.88))
            ),
            array(
                'Numeric setting type - value is a number with trash symbols',
                array(array('name' => 'MySetting', 'type' => 'num', 'value' => ' 10.88 ')),
                array((object) array('name' => 'MySetting', 'type' => 'num', 'value' => 10.88))
            ),

            array(
                'Array setting type - empty value',
                array(array('name' => 'MySetting', 'type' => 'arr', 'value' => '')),
                array((object) array('name' => 'MySetting', 'type' => 'arr', 'value' => "array('')"))
            ),
            array(
                'Array setting type - one line input',
                array(array('name' => 'MySetting', 'type' => 'arr', 'value' => 'item one')),
                array((object) array('name' => 'MySetting', 'type' => 'arr', 'value' => "array('item one')"))
            ),
            array(
                'Array setting type - multi-line input',
                array(array('name' => 'MySetting', 'type' => 'arr', 'value' => 'item one' . PHP_EOL . 'item2')),
                array((object) array('name' => 'MySetting', 'type' => 'arr', 'value' => "array('item one', 'item2')"))
            ),

            array(
                'Assoc array setting type - empty value',
                array(array('name' => 'MySetting', 'type' => 'aarr', 'value' => '')),
                array((object) array('name' => 'MySetting', 'type' => 'aarr', 'value' => "array()"))
            ),
            array(
                'Assoc array setting type - invalid input',
                array(array('name' => 'MySetting', 'type' => 'aarr', 'value' => 'a' . PHP_EOL . 'b')),
                array((object) array('name' => 'MySetting', 'type' => 'aarr', 'value' => "array()"))
            ),
            array(
                'Assoc array setting type - one line input',
                array(array('name' => 'MySetting', 'type' => 'aarr', 'value' => 'a => b')),
                array((object) array('name' => 'MySetting', 'type' => 'aarr', 'value' => "array('a' => 'b')"))
            ),
            array(
                'Assoc array setting type - multi-line input',
                array(array('name' => 'MySetting', 'type' => 'aarr', 'value' => 'a => b' . PHP_EOL . '1=>VALUE')),
                array((object) array('name' => 'MySetting', 'type' => 'aarr', 'value' => "array('a' => 'b', '1' => 'VALUE')"))
            ),

            array(
                'Select setting type - empty value',
                array(array('name' => 'MySetting', 'type' => 'select', 'value' => '')),
                array((object) array('name' => 'MySetting', 'type' => 'select', 'value' => "''", 'constrains' => "''"))
            ),
            array(
                'Select setting type - one line input',
                array(array('name' => 'MySetting', 'type' => 'select', 'value' => 'VAL_1')),
                array((object) array('name' => 'MySetting', 'type' => 'select', 'value' => "'VAL_1'", 'constrains' => "'VAL_1'"))
            ),
            array(
                'Select setting type - multi-line input',
                array(array('name' => 'MySetting', 'type' => 'select', 'value' => 'VAL_1' . PHP_EOL . '2' . PHP_EOL . 'b')),
                array((object) array('name' => 'MySetting', 'type' => 'select', 'value' => "'VAL_1'", 'constrains' => "'VAL_1|2|b'"))
            ),
        );
    }
}
