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
 * Class Admin_oxpsModuleGeneratorTest
 * INTEGRATION tests for controller class Admin_oxpsModuleGenerator.
 *
 * @see Admin_oxpsModuleGenerator
 */
class Admin_oxpsModuleGeneratorTest extends OxidTestCase
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

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        importTestdataFile('testdata_remove.sql');
        importTestdataFile('testdata_add.sql');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        importTestdataFile('testdata_remove.sql');
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
        modConfig::setRequestParameter('modulegenerator_module_name', 'badModuleName ');
        modConfig::setRequestParameter(
            'modulegenerator_extend_classes',
            'oxarticle' . PHP_EOL . 'oxarticle' . PHP_EOL . 'oxlist' . PHP_EOL . 'oxarticle' . PHP_EOL . 'asdasd'
        );
        modConfig::setRequestParameter(
            'modulegenerator_controllers',
            ' Page' . PHP_EOL . PHP_EOL . ' ' . PHP_EOL . 'view'
        );
        modConfig::setRequestParameter('modulegenerator_models', 'Item' . PHP_EOL . 'Thing_Two');
        modConfig::setRequestParameter('modulegenerator_lists', 'Item' . PHP_EOL . 'ThingTwo' . PHP_EOL . 'ItemList');
        modConfig::setRequestParameter('modulegenerator_widgets', 'Bar' . PHP_EOL . '1Trash');
        modConfig::setRequestParameter(
            'modulegenerator_blocks',
            'block@' . PHP_EOL . '@page.tpl' . PHP_EOL . 'block@page.tpl'
        );
        modConfig::setRequestParameter(
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
        modConfig::setRequestParameter('modulegenerator_init_version', '0.0.1 beta');
        modConfig::setRequestParameter('modulegenerator_render_tasks', '1');
        modConfig::setRequestParameter('modulegenerator_render_samples', '1');

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
        modConfig::setRequestParameter('modulegenerator_module_name', '');

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
        modConfig::setRequestParameter('modulegenerator_module_name', 'myModule');

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
        modConfig::setRequestParameter('modulegenerator_module_name', 'NameOnly');

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
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/metadata.php'));

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/components'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/components/widgets'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/controllers'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/controllers/admin'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/core'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/core/testnameonlymodule.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/docs'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/docs/install.sql'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/docs/README.txt'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/docs/uninstall.sql'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/models'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/out'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/out/pictures'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/out/pictures/picture.png'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/tests'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/translations'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/translations/de'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/translations/de/testnameonly_de_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/translations/en'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/translations/en/testnameonly_en_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/admin'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/admin/de'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/admin/de/testnameonly_admin_de_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/admin/en'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/admin/en/testnameonly_admin_en_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/admin/popups'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/blocks'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/pages'));
        $this->assertFileExists($this->_getTestPath('modules/test/nameonly/views/widgets'));

        // Check metadata content and file comments
        $sMetadata = file_get_contents($this->_getTestPath('modules/test/nameonly/metadata.php'));
        $this->assertContains(' * This is automatically generated test output.', $sMetadata);
        $this->assertContains(' * @package       nameonly', $sMetadata);
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
                        'de' => '[TR - TEST Name Only]',
                        'en' => 'TEST Name Only',
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
                        'testnameonlymodule' => 'test/nameonly/core/testnameonlymodule.php'
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
        include($this->_getTestPath('modules/test/nameonly/core/testnameonlymodule.php'));
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
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Extended');
        modConfig::setRequestParameter('modulegenerator_extend_classes', 'oxarticle' . PHP_EOL . 'oxList');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/extended/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/extended/core/testextendedoxlist.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/extended/models/testextendedoxarticle.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/extended/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('extend', $aModule);
            $this->assertSame(
                array(
                    'oxarticle' => 'test/extended/models/testextendedoxarticle',
                    'oxlist'    => 'test/extended/core/testextendedoxlist',
                ),
                $aModule['extend']
            );
        }

        // Check extended class content
        $this->assertFalse(class_exists('testExtendedOxList'));
        include($this->_getTestPath('modules/test/extended/core/testextendedoxlist.php'));
        $this->assertTrue(class_exists('testExtendedOxList'));
    }

    public function testGenerateModule_extendedClassesAreInvalid_generateModuleSkeletonWithNoFaultyExtendedClasses()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Extended');
        modConfig::setRequestParameter('modulegenerator_extend_classes', 'oxNonExistingBox');

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
        modConfig::setRequestParameter('modulegenerator_module_name', 'Ctrl1');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', 'Page');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/controllers/testctrl1page.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/views/pages/testctrl1page.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array(
                    'testctrl1module' => 'test/ctrl1/core/testctrl1module.php',
                    'testctrl1page'   => 'test/ctrl1/controllers/testctrl1page.php',
                ),
                $aModule['files']
            );
            $this->assertSame(
                array('testctrl1page.tpl' => 'test/ctrl1/views/pages/testctrl1page.tpl'),
                $aModule['templates']
            );
        }

        // Check controller class content
        $this->assertFalse(class_exists('testCtrl1Page'));
        include($this->_getTestPath('modules/test/ctrl1/controllers/testctrl1page.php'));
        $this->assertTrue(class_exists('testCtrl1Page'));
        $oClass = new testCtrl1Page();
        $this->assertTrue(method_exists($oClass, 'render'));
    }

    public function testGenerateModule_sameControllersSetMultipleTimes_generateModuleSkeletonWithUniqueControllersClasses()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Ctrl1');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', 'Page' . PHP_EOL . 'Page' . PHP_EOL . 'View');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/controllers/testctrl1page.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/controllers/testctrl1view.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/views/pages/testctrl1page.tpl'));
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/views/pages/testctrl1view.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array(
                    'testctrl1module' => 'test/ctrl1/core/testctrl1module.php',
                    'testctrl1page'   => 'test/ctrl1/controllers/testctrl1page.php',
                    'testctrl1view'   => 'test/ctrl1/controllers/testctrl1view.php',
                ),
                $aModule['files']
            );
            $this->assertSame(
                array(
                    'testctrl1page.tpl' => 'test/ctrl1/views/pages/testctrl1page.tpl',
                    'testctrl1view.tpl' => 'test/ctrl1/views/pages/testctrl1view.tpl',
                ),
                $aModule['templates']
            );
        }
    }

    public function testGenerateModule_invalidControllerName_generateModuleSkeletonWithNoControllersClasses()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Ctrl1');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', ' some_class');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/ctrl1/metadata.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/ctrl1/controllers/testctrl1some_class.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/ctrl1/views/pages/testctrl1some_class.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(array('testctrl1module' => 'test/ctrl1/core/testctrl1module.php'), $aModule['files']);
            $this->assertSame(array(), $aModule['templates']);
        }
    }

    public function testGenerateModule_modelsSet_generateModuleSkeletonWithModelsClasses()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Special');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', 'Offer');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/special/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/special/models/testspecialoffer.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/special/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertSame(
                array(
                    'testspecialmodule' => 'test/special/core/testspecialmodule.php',
                    'testspecialoffer'  => 'test/special/models/testspecialoffer.php',
                ),
                $aModule['files']
            );
        }

        // Check model class content
        $this->assertFalse(class_exists('testSpecialOffer'));
        include($this->_getTestPath('modules/test/special/models/testspecialoffer.php'));
        $this->assertTrue(class_exists('testSpecialOffer'));
        $oClass = new testSpecialOffer();
        $this->assertTrue(method_exists($oClass, '__construct'));
    }

    public function testGenerateModule_listModelSetWithNoItemModel_generateModuleSkeletonWithNoListModel()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Special');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', 'Offer');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/special/metadata.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/special/models/testspecialoffer.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/special/models/testspecialofferlist.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/special/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertSame(
                array('testspecialmodule' => 'test/special/core/testspecialmodule.php'),
                $aModule['files']
            );
        }
    }

    public function testGenerateModule_listAndSameItemModelSet_generateModuleSkeletonWithListAndItemModels()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Special');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', 'Offer');
        modConfig::setRequestParameter('modulegenerator_lists', 'Offer');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/special/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/special/models/testspecialoffer.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/special/models/testspecialofferlist.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/special/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertSame(
                array(
                    'testspecialmodule'    => 'test/special/core/testspecialmodule.php',
                    'testspecialoffer'     => 'test/special/models/testspecialoffer.php',
                    'testspecialofferlist' => 'test/special/models/testspecialofferlist.php',
                ),
                $aModule['files']
            );
        }

        // Check list model class
        $this->assertFalse(class_exists('testSpecialOfferList'));
        include($this->_getTestPath('modules/test/special/models/testspecialofferlist.php'));
        $this->assertTrue(class_exists('testSpecialOfferList'));
    }

    public function testGenerateModule_widgetsSet_generateModuleSkeletonWithWidgetsClasses()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Wi');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', '');
        modConfig::setRequestParameter('modulegenerator_widgets', 'Bar');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/wi/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/wi/components/widgets/testwibar.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/wi/views/widgets/testwibar.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/wi/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('files', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array(
                    'testwimodule' => 'test/wi/core/testwimodule.php',
                    'testwibar'    => 'test/wi/components/widgets/testwibar.php',
                ),
                $aModule['files']
            );
            $this->assertSame(
                array('testwibar.tpl' => 'test/wi/views/widgets/testwibar.tpl'),
                $aModule['templates']
            );
        }

        // Check widget class content
        $this->assertFalse(class_exists('testWiBar'));
        include($this->_getTestPath('modules/test/wi/components/widgets/testwibar.php'));
        $this->assertTrue(class_exists('testWiBar'));
        $oClass = new testWiBar();
        $this->assertTrue(method_exists($oClass, 'isCacheable'));
    }

    public function testGenerateModule_blocksSet_generateModuleSkeletonWithBlocks()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Block');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', '');
        modConfig::setRequestParameter('modulegenerator_widgets', '');
        modConfig::setRequestParameter('modulegenerator_blocks', 'my_block@page.tpl');

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/block/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/block/views/blocks/testblock_my_block.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/block/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('blocks', $aModule);
            $this->assertSame(
                array(
                    array(
                        'template' => 'page.tpl',
                        'block'    => 'my_block',
                        'file'     => 'views/blocks/testblock_my_block.tpl',
                    )
                ),
                $aModule['blocks']
            );
        }
    }

    public function testGenerateModule_blocksDefinitionIsInvalid_generateModuleSkeletonWithNoBlocks()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'Block');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', '');
        modConfig::setRequestParameter('modulegenerator_widgets', '');
        modConfig::setRequestParameter('modulegenerator_blocks', ' @page.tpl');

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
        modConfig::setRequestParameter('modulegenerator_module_name', 'Conf');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', '');
        modConfig::setRequestParameter('modulegenerator_widgets', '');
        modConfig::setRequestParameter('modulegenerator_blocks', '');
        modConfig::setRequestParameter(
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
        $this->assertFileExists($this->_getTestPath('modules/test/conf/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/conf/views/admin/en/testconf_admin_en_lang.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/conf/metadata.php'));
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
        include($this->_getTestPath('modules/test/conf/views/admin/en/testconf_admin_en_lang.php'));
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
        modConfig::setRequestParameter('modulegenerator_module_name', 'Conf');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', '');
        modConfig::setRequestParameter('modulegenerator_widgets', '');
        modConfig::setRequestParameter('modulegenerator_blocks', '');
        modConfig::setRequestParameter(
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
        $this->assertFileExists($this->_getTestPath('modules/test/conf/metadata.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/conf/views/admin/en/testconf_admin_en_lang.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/conf/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('settings', $aModule);
            $this->assertSame(array(), $aModule['settings']);
        }

        // Check backend translation file
        include($this->_getTestPath('modules/test/conf/views/admin/en/testconf_admin_en_lang.php'));
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
        modConfig::setRequestParameter('modulegenerator_module_name', 'Version');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', '');
        modConfig::setRequestParameter('modulegenerator_widgets', '');
        modConfig::setRequestParameter('modulegenerator_blocks', '');
        modConfig::setRequestParameter('modulegenerator_settings', array());
        modConfig::setRequestParameter('modulegenerator_init_version', '0.1.0 beta');

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
        modConfig::setRequestParameter('modulegenerator_module_name', 'UnitTests');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', '');
        modConfig::setRequestParameter('modulegenerator_widgets', '');
        modConfig::setRequestParameter('modulegenerator_blocks', '');
        modConfig::setRequestParameter('modulegenerator_settings', array());
        modConfig::setRequestParameter('modulegenerator_init_version', '1.0.0');
        modConfig::setRequestParameter('modulegenerator_fetch_unit_tests', true);

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
        modConfig::setRequestParameter('modulegenerator_module_name', 'Learning');
        modConfig::setRequestParameter('modulegenerator_extend_classes', '');
        modConfig::setRequestParameter('modulegenerator_controllers', '');
        modConfig::setRequestParameter('modulegenerator_models', '');
        modConfig::setRequestParameter('modulegenerator_lists', '');
        modConfig::setRequestParameter('modulegenerator_widgets', '');
        modConfig::setRequestParameter('modulegenerator_blocks', '');
        modConfig::setRequestParameter('modulegenerator_settings', array());
        modConfig::setRequestParameter('modulegenerator_init_version', '1.0.0');
        modConfig::setRequestParameter('modulegenerator_fetch_unit_tests', false);
        modConfig::setRequestParameter('modulegenerator_render_tasks', true);
        modConfig::setRequestParameter('modulegenerator_render_samples', true);

        $this->SUT->init();
        $this->SUT->generateModule();

        $aViewData = $this->SUT->getViewData();
        $this->assertArrayHasKey('sMessage', $aViewData);
        $this->assertSame('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', $aViewData['sMessage']);
        $this->assertArrayHasKey('blError', $aViewData);
        $this->assertFalse($aViewData['blError']);

        // Check module structure
        $this->assertFileExists($this->_getTestPath('modules/test/learning/metadata.php'));

        // Check metadata content
        $sMetadata = file_get_contents($this->_getTestPath('modules/test/learning/metadata.php'));
        $this->assertContains(' * TODO: Remove all this TODO comment.', $sMetadata);
        $this->assertContains("//'[ParentClassName]' => 'test/learning/[appropriate_folder]/testlearning[parent_class_name]',", $sMetadata);
    }

    public function testGenerateModule_allOptionsSet_generateModuleSkeletonWithAllFeatres()
    {
        // Config mock
        modConfig::setRequestParameter('modulegenerator_module_name', 'AllThings');
        modConfig::setRequestParameter('modulegenerator_extend_classes', 'oxbasket' . PHP_EOL . 'oxList');
        modConfig::setRequestParameter('modulegenerator_controllers', 'View' . PHP_EOL . 'Preview');
        modConfig::setRequestParameter('modulegenerator_models', 'Item' . PHP_EOL . 'Model');
        modConfig::setRequestParameter('modulegenerator_lists', 'Model');
        modConfig::setRequestParameter('modulegenerator_widgets', 'Bar' . PHP_EOL . 'Menu');
        modConfig::setRequestParameter('modulegenerator_blocks', 'block@page.tpl' . PHP_EOL . 'footer@layout.tpl');
        modConfig::setRequestParameter(
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
        modConfig::setRequestParameter('modulegenerator_init_version', '1.0.1');
        modConfig::setRequestParameter('modulegenerator_fetch_unit_tests', false); //NOTE: This test takes very long.
        modConfig::setRequestParameter('modulegenerator_render_tasks', true);
        modConfig::setRequestParameter('modulegenerator_render_samples', true);

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
                        'de' => '[TR - TEST All Things]',
                        'en' => 'TEST All Things',
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
                        'oxbasket' => 'test/allthings/models/testallthingsoxbasket',
                        'oxlist'   => 'test/allthings/core/testallthingsoxlist',
                    ),
                    'files'       => array(
                        'testallthingsmodule'    => 'test/allthings/core/testallthingsmodule.php',
                        'testallthingsbar'       => 'test/allthings/components/widgets/testallthingsbar.php',
                        'testallthingsmenu'      => 'test/allthings/components/widgets/testallthingsmenu.php',
                        'testallthingsview'      => 'test/allthings/controllers/testallthingsview.php',
                        'testallthingspreview'   => 'test/allthings/controllers/testallthingspreview.php',
                        'testallthingsitem'      => 'test/allthings/models/testallthingsitem.php',
                        'testallthingsmodel'     => 'test/allthings/models/testallthingsmodel.php',
                        'testallthingsmodellist' => 'test/allthings/models/testallthingsmodellist.php',
                    ),
                    'templates'   => array(
                        'testallthingsview.tpl'    => 'test/allthings/views/pages/testallthingsview.tpl',
                        'testallthingspreview.tpl' => 'test/allthings/views/pages/testallthingspreview.tpl',
                        'testallthingsbar.tpl'     => 'test/allthings/views/widgets/testallthingsbar.tpl',
                        'testallthingsmenu.tpl'    => 'test/allthings/views/widgets/testallthingsmenu.tpl',
                    ),
                    'blocks'      => array(
                        array(
                            'template' => 'page.tpl',
                            'block'    => 'block',
                            'file'     => 'views/blocks/testallthings_block.tpl',
                        ),
                        array(
                            'template' => 'layout.tpl',
                            'block'    => 'footer',
                            'file'     => 'views/blocks/testallthings_footer.tpl',
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
