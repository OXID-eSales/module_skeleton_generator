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
 * Class oxpsModuleGeneratorRenderTest
 * UNIT tests for core class oxpsModuleGeneratorRender.
 *
 * @see oxpsModuleGeneratorRender
 */
class oxpsModuleGeneratorRenderTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var oxpsModuleGeneratorRender
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('oxpsModuleGeneratorRender', array('__call'));
    }


    public function testInit()
    {
        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('__construct', '__call'));

        $this->SUT->init($oModule);

        $this->assertSame($oModule, $this->SUT->getModule());
    }


    public function testGetModule()
    {
        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('__construct', '__call'));

        $this->SUT->setModule($oModule);

        $this->assertSame($oModule, $this->SUT->getModule());
    }


    /**
     * Test expect the following sequence of...
     *
     * ----------------------------------------------------------------------------------------------------------
     *  Rename file name                 => Template path                                FS exec     Smarty exec
     * ----------------------------------------------------------------------------------------------------------
     *  'oxpsmymodule_de_lang.php'       => 'translations/de/oxpsmodule_lang.php.tpl',   0-1         1-5
     *  'oxpsmymodule_en_lang.php'       => 'translations/en/oxpsmodule_lang.php.tpl',   2-3         6-10
     *  'oxpsmymodulemodule.php'         => 'core/oxpsmodule.php.tpl',                   4-5         11-15
     *  'docs/install.sql',                                                              6           16-20
     *  'docs/README.txt',                                                               7           21-25
     *  'docs/uninstall.sql',                                                            8           26-30
     *  'oxpsmymodule_admin_de_lang.php' => 'views/admin/de/oxpsmodule_lang.php.tpl',    9-10        31-35
     *  'oxpsmymodule_admin_en_lang.php' => 'views/admin/en/oxpsmodule_lang.php.tpl',    11-12       36-40
     *  'metadata.php'                   => 'metadata.php.tpl',                          13-14       41-45
     *  'models/oxpsmymoduleoxarticle.php',                                              15          46-50
     *  'controllers/oxpsmymodulepage.php',                                              16          51-55
     *  'models/oxpsmymoduleitem.php',                                                   17          56-60
     * ----------------------------------------------------------------------------------------------------------
     */
    public function testRenderModuleFiles()
    {
        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'createFile', 'renameFile'));

        /* German translation file */
        $oFileSystem->expects($this->at(0))->method('createFile')
            ->with(
                '/path/to/modules/oxps/mymodule/translations/de/oxpsmodule_lang.php.tpl',
                '_processed_de_trans_content_'
            );
        $oFileSystem->expects($this->at(1))->method('renameFile')
            ->with(
                '/path/to/modules/oxps/mymodule/translations/de/oxpsmodule_lang.php.tpl',
                '/path/to/modules/oxps/mymodule/translations/de/oxpsmymodule_de_lang.php'
            );

        /* Readme file */
        $oFileSystem->expects($this->at(7))->method('createFile')
            ->with('/path/to/modules/oxps/mymodule/docs/README.txt', '_processed_readme_content_');

        /* Extended oxArticle model */
        $oFileSystem->expects($this->at(15))->method('createFile')
            ->with('/path/to/modules/oxps/mymodule/models/oxpsmymoduleoxarticle.php', '_processed_oxarticle_content_');

        /* New "Item" model */
        $oFileSystem->expects($this->at(17))->method('createFile')
            ->with('/path/to/modules/oxps/mymodule/models/oxpsmymoduleitem.php', '_processed_item_content_');

        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getModuleId', 'getFullPath')
        );
        $oModule->expects($this->once())->method('getModuleId')->will($this->returnValue('oxpsmymodule'));
        $oModule->expects($this->once())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));

        // Smarty mock
        /* German translation file */
        $oSmarty = $this->getMock('Smarty', array('assign', 'fetch', 'clear_assign'));
        $oSmarty->expects($this->at(0))->method('assign')->with('oModule', $oModule);

        $oSmarty->expects($this->at(1))->method('assign')->with('sFilePath', 'translations/de/oxpsmodule_lang.php.tpl');
        $oSmarty->expects($this->at(2))->method('assign')->with('sClassRealName', '');
        $oSmarty->expects($this->at(3))->method('fetch')
            ->with('/path/to/modules/oxps/mymodule/translations/de/oxpsmodule_lang.php.tpl')
            ->will($this->returnValue('_processed_de_trans_content_'));
        $oSmarty->expects($this->at(4))->method('clear_assign')->with('sFilePath');
        $oSmarty->expects($this->at(5))->method('clear_assign')->with('sClassRealName');

        /* Readme file */
        $oSmarty->expects($this->at(21))->method('assign')->with('sFilePath', 'docs/README.txt');
        $oSmarty->expects($this->at(23))->method('fetch')
            ->with('/path/to/modules/oxps/mymodule/docs/README.txt')
            ->will($this->returnValue('_processed_readme_content_'));

        /* Extended oxArticle model */
        $oSmarty->expects($this->at(46))->method('assign')->with('sFilePath', 'models/oxpsmymoduleoxarticle.php');
        $oSmarty->expects($this->at(47))->method('assign')->with('sClassRealName', 'oxArticle');
        $oSmarty->expects($this->at(48))->method('fetch')
            ->with('/path/to/modules/oxps/mymodule/models/oxpsmymoduleoxarticle.php')
            ->will($this->returnValue('_processed_oxarticle_content_'));

        /* Extended oxArticle model */
        $oSmarty->expects($this->at(56))->method('assign')->with('sFilePath', 'models/oxpsmymoduleitem.php');
        $oSmarty->expects($this->at(57))->method('assign')->with('sClassRealName', 'Item');
        $oSmarty->expects($this->at(58))->method('fetch')
            ->with('/path/to/modules/oxps/mymodule/models/oxpsmymoduleitem.php')
            ->will($this->returnValue('_processed_item_content_'));

        // View utils mock
        $oViewUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('__call', 'getSmarty'));
        $oViewUtils->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsView::class, $oViewUtils, null);

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
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('__construct', '__call'));

        // Smarty mock
        $oSmarty = $this->getMock('Smarty', array('assign', 'fetch'));
        $oSmarty->expects($this->at(0))->method('assign')->with('oModule', $oModule);
        $oSmarty->expects($this->at(1))->method('fetch')
            ->with($this->stringEndsWith('modulegenerator/core/module.tpl/oxpscomment.inc.php.tpl'))
            ->will($this->returnValue('_processed_comment_content_'));

        // View utils mock
        $oViewUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('__call', 'getSmarty'));
        $oViewUtils->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsView::class, $oViewUtils, null);

        $this->SUT->init($oModule);

        $this->assertSame('_processed_comment_content_', $this->SUT->renderFileComment());
    }

    public function testRenderFileComment_argumentNotEmpty_rendersCommentWithSubPackageArgumentInfo()
    {
        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('__construct', '__call'));

        // Smarty mock
        $oSmarty = $this->getMock('Smarty', array('assign', 'fetch'));
        $oSmarty->expects($this->at(0))->method('assign')->with('oModule', $oModule);
        $oSmarty->expects($this->at(1))->method('assign')->with('sSubPackage', 'mySubModule');
        $oSmarty->expects($this->at(2))->method('fetch')
            ->with($this->stringEndsWith('modulegenerator/core/module.tpl/oxpscomment.inc.php.tpl'))
            ->will($this->returnValue('_processed_comment_content_'));

        // View utils mock
        $oViewUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('__call', 'getSmarty'));
        $oViewUtils->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsView::class, $oViewUtils, null);

        $this->SUT->init($oModule);

        $this->assertSame('_processed_comment_content_', $this->SUT->renderFileComment('mySubModule'));
    }
}
