<?php
[{$oModule->renderFileComment()}]
[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]
[{assign var='sTableName' value=$oModule->getModuleId()|cat:'_'}]
[{assign var='sTableName' value=$sTableName|cat:$sClassName|lower}]

use \OxidEsales\Eshop\Core\Model\BaseModel;

/**
 * Class [{$sClassNamePrefix}][{$sClassName}].
 * [{$sClassName}] model.
 */
[{if $oModule->renderTasks()}]
//TODO: Extend oxI18n - if multilingual fields are used
[{/if}]
class [{$sClassNamePrefix}][{$sClassName}] extends BaseModel
{

    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct();

[{if $oModule->renderTasks()}]
        // TODO: Adjust the table name if needed! Create the table in docs/install.sql.
[{/if}]
        $this->init('[{$sTableName}]');
    }

[{if $oModule->renderTasks()}]
    // NOTE: If You overload parent methods like save, load, delete, etc.,
    //       call parent methods through protected functions like "[{$sClassNamePrefix}][{$sClassName}]_[someMethod]_parent(..."

    // For empty OXID use $this->getId();
    // And You can use methods like $this-getClassName / getCoreTableName / getViewName / isLoaded ... and so on.
[{/if}]
[{if $oModule->renderSamples()}]

    // !!!EXAMPLES!!!
[{if $oModule->renderTasks()}]
    // TODO: Implement real getters, setter, loaders, etc. and delete the examples.
[{/if}]

    /**
     * Set [somefield].
     *
     * @param mixed [someField]
     */
    /*public function set[SomeField]([someValue])
    {
        $this->[{$sTableName}]__[somefield] = new oxField([someValue]);
    }*/

    /**
     * Get [somefield].
     *
     * @return double
     */
    /*public function get[SomeField]()
    {
        return $this->[{$sTableName}]__[somefield]->value;
    }*/

    /**
     * Load by... [sample function]
     *
     * @param mixed [mSomeField]
     *
     * @return mixed
     */
    // TODO: Replace [mSomeField] and [SOME_FIELD] with real values You need!
    /* public function loadBy...([mSomeField])
    {
[{if $oModule->renderTasks()}]
        // NOTE: For MySQL queries user proper case, backticks (`) and keep it clear and readable.
        // NOTE: Database tables are name lowercase, as example "[{$sTableName}]"
        //       Each new table MUST have a primary key named "OXID" of type char(32)
        //       Custom fields are named UPPERCASE with Your vendor prefix each, for example "[{$oModule->getVendorPrefix(true)}]MYFIELD"
[{/if}]
        $sQuery = sprintf(
            "SELECT * FROM `%s` WHERE `[SOME_FIELD]` = %s LIMIT 1",
            getViewName('[{$sTableName}]'),
            oxDb::getDb()->quote(trim([mSomeField]))
        );

        return $this->assignRecord($sQuery);
    } */
[{/if}]
}
