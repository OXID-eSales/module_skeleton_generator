<?php
[{$oModule->renderFileComment()}]

[{assign var='sModuleId' value=$oModule->getModuleId()}]
[{assign var='aExtendClasses' value=$oModule->getClassesToExtend()}]
[{if $aExtendClasses}]
/**
 * This file contains information about extended classes real parent classes.
 * NOTE: Never include it. That's just an information for IDE to link classes together.
 */
[{foreach from=$aExtendClasses key='sExtendClass' item='mApplicationPath'}]


/**
 * Class [{$sModuleId}][{$sExtendClass}]_parent
 */
class [{$sModuleId}][{$sExtendClass}]_parent extends [{$sExtendClass}]
{

}
[{/foreach}]
[{elseif $oModule->renderTasks()}]
// TODO: Delete this file if no classes are defined here.
[{/if}]
