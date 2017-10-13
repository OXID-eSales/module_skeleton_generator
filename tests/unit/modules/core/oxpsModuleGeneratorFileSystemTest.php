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
 * Class oxpsModuleGeneratorFileSystemTest
 * INTEGRATION tests for core class oxpsModuleGeneratorFileSystem.
 *
 * @see oxpsModuleGeneratorFileSystem
 */
class oxpsModuleGeneratorFileSystemTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var oxpsModuleGeneratorFileSystem
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     * Create test files and folders.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = new oxpsModuleGeneratorFileSystem();

        @mkdir($this->_getTestPath());
        @mkdir($this->_getTestPath('folder'));
        @file_put_contents($this->_getTestPath('file.txt'), PHP_EOL);
        @file_put_contents($this->_getTestPath('folder/file.txt'), PHP_EOL);
    }

    /**
     * Clean state after test.
     * Remove test files and folders.
     */
    public function tearDown()
    {
        @shell_exec('rm -rf ' . $this->_getTestPath());

        parent::tearDown();
    }


    public function testIsDir_directoryDoeNotExist_returnFalse()
    {
        $this->assertFalse($this->SUT->isDir($this->_getTestPath('not_existing_folder')));
    }

    public function testIsDir_pathIsFile_returnFalse()
    {
        $this->assertFalse($this->SUT->isDir($this->_getTestPath('file.txt')));
    }

    public function testIsDir_pathIsExistingFolder_returnTrue()
    {
        $this->assertTrue($this->SUT->isDir($this->_getTestPath('folder')));
    }


    public function testIsFile_fileDoeNotExist_returnFalse()
    {
        $this->assertFalse($this->SUT->isFile($this->_getTestPath('not_existing_file.txt')));
    }

    public function testIsFile_pathIsFolder_returnFalse()
    {
        $this->assertFalse($this->SUT->isFile($this->_getTestPath('folder')));
    }

    public function testIsFile_pathIsExistingFile_returnTrue()
    {
        $this->assertTrue($this->SUT->isFile($this->_getTestPath('file.txt')));
    }


    public function testCreateFolder_folderExists_notTryToCreateIt()
    {
        $sPath = $this->_getTestPath('folder');

        $this->assertFileExists($sPath);
        $this->SUT->createFolder($sPath);
        $this->assertFileExists($sPath);
    }

    public function testCreateFolder_folderDoeNotExists_createFolder()
    {
        $sPath = $this->_getTestPath('new_folder');

        $this->assertFileNotExists($sPath);
        $this->SUT->createFolder($sPath);
        $this->assertFileExists($sPath);
    }

    public function testCreateFolder_pathIsInsideNonExistingFolder_createFoldersRecursively()
    {
        $sPath = $this->_getTestPath('sub_folder/new_folder');

        $this->assertFileNotExists($sPath);
        $this->SUT->createFolder($sPath);
        $this->assertFileExists($sPath);
    }


    public function testCreateFile_fileDoesNotExist_createFile()
    {
        $sPath = $this->_getTestPath('new_file.txt');

        $this->assertFileNotExists($sPath);
        $this->SUT->createFile($sPath, 'TEST NEW FILE');
        $this->assertFileExists($sPath);
        $this->assertStringEqualsFile($sPath, 'TEST NEW FILE');
    }

    public function testCreateFile_fileExistsThirdArgumentEmpty_overwriteFile()
    {
        $sPath = $this->_getTestPath('file.txt');

        $this->assertFileExists($sPath);
        $this->assertStringEqualsFile($sPath, PHP_EOL);

        $this->SUT->createFile($sPath, '!TEST FILE - changed');
        $this->assertStringEqualsFile($sPath, '!TEST FILE - changed');
    }

    public function testCreateFile_fileExistsThirdArgumentTrue_fileRemainUnchanged()
    {
        $sPath = $this->_getTestPath('file.txt');

        $this->assertFileExists($sPath);
        $this->assertStringEqualsFile($sPath, PHP_EOL);

        $this->SUT->createFile($sPath, '!TEST NEW FILE - changed', true);
        $this->assertStringEqualsFile($sPath, PHP_EOL);
    }


    public function testRenameFile_fileDoesNotExist_notIssueRename()
    {
        $sOldPath = $this->_getTestPath('file-one.txt');
        $sNewPath = $this->_getTestPath('file-two.txt');

        $this->assertFileNotExists($sOldPath);
        $this->assertFileNotExists($sNewPath);

        $this->SUT->renameFile($sOldPath, $sNewPath);

        $this->assertFileNotExists($sOldPath);
        $this->assertFileNotExists($sNewPath);
    }

    public function testRenameFile_fileExists_renameTheFile()
    {
        $sOldPath = $this->_getTestPath('file.txt');
        $sNewPath = $this->_getTestPath('file-two.txt');

        $this->assertFileExists($sOldPath);
        $this->assertFileNotExists($sNewPath);

        $this->SUT->renameFile($sOldPath, $sNewPath);

        $this->assertFileNotExists($sOldPath);
        $this->assertFileExists($sNewPath);
    }


    public function testCopyFolder_folderDoesNotExist_returnFalse()
    {
        $sOldPath = $this->_getTestPath('folder-one');
        $sNewPath = $this->_getTestPath('folder-two');

        $this->assertFileNotExists($sOldPath);
        $this->assertFileNotExists($sNewPath);

        $this->assertFalse($this->SUT->copyFolder($sOldPath, $sNewPath));

        $this->assertFileNotExists($sOldPath);
        $this->assertFileNotExists($sNewPath);
    }

    public function testCopyFolder_folderExists_copyContentAndReturnTrue()
    {
        $sOldPath = $this->_getTestPath('folder');
        $sNewPath = $this->_getTestPath('folder-two');

        $this->assertFileExists($sOldPath);
        $this->assertFileNotExists($sNewPath);

        $this->assertTrue($this->SUT->copyFolder($sOldPath, $sNewPath));

        $this->assertFileExists($sOldPath);
        $this->assertFileExists($sNewPath);
        $this->assertFileExists($this->_getTestPath('folder-two/file.txt'));
    }

    public function testCopyFolder_folderExistsWithSubFoldersAndFiles_copyAllContentRecursivelyAndReturnTrue()
    {
        $sOldPath = $this->_getTestPath('folder');
        $sNewPath = $this->_getTestPath('folder-two');

        @mkdir($this->_getTestPath('folder/sub-folder'));
        @mkdir($this->_getTestPath('folder/sub-folder/sub-sub-folder'));
        @file_put_contents($this->_getTestPath('folder/sub-folder/file-one.txt'), '1');
        @file_put_contents($this->_getTestPath('folder/sub-folder/file-two.txt'), '2');
        @file_put_contents($this->_getTestPath('folder/sub-folder/sub-sub-folder/file-three.txt'), '3');

        $this->assertFileExists($sOldPath);
        $this->assertFileNotExists($sNewPath);

        $this->assertTrue($this->SUT->copyFolder($sOldPath, $sNewPath));

        $this->assertFileExists($sOldPath);
        $this->assertFileExists($sNewPath);
        $this->assertFileExists($this->_getTestPath('folder-two/file.txt'));
        $this->assertFileExists($this->_getTestPath('folder-two/sub-folder'));
        $this->assertFileExists($this->_getTestPath('folder-two/sub-folder/file-one.txt'));
        $this->assertStringEqualsFile($this->_getTestPath('folder-two/sub-folder/file-one.txt'), '1');
        $this->assertFileExists($this->_getTestPath('folder-two/sub-folder/file-two.txt'));
        $this->assertStringEqualsFile($this->_getTestPath('folder-two/sub-folder/file-two.txt'), '2');
        $this->assertFileExists($this->_getTestPath('folder-two/sub-folder/sub-sub-folder'));
        $this->assertFileExists($this->_getTestPath('folder-two/sub-folder/sub-sub-folder/file-three.txt'));
        $this->assertStringEqualsFile($this->_getTestPath('folder-two/sub-folder/sub-sub-folder/file-three.txt'), '3');
    }


    public function testCopyFile_fileDoesNotExist_notCopyAnything()
    {
        $sOldPath = $this->_getTestPath('file-one.txt');
        $sNewPath = $this->_getTestPath('file-two.txt');

        $this->assertFileNotExists($sOldPath);
        $this->assertFileNotExists($sNewPath);

        $this->SUT->copyFile($sOldPath, $sNewPath);

        $this->assertFileNotExists($sOldPath);
        $this->assertFileNotExists($sNewPath);
    }

    public function testCopyFile_fileExists_copyTheFile()
    {
        $sOldPath = $this->_getTestPath('file.txt');
        $sNewPath = $this->_getTestPath('file-two.txt');

        $this->assertFileExists($sOldPath);
        $this->assertFileNotExists($sNewPath);

        $this->SUT->copyFile($sOldPath, $sNewPath);

        $this->assertFileExists($sOldPath);
        $this->assertFileExists($sNewPath);
        $this->assertStringEqualsFile($sNewPath, PHP_EOL);
    }


    /**
     * Get a path inside test folder in temp directory.
     *
     * @param string $sPathSuffix
     *
     * @return string
     */
    protected function _getTestPath($sPathSuffix = '')
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sCompileDir') . DIRECTORY_SEPARATOR .
               'test' . DIRECTORY_SEPARATOR . (string) $sPathSuffix;
    }
}
