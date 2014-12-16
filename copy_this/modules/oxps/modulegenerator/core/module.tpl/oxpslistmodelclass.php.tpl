<?php
[{$oModule->renderFileComment()}]
[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]
[{assign var='iListClassNameLength' value=$sClassName|count_characters}]
[{math assign='iTruncateLength' equation='iListClassNameLength - 4' iListClassNameLength=$iListClassNameLength}]
[{assign var='sListObjectsClassName' value=$sClassName|truncate:$iTruncateLength:''}]
[{assign var='sTableName' value=$oModule->getModuleId()|cat:'_'}]
[{assign var='sTableName' value=$sTableName|cat:$sListObjectsClassName|lower}]
[{assign var='sFullClassName' value=$sClassNamePrefix|cat:$sClassName}]
[{assign var='sFullObjectName' value=$sClassNamePrefix|cat:$sListObjectsClassName}]

/**
 * Class [{$sFullClassName}].
 * [{$sFullObjectName}] list model.
 */
class [{$sFullClassName}] extends oxList
{

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = '[{$sFullObjectName}]';

[{if $oModule->renderSamples()}]

    // !!!EXAMPLES!!!
[{if $oModule->renderTasks()}]
    // TODO: Implement real loaders and delete the examples.

    // NOTE: List class assign loaded object ot itself, so use methods like:
    // $this->count / key / next / prev / clear / assign / getArray / getBaseObject / ...  and so on!
    // If You user objects list in other class, define it with /** @var [{$sFullClassName}][] $oMyObjectsList */
[{/if}]

    /**
     * Loads all entries from the table.
     */
    /* public function loadAll()
    {
[{if $oModule->renderTasks()}]
        // TODO: Create other loaders like this using `selectString` method.
        // NOTE: For MySQL queries user proper case, backticks (`) and keep it clear and readable.
[{/if}]
        $sSql = "SELECT * FROM " . getViewName('[{$sTableName}]');

        $this->selectString($sSql);
    } */
[{/if}]
}
