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

if (!class_exists('Smarty')) {
    include dirname(__FILE__) . '../../../../../../../../../vendor/smarty/smarty/libs/Smarty.class.php';
}


/**
 * Class Admin_oxpsModuleGeneratorTest
 * INTEGRATION tests for controller class Admin_oxpsModuleGenerator.
 *
 * @see Admin_oxpsModuleGenerator
 */
class Admin_oxpsModuleGeneratorTest extends OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var Admin_oxpsModuleGenerator
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     * Create test folder for modules generation.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock(
            'Admin_oxpsModuleGenerator',
            array('_Admin_oxpsModuleGenerator_init_parent', '_Admin_oxpsModuleGenerator_render_parent')
        );

        // Mock config for module settings
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'test');
        $oConfig->setConfigParam('oxpsModuleGeneratorModuleAuthor', 'TEST');
        $oConfig->setConfigParam('oxpsModuleGeneratorAuthorLink', 'www.example.com');
        $oConfig->setConfigParam('oxpsModuleGeneratorAuthorMail', 'test@example.com');
        $oConfig->setConfigParam('oxpsModuleGeneratorCopyright', 'TEST ');
        $oConfig->setConfigParam('oxpsModuleGeneratorComment', '* This is automatically generated test output.');
        oxRegistry::set('oxConfig', $oConfig);

        // Module instance mock for generation path only
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('getVendorPath'));
        $oModule->expects($this->any())->method('getVendorPath')->will(
            $this->returnValue($this->_getTestPath('modules/test/'))
        );
        oxTestModules::addModuleObject('oxpsModuleGeneratorOxModule', $oModule);

        @mkdir($this->_getTestPath());
        @mkdir($this->_getTestPath('modules'));
    }

    /**
     * Clean state after test.
     * Remove test folders and generated module files.
     */
    public function tearDown()
    {
        @shell_exec('rm -rf ' . $this->_getTestPath());

        parent::tearDown();
    }


    public function testInit()
    {
        $this->assertNull($this->SUT->getModule());

        $this->SUT->expects($this->once())->method('_Admin_oxpsModuleGenerator_init_parent');

        $this->SUT->init();

        $oModule = $this->SUT->getModule();

        $this->assertInstanceOf('oxpsModuleGeneratorOxModule', $oModule);
        $this->assertSame('test', $oModule->getVendorPrefix());
        $this->assertSame(
            array(
                'name' => 'TEST',
                'link' => 'www.example.com',
                'mail' => 'test@example.com',
                'copy' => 'TEST ',
                'info' => ' * This is automatically generated test output.'
            ),
            $oModule->getAuthorData()
        );
    }


    public function testRender()
    {
        $this->SUT->expects($this->once())->method('_Admin_oxpsModuleGenerator_init_parent');
        $this->SUT->expects($this->once())->method('_Admin_oxpsModuleGenerator_render_parent')->will(
            $this->returnValue('admin_oxpsmodulegenerator.tpl')
        );
        $this->SUT->init();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayNotHasKey('oModule', $aViewData);

        $this->assertSame('admin_oxpsmodulegenerator.tpl', $this->SUT->render());

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('oModule', $aViewData);
        $this->assertSame($this->SUT->getModule(), $aViewData['oModule']);
    }

    public function testRender_noVendorDataConfigured_setError()
    {
        // Config mock
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('oxpsModuleGeneratorVendorPrefix', '');
        oxRegistry::set('oxConfig', $oConfig);

        $this->SUT->expects($this->once())->method('_Admin_oxpsModuleGenerator_init_parent');
        $this->SUT->expects($this->once())->method('_Admin_oxpsModuleGenerator_render_parent')->will(
            $this->returnValue('admin_oxpsmodulegenerator.tpl')
        );
        $this->SUT->init();
        $this->SUT->render();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_NO_VENDOR', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertTrue($aViewData['blError']);
    }

    public function testRender_formWasSubmitted_collectProperFormInitialValuesFromParsedRequest()
    {
        // Config mock (request data)
        $this->setRequestParameter('modulegenerator_module_name', 'badModuleName ');
        $this->setRequestParameter(
            'modulegenerator_extend_classes',
            'oxarticle' . PHP_EOL . 'oxarticle' . PHP_EOL . 'oxlist' . PHP_EOL . 'oxarticle' . PHP_EOL . 'asdasd'
        );
        $this->setRequestParameter(
            'modulegenerator_controllers',
            ' Page' . PHP_EOL . PHP_EOL . ' ' . PHP_EOL . 'view'
        );
        $this->setRequestParameter('modulegenerator_models', 'Item' . PHP_EOL . 'Thing_Two');
        $this->setRequestParameter('modulegenerator_lists', 'Item' . PHP_EOL . 'ThingTwo' . PHP_EOL . 'ItemList');
        $this->setRequestParameter('modulegenerator_widgets', 'Bar' . PHP_EOL . '1Trash');
        $this->setRequestParameter(
            'modulegenerator_blocks',
            'block@' . PHP_EOL . '@page.tpl' . PHP_EOL . 'block@page.tpl'
        );
        $this->setRequestParameter('modulegenerator_theme_none', 0);
        $this->setRequestParameter('modulegenerator_theme_list', 'flow' . PHP_EOL . 'my_azure');
        $this->setRequestParameter(
            'modulegenerator_settings',
            array(
                array(
                    'name'  => 'MyString',
                    'type'  => 'str',
                    'value' => 'Hello, word!',
                ),
                array(
                    'name'  => '',
                    'type'  => 'str',
                    'value' => '',
                ),
                array(
                    'name'  => 'MyNumber',
                    'type'  => 'num',
                    'value' => '888.8',
                )
            )
        );
        $this->setRequestParameter('modulegenerator_init_version', '0.0.1 beta');
        $this->setRequestParameter('modulegenerator_render_tasks', '1');
        $this->setRequestParameter('modulegenerator_render_samples', '1');

        $this->SUT->expects($this->once())->method('_Admin_oxpsModuleGenerator_init_parent');
        $this->SUT->expects($this->once())->method('_Admin_oxpsModuleGenerator_render_parent')->will(
            $this->returnValue('admin_oxpsmodulegenerator.tpl')
        );
        $this->SUT->init();
        $this->SUT->render();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('oValues', $aViewData);
        $this->assertEquals(
            (object) array(
                'name'        => 'badModuleName',
                'extend'      => 'oxarticle' . PHP_EOL . 'oxlist',
                'controllers' => 'Page',
                'models'      => 'Item',
                'lists'       => 'Item',
                'widgets'     => 'Bar',
                'blocks'      => 'block@page.tpl',
                'settings'    => array(
                    array(
                        'name'  => 'MyString',
                        'type'  => 'str',
                        'value' => 'Hello, word!'
                    ),
                    array(
                        'name'  => '',
                        'type'  => 'str',
                        'value' => ''
                    ),
                    array(
                        'name'  => 'MyNumber',
                        'type'  => 'num',
                        'value' => '888.8'
                    ),
                ),
                'theme_none'  => false,
                'theme_list'  => 'flow' . PHP_EOL . 'my_azure',
                'version'     => '0.0.1 beta',
                'tests'       => false,
                'tasks'       => true,
                'samples'     => true,
            ),
            $aViewData['oValues']
        );
    }


    public function testGetModule()
    {
        $this->SUT->expects($this->once())->method('_Admin_oxpsModuleGenerator_init_parent');
        $this->SUT->init();

        $this->assertInstanceOf('oxpsModuleGeneratorOxModule', $this->SUT->getModule());
    }


    public function testGetVendorPrefix()
    {
        $this->assertSame('test', $this->SUT->getVendorPrefix());
    }


    public function testGetAuthorData()
    {
        $this->assertSame(
            array(
                'name' => 'TEST',
                'link' => 'www.example.com',
                'mail' => 'test@example.com',
                'copy' => 'TEST ',
                'info' => ' * This is automatically generated test output.'
            ),
            $this->SUT->getAuthorData()
        );
    }


    public function testGenerateModule_noVendorPrefixConfigured_setErrorMessage()
    {
        // Config mock
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('oxpsModuleGeneratorVendorPrefix', '');
        oxRegistry::set('oxConfig', $oConfig);

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_VENDOR', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertTrue($aViewData['blError']);
    }

    public function testGenerateModule_invalidVendorPrefixConfigured_setErrorMessage()
    {
        // Config mock
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'Test');
        oxRegistry::set('oxConfig', $oConfig);

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_VENDOR', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertTrue($aViewData['blError']);
    }

    public function testGenerateModule_noModuleName_setErrorMessage()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', '');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_NAME', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertTrue($aViewData['blError']);
    }

    public function testGenerateModule_invalidModuleName_setErrorMessage()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'myModule');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_NAME', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertTrue($aViewData['blError']);
    }

    public function testGenerateModule_onlyModuleNameSet_generateModuleSkeletonWithNoCustomFeatures()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'NameOnly');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check vendor and module metadata paths and files
        $this->assertFileExists($this->_getTestPath('modules/test'));
        $this->assertFileExists($this->_getTestPath('modules/test/vendormetadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/metadata.php'));

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/Component'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/Component/Widget'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/Controller'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/Controller/Admin'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Core'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Core/testNameOnlyModule.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/docs'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/docs/install.sql'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/docs/README.txt'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/docs/uninstall.sql'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/Model'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/out'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/out/pictures'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/out/pictures/picture.png'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/tests'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/translations'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/translations/de'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/translations/de/testnameonly_de_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/translations/en'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/translations/en/testnameonly_en_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/de'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/de/testnameonly_admin_de_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/en'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/en/testnameonly_admin_en_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/popups'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/blocks'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/pages'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/widgets'));

        // Check metadata content and file comments
        $sMetadata = file_get_contents($this->_getTestPath('modules/test/NameOnly/metadata.php'));
        $this->assertContains(' * This is automatically generated test output.', $sMetadata);
        $this->assertContains(' * @package       NameOnly', $sMetadata);
        $this->assertContains(' * @author        TEST', $sMetadata);
        $this->assertContains(' * @link          www.example.com', $sMetadata);
        $this->assertContains(' * @copyright (C) TEST ', $sMetadata);

        include($this->_getTestPath('modules/test/nameonly/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertSame(
                array(
                    'id'          => 'testnameonly',
                    'title'       => array(
                        'de' => '[TR - TEST :: Name Only]',
                        'en' => 'TEST :: Name Only',
                    ),
                    'description' => array(
                        'de' => '[TR - TEST Name Only Module]',
                        'en' => 'TEST Name Only Module',
                    ),
                    'thumbnail'   => 'out/pictures/picture.png',
                    'version'     => '',
                    'author'      => 'TEST',
                    'url'         => 'www.example.com',
                    'email'       => 'test@example.com',
                    'extend'      => array(),
                    'files'       => array(
                        'testNameOnlyModule' => 'test/NameOnly/Core/testNameOnlyModule.php'
                    ),
                    'templates'   => array(),
                    'blocks'      => array(),
                    'settings'    => array(),
                    'events'      => array(
                        'onActivate'   => 'testNameOnlyModule::onActivate',
                        'onDeactivate' => 'testNameOnlyModule::onDeactivate',
                    ),
                ),
                $aModule
            );
        }

        // Check module main class content
        $this->assertFalse(class_exists('testNameOnlyModule'));
        include($this->_getTestPath('modules/test/NameOnly/Core/TestNameOnlyModule.php'));
        $this->assertTrue(class_exists('testNameOnlyModule'));
        $oModule = new testNameOnlyModule();
        $this->assertTrue(method_exists($oModule, '__construct'));
        $this->assertTrue(method_exists($oModule, 'onActivate'));
        $this->assertTrue(method_exists($oModule, 'onDeactivate'));
        $this->assertTrue(method_exists($oModule, 'clearTmp'));
        $this->assertTrue(method_exists($oModule, 'translate'));
        $this->assertTrue(method_exists($oModule, 'getCmsContent'));
        $this->assertTrue(method_exists($oModule, 'getSetting'));
        $this->assertTrue(method_exists($oModule, 'getPath'));
    }

    public function testGenerateModule_extendedClassesSet_generateModuleSkeletonWithExtendedClasses()
    {
        $this->markTestIncomplete('#PSGEN-257: Invalid generation of extending classes'); // TODO #SVO
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Extended');
        $this->setRequestParameter('modulegenerator_extend_classes', 'oxArticle' . PHP_EOL . 'oxList');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Extended/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Extended/Core/testExtendedModule.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Extended/Application/translations/de/testExtended_de_lang.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Extended/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('extend', $aModule);
            $this->assertSame(
                array(
                    'oxArticle' => 'test/Extended/Application/Model/testExtendedoxArticle',
                    'oxList'    => 'test/Extended/Core/testExtendedoxList',
                ),
                $aModule['extend']
            );
        }

        // Check extended class content
        $this->assertFalse(class_exists('testExtendedoxList'));
        include($this->_getTestPath('modules/test/Extended/Core/testExtendedoxList.php'));
        $this->assertTrue(class_exists('testExtendedoxList'));
    }

    public function testGenerateModule_extendedClassesAreInvalid_generateModuleSkeletonWithNoFaultyExtendedClasses()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Extended');
        $this->setRequestParameter('modulegenerator_extend_classes', 'oxNonExistingBox');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/extended/metadata.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/extended/core/testextendedoxnonexistingbox.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/extended/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('extend', $aModule);
            $this->assertSame(array(), $aModule['extend']);
        }
    }

    public function testGenerateModule_controllersSet_generateModuleSkeletonWithControllersClasses()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Ctrl1');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', 'Page');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/Controller/testCtrl1Page.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/views/pages/testCtrl1Page.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array(
                    'testCtrl1Module' => 'test/Ctrl1/Core/testCtrl1Module.php',
                    'testCtrl1Page'   => 'test/Ctrl1/Application/Controller/testCtrl1Page.php',
                ),
                $aModule['files']
            );
            $this->assertSame(
                array('testCtrl1Page.tpl' => 'test/Ctrl1/Application/views/pages/testCtrl1Page.tpl'),
                $aModule['templates']
            );
        }

        // Check controller class content
        $this->assertFalse(class_exists('testCtrl1Page'));
        include($this->_getTestPath('modules/test/Ctrl1/Application/Controller/testCtrl1Page.php'));
        $this->assertTrue(class_exists('testCtrl1Page'));
        $oClass = new testCtrl1Page();
        $this->assertTrue(method_exists($oClass, 'render'));
    }

    public function testGenerateModule_sameControllersSetMultipleTimes_generateModuleSkeletonWithUniqueControllersClasses()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Ctrl1');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', 'Page' . PHP_EOL . 'Page' . PHP_EOL . 'View');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/Controller/testCtrl1Page.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/Controller/testCtrl1View.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/views/pages/testCtrl1Page.tpl'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/views/pages/testCtrl1View.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array(
                    'testCtrl1Module' => 'test/Ctrl1/Core/testCtrl1Module.php',
                    'testCtrl1Page'   => 'test/Ctrl1/Application/Controller/testCtrl1Page.php',
                    'testCtrl1View'   => 'test/Ctrl1/Application/Controller/testCtrl1View.php',
                ),
                $aModule['files']
            );
            $this->assertSame(
                array(
                    'testCtrl1Page.tpl' => 'test/Ctrl1/Application/views/pages/testCtrl1Page.tpl',
                    'testCtrl1View.tpl' => 'test/Ctrl1/Application/views/pages/testCtrl1View.tpl',
                ),
                $aModule['templates']
            );
        }
    }

    public function testGenerateModule_invalidControllerName_generateModuleSkeletonWithNoControllersClasses()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Ctrl1');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', ' some_class');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/Ctrl1/Application/Controller/testCtrl1Some_class.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/Ctrl1/Application/views/pages/testCtrl1Some_class.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(array('testCtrl1Module' => 'test/Ctrl1/Core/testCtrl1Module.php'), $aModule['files']);
            $this->assertSame(array(), $aModule['templates']);
        }
    }

    public function testGenerateModule_modelsSet_generateModuleSkeletonWithModelsClasses()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Special');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', 'Offer');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Special/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Special/Application/Model/testSpecialOffer.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Special/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertSame(
                array(
                    'testSpecialModule' => 'test/Special/Core/testSpecialModule.php',
                    'testSpecialOffer'  => 'test/Special/Application/Model/testSpecialOffer.php',
                ),
                $aModule['files']
            );
        }

        // Check model class content
        $this->assertFalse(class_exists('testSpecialOffer'));
        include($this->_getTestPath('modules/test/Special/Application/Model/testSpecialOffer.php'));
        $this->assertTrue(class_exists('testSpecialOffer'));
        $oClass = new testSpecialOffer();
        $this->assertTrue(method_exists($oClass, '__construct'));
    }

    public function testGenerateModule_listModelSetWithNoItemModel_generateModuleSkeletonWithNoListModel()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Special');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', 'Offer');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Special/metadata.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/Special/Application/Model/testSpecialOffer.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/Special/Application/Model/testSpecialOfferList.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Special/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertSame(
                array('testSpecialModule' => 'test/Special/Core/testSpecialModule.php'),
                $aModule['files']
            );
        }
    }

    public function testGenerateModule_listAndSameItemModelSet_generateModuleSkeletonWithListAndItemModels()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Special');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', 'Offer');
        $this->setRequestParameter('modulegenerator_lists', 'Offer');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Special/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Special/Application/Model/testSpecialOffer.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Special/Application/Model/testSpecialOfferList.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Special/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertSame(
                array(
                    'testSpecialModule'    => 'test/Special/Core/testSpecialModule.php',
                    'testSpecialOffer'     => 'test/Special/Application/Model/testSpecialOffer.php',
                    'testSpecialOfferList' => 'test/Special/Application/Model/testSpecialOfferList.php',
                ),
                $aModule['files']
            );
        }

        // Check list model class
        $this->assertFalse(class_exists('testSpecialOfferList'));
        include($this->_getTestPath('modules/test/Special/Application/Model/testSpecialOfferList.php'));
        $this->assertTrue(class_exists('testSpecialOfferList'));
    }

    public function testGenerateModule_widgetsSet_generateModuleSkeletonWithWidgetsClasses()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Wi');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', '');
        $this->setRequestParameter('modulegenerator_widgets', 'Bar');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Wi/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Wi/Application/Component/Widget/testWiBar.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Wi/Application/views/widgets/testWiBar.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Wi/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array(
                    'testWiModule' => 'test/Wi/Core/testWiModule.php',
                    'testWiBar'    => 'test/Wi/Application/Component/Widget/testWiBar.php',
                ),
                $aModule['files']
            );
            $this->assertSame(
                array('testWiBar.tpl' => 'test/Wi/Application/views/widgets/testWiBar.tpl'),
                $aModule['templates']
            );
        }

        // Check widget class content
        $this->assertFalse(class_exists('testWiBar'));
        include($this->_getTestPath('modules/test/Wi/Application/Component/Widget/testWiBar.php'));
        $this->assertTrue(class_exists('testWiBar'));
        $oClass = new testWiBar();
        $this->assertTrue(method_exists($oClass, 'isCacheable'));
    }

    public function testGenerateModule_blocksSet_generateModuleSkeletonWithBlocks()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Block');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', '');
        $this->setRequestParameter('modulegenerator_widgets', '');
        $this->setRequestParameter('modulegenerator_blocks', 'my_block@page.tpl');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Block/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Block/Application/views/blocks/testBlock_my_block.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Block/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('blocks', $aModule);
            $this->assertSame(
                array(
                    array(
                        'template' => 'page.tpl',
                        'block'    => 'my_block',
                        'file'     => 'Application/views/blocks/testBlock_my_block.tpl',
                    )
                ),
                $aModule['blocks']
            );
        }
    }

    public function testGenerateModule_blocksDefinitionIsInvalid_generateModuleSkeletonWithNoBlocks()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Block');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', '');
        $this->setRequestParameter('modulegenerator_widgets', '');
        $this->setRequestParameter('modulegenerator_blocks', ' @page.tpl');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/block/metadata.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/block/views/blocks/testblock_.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/block/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('blocks', $aModule);
            $this->assertSame(array(), $aModule['blocks']);
        }
    }

    public function testGenerateModule_SettingsSet_generateModuleSkeletonWithSettings()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Conf');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', '');
        $this->setRequestParameter('modulegenerator_widgets', '');
        $this->setRequestParameter('modulegenerator_blocks', '');
        $this->setRequestParameter(
            'modulegenerator_settings',
            array(
                0 => array(
                    'name'  => 'MyString',
                    'type'  => 'str',
                    'value' => '',
                ),
                1 => array(
                    'name'  => 'MyNumber',
                    'type'  => 'num',
                    'value' => '888',
                ),
                3 => array(
                    'name'  => 'MyArray',
                    'type'  => 'arr',
                    'value' => '1' . PHP_EOL . '2',
                ),
            )
        );

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Conf/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Conf/Application/views/admin/en/testConf_admin_en_lang.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Conf/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('settings', $aModule);
            $this->assertSame(
                array(
                    array(
                        'group' => 'testConfSettings',
                        'name'  => 'testConfMyString',
                        'type'  => 'str',
                        'value' => '',
                    ),
                    array(
                        'group' => 'testConfSettings',
                        'name'  => 'testConfMyNumber',
                        'type'  => 'num',
                        'value' => 888,
                    ),
                    array(
                        'group' => 'testConfSettings',
                        'name'  => 'testConfMyArray',
                        'type'  => 'arr',
                        'value' => array('1', '2'),
                    ),
                ),
                $aModule['settings']
            );
        }

        // Check backend translation file
        include($this->_getTestPath('modules/test/Conf/Application/views/admin/en/testConf_admin_en_lang.php'));
        $this->assertTrue(isset($aLang));
        if (isset($aLang)) {
            $this->assertArrayHasKey('SHOP_MODULE_GROUP_testConfSettings', $aLang);
            $this->assertSame('TEST Conf Module Settings', $aLang['SHOP_MODULE_GROUP_testConfSettings']);
            $this->assertArrayHasKey('SHOP_MODULE_testConfMyString', $aLang);
            $this->assertSame('My String', $aLang['SHOP_MODULE_testConfMyString']);
            $this->assertArrayHasKey('SHOP_MODULE_testConfMyNumber', $aLang);
            $this->assertSame('My Number', $aLang['SHOP_MODULE_testConfMyNumber']);
            $this->assertArrayHasKey('SHOP_MODULE_testConfMyArray', $aLang);
            $this->assertSame('My Array', $aLang['SHOP_MODULE_testConfMyArray']);
        }
    }

    public function testGenerateModule_settingsAreInvalid_generateModuleSkeletonWithNoSettings()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Conf');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', '');
        $this->setRequestParameter('modulegenerator_widgets', '');
        $this->setRequestParameter('modulegenerator_blocks', '');
        $this->setRequestParameter(
            'modulegenerator_settings',
            array(
                0 => array(
                    'name'  => '',
                    'type'  => 'str',
                    'value' => '',
                ),
                1 => array(
                    'name'  => 'one',
                    'type'  => 'num',
                    'value' => '888',
                ),
                3 => array(
                    'name'  => 'MyArray',
                    'type'  => 'table',
                    'value' => '1' . PHP_EOL . '2',
                ),
            )
        );

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Conf/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Conf/Application/views/admin/en/testConf_admin_en_lang.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Conf/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('settings', $aModule);
            $this->assertSame(array(), $aModule['settings']);
        }

        // Check backend translation file
        include($this->_getTestPath('modules/test/Conf/Application/views/admin/en/testConf_admin_en_lang.php'));
        $this->assertTrue(isset($aLang));
        if (isset($aLang)) {
            $this->assertArrayNotHasKey('SHOP_MODULE_GROUP_testConfSettings', $aLang);
            $this->assertArrayNotHasKey('SHOP_MODULE_testConf', $aLang);
            $this->assertArrayNotHasKey('SHOP_MODULE_testConfone', $aLang);
            $this->assertArrayNotHasKey('SHOP_MODULE_testConfMyArray', $aLang);
        }
    }

    public function testGenerateModule_versionSet_generateModuleSkeletonWithThatVersion()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Version');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', '');
        $this->setRequestParameter('modulegenerator_widgets', '');
        $this->setRequestParameter('modulegenerator_blocks', '');
        $this->setRequestParameter('modulegenerator_settings', array());
        $this->setRequestParameter('modulegenerator_init_version', '0.1.0 beta');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/version/metadata.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/version/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('version', $aModule);
            $this->assertSame('0.1.0 beta', $aModule['version']);
        }
    }

    /**
     * NOTE: This test takes very long since copies files from remote GIT repository.
     */
    /*public function testGenerateModule_unitTestsChecked_generateModuleSkeletonWithFilledTestFolder()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'UnitTests');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', '');
        $this->setRequestParameter('modulegenerator_widgets', '');
        $this->setRequestParameter('modulegenerator_blocks', '');
        $this->setRequestParameter('modulegenerator_settings', array());
        $this->setRequestParameter('modulegenerator_init_version', '1.0.0');
        $this->setRequestParameter('modulegenerator_fetch_unit_tests', true);

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/unittests/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/unittests/tests/phpunit.xml'));
        $this->assertFileExists($this->_getTestPath('modules/test/unittests/tests/unit'));
        $this->assertFileExists($this->_getTestPath('modules/test/unittests/tests/unit/modules'));
        $this->assertFileExists($this->_getTestPath('modules/test/unittests/tests/unit/modules/models'));
        $this->assertFileExists($this->_getTestPath('modules/test/unittests/tests/unit/modules/models/testunittestsitemTest.php'));

        // Check test class
        $this->assertFalse(class_exists('testUnitTestsItemTest'));
        include($this->_getTestPath('modules/test/unittests/tests/unit/modules/models/testunittestsitemTest.php'));
        $this->assertTrue(class_exists('testUnitTestsItemTest'));
    }*/

    public function testGenerateModule_learningTipsAndInstructionsChecked_generateModuleSkeletonWithHintsComments()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'Learning');
        $this->setRequestParameter('modulegenerator_extend_classes', '');
        $this->setRequestParameter('modulegenerator_controllers', '');
        $this->setRequestParameter('modulegenerator_models', '');
        $this->setRequestParameter('modulegenerator_lists', '');
        $this->setRequestParameter('modulegenerator_widgets', '');
        $this->setRequestParameter('modulegenerator_blocks', '');
        $this->setRequestParameter('modulegenerator_settings', array());
        $this->setRequestParameter('modulegenerator_init_version', '1.0.0');
        $this->setRequestParameter('modulegenerator_fetch_unit_tests', false);
        $this->setRequestParameter('modulegenerator_render_tasks', true);
        $this->setRequestParameter('modulegenerator_render_samples', true);

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/Learning/metadata.php'));

        // Check metadata content
        $sMetadata = file_get_contents($this->_getTestPath('modules/test/Learning/metadata.php'));
        $this->assertContains(' * TODO: Remove all these TODO comments.', $sMetadata);
        $this->assertContains("//'[ParentClassName]' => 'test/Learning/[appropriate_folder]/testLearning[parent_class_name]',", $sMetadata);
    }

    public function testGenerateModule_allOptionsSet_generateModuleSkeletonWithAllFeatures()
    {
        // Config mock
        $this->setRequestParameter('modulegenerator_module_name', 'AllThings');
        $this->setRequestParameter('modulegenerator_extend_classes', 'oxbasket' . PHP_EOL . 'oxList');
        $this->setRequestParameter('modulegenerator_controllers', 'View' . PHP_EOL . 'Preview');
        $this->setRequestParameter('modulegenerator_models', 'Item' . PHP_EOL . 'Model');
        $this->setRequestParameter('modulegenerator_lists', 'Model');
        $this->setRequestParameter('modulegenerator_widgets', 'Bar' . PHP_EOL . 'Menu');
        $this->setRequestParameter('modulegenerator_blocks', 'block@page.tpl' . PHP_EOL . 'footer@layout.tpl');
        $this->setRequestParameter(
            'modulegenerator_settings',
            array(
                0 => array(
                    'name'  => 'Assoc',
                    'type'  => 'aarr',
                    'value' => 'a => b' . PHP_EOL . '1 => VAL',
                ),
                1 => array(
                    'name'  => 'Dropdown',
                    'type'  => 'select',
                    'value' => 'A' . PHP_EOL . 'B' . PHP_EOL . 'C',
                )
            )
        );
        $this->setRequestParameter('modulegenerator_init_version', '1.0.1');
        $this->setRequestParameter('modulegenerator_fetch_unit_tests', false); //NOTE: This test takes very long.
        $this->setRequestParameter('modulegenerator_render_tasks', true);
        $this->setRequestParameter('modulegenerator_render_samples', true);

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/allthings/metadata.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/allthings/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertSame(
                array(
                    'id'          => 'testallthings',
                    'title'       => array(
                        'de' => '[TR - TEST :: All Things]',
                        'en' => 'TEST :: All Things',
                    ),
                    'description' => array(
                        'de' => '[TR - TEST All Things Module]',
                        'en' => 'TEST All Things Module',
                    ),
                    'thumbnail'   => 'out/pictures/picture.png',
                    'version'     => '1.0.1',
                    'author'      => 'TEST',
                    'url'         => 'www.example.com',
                    'email'       => 'test@example.com',
                    'extend'      => array(
                        'oxbasket' => 'test/AllThings/Application/Model/testAllThingsoxbasket',
                        'oxList'   => 'test/AllThings/Core/testAllThingsoxList',
                    ),
                    'files'       => array(
                        'testAllThingsModule'    => 'test/AllThings/Core/testAllThingsModule.php',
                        'testAllThingsBar'       => 'test/AllThings/Application/Component/Widget/testAllThingsBar.php',
                        'testAllThingsMenu'      => 'test/AllThings/Application/Component/Widget/testAllThingsMenu.php',
                        'testAllThingsView'      => 'test/AllThings/Application/Controller/testAllThingsView.php',
                        'testAllThingsPreview'   => 'test/AllThings/Application/Controller/testAllThingsPreview.php',
                        'testAllThingsItem'      => 'test/AllThings/Application/Model/testAllThingsItem.php',
                        'testAllThingsModel'     => 'test/AllThings/Application/Model/testAllThingsModel.php',
                        'testAllThingsModelList' => 'test/AllThings/Application/Model/testAllThingsModelList.php',
                    ),
                    'templates'   => array(
                        'testAllThingsView.tpl'    => 'test/AllThings/Application/views/pages/testAllThingsView.tpl',
                        'testAllThingsPreview.tpl' => 'test/AllThings/Application/views/pages/testAllThingsPreview.tpl',
                        'testAllThingsBar.tpl'     => 'test/AllThings/Application/views/widgets/testAllThingsBar.tpl',
                        'testAllThingsMenu.tpl'    => 'test/AllThings/Application/views/widgets/testAllThingsMenu.tpl',
                    ),
                    'blocks'      => array(
                        array(
                            'template' => 'page.tpl',
                            'block'    => 'block',
                            'file'     => 'Application/views/blocks/testAllThings_block.tpl',
                        ),
                        array(
                            'template' => 'layout.tpl',
                            'block'    => 'footer',
                            'file'     => 'Application/views/blocks/testAllThings_footer.tpl',
                        ),
                    ),
                    'settings'    => array(
                        array(
                            'group' => 'testAllThingsSettings',
                            'name'  => 'testAllThingsAssoc',
                            'type'  => 'aarr',
                            'value' => array(
                                'a' => 'b',
                                '1' => 'VAL',
                            ),
                        ),
                        array(
                            'group'      => 'testAllThingsSettings',
                            'name'       => 'testAllThingsDropdown',
                            'type'       => 'select',
                            'value'      => 'A',
                            'constrains' => 'A|B|C',
                        ),
                    ),
                    'events'      => array(
                        'onActivate'   => 'testAllThingsModule::onActivate',
                        'onDeactivate' => 'testAllThingsModule::onDeactivate',
                    ),
                ),
                $aModule
            );
        }
    }


    /**
     * Get a path inside test folder in temp directory.
     *
     * @param string $sPathSuffix
     *
     * @return string
     */
    protected function _getTestPath($sPathSuffix = '')
    {
        return oxRegistry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR .
               'test' . DIRECTORY_SEPARATOR . (string) $sPathSuffix;
    }
}

/**
 * Class testExtendedOxList_parent
 * Test dummy class.
 */
class testExtendedOxList_parent
{

}
