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

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\Eshop\Core\Registry;
use Oxps\ModuleGenerator\Core\Module;
use Oxps\ModuleGenerator\Core\OxModule;
use Oxps\ModuleGenerator\Core\Validator;

/**
 * Class Admin_oxpsAjaxDataProvider.
 * Module Generator Ajax Data Provider for data validation in form and data provision for Edit Mode.
 */
class Admin_oxpsAjaxDataProvider extends AdminController
{
    /**
     * @var string
     */
    protected $_sVendorPrefix;

    /**
     * @var OxModule
     */
    protected $_oOxModule;

    /**
     * @var Module
     */
    protected $_oModule;

    /**
     * @var Validator
     */
    protected $_oValidator;

    /**
     *  Overridden init method
     */
    public function init()
    {
        // Parent call
        $this->_Admin_oxpsAjaxDataProvider_init_parent();
    }

    /**
     * Get oxModule instance to access generated module data.
     *
     * @return OxModule
     */
    public function getOxModule()
    {
        if (null === $this->_oOxModule) {
            $this->_oOxModule = oxNew(OxModule::class);
        }

        return $this->_oOxModule;
    }

    /**
     * Get Generator module instance to access modules settings.
     *
     * @return Module
     */
    public function getModule()
    {
        if (null === $this->_oModule) {
            $this->_oModule = oxNew(Module::class);
        }

        return $this->_oModule;
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        if (null === $this->_oValidator) {
            $this->_oValidator = oxNew(Validator::class);
        }

        return $this->_oValidator;
    }

    /**
     * Get Module Settings if exists. Returns metadata.php module file information rendered like form input.
     */
    public function getModuleData()
    {
        $sModuleName = $this->_getParameter('moduleName');

        if ($this->_validateModuleName($sModuleName)) {
            $aExistingModuleSettings = $this->getOxModule()->readGenerationOptions($sModuleName);
            $this->_returnJsonResponse($aExistingModuleSettings);
        } else {
            $this->_returnJsonResponse([]);
        }
    }
    
    /**
     * Validate extended classes names and return JSON with module's metadata.
     *
     * @throws \ReflectionException
     */
    public function validateExtendClassNames()
    {
        $sExtendClasses = $this->_getParameter('extendClasses');

        $aValidLinkedClasses = $this->getValidator()->validateAndLinkClasses($sExtendClasses);
        $this->_returnJsonResponse($aValidLinkedClasses);
    }

    /**
     * Check module availability
     *
     * @param string $sModuleName
     *
     * @return bool
     */
    protected function _validateModuleName($sModuleName)
    {
        $this->getOxModule()->init($sModuleName, [], $this->getModule()->getSetting('VendorPrefix'));

        return (
            !empty($sModuleName)
            && $this->getValidator()->moduleExists($sModuleName)
        );
    }

    /**
     * @param array $aExistingModuleSettings
     *
     * @codeCoverageIgnore
     */
    protected function _returnJsonResponse(array $aExistingModuleSettings)
    {
        header('Content-Type: application/json');
        Registry::getUtils()->showMessageAndExit(
            json_encode($aExistingModuleSettings, JSON_FORCE_OBJECT)
        );
    }

    /**
     * @param string $sName
     *
     * @return string
     */
    protected function _getParameter($sName)
    {
        return (string) $this->getConfig()->getRequestParameter($sName);
    }

    /**
     * Parent `init` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreStart
     */
    protected function _Admin_oxpsAjaxDataProvider_init_parent()
    {
        parent::init();
    }
    // @codingStandardsIgnoreEnd
}
