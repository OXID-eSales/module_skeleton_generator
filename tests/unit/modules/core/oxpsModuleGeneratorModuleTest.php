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
 * Class oxpsModuleGeneratorModuleTest
 * INTEGRATION tests for core class oxpsModuleGeneratorModule.
 *
 * @see oxpsModuleGeneratorModule
 */
class oxpsModuleGeneratorModuleTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var oxpsModuleGeneratorModule
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = new oxpsModuleGeneratorModule();
    }


    public function testConstructor_loadModuleData()
    {
        $this->setLanguage(1);

        $this->assertSame('oxpsmodulegenerator', $this->SUT->getId());
        $this->assertSame('OXID :: Module Skeleton Generator', $this->SUT->getTitle());
        $this->assertSame(
            'Folders structure, empty classes and metadata generation for new OXID eShop modules.',
            $this->SUT->getDescription()
        );
    }


    public function testOnActivate_clearTempFiles()
    {
        $sTestFilePath = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR . 'test.file';

        file_put_contents($sTestFilePath, 'TEST' . PHP_EOL);

        $this->assertFileExists($sTestFilePath);

        $SUT = $this->SUT;
        $SUT::onActivate();

        $this->assertFileNotExists($sTestFilePath);
    }


    public function testOnDeactivate_clearTempFiles()
    {
        $sTestFilePath = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR . 'test.file';

        file_put_contents($sTestFilePath, 'TEST' . PHP_EOL);

        $this->assertFileExists($sTestFilePath);

        $SUT = $this->SUT;
        $SUT::onDeactivate();

        $this->assertFileNotExists($sTestFilePath);
    }


    public function testClearTmp_argumentDirProvided_clearsOnlyInsideProvidedDirectory()
    {
        $sTestFilePath = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR . 'test.file';
        $sSmartyFilePath = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR .
                           'smarty' . DIRECTORY_SEPARATOR . 'test.file';

        file_put_contents($sTestFilePath, 'TEST' . PHP_EOL);
        file_put_contents($sSmartyFilePath, 'TEST' . PHP_EOL);

        $this->assertFileExists($sTestFilePath);
        $this->assertFileExists($sSmartyFilePath);

        $SUT = $this->SUT;
        $SUT::clearTmp(\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR . 'smarty');

        $this->assertFileExists($sTestFilePath);
        $this->assertFileNotExists($sSmartyFilePath);
    }

    public function testClearTmp_noArguments_clearsTempFolder()
    {
        $sTestFilePath = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR . 'test.file';

        file_put_contents($sTestFilePath, 'TEST' . PHP_EOL);

        $this->assertFileExists($sTestFilePath);

        $SUT = $this->SUT;
        $SUT::clearTmp();

        $this->assertFileNotExists($sTestFilePath);
    }


    public function testTranslate_noSecondArgument_returnModuleTranslationStringByCode()
    {
        $this->assertSame('OXPS_MODULEGENERATOR_SOME_CODE', $this->SUT->translate('SOME_CODE'));
    }

    public function testTranslate_secondArgumentIsFalse_returnGlobalTranslationStringByCode()
    {
        $this->assertSame('SOME_CODE', $this->SUT->translate('SOME_CODE', false));
    }


    public function testGetCmsContent_noIdentFound_returnEmptyString()
    {
        $this->assertSame('', $this->SUT->getCmsContent('oxps_non_existing_ident!'));
    }

    public function testGetCmsContent_noSecondArgument_returnCmsSnippetContentByIdentWithNoHtml()
    {
        $this->assertSame('Hello, World! ', $this->SUT->getCmsContent('oxpstestident'));
    }

    public function testGetCmsContent_secondArgumentIsFalse_returnCmsSnippetContentByIdentInHtml()
    {
        $this->assertSame('<p>Hello,</p> <p><i>World!</i></p> ', $this->SUT->getCmsContent('oxpstestident', false));
    }


    public function testGetSetting_noSecondArgument_returnModuleSettingByItsNameWithNoModulePrefix()
    {
        \OxidEsales\Eshop\Core\Registry::getConfig()->setConfigParam('oxpsModuleGeneratorVendorPrefix', 'test');

        $this->assertSame('test', $this->SUT->getSetting('VendorPrefix'));
    }

    public function testGetSetting_secondArgumentIsFalse_returnModuleSettingByItsNameWithNoModulePrefix()
    {
        \OxidEsales\Eshop\Core\Registry::getConfig()->setConfigParam('sAdminEmail', 'test@example.com');

        $this->assertSame('test@example.com', $this->SUT->getSetting('sAdminEmail', false));
    }


    public function testGetPath()
    {
        $sPath = $this->SUT->getPath();

        $this->assertStringEndsWith('/oxps/ModuleGenerator/', $sPath);
        $this->assertFileExists($sPath);
    }
}
