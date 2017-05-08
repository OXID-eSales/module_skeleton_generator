<?php
[{$oModule->renderFileComment()}]

[{assign var='sModuleCamelCaseId' value=$oModule->getModuleId(true)}]
[{assign var='aModuleSettings' value=$oModule->getSettings()}]

$sLangName = 'English';

$aLang = array(
    'charset' => 'UTF-8',
    '[{$oModule->getModuleId(false)}]' => '[{$oModule->getTitle()}]',

[{if $aModuleSettings}]
    // Settings interface translations
[{if $oModule->renderTasks()}]
    // TODO: Adjust the settings translation if needed.
[{/if}]
    'SHOP_MODULE_GROUP_[{$sModuleCamelCaseId}]Settings' => '[{$oModule->getTitle()}] Module Settings',
[{foreach from=$aModuleSettings key='iSettingKey' item='aModuleSetting'}]
    'SHOP_MODULE_[{$sModuleCamelCaseId}][{$aModuleSetting->name}]' => '[{$oModule->camelCaseToHumanReadable($aModuleSetting->name)}]',
[{if $aModuleSetting->type eq 'select'}]
    'SHOP_MODULE_[{$sModuleCamelCaseId}][{$aModuleSetting->name}]_' => ' - ',
[{assign var='aSelectOptions' value=$oModule->getSelectSettingOptions($aModuleSetting->constrains)}]
[{foreach from=$aSelectOptions item='sOption'}]
    'SHOP_MODULE_[{$sModuleCamelCaseId}][{$aModuleSetting->name}]_[{$sOption}]' => '[{$sOption}]',
[{/foreach}]
[{/if}]
[{/foreach}]
[{/if}]

[{if $oModule->renderTasks()}]
    // TODO: Follow this pattern to add more translation. Delete this comment.
[{/if}]
[{if $oModule->renderSamples()}]
    //'[{$oModule->getVendorPrefix(true)}]_[{$oModule->getModuleFolderName(true)}]_ADMIN_[KEY]' => 'Value',
[{/if}]
);
