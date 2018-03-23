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

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\TestingLibrary\UnitTestCase;
use Oxps\ModuleGenerator\Core\FileSystem;
use Oxps\ModuleGenerator\Core\OxModule;
use Oxps\ModuleGenerator\Core\Render;

/**
 * Class RenderTest
 * UNIT tests for core class Render.
 *
 * @see render
 */
class RenderTest extends UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var render
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock(Render::class, array('__call'));
    }


    public function testInit()
    {
        // Module instance mock
        $oModule = $this->getMock(OxModule::class, array('__construct', '__call'));

        $this->SUT->init($oModule);

        $this->assertSame($oModule, $this->SUT->getModule());
    }


    public function testGetModule()
    {
        // Module instance mock
        $oModule = $this->getMock(OxModule::class, array('__construct', '__call'));

        $this->SUT->setModule($oModule);

        $this->assertSame($oModule, $this->SUT->getModule());
    }


    /**
     * Test expect the following sequence of...
     * @see https://oxid-esales.atlassian.net/browse/PSGEN-229 attached files for sequence revision
     * -----------------------------------------------------------------------------------------------
     *  Rename file name                    => Template path
     * -----------------------------------------------------------------------------------------------
     *  'oxpsmymodule_de_lang.php'          => 'Application/translations/de/oxpsModule_lang.php.tpl',
     *  'oxpsmymodule_en_lang.php'          => 'Application/translations/en/oxpsModule_lang.php.tpl',
     *  'oxpsmymodulemodule.php'            => 'Core/oxpsModule.php.tpl',
     *  'docs/install.sql',
     *  'docs/README.txt',
     *  'docs/uninstall.sql',
     *  'oxpsmymodule_admin_de_lang.php'    => 'Application/views/admin/de/oxpsModule_lang.php.tpl',
     *  'oxpsmymodule_admin_en_lang.php'    => 'Application/views/admin/en/oxpsModule_lang.php.tpl',
     *  '.ide-helper.php',                  => '.ide-helper.php.tpl',
     *  'composer.json',                    => 'composer.json.tpl',
     *  'metadata.php'                      => 'metadata.php.tpl',
     *  'models/oxpsmymoduleoxarticle.php',
     *  'controllers/oxpsmymodulepage.php',
     *  'models/oxpsmymoduleitem.php',
     * -----------------------------------------------------------------------------------------------
     */
    public function testRenderModuleFiles()
    {
        // File system helper mock
        $oFileSystem = $this->getMock(
            FileSystem::class,
            array('__call', 'createFile', 'renameFile', 'isFile')
        );

        $oFileSystem->expects($this->any())->method('isFile')->willReturn(true);

        /* German translation file */
        $oFileSystem->expects($this->at(1))->method('createFile')
            ->with(
                '/path/to/modules/oxps/mymodule/Application/translations/de/oxpsModule_lang.php.tpl',
                '_processed_de_trans_content_'
            );
        $oFileSystem->expects($this->at(2))->method('renameFile')
            ->with(
                '/path/to/modules/oxps/mymodule/Application/translations/de/oxpsModule_lang.php.tpl',
                '/path/to/modules/oxps/mymodule/Application/translations/de/oxpsmymodule_de_lang.php'
            );

        /* Readme file */
        $oFileSystem->expects($this->at(12))->method('createFile')
            ->with('/path/to/modules/oxps/mymodule/docs/README.txt', '_processed_readme_content_');

        /* Extended oxArticle model */
        $oFileSystem->expects($this->at(31))->method('createFile')
            ->with('/path/to/modules/oxps/mymodule/models/oxpsmymoduleoxarticle.php', '_processed_oxarticle_content_');

        /* New "Item" model */
        $oFileSystem->expects($this->at(35))->method('createFile')
            ->with('/path/to/modules/oxps/mymodule/models/oxpsmymoduleitem.php', '_processed_item_content_');

        Registry::set(FileSystem::class, $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            OxModule::class,
            array('__construct', '__call', 'getModuleId', 'getFullPath')
        );
        $oModule->expects($this->once())
            ->method('getModuleId')
            ->will($this->returnValue('oxpsmymodule'));
        $oModule->expects($this->once())
            ->method('getFullPath')
            ->will($this->returnValue('/path/to/modules/oxps/mymodule/'));

        // Smarty mock
        /* German translation file */
        $oSmarty = $this->getMock('Smarty', array('assign', 'fetch', 'clear_assign'));
        $oSmarty->expects($this->at(0))->method('assign')->with('oModule', $oModule);

        $oSmarty->expects($this->at(1))->method('assign')
            ->with('sFilePath', 'Application/translations/de/oxpsModule_lang.php.tpl');
        $oSmarty->expects($this->at(2))->method('assign')->with('sClassRealName', '');
        $oSmarty->expects($this->at(3))->method('fetch')
            ->with('/path/to/modules/oxps/mymodule/Application/translations/de/oxpsModule_lang.php.tpl')
            ->will($this->returnValue('_processed_de_trans_content_'));
        $oSmarty->expects($this->at(4))->method('clear_assign')->with('sFilePath');
        $oSmarty->expects($this->at(5))->method('clear_assign')->with('sClassRealName');

        /* Readme file */
        $oSmarty->expects($this->at(21))->method('assign')->with('sFilePath', 'docs/README.txt');
        $oSmarty->expects($this->at(23))->method('fetch')
            ->with('/path/to/modules/oxps/mymodule/docs/README.txt')
            ->will($this->returnValue('_processed_readme_content_'));

        /* Extended oxArticle model */
        $oSmarty->expects($this->at(56))->method('assign')->with('sFilePath', 'models/oxpsmymoduleoxarticle.php');
        $oSmarty->expects($this->at(57))->method('assign')->with('sClassRealName', 'oxArticle');
        $oSmarty->expects($this->at(58))->method('fetch')
            ->with('/path/to/modules/oxps/mymodule/models/oxpsmymoduleoxarticle.php')
            ->will($this->returnValue('_processed_oxarticle_content_'));

        /* Extended oxArticle model */
        $oSmarty->expects($this->at(66))->method('assign')->with('sFilePath', 'models/oxpsmymoduleitem.php');
        $oSmarty->expects($this->at(67))->method('assign')->with('sClassRealName', 'Item');
        $oSmarty->expects($this->at(68))->method('fetch')
            ->with('/path/to/modules/oxps/mymodule/models/oxpsmymoduleitem.php')
            ->will($this->returnValue('_processed_item_content_'));

        // View utils mock
        $oViewUtils = $this->getMock(UtilsView::class, array('__call', 'getSmarty'));
        $oViewUtils->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        Registry::set(UtilsView::class, $oViewUtils, null);

        $this->SUT->init($oModule);

        $this->assertTrue(
            $this->SUT->renderModuleFiles(
                array('models/oxpsmymoduleoxarticle.php' => 'oxArticle'),
                array(
                    'controllers/oxpsmymodulepage.php' => 'Page',
                    'models/oxpsmymoduleitem.php'      => 'Item',
                )
            )
        );
    }


    public function testRenderFileComment_emptyArgument_rendersCommentWithNoSubPackageInfo()
    {
        // Module instance mock
        $oModule = $this->getMock(OxModule::class, array('__construct', '__call'));

        // Smarty mock
        $oSmarty = $this->getMock('Smarty', array('assign', 'fetch'));
        $oSmarty->expects($this->at(0))->method('assign')->with('oModule', $oModule);
        $oSmarty->expects($this->at(1))->method('fetch')
            ->with($this->stringEndsWith('ModuleGenerator/Core/module.tpl/oxpsComment.inc.php.tpl'))
            ->will($this->returnValue('_processed_comment_content_'));

        // View utils mock
        $oViewUtils = $this->getMock(UtilsView::class, array('__call', 'getSmarty'));
        $oViewUtils->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        Registry::set(UtilsView::class, $oViewUtils, null);

        $this->SUT->init($oModule);

        $this->assertSame('_processed_comment_content_', $this->SUT->renderFileComment());
    }

    public function testRenderFileComment_argumentNotEmpty_rendersCommentWithSubPackageArgumentInfo()
    {
        // Module instance mock
        $oModule = $this->getMock(OxModule::class, array('__construct', '__call'));

        // Smarty mock
        $oSmarty = $this->getMock('Smarty', array('assign', 'fetch'));
        $oSmarty->expects($this->at(0))->method('assign')->with('oModule', $oModule);
        $oSmarty->expects($this->at(1))->method('assign')->with('sSubPackage', 'mySubModule');
        $oSmarty->expects($this->at(2))->method('fetch')
            ->with($this->stringEndsWith('ModuleGenerator/Core/module.tpl/oxpsComment.inc.php.tpl'))
            ->will($this->returnValue('_processed_comment_content_'));

        // View utils mock
        $oViewUtils = $this->getMock(UtilsView::class, array('__call', 'getSmarty'));
        $oViewUtils->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        Registry::set(UtilsView::class, $oViewUtils, null);

        $this->SUT->init($oModule);

        $this->assertSame('_processed_comment_content_', $this->SUT->renderFileComment('mySubModule'));
    }
}
