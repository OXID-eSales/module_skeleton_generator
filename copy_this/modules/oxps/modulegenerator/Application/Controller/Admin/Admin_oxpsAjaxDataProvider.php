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

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;

/**
 * Class Admin_oxpsAjaxDataProvider.
 * Module Generator Ajax Data Provider for data validation in form and data provision for Edit Mode.
 */
// TODO: Refactor field names to be short
class Admin_oxpsAjaxDataProvider extends AdminController
{

    /**
     * @var string
     */
    protected $_sVendorPrefix;

    /**
     * @var oxpsModuleGeneratorOxModule
     */
    protected $_oOxModule;

    /**
     * @var oxpsModuleGeneratorModule
     */
    protected $_oModule;

    /**
     * @var oxpsModuleGeneratorValidator
     */
    protected $_oValidator;

    /**
     * @var string
     */
    protected $_sModuleName;

    /**
     * Fill Module object with required content before other interactions
     */
    public function init()
    {
        $this->_init_parent();

        $this->_sModuleName = $this->getConfig()->getRequestParameter('moduleName');

        // Set vendor prefix from settings as it can only be set through oxpsModuleGenerator Controller
        $this->setVendorPrefix(
            $this->getModule()->getSetting('VendorPrefix')
        );

        $this->getOxModule()->init(
            $this->_sModuleName,
            [],
            $this->getVendorPrefix()
        );
    }

    /**
     * @return string
     */
    public function getVendorPrefix()
    {
        return $this->_sVendorPrefix;
    }

    /**
     * @param string $sVendorPrefix
     */
    public function setVendorPrefix($sVendorPrefix)
    {
        $this->_sVendorPrefix = $sVendorPrefix;
    }

    /**
     * @return oxpsModuleGeneratorOxModule
     */
    public function getOxModule()
    {
        if (null === $this->_oOxModule) {
            $this->_oOxModule = oxNew('oxpsModuleGeneratorOxModule');
        }

        return $this->_oOxModule;
    }

    /**
     * @return oxpsModuleGeneratorModule
     */
    public function getModule()
    {
        if (null === $this->_oModule) {
            $this->_oModule = oxNew('oxpsModuleGeneratorModule');
        }

        return $this->_oModule;
    }

    /**
     * @return oxpsModuleGeneratorValidator
     */
    public function getValidator()
    {
        if (null === $this->_oValidator) {
            $this->_oValidator = oxNew('oxpsModuleGeneratorValidator');
        }

        return $this->_oValidator;
    }

    /**
     * Get Module Settings if exists. Returns metadata.php module file information rendered like form input.
     */
    public function validateModuleName()
    {
        if ($this->_moduleExists()) {
            $aExistingModuleSettings = $this->getOxModule()->readGenerationOptions($this->_sModuleName);
            $this->_encodeToJson($aExistingModuleSettings);
        }
    }

    public function validateExtendClassNames()
    {
        $sExtendClasses = $this->getConfig()->getRequestParameter('extendClasses');

        $aValidLinkedClasses = $this->getValidator()->validateAndLinkClasses($sExtendClasses);
        $this->_encodeToJson($aValidLinkedClasses);
    }

    /**
     * Check module availability
     *
     * @return bool
     */
    protected function _moduleExists()
    {
        return (
            $this->getValidator()->moduleExists($this->_sModuleName)
            && !empty($this->_sModuleName)
        );
    }

    /**
     * @param array $aExistingModuleSettings
     */
    protected function _encodeToJson(array $aExistingModuleSettings)
    {
        header('Content-Type: application/json');
        OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit(
            json_encode($aExistingModuleSettings, JSON_FORCE_OBJECT)
        );
    }

    protected function _init_parent()
    {
        parent::init();
    }
}
