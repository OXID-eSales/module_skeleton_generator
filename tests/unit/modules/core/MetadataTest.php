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

use org\bovigo\vfs\vfsStream;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\TestingLibrary\UnitTestCase;
use Oxps\ModuleGenerator\Core\Metadata;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class MetadataTest
 * UNIT tests for core class Metadata.
 *
 * @see Metadata
 */
class MetadataTest extends UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var Metadata|PHPUnit_Framework_MockObject_MockObject
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
    protected $_sModulePath = '/var/www/oxideshop/source/modules/oxps/TestModule';

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock(Metadata::class);
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

        $aStructure = [
            'Application' => [
                'Component' => [
                    'Widget' => []
                ],
                'Model' => []
            ]
        ];

        $oModuleDir = vfsStream::setup($this->_sModuleName);
        vfsStream::create($aStructure, $oModuleDir);

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName, $oModuleDir->url())
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
                Module::class => 'oxps/TestModule/Core/oxpsTestModuleOxModule',

            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [
                'OxidEsales\Eshop\Core\Module\Module' => [
                    'classPath' =>'Core/',
                    'v6ClassName' =>'Module',
                    'v6Namespace' => 'OxidEsales\Eshop\Core\Module'
                    ]
            ],
            'aNewControllers' => [],
            'aNewModels'      => [],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $aStructure = [
            'Application' => [
                'Component' => [
                    'Widget' => []
                ],
                'Model' => []
            ]
        ];

        $oModuleDir = vfsStream::setup($this->_sModuleName);
        vfsStream::create($aStructure, $oModuleDir);

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName, $oModuleDir->url())
        );
    }

    public function testParseMetadata_parseControllers()
    {
        $aMetadata = [
            'id'    => $this->_sModuleId,
            'controllers' => [
                'oxps_test_testcontroller'       => 'Oxps\Test\Application\Controller\TestController',
            ],
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [
                0 => 'TestController',
            ],
            'aNewModels'      => [],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $aStructure = [
            'Application' => [
                'Component' => [
                    'Widget' => []
                ],
                'Model' => []
            ]
        ];

        $oModuleDir = vfsStream::setup($this->_sModuleName);
        vfsStream::create($aStructure, $oModuleDir);

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName, $oModuleDir->url())
        );
    }

    public function testParseMetadata_parseModels()
    {
        $aMetadata = [
            'id'    => $this->_sModuleId
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [],
            'aNewModels'      => [
                0 => 'AnotherModel',
                1 => 'TestModel'
            ],
            'aNewLists'       => [],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $aStructure = [
          'Application' => [
              'Component' => [
                  'Widget' => []
              ],
              'Model' => [
                  'TestModel.php' => 'content',
                  'AnotherModel.php' => 'content'
              ]
          ]
        ];

        // Mock file system. Models get parsed from a directory, since they're no longer in metadata v2.0.
        $oModuleDir = vfsStream::setup($this->_sModuleName);
        vfsStream::create($aStructure, $oModuleDir);

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName, $oModuleDir->url())
        );
    }

    public function testParseMetadata_parseLists()
    {
        $aMetadata = [
            'id'    => $this->_sModuleId
        ];

        $aExpectedValue = [
            'aExtendClasses'  => [],
            'aNewControllers' => [],
            'aNewModels'      => [],
            'aNewLists'       => [
                0 => 'TestModelList',
            ],
            'aNewWidgets'     => [],
            'aNewBlocks'      => [],
            'aModuleSettings' => [],
        ];

        $aStructure = [
            'Application' => [
                'Component' => [
                    'Widget' => []
                ],
                'Model' => [
                    'TestModelList.php' => 'content',   // Valid Model List
                    'otherClass.php' => 'content'       // Non list class
                ]
            ]
        ];

        // Mock file system. Model lists get parsed from a directory, since they're no longer in metadata v2.0.
        $oModuleDir = vfsStream::setup($this->_sModuleName);
        vfsStream::create($aStructure, $oModuleDir);

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName, $oModuleDir->url())
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

        $aStructure = [
            'Application' => [
                'Component' => [
                    'Widget' => [
                        'Widget.php' => 'content',       //valid
                        'random.txt' => 'why am i here', //invalid
                        'unknownfile' => 'no extension' //invalid
                    ]
                ],
                'Model' => []
            ]
        ];

        // Mock file system. Widgets get parsed from a directory, since they're no longer in metadata v2.0.
        $oModuleDir = vfsStream::setup($this->_sModuleName);
        vfsStream::create($aStructure, $oModuleDir);

        $this->assertSame(
            $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName, $oModuleDir->url())
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

        $aStructure = [
            'Application' => [
                'Component' => [
                    'Widget' => []
                ],
                'Model' => []
            ]
        ];

        $oModuleDir = vfsStream::setup($this->_sModuleName);
        vfsStream::create($aStructure, $oModuleDir);

        $this->assertEquals(
            $aExpectedValue, $result = $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName, $oModuleDir->url())
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

        $aStructure = [
            'Application' => [
                'Component' => [
                    'Widget' => []
                ],
                'Model' => []
            ]
        ];

        $oModuleDir = vfsStream::setup($this->_sModuleName);
        vfsStream::create($aStructure, $oModuleDir);
    
        try {
            $this->assertSame(
                $aExpectedValue, $this->SUT->parseMetadata($aMetadata, $this->_sVendorPrefix, $this->_sModuleName, $oModuleDir->url())
            );
        } catch (\ReflectionException $e) {
        }
    }
}