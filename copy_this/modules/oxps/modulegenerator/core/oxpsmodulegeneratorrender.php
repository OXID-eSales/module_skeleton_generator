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
 * @package       ModuleGenerator
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

use \OxidEsales\Eshop\Core\Base;
use \OxidEsales\Eshop\Core\Registry;

/**
 * Class oxpsModuleGeneratorRender
 * Smarty templates (module files) rendering helper for new copied module files.
 */
class oxpsModuleGeneratorRender extends Base
{

    /**
     * A module instance to generate stuff for.
     *
     * @var null|oxpsModuleGeneratorOxModule
     */
    protected $_oModule = null;


    /**
     * Alias for `setModule`.
     *
     * @param oxpsModuleGeneratorOxModule $oModule
     */
    public function init(oxpsModuleGeneratorOxModule $oModule)
    {
        $this->setModule($oModule);
    }

    /**
     * Set module instance to generate stuff for.
     *
     * @param oxpsModuleGeneratorOxModule $oModule
     */
    public function setModule(oxpsModuleGeneratorOxModule $oModule)
    {
        $this->_oModule = $oModule;
    }

    /**
     * Get module instance to generate stuff for.
     *
     * @return oxpsModuleGeneratorOxModule.
     */
    public function getModule()
    {
        return $this->_oModule;
    }

    /**
     * Open each given file to render it with Smarty and write processed output back to corresponding file.
     * After that file is optionally renamed using files array key as a name.
     *
     * @param array $aClassesToExtend Module extended classes.
     * @param array $aNewClasses      New module classes.
     *
     * @return bool
     */
    public function renderModuleFiles($aClassesToExtend, $aNewClasses)
    {
        $aFilesToProcess = $this->_getFilesToProcess($aClassesToExtend, $aNewClasses);
        $aNewClasses = array_merge($aClassesToExtend, $aNewClasses);

        $this->renderWithSmartyAndRename($aFilesToProcess, $aNewClasses);

        return true;
    }

    /**
     * Open each class as Smarty template, render the template and write rendered content back to proper file,
     * then rename the file.
     * Sets module and class data to Smarty.
     *
     * @param array $aClasses
     * @param array $sClassesNames
     */
    public function renderWithSmartyAndRename(array $aClasses, array $sClassesNames)
    {
        $oModule = $this->getModule();
        $sModulePath = $oModule->getFullPath();

        /** @var oxpsModuleGeneratorValidator $oValidator */
        $oValidator = Registry::get('oxpsModuleGeneratorValidator');

        /** @var oxpsModuleGeneratorFileSystem $oFileSystemHelper */
        $oFileSystemHelper = Registry::get('oxpsModuleGeneratorFileSystem');

        // Initialize Smarty and process template files

        /** @var Smarty $oSmarty */
        $oSmarty = Registry::get(\OxidEsales\Eshop\Core\UtilsView::class)->getSmarty();
        $oSmarty->assign('oModule', $oModule);

        foreach ($aClasses as $sFileName => $sFilePath) {
            $oSmarty->assign('sFilePath', $sFilePath);
            $oSmarty->assign('sClassRealName', $oValidator->getArrayValue($sClassesNames, $sFilePath));

            $sFileFullPath = $sModulePath . $sFilePath;

            // TODO: remake condition as it preserves to generate composer.json in existing module
            if (!$oFileSystemHelper->isFile($sFileFullPath)) {
                continue;
            }

            // Render template file with Smarty and overwrite it
            $oFileSystemHelper->createFile($sFileFullPath, $oSmarty->fetch($sFileFullPath));

            if (is_string($sFileName)) {

                // Renaming the file
                $sFileName = str_replace('.php.tpl', '.php', $sFileName);
                $sNewFullPath = str_replace(basename($sFileFullPath), $sFileName, $sFileFullPath);
                $oFileSystemHelper->renameFile($sFileFullPath, $sNewFullPath);
            }

            $oSmarty->clear_assign('sFilePath');
            $oSmarty->clear_assign('sClassRealName');
        }
    }

    /**
     * Render file comment using a template and author/vendor data.
     *
     * @param string $sSubPackage Optional subpackage title.
     *
     * @return mixed
     */
    public function renderFileComment($sSubPackage = '')
    {
        $sBaseModulePath = realpath(dirname(__FILE__) . '/../../') . '/';
        $sCommentTemplate = $sBaseModulePath . 'ModuleGenerator/Core/module.tpl/oxpsComment.inc.php.tpl';

        /** @var Smarty $oSmarty */
        $oSmarty = Registry::get(\OxidEsales\Eshop\Core\UtilsView::class)->getSmarty();
        $oSmarty->assign('oModule', $this->getModule());

        if (!empty($sSubPackage)) {
            $oSmarty->assign('sSubPackage', (string) $sSubPackage);
        }

        return $oSmarty->fetch($sCommentTemplate);
    }


    /**
     * Collect copied module files, that need to be processed (rendered) with Smarty.
     *
     * @param array $aClassesToExtend Generated classes that overload (extend) eShop classes.
     * @param array $aNewClasses      Other newly generated classes.
     *
     * @return array
     */
    protected function _getFilesToProcess(array $aClassesToExtend, array $aNewClasses)
    {
        $sModuleId = $this->getModule()->getModuleId();

        $aFilesToProcess = array(
            $sModuleId . '_de_lang.php'       => 'Application/translations/de/oxpsModule_lang.php.tpl',
            $sModuleId . '_en_lang.php'       => 'Application/translations/en/oxpsModule_lang.php.tpl',
            $sModuleId . 'Module.php'         => 'Core/oxpsModule.php.tpl',
            'docs/install.sql',
            'docs/README.txt',
            'docs/uninstall.sql',
            $sModuleId . '_admin_de_lang.php' => 'Application/views/admin/de/oxpsModule_lang.php.tpl',
            $sModuleId . '_admin_en_lang.php' => 'Application/views/admin/en/oxpsModule_lang.php.tpl',
            '.ide-helper.php'                 => '.ide-helper.php.tpl',
            'composer.json'                   => 'composer.json.tpl',
            'metadata.php'                    => 'metadata.php.tpl',
        );

        $aFilesToProcess = array_merge($aFilesToProcess, array_keys($aClassesToExtend), array_keys($aNewClasses));

        return $aFilesToProcess;
    }
}
