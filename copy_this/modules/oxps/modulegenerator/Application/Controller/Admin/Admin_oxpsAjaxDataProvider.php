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

use \OxidEsales\Eshop\Application\Controller\Admin\AdminController;

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
     * @var oxpsModuleGeneratorOxModule
     */
    protected $_oModuleGeneratorOxModule;

    /**
     * @var oxpsModuleGeneratorModule
     */
    protected $_oModuleGeneratorModule;

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
    public function getModuleGeneratorOxModule()
    {
        if (null === $this->_oModuleGeneratorOxModule) {
            $this->_oModuleGeneratorOxModule = oxNew('oxpsModuleGeneratorOxModule');
        }

        return $this->_oModuleGeneratorOxModule;
    }

    /**
     * @return oxpsModuleGeneratorModule
     */
    public function getModuleGeneratorModule()
    {
        if (null === $this->_oModuleGeneratorModule) {
            $this->_oModuleGeneratorModule = oxNew('oxpsModuleGeneratorModule');
        }

        return $this->_oModuleGeneratorModule;
    }

    /**
     * Get Module Settings if exists. Returns metadata.php module file information rendered like form input.
     */
    public function validateModuleName()
    {
        $sModuleName = $this->getConfig()->getRequestParameter('moduleName');

        // Set vendor prefix from settings as it can only be set through oxpsModuleGenerator Controller
        $this->setVendorPrefix(
            $this->getModuleGeneratorModule()->getSetting('VendorPrefix')
        );

        $this->getModuleGeneratorOxModule()->init(
            $sModuleName,
            [],
            $this->getVendorPrefix()
        );

        if ($this->_isModuleExists($sModuleName)) {
            $aExistingModuleSettings = $this->getModuleGeneratorOxModule()->readGenerationOptions($sModuleName);

            header('Content-Type: application/json');
            echo json_encode($aExistingModuleSettings);
        }
        exit;
    }

    /**
     * Check module availability
     *
     * @param string $sModuleName
     *
     * @return bool
     */
    protected function _isModuleExists($sModuleName)
    {
        return (
            $this->getModuleGeneratorOxModule()->moduleExists($sModuleName)
            && !empty($sModuleName)
        );
    }
}
