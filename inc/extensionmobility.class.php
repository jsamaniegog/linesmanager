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

class PluginLinesmanagerExtensionmobility extends PluginLinesmanagerLine {

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
            'loginduration' => array(
                'name' => __("Login duration", "linesmanager"), 
                'type' => 'specific', 
                'specifictype' => 'time',
                'timezero' => false,
                'default' => '24:00:00'
            ),
            'description' => array('name' => __("Description", "linesmanager"), 'mandatory' => true),
            'category' => array(
                'name' => PluginLinesmanagerCategory::getTypeName(),
                'description' => array('name' => __("Description", "linesmanager")),
                'mandatory' => true,
                'default' => 4,
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerCategory',
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
        return _n('Extension mobility', 'Extension mobilities', $nb, 'linesmanager');
    }
    
    /**
     * Prepare input datas for adding the item
     *
     * @param $input datas used to update the item
     *
     * @return the modified $input array
     */
    function prepareInputForAdd($input) {
        $input['name'] = $input['description'];
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
        $input['name'] = $input['description'];
        return $input;
    }
}