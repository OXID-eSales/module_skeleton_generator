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

use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\Str;

/**
 * Class oxpsModuleGeneratorOxModule overloads \OxidEsales\Eshop\Core\Module
 * Extends \OxidEsales\Eshop\Core\Module class to add extra fields and tools for modules generation.
 * The class becomes a container for all information required to generate  module.
 * NOTE: This class is very long, but it cannot be split since consists mostly of getters and setters.
 *
 * @see \OxidEsales\Eshop\Core\Module
 */
class oxpsModuleGeneratorOxModule extends oxpsModuleGeneratorOxModule_parent
{

    /**
     * Vendor directory name and module vendor prefix.
     *
     * @var string
     */
    protected $_sVendorPrefix = '';

    /**
     * Module author/vendor data: name, link, mail, copyright and file comment.
     *
     * @var array
     */
    protected $_aAuthorData = array();

    /**
     * Validator helper instance.
     *
     * @var null|oxpsModuleGeneratorValidator
     */
    protected $_oValidator = null;

    /**
     * Edit Mode flag.
     *
     * @var null|bool
     */
    protected $_blEditMode = null;

    /**
     * Generated module name.
     *
     * @var string
     */
    protected $_sModuleName = '';

    /**
     * @var array
     */
    protected $_aGenerationOptions = [];

    /**
     * @var array
     */
    protected $_aParsedMetadataOptions = [];

    /**
     * Array for storing filenames that need to be backed up during Edit Mode.
     *
     * @var array
     */
    protected $_aFilesToBackupOnEdit = [
        'metadata.php',
        '.ide-helper.php',
    ];

    /**
     * File templates to ignore in edit mode.
     *
     * @var array
     */
    protected $_aIgnoreOnEdit = array(
        'oxpsModule_lang.php.tpl',
        'oxpsModule.php.tpl',
        'install.sql',
        'README.txt',
        'uninstall.sql',
        'composer.json.tpl',
    );


    /**
     * Set module vendor prefix. It is also a vendor directory name.
     *
     * @param string $sVendorPrefix
     */
    public function setVendorPrefix($sVendorPrefix)
    {
        $this->_sVendorPrefix = $sVendorPrefix;
    }

    /**
     * Get module vendor prefix. It is also vendor directory name.
     *
     * @param bool $blUppercase
     *
     * @return string
     */
    public function getVendorPrefix($blUppercase = false)
    {
        if (!empty($blUppercase)) {

            /** @var \OxidEsales\Eshop\Core\StrMb|\OxidEsales\Eshop\Core\StrRegular $oStr */
            $oStr = Str::getStr();

            return $oStr->strtoupper($this->_sVendorPrefix);
        }

        return $this->_sVendorPrefix;
    }

    /**
     * Set module author/vendor data. It is used in metadata, README file and PHP files comments.
     *
     * @param array $aAuthorData
     */
    public function setAuthorData(array $aAuthorData)
    {
        $this->_aAuthorData = $aAuthorData;
    }

    /**
     * Get module author/vendor data. It is used in metadata, README file and PHP files comments.
     *
     * @param string $mField
     *
     * @return array|string
     */
    public function getAuthorData($mField = null)
    {
        if (!is_null($mField)) {
            return $this->getArrayValue($this->_aAuthorData, $mField);
        }

        return $this->_aAuthorData;
    }

    /**
     * Get validation and data access helper instance.
     *
     * @return oxpsModuleGeneratorValidator
     */
    public function getValidator()
    {
        if (is_null($this->_oValidator)) {
            $this->_oValidator = Registry::get('oxpsModuleGeneratorValidator');
        }

        return $this->_oValidator;
    }

    /**
     * Get module ID.
     *
     * @param bool $blCamelCase If True - returns CamelCase ID interpretation (module classes prefix),
     *                          if False - a lowercase value.
     *
     * @return string|null
     */
    public function getModuleId($blCamelCase = true)
    {
        return empty($blCamelCase) ? $this->getId() : $this->getInfo('oxpsmodulegenerator_class');
    }

    /**
     * Get module folder name.
     *
     * @param boolean $blUppercase If True - returns uppercase name, if False - CamelCase.
     *
     * @return string|null
     */
    public function getModuleFolderName($blUppercase = false)
    {
        $sFolderName = $this->getInfo('oxpsmodulegenerator_folder');

        if ($blUppercase and !is_null($sFolderName)) {

            /** @var \OxidEsales\Eshop\Core\StrMb|\OxidEsales\Eshop\Core\StrRegular $oStr */
            $oStr = Str::getStr();

            $sFolderName = $oStr->strtoupper($sFolderName);
        }

        return $sFolderName;
    }

    /**
     * Get module main class name. An alias for getModuleId method with True argument.
     *
     * @return string|boolean
     */
    public function getModuleClassName()
    {
        return $this->getModuleId(true);
    }

    /**
     * Get a list of classes to overload (extend).
     *
     * @return array
     */
    public function getClassesToExtend()
    {
        return (array) $this->getInfo('oxpsmodulegenerator_extend_classes');
    }

    /**
     * Get a list of new classes to create.
     * Each entry comes as array with:
     *   `aClasses`       - An array of classes names.
     *   `sTemplateName`  - A template to use for new classes creation.
     *   `sInModulePath`  - A path inside a module to place files in.
     *   `sTemplatesPath` - A folder to keep related class template in.
     *
     * @param null|string $sObjectType   Objects type to return, e.g. 'models'. Returns all if null.
     * @param null|string $sObjectsParam Objects param to return, e.g. 'aClasses'. Returns all if null.
     *
     * @return array|string
     */
    public function getClassesToCreate($sObjectType = null, $sObjectsParam = null)
    {
        $mData = array(
            'widgets'     => array(
                'aClasses'       => (array) $this->getInfo('oxpsmodulegenerator_widgets'),
                'sTemplateName'  => 'oxpsWidgetClass.php.tpl',
                'sInModulePath'  => 'Application/Component/Widget/',
                'sTemplatesPath' => 'widgets',
            ),
            'controllers' => array(
                'aClasses'       => (array) $this->getInfo('oxpsmodulegenerator_controllers'),
                'sTemplateName'  => 'oxpsControllerClass.php.tpl',
                'sInModulePath'  => 'Application/Controller/',
                'sTemplatesPath' => 'pages',
            ),
            'models'      => array(
                'aClasses'      => (array) $this->getInfo('oxpsmodulegenerator_models'),
                'sTemplateName' => 'oxpsModelClass.php.tpl',
                'sInModulePath' => 'Application/Model/',
            ),
            'list_models' => array(
                'aClasses'      => (array) $this->getInfo('oxpsmodulegenerator_list_models'),
                'sTemplateName' => 'oxpsListModelClass.php.tpl',
                'sInModulePath' => 'Application/Model/',
            ),
        );

        if (!is_null($sObjectType)) {

            // Get only one type of classes
            $mData = (array) $this->getArrayValue($mData, $sObjectType, 'array');

            if (!is_null($sObjectsParam)) {

                // Get only one param of one class type
                $sType = ($sObjectsParam == 'aClasses') ? 'array' : 'string';
                $mData = $this->getArrayValue($mData, $sObjectsParam, $sType);
            }
        }

        return $mData;
    }

    /**
     * Get module blocks array.
     *
     * @return array
     */
    public function getBlocks()
    {
        return (array) $this->getInfo('oxpsmodulegenerator_blocks');
    }

    /**
     * Get module settings array.
     *
     * @return array
     */
    public function getSettings()
    {
        return (array) $this->getInfo('oxpsmodulegenerator_module_settings');
    }

    /**
     * Parser setting type "select" options.
     *
     * @param string $sOptions
     *
     * @return array
     */
    public function getSelectSettingOptions($sOptions)
    {
        return (array) explode('|', str_replace("'", "", trim($sOptions)));
    }

    /**
     * Get a list of theme IDs to generate tempalte for - multi-theme support.
     * List with empty string means use default templates generation - same for all themes.
     *
     * @return array
     */
    public function getThemesList()
    {
        if ($this->getInfo('oxpsmodulegenerator_module_theme_none')) {
            return array('');
        }

        $aThemes = (array) $this->getInfo('oxpsmodulegenerator_module_theme_list');

        return empty($aThemes) ? array('') : $aThemes;
    }

    /**
     * Get module version.
     *
     * @return string
     */
    public function getInitialVersion()
    {
        return (string) $this->getInfo('oxpsmodulegenerator_module_init_version');
    }

    /**
     * Should the generator render "To Do" hints and tasks, e.g. hints how to use metadata samples, etc.
     *
     * @return bool
     */
    public function renderTasks()
    {
        return (bool) $this->getInfo('oxpsmodulegenerator_render_tasks');
    }

    /**
     * Should the generator render sample data and example code, e.g. sample metadata lines, etc.
     *
     * @return bool
     */
    public function renderSamples()
    {
        return (bool) $this->getInfo('oxpsmodulegenerator_render_samples');
    }

    /**
     * Get a full path of vendor directory.
     *
     * @return string
     */
    public function getVendorPath()
    {
        return Registry::getConfig()->getModulesDir() . $this->getVendorPrefix() . '/';
    }

    /**
     * Get a full path to the module directory.
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->getVendorPath() . $this->getModuleFolderName() . '/';
    }

    /**
     * Initialization for module generation method.
     *
     * @param string $sModuleName
     * @param array  $aGenerationOptions
     * @param string $sVendorPrefix
     *
     * @return array
     */
    public function init($sModuleName, array $aGenerationOptions = array(), $sVendorPrefix = '')
    {

        if (!empty($sVendorPrefix)) {
            $this->setVendorPrefix($sVendorPrefix);
        }
        // Set field for generated module name
        $this->_sModuleName = $sModuleName;

        // Set field for Generation Options from Generator submitted form
        $this->_aGenerationOptions = $aGenerationOptions;

        // Initialize helpers
        /** @var oxpsModuleGeneratorHelper $oHelper */
        $oHelper = Registry::get('oxpsModuleGeneratorHelper');
        $oHelper->init($this);

        /** @var oxpsModuleGeneratorRender $oRenderHelper */
        $oRenderHelper = Registry::get('oxpsModuleGeneratorRender');
        $oRenderHelper->init($this);

        // Set module data - initializes it with new module info
        $this->setNewModuleData();

        return array($oHelper, $oRenderHelper);
    }

    /**
     * Generate a module.
     * Creates blank pre-configure module skeleton in a vendor folder.
     *
     * @todo (nice2have): move to generation helper class.
     *
     * @param string $sModuleName          CamelCase module name to use as a base for new module naming.
     * @param array  $aGenerationOptions   Additional module generation options:
     *                                     `aExtendClasses`   - A list of classes to overload (extend).
     *                                     `aNewControllers`  - A list of controllers to create.
     *                                     `aNewModels`       - A list of models (item models) to create.
     *                                     `aNewLists`        - Repeat some or all of item models to create list models.
     *                                     `aNewWidgets`      - A list of widgets to create.
     *                                     `aNewBlocks`       - A list of blocks data to create.
     *                                     `aModuleSettings`  - Data for module settings to create.
     *                                     `lbThemesNone`     - Use same templates for all themes (no multi-theme)
     *                                     `aThemesList`      - A list on theme IDs for multi-theme templates
     *                                     `sInitialVersion`  - Initial version value for a new module.
     *                                     `blFetchUnitTests` - Whatever to clone PHPUnit tests from GIT or not.
     *                                     `blRenderTasks`    - Option to render "To Do" tasks comments.
     *                                     `blRenderSamples`  - Option to render sample data comments.
     *
     * @return bool
     */
    public function generateModule($sModuleName, array $aGenerationOptions = array())
    {
        list($oHelper, $oRenderHelper) = $this->init($sModuleName, $aGenerationOptions);
        $this->_moduleGeneration($aGenerationOptions, $oHelper, $oRenderHelper);

        return true;
    }

    /**
     * Set module data with or without existing parsed data depending on bool flag.
     * For example, flag is used to render full metadata.php template, but skip
     * the regeneration of already existing files to avoid overwriting.
     *
     * @param bool $blAddParsedOptions
     */
    public function setNewModuleData($blAddParsedOptions = false)
    {
        $aOptionsToSet = $this->_aGenerationOptions;

        if ($blAddParsedOptions) {
            foreach ($this->_aGenerationOptions as $index => $aGenerationOption) {
                if (array_key_exists($index, $this->_aParsedMetadataOptions)) {
                    $aOptionsToSet[$index] = array_merge(
                        $this->_aParsedMetadataOptions[$index],
                        $this->_aGenerationOptions[$index]
                    );
                }
            }
        }

        // Set module data - initializes it with new module info
        $this->_setNewModuleData($this->_sModuleName, $aOptionsToSet);
    }

    /**
     * Validate new module name: should be "UpperCamelCase".
     *
     * @param string $sModuleName
     *
     * @return boolean
     */
    public function validateModuleName($sModuleName)
    {
        return ($this->getValidator()->validateCamelCaseName($sModuleName));
    }

    /**
     * Get suffix of module file name by its path.
     * Expects argument to be something like "path/to/[vendor_prefix][module_name][desired_suffix].php"
     *
     * @param string $sPath
     *
     * @return string
     */
    public function getFileNameSuffix($sPath)
    {
        $sSuffix = '';
        $sFileName = basename($sPath);

        if (!empty($sFileName)) {
            $aFileName = explode('.', $sFileName);

            if (!empty($aFileName[0])) {
                $sSuffix = str_replace($this->getModuleId(), '', $aFileName[0]);
            }
        }

        return $sSuffix;
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
        /** @var oxpsModuleGeneratorRender $oRenderHelper */
        $oRenderHelper = Registry::get('oxpsModuleGeneratorRender');
        $oRenderHelper->init($this);

        return $oRenderHelper->renderFileComment($sSubPackage);
    }

    /**
     * An alias for validator class method.
     * Converts camel case string to human readable string with spaces between words.
     * Treats UPPERCASE abbreviations and numbers as separate words.
     *
     * @param string $sCamelCaseString
     *
     * @return string
     */
    public function camelCaseToHumanReadable($sCamelCaseString)
    {
        return $this->getValidator()->camelCaseToHumanReadable($sCamelCaseString);
    }


    /**
     * An alias for validator class method.
     * Get array value by key, optionally casting its type to desired one.
     *
     * @param array  $aDataArray
     * @param mixed  $mArrayKey
     * @param string $sType
     *
     * @return bool
     */
    public function getArrayValue(array $aDataArray, $mArrayKey, $sType = 'string')
    {
        return $this->getValidator()->getArrayValue($aDataArray, $mArrayKey, $sType);
    }

    /**
     * Check if entered module name already exists.
     *
     * @return bool
     */
    public function isEditMode()
    {
        if (null === $this->_blEditMode) {
            $this->_blEditMode = $this->moduleExists($this->_sModuleName);
        }

        return $this->_blEditMode;
    }

    public function backupFiles()
    {
        foreach ($this->_aFilesToBackupOnEdit as $item) {
            $this->_backupFileIfExists($item);
        }
    }

    /**
     * Check if module with provided name exists in the configured vendor directory.
     *
     * @param string $sModuleName
     *
     * @return boolean
     */
    public function moduleExists($sModuleName)
    {
        /** @var oxpsModuleGeneratorFileSystem $oFileSystemHelper */
        $oFileSystemHelper = Registry::get('oxpsModuleGeneratorFileSystem');

        return $oFileSystemHelper->isDir($this->getVendorPath() . $sModuleName);
    }

    /**
     * Return parsed module metadata info to Generation Options to fill Module Generator form.
     *
     * @param string $sModuleName
     *
     * @return array
     */
    public function readGenerationOptions($sModuleName)
    {
        $aGenerationOptions = [];

        $aMetadata = $this->_getMetadataInfo($sModuleName);
        if (!empty($aMetadata)) {
            /** @var oxpsModuleGeneratorMetadata $oMetadataParser */
            $oMetadataParser = Registry::get('oxpsModuleGeneratorMetadata');
            $aGenerationOptions = $oMetadataParser->parseMetadata($aMetadata, $this->getVendorPrefix(), $sModuleName);
        }

        return $aGenerationOptions;
    }

    /**
     * Compile additional module params and set all module data.
     *
     * @param string $sModuleName Module name in CamelCase style.
     * @param array  $aOptions    Additional module generation options.
     */
    protected function _setNewModuleData($sModuleName, array $aOptions = array())
    {
        /** @var oxpsModuleGeneratorSettings $oSettingsParser */
        $oSettingsParser = Registry::get('oxpsModuleGeneratorSettings');

        /** @var \OxidEsales\Eshop\Core\StrMb|\OxidEsales\Eshop\Core\StrRegular $oStr */
        $oStr = Str::getStr();
        $sVendorPrefix = $this->getVendorPrefix();
        $sVarPrefix = $oStr->strtoupper($sVendorPrefix);
        $sModuleFolder = $sModuleName;
        $sModuleClass = $sVendorPrefix . $sModuleName;
        $sModuleId = $oStr->strtolower($sModuleClass);
        $sReadableName = $this->camelCaseToHumanReadable($sModuleName);
        $aModuleData = array(
            // Default parent class params
            'id'                                      => $sModuleId,
            'title'                                   => $sVarPrefix . ' ' . $sReadableName,
            'description'                             => $sVarPrefix . ' ' . $sReadableName . ' Module',

            // Additional params for new module generation
            'oxpsmodulegenerator_name'                => $sModuleName,
            'oxpsmodulegenerator_folder'              => $sModuleFolder,
            'oxpsmodulegenerator_class'               => $sModuleClass,
            'oxpsmodulegenerator_extend_classes'      => $this->getArrayValue($aOptions, 'aExtendClasses', 'array'),
            'oxpsmodulegenerator_controllers'         => $this->getArrayValue($aOptions, 'aNewControllers', 'array'),
            'oxpsmodulegenerator_models'              => $this->getArrayValue($aOptions, 'aNewModels', 'array'),
            'oxpsmodulegenerator_list_models'         => $this->getArrayValue($aOptions, 'aNewLists', 'array'),
            'oxpsmodulegenerator_widgets'             => $this->getArrayValue($aOptions, 'aNewWidgets', 'array'),
            'oxpsmodulegenerator_blocks'              => $this->getArrayValue($aOptions, 'aNewBlocks', 'array'),
            'oxpsmodulegenerator_module_settings'     => $oSettingsParser->getModuleSettings(
                (array) $this->getArrayValue($aOptions, 'aModuleSettings', 'array')
            ),
            'oxpsmodulegenerator_module_theme_none'   => $this->getArrayValue($aOptions, 'lbThemesNone'),
            'oxpsmodulegenerator_module_theme_list'   => $this->getArrayValue($aOptions, 'aThemesList', 'array'),
            'oxpsmodulegenerator_module_init_version' => $this->getArrayValue($aOptions, 'sInitialVersion'),
            'oxpsmodulegenerator_render_tasks'        => $this->getArrayValue($aOptions, 'blRenderTasks', 'bool'),
            'oxpsmodulegenerator_render_samples'      => $this->getArrayValue($aOptions, 'blRenderSamples', 'bool'),
        );
        $this->setModuleData($aModuleData);
    }

    /**
     * Get Metadata of provided module name.
     *
     * @param $sModuleName
     *
     * @return array
     */
    protected function _getMetadataInfo($sModuleName)
    {
        $sMetadataPath = $this->_getFullFilePath($sModuleName, 'metadata.php');
        $aModule = [];
        if (file_exists($sMetadataPath)) {
            try {
                include $sMetadataPath;
            } catch (Exception $e) {
                // Optionally it could log to eShop standard exceptions log
            }
        }

        return (array) $aModule;
    }

    /**
     * Get full path to module metadata.php file
     *
     * @param string $sModuleName
     * @param string $sFilename
     *
     * @return string
     */
    protected function _getFullFilePath($sModuleName, $sFilename)
    {
        // TODO: VendorPath is null using this method from AjaxDataProvider;
        $sFullModulePath = $this->getVendorPath() . $sModuleName;

        $sMetadataPath = $sFullModulePath . "/" . $sFilename;

        return (string) $sMetadataPath;
    }

    /**
     * @param string $sFileName
     */
    // TODO: Method should go to class \oxpsModuleGeneratorFileSystem and use its helpers. Of course parameter
    // TODO: would become full path, not just file name (full path could be set in new "backupFiles" method).
    protected function _backupFileIfExists($sFileName)
    {
        if (file_exists($this->_getFullFilePath($this->_sModuleName, $sFileName))) {
            $sFileBackupName = $sFileName . '.' . time() . '.bak';
            rename(
                $this->_getFullFilePath($this->_sModuleName, $sFileName),
                $this->getVendorPath() . $this->_sModuleName . '/' . $sFileBackupName
            );
        }
    }

    /**
     * Module generation action.
     *
     * @param array                     $aGenerationOptions
     * @param oxpsModuleGeneratorHelper $oHelper
     * @param oxpsModuleGeneratorRender $oRenderHelper
     */
    protected function _moduleGeneration(array $aGenerationOptions, $oHelper, $oRenderHelper)
    {
        // Check if Edit Mode is activated
        if ($this->isEditMode()) {
            $this->_aParsedMetadataOptions = $this->readGenerationOptions($this->_sModuleName);
            $this->backupFiles();
        }

        // Get new module and module generation template full paths
        $sModuleGeneratorPath = Registry::get('oxpsModuleGeneratorModule')->getPath();
        $sModulePath = $this->getFullPath();

        // Copy the module from a folder structure with templates to a new module path
        $oHelper->createVendorMetadata($this->getVendorPath());
        $oHelper->getFileSystemHelper()->copyFolder(
            $sModuleGeneratorPath . 'Core/module.tpl/module/',
            $sModulePath,
            $this->isEditMode() ? (array) $this->_aIgnoreOnEdit : array()
        );

        // Create classes to overload (extend)
        $aClassesToExtend = (array) $oHelper->createClassesToExtend(
            $sModuleGeneratorPath . 'Core/module.tpl/oxpsExtendClass.php.tpl'
        );

        // Create new module classes and templates
        $aNewClasses = (array) $oHelper->createNewClassesAndTemplates($sModuleGeneratorPath);

        // Create blocks templates
        $oHelper->createBlock($sModulePath);

        // Process copied module files as Smarty templates to fill them with the real module data
        $oRenderHelper->renderModuleFiles($aClassesToExtend, $aNewClasses);

        // Clone PHP Unit tests libraries if the option is checked and configured
        if ($this->getArrayValue($aGenerationOptions, 'blFetchUnitTests')) {
            $oHelper->fillTestsFolder($oRenderHelper, $sModuleGeneratorPath, $aClassesToExtend, $aNewClasses);
        }
    }
}
