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
 * @copyright (C) OXID eSales AG 2003-2014
 */

$sLangName = 'Deutsch';

$aLang = array(
    'charset'                                                  => 'UTF-8',
    'oxpsmodulegenerator'                                      => 'Modul Generator',
    'oxpsmodulegeneratormodule'                                => 'Wizard',
    'OXPS_MODULEGENERATOR_ADMIN_TITLE'                         => 'Modul Skeleton Generator',

    // Settings
    'SHOP_MODULE_GROUP_oxpsModuleGeneratorVendor'              => 'Hersteller und Copyright Einstellungen',
    'SHOP_MODULE_oxpsModuleGeneratorVendorPrefix'              => 'Hersteller-Präfix für das neue Modul. Es sollte ab zwei bis vier Klein lateinische Buchstaben enthalten, z.B. "abc"',
    'SHOP_MODULE_oxpsModuleGeneratorModuleAuthor'              => 'Name von Unternehmen oder Entwickler',
    'SHOP_MODULE_oxpsModuleGeneratorAuthorLink'                => 'Website URL des Modulauthors',
    'SHOP_MODULE_oxpsModuleGeneratorAuthorMail'                => 'E-Mail-Adresse des Modulauthors',
    'SHOP_MODULE_oxpsModuleGeneratorCopyright'                 => 'Copyright bis zum aktuellen Jahr. z.B. "My Company, 2001-"',
    'SHOP_MODULE_oxpsModuleGeneratorComment'                   => 'Kommentar-Text für PHP Dateien. Jede Zeile beginnt mit *<br/>' .
                                                                  'Normalerweise sollte es Hinweise und Kurz Lizenz Info stehen',

    'SHOP_MODULE_GROUP_oxpsModuleGeneratorTests'               => 'Module Tests Einstellungen',
    'SHOP_MODULE_oxpsModuleGeneratorTestsGitUrl'               => 'URL eines entfernten GIT-Repository für Tests-Ordner für generierte Module zu klonen',

    // Module generation form
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_NAME'                   => 'Modul Name',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_NAME_HINT'              => 'Kurzbezeichnung des Moduls (Großbuchstaben, camel case, z.B. "MyModule")',
    'OXPS_MODULEGENERATOR_ADMIN_RENDER_TASKS'                  => 'Lernhinweise',
    'OXPS_MODULEGENERATOR_ADMIN_RENDER_TASKS_HINT'             => 'Hilfreiche Hinweise, die den Lernprozess unterstützen.',
    'OXPS_MODULEGENERATOR_ADMIN_RENDER_SAMPLES'                => 'Beispiel erstellen',
    'OXPS_MODULEGENERATOR_ADMIN_RENDER_SAMPLES_HINT'           => 'Kommentierten Beispielinhalt erstellen um die Entwicklung zu beschleunigen und an jede Klassenfunktionalität zu erinnern.',
    'OXPS_MODULEGENERATOR_ADMIN_OVERRIDE_CLASSES'              => 'Erweiterte Klassen',
    'OXPS_MODULEGENERATOR_ADMIN_OVERRIDE_CLASSES_HINT'         => 'Liste aller zu erweiternder Klassen, eine pro Zeile z.B. "oxarticle"',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_CONTROLLERS'            => 'Controller erstellen',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_CONTROLLERS_HINT'       => 'Einen Controller Namen pro Zeile (Großbuchstaben, camel case z.B. "MyController")',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_MODELS'                 => 'Models erstellen',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_MODELS_HINT'            => 'Einen Model Namen pro Zeile (Großbuchstaben, camel case z.B. "MyItemModel")',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_LISTS'                  => 'Listen erstellen',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_LISTS_HINT'             => 'Wiederhole die Namen der Modelle um eine Liste zu generieren, z.B. "MyItemModel"',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_WIDGETS'                => 'Widgets erstellen',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_WIDGETS_HINT'           => 'Einen Widget pro Zeile (Großbuchstaben, camel case z.B. "MyWidget")',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_BLOCKS'                 => 'Blocks erstellen',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_BLOCKS_HINT'            => 'Einen Block pro Zeile (Name und Template-Pfad, kleingeschrieben und durch "@" getrennt, ein Paar pro Zeile z.B. "details_productmain_title@page/details/inc/productmain.tpl")',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTINGS'               => 'Modul Settings erstellen',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_NAME'           => 'Name',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_TYPE'           => 'Typ',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_VALUE'          => 'Standardwert',
    'OXPS_MODULEGENERATOR_ADMIN_CREATE_SETTINGS_HINT'          => 'Initialwert (z.B. Checkbox - "0" or "1", String - "my value", Nummer - "7.88", usw. - Ein Wert pro Zeile.<br/>' .
                                                                  'Alte Shop Versionen unterstützen nicht den Einstellungs-Typ "Number"',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_INIT_VERSION'           => 'Initiale Modulversion',
    'OXPS_MODULEGENERATOR_ADMIN_HAS_ADMINISTRATION'            => 'Adminoberfläche erstellen',
    'OXPS_MODULEGENERATOR_ADMIN_HAS_ADMINISTRATION_HINT'       => 'Gibt an ob das Modul eine Adminoberfläche benötigt.',
    'OXPS_MODULEGENERATOR_ADMIN_CHECKOUT_UNIT_TESTS'           => 'Ckeckout Unit tests',
    'OXPS_MODULEGENERATOR_ADMIN_CHECKOUT_UNIT_TESTS_HINT'      => 'Klone den neuesten Generic-Test-Ordner aus dem GIT. Anmerkung: Die Umgebung sollte entsprechend konfiguriert sein!',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_GENERATE'               => 'Neues Modul anlegen',

    // Module generation form errors and messages
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_NO_VENDOR'        => 'Achtung! Hersteller oder Autor Parameter sind nicht konfiguriert.<br/>' .
                                                                  'Bitte öffnen Sie <i>Erweiterungen -> Module -> OXID Module Skeleton Generator -> Einstell. -> Hersteller und Copyright Einstellungen</i> ' .
                                                                  'und geben Sie Hersteller, Copyright und Autor an.',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_VENDOR'   => 'ERROR! Hersteller-Präfix ist ungültig. Es sollte aus zwei bis vier lateinischen Kleinbuchstaben bestehen.',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_ERROR_INVALID_NAME'     => 'ERROR! Module Name ist ungültig oder existiert bereits. Bitte nur eindeutige Großbuchstaben (camel case) verwenden, z.B. "MyModule"',
    'OXPS_MODULEGENERATOR_ADMIN_MODULE_MSG_GENERATION_SUCCESS' => 'Ihr Modul wurde erfolgreich generiert! Bitte prüfen Sie in <i>Erweiterungen -> Module</i> und den Source-Code in Ihrem Hersteller-Verzeichnis.'
);
