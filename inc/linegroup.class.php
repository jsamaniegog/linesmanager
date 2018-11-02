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
                    'item' => 'PluginLinesmanagerNumplan',
                    'condition' => $this->condition_to_load_numplan,
                    'filterUsedValues' => true,
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
                    'item' => 'PluginLinesmanagerAlgorithm',
                    'field_id' => 'id',
                    'field_name' => 'name',
                    'field_tooltip' => 'description'
                )
            ),
            'forward' => array(
                'name' => PluginLinesmanagerForward::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerForward',
                    'field_id' => 'id',
                    'field_name' => array('numplan', 'category', 'other'),
                    'field_tooltip' => array('numplan', 'category', 'other'),
                    'string_format' => array('category' => PluginLinesmanagerCategory::getTypeName() . ' %s')
                )
            ),
            'forwardtimeout' => array(
                'name' => __("Forward timeout", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'time',
                'minutes' => false,
                'hours' => false
            ),
            'timeslot' => array(
                'name' => PluginLinesmanagerTimeslot::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerTimeslot',
                    'field_id' => 'id',
                    'field_name' => array('timeperiod', 'category'),
                    'field_tooltip' => array('description', 'timeperiod', 'category'),
                    'string_format' => array('category' => PluginLinesmanagerCategory::getTypeName() . ' %s')
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
