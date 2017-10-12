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

use \OxidEsales\Eshop\Core\Base;

/**
 * Class oxpsModuleGeneratorFileSystem.
 * A helper class for files and folders verification, creation and management methods.
 */
class oxpsModuleGeneratorFileSystem extends Base
{

    /**
     * Files and paths to ignore while copying.
     *
     * @var array
     */
    protected $_aIgnoreFiles = array('.', '..', '.gitkeep');


    /**
     * Get a list of files and paths to ignore while copying.
     *
     * @param array $aExtraIgnoreFiles Additional files and paths to ignore.
     *
     * @return array
     */
    public function getIgnoreFiles($aExtraIgnoreFiles = array())
    {
        $this->_aIgnoreFiles = array_merge((array) $this->_aIgnoreFiles, (array) $aExtraIgnoreFiles);

        return $this->_aIgnoreFiles;
    }


    /**
     * An alias for PHP function `is_file`.
     *
     * @param string $sPath
     *
     * @return bool
     */
    public function isFile($sPath)
    {
        return is_file($sPath);
    }

    /**
     * An alias for PHP function `is_dir`.
     *
     * @param string $sPath
     *
     * @return bool
     */
    public function isDir($sPath)
    {
        return is_dir($sPath);
    }

    /**
     * Check if folder exists and create folder(s) recursively if missing.
     *
     * @param string $sFolderFullPath
     */
    public function createFolder($sFolderFullPath)
    {
        if (!$this->isDir($sFolderFullPath)) {

            mkdir($sFolderFullPath, 0777, true);
        }
    }

    /**
     * Create a file using provided path and content, set full access permissions.
     *
     * @param string $sFileFullPath
     * @param string $sFileContent
     * @param bool   $blIfDoesNotExist
     */
    public function createFile($sFileFullPath, $sFileContent, $blIfDoesNotExist = false)
    {
        $blFileCreated = false;

        if (!$this->isFile($sFileFullPath) or empty($blIfDoesNotExist)) {
            $blFileCreated = (bool) file_put_contents($sFileFullPath, $sFileContent);
        }
        if ($blFileCreated) {
            chmod($sFileFullPath, 0777);
        }
    }

    /**
     * Rename a file.
     *
     * @param string $sOldPathAndName
     * @param string $sNewPathAndName
     */
    public function renameFile($sOldPathAndName, $sNewPathAndName)
    {
        if ($this->isFile($sOldPathAndName)) {
            rename($sOldPathAndName, $sNewPathAndName);
        }
    }

    /**
     * Recursive directory copying.
     * Copies all files and folders to a new location.
     *
     * @param string $sSourcePath      Where to copy from.
     * @param string $sDestinationPath Where to copy to.
     * @param array  $aExtraIgnoreFiles
     *
     * @return bool
     */
    public function copyFolder($sSourcePath, $sDestinationPath, $aExtraIgnoreFiles = array())
    {
        $sDS = DIRECTORY_SEPARATOR;

        if (!$this->isDir($sSourcePath) or !($hDir = opendir($sSourcePath))) {
            return false;
        }

        $aIgnoreFiles = (array) $this->getIgnoreFiles($aExtraIgnoreFiles);

        // Check module path to make sure nothing is missing
        $this->createFolder($sDestinationPath);

        while (false !== ($sFile = readdir($hDir))) {
            if (!in_array($sFile, $aIgnoreFiles)) {
                $this->_copy($sSourcePath . $sDS . $sFile, $sDestinationPath . $sDS . $sFile);
            }
        }

        closedir($hDir);

        return true;
    }

    /**
     * File copying.
     *
     * @param string $sSourcePath
     * @param string $sDestinationPath
     */
    public function copyFile($sSourcePath, $sDestinationPath)
    {
        if (!$this->_isFileOrTemplateAvailable($sDestinationPath)
            && $this->isFile($sSourcePath)
            && copy($sSourcePath, $sDestinationPath)
        ) {
            chmod($sDestinationPath, 0777);
        }
    }

    /**
     * Delete file.
     *
     * @param string $sFilePath
     */
    public function deleteFile($sFilePath)
    {
        if ($this->isFile($sFilePath)) {
            unlink($sFilePath);
        }
    }

    /**
     * Scans provided path for files and returns an array of file names.
     *
     * @param string $sPath Relative path to append to base module dir
     * @param bool $blRemoveFileExt Flag to remove file extensions
     * @return array
     */
    public function scanDirForFiles($sPath, $blRemoveFileExt = true)
    {
        $aFilteredFiles = array_filter(scandir($sPath), function ($item) use ($sPath) {
            return !is_dir($sPath . $item);
        });

        if ($blRemoveFileExt){
            $aFilteredFiles = array_map(function ($sFile){
                return pathinfo($sFile, PATHINFO_FILENAME);
            }, $aFilteredFiles);
        }

        return $aFilteredFiles;
    }


    /**
     * @param string $sPath
     *
     * @return bool
     */
    protected function _isFileOrTemplateAvailable($sPath)
    {
        return (
               $this->isFile($sPath)) ||
               $this->isFile(
                   str_replace('.tpl', '', $sPath)
               );
    }

    /**
     * If it's a file, copy it. If it's a folder, call recursive folder copy.
     *
     * @param string $sSourcePath
     * @param string $sDestinationPath
     */
    protected function _copy($sSourcePath, $sDestinationPath)
    {
            if ($this->isDir($sSourcePath)) {
                $this->copyFolder($sSourcePath, $sDestinationPath);
            } else {
                $this->copyFile($sSourcePath, $sDestinationPath);
        }
    }
}
