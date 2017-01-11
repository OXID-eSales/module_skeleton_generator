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
 * @copyright (C) OXID eSales AG 2003-2017
 */

use \OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\Str;

/**
 * Class Admin_oxpsModuleGenerator.
 * Module Generator GUI controller.
 *
 * @todo (nice2have) Class got too long -> move some methods to validation helper or some other class.
 */
class Admin_oxpsModuleGenerator extends AdminController
{

    /**
     * Folder name identifier in class path which determines backward compatibility - old class name usage.
     */
    const OXPS_BACKWARD_COMPATIBILITY_FOLDER = 'BackwardCompatibility';


    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'admin_oxpsmodulegenerator.tpl';

    /**
     * Module instance used as information container for new module generation.
     *
     * @var oxpsModuleGeneratorOxModule
     */
    protected $_oModule;


    /**
     * Overridden parent method.
     * Initializes internal variables for module instance.
     */
    public function init()
    {
        /** @var oxpsModuleGeneratorOxModule $oModule */
        $oModule = oxNew('oxpsModuleGeneratorOxModule');
        $oModule->setVendorPrefix($this->getVendorPrefix());
        $oModule->setAuthorData($this->getAuthorData());

        $this->_oModule = $oModule;

        // Parent call
        $this->_Admin_oxpsModuleGenerator_init_parent();
    }

    /**
     * Overridden parent method.
     * Adds module instance to view data.
     *
     * @return string
     */
    public function render()
    {
        // Assign additional view data
        $this->_addViewData(array('oModule' => $this->getModule()));

        // Set an error if vendor/author data is not configured in the module settings
        if (!$this->_isVendorConfigured()) {
            $this->_setMessage('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_NO_VENDOR');
        }

        // Add clean module generation options and form values to view data
        $this->_addViewData(array('oValues' => (object) $this->_getFormValues()));

        // Parent render call
        return $this->_Admin_oxpsModuleGenerator_render_parent();
    }


    /**
     * Get module instance.
     *
     * @return oxpsModuleGeneratorOxModule
     */
    public function getModule()
    {
        return $this->_oModule;
    }

    /**
     * Get vendor prefix stored in settings.
     *
     * @return string
     */
    public function getVendorPrefix()
    {
        return (string) Registry::get('oxpsModuleGeneratorModule')->getSetting('VendorPrefix');
    }

    /**
     * Get module author/vendor data from settings: name, link, mail, copyright and file comment.
     *
     * @return array
     */
    public function getAuthorData()
    {
        /** @var oxpsModuleGeneratorModule $oModuleGeneratorModule */
        $oModuleGeneratorModule = Registry::get('oxpsModuleGeneratorModule');

        return array(
            'name' => (string) $oModuleGeneratorModule->getSetting('ModuleAuthor'),
            'link' => (string) $oModuleGeneratorModule->getSetting('AuthorLink'),
            'mail' => (string) $oModuleGeneratorModule->getSetting('AuthorMail'),
            'copy' => (string) $oModuleGeneratorModule->getSetting('Copyright'),
            'info' => ' ' . trim(implode(PHP_EOL . ' ', (array) $oModuleGeneratorModule->getSetting('Comment'))),
        );
    }

    /**
     * Module generation action.
     * Collects, cleans and validates form data, calls generation, sets messages.
     */
    public function generateModule()
    {
        // Get module name from request
        $sModuleName = $this->_getTextParam('modulegenerator_module_name');

        // Get module generation options
        $aGenerationOptions = $this->_getGenerationOptions();

        /** @var oxpsModuleGeneratorValidator $oValidator */
        $oValidator = Registry::get('oxpsModuleGeneratorValidator');

        if (!$oValidator->validateVendorPrefix($this->getVendorPrefix())) {

            // Set an error if configured module vendor prefix is not valid
            $this->_setMessage('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_VENDOR');
        } elseif (!$this->getModule()->validateModuleName($sModuleName)) {

            // Set an error if module name is not valid
            $this->_setMessage('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_NAME');
        } elseif ($this->getModule()->generateModule($sModuleName, $aGenerationOptions)
        ) {

            // Set success massage
            $this->_setMessage('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', false);
        }
    }


    /**
     * Check if vendor/author data is configured in the module settings.
     *
     * @return bool
     */
    protected function _isVendorConfigured()
    {
        $sVendorPrefix = $this->getVendorPrefix();

        return !empty($sVendorPrefix);
    }

    /**
     * Add additional array to the view data.
     *
     * @param array $aViewData Assoc array of parameters to add to view data.
     */
    protected function _addViewData($aViewData)
    {
        if (!empty($aViewData) and is_array($aViewData)) {
            $this->setViewData(array_merge($this->getViewData(), $aViewData));
        }
    }

    /**
     * Collect request data, parse it and compile to module generation options array.
     *
     * @return array
     */
    protected function _getGenerationOptions()
    {
        /** @var \OxidEsales\Eshop\Core\Request $oRequest */
        $oRequest = Registry::get(\OxidEsales\Eshop\Core\Request::class);

        $aGenerationOptions = array(
            'aExtendClasses'   => $this->_validateAndLinkClasses($this->_getTextParam('modulegenerator_extend_classes')),
            'aNewControllers'  => $this->_parseMultiLineInput($this->_getTextParam('modulegenerator_controllers')),
            'aNewModels'       => $this->_parseMultiLineInput($this->_getTextParam('modulegenerator_models')),
            'aNewLists'        => $this->_parseMultiLineInput($this->_getTextParam('modulegenerator_lists')),
            'aNewWidgets'      => $this->_parseMultiLineInput($this->_getTextParam('modulegenerator_widgets')),
            'aNewBlocks'       => $this->_parseBlocksData(
                $this->_getTextParam('modulegenerator_blocks'),
                $this->getVendorPrefix(),
                $this->_getTextParam('modulegenerator_module_name')
            ),
            'aModuleSettings'  => (array) $oRequest->getRequestParameter('modulegenerator_settings'),
            'sInitialVersion'  => (string) $oRequest->getRequestParameter('modulegenerator_init_version'),
            'blFetchUnitTests' => (bool) $oRequest->getRequestParameter('modulegenerator_fetch_unit_tests'),
            'blRenderTasks'    => (bool) $oRequest->getRequestParameter('modulegenerator_render_tasks'),
            'blRenderSamples'  => (bool) $oRequest->getRequestParameter('modulegenerator_render_samples'),
        );

        return $this->_filterListModels($aGenerationOptions);
    }

    /**
     * Get initial form values for a case when the form was submitted.
     *
     * @return array
     */
    protected function _getFormValues()
    {
        /** @var oxpsModuleGeneratorValidator $oValidator */
        $oValidator = Registry::get('oxpsModuleGeneratorValidator');

        $aOptions = (array) $this->_getGenerationOptions();

        return array(
            'name'        => $this->_getTextParam('modulegenerator_module_name'),
            'extend'      => $this->_toString(array_keys($oValidator->getArrayValue($aOptions, 'aExtendClasses', 'array'))),
            'controllers' => $this->_toString($oValidator->getArrayValue($aOptions, 'aNewControllers', 'array')),
            'models'      => $this->_toString($oValidator->getArrayValue($aOptions, 'aNewModels', 'array')),
            'lists'       => $this->_getListModelsFieldValue($oValidator->getArrayValue($aOptions, 'aNewLists', 'array')),
            'widgets'     => $this->_toString($oValidator->getArrayValue($aOptions, 'aNewWidgets', 'array')),
            'blocks'      => $this->_getBlocksFieldValue($oValidator->getArrayValue($aOptions, 'aNewBlocks', 'array')),
            'settings'    => $oValidator->getArrayValue($aOptions, 'aModuleSettings', 'array'),
            'version'     => $this->_getFormVersionFieldValue($oValidator->getArrayValue($aOptions, 'sInitialVersion')),
            'tests'       => $oValidator->getArrayValue($aOptions, 'blFetchUnitTests', 'boolean'),
            'tasks'       => $oValidator->getArrayValue($aOptions, 'blRenderTasks', 'boolean'),
            'samples'     => $oValidator->getArrayValue($aOptions, 'blRenderSamples', 'boolean'),
        );
    }

    /**
     * Convert array to a multi-line string.
     *
     * @param array $aData
     *
     * @return string
     */
    protected function _toString(array $aData)
    {
        return trim((string) implode(PHP_EOL, $aData));
    }

    /**
     * Get initial value for list models field.
     * Removes "List" suffixes from names.
     *
     * @param array $aRequestListModels
     *
     * @return string
     */
    protected function _getListModelsFieldValue(array $aRequestListModels)
    {
        /** @var \OxidEsales\Eshop\Core\StrMb|\OxidEsales\Eshop\Core\StrRegular $oStr */
        $oStr = Str::getStr();

        $aLists = array();

        foreach ($aRequestListModels as $sListClassName) {
            $aLists[] = $oStr->substr($sListClassName, 0, ($oStr->strlen($sListClassName) - 4));
        }

        return $this->_toString($aLists);
    }

    /**
     * Get initial value for module blocks field.
     *
     * @param array $aRequestBlocks
     *
     * @return string
     */
    protected function _getBlocksFieldValue(array $aRequestBlocks)
    {
        /** @var oxpsModuleGeneratorValidator $oValidator */
        $oValidator = Registry::get('oxpsModuleGeneratorValidator');

        $aBlocks = array();

        foreach ($aRequestBlocks as $oBlock) {
            $aBlock = (array) $oBlock;
            $aBlocks[] = $oValidator->getArrayValue($aBlock, 'block') . '@' .
                         $oValidator->getArrayValue($aBlock, 'template');
        }

        return $this->_toString($aBlocks);
    }

    /**
     * Get initial value for module version field.
     *
     * @param string $sRequestVersion
     *
     * @return string
     */
    protected function _getFormVersionFieldValue($sRequestVersion)
    {
        $sModuleVersion = trim((string) $sRequestVersion);

        if (empty($sModuleVersion)) {
            $sModuleVersion = '1.0.0';
        }

        return $sModuleVersion;
    }

    /**
     * Get request parameter as trimmed string.
     *
     * @param string $sKey
     *
     * @return string
     */
    protected function _getTextParam($sKey)
    {
        /** @var \OxidEsales\Eshop\Core\Request $oRequest */
        $oRequest = Registry::get(\OxidEsales\Eshop\Core\Request::class);

        return trim((string) $oRequest->getRequestParameter($sKey));
    }

    /**
     * Set a message to view data.
     * Uses key `sMessage` for the message.
     * Sets key `blError` True if it is an error, False for a success/info message.
     *
     * @param string $sMessage
     * @param bool   $blError
     */
    protected function _setMessage($sMessage, $blError = true)
    {
        $this->_addViewData(array('sMessage' => (string) $sMessage, 'blError' => !empty($blError)));
    }

    /**
     * Check list of classes and link it with its relative path for each valid class.
     *
     * @param string $sClasses List of classes names separated with a new line.
     *
     * @return array With relative application path as value and clean class name as key for each valid class.
     */
    protected function _validateAndLinkClasses($sClasses)
    {
        /** @var oxpsModuleGeneratorFileSystem $oFileSystemHelper */
        $oFileSystemHelper = Registry::get('oxpsModuleGeneratorFileSystem');
        $aClasses = $this->_parseMultiLineInput($sClasses, 'not_empty');
        $aValidLinkedClasses = array();
        $oConfig = Registry::getConfig();
        $sBasePath = $oConfig->getConfigParam('sShopDir');

        foreach ($aClasses as $sClassName) {
            if (!class_exists($sClassName)) {
                continue;
            }

            $sClassPath = (string) $this->_getClassPath($sClassName);

            if ($oFileSystemHelper->isFile($sClassPath)) {
                $sClassPath = str_replace($sBasePath, '', dirname($sClassPath)) . DIRECTORY_SEPARATOR;
                $aValidLinkedClasses[$sClassName] = $sClassPath;
            }
        }

        return $aValidLinkedClasses;
    }

    /**
     * Build reflection object to get path to a class.
     * In case of old class name (backwards compatibility), use new class name of parent reflection.
     *
     * @param string $sClassName
     *
     * @return string
     */
    protected function _getClassPath($sClassName)
    {
        /** @var \OxidEsales\Eshop\Core\StrMb|\OxidEsales\Eshop\Core\StrRegular $oStr */
        $oStr = Str::getStr();

        $oReflection = new ReflectionClass(new $sClassName());
        $sClassPath = (string) $oReflection->getFilename();

        if (false !== $oStr->strpos($sClassPath, self::OXPS_BACKWARD_COMPATIBILITY_FOLDER)) {
            $oReflection = $oReflection->getParentClass();
            $sClassPath = (string) $oReflection->getFilename();
        }

        return $sClassPath;
    }

    /**
     * Parse new blocks multi-line data to valid metadata blocks definition.
     * Each valid element will have a template, block and file keys.
     *
     * @todo (nice2have): When using block name as blocks list key, block becomes unique in a module.
     *                    Maybe it should not be unique, i.e. module could extend same block many times, or not?
     *
     * @param string $sBlocks
     * @param string $sVendorPrefix
     * @param string $sModuleName
     *
     * @return array
     */
    protected function _parseBlocksData($sBlocks, $sVendorPrefix, $sModuleName)
    {
        $sModuleId = sprintf('%s%s', $sVendorPrefix, $sModuleName);
        $aBlocks = $this->_parseMultiLineInput($sBlocks, 'not_empty');
        $aValidBlocks = array();

        foreach ($aBlocks as $sBlockDefinition) {
            $aBlock = $this->_parseBlockDefinition($sBlockDefinition, $sModuleId);

            if (isset($aBlock['template'], $aBlock['block'], $aBlock['file'])) {
                $aValidBlocks[sprintf('_%s', $aBlock['block'])] = (object) $aBlock;
            }
        }

        return $aValidBlocks;
    }

    /**
     * Parse block definition from string to metadata block entry array.
     *
     * @todo (nice2have): Validate block name [a-z{1}a-z_{1,}] and check if template exists in active theme?
     *
     * @param string $sBlockDefinition String in format "[block_name]@[path/to/existing/template.tpl]"
     * @param string $sModuleId
     *
     * @return array
     */
    protected function _parseBlockDefinition($sBlockDefinition, $sModuleId)
    {
        /** @var oxpsModuleGeneratorValidator $oValidator */
        $oValidator = Registry::get('oxpsModuleGeneratorValidator');

        $sBlockDefinition = trim((string) $sBlockDefinition);
        $aBlockDefinition = !empty($sBlockDefinition) ? explode("@", $sBlockDefinition) : array();

        $sBlockName = $oValidator->getArrayValue($aBlockDefinition, 0);
        $sTemplatePath = $oValidator->getArrayValue($aBlockDefinition, 1);

        if (empty($sBlockName) or empty($sTemplatePath)) {
            return array();
        }

        return array(
            'template' => $sTemplatePath,
            'block'    => $sBlockName,
            'file'     => sprintf('views/blocks/%s_%s.tpl', $sModuleId, $sBlockName),
        );
    }

    /**
     * Parse multi-line string input as array and validate each line.
     * Line validation is one of following:
     *  `not_empty`  - there must be something in the line
     *  `camel_case` - a value must be in "UpperCamelCase" format
     *
     * @todo (nice2have): Check class names among all types to be unique
     *
     * @param string $sInput
     * @param string $sLineValidation A validation rule name.
     *
     * @return array
     */
    protected function _parseMultiLineInput($sInput, $sLineValidation = 'camel_case')
    {
        $aInput = (array) explode(PHP_EOL, (string) $sInput);
        $aValidInput = array();

        foreach ($aInput as $sLine) {
            $sLine = trim((string) $sLine);

            if ($this->_isLineInputValid($sLine, $sLineValidation) and !in_array($sLine, $aValidInput)) {
                $aValidInput[] = $sLine;
            }
        }

        return $aValidInput;
    }

    /**
     * Check if a value passes specified validation rule.
     *
     * @param string $sValue
     * @param string $sValidationRule
     *
     * @return bool
     */
    protected function _isLineInputValid($sValue, $sValidationRule)
    {
        $blIsValid = false;

        switch ($sValidationRule) {
            case 'not_empty';
                $blIsValid = !empty($sValue);
                break;

            case 'camel_case':
                /** @var oxpsModuleGeneratorValidator $oValidator */
                $oValidator = Registry::get('oxpsModuleGeneratorValidator');
                $blIsValid = (bool) $oValidator->validateCamelCaseName($sValue);
                break;
        }

        return $blIsValid;
    }

    /**
     * Filter generation options to check if list models were set and if those list models match item models.
     * List model is added only if item model with same name was also set.
     * Adds "List" prefix for each valid list model.
     *
     * @param array $aData
     *
     * @return array
     */
    protected function _filterListModels(array $aData)
    {
        /** @var oxpsModuleGeneratorValidator $oValidator */
        $oValidator = Registry::get('oxpsModuleGeneratorValidator');

        $aLists = (array) $oValidator->getArrayValue($aData, 'aNewLists', 'array');
        $aModels = (array) $oValidator->getArrayValue($aData, 'aNewModels', 'array');

        foreach ($aLists as $mKey => $sNewListClass) {
            if (!in_array($sNewListClass, $aModels)) {
                unset($aLists[$mKey]);
            } else {
                $aLists[$mKey] = sprintf('%sList', $sNewListClass);
            }
        }

        $aData['aNewLists'] = $aLists;

        return $aData;
    }


    /**
     * Parent `init` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     *
     * @return null
     */
    protected function _Admin_oxpsModuleGenerator_init_parent()
    {
        return parent::init();
    }

    /**
     * Parent `render` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     *
     * @return null
     */
    protected function _Admin_oxpsModuleGenerator_render_parent()
    {
        return parent::render();
    }
}
