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
 * @package       modulegenerator
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

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
    'thumbnail'   => 'out/pictures/oxpsmodulegenerator.png',
    'version'     => '0.6.0',
    'author'      => 'OXID Professional Services',
    'url'         => 'http://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => array(
        'oxmodule' => 'oxps/modulegenerator/core/oxpsmodulegeneratoroxmodule',
    ),
    'files'       => array(
        'admin_oxpsmodulegenerator'     => 'oxps/modulegenerator/controllers/admin/admin_oxpsmodulegenerator.php',
        'oxpsmodulegeneratorfilesystem' => 'oxps/modulegenerator/core/oxpsmodulegeneratorfilesystem.php',
        'oxpsmodulegeneratorhelper'     => 'oxps/modulegenerator/core/oxpsmodulegeneratorhelper.php',
        'oxpsmodulegeneratormodule'     => 'oxps/modulegenerator/core/oxpsmodulegeneratormodule.php',
        'oxpsmodulegeneratorrender'     => 'oxps/modulegenerator/core/oxpsmodulegeneratorrender.php',
        'oxpsmodulegeneratorsettings'   => 'oxps/modulegenerator/core/oxpsmodulegeneratorsettings.php',
        'oxpsmodulegeneratorvalidator'  => 'oxps/modulegenerator/core/oxpsmodulegeneratorvalidator.php',
    ),
    'templates'   => array(
        'admin_oxpsmodulegenerator.tpl' => 'oxps/modulegenerator/views/admin/admin_oxpsmodulegenerator.tpl',
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
        array(
            'group' => 'oxpsModuleGeneratorTests',
            'name'  => 'oxpsModuleGeneratorTestsGitUrl',
            'type'  => 'str',
            'value' => '',
        ),
    ),
    'events'      => array(
        'onActivate'   => 'oxpsModuleGeneratorModule::onActivate',
        'onDeactivate' => 'oxpsModuleGeneratorModule::onDeactivate',
    ),
);
