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

class PluginLinesmanagerForward extends PluginLinesmanagerLine {

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
            'id' => array('name' => 'id', 'hidden' => true),
            'numplan' => array('name' => __("Number", "linesmanager"),
                'add' => false,
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerNumplan',
                    'condition' => $this->condition_to_load_numplan,
                    'filterUsedValues' => true,
                    'field_id' => 'id',
                    'filterUsedValues' => false,
                    'field_name' => 'number',
                    'field_tooltip' => 'number'
                )
            ),
            'category' => array(
                'name' => PluginLinesmanagerCategory::getTypeName(),
                //'default' => 4,
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerCategory',
                    'field_id' => 'id',
                    'field_name' => 'name',
                    'field_tooltip' => array('name', 'description')
                )
            ),
            'other' => array('name' => __("Others", "linesmanager")),
        );
    }

    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     * */
    static function getTypeName($nb = 1) {
        return _n('Forward', 'Forwards', $nb, 'linesmanager');
    }

    /**
     * Return the name.
     */
    private function getNameString($input) {
        $pieces = array();
        
        $piece = PluginLinesmanagerUtilform::getForeingkeyName($input['numplan'], $this->attributes['numplan']);
        if ($piece != "") {
            $piece = "Number: " . $piece;
            $pieces[] = $piece;
        }
        $piece = PluginLinesmanagerUtilform::getForeingkeyName($input['category'], $this->attributes['category']);
        if ($piece != "") {
            $piece = PluginLinesmanagerCategory::getTypeName() . ": " . $piece;
            $pieces[] = $piece;
        }
        $piece = $input['other'];
        if ($piece != "") {
            $piece = "Other: " . $piece;
            $pieces[] = $piece;
        }
        
        return implode(", ", $pieces);
    }


    /**
     * Prepare input datas for adding the item
     *
     * @param $input datas used to update the item
     *
     * @return the modified $input array
     */
    function prepareInputForAdd($input) {
        $input['name'] = $this->getNameString($input);
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
        $input['name'] = $this->getNameString($input);
        return $input;
    }

}
