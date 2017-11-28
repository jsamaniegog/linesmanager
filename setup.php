<?php

/*
 * Copyright (C) 2016 Javier Samaniego García <jsamaniegog@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Init the hooks of the plugins -Needed
 * @global array $PLUGIN_HOOKS
 * @glpbal array $CFG_GLPI
 */
function plugin_init_linesmanager() {
    global $PLUGIN_HOOKS, $CFG_GLPI;

    include_once("inc/utilsetup.class.php");
    
    Plugin::registerClass('PluginLinesmanagerConfig', array('addtabon' => 'Config'));
    Plugin::registerClass('PluginLinesmanagerEntity', array('addtabon' => array('Entity')));
    Plugin::registerClass('PluginLinesmanagerProfile', array('addtabon' => array('Profile')));
    Plugin::registerClass('PluginLinesmanagerLine', array('addtabon' => PluginLinesmanagerUtilsetup::getAssets()));
    Plugin::registerClass('PluginLinesmanagerAlgorithm');
    Plugin::registerClass('PluginLinesmanagerUtilform');
    
    // to update location and state in lines
    foreach (PluginLinesmanagerUtilsetup::getAssets() as $asset) {
        $PLUGIN_HOOKS['item_update']['linesmanager'][$asset] = 'plugin_post_item_update_linesmanager';
        $PLUGIN_HOOKS['item_purge']['linesmanager'][$asset] = 'plugin_post_item_purge_linesmanager';
    }
    $PLUGIN_HOOKS['item_add']['linesmanager']['PluginLinesmanagerLine'] = 'plugin_post_item_add_linesmanager';
    
    // simcard plugin
    $PLUGIN_HOOKS['item_add']['linesmanager']['PluginSimcardSimcard_Item'] = 'plugin_item_add_linesmanager_PluginSimcardSimcard_Item';
    $PLUGIN_HOOKS['item_purge']['linesmanager']['PluginSimcardSimcard_Item'] = 'plugin_item_purge_linesmanager_PluginSimcardSimcard_Item';

    $PLUGIN_HOOKS['csrf_compliant']['linesmanager'] = true;
    
    $PLUGIN_HOOKS['webservices']['linesmanager'] = 'plugin_linesmanager_registerMethods';
}

/**
 * Fonction de définition de la version du plugin
 * @return type
 */
function plugin_version_linesmanager() {
    return array('name' => __('Lines Manager', 'linesmanager'),
        'version' => '0.4.0',
        'author' => 'Javier Samaniego',
        'license' => 'AGPLv3+',
        'homepage' => 'https://github.com/jsamaniegog/linesmanager',
        'minGlpiVersion' => '9.2');
}

/**
 * Fonction de vérification des prérequis
 * @return boolean
 */
function plugin_linesmanager_check_prerequisites() {
    if (version_compare(GLPI_VERSION, '0.90', 'lt')) {
        echo __('This plugin requires GLPI >= 0.90', 'linesmanager');
        return false;
    }

    return true;
}

/**
 * Fonction de vérification de la configuration initiale
 * Uninstall process for plugin : need to return true if succeeded
 * may display messages or add to message after redirect.
 * @param type $verbose
 * @return boolean
 */
function plugin_linesmanager_check_config($verbose = false) {
    // check here
    if (true) {
        return true;
    }

    if ($verbose) {
        echo __('Installed / not configured', 'linesmanager');
    }

    return false;
}

?>