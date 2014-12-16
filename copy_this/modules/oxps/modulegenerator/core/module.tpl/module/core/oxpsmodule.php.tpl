<?php
[{$oModule->renderFileComment()}]

[{assign var='sModuleClassName' value=$oModule->getModuleClassName()|cat:'Module'}]
/**
 * Class [{$sModuleClassName}]
 * Handles module setup, provides additional tools and module related helpers.
 *
 * @codeCoverageIgnore
 */
class [{$sModuleClassName}] extends oxModule
{

    /**
     * Class constructor.
     * Sets current module main data and loads the rest module info.
     */
    function __construct()
    {
        $sModuleId = '[{$oModule->getModuleId()}]';

        $this->setModuleData(
            array(
                 'id'          => $sModuleId,
                 'title'       => '[{$oModule->getTitle()}]',
                 'description' => '[{$oModule->getTitle()}] Module',
            )
        );

        $this->load($sModuleId);

        oxRegistry::set('[{$sModuleClassName}]', $this);
    }


    /**
     * Module activation script.
     */
    public static function onActivate()
    {
        return self::_dbEvent('install.sql', 'Error activating module: ');
    }

    /**
     * Module deactivation script.
     */
    public static function onDeactivate()
    {
        self::_dbEvent('uninstall.sql', 'Error deactivating module: ');
    }

    /**
     * Clean temp folder content.
     *
     * @param string $sClearFolderPath Sub-folder path to delete from. Should be a full, valid path inside temp folder.
     *
     * @return boolean
     */
    public static function clearTmp($sClearFolderPath = '')
    {
        $sFolderPath = self::_getFolderToClear($sClearFolderPath);
        $hDirHandler = opendir($sFolderPath);

        if (!empty($hDirHandler)) {
            while (false !== ($sFileName = readdir($hDirHandler))) {
                $sFilePath = $sFolderPath . DIRECTORY_SEPARATOR . $sFileName;
                self::_clear($sFileName, $sFilePath);
            }

            closedir($hDirHandler);
        }

        return true;
    }

    /**
     * Get translated string by the translation code.
     *
     * @param string  $sCode
     * @param boolean $blUseModulePrefix If True - adds the module translations prefix, if False - not.
     *
     * @return string
     */
    public function translate($sCode, $blUseModulePrefix = true)
    {
        if ($blUseModulePrefix) {
            $sCode = '[{$oModule->getVendorPrefix(true)}]_[{$oModule->getModuleFolderName(true)}]_' . $sCode;
        }

        return oxRegistry::getLang()->translateString($sCode, oxRegistry::getLang()->getBaseLanguage(), false);
    }

    /**
     * Get CMS snippet content by identified ID.
     *
     * @param string $sIdentifier
     * @param bool   $blNoHtml
     *
     * @return string
     */
    public function getCmsContent($sIdentifier, $blNoHtml = true)
    {
        $sValue = '';

        /** @var oxContent|oxI18n $oContent */
        $oContent = oxNew('oxContent');
        $oContent->loadByIdent(trim((string) $sIdentifier));

        if ($oContent->oxcontents__oxcontent instanceof oxField) {
            $sValue = (string) $oContent->oxcontents__oxcontent->getRawValue();
            $sValue = (empty($blNoHtml) ? $sValue : nl2br(strip_tags($sValue)));
        }

        return $sValue;
    }

    /**
     * Get module setting value.
     *
     * @param string  $sModuleSettingName Module setting parameter name (key).
     * @param boolean $blUseModulePrefix  If True - adds the module settings prefix, if False - not.
     *
     * @return mixed
     */
    public function getSetting($sModuleSettingName, $blUseModulePrefix = true)
    {
        if ($blUseModulePrefix) {
            $sModuleSettingName = '[{$oModule->getModuleClassName()}]' . (string) $sModuleSettingName;
        }

        return oxRegistry::getConfig()->getConfigParam((string) $sModuleSettingName);
    }

    /**
     * Get module path.
     *
     * @return string Full path to the module directory.
     */
    public function getPath()
    {
        return oxRegistry::getConfig()->getModulesDir() . '[{$oModule->getVendorPrefix()}]/[{$oModule->getModuleFolderName()}]/';
    }


    /**
     * Install/uninstall event.
     * Executes SQL queries form a file.
     *
     * @param string $sSqlFile      SQL file located in module docs folder (usually install.sql or uninstall.sql).
     * @param string $sFailureError An error message to show on failure.
     */
    protected static function _dbEvent($sSqlFile, $sFailureError = 'Operation failed: ')
    {
        try {
            $oDb  = oxDb::getDb();
            $sSql = file_get_contents(dirname(__FILE__) . '/../docs/' . (string) $sSqlFile);
            $aSql = (array) explode(';', $sSql);

            foreach ($aSql as $sQuery) {
                if (!empty($sQuery)) {
                    $oDb->execute($sQuery);
                }
            }
        } catch (Exception $ex) {
            error_log($sFailureError . $ex->getMessage());
        }

[{if $oModule->renderSamples()}]
[{if $oModule->renderTasks()}]
        // TODO: Use following lines to update database views if You need that.
[{/if}]
        /** @var oxDbMetaDataHandler $oDbHandler */
        //$oDbHandler = oxNew('oxDbMetaDataHandler');
        //$oDbHandler->updateViews();
[{/if}]

        self::clearTmp();

        return true;
    }
    
    /**
     * Check if provided path is inside eShop `tpm/` folder or use the `tmp/` folder path.
     *
     * @param string $sClearFolderPath
     *
     * @return string
     */
    protected static function _getFolderToClear($sClearFolderPath = '')
    {
        $sTempFolderPath = (string) oxRegistry::getConfig()->getConfigParam('sCompileDir');

        if (!empty($sClearFolderPath) and (strpos($sClearFolderPath, $sTempFolderPath) !== false)) {
            $sFolderPath = $sClearFolderPath;
        } else {
            $sFolderPath = $sTempFolderPath;
        }

        return $sFolderPath;
    }

    /**
     * Check if resource could be deleted, then delete it's a file or
     * call recursive folder deletion if it's a directory.
     *
     * @param string $sFileName
     * @param string $sFilePath
     */
    protected static function _clear($sFileName, $sFilePath)
    {
        if (!in_array($sFileName, array('.', '..', '.gitkeep', '.htaccess'))) {
            if (is_file($sFilePath)) {
                @unlink($sFilePath);
            } else {
                self::clearTmp($sFilePath);
            }
        }
    }
}
