<?php

/*
 * Copyright (C) 2017 Javier Samaniego García <jsamaniegog@gmail.com>
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

class PluginLinesmanagerLinegroup extends PluginLinesmanagerLine {

    /**
     * Belongs to this tables. Needed for search used and delete.
     * @var type 
     */
    static public $belongsTo = array(
        "PluginLinesmanagerLine"
    );
    
    function __construct() {
        parent::__construct();

        $this->attributes = array(
            'id' => array('name' => 'ID', 'hidden' => true),
            'numplan' => array(
                'name' => __("Number", "linesmanager"),
                'mandatory' => true,
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerNumplan,
                    'condition' => $this->condition_to_load_numplan,
                    //'filterUsedValues' => true,
                    'field_id' => 'id',
                    'field_name' => 'number',
                    'field_tooltip' => 'number'
                )),
            'name' => array(
                'name' => __("Group name", "linesmanager"),
                //'readOnly' => true
            ),
            'algorithm' => array(
                'name' => __("Algorithm", "linesmanager"),
                'mandatory' => true,
                'default' => "1",
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerAlgorithm,
                    'field_id' => 'id',
                    'field_name' => 'name',
                    'field_tooltip' => 'description'
                )
            )
        );
    }

    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     * */
    static function getTypeName($nb = 1) {
        return _n('Line group', 'Line groups', $nb, 'linesmanager');
    }

    /**
     * 
     * @param type $item
     * @param type $line
     */
    /*public function showFormTab($item, $line) {
        // this prints a select box and div tag to load when a element is seleted
        $this->showSelectLines("linegroup", $item);

        // show table records list
        $this->showLineGroupsList($item);

        if (self::canUpdate())
            Html::closeForm();
    }*/

    /**
     * Show table list of lines. This method don't start and end the form.
     */
    /*public function showLineGroupsList($item) {
        PluginLinesmanagerUtilform::showHtmlList(
            "table_linegroups", $this, "line IN (SELECT id FROM " . PluginLinesmanagerLine::getTable() . " "
            . "WHERE itemtype = '" . $item->getType() . "' "
            . " AND items_id = " . $item->getID() . ")"
        );
    }*/

}
