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
namespace Oxps\ModuleGenerator\Application\Controller\Admin;

use \OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use \OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use \OxidEsales\Eshop\Core\Str;
use \Oxps\ModuleGenerator\Core\OxModule;
use Oxps\ModuleGenerator\Core\Validator;
use \Oxps\ModuleGenerator\Core\Module;


    /**
 * Class Admin_oxpsModuleGenerator.
 * Module Generator GUI controller.
 */
class Admin_oxpsModuleGenerator extends AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'Admin_oxpsModuleGenerator.tpl';

    /**
     * Module instance used as information container for new module generation.
     *
     * @var OxModule
     */
    protected $_oModule;

    /**
     * Module Validator instance used for validating data required for new module generation.
     *
     * @var Validator
     */
    protected $_oValidator;

    /**
     * Overridden parent method.
     * Initializes internal variables for module instance.
     */
    public function init()
    {
        /** @var OxModule $oModule */
        $oModule = oxNew(OxModule::class);
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
     * @throws \ReflectionException
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
     * Get Validator instance or set it if not available.
     *
     * @return Validator
     */
    public function getValidator()
    {
        if (null === $this->_oValidator) {
            /** @var Validator $oValidator */
            $this->_oValidator = Registry::get(Validator::class);
        }

        return $this->_oValidator;
    }

    /**
     * Get module instance.
     *
     * @return OxModule
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
        return (string) Registry::get(Module::class)->getSetting('VendorPrefix');
    }

    /**
     * Get module author/vendor data from settings: name, link, mail, copyright and file comment.
     *
     * @return array
     */
    public function getAuthorData()
    {
        /** @var Module $oModuleGeneratorModule */
        $oModuleGeneratorModule = Registry::get(Module::class);

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


        if (!$this->getValidator()->validateVendorPrefix($this->getVendorPrefix())) {
            // Set an error if configured module vendor prefix is not valid
            $this->_setMessage('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_VENDOR');
        } elseif (!$this->getModule()->validateModuleName($sModuleName)) {
            // Set an error if module name is not valid
            $this->_setMessage('OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_NAME');
        } else {
            $this->getModule()->generateModule($sModuleName, $aGenerationOptions);

            // Set success massage
            $this->_setMessage('OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS', false);
        }
    }

    /**
     * Generate full URL for AJAX response;
     *
     * @param string $sResponseMethodName
     *
     * @return string
     */
    public function generateAjaxResponseUrl($sResponseMethodName)
    {
        return (string)
        htmlspecialchars_decode(
            Registry::getUtilsUrl()->processUrl($this->getConfig()->getCurrentShopUrl() . 'index.php', true) .
            '&amp;cl=admin_oxpsajaxdataprovider&amp;fnc=' .
            strtolower($sResponseMethodName)
        );
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
     * @throws \ReflectionException
     */
    protected function _getGenerationOptions()
    {
        /** @var Request $oRequest */
        $oRequest = Registry::get(Request::class);

        $aGenerationOptions = array(
            'aExtendClasses'   => $this->getValidator()->validateAndLinkClasses(
                $this->_getTextParam('modulegenerator_extend_classes')
            ),
            'aNewControllers'  => $this->getValidator()->parseMultiLineInput(
                $this->_getTextParam('modulegenerator_controllers')
            ),
            'aNewModels'       => $this->getValidator()->parseMultiLineInput(
                $this->_getTextParam('modulegenerator_models')
            ),
            'aNewLists'        => $this->getValidator()->parseMultiLineInput(
                $this->_getTextParam('modulegenerator_lists')
            ),
            'aNewWidgets'      => $this->getValidator()->parseMultiLineInput(
                $this->_getTextParam('modulegenerator_widgets')
            ),
            'aNewBlocks'       => $this->getValidator()->parseBlocksData(
                $this->_getTextParam('modulegenerator_blocks'),
                $this->getVendorPrefix(),
                $this->_getTextParam('modulegenerator_module_name')
            ),
            'aModuleSettings'  => (array) $oRequest->getRequestParameter('modulegenerator_settings'),
            'blThemesNone'     => (bool) $oRequest->getRequestParameter('modulegenerator_theme_none'),
            'aThemesList'      => $this->getValidator()->parseMultiLineInput(
                $this->_getTextParam('modulegenerator_theme_list'),
                'not_empty'
            ),
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
     * @throws \ReflectionException
     */
    protected function _getFormValues()
    {
        /** @var Validator $oValidator */
        $oValidator = Registry::get(Validator::class);

        $blFormSubmitted = !empty($_POST);
        $aOptions = (array) $this->_getGenerationOptions();

        $sThemeList = $this->_toString($oValidator->getArrayValue($aOptions, 'aThemesList', 'array'));
        $sThemeList .= (!$blFormSubmitted or empty($sThemeList)) ? $this->_toString(array('flow', 'azure')) : '';

        return array(
            'name'        => $this->_getTextParam('modulegenerator_module_name'),
            'extend'      => $this->_toString(array_keys($oValidator->getArrayValue($aOptions, 'aExtendClasses', 'array'))),
            'controllers' => $this->_toString($oValidator->getArrayValue($aOptions, 'aNewControllers', 'array')),
            'models'      => $this->_toString($oValidator->getArrayValue($aOptions, 'aNewModels', 'array')),
            'lists'       => $this->_getListModelsFieldValue($oValidator->getArrayValue($aOptions, 'aNewLists', 'array')),
            'widgets'     => $this->_toString($oValidator->getArrayValue($aOptions, 'aNewWidgets', 'array')),
            'blocks'      => $this->_getBlocksFieldValue($oValidator->getArrayValue($aOptions, 'aNewBlocks', 'array')),
            'settings'    => $oValidator->getArrayValue($aOptions, 'aModuleSettings', 'array'),
            'theme_none'  => $blFormSubmitted ? $oValidator->getArrayValue($aOptions, 'blThemesNone', 'boolean') : true,
            'theme_list'  => $sThemeList,
            'version'     => $this->_getFormVersionFieldValue($oValidator->getArrayValue($aOptions, 'sInitialVersion')),
            'tests'       => $blFormSubmitted ? $oValidator->getArrayValue($aOptions, 'blFetchUnitTests', 'boolean') : true,
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
        /** @var Validator $oValidator */
        $oValidator = Registry::get(Validator::class);

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
            $sModuleVersion = '2.0.0';
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
        /** @var Request $oRequest */
        $oRequest = Registry::get(Request::class);

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
        /** @var Validator $oValidator */
        $oValidator = Registry::get(Validator::class);

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
     * @codingStandardsIgnoreStart
     * @return null
     */
    protected function _Admin_oxpsModuleGenerator_init_parent()
    {
        return parent::init();
    }
    // @codingStandardsIgnoreEnd
    /**
     * Parent `render` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreStart
     * @return null
     */
    protected function _Admin_oxpsModuleGenerator_render_parent()
    {
        return parent::render();
    }
    // @codingStandardsIgnoreEnd
}
