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

namespace Oxps\ModuleGenerator\Core;

use \OxidEsales\Eshop\Core\Base;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\StrMb;
use OxidEsales\Eshop\Core\StrRegular;
use ReflectionClass;

/**
 * Class oxpsModuleGeneratorValidator.
 * Validation helpers used in module generation processes and data access helpers.
 */
class Validator extends Base
{

    /**
     * Folder name identifier in class path which determines backward compatibility - old class name usage.
     */
    const OXPS_BACKWARD_COMPATIBILITY_FOLDER = 'oxideshop-unified-namespace-generator/generated/';

    /**
     * Module instance used as information container for new module generation.
     *
     * @var OxModule
     */
    protected $_oOxModule;

    /**
     * @var Module
     */
    protected $_oModule;

    /**
     * Setting types
     *
     * @var array
     */
    protected $_aSettingTypes = [
        'bool',
        'str',
        'num',
        'arr',
        'aarr',
        'select',
    ];

    /**
     * Get oxModule instance or set it if not available.
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
     * Get Module instance or set it if not available.
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
     * Validate Settings type.
     *
     * @param string $sSettingType
     *
     * @return bool
     */
    public function validateSettingsType($sSettingType)
    {
        return in_array($sSettingType, $this->_aSettingTypes, true);
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
     * @return mixed
     */
    public function getArrayValue(array $aDataArray, $mArrayKey, $sType = 'string')
    {
        $mValue = isset($aDataArray[$mArrayKey]) ? $aDataArray[$mArrayKey] : null;

        return settype($mValue, $sType) ? $mValue : null;
    }
    
    /**
     * Check list of classes and link it with its relative path for each valid class.
     *
     * @param string $sClasses List of classes names separated with a new line.
     *
     * @return array With relative application path as value and clean class name as key for each valid class.
     * @throws \ReflectionException
     */
    public function validateAndLinkClasses($sClasses)
    {
        /** @var FileSystem $oFileSystemHelper */
        $oFileSystemHelper = Registry::get(FileSystem::class);
        $aClasses = $this->parseMultiLineInput($sClasses, 'not_empty');
        $aValidLinkedClasses = array();
        $oConfig = Registry::getConfig();
        $sBasePath = $oConfig->getConfigParam('sShopDir');

        foreach ($aClasses as $sClassName) {
            if (!class_exists($sClassName)) {
                continue;
            }

            $sClassData = (array) $this->_getClassPath($sClassName);
            
            if ($oFileSystemHelper->isFile($sClassData['classPath'])) {
                if (strpos(dirname($sClassData['classPath']), DIRECTORY_SEPARATOR . 'Core'. DIRECTORY_SEPARATOR)) {
                    $sClassData['classPath'] = 'Core' . DIRECTORY_SEPARATOR;
                } else {
                    $sClassData['classPath'] = str_replace($sBasePath, '', dirname($sClassData['classPath'])) . DIRECTORY_SEPARATOR;
                }

                $aValidLinkedClasses[$sClassName] = $sClassData;
            }
        }

        return $aValidLinkedClasses;
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
    public function parseMultiLineInput($sInput, $sLineValidation = 'camel_case')
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
    public function parseBlocksData($sBlocks, $sVendorPrefix, $sModuleName)
    {
        $sModuleId = sprintf('%s%s', $sVendorPrefix, $sModuleName);
        $aBlocks = $this->parseMultiLineInput($sBlocks, 'not_empty');
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
     * Check if module with provided name exists in the configured vendor directory.
     *
     * @param string $sModuleName
     *
     * @return bool
     */
    public function moduleExists($sModuleName)
    {
        $this->getOxModule()->init($sModuleName, [], $this->getModule()->getSetting('VendorPrefix'));

        /** @var FileSystem $oFileSystemHelper */
        $oFileSystemHelper = Registry::get(FileSystem::class);

        return $oFileSystemHelper->isDir($this->getOxModule()->getVendorPath() . $sModuleName)
               && !empty($sModuleName);
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
        $sBlockDefinition = trim((string) $sBlockDefinition);
        $aBlockDefinition = !empty($sBlockDefinition) ? explode("@", $sBlockDefinition) : array();

        $sBlockName = $this->getArrayValue($aBlockDefinition, 0);
        $sTemplatePath = $this->getArrayValue($aBlockDefinition, 1);

        if (empty($sBlockName) or empty($sTemplatePath)) {
            return array();
        }

        return array(
            'template' => $sTemplatePath,
            'block'    => $sBlockName,
            'file'     => sprintf('Application/views/blocks/%s_%s.tpl', $sModuleId, $sBlockName),
        );
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
            case 'not_empty':
                $blIsValid = !empty($sValue);
                break;

            case 'camel_case':
                /** @var Validator $oValidator */
                $oValidator = Registry::get(Validator::class);
                $blIsValid = (bool) $oValidator->validateCamelCaseName($sValue);
                break;
        }

        return $blIsValid;
    }
    
    /**
     * Build reflection object to get path to a class.
     * In case of old class name (backwards compatibility), use new class name of parent reflection
     * and include other necessary data such as namespace.
     *
     * @param string $sClassName
     *
     * @return array    Array keys:
     *                  'classPath' - path to a class
     *                  'v6ClassName - Oxid version 6 class name
     *                  'v6Namespace' - Class namespace
     * @throws \ReflectionException
     */
    protected function _getClassPath($sClassName)
    {
        /** @var StrMb|StrRegular $oStr */
        $oStr = Str::getStr();
        $aResult = array();
        $oReflection = new ReflectionClass(new $sClassName());
        $aResult['classPath'] = (string) $oReflection->getFilename();
        if (false !== $oStr->strpos($aResult['classPath'], self::OXPS_BACKWARD_COMPATIBILITY_FOLDER)) {
            $oReflection = $oReflection->getParentClass();
            $aResult['classPath'] = $sClassPath = (string) $oReflection->getFilename();
            $aResult['v6ClassName'] = $sNewClassName = (string) $oReflection->getShortName();
            $aResult['v6Namespace'] = (string) $this->_unifyNamespace($oReflection->getNamespaceName());
            
            //Making part of namespace (like /Application/Controller/Admin/...) for metadata.
            $aResult['v6ModuleNamespace'] = substr($aResult['classPath'], strpos($aResult['classPath'], 'source/') + strlen('source/'), strlen($aResult['classPath']));
            $aResult['v6ModuleNamespace'] = substr($aResult['v6ModuleNamespace'], 0, strrpos($aResult['v6ModuleNamespace'], '/') + strlen('/'));
        }

        return $aResult;
    }


    /**
     * Returns a unified namespace (eg. OxidEsales\EshopCommunity\Application -> OxidEsales\Eshop\Application)
     *
     * @param string $sNamespace
     * @return string
     */
    protected function _unifyNamespace($sNamespace)
    {
        return str_replace(array('EshopCommunity', 'EshopEnterprise', 'EshopProfessional'), 'Eshop', $sNamespace);
    }
}
