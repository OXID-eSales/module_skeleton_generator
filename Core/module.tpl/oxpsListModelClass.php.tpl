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
[{assign var='iListClassNameLength' value=$sClassName|count_characters}]
[{math assign='iTruncateLength' equation='iListClassNameLength - 4' iListClassNameLength=$iListClassNameLength}]
[{assign var='sListObjectsClassName' value=$sClassName|truncate:$iTruncateLength:''}]
[{assign var='sTableName' value=$oModule->getModuleId(false)|cat:'_'}]
[{assign var='sTableName' value=$sTableName|cat:$sListObjectsClassName|lower}]
[{assign var='sFullClassName' value=$sClassNamePrefix|cat:$sClassName}]
[{assign var='sFullObjectName' value=$sListObjectsClassName}]

namespace [{$sVendorPrefix}]\[{$sModuleName}]\[{$sNamespaceSuffix}];

use \OxidEsales\Eshop\Core\Model\ListModel;
use \OxidEsales\Eshop\Core\TableViewNameGenerator;
[{if $oModule->renderTasks()}]
// TODO: Define other classes to use in the list model, and delete not used.
[{/if}]

/**
 * Class [{$sClassName}].
 * [{$sFullObjectName}] list model.
 *
 * @see [{$sFullObjectName}]
 */
class [{$sClassName}] extends ListModel
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

    // NOTE: List class assign loaded objects ot itself, so use methods like:
    // $this->count / key / next / prev / clear / assign / getArray / getBaseObject / ...  and so on!
    // If You use objects list in other class, define it with /** @var [{$sFullClassName}][] $oMyObjectsList */
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
        /** @var TableViewNameGenerator $tableGenerator */
        /* $tableGenerator = \OxidEsales\Eshop\Core\Registry::get(TableViewNameGenerator::class);

        $query = "SELECT * FROM " . $tableGenerator->getViewName($this->getBaseObject()->getCoreTableName());

        $this->selectString($query);
    } */
[{/if}]
}
