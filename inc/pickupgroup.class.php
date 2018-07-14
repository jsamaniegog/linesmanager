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

class PluginLinesmanagerPickupgroup extends PluginLinesmanagerLine {

    /**
     * Belongs to this tables. Needed for search used and delete.
     * @var type 
     */
    static public $belongsTo = array(
        "PluginLinesmanagerLine"
    );
    
    function __construct() {
        parent::__construct();

        $condition_to_load_numplan = str_replace("only_pickup=0", "only_pickup=1", $this->condition_to_load_numplan);
        
        $this->attributes = array(
            'id' => array('name' => 'ID', 'hidden' => true),
            'numplan' => array(
                'name' => __("Number", "linesmanager"),
                'mandatory' => true,
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerNumplan',
                    'condition' => $condition_to_load_numplan,
                    'filterUsedValues' => true,
                    'field_id' => 'id',
                    'field_name' => 'number',
                    'field_tooltip' => 'number'
                )),
            'name' => array('name' => __("Pickup group name", "linesmanager"))
        );
    }

    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     * */
    static function getTypeName($nb = 1) {
        return _n('Pickup group', 'Pickup groups', $nb, 'linesmanager');
    }

    /**
     * Prepare input datas for adding the item
     *
     * @param $input datas used to update the item
     *
     * @return the modified $input array
     */
    function prepareInputForAdd($input) {
        if ($input['name'] == "") {
            $input['name'] = PluginLinesmanagerUtilform::getForeingkeyName($input['numplan'], $this->attributes['numplan']);
        }
        return $input;
    }

    /**
     * Prepare input datas for updating the item
     *
     * @param $input datas used to update the item
     *
     * @return the modified $input array
     * */
    function prepareInputForUpdate($input) {
        if ($input['name'] == "") {
            $input['name'] = PluginLinesmanagerUtilform::getForeingkeyName($input['numplan'], $this->attributes['numplan']);
        }
        
        return $input;
    }

}
