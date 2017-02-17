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

    /**
     * Array of methods to parse different metadata settings depending on setting type.
     *
     * @var array
     *
     * @see _parseBoolSettingValue      Parse Checkbox values
     * @see _parseStrSettingValue       Parse String values
     * @see _parseNumSettingValue       Parse Number values
     * @see _parseArrSettingValue       Parse Array values
     * @see _parseAarrSettingValue      Parse Associative Array values
     * @see _parseSelectSettingValue    Parse Dropdown values
     */
    protected $aMetadataSettingsParse = [
        'bool'   => '_parseBoolSettingValue',
        'str'    => '_parseStrSettingValue',
        'num'    => '_parseNumSettingValue',
        'arr'    => '_parseArrSettingValue',
        'aarr'   => '_parseAarrSettingValue',
        'select' => '_parseSelectSettingValue',
    ];

    /**
     * Existing module metadata from metadata.php file
     *
     * @var array
     */
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

        // TODO: check with 1) array_key_exists() and 2) cast type

        $aGenerationOptions = [
            'aExtendClasses'  => array_keys($this->_aMetadata['extend']),
            'aNewControllers' => $this->_parseMetadataControllers($this->_aMetadata['files']),
            'aNewModels'      => $this->_parseMetadataModels($this->_aMetadata['files'], 'model'),
            'aNewLists'       => $this->_parseMetadataModels($this->_aMetadata['files'], 'list'),
            'aNewWidgets'     => $this->_parseMetadataWidgets($this->_aMetadata['files']),
            'aNewBlocks'      => $this->_parseMetadataBlocks($this->_aMetadata['blocks']),
            'aModuleSettings' => $this->_parseMetadataSettings($this->_aMetadata['settings']),
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
     * Parse Models (or Lists) from existing Metadata
     *
     * @param array  $aMetadataFiles
     * @param string $sFileType
     *
     * @return array
     */
    protected function _parseMetadataModels(array $aMetadataFiles, $sFileType = '')
    {
        $aMetadataModels = [];
        foreach ($aMetadataFiles as $aMetadataKey => $aMetadataValue) {
            if (stripos($aMetadataValue, self::MODEL_PATTERN) !== false) {
                $aExplodedModelPath = explode("/", $aMetadataValue);
                if ('model' === $sFileType) {
                    if (stripos(end($aExplodedModelPath), self::LIST_PATTERN) === false) {
                        $aMetadataModels[] = $this->_stripModuleId($aMetadataKey);
                    }
                } elseif ('list' === $sFileType) {
                    if (stripos(end($aExplodedModelPath), self::LIST_PATTERN) !== false) {
                        $aMetadataModels[] = $this->_stripModuleId($aMetadataKey);
                    }
                }
            }
        }

        return $aMetadataModels;
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

        return $aMetadataWidgets;
    }

    /**
     * Parse Metadata blocks from existing Metadata and check if they are unique.
     *
     * @param array $aMetadataBlockFiles
     *
     * @return array
     */
    protected function _parseMetadataBlocks(array $aMetadataBlockFiles)
    {
        $aMetadataBlocks = [];
        foreach ($aMetadataBlockFiles as $aMetadataBlockFile) {
            $sBlockPath = $aMetadataBlockFile['block'] . "@" . $aMetadataBlockFile['template'];
            if (!in_array($sBlockPath, $aMetadataBlocks)) {
                $aMetadataBlocks[] = $sBlockPath;
            }
        }

        return $aMetadataBlocks;
    }

    /**
     * Parse Metadata settings arrays of existing Metadata using different methods depending on type.
     *
     * @param array $aMetadataSettingsArrays
     *
     * @return array
     */
    protected function _parseMetadataSettings(array $aMetadataSettingsArrays)
    {
        $aMetadataSettings = [];
        $iArrayKey = 0;

        foreach ($aMetadataSettingsArrays as $aMetadataSettingsArray) {

            $aMetadataSettings[$iArrayKey]['name'] = $aMetadataSettingsArray['name'];

            $sType = array_key_exists($aMetadataSettingsArray['type'], $this->aMetadataSettingsParse)
                ? $aMetadataSettingsArray['type']
                : 'str';

            $sMethod = $this->aMetadataSettingsParse[$sType];

            $aMetadataSettings[$iArrayKey]['type'] = $sType;
            $aMetadataSettings[$iArrayKey]['value'] = $this->$sMethod($aMetadataSettingsArray);

            $iArrayKey++;
        }

        return $aMetadataSettings;
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

    /**
     * @param array $aMetadataSettingsArray
     *
     * @return string
     */
    protected function _parseBoolSettingValue(array $aMetadataSettingsArray)
    {
        return (string) $aMetadataSettingsArray['value'];
    }

    /**
     * @param array $aMetadataSettingsArray
     *
     * @return string
     */
    protected function _parseStrSettingValue(array $aMetadataSettingsArray)
    {
        return (string) $aMetadataSettingsArray['value'];
    }

    /**
     * @param array $aMetadataSettingsArray
     *
     * @return string
     */
    protected function _parseNumSettingValue(array $aMetadataSettingsArray)
    {
        return (string) $aMetadataSettingsArray['value'];
    }

    /**
     * @param array $aMetadataSettingsArray
     *
     * @return string
     */
    protected function _parseArrSettingValue(array $aMetadataSettingsArray)
    {
        return (string) implode(PHP_EOL, $aMetadataSettingsArray['value']);
    }

    /**
     * @param array $aMetadataSettingsArray
     *
     * @return string
     */
    protected function _parseAarrSettingValue(array $aMetadataSettingsArray)
    {
        $sArray = '';
        foreach ($aMetadataSettingsArray['value'] as $index => $item) {
            $sArray .= $index . ' => ' . $item . PHP_EOL;
        }

        return $sArray;
    }

    /**
     * @param array $aMetadataSettingsArray
     *
     * @return string
     */
    protected function _parseSelectSettingValue(array $aMetadataSettingsArray)
    {
        $sConstrains = $aMetadataSettingsArray['constrains'];

        return (string) str_replace("|", PHP_EOL, $sConstrains);
    }
}
