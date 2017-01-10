<?php
[{$oModule->renderFileComment()}]

[{assign var='sVendorDir' value=$oModule->getVendorPrefix()}]
[{assign var='sModuleId' value=$oModule->getModuleId()}]
[{assign var='sModuleCamelCaseId' value=$oModule->getModuleId(true)}]
[{assign var='sModuleFolderName' value=$oModule->getModuleFolderName()}]
[{assign var='aNewClasses' value=$oModule->getClassesToCreate()}]
[{assign var='aControllersClasses' value=$oModule->getClassesToCreate('controllers', 'aClasses')}]
[{assign var='aWidgetsClasses' value=$oModule->getClassesToCreate('widgets', 'aClasses')}]
[{assign var='aExtendClasses' value=$oModule->getClassesToExtend()}]
[{assign var='aModuleBlocks' value=$oModule->getBlocks()}]
[{assign var='aModuleSettings' value=$oModule->getSettings()}]
/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
[{if $oModule->renderTasks()}]/**
 * TODO: Replace sample names and paths (like '[ParentClassName]', '[your_template]', etc.) with real ones You need.
 * TODO: Uncomment lines You need, add more if needed, delete not required.
 * TODO: Remove all this TODO comment.
 */
[{/if}]
$aModule = array(
    'id'          => '[{$sModuleId}]',
    'title'       => array(
        'de' => '[TR - [{$oModule->getTitle()}]]',
        'en' => '[{$oModule->getTitle()}]',
    ),
    'description' => array(
        'de' => '[TR - [{$oModule->getDescription()}]]',
        'en' => '[{$oModule->getDescription()}]',
    ),
    'thumbnail'   => 'out/pictures/picture.png',
    'version'     => '[{$oModule->getInitialVersion()}]',
    'author'      => '[{$oModule->getAuthorData('name')}]',
    'url'         => '[{$oModule->getAuthorData('link')}]',
    'email'       => '[{$oModule->getAuthorData('mail')}]',
    'extend'      => array(
[{if $aExtendClasses}]
[{foreach from=$aExtendClasses key='sExtendClass' item='mApplicationPath'}]
        '[{$sExtendClass}]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[{$mApplicationPath}][{$sModuleId}][{$sExtendClass}]',
[{/foreach}]
[{/if}]
        [{if $oModule->renderSamples()}]//'[ParentClassName]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[appropriate_folder]/[{$sModuleId}][parent_class_name]',
[{/if}]
    ),
    'files'       => array(
        '[{$sModuleId}]module' => '[{$sVendorDir}]/[{$sModuleFolderName}]/core/[{$sModuleId}]module.php',
[{if $aNewClasses}]
[{foreach from=$aNewClasses key='sObjectType' item='aClassesData'}]
[{assign var='aClasses' value=$aClassesData.aClasses}]
[{foreach from=$aClasses key='sClassKey' item='sClassName'}]
        '[{$sModuleId}][{$sClassName|lower}]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[{$aClassesData.sInModulePath}][{$sModuleId}][{$sClassName|lower}].php',
[{/foreach}]
[{/foreach}]
[{/if}]
[{if $oModule->renderSamples()}]
        //'[your_class_name]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[appropriate_folder]/[{$sModuleId}][your_class_name].php',
[{/if}]
),
    'templates'   => array(
[{if $aControllersClasses}]
[{foreach from=$aControllersClasses item='sControllerClassName'}]
        '[{$sModuleId}][{$sControllerClassName|lower}].tpl' => '[{$sVendorDir}]/[{$sModuleFolderName}]/views/pages/[{$sModuleId}][{$sControllerClassName|lower}].tpl',
[{/foreach}]
[{/if}]
[{if $aWidgetsClasses}]
[{foreach from=$aWidgetsClasses item='sWidgetClassName'}]
        '[{$sModuleId}][{$sWidgetClassName|lower}].tpl' => '[{$sVendorDir}]/[{$sModuleFolderName}]/views/widgets/[{$sModuleId}][{$sWidgetClassName|lower}].tpl',
[{/foreach}]
[{/if}]
[{if $oModule->renderSamples()}]
        //'[your_template].tpl' => '[{$sVendorDir}]/[{$sModuleFolderName}]/views/pages/[theme_folder_path]/[{$sModuleId}][your_template].tpl',
[{/if}]
),
    'blocks'      => array(
[{if $aModuleBlocks}]
    [{foreach from=$aModuleBlocks item='aModuleBlock'}]
    array(
            'template'  => '[{$aModuleBlock->template}]',
            'block'  => '[{$aModuleBlock->block}]',
            'file' => '[{$aModuleBlock->file}]',
        ),
    [{/foreach}]
[{else}]
        [{if $oModule->renderSamples()}]/*array(
            'template' => '[theme_folder]/[theme_template].tpl',
            'block' => '[{$sModuleId}]_[your_block_name]',
            'file' => 'views/blocks/[{$sModuleId}][your_block_name].tpl',
        ),*/
[{/if}]
[{/if}]
    ),
    'settings'    => array(
[{if $aModuleSettings}]
    [{foreach from=$aModuleSettings key='iSettingKey' item='aModuleSetting'}]
    array(
            'group' => '[{$sModuleCamelCaseId}]Settings',
            'name'  => '[{$sModuleCamelCaseId}][{$aModuleSetting->name}]',
            'type'  => '[{$aModuleSetting->type}]',
            'value' => [{$aModuleSetting->value}],
[{if $aModuleSetting->type eq 'select'}]
            'constrains' => [{$aModuleSetting->constrains}],
[{/if}]
        ),
    [{/foreach}]
[{else}]
        [{if $oModule->renderSamples()}]/*array(
            'group' => '[{$sModuleCamelCaseId}][SettingsGroup]',
            'name'  => '[{$sModuleCamelCaseId}][SettingName]',
[{if $oModule->renderTasks()}]
            //TODO: Change type to one You need: 'bool', 'str', 'num', 'arr', 'aarr', 'select'. Remove this comment.
[{/if}]
            'type'  => 'str',
            'value' => '[initial_setting_value]',
        ),*/
[{/if}]
[{/if}]
),
    'events'      => array(
        'onActivate'   => '[{$oModule->getModuleClassName()}]Module::onActivate',
        'onDeactivate' => '[{$oModule->getModuleClassName()}]Module::onDeactivate',
    ),
);
