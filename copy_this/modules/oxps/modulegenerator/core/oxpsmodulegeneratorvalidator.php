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

/**
 * Class oxpsModuleGeneratorValidator.
 * Validation helpers used in module generation processes and data access helpers.
 */
class oxpsModuleGeneratorValidator extends oxSuperCfg
{

    /**
     * Check if vendor prefix matches official format: 2 to 4 latin lowercase letters.
     *
     * @param string $sVendorPrefix
     *
     * @return bool
     */
    public function validateVendorPrefix($sVendorPrefix)
    {
        return (bool) preg_match('/^([a-z]{2,4})$/', (string) $sVendorPrefix);
    }

    /**
     * Validate a name in UpperCamelCase style.
     * Accepts only latin letters and numbers, first char is always capitalized latin letter.
     *
     * @param string $sVariableName
     *
     * @return bool
     */
    public function validateCamelCaseName($sVariableName)
    {
        return (
            !empty($sVariableName) and
            preg_match('/^([A-Z]{1})([a-zA-Z0-9]{1,63})$/', (string) $sVariableName)
        );
    }

    /**
     * Converts camel case string to human readable string with spaces between words.
     * Treats UPPERCASE abbreviations and numbers as separate words.
     *
     * @param string $sCamelCaseString
     *
     * @return string
     */
    public function camelCaseToHumanReadable($sCamelCaseString)
    {
        // Split CamelCase
        $sHumanReadableString = preg_replace('/([A-Z]{1}[a-z]+)/', ' $1', (string) $sCamelCaseString);

        // Split numbers attached to letters
        $sHumanReadableString = preg_replace('/([a-zA-Z])([0-9]{1})/', '$1 $2', $sHumanReadableString);

        // Split UPPERCASE attached to words
        $sHumanReadableString = preg_replace('/([a-z0-9])([A-Z]{1})/', '$1 $2', $sHumanReadableString);

        return !is_null($sHumanReadableString) ? trim($sHumanReadableString) : $sCamelCaseString;
    }

    /**
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
        $mValue = isset($aDataArray[$mArrayKey]) ? $aDataArray[$mArrayKey] : null;

        return settype($mValue, $sType) ? $mValue : null;
    }
}
