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
 * Class oxpsModuleGeneratorMetadataTest
 * UNIT tests for core class oxpsModuleGeneratorMetadata.
 *
 * @see oxpsModuleGenratorMetadata
 */
class oxpsModuleGeneratorMetadataTest extends OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var oxpsModuleGeneratorMetadata|PHPUnit_Framework_MockObject_MockObject
     */
    protected $SUT;

    /**
     * Vendor prefix for tests.
     *
     * @var string
     */
    protected $_sVendorPrefix = 'oxps';

    /**
     * Module name for tests.
     *
     * @var string
     */
    protected $_sModuleName = 'TestModule';

    /**
     * Module id for correct metadada parse process.
     *
     * @var string
     */
    protected $_sModuleId = 'oxpstestmodule';

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('oxpsModuleGeneratorMetadata', array('__call'));
    }

    public function testParseMetadata_parseEmptyMetadata()
    {
        $aMetadata = [];
        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [],
            'aNewModels'      => [],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName)
        );
    }

    public function testParseMetadata_parseExtendedClasses()
    {
        $aMetadata = [
            'id'     => $this->_sModuleId,
            'extend' => [
                // invalid values
                'notExistingClass'                          => 'notExistingClass',
                ''                                          => '',
                0                                           => 0,
                null                                        => null,
                1                                           => 1,
                'array'                                     => [],
                // valid values
                \OxidEsales\Eshop\Core\Module\Module::class => 'oxps/TestModule/Core/oxpsTestModuleOxModule',

            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [
                'OxidEsales\Eshop\Core\Module\Module' => 'Core/',
            ],
            'aNewControllers' => [],
            'aNewModels'      => [],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName)
        );
    }

    public function testParseMetadata_parseControllers()
    {
        $this->markTestIncomplete('Fix is needed for str_replace()');

        $aMetadata = [
            'id'    => $this->_sModuleId,
            'files' => [
                'Admin_oxpsTestModule'       => 'oxps/TestModule/Application/Controller/Admin/Admin_oxpsTestModule.php',
                'Admin_oxpsAjaxDataProvider' => 'oxps/TestModule/Application/Controller/Admin/Admin_oxpsAjaxDataProvider.php',
            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [
                0 => 'Admin_', // TODO: FIX THIS!!!
                1 => 'Admin_oxpsAjaxDataProvider',
            ],
            'aNewModels'      => [],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName)
        );
    }

    public function testParseMetadata_parseModels()
    {
        $aMetadata = [
            'id'    => $this->_sModuleId,
            'files' => [
                // valid values
                'ModelName'                => 'oxps/TestModule/Application/Model/oxpsTestModuleModel.php',
                // invalid values
                'oxpsTestModuleFileSystem' => 'oxps/TestModule/Core/oxpsTestModuleFileSystem.php',
            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [],
            'aNewModels'      => [
                0 => 'ModelName',
            ],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName)
        );
    }

    public function testParseMetadata_parseLists()
    {
        $aMetadata = [
            'id'    => $this->_sModuleId,
            'files' => [
                // valid values
                'ModelList'                => 'oxps/TestModule/Application/Model/oxpsTestModuleList.php',
                // invalid values
                'oxpsTestModuleFileSystem' => 'oxps/TestModule/Core/oxpsTestModuleFileSystem.php',
            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [],
            'aNewModels'      => [],
            'aNewLists'       => [
                0 => 'ModelList',
            ],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName)
        );
    }

    public function testParseMetadata_parseWidgets()
    {
        $aMetadata = [
            'id'    => $this->_sModuleId,
            'files' => [
                // valid values
                'Widget'                   => 'oxps/TestModule/Application/Component/Widget/oxpsTestModuleWidget.php',
                // invalid values
                'oxpsTestModuleFileSystem' => 'oxps/TestModule/Core/oxpsTestModuleFileSystem.php',
            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [],
            'aNewModels'      => [],
            'aNewLists'       => [],
            'aNewWidgets'     => [
                0 => 'Widget',
            ],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName)
        );
    }

    public function testParseMetadata_parseBlocks()
    {
        $aMetadata = [
            'id'     => $this->_sModuleId,
            'blocks' => [
                [
                    'template' => 'page/details/inc/productmain.tpl',
                    'block'    => 'details_productmain_title',
                    'file'     => 'Application/views/blocks/oxpsTestModule_details_productmain_title.tpl',
                ],
            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [],
            'aNewModels'      => [],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [
                '_details_productmain_title' => (object) [
                    'template' => 'page/details/inc/productmain.tpl',
                    'block'    => 'details_productmain_title',
                    'file'     => 'Application/views/blocks/oxpsTestModule_details_productmain_title.tpl',
                ],
            ],
            'aModuleSettings' => [],
        ];

        $this->assertEquals(
            $aExpectedValue, $result = $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName)
        );
    }

    public function testParseMetadata_parseSettings()
    {
        $aMetadata = [
            'id'       => $this->_sModuleId,
            'settings' => [
                [
                    'group' => 'oxpsTestModuleVendor',
                    'name'  => 'oxpsTestModuleVendorPrefix',
                    'type'  => 'str',
                    'value' => '',
                ],
                [
                    'group' => 'oxpsTestModuleVendor',
                    'name'  => 'oxpsTestModuleModuleAuthor',
                    'type'  => 'str',
                    'value' => '',
                ],
                [
                    'group' => 'oxpsTestModuleVendor',
                    'name'  => 'oxpsTestModuleAuthorLink',
                    'type'  => 'str',
                    'value' => '',
                ],
                [
                    'group' => 'oxpsTestModuleVendor',
                    'name'  => 'oxpsTestModuleAuthorMail',
                    'type'  => 'str',
                    'value' => '',
                ],
                [
                    'group' => 'oxpsTestModuleVendor',
                    'name'  => 'oxpsTestModuleCopyright',
                    'type'  => 'str',
                    'value' => '',
                ],
                [
                    'group' => 'oxpsTestModuleVendor',
                    'name'  => 'oxpsTestModuleComment',
                    'type'  => 'arr',
                    'value' => [],
                ],
            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [],
            'aNewModels'      => [],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [
                0 => [
                    'name'  => 'VendorPrefix',
                    'type'  => 'str',
                    'value' => '',
                ],
                1 => [
                    'name'  => 'ModuleAuthor',
                    'type'  => 'str',
                    'value' => '',
                ],
                2 => [
                    'name'  => 'AuthorLink',
                    'type'  => 'str',
                    'value' => '',
                ],
                3 => [
                    'name'  => 'AuthorMail',
                    'type'  => 'str',
                    'value' => '',
                ],
                4 => [
                    'name'  => 'Copyright',
                    'type'  => 'str',
                    'value' => '',
                ],
                5 => [
                    'name'  => 'Comment',
                    'type'  => 'arr',
                    'value' => [],
                ],
            ],
        ];

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName)
        );
    }
}