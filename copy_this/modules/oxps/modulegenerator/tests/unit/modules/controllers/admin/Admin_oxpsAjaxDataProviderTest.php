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

class Admin_oxpsAjaxDataProviderTest extends OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var Admin_oxpsAjaxDataProvider
     */
    protected $SUT;

    /**
     * Set SUT state before test.
     * Create test folder for modules generation.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock( 'Admin_oxpsAjaxDataProvider',
            [
                'Admin_oxpsAjaxDataProvider_init_parent',
            ]
        );

        // Mock config for module settings
        $this->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'test');
        $this->setConfigParam('oxpsModuleGeneratorModuleAuthor', 'TEST');
        $this->setConfigParam('oxpsModuleGeneratorAuthorLink', 'www.example.com');
        $this->setConfigParam('oxpsModuleGeneratorAuthorMail', 'test@example.com');
        $this->setConfigParam('oxpsModuleGeneratorCopyright', 'TEST ');
        $this->setConfigParam('oxpsModuleGeneratorComment', '* This is automatically generated test output.');

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

    public function testGetModule()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorModule', $this->SUT->getModule());
    }

    public function testGetOxModule()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorOxModule', $this->SUT->getOxModule());
    }

    public function testGetValidator()
    {
        $this->assertInstanceOf('oxpsModuleGeneratorValidator', $this->SUT->getValidator());
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
