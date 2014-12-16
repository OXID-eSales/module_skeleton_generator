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
 * Class oxpsModuleGeneratorSettings
 * Generated module settings parsing and validation helper.
 */
class oxpsModuleGeneratorSettings extends oxSuperCfg
{

    /**
     * Cleans settings array got from request to make it suitable for a module metadata file generation.
     *
     * @param array $aModuleSettings Raw array got from request, could contain empty or faulty values.
     *
     * @return array Clean settings array, that could be used in metadata generation.
     */
    public function getModuleSettings(array $aModuleSettings)
    {
        /** @var oxpsModuleGeneratorValidator $oValidator */
        $oValidator = oxRegistry::get('oxpsModuleGeneratorValidator');

        $aCleanSettings = array();

        foreach ($aModuleSettings as $aModuleSetting) {

            // Get clean name, type and value
            $aModuleSetting = (array) $aModuleSetting;
            $sSettingName = trim($oValidator->getArrayValue($aModuleSetting, 'name', 'string'));
            $sSettingType = $oValidator->getArrayValue($aModuleSetting, 'type', 'string');
            $sInitialValue = $oValidator->getArrayValue($aModuleSetting, 'value');

            // Check if name and type are valid
            if ($oValidator->validateCamelCaseName($sSettingName) and
                in_array($sSettingType, array('bool', 'str', 'num', 'arr', 'aarr', 'select'))
            ) {
                // Prepare for rendering as raw value (variable as string with quotes and so on)
                $aSetting = (array) $this->_getRawSettingValue($sSettingName, $sSettingType, $sInitialValue);

                // Add clean settings entry
                $aCleanSettings[] = (object) $aSetting;
            }
        }

        return $aCleanSettings;
    }


    /**
     * Compile a settings entry array to be used in metadata generation.
     * Value is a raw output to be rendered in metadata (variable as string with quotes and so on).
     *
     * @param string $sSettingName
     * @param string $sSettingType
     * @param string $sInitialValue
     *
     * @return array
     */
    protected function _getRawSettingValue($sSettingName, $sSettingType, $sInitialValue)
    {
        $aSettingParsersMap = array(
            'bool'   => '_getBooleanSettingValue',
            'num'    => '_getNumericSettingValue',
            'arr'    => '_getArraySettingValue',
            'aarr'   => '_getAssocArraySettingValue',
            'select' => '_getSelectSettingValue',
        );

        if (array_key_exists($sSettingType, $aSettingParsersMap)) {
            $sMethod = $aSettingParsersMap[$sSettingType];
            $sValue = $this->$sMethod((string) $sInitialValue);
        } else {

            // String ("str") type is the default fallback
            $sValue = "'" . (string) $sInitialValue . "'";
        }

        $aSetting = array(
            'name'  => $sSettingName,
            'type'  => $sSettingType,
            'value' => $sValue,
        );

        if ($sSettingType == 'select') {

            // For "select" type also adds options string under "constrains" key
            $aSetting['constrains'] = $this->_getSelectSettingValue($sInitialValue, true);
        }

        return $aSetting;
    }

    /**
     * Boolean type value parser.
     *
     * @param string $sInitialValue
     *
     * @return string
     */
    protected function _getBooleanSettingValue($sInitialValue)
    {
        return (empty($sInitialValue) or oxStr::getStr()->strtolower($sInitialValue) == 'false')
            ? 'false'
            : 'true';
    }

    /**
     * Numeric type value parser.
     *
     * @param string $sInitialValue
     *
     * @return float
     */
    protected function _getNumericSettingValue($sInitialValue)
    {
        return (double) $sInitialValue;
    }

    /**
     * Array type value parser.
     *
     * @param string $sInitialValue
     *
     * @return string
     */
    protected function _getArraySettingValue($sInitialValue)
    {
        $sValue = str_replace(array("\r\n", "\n\r", "\r"), PHP_EOL, $sInitialValue);

        return "array('" . implode("', '", explode(PHP_EOL, $sValue)) . "')";
    }

    /**
     * Parse clean initial value for assoc array ("aarr") setting type.
     *
     * @param string $sInitialValue
     *
     * @return string
     */
    protected function _getAssocArraySettingValue($sInitialValue)
    {
        $sInitialValue = str_replace(array("\r\n", "\n\r", "\r"), PHP_EOL, $sInitialValue);
        $aInitialValue = array();

        $aLines = explode(PHP_EOL, $sInitialValue);

        foreach ($aLines as $sLine) {
            $aKeyAndValue = explode('=>', $sLine);

            if (count($aKeyAndValue) == 2) {
                $aInitialValue[] = sprintf(
                    "'%s' => '%s'",
                    trim($aKeyAndValue[0]),
                    trim($aKeyAndValue[1])
                );
            }
        }

        return "array(" . implode(", ", $aInitialValue) . ")";
    }

    /**
     * Select (drop down) type value parser.
     *
     * @param string $sInitialValue
     * @param bool   $blReturnOptions If False returns default select value, if True - options string is returned.
     *
     * @return string
     */
    protected function _getSelectSettingValue($sInitialValue, $blReturnOptions = false)
    {
        $sInitialValue = str_replace(array("\r\n", "\n\r", "\r"), PHP_EOL, $sInitialValue);
        $aOptions = explode(PHP_EOL, $sInitialValue);
        $sOptions = "'" . implode("|", $aOptions) . "'";
        $sInitialValue = "'" . reset($aOptions) . "'";

        return empty($blReturnOptions) ? $sInitialValue : $sOptions;
    }
}
