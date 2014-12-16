<?php

/**
 * Database maintenance class responsible complete for backup and restoration of test database.
 */
class DbRestore
{
    /**
     * Temp directory, where to store database dump
     *
     * @var string
     */
    private $_sTmpDir = '/tmp/';

    /**
     * Dump file path
     *
     * @var string
     */
    private $_sTmpFilePath = null;

    /*
     * Dump of the original db
     *
     * @var array
     */
    private $_aDBDump = null;

    /**
     * Sets temp directory to oxCCTempDir if constant exists
     */
    public function __construct()
    {
        if (defined('oxCCTempDir')) {
            $this->_sTmpDir = oxCCTempDir;
        }
    }

    /**
     * Returns dump file path
     *
     * @return string
     */
    public function getDumpFolderPath()
    {
        if (is_null($this->_sTmpFilePath)) {
            $sDbName = oxRegistry::getConfig()->getConfigParam('dbName');
            $this->_sTmpFilePath = $this->_sTmpDir . '/' . $sDbName . '_dbdump/';
            if (!file_exists($this->_sTmpFilePath)) {
                mkdir($this->_sTmpFilePath, 0777, true);
                chmod($this->_sTmpFilePath, 0777);
            }
        }

        return $this->_sTmpFilePath;
    }

    /**
     * Returns database dump data
     *
     * @return array
     */
    public function getDumpChecksum()
    {
        if (is_null($this->_aDBDump)) {
            modConfig::getInstance()->cleanup();
            modConfig::$unitMOD = null;

            $aDBDump = file_get_contents($this->getDumpFolderPath() . 'dbdata');
            $this->_aDBDump = unserialize($aDBDump);
        }

        return $this->_aDBDump;
    }

    /**
     * Checks which tables of the db changed and then restores these tables.
     * Uses dump file '/tmp/tmp_db_dump' for comparison and restoring.
     */
    public function restoreDB()
    {
        $aTables = $this->_getDbTables();
        $aChecksum = $this->_getTableChecksum($aTables);

        $aDumpChecksum = $this->getDumpChecksum();
        $aDumpTables = array_keys($aDumpChecksum);

        foreach ($aTables as $sTable) {
            if (!in_array($sTable, $aDumpTables)) {
                $this->dropTable($sTable);
            } else {
                if ($aChecksum[$sTable] !== $aDumpChecksum[$sTable]) {
                    $this->restoreTable($sTable, false);
                }
            }
        }

        $aMissingTables = array_diff($aDumpTables, $aTables);
        foreach ($aMissingTables as $sTable) {
            $this->restoreTable($sTable, false);
        }
    }

    /**
     * Restores table records
     *
     * @param string $sTable
     * @param bool $blCheckChecksum
     */
    public function restoreTable($sTable, $blCheckChecksum = true)
    {
        if ($blCheckChecksum) {
            $aDumpChecksum = $this->getDumpChecksum();
            $aChecksum = $this->_getTableChecksum($sTable);
            if ($aChecksum[$sTable] === $aDumpChecksum[$sTable]) {
                return;
            }
        }

        $sFile = $this->getDumpFolderPath() . $sTable . "_dump.sql";

        if (file_exists($sFile)) {
            $oDb = oxDb::getDb();
            $oDb->query("TRUNCATE TABLE `$sTable`");

            $sql = "LOAD DATA INFILE '$sFile' INTO TABLE `$sTable`";
            $oDb->Query($sql);
        }
    }

    /**
     * Drops table
     *
     * @param $sTable
     */
    public function dropTable($sTable)
    {
        $oDB = oxDb::getDb();
        $oDB->query("DROP TABLE `$sTable`");
    }

    /**
     * Create database tables dump for active database
     *
     * @return null
     */
    public function dumpDB()
    {
        $aTables = $this->_getDbTables();
        $oDb = oxDb::getDb();

        foreach ($aTables as $sTable) {
            $sFile = $this->getDumpFolderPath() . $sTable . '_dump.sql';
            if (file_exists($sFile)) {
                unlink($sFile);
            }

            $sql = "SELECT * INTO OUTFILE '" . $sFile . "' FROM $sTable";
            $oDb->Query($sql);
        }

        $aChecksum = $this->_getTableChecksum($aTables);

        file_put_contents($this->getDumpFolderPath() . 'dbdata', serialize($aChecksum));
    }

    /**
     * Converts a string to UTF format.
     *
     * @param array|string $aTables
     *
     * @return array
     */
    protected function _getTableChecksum($aTables)
    {
        $aTables = is_array($aTables) ? $aTables : array($aTables);
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sSelect = 'CHECKSUM TABLE ' . implode(", ", $aTables);
        $aResults = $oDb->getArray($sSelect);

        $sDbName = oxRegistry::getConfig()->getConfigParam('dbName');
        $aChecksum = array();
        foreach ($aResults as $aResult) {
            $sTable = str_replace($sDbName . '.', '', $aResult['Table']);
            $aChecksum[$sTable] = $aResult['Checksum'];
        }

        return $aChecksum;
    }

    /**
     * Returns database tables, excluding views
     */
    protected function _getDbTables()
    {
        $oDB = oxDb::getDb(oxDb::FETCH_MODE_NUM);
        $aTables = $oDB->getCol("SHOW TABLES");

        foreach ($aTables as $iKey => $sTable) {
            if (strpos($sTable, 'oxv_') === 0) {
                unset($aTables[$iKey]);
            }
        }

        return $aTables;
    }
}