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
 * Class oxpsModuleGeneratorFileSystem.
 * A helper class for files and folders verification, creation and management methods.
 */
class oxpsModuleGeneratorFileSystem extends oxSuperCfg
{

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
     *
     * @return bool
     */
    public function copyFolder($sSourcePath, $sDestinationPath)
    {
        $sDS = DIRECTORY_SEPARATOR;

        if (!$this->isDir($sSourcePath) or !($hDir = opendir($sSourcePath))) {
            return false;
        }

        // Check module path to make sure nothing is missing
        $this->createFolder($sDestinationPath);

        while (false !== ($sFile = readdir($hDir))) {
            $this->_copy($sFile, $sSourcePath . $sDS . $sFile, $sDestinationPath . $sDS . $sFile);
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
        if ($this->isFile($sSourcePath) and copy($sSourcePath, $sDestinationPath)) {
            chmod($sDestinationPath, 0777);
        }
    }


    /**
     * Check if resource could be copied.
     * If it's a file, copy it. If it's a folder, call recursive folder copy.
     *
     * @param string $sFile
     * @param string $sSourcePath
     * @param string $sDestinationPath
     */
    protected function _copy($sFile, $sSourcePath, $sDestinationPath)
    {
        if (!in_array($sFile, array('.', '..', '.gitkeep'))) {
            if ($this->isDir($sSourcePath)) {
                $this->copyFolder($sSourcePath, $sDestinationPath);
            } else {
                $this->copyFile($sSourcePath, $sDestinationPath);
            }
        }
    }
}
