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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginLinesmanagerConfig extends CommonDBTM {

    static function getTypeName($nb = 0) {
        return __("Lines Manager", "linesmanager");
    }

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        if (!$withtemplate) {
            if ($item->getType() == 'Config') {
                return __("Lines Manager", "linesmanager");
            }
        }
        return '';
    }

    function showFormLinesmanager() {
        global $CFG_GLPI;
        if (!Session::haveRight("config", UPDATE)) {
            return false;
        }

        $config_data = self::getConfigData();
        
        echo "<form name='form' action=\"" . Toolbox::getItemTypeFormURL('PluginLinesmanagerConfig') . "\" method='post'>";
        echo "<div class='center' id='tabsbody'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr><th colspan='4'>" . __("Lines Manager", "linesmanager") . "</th></tr>";
        
        echo "<tr class='tab_bg_2'>";
        echo "<td>" . __('Automate description based on name and surname: ', 'linesmanager') . "</td>";
        echo "<td colspan='3'>";
        Dropdown::showYesNo('automate_description', $config_data['automate_description']);
        echo "</td></tr>";
        
        echo "<tr class='tab_bg_2'>";
        echo "<td>" . __('Automate user id based on entity and number: ', 'linesmanager') . "</td>";
        echo "<td colspan='3'>";
        Dropdown::showYesNo('automate_user_id', $config_data['automate_user_id']);
        echo "</td></tr>";
        
        echo "<tr class='tab_bg_2'>";
        echo "<td>" . __('Fill contact information of the asset (the fields alternate username and number): ', 'linesmanager') . "</td>";
        echo "<td colspan='3'>";
        Dropdown::showYesNo('fill_contact_information', $config_data['fill_contact_information']);
        echo "</td></tr>";
        
        echo "<tr class='tab_bg_2'>";
        echo "<td>" . __('Fill line information (name, surname and description) with data of the selected number: ', 'linesmanager') . "</td>";
        echo "<td colspan='3'>";
        Dropdown::showYesNo('fill_line_information', $config_data['fill_line_information']);
        echo "</td></tr>";
        
        echo "</table></div>";
        
        echo "<input type='submit' name='update' class='submit' value=\"" . _sx('button', 'Save') . "\">";
        
        Html::closeForm();
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        if ($item->getType() == 'Config') {
            $config = new self();
            $config->showFormLinesmanager();
        }
    }

    /**
     * Returns an array with the config.
     * @global type $DB
     */
    public static function getConfigData() {
        global $DB;
        $config = new PluginLinesmanagerConfig();
        foreach ($config->find() as $v) {
            $config_data[$v['type']] = $v['value'];
        }
        return $config_data;
    }

    public function setConfig($datas) {
        try {
            $this->updateConfig($datas, 'automate_description');
            $this->updateConfig($datas, 'automate_user_id');
            $this->updateConfig($datas, 'fill_contact_information');
            $this->updateConfig($datas, 'fill_line_information');
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function updateConfig($datas, $type) {
        global $DB;
        
        $query  = "UPDATE glpi_plugin_linesmanager_configs SET ";
        $query .= "value = '" . $datas[$type] . "' ";
        $query .= "WHERE type = '$type'";
            
        if (!$DB->query($query)) {
            throw new Exception("Error al actualizar");
        }
    }
    
}

?>