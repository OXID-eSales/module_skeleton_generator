<?php

class CriticalErrorsHandled
{
    /**
     * To store all needed error types
     * @var array
     */
    protected $_aErrorsTypes = array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR);

    public function __construct()
    {
        register_shutdown_function(array($this, "errorsHandling"));
    }

    /**
     * To add new error type if needed
     *
     * @param int $iType error type
     */
    public function setErrorType($iType)
    {
        if (!in_array($iType, $this->_aErrorsTypes)) {
            $this->_aErrorsTypes[] = $iType;
        }
    }

    /**
     * To get all errors types
     *
     * @return array
     */
    public function getAllErrorsTypes()
    {
        return $this->_aErrorsTypes;
    }

    /**
     * To activate additional error handling for fatal errors
     */
    public function errorsHandling()
    {
        ob_get_clean();
        $aError = error_get_last();

        if (in_array($aError["type"], $this->_aErrorsTypes)) {
            $iTime = microtime(true);
            $this->_restoreDatabase();
            echo "DB restore time: " . sprintf("%.2f", (microtime(true) - $iTime)) . " seconds\n";
            exit(1);
        }
    }

    /**
     * To restore all database tables
     *
     * @return int number of restored database tables
     */
    protected function _restoreDatabase()
    {
        $oDbMaintenance = new dbRestore();

        return $oDbMaintenance->restoreDB();
    }
}