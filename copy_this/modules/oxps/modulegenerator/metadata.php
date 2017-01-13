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

/**
 * Metadata version
 */
$sMetadataVersion = '1.3';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'oxpsmodulegenerator',
    'title'       => 'OXID Module Skeleton Generator',
    'description' => array(
        'de' => 'Die Erzeugung von Ordner-Struktur, leere Klassen und Metadata-Datei für neue OXID eShop Module',
        'en' => 'Folders structure, empty classes and metadata generation for new OXID eShop modules.',
    ),
    'thumbnail'   => 'out/pictures/oxps_module_generator.png',
    'version'     => '0.6.0',
    'author'      => 'OXID Professional Services',
    'url'         => 'http://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => array(
        \OxidEsales\EshopCommunity\Core\Module::class => 'oxps/ModuleGenerator/Core/oxpsModuleGeneratorOxModule',
    ),
    'files'       => array(
        'Admin_oxpsModuleGenerator'     => 'oxps/ModuleGenerator/Application/Controller/Admin/Admin_oxpsModuleGenerator.php',
        'oxpsModuleGeneratorFileSystem' => 'oxps/ModuleGenerator/Core/oxpsModuleGeneratorFileSystem.php',
        'oxpsModuleGeneratorHelper'     => 'oxps/ModuleGenerator/Core/oxpsModuleGeneratorHelper.php',
        'oxpsModuleGeneratorModule'     => 'oxps/ModuleGenerator/Core/oxpsModuleGeneratorModule.php',
        'oxpsModuleGeneratorRender'     => 'oxps/ModuleGenerator/Core/oxpsModuleGeneratorRender.php',
        'oxpsModuleGeneratorSettings'   => 'oxps/ModuleGenerator/Core/oxpsModuleGeneratorSettings.php',
        'oxpsModuleGeneratorValidator'  => 'oxps/ModuleGenerator/Core/oxpsModuleGeneratorValidator.php',
    ),
    'templates'   => array(
        'Admin_oxpsModuleGenerator.tpl' => 'oxps/ModuleGenerator/Application/views/admin/Admin_oxpsModuleGenerator.tpl',
    ),
    'settings'    => array(
        array(
            'group' => 'oxpsModuleGeneratorVendor',
            'name'  => 'oxpsModuleGeneratorVendorPrefix',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsModuleGeneratorVendor',
            'name'  => 'oxpsModuleGeneratorModuleAuthor',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsModuleGeneratorVendor',
            'name'  => 'oxpsModuleGeneratorAuthorLink',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsModuleGeneratorVendor',
            'name'  => 'oxpsModuleGeneratorAuthorMail',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsModuleGeneratorVendor',
            'name'  => 'oxpsModuleGeneratorCopyright',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsModuleGeneratorVendor',
            'name'  => 'oxpsModuleGeneratorComment',
            'type'  => 'arr',
            'value' => array()
        ),
    ),
    'events'      => array(
        'onActivate'   => 'oxpsModuleGeneratorModule::onActivate',
        'onDeactivate' => 'oxpsModuleGeneratorModule::onDeactivate',
    ),
);
