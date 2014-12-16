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
 * Class oxpsModuleGeneratorHelper.
 * Implement helpers and additional methods for module generation.
 */
class oxpsModuleGeneratorHelper extends oxSuperCfg
{

    /**
     * A module instance to generate stuff for.
     *
     * @var oxModule
     */
    protected $_oModule = null;

    /**
     * File system helper instance.
     *
     * @var null
     */
    protected $_oFileSystemHelper = null;


    /**
     * Alias for `setModule`.
     *
     * @param oxModule|oxpsModuleGeneratorOxModule $oModule
     */
    public function init(oxpsModuleGeneratorOxModule $oModule)
    {
        $this->setModule($oModule);
    }

    /**
     * Set module instance to generate stuff for.
     *
     * @param oxModule|oxpsModuleGeneratorOxModule $oModule
     */
    public function setModule(oxpsModuleGeneratorOxModule $oModule)
    {
        $this->_oModule = $oModule;
    }

    /**
     * Get module instance to generate stuff for.
     *
     * @return oxModule|oxpsModuleGeneratorOxModule.
     */
    public function getModule()
    {
        return $this->_oModule;
    }

    /**
     * Get file system management helper instance.
     *
     * @return oxpsModuleGeneratorFileSystem
     */
    public function getFileSystemHelper()
    {
        if (is_null($this->_oFileSystemHelper)) {
            $this->_oFileSystemHelper = oxRegistry::get('oxpsModuleGeneratorFileSystem');
        }

        return $this->_oFileSystemHelper;
    }

    /**
     * Check if vendor metadata file is present and crates one if it's missing.
     *
     * @param string $sVendorFolderPath
     */
    public function createVendorMetadata($sVendorFolderPath)
    {
        $oFileSystemHelper = $this->getFileSystemHelper();

        $oFileSystemHelper->createFolder($sVendorFolderPath);
        $sVendorMetadataPath = $sVendorFolderPath . DIRECTORY_SEPARATOR . 'vendormetadata.php';

        $oFileSystemHelper->createFile(
            $sVendorMetadataPath,
            '<?php' . PHP_EOL .
            PHP_EOL .
            '/**' . PHP_EOL .
            ' * Metadata version' . PHP_EOL .
            ' */' . PHP_EOL .
            '$sVendorMetadataVersion = \'1.0\';' . PHP_EOL,
            true
        );
    }

    /**
     * Create classes to extend from a template and copy each to its proper destination.
     *
     * @param string $sClassExtendTemplatePath Template file to use.
     *
     * @return array Internal paths of created classes inside a module.
     */
    public function createClassesToExtend($sClassExtendTemplatePath)
    {
        $oFileSystemHelper = $this->getFileSystemHelper();
        $oModule = $this->getModule();

        $aClassesToExtend = (array) $oModule->getClassesToExtend();
        $sModulePath = $oModule->getFullPath();
        $sModuleId = $oModule->getModuleId();

        $aExtendedClasses = array();

        if (!$oFileSystemHelper->isFile($sClassExtendTemplatePath) or !$oFileSystemHelper->isDir($sModulePath)) {
            return $aExtendedClasses;
        }

        foreach ($aClassesToExtend as $sClassName => $mApplicationPath) {
            $sInModulePath = $this->_getPathInsideModule($sModulePath, $mApplicationPath);
            $sDestinationPath = $sModulePath . $sInModulePath;
            $sClassFileName = $sModuleId . oxStr::getStr()->strtolower($sClassName) . '.php';
            $sClassFilePath = $sDestinationPath . $sClassFileName;

            $oFileSystemHelper->copyFile($sClassExtendTemplatePath, $sClassFilePath);

            $aExtendedClasses[$sInModulePath . $sClassFileName] = $this->_getCoreClassName($sClassName);
        }

        return $aExtendedClasses;
    }

    /**
     * Collect information of new classes to create, copy new classes from corresponding templates,
     * create templates for controllers and widgets.
     *
     * @param string $sModuleGeneratorPath
     *
     * @return array
     */
    public function createNewClassesAndTemplates($sModuleGeneratorPath)
    {
        /** @var oxpsModuleGeneratorValidator $oValidator */
        $oValidator = oxRegistry::get('oxpsModuleGeneratorValidator');

        $aClassesToCreate = (array) $this->getModule()->getClassesToCreate();
        $aCreatedClasses = array();

        foreach ($aClassesToCreate as $sObjectType => $aClassesData) {
            $aClassesData = (array) $aClassesData;
            $aClasses = (array) $oValidator->getArrayValue($aClassesData, 'aClasses', 'array');
            $sClassTemplate = $oValidator->getArrayValue($aClassesData, 'sTemplateName');
            $sClassPath = $oValidator->getArrayValue($aClassesData, 'sInModulePath');
            $sTemplatePath = $oValidator->getArrayValue($aClassesData, 'sTemplatesPath');

            if (empty($aClasses)) {
                continue;
            }

            $aNewFiles = $this->_createNewClasses($aClasses, $sClassTemplate, $sClassPath, $sModuleGeneratorPath);
            $this->_createNewTemplates($sObjectType, $aNewFiles, $sTemplatePath);

            $aCreatedClasses = array_merge($aCreatedClasses, $aNewFiles);
        }

        return $aCreatedClasses;
    }

    /**
     * Create module blocks templates.
     *
     * @param string $sModulePath
     */
    public function createBlock($sModulePath)
    {
        $aBlock = $this->getModule()->getBlocks();

        if (!empty($aBlock)) {
            $this->_createTemplates(array_keys($aBlock), sprintf('%sviews/blocks/', $sModulePath), true);
        }
    }

    /**
     * Clone and copy tests folder into generated module,
     * create pre-configured unit test class for each generated module class.
     *
     * @param oxpsModuleGeneratorRender $oRenderHelper
     * @param string                    $sModuleGeneratorPath
     * @param string                    $sModulePath
     * @param array                     $aClassesToExtend
     * @param array                     $aNewClasses
     */
    public function fillTestsFolder(oxpsModuleGeneratorRender $oRenderHelper, $sModuleGeneratorPath, $sModulePath,
                                    array $aClassesToExtend, array $aNewClasses)
    {
        if ($this->_cloneUnitTests($sModulePath)) {
            $aAllFiles = array_merge($aClassesToExtend, $aNewClasses);
            $sTemplate = sprintf('%score/module.tpl/oxpstestclass.php.tpl', $sModuleGeneratorPath);
            $aNewFiles = (array) $this->_copyNewClasses($aAllFiles, $sTemplate, 'tests/unit/modules/', true);

            if (!empty($aNewFiles)) {
                $oRenderHelper->renderWithSmartyAndRename(array_keys($aNewFiles), $aNewFiles);
            }
        }
    }

    /**
     * Check if provided path inside a module exists, if not choose "core" folder as internal path.
     *
     * @param string $sModulePath
     * @param string $mInnerPath
     *
     * @return string
     */
    protected function _getPathInsideModule($sModulePath, $mInnerPath)
    {
        if (!empty($mInnerPath) and $this->getFileSystemHelper()->isDir($sModulePath . $mInnerPath)) {
            $sPathInsideModule = $mInnerPath;
        } else {
            $sPathInsideModule = 'core/';
        }

        return $sPathInsideModule;
    }

    /**
     * Check if class template file and path are valid and create new classes using the provided class template and path.
     *
     * @param array  $aClasses
     * @param string $sClassTemplate
     * @param string $sClassPath
     * @param string $sModuleGeneratorPath
     *
     * @return array
     */
    protected function _createNewClasses(array $aClasses, $sClassTemplate, $sClassPath, $sModuleGeneratorPath)
    {
        $aNewFiles = array();

        if (!empty($sClassTemplate) and !empty($sClassPath)) {
            $sTemplatePath = sprintf('%score/module.tpl/%s', $sModuleGeneratorPath, $sClassTemplate);
            $aNewFiles = $this->_copyNewClasses($aClasses, $sTemplatePath, $sClassPath);
        }

        return $aNewFiles;
    }

    /**
     * Check if class type requires a template to be created,
     * check template path and the classes and then create new templates.
     *
     * @param string $sObjectType
     * @param array  $aNewFiles
     * @param string $sTemplatePath
     */
    protected function _createNewTemplates($sObjectType, array $aNewFiles, $sTemplatePath)
    {
        if (in_array($sObjectType, array('controllers', 'widgets')) and
            !empty($aNewFiles) and !empty($sTemplatePath)
        ) {
            $sTemplateDestination = sprintf('%sviews/%s/', $this->getModule()->getFullPath(), $sTemplatePath);
            $this->_createTemplates($aNewFiles, $sTemplateDestination);
        }
    }

    /**
     * Get format string for newly generated templates demo content.
     *
     * @param bool $blBlocks If True, renders block template content, if False - controller/widget template content.
     *
     * @return string
     */
    protected function _getTemplateContentFormat($blBlocks = false)
    {
        if (empty($blBlocks)) {
            $sDemoContent = '<h1>A Blank %s Template</h1>' . PHP_EOL .
                            '<p>This is a template located in %s belonging to class %s</p>';
        } else {
            $sDemoContent = '<h1>A Blank Block Template</h1>' . PHP_EOL .
                            '<p>This is a template for block %s located in %s</p>' . PHP_EOL .
                            '[{$smarty.block.parent}]';
        }

        return $sDemoContent;
    }

    /**
     * Create new classes using a template and provided class names by copying this template to the provided path.
     *
     * @param array  $aClasses      Class names array.
     * @param string $sTemplatePath Template file full path.
     * @param string $sInModulePath Module inner path to copy to.
     * @param bool   $blTestClasses If True, treat as test classes.
     *
     * @return array
     */
    protected function _copyNewClasses(array $aClasses, $sTemplatePath, $sInModulePath, $blTestClasses = false)
    {
        $oFileSystemHelper = $this->getFileSystemHelper();
        $aCopiedClasses = array();

        if (!$oFileSystemHelper->isFile($sTemplatePath) or
            !$oFileSystemHelper->isDir($this->getModule()->getFullPath() . $sInModulePath)
        ) {
            return $aCopiedClasses;
        }

        foreach ($aClasses as $mKey => $sClass) {
            list($sClassFilePath, $sProcessedFileKey) = $this->_getNewClassPathAndKey(
                $mKey, $sClass, $sInModulePath, $blTestClasses
            );
            $oFileSystemHelper->copyFile($sTemplatePath, $sClassFilePath);

            $aCopiedClasses[$sProcessedFileKey] = $sClass;
        }

        return $aCopiedClasses;
    }

    /**
     * Compile full path to a new class and a key value to use for created classes array.
     * It depends on whatever it is a Unit test class or general PHP class.
     *
     * @param int|string $mKey
     * @param string     $sClass
     * @param string     $sInModulePath
     * @param bool       $blTestClasses
     *
     * @return array
     */
    protected function _getNewClassPathAndKey($mKey, $sClass, $sInModulePath, $blTestClasses = false)
    {
        $oModule = $this->getModule();

        $sModuleId = $oModule->getModuleId();
        $sModulePath = $oModule->getFullPath();

        if (empty($blTestClasses)) {
            $sClassFileName = sprintf('%s%s.php', $sModuleId, oxStr::getStr()->strtolower($sClass));
            $sClassFilePath = $sModulePath . $sInModulePath . $sClassFileName;
            $sProcessedFileKey = $sInModulePath . $sClassFileName;
        } else {
            $sClassFileName = sprintf('%s%sTest.php', $sModuleId, oxStr::getStr()->strtolower($sClass));
            $sClassDirPath = $sModulePath . $sInModulePath . dirname($mKey) . DIRECTORY_SEPARATOR;
            $this->getFileSystemHelper()->createFolder($sClassDirPath);
            $sClassFilePath = $sClassDirPath . $sClassFileName;
            $sProcessedFileKey = $sInModulePath . dirname($mKey) . DIRECTORY_SEPARATOR . $sClassFileName;
        }

        return array($sClassFilePath, $sProcessedFileKey);
    }

    /**
     * Create template files for a given classes in provided location.
     *
     * @param array  $aClasses         Class names array to create templates for.
     * @param string $sDestinationPath A full path to folder to create templates in.
     * @param bool   $blBlocks         If true block templates are generated.
     *
     * @return bool
     */
    protected function _createTemplates(array $aClasses, $sDestinationPath, $blBlocks = false)
    {
        $oFileSystemHelper = $this->getFileSystemHelper();

        if (!$oFileSystemHelper->isDir($sDestinationPath)) {
            return false;
        }

        $oModule = $this->getModule();

        $sModuleId = $oModule->getModuleId();
        $sClassPrefix = $oModule->getModuleClassName();

        $sDemoContentFormat = $this->_getTemplateContentFormat($blBlocks);

        foreach ($aClasses as $sClass) {
            $sTemplateFileName = sprintf('%s%s.tpl', $sModuleId, oxStr::getStr()->strtolower($sClass));
            $sTemplateFilePath = $sDestinationPath . $sTemplateFileName;
            $sTemplateContent = sprintf($sDemoContentFormat, $sClass, $sTemplateFilePath, $sClassPrefix . $sClass);

            $oFileSystemHelper->createFile($sTemplateFilePath, $sTemplateContent);
        }

        return true;
    }

    /**
     * Get real class name of existing core class (for class that are being extended).
     *
     * @param string $sClassName
     *
     * @return string
     */
    protected function _getCoreClassName($sClassName)
    {
        if (!class_exists($sClassName)) {
            return $sClassName;
        }

        $oReflection = new ReflectionClass(new $sClassName());

        return $oReflection->getName();
    }

    /**
     * Clone tests library (Generic Test Folder) to temp location,
     * then copy tests library into the module and do a cleanup.
     *
     * @param string $sModulePath Full path to a module.
     *
     * @return bool
     */
    protected function _cloneUnitTests($sModulePath)
    {
        /** @var oxpsModuleGeneratorModule $oModule */
        $oModule = oxRegistry::get('oxpsModuleGeneratorModule');
        $sGitUrl = (string) $oModule->getSetting('TestsGitUrl');

        if (empty($sGitUrl)) {
            return false;
        }

        $sTmpDir = sys_get_temp_dir() . '/tests' . date('YmdHis') . mt_rand(10000, 99999) . '/';
        $aCommands = array(
            sprintf('git clone %s %s', $sGitUrl, $sTmpDir),
            sprintf('cp -R %stests/* %stests/', $sTmpDir, $sModulePath),
            sprintf('chmod 0777 -R %stests', $sModulePath),
            sprintf('dos2unix %stests/*.sh', $sModulePath),
            sprintf('rm -rf %s', $sTmpDir),
        );

        foreach ($aCommands as $sCommand) {
            $this->_shellExec($sCommand);
        }

        return $this->getFileSystemHelper()->isDir($sModulePath . 'tests/unit/');
    }

    /**
     * An alias for PHP function `shell_exec`.
     * Errors are suspended.
     *
     * @codeCoverageIgnore
     *
     * @param string $sCommand
     */
    protected function _shellExec($sCommand)
    {
        @shell_exec((string) $sCommand);
    }
}
