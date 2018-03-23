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
    
namespace Oxps\ModuleGenerator\Tests\Unit\Modules\Controllers\Admin;

use OxidEsales\TestingLibrary\UnitTestCase;
use Oxps\ModuleGenerator\Application\Controller\Admin\Admin_oxpsModuleGenerator;
use Oxps\ModuleGenerator\Core\Render;
use oxTestModules;

if (!class_exists('Smarty')) {
    include dirname(__FILE__) . '../../../../../../../../../vendor/smarty/smarty/libs/Smarty.class.php';
}


/**
 * Class Admin_oxpsModuleGeneratorTest
 * INTEGRATION tests for controller class Admin_oxpsModuleGenerator.
 *
 * @see Admin_oxpsModuleGenerator
 */
class Admin_oxpsModuleGeneratorTest extends UnitTestCase
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
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oConfig->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'test');
        $oConfig->setConfigParam('oxpsModuleGeneratorModuleAuthor', 'TEST');
        $oConfig->setConfigParam('oxpsModuleGeneratorAuthorLink', 'www.example.com');
        $oConfig->setConfigParam('oxpsModuleGeneratorAuthorMail', 'test@example.com');
        $oConfig->setConfigParam('oxpsModuleGeneratorCopyright', 'TEST ');
        $oConfig->setConfigParam('oxpsModuleGeneratorComment', '* This is automatically generated test output.');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

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
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oConfig->setConfigParam('oxpsModuleGeneratorVendorPrefix', '');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

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
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oConfig->setConfigParam('oxpsModuleGeneratorVendorPrefix', '');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

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
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oConfig->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'Test');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

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
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Core/NameOnlyModule.php'));
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
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/translations/de/testNameOnly_de_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/translations/en'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/translations/en/testNameOnly_en_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/de'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/de/testNameOnly_admin_de_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/en'));
        $this->assertFileExists($this->_getTestPath('modules/test/NameOnly/Application/views/admin/en/testNameOnly_admin_en_lang.php'));
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

        include($this->_getTestPath('modules/test/NameOnly/metadata.php'));
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
                    'controllers' => array(),
                    'templates'   => array(),
                    'blocks'      => array(),
                    'settings'    => array(),
                    'events'      => array(
                        'onActivate'   => 'NameOnlyModule::onActivate',
                        'onDeactivate' => 'NameOnlyModule::onDeactivate',
                    ),
                ),
                $aModule
            );
        }

        // Check module main class content
        $this->assertFalse(class_exists('NameOnlyModule'));
        include($this->_getTestPath('modules/test/NameOnly/Core/NameOnlyModule.php'));
        $this->assertTrue(class_exists('\\Test\\NameOnly\\Core\\NameOnlyModule'));
        $oModule = new \Test\NameOnly\Core\NameOnlyModule();
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
        $this->assertFileExists($this->_getTestPath('modules/test/Extended/Core/ExtendedModule.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Extended/Application/translations/de/testExtended_de_lang.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Extended/Application/Model/Article.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Extended/Core/ListModel.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Extended/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('extend', $aModule);
            $this->assertSame(
                array(
                    'OxidEsales\Eshop\Application\Model\Article' => 'Test\Extended\Application\Model\Article',
                    'OxidEsales\Eshop\Core\Model\ListModel'    => 'Test\Extended\Core\ListModel',
                ),
                $aModule['extend']
            );
        }

        // Get mock classes so generated classes has something to extend
        $this->getMockBuilder(\Test\Extended\Core\ListModel_parent::class)->getMock();
        $this->getMockBuilder(\Test\Extended\Application\Model\Article_parent::class)->getMock();
        // Check extended class content
        $this->assertFalse(class_exists('\Test\Extended\Core\ListModel'));
        $this->assertFalse(class_exists('\Test\Extended\Application\Model\Article'));
        include($this->_getTestPath('modules/test/Extended/Core/ListModel.php'));
        include($this->_getTestPath('modules/test/Extended/Application/Model/Article.php'));
        $this->assertTrue(class_exists('Test\Extended\Core\ListModel'));
        $this->assertTrue(class_exists('Test\Extended\Application\Model\Article'));
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
        $this->assertFileExists($this->_getTestPath('modules/test/Extended/metadata.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/Extended/core/testextendedoxnonexistingbox.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Extended/metadata.php'));
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
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/Controller/Page.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/views/pages/testCtrl1Page.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('controllers', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array(
                    'test_ctrl1_page'   => 'Test\Ctrl1\Application\Controller\Page',
                ),
                $aModule['controllers']
            );
            $this->assertSame(
                array('testCtrl1Page.tpl' => 'test/Ctrl1/Application/views/pages/testCtrl1Page.tpl'),
                $aModule['templates']
            );
        }

        // Check controller class content
        $this->assertFalse(class_exists('Test\Ctrl1\Application\Controller\Page'));
        include($this->_getTestPath('modules/test/Ctrl1/Application/Controller/Page.php'));
        $this->assertTrue(class_exists('Test\Ctrl1\Application\Controller\Page'));
        $oClass = new Test\Ctrl1\Application\Controller\Page();
        $this->assertTrue(method_exists($oClass, Render::class));
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
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/Controller/Page.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/Controller/View.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/views/pages/testCtrl1Page.tpl'));
        $this->assertFileExists($this->_getTestPath('modules/test/Ctrl1/Application/views/pages/testCtrl1View.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('controllers', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array(
                    'test_ctrl1_page'   => 'Test\Ctrl1\Application\Controller\Page',
                    'test_ctrl1_view'   => 'Test\Ctrl1\Application\Controller\View',
                ),
                $aModule['controllers']
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
        $this->assertFileNotExists($this->_getTestPath('modules/test/Ctrl1/Application/Controller/Some_class.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/Ctrl1/Application/views/pages/testCtrl1Some_class.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Ctrl1/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('controllers', $aModule);
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(array(), $aModule['controllers']);
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
        $this->assertFileExists($this->_getTestPath('modules/test/Special/Application/Model/Offer.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Special/metadata.php'));
        $this->assertTrue(isset($aModule));

        // Check model class content
        $this->assertFalse(class_exists('\Test\Special\Application\Model\Offer'));
        include($this->_getTestPath('modules/test/Special/Application/Model/Offer.php'));
        $this->assertTrue(class_exists('\Test\Special\Application\Model\Offer'));
        $oClass = new \Test\Special\Application\Model\Offer();
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
        $this->assertFileNotExists($this->_getTestPath('modules/test/Special/Application/Model/Offer.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/Special/Application/Model/OfferList.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Special/metadata.php'));
        $this->assertTrue(isset($aModule));
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
        $this->assertFileExists($this->_getTestPath('modules/test/Special/Application/Model/Offer.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Special/Application/Model/OfferList.php'));

        // Check list model class
        $this->assertFalse(class_exists('Test\Special\Application\Model\OfferList'));
        include($this->_getTestPath('modules/test/Special/Application/Model/OfferList.php'));
        $this->assertTrue(class_exists('Test\Special\Application\Model\OfferList'));
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
        $this->assertFileExists($this->_getTestPath('modules/test/Wi/Application/Component/Widget/Bar.php'));
        $this->assertFileExists($this->_getTestPath('modules/test/Wi/Application/views/widgets/testWiBar.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Wi/metadata.php'));
        $this->assertTrue(isset($aModule));
        if (isset($aModule)) {
            $this->assertArrayHasKey('templates', $aModule);
            $this->assertSame(
                array('testWiBar.tpl' => 'test/Wi/Application/views/widgets/testWiBar.tpl'),
                $aModule['templates']
            );
        }

        // Check widget class content
        $this->assertFalse(class_exists('test\Wi\Application\Component\Widget\Bar'));
        include($this->_getTestPath('modules/test/Wi/Application/Component/Widget/Bar.php'));
        $this->assertTrue(class_exists('Test\Wi\Application\Component\Widget\Bar'));
        $oClass = new Test\Wi\Application\Component\Widget\Bar();
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
        $this->assertFileExists($this->_getTestPath('modules/test/Block/metadata.php'));
        $this->assertFileNotExists($this->_getTestPath('modules/test/Block/views/blocks/testblock_.tpl'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Block/metadata.php'));
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
        $this->assertFileExists($this->_getTestPath('modules/test/Version/metadata.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/Version/metadata.php'));
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
        $this->assertFileExists($this->_getTestPath('modules/test/AllThings/metadata.php'));

        // Check metadata content
        include($this->_getTestPath('modules/test/AllThings/metadata.php'));
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
                        'OxidEsales\Eshop\Application\Model\Basket' => 'Test\AllThings\Application\Model\Basket',
                        'OxidEsales\Eshop\Core\Model\ListModel'   => 'Test\AllThings\Core\ListModel',
                    ),
                    'controllers'       => array(
                        'test_allthings_view'    => 'Test\AllThings\Application\Controller\View',
                        'test_allthings_preview'       => 'Test\AllThings\Application\Controller\Preview'
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
                        'onActivate'   => 'AllThingsModule::onActivate',
                        'onDeactivate' => 'AllThingsModule::onDeactivate',
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
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR .
               'test' . DIRECTORY_SEPARATOR . (string) $sPathSuffix;
    }
}

/**
 * Class testExtendedOxList_parent
 * Test dummy class.
 */
class ListModel_parent
{

}
