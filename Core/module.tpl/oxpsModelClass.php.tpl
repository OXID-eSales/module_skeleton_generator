<?php
[{$oModule->renderFileComment()}]
[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{assign var='sVendorPrefix' value=$oModule->getVendorPrefix()|ucfirst}]
[{assign var='sModuleName' value=$oModule->getModuleFolderName()}]
[{assign var='sNamespaceSuffix' value=$oModule->getNamespaceSuffixFromPath($sFilePath)}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]
[{assign var='sTableName' value=$oModule->getModuleId(false)|cat:'_'}]
[{assign var='sTableName' value=$sTableName|cat:$sClassName|lower}]

namespace [{$sVendorPrefix}]\[{$sModuleName}]\[{$sNamespaceSuffix}];

use \OxidEsales\Eshop\Core\Model\BaseModel;
use \OxidEsales\Eshop\Core\DatabaseProvider;
use \OxidEsales\Eshop\Core\TableViewNameGenerator;
[{if $oModule->renderTasks()}]
// TODO: Define other classes to use in the model, and delete not used.
[{/if}]

/**
 * Class [{$sClassName}].
 * [{$sClassName}] model.
 */
[{if $oModule->renderTasks()}]
//TODO: Extend oxI18n - if multilingual fields are used
[{/if}]
class [{$sClassName}] extends BaseModel
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

    // To get `OXID` use $this->getId();
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
        $this->_setFieldData('[somefield]', '[someValue]');
        // Or direct way: $this->[{$sTableName}]__[somefield] = new oxField([someValue]);
    }*/

    /**
     * Get [somefield].
     *
     * @return mixed
     */
    /*public function get[SomeField]()
    {
        return $this->getFieldData('[somefield]');
        // OR get raw value: return $this->oxpstest040_what__[somefield]->getRawValue();
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
        /** @var TableViewNameGenerator $tableGenerator */
        /* $tableGenerator = \OxidEsales\Eshop\Core\Registry::get(TableViewNameGenerator::class);

        $query = sprintf(
            "SELECT * FROM `%s` WHERE `[SOME_FIELD]` = %s LIMIT 1",
            $tableGenerator->getViewName($this->getCoreTableName()),
            DatabaseProvider::getDb()->quote(trim([mSomeField]))
        );

        return $this->assignRecord($query);
    } */
[{/if}]
}
