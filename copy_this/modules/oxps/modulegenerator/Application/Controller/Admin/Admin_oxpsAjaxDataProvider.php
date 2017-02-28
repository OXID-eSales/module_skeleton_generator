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
// TODO: pagal suvestą modulio name nustatyti ar tai edit mode; užkrauti ir perduoti parsedGenerationOptions; ...
class Admin_oxpsAjaxDataProvider extends AdminController
{

    /**
     * @var oxpsModuleGeneratorOxModule
     */
    protected $_oModuleGeneratorOxModule;

    /**
     * @var oxpsModuleGeneratorModule
     */
    protected $_oModuleGeneratorModule;

    /**
     * Get Module Settings if exists
     */
    public function getExistingModuleSettings()
    {
        /** @var oxpsModuleGeneratorOxModule $_oModuleGeneratorOxModule */
        $this->_oModuleGeneratorOxModule = oxNew('oxpsModuleGeneratorOxModule');

        /** @var oxpsModuleGeneratorModule $_oModuleGeneratorModule */
        $this->_oModuleGeneratorModule = oxNew('oxpsModuleGeneratorModule');

        $sModuleName = $this->getConfig()->getRequestParameter('moduleName');
        if ($this->_isModuleExists($sModuleName)) {
            // TODO: Cannot read Generation Options as vendor prefix is not defined in full path during metadata.php
            // TODO: file include process and as a result returns empty $aExistingModuleSettings array.
            $aExistingModuleSettings = $this->_oModuleGeneratorOxModule->readGenerationOptions($sModuleName);

            header('Content-Type: application/json');
            echo json_encode($aExistingModuleSettings);
        } else {
            // Shows that user is in new module generation mode
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
        $sVendorPrefix = $this->_oModuleGeneratorModule->getSetting('VendorPrefix');

        return ($this->_oModuleGeneratorOxModule->moduleExists($sVendorPrefix . "/" . $sModuleName) && $sModuleName)
            ? true
            : false;
    }


}
