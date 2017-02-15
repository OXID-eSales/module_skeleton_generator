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

use OxidEsales\Eshop\Core\Base;

/**
 * Class oxpsModuleGeneratorMetaData is used for Module Generator's
 * Edit Mode as a parser getting info from existing metadata.php file and
 * converting it to Generation Options data structure to show module components.
 */
class oxpsModuleGeneratorMetadata extends Base
{

    /**
     * Components' path patterns to extract file types from metadata 'files' array
     */
    const CONTROLLER_PATTERN = '/Application/Controller/';
    const MODEL_PATTERN = '/Application/Model/';
    const LIST_PATTERN = 'List.php';
    const WIDGET_PATTERN = '/Application/Component/Widget/';

    /** @var array */
    protected $_aMetadata = [];

    /**
     * Parse existing metadata to Generation Options array
     *
     * @param array $aMetadata
     *
     * @return array
     */
    public function parseMetadata(array $aMetadata)
    {
        $this->_aMetadata = $aMetadata;

        // debug
        echo '<pre>';
        print_r($this->_aMetadata);
        echo "</pre>";
        // debug

        $aGenerationOptions = [
            'aExtendClasses'   => array_keys($this->_aMetadata['extend']),
            'aNewControllers'  => $this->_parseMetadataControllers($this->_aMetadata['files']),
            'aNewModels'       => $this->_parseMetadataModels($this->_aMetadata['files']),
            'aNewLists'        => $this->_parseMetadataLists($this->_aMetadata['files']),
            'aNewWidgets'      => $this->_parseMetadataWidgets($this->_aMetadata['files']),
            'aNewBlocks'       => $this->_parseMetadataBlocks($this->_aMetadata['blocks']),
            'aModuleSettings'  => $this->_parseMetadataSettings($this->_aMetadata['settings']),
            'lbThemesNone'     => '',
            'aThemesList'      => [],
            'sInitialVersion'  => $this->_aMetadata['version'],
            'blFetchUnitTests' => '',
            'blRenderTasks'    => '',
            'blRenderSamples'  => '',
        ];

        return $aGenerationOptions;
    }

    /**
     * Parse Controllers from existing metadata
     *
     * @param array $aMetadataFiles
     *
     * @return array
     */
    protected function _parseMetadataControllers(array $aMetadataFiles)
    {
        $aMetadataControllers = [];
        foreach ($aMetadataFiles as $aMetadataKey => $aMetadataValue) {
            if (stripos($aMetadataValue, self::CONTROLLER_PATTERN) !== false) {
                $aMetadataControllers[] = $this->_stripModuleId($aMetadataKey);
            }
        }

        return (array) $aMetadataControllers;
    }

    /**
     * Parse Models from existing Metadata
     *
     * @param array $aMetadataFiles
     *
     * @return array
     */
    protected function _parseMetadataModels(array $aMetadataFiles)
    {
        $aMetadataModels = [];
        foreach ($aMetadataFiles as $aMetadataKey => $aMetadataValue) {
            if (stripos($aMetadataValue, self::MODEL_PATTERN) !== false) {
                $aExplodedModelPath = explode("/", $aMetadataValue);
                if (stripos(end($aExplodedModelPath), self::LIST_PATTERN) === false) {
                    $aMetadataModels[] = $this->_stripModuleId($aMetadataKey);
                }
            }
        }

        return (array) $aMetadataModels;
    }

    /**
     * Parse Lists from existing Metadata
     *
     * @param array $aMetadataFiles
     *
     * @return array
     */
    protected function _parseMetadataLists(array $aMetadataFiles)
    {
        $aMetadataLists = [];
        foreach ($aMetadataFiles as $aMetadataKey => $aMetadataValue) {
            if (stripos($aMetadataValue, self::MODEL_PATTERN) !== false) {
                $aExplodedListPath = explode("/", $aMetadataValue);
                if (stripos(end($aExplodedListPath), self::LIST_PATTERN) !== false) {
                    $aMetadataLists[] = $this->_stripModuleId($aMetadataKey);
                }
            }
        }

        return (array) $aMetadataLists;
    }

    /**
     * Parse Widgets from existing Metadata
     *
     * @param array $aMetadataFiles
     *
     * @return array
     */
    protected function _parseMetadataWidgets(array $aMetadataFiles)
    {
        $aMetadataWidgets = [];
        foreach ($aMetadataFiles as $aMetadataKey => $aMetadataValue) {
            if (stripos($aMetadataValue, self::WIDGET_PATTERN) !== false) {
                $aMetadataWidgets[] = $this->_stripModuleId($aMetadataKey);
            }
        }

        return (array) $aMetadataWidgets;
    }

    protected function _parseMetadataBlocks(array $aMetadataBlockFiles)
    {
        $aMetadataBlocks = [];
        foreach ($aMetadataBlockFiles as $aMetadataBlockFile) {
            $aMetadataBlocks[] = $aMetadataBlockFile['block'] . "@" . $aMetadataBlockFile['template'];
        }

        return (array) $aMetadataBlocks;
    }

    protected function _parseMetadataSettings(array $aMetadataSettingsFiles)
    {
        $aMetadataSettings = [];
        $arrKey = 0;
        foreach ($aMetadataSettingsFiles as $aMetadataSettingsFile) {
            foreach ($aMetadataSettingsFile as $index => $item) {
                if ($index !== 'group') {
                    $aMetadataSettings[$arrKey][$index] = $item;
                }
            }
            $arrKey++;
        }
        // TODO: need to seperate 'value' index by new lines (\n)
        echo "<pre>";
        print_r($aMetadataSettings);
        echo "</pre>";
        die;

        return (array) $aMetadataSettings;
    }

    /**
     * Strip module ID (vendor and module names) from module components names.
     *
     * @param $sFullName
     *
     * @return string
     */
    protected function _stripModuleId($sFullName)
    {
        return (string) str_ireplace($this->_aMetadata['id'], '', $sFullName);
    }
}
