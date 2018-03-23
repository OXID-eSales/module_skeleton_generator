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
use Oxps\ModuleGenerator\Application\Controller\Admin\Admin_oxpsAjaxDataProvider;
use PHPUnit_Framework_MockObject_MockObject;

if (!class_exists('Smarty')) {
    include dirname(__FILE__) . '../../../../../../../../../vendor/smarty/smarty/libs/Smarty.class.php';
}

class Admin_oxpsAjaxDataProviderTest extends UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var Admin_oxpsAjaxDataProvider|PHPUnit_Framework_MockObject_MockObject
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
            'Admin_oxpsAjaxDataProvider',
            [
                'Admin_oxpsAjaxDataProvider_init_parent',
                '_returnJsonResponse',
            ]
        );
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

    public function testGetOxModule()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorOxModule', $this->SUT->getOxModule());
    }

    public function testGetModule()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorModule', $this->SUT->getModule());
    }

    public function testGetValidator()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorValidator', $this->SUT->getValidator());
    }

    public function testGetModuleData_moduleNameEmpty_returnEmptyJsonResponse()
    {
        $this->setRequestParameter('moduleName', '');
        $this->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'test');
        $oOxModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            [
                '__construct',
                '__call',
                'init',
                'readGenerationOptions',
            ]
        );

        $oOxModule->expects($this->once())->method('init')->with('', [], 'test');
        $oOxModule->expects($this->never())->method('readGenerationOptions');

        // TODO: Deprecaded solution. Need to use the way below:
        // TODO: \OxidEsales\Eshop\Core\Registry::set('oxpsModuleGeneratorOxModule', $oOxModule);
        oxTestModules::addModuleObject('oxpsModuleGeneratorOxModule', $oOxModule);
        $this->SUT->expects($this->once())->method('_returnJsonResponse')->with([]);

        $this->SUT->getModuleData();
    }

    public function testGetModuleData_moduleDoesNotExist_returnEmptyJsonResponse()
    {
        $this->setRequestParameter('moduleName', 'NotExistingModuleName');
        $this->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'test');
        $oOxModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            [
                '__construct',
                '__call',
                'init',
                'readGenerationOptions',
            ]
        );

        $oOxModule->expects($this->once())->method('init')->with('NotExistingModuleName', [], 'test');
        $oOxModule->expects($this->never())->method('readGenerationOptions');

        oxTestModules::addModuleObject('oxpsModuleGeneratorOxModule', $oOxModule);

        $oValidator = $this->getMock(
            'oxpsModuleGeneratorValidator',
            [
                '__construct',
                '__call',
                'moduleExists',
            ]
        );
        $oValidator->expects($this->once())->method('moduleExists')
            ->with('NotExistingModuleName')
            ->will($this->returnValue(false));

        oxTestModules::addModuleObject('oxpsModuleGeneratorValidator', $oValidator);

        $this->SUT->expects($this->once())->method('_returnJsonResponse')->with([]);

        $this->SUT->getModuleData();
    }

    public function testGetModuleData_moduleExists_returnGenerationOptionsAsJsonResponse()
    {
        $this->setRequestParameter('moduleName', 'existingModuleName');
        $this->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'test');
        $oOxModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            [
                '__construct',
                '__call',
                'init',
                'readGenerationOptions',
            ]
        );

        $oOxModule->expects($this->once())->method('init')->with('existingModuleName', [], 'test');
        $oOxModule->expects($this->once())->method('readGenerationOptions')
            ->with('existingModuleName')
            ->will(
                $this->returnValue(
                    [
                        'name' => 'existingModuleName',
                    ]
                )
            );

        oxTestModules::addModuleObject('oxpsModuleGeneratorOxModule', $oOxModule);

        $oValidator = $this->getMock(
            'oxpsModuleGeneratorValidator',
            [
                '__construct',
                '__call',
                'moduleExists',
            ]
        );
        $oValidator->expects($this->once())->method('moduleExists')
            ->with('existingModuleName')
            ->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxpsModuleGeneratorValidator', $oValidator);

        $this->SUT->expects($this->once())->method('_returnJsonResponse')->with(
            [
                'name' => 'existingModuleName',
            ]
        );

        $this->SUT->getModuleData();
    }

    public function testValidateExtendClassNames_enteredClassName_returnExistingExtensibleClass()
    {
        $this->setRequestParameter('extendClasses', 'existingClass');
        $oValidator = $this->getMock(
            'oxpsModuleGeneratorValidator',
            [
                '__construct',
                '__call',
                'validateAndLinkClasses',
            ]
        );

        $oValidator->expects($this->once())->method('validateAndLinkClasses')
            ->with('existingClass')
            ->will(
                $this->returnValue(
                    [
                        'className' => 'existingClass',
                    ]
                )
            );

        oxTestModules::addModuleObject('oxpsModuleGeneratorValidator', $oValidator);

        $this->SUT->expects($this->once())->method('_returnJsonResponse')->with(
            [
                'className' => 'existingClass',
            ]
        );

        $this->SUT->validateExtendClassNames();
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
        return $this->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR .
               'test' . DIRECTORY_SEPARATOR . (string) $sPathSuffix;
    }
}
