<?php
[{$oModule->renderFileComment()}]

[{assign var='sVendorDir' value=$oModule->getVendorPrefix()}]
[{assign var='sModuleId' value=$oModule->getModuleId(false)}]
[{assign var='sModuleCamelCaseId' value=$oModule->getModuleId(true)}]
[{assign var='sModuleFolderName' value=$oModule->getModuleFolderName()}]
[{assign var='aNewClasses' value=$oModule->getClassesToCreate()}]
[{assign var='aControllersClasses' value=$oModule->getClassesToCreate('controllers', 'aClasses')}]
[{assign var='aWidgetsClasses' value=$oModule->getClassesToCreate('widgets', 'aClasses')}]
[{assign var='aExtendClasses' value=$oModule->getClassesToExtend()}]
[{assign var='aModuleBlocks' value=$oModule->getBlocks()}]
[{assign var='aModuleSettings' value=$oModule->getSettings()}]
[{assign var='aThemesList' value=$oModule->getThemesList()}]
/**
 * Metadata version
 */
$sMetadataVersion = '1.3';

/**
 * Module information
 */
[{if $oModule->renderTasks()}]/**
 * TODO: Replace sample names and paths (like '[ParentClassName]', '[your_template]', etc.) with real ones You need.
 * TODO: Uncomment lines You need, add more if needed, delete not required.
 * TODO: Remove all these TODO comment.
 * TODO: Check generated composer.json file on the module and adjust if needed.
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
        '[{$sExtendClass}]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[{$mApplicationPath}][{$sModuleCamelCaseId}][{$sExtendClass}]',
[{/foreach}]
[{/if}]
        [{if $oModule->renderSamples()}]//'[ParentClassName]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[appropriate_folder]/[{$sModuleCamelCaseId}][parent_class_name]',
[{/if}]
    ),
    'files'       => array(
        '[{$sModuleCamelCaseId}]Module' => '[{$sVendorDir}]/[{$sModuleFolderName}]/Core/[{$sModuleCamelCaseId}]Module.php',
[{if $aNewClasses}]
[{foreach from=$aNewClasses key='sObjectType' item='aClassesData'}]
[{assign var='aClasses' value=$aClassesData.aClasses}]
[{foreach from=$aClasses key='sClassKey' item='sClassName'}]
        '[{$sModuleCamelCaseId}][{$sClassName}]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[{$aClassesData.sInModulePath}][{$sModuleCamelCaseId}][{$sClassName}].php',
[{/foreach}]
[{/foreach}]
[{/if}]
[{if $oModule->renderSamples()}]
        //'[your_class_name]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[appropriate_folder]/[{$sModuleCamelCaseId}][your_class_name].php',
[{/if}]
),
    'templates'   => array(
[{foreach from=$aThemesList item='sThemeID'}]
[{if $sThemeID}]
[{assign var='sThemeSuffix' value='.'|cat:$sThemeID}]
      '[{$sThemeID}]' => array(
[{else}]
[{assign var='sThemeSuffix' value=''}]
[{/if}]
[{if $aControllersClasses}]
[{foreach from=$aControllersClasses item='sControllerClassName'}]
        '[{$sModuleCamelCaseId}][{$sControllerClassName}].tpl' => '[{$sVendorDir}]/[{$sModuleFolderName}]/Application/views/pages/[{$sModuleCamelCaseId}][{$sControllerClassName}][{$sThemeSuffix}].tpl',
[{/foreach}]
[{/if}]
[{if $aWidgetsClasses}]
[{foreach from=$aWidgetsClasses item='sWidgetClassName'}]
        '[{$sModuleCamelCaseId}][{$sWidgetClassName}].tpl' => '[{$sVendorDir}]/[{$sModuleFolderName}]/Application/views/widgets/[{$sModuleCamelCaseId}][{$sWidgetClassName}][{$sThemeSuffix}].tpl',
[{/foreach}]
[{/if}]
[{if $sThemeID}]
      ),
[{/if}]
[{/foreach}]
[{if $oModule->renderSamples()}]
        //'[your_template].tpl' => '[{$sVendorDir}]/[{$sModuleFolderName}]/Application/views/pages/[theme_folder_path]/[{$sModuleCamelCaseId}][your_template].tpl',
[{/if}]
    ),
    'blocks'      => array(
[{if $aModuleBlocks}]
[{foreach from=$aThemesList item='sThemeID'}]
[{if $sThemeID}]
[{assign var='sThemeSuffix' value='.'|cat:$sThemeID}]
[{else}]
[{assign var='sThemeSuffix' value=''}]
[{/if}]
[{foreach from=$aModuleBlocks item='aModuleBlock'}]
        array(
[{if $sThemeID}]
            'theme' => '[{$sThemeID}]',
[{/if}]
            'template'  => '[{$aModuleBlock->template}]',
            'block'  => '[{$aModuleBlock->block}]',
            'file' => '[{$aModuleBlock->file|replace:'.tpl':$sThemeSuffix|cat:'.tpl'}]',
        ),
[{/foreach}]
[{/foreach}]
[{elseif $oModule->renderSamples()}]
        /*array(
            'template' => '[theme_folder]/[theme_template].tpl',
            'block' => '[{$sModuleId}]_[your_block_name]',
            'file' => 'Application/views/blocks/[{$sModuleCamelCaseId}][your_block_name].tpl',
        ),*/
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
[{elseif $oModule->renderSamples()}]
        /*array(
            'group' => '[{$sModuleCamelCaseId}][SettingsGroup]',
            'name'  => '[{$sModuleCamelCaseId}][SettingName]',
[{if $oModule->renderTasks()}]
            //TODO: Change type to one You need: 'bool', 'str', 'num', 'arr', 'aarr', 'select'. Remove this comment.
[{/if}]
            'type'  => 'str',
            'value' => '[initial_setting_value]',
        ),*/
[{/if}]
    ),
    'events'      => array(
        'onActivate'   => '[{$oModule->getModuleClassName()}]Module::onActivate',
        'onDeactivate' => '[{$oModule->getModuleClassName()}]Module::onDeactivate',
    ),
);
