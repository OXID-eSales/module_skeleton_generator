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
 * Class oxpsModuleGeneratorOxModuleTest
 * UNIT/INTEGRATION tests for core class oxpsModuleGeneratorOxModule.
 * NOTE: This test class does not mock validator instance.
 *
 * @see oxpsModuleGeneratorOxModule
 */
class oxpsModuleGeneratorOxModuleTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var oxpsModuleGeneratorOxModule|\OxidEsales\EshopCommunity\Core\Module|
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('oxpsModuleGeneratorOxModule', array('__call'));
    }


    public function testGetVendorPrefix_nothingSet_returnEmptyString()
    {
        $this->assertSame('', $this->SUT->getVendorPrefix());
    }

    public function testGetVendorPrefix_valueSet_returnTheValue()
    {
        $this->SUT->setVendorPrefix('oxps');

        $this->assertSame('oxps', $this->SUT->getVendorPrefix());
    }

    public function testGetVendorPrefix_valueSetArgumentIsTrue_returnTheValueInUppercase()
    {
        $this->SUT->setVendorPrefix('oxps');

        $this->assertSame('OXPS', $this->SUT->getVendorPrefix(true));
    }


    public function testGetAuthorData_nothingSet_returnEmptyArray()
    {
        $this->assertSame(array(), $this->SUT->getAuthorData());
    }

    public function testGetAuthorData_dataSet_returnDataArray()
    {
        $this->SUT->setAuthorData(array('email' => 'vendor@example.com', 'url' => 'www.example.com'));

        $this->assertSame(
            array('email' => 'vendor@example.com', 'url' => 'www.example.com'),
            $this->SUT->getAuthorData()
        );
    }

    public function testGetAuthorData_argumentDoesNotMatchDataKey_returnEmptyString()
    {
        $this->SUT->setAuthorData(array('email' => 'vendor@example.com', 'url' => 'www.example.com'));

        $this->assertSame('', $this->SUT->getAuthorData('author'));
    }

    public function testGetAuthorData_argumentMatchesDataKey_returnArrayValueByTheKey()
    {
        $this->SUT->setAuthorData(array('email' => 'vendor@example.com', 'url' => 'www.example.com'));

        $this->assertSame('vendor@example.com', $this->SUT->getAuthorData('email'));
    }


    public function testGetValidator()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorValidator', $this->SUT->getValidator());
    }


    public function testGetModuleId_noDataSet_returnNull()
    {
        $this->assertNull($this->SUT->getModuleId());
    }

    public function testGetModuleId_moduleIdSet_returnTheValue()
    {
        $this->SUT->setModuleData(array('id' => 'oxpstestmodule'));

        $this->assertSame('oxpstestmodule', $this->SUT->getModuleId(false));
    }

    public function testGetModuleId_argumentIsTrueNoAdditionalDataSet_returnNull()
    {
        $this->SUT->setModuleData(array('id' => 'oxpstestmodule'));

        $this->assertNull($this->SUT->getModuleId(true));
    }

    public function testGetModuleId_argumentIsTrueAdditionalDataIsSet_returnCamelCaseValue()
    {
        $this->SUT->setModuleData(
            array(
                'id'                        => 'oxpstestmodule',
                'oxpsmodulegenerator_class' => 'oxpsTestModule',
            )
        );

        $this->assertSame('oxpsTestModule', $this->SUT->getModuleId(true));
    }


    public function testGetModuleFolderName_noDataSet_returnNull()
    {
        $this->assertNull($this->SUT->getModuleFolderName());
    }

    public function testGetModuleFolderName_folderNameSet_returnTheValue()
    {
        $this->SUT->setModuleData(array('oxpsmodulegenerator_folder' => 'testmodule'));

        $this->assertSame('testmodule', $this->SUT->getModuleFolderName());
    }

    public function testGetModuleFolderName_argumentIsTrueNoAdditionalDataSet_returnNull()
    {
        $this->SUT->setModuleData(array('id' => 'oxpstestmodule'));

        $this->assertNull($this->SUT->getModuleFolderName(true));
    }

    public function testGetModuleFolderName_argumentIsTrueAdditionalDataIsSet_returnUppercaseValue()
    {
        $this->SUT->setModuleData(
            array(
                'id'                         => 'oxpstestmodule',
                'oxpsmodulegenerator_folder' => 'testmodule',
            )
        );

        $this->assertSame('TESTMODULE', $this->SUT->getModuleFolderName(true));
    }


    public function testGetModuleClassName_argumentIsTrueNoAdditionalDataSet_returnNull()
    {
        $this->SUT->setModuleData(array('id' => 'oxpstestmodule'));

        $this->assertNull($this->SUT->getModuleClassName());
    }

    public function testGetModuleClassName_argumentIsTrueAdditionalDataIsSet_returnCamelCaseValue()
    {
        $this->SUT->setModuleData(
            array(
                'id'                        => 'oxpstestmodule',
                'oxpsmodulegenerator_class' => 'oxpsTestModule',
            )
        );

        $this->assertSame('oxpsTestModule', $this->SUT->getModuleClassName());
    }


    public function testGetClassesToExtend_noDataSet_returnEmptyArray()
    {
        $this->assertSame(array(), $this->SUT->getClassesToExtend());
    }

    public function testGetClassesToExtend_dataIsSet_returnTheSetData()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_extend_classes' => array('oxarticle', 'oxlist'),
            )
        );

        $this->assertSame(array('oxarticle', 'oxlist'), $this->SUT->getClassesToExtend());
    }


    public function testGetClassesToCreate_noDataSetNoArguments_returnEmptyStructureArray()
    {
        $aReturn = $this->SUT->getClassesToCreate();

        $this->assertInternalType('array', $aReturn);

        $this->assertArrayHasKey('widgets', $aReturn);
        $this->assertArrayHasKey('controllers', $aReturn);
        $this->assertArrayHasKey('models', $aReturn);
        $this->assertArrayHasKey('list_models', $aReturn);

        $this->assertArrayHasKey('aClasses', $aReturn['widgets']);
        $this->assertArrayHasKey('sTemplateName', $aReturn['widgets']);
        $this->assertArrayHasKey('sInModulePath', $aReturn['widgets']);
        $this->assertArrayHasKey('sTemplatesPath', $aReturn['widgets']);

        $this->assertSame(array(), $aReturn['widgets']['aClasses']);
        $this->assertSame('oxpsWidgetClass.php.tpl', $aReturn['widgets']['sTemplateName']);
        $this->assertSame('Application/Component/Widget/', $aReturn['widgets']['sInModulePath']);
        $this->assertSame('widgets', $aReturn['widgets']['sTemplatesPath']);

        $this->assertArrayHasKey('aClasses', $aReturn['controllers']);
        $this->assertArrayHasKey('sTemplateName', $aReturn['controllers']);
        $this->assertArrayHasKey('sInModulePath', $aReturn['controllers']);
        $this->assertArrayHasKey('sTemplatesPath', $aReturn['controllers']);

        $this->assertSame(array(), $aReturn['controllers']['aClasses']);
        $this->assertSame('oxpsControllerClass.php.tpl', $aReturn['controllers']['sTemplateName']);
        $this->assertSame('Application/Controller/', $aReturn['controllers']['sInModulePath']);
        $this->assertSame('pages', $aReturn['controllers']['sTemplatesPath']);

        $this->assertArrayHasKey('aClasses', $aReturn['models']);
        $this->assertArrayHasKey('sTemplateName', $aReturn['models']);
        $this->assertArrayHasKey('sInModulePath', $aReturn['models']);

        $this->assertSame(array(), $aReturn['models']['aClasses']);
        $this->assertSame('oxpsModelClass.php.tpl', $aReturn['models']['sTemplateName']);
        $this->assertSame('Application/Model/', $aReturn['models']['sInModulePath']);

        $this->assertArrayHasKey('aClasses', $aReturn['list_models']);
        $this->assertArrayHasKey('sTemplateName', $aReturn['list_models']);
        $this->assertArrayHasKey('sInModulePath', $aReturn['list_models']);

        $this->assertSame(array(), $aReturn['list_models']['aClasses']);
        $this->assertSame('oxpsListModelClass.php.tpl', $aReturn['list_models']['sTemplateName']);
        $this->assertSame('Application/Model/', $aReturn['list_models']['sInModulePath']);
    }

    public function testGetClassesToCreate_classesDataSet_returnDataStructureArrayWithClassesLists()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_widgets'     => array('Bar', 'Menu'),
                'oxpsmodulegenerator_controllers' => array('Page', 'MyView', 'OtherPage'),
                'oxpsmodulegenerator_models'      => array('Item', 'Thing'),
                'oxpsmodulegenerator_list_models' => array('Item'),
            )
        );

        $aReturn = $this->SUT->getClassesToCreate();

        $this->assertInternalType('array', $aReturn);

        $this->assertArrayHasKey('widgets', $aReturn);
        $this->assertArrayHasKey('controllers', $aReturn);
        $this->assertArrayHasKey('models', $aReturn);
        $this->assertArrayHasKey('list_models', $aReturn);

        $this->assertArrayHasKey('aClasses', $aReturn['widgets']);
        $this->assertArrayHasKey('aClasses', $aReturn['controllers']);
        $this->assertArrayHasKey('aClasses', $aReturn['models']);
        $this->assertArrayHasKey('aClasses', $aReturn['list_models']);

        $this->assertSame(array('Bar', 'Menu'), $aReturn['widgets']['aClasses']);
        $this->assertSame(array('Page', 'MyView', 'OtherPage'), $aReturn['controllers']['aClasses']);
        $this->assertSame(array('Item', 'Thing'), $aReturn['models']['aClasses']);
        $this->assertSame(array('Item'), $aReturn['list_models']['aClasses']);
    }

    public function testGetClassesToCreate_noExistingObjectArgument_returnEmptyArray()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_widgets'     => array('Bar', 'Menu'),
                'oxpsmodulegenerator_controllers' => array('Page', 'MyView', 'OtherPage'),
                'oxpsmodulegenerator_models'      => array('Item', 'Thing'),
                'oxpsmodulegenerator_list_models' => array('Item'),
            )
        );

        $aReturn = $this->SUT->getClassesToCreate('extend');

        $this->assertSame(array(), $aReturn);
    }

    public function testGetClassesToCreate_existingObjectArgument_returnOnlyThatObjectTypeData()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_widgets'     => array('Bar', 'Menu'),
                'oxpsmodulegenerator_controllers' => array('Page', 'MyView', 'OtherPage'),
                'oxpsmodulegenerator_models'      => array('Item', 'Thing'),
                'oxpsmodulegenerator_list_models' => array('Item'),
            )
        );

        $aReturn = $this->SUT->getClassesToCreate('controllers');

        $this->assertArrayHasKey('aClasses', $aReturn);
        $this->assertSame(array('Page', 'MyView', 'OtherPage'), $aReturn['aClasses']);
    }

    public function testGetClassesToCreate_notExistingParameterArgument_returnEmptyString()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_widgets'     => array('Bar', 'Menu'),
                'oxpsmodulegenerator_controllers' => array('Page', 'MyView', 'OtherPage'),
                'oxpsmodulegenerator_models'      => array('Item', 'Thing'),
                'oxpsmodulegenerator_list_models' => array('Item'),
            )
        );

        $aReturn = $this->SUT->getClassesToCreate('controllers', 'version');

        $this->assertSame('', $aReturn);
    }

    public function testGetClassesToCreate_existingParameterArgument_returnOnlySpecificObjectParam()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_widgets'     => array('Bar', 'Menu'),
                'oxpsmodulegenerator_controllers' => array('Page', 'MyView', 'OtherPage'),
                'oxpsmodulegenerator_models'      => array('Item', 'Thing'),
                'oxpsmodulegenerator_list_models' => array('Item'),
            )
        );

        $aReturn = $this->SUT->getClassesToCreate('controllers', 'sTemplatesPath');

        $this->assertSame('pages', $aReturn);
    }

    public function testGetClassesToCreate_classesParam_returnArrayValue()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_widgets'     => array('Bar', 'Menu'),
                'oxpsmodulegenerator_controllers' => array('Page', 'MyView', 'OtherPage'),
                'oxpsmodulegenerator_models'      => array('Item', 'Thing'),
                'oxpsmodulegenerator_list_models' => array('Item'),
            )
        );

        $aReturn = $this->SUT->getClassesToCreate('controllers', 'aClasses');

        $this->assertSame(array('Page', 'MyView', 'OtherPage'), $aReturn);
    }


    public function testGetBlocks_noDataSet_returnEmptyArray()
    {
        $this->assertSame(array(), $this->SUT->getBlocks());
    }

    public function testGetBlocks_dataIsSet_returnTheSetData()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_blocks' => array(
                    array('template' => 'page.tpl', 'block' => 'block', 'file' => 'block.tpl')
                ),
            )
        );

        $this->assertSame(
            array(array('template' => 'page.tpl', 'block' => 'block', 'file' => 'block.tpl')),
            $this->SUT->getBlocks()
        );
    }


    public function testGetSettings_noDataSet_returnEmptyArray()
    {
        $this->assertSame(array(), $this->SUT->getSettings());
    }

    public function testGetSettings_dataIsSet_returnTheSetData()
    {
        $this->SUT->setModuleData(
            array(
                'oxpsmodulegenerator_module_settings' => array(
                    array('name' => 'setting', 'type' => 'str', 'value' => 'VALUE')
                ),
            )
        );

        $this->assertSame(
            array(array('name' => 'setting', 'type' => 'str', 'value' => 'VALUE')),
            $this->SUT->getSettings()
        );
    }


    /**
     * @dataProvider selectOptionsDataProvider
     */
    public function testGetSelectSettingOptions($mArgument, $mExpectedReturn)
    {
        $this->assertSame($mExpectedReturn, $this->SUT->getSelectSettingOptions($mArgument));
    }

    public function selectOptionsDataProvider()
    {
        return array(
            array('', array('')),
            array('a', array('a')),
            array('a b', array('a b')),
            array('a,b', array('a,b')),
            array('a|b', array('a', 'b')),
            array("'a|b'", array('a', 'b')),
            array("'a'|'b'", array('a', 'b')),
            array('a|b|c e', array('a', 'b', 'c e')),
            array('a|b|', array('a', 'b', '')),
            array('|', array('', '')),
        );
    }


    public function testGetInitialVersion_noDataSet_returnEmptyString()
    {
        $this->assertSame('', $this->SUT->getInitialVersion());
    }

    public function testGetInitialVersion_versionIsSet_returnTheSetValue()
    {
        $this->SUT->setModuleData(array('oxpsmodulegenerator_module_init_version' => '0.0.1 beta'));

        $this->assertSame('0.0.1 beta', $this->SUT->getInitialVersion());
    }


    public function testRenderTasks_noDataSet_returnFalse()
    {
        $this->assertFalse($this->SUT->renderTasks());
    }

    public function testRenderTasks_valueIsSet_returnTrue()
    {
        $this->SUT->setModuleData(array('oxpsmodulegenerator_render_tasks' => true));

        $this->assertTrue($this->SUT->renderTasks());
    }


    public function testRenderSamples_noDataSet_returnFalse()
    {
        $this->assertFalse($this->SUT->renderSamples());
    }

    public function testRenderSamples_valueIsSet_returnTrue()
    {
        $this->SUT->setModuleData(array('oxpsmodulegenerator_render_samples' => true));

        $this->assertTrue($this->SUT->renderSamples());
    }


    public function testGetVendorPath()
    {
        // Config mock
        $oConfig = $this->getMock('oxConfig', array('getModulesDir'));
        $oConfig->expects($this->once())->method('getModulesDir')->will($this->returnValue('/path/to/modules/'));

        oxRegistry::set('oxConfig', $oConfig);

        $this->SUT->setVendorPrefix('oxps');

        $this->assertSame('/path/to/modules/oxps/', $this->SUT->getVendorPath());
    }


    public function testGetFullPath()
    {
        // Config mock
        $oConfig = $this->getMock('oxConfig', array('getModulesDir'));
        $oConfig->expects($this->once())->method('getModulesDir')->will($this->returnValue('/path/to/modules/'));

        oxRegistry::set('oxConfig', $oConfig);

        $this->SUT->setVendorPrefix('oxps');
        $this->SUT->setModuleData(array('oxpsmodulegenerator_folder' => 'testmodule'));

        $this->assertSame('/path/to/modules/oxps/testmodule/', $this->SUT->getFullPath());
    }


    public function testGenerateModule()
    {
        // Config mock
        $oConfig = $this->getMock('oxConfig', array('getModulesDir'));
        $oConfig->expects($this->any())->method('getModulesDir')->will($this->returnValue('/path/to/modules/'));
        oxRegistry::set('oxConfig', $oConfig);

        // The generator module main class mock
        $oGeneratorModule = $this->getMock('oxpsModuleGeneratorModule', array('__construct', '__call', 'getPath'));

        oxRegistry::set('oxpsModuleGeneratorModule', $oGeneratorModule);

        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'copyFolder'));
        $oFileSystem->expects($this->once())->method('copyFolder')->with(
            '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/module/',
            '/path/to/modules/oxps/MyModule/'
        );

        // Render helper mock
        $oRenderHelper = $this->getMock('oxpsModuleGeneratorRender', array('__call', 'init', 'renderModuleFiles'));
        $oRenderHelper->expects($this->once())->method('init')->with($this->isInstanceOf(get_class($this->SUT)));
        $oRenderHelper->expects($this->once())->method('renderModuleFiles')->with(
            array('models/oxpsmymoduleoxarticle.php' => 'oxArticle'),
            array('models/oxpsmymoduleitem.php' => 'oxpsMyModuleItem')
        );
        oxRegistry::set('oxpsModuleGeneratorRender', $oRenderHelper);

        // Module generation helper mock
        $oHelper = $this->getMock(
            'oxpsModuleGeneratorHelper',
            array(
                '__call',
                'init',
                'createVendorMetadata',
                'getFileSystemHelper',
                'createClassesToExtend',
                'createNewClassesAndTemplates',
                'createBlock',
                'fillTestsFolder',
            )
        );
        $oHelper->expects($this->once())->method('init')->with($this->isInstanceOf(get_class($this->SUT)));
        $oHelper->expects($this->once())->method('createVendorMetadata')->with('/path/to/modules/oxps/');
        $oHelper->expects($this->once())->method('getFileSystemHelper')->will($this->returnValue($oFileSystem));
        $oHelper->expects($this->once())->method('createClassesToExtend')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsExtendClass.php.tpl')
            ->will($this->returnValue(array('models/oxpsmymoduleoxarticle.php' => 'oxArticle')));
        $oHelper->expects($this->once())->method('createNewClassesAndTemplates')
            ->with('/path/to/modules/oxps/ModuleGenerator/')
            ->will($this->returnValue(array('models/oxpsmymoduleitem.php' => 'oxpsMyModuleItem')));
        $oHelper->expects($this->once())->method('createBlock')->with('/path/to/modules/oxps/MyModule/');
        $oHelper->expects($this->once())->method('fillTestsFolder')->with(
            $oRenderHelper,
            '/path/to/modules/oxps/ModuleGenerator/',
            array('models/oxpsmymoduleoxarticle.php' => 'oxArticle'),
            array('models/oxpsmymoduleitem.php' => 'oxpsMyModuleItem')
        );
        oxRegistry::set('oxpsModuleGeneratorHelper', $oHelper);

        $this->SUT->setVendorPrefix('oxps');

        $this->assertTrue(
            $this->SUT->generateModule(
                'MyModule',
                array(
                    'aExtendClasses'   => array('oxarticle' => 'models/'),
                    'aNewControllers'  => array(),
                    'aNewModels'       => array('Item'),
                    'aNewLists'        => array(),
                    'aNewWidgets'      => array(),
                    'aNewBlocks'       => array(),
                    'aModuleSettings'  => array(
                        array(
                            'name'  => 'MySetting',
                            'type'  => 'str',
                            'value' => '',
                        )
                    ),
                    'sInitialVersion'  => '0.1 alpha',
                    'blFetchUnitTests' => true,
                    'blRenderTasks'    => true,
                    'blRenderSamples'  => true,
                )
            )
        );

        $this->assertSame('oxps', $this->SUT->getVendorPrefix());
        $this->assertSame(array(), $this->SUT->getAuthorData());
        $this->assertSame('oxpsmymodule', $this->SUT->getModuleId(false));
        $this->assertSame('MyModule', $this->SUT->getModuleFolderName());
        $this->assertSame('oxpsMyModule', $this->SUT->getModuleClassName());
        $this->assertSame('OXPS My Module', $this->SUT->getTitle());
        $this->assertSame(array('oxarticle' => 'models/'), $this->SUT->getClassesToExtend());
        $this->assertInternalType('array', $this->SUT->getClassesToCreate());
        $this->assertSame(array(), $this->SUT->getBlocks());
        $this->assertEquals(
            array((object) array('name' => 'MySetting', 'type' => 'str', 'value' => "''")),
            $this->SUT->getSettings()
        );
        $this->assertSame('0.1 alpha', $this->SUT->getInitialVersion());
        $this->assertSame(true, $this->SUT->renderTasks());
        $this->assertSame(true, $this->SUT->renderSamples());
    }


    public function testValidateModuleName_nameInvalid_returnFalse()
    {
        $this->assertFalse($this->SUT->validateModuleName('badName'));
    }

    public function testValidateModuleName_moduleAlreadyExists_returnTrue()
    {
        $this->assertTrue($this->SUT->validateModuleName('ModuleGenerator'));
    }

    public function testValidateModuleName_moduleNameValidAndNew_returnTrue()
    {
        $this->assertTrue($this->SUT->validateModuleName('MyModule'));
    }


    /**
     * @dataProvider filePathsDataProvider
     */
    public function testGetFileNameSuffix($sFilePath, $sExpectedSuffix)
    {
        $SUT = $this->GetMock('oxpsModuleGeneratorOxModule', array('__call', 'getInfo'));
        $SUT->expects($this->any())->method('getInfo')
            ->will($this->returnValue('oxpsmymodule'))
        ;

        /** @var oxpsModuleGeneratorOxModule $SUT */
        $this->assertSame($sExpectedSuffix, $SUT->getFileNameSuffix($sFilePath));
    }

    public function filePathsDataProvider()
    {
        return array(
            array('', ''),
            array('oxpsmymodule.php', ''),
            array('oemymoduleitem.php', 'oemymoduleitem'),
            array('oxpssomemoduleitem.php', 'oxpssomemoduleitem'),
            array('oxpsmymoduleitem.php', 'item'),
            array('path/to/oxpsmymoduleitem.php', 'item'),
            array('path/to/oxpsmymoduleitem.tpl', 'item'),
        );
    }


    public function testRenderFileComment()
    {
        // Render helper mock
        $oHelper = $this->getMock('oxpsModuleGeneratorRender', array('__call', 'init', 'renderFileComment'));
        $oHelper->expects($this->once())->method('init')->with($this->isInstanceOf(get_class($this->SUT)));
        $oHelper->expects($this->once())->method('renderFileComment')->with('');
        oxRegistry::set('oxpsModuleGeneratorRender', $oHelper);

        $this->SUT->renderFileComment();
    }
}
