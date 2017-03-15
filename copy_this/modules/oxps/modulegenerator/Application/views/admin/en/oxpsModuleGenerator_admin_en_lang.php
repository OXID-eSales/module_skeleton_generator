<?php
/**
 * This file is part of OXID Module Skeleton Generator module.
 *
 * OXID Module Skeleton Generator module is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * OXID Module Skeleton Generator module is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Module Skeleton Generator module.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @category      module
 * @package       ModuleGenerator
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

$sLangName = 'English';

$aLang = array(
    'charset'                                                  => 'UTF-8',
    'oxpsmodulegenerator'                                      => 'Module Generator',
    'oxpsmodulegeneratormodule'                                => 'Wizard',
    'OXPS_MODULEGENERATOR_ADMIN_TITLE'                         => 'Module Skeleton Generator',
    'OXPS_MODULEGENERATOR_ADMIN_EDIT_MODE'                     => 'Edit Mode Activated',

    // Settings
    'SHOP_MODULE_GROUP_oxpsModuleGeneratorVendor'              => 'Vendor and Copyright Settings',
    'SHOP_MODULE_oxpsModuleGeneratorVendorPrefix'              => 'Vendor prefix used for module generation. It should contain two to four lowercase latin letters, e.g. "abc"',
    'SHOP_MODULE_oxpsModuleGeneratorModuleAuthor'              => 'Module authors\' name. Company or developer full name.',
    'SHOP_MODULE_oxpsModuleGeneratorAuthorLink'                => 'Module authors\' website URL.',
    'SHOP_MODULE_oxpsModuleGeneratorAuthorMail'                => 'Module authors\' contact email.',
    'SHOP_MODULE_oxpsModuleGeneratorCopyright'                 => 'Module copyright part that goes before current year. For example: "My Company, 2001-"',
    'SHOP_MODULE_oxpsModuleGeneratorComment'                   => 'File comment text used in PHP files. Start each line with an asterisk: `*`.<br/>' .
                                                                  'Normally it should contain legal notices and short license info.',

    // Module generation form
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_NAME'                   => 'Module name',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_NAME_HINT'              => 'Enter short, capitalized camel case name of new module, e.g. "MyModule"',
    'OXPS_MODULEGENERATOR_ADMIN_RENDER_TASKS'                  => 'Learning hints',
    'OXPS_MODULEGENERATOR_ADMIN_RENDER_TASKS_HINT'             => 'Renders To Do comments, explanations and useful hints for learning.',
    'OXPS_MODULEGENERATOR_ADMIN_RENDER_SAMPLES'                => 'Create examples',
    'OXPS_MODULEGENERATOR_ADMIN_RENDER_SAMPLES_HINT'           => 'Renders commented sample content to make development faster and remind about each class features.',
    'OXPS_MODULEGENERATOR_ADMIN_OVERRIDE_CLASSES'              => 'Extend classes',
    'OXPS_MODULEGENERATOR_ADMIN_OVERRIDE_CLASSES_HINT'         => 'List classes to overload (extend), each from new line, e.g. "oxArticle"',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_CONTROLLERS'            => 'Create controllers',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_CONTROLLERS_HINT'       => 'Enter capitalized camel case names each from new line, e.g. "MyController"',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_MODELS'                 => 'Create models',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_MODELS_HINT'            => 'Enter capitalized camel case names each from new line, e.g. "MyItemModel"',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_LISTS'                  => 'Create lists',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_LISTS_HINT'             => 'Repeat item models names, to create list models, e.g. "MyItemModel"',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_WIDGETS'                => 'Create widgets',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_WIDGETS_HINT'           => 'Enter capitalized camel case names each from new line, e.g. "MyWidget"',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_BLOCKS'                 => 'Create blocks',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_BLOCKS_HINT'            => 'Enter block name and template path, both lowercase, separated by "@", each pair from new line, e.g. "details_productmain_title@page/details/inc/productmain.tpl"',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTINGS'               => 'Create module settings',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_NAME'           => 'Name',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_TYPE'           => 'Type',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_VALUE'          => 'Default value',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_SETTINGS_HINT'          => 'Enter initial values like in examples: Checkbox - "0" or "1", String - "my value", Number - "7.88", for other types - each value from new line.<br/>' .
                                                                  'NOTE: Older eShop versions don\'t support "Number" setting type.',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES'                 => 'Multi-theme support',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES_HINT'            => 'If a list on theme IDs is specified, multi-theme feature will be used to create separate templates for each theme.' .
                                                                  'Else standard functionality is used - same templates for all themes.',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES_NONE'            => 'No theme specific templates (standard)',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES_OR'              => 'OR',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES_LIST'            => 'Define theme IDs for module multi-theme template',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_INIT_VERSION'           => 'Initial module version',
    'OXPS_MODULEGENERATOR_ADMIN_HAS_ADMINISTRATION'            => 'Has admin interface',
    'OXPS_MODULEGENERATOR_ADMIN_HAS_ADMINISTRATION_HINT'       => 'Select this option if module requires administration interface.',
    'OXPS_MODULEGENERATOR_ADMIN_CHECKOUT_UNIT_TESTS'           => 'Generate Unit tests',
    'OXPS_MODULEGENERATOR_ADMIN_CHECKOUT_UNIT_TESTS_HINT'      => 'Create empty test class for each generated module class with configured relations and proper naming.',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_GENERATE'               => 'Generate Module',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_ADD_SETTINGS_LINE'      => 'Add New Line',

    // Module generation form errors and messages
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_NO_VENDOR'        => 'Attention! Vendor or author parameters are not configured.<br/>' .
                                                                  'Please open <i>Extensions -> Modules -> OXID Module Skeleton Generator -> Settings -> Vendor and Copyright Settings</i> ' .
                                                                  'and enter Your vendor, copyright and author data.',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_VENDOR'   => 'ERROR! Module vendor prefix configured in settings is invalid. It should contain two to four lowercase latin letters.',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_NAME'     => 'ERROR! Module name is invalid. Use unique capitalized camel case name, e.g. "MyModule"',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS' => 'Success! New module have been generated! Please check in <i>Extensions -> Modules</i> and the source code in Your vendor sub-folder.',

    // Module generation form JavaScript notifications
    'OXPS_MODULEGENERATOR_JS_NOTIFICATION_SUCCESS'             => 'SUCCESS: Entered value is valid!',
    'OXPS_MODULEGENERATOR_JS_NOTIFICATION_ERROR'               => 'ERROR: Entered value is invalid!',
    'OXPS_MODULEGENERATOR_JS_NOTIFICATION_WARNING'             => 'WARNING: Entered value is invalid!',
    'OXPS_MODULEGENERATOR_JS_NOTIFICATION_VALID_CLASSES'       => 'INFO: Valid classes: ',
);
