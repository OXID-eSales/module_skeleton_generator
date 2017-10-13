<?php
[{$oModule->setNewModuleData(true)}]
[{$oModule->renderFileComment()}]

[{assign var='sVendorDir' value=$oModule->getVendorPrefix()}]
[{assign var='sModuleId' value=$oModule->getModuleId(false)}]
[{assign var='sModuleCamelCaseId' value=$oModule->getModuleId(true)}]
[{assign var='sModuleCamelCaseName' value=$oModule->getModuleFolderName()}]
[{assign var='sModuleFolderName' value=$oModule->getModuleFolderName()}]
[{assign var='aNewClasses' value=$oModule->getClassesToCreate()}]
[{assign var='aControllersClasses' value=$oModule->getClassesToCreate('controllers', 'aClasses')}]
[{assign var='aControllerNamespace' value=$oModule->getNamespaceSuffixFromPath($oModule->getClassesToCreate('controllers', 'sInModulePath'), false)}]
[{assign var='aWidgetsClasses' value=$oModule->getClassesToCreate('widgets', 'aClasses')}]
[{assign var='aExtendClasses' value=$oModule->getClassesToExtend()}]
[{assign var='aModuleBlocks' value=$oModule->getBlocks()}]
[{assign var='aModuleSettings' value=$oModule->getSettings()}]
[{assign var='aThemesList' value=$oModule->getThemesList()}]
/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
[{if $oModule->renderTasks()}]/**
 * TODO: Replace sample names and paths (like '[ParentClassName]', '[your_template]', etc.) with real ones You need.
 * TODO: Uncomment lines You need, add more if needed, delete not required.
 * TODO: Remove all these TODO comments.
 * TODO: Check generated composer.json file on the module and adjust if needed.
 */
[{/if}]
$aModule = array(
    'id'          => '[{$sModuleId}]',
    'title'       => array(
        'de' => '[TR - [{$oModule->getModuleTitle()}]]',
        'en' => '[{$oModule->getModuleTitle()}]',
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
[{foreach from=$aExtendClasses key='sLegacyClass' item='aV6ClassData'}]
        \[{$aV6ClassData.v6Namespace}]\[{$aV6ClassData.v6ClassName}]::class => [{$sVendorDir|ucfirst}]\[{$sModuleFolderName}]\[{$aV6ClassData.classPath|replace:"/":"\\"}][{$aV6ClassData.v6ClassName}]::class,
[{/foreach}]
[{/if}]
        [{if $oModule->renderSamples()}]//'[ParentClassName]' => '[{$sVendorDir}]/[{$sModuleFolderName}]/[appropriate_folder]/[{$sModuleCamelCaseId}][parent_class_name]',
[{/if}]
    ),
    'controllers' => array(
[{if $aControllersClasses}]
    [{foreach from=$aControllersClasses item='sControllerClassName'}]
    '[{$sVendorDir|lower}]_[{$sModuleFolderName|lower}]_[{$sControllerClassName|lower}]' => [{$sVendorDir|ucfirst}]\[{$sModuleFolderName}]\[{$aControllerNamespace}]\[{$sControllerClassName}]::class,
    [{/foreach}]
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
        'onActivate'   => '[{$oModule->getModuleFolderName()}]Module::onActivate',
        'onDeactivate' => '[{$oModule->getModuleFolderName()}]Module::onDeactivate',
    ),
);
