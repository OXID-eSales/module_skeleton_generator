<?php
[{$oModule->setNewModuleData(true)}]
[{$oModule->renderFileComment()}]

[{assign var='sVendorPrefix' value=$oModule->getVendorPrefix()|ucfirst}]
[{assign var='sModuleName' value=$oModule->getModuleFolderName()}]
[{assign var='sModuleId' value=$oModule->getModuleId()}]
[{assign var='aExtendClasses' value=$oModule->getClassesToExtend()}]
[{if $aExtendClasses}]
/**
 * This file contains information about extended classes real parent classes.
 * NOTE: Never include it. That's just an information for IDE to link classes together.
 */
[{foreach from=$aExtendClasses key='sExtendClass' item='aClassData'}]

namespace [{$sVendorPrefix}]\[{$sModuleName}]\[{$oModule->getNamespaceSuffixFromPath($aClassData.classPath, false)}];
/**
 * Class [{$aClassData.v6ClassName}]_parent
 */
class [{$aClassData.v6ClassName}]_parent extends \[{$aClassData.v6Namespace}]\[{$aClassData.v6ClassName}]
{

}
[{/foreach}]
[{elseif $oModule->renderTasks()}]
// TODO: Delete this file if no classes are defined here.
[{/if}]
