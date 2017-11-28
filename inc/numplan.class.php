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

/**
 * Description of numplan.
 *
 * @author Javier Samaniego García <jsamaniegog@gmail.com>
 */
class PluginLinesmanagerNumplan extends PluginLinesmanagerLine {
    
    /**
     * Name for profile rights.
     * @var type 
     */
    static $rightname = 'entity';
    
    /**
     * Belongs to this tables. Needed for search used and delete.
     * @var type 
     */
    static public $belongsTo = array(
        "PluginLinesmanagerLine", 
        "PluginLinesmanagerLinegroup", 
        "PluginLinesmanagerPickupgroup",
        "PluginLinesmanagerForward",
        "PluginLinesmanagerDdi"
    );
    
    function __construct() {
        
        // property for attribute number: 'number_format' => array(0, "", "."))
        // argmunetos para el array number_format: int $decimales, string $sep_dec, string $sep_miles
        
        $this->attributes = array(
            'id' => array('name' => 'id', 'hidden' => true),
            'number' => array(
                'name' => __("Number", "linesmanager"),
                'readOnly' => true,
                'mandatory' => true,
                'type' => 'number'
            ),
//            'range' => array(
//                'name' => __("Range", "linesmanager"), 
//                'type' => 'integer',
//                'readOnly' => true,
//                'foreingkey' => array(
//                    'item' => 'PluginLinesmanagerRange',
//                    'field_id' => 'id',
//                    'field_name' => array('name', 'min_number', 'max_number'),
//                    'field_tooltip' => array('name', 'description', 'min_number', 'max_number', 'only_pickup')
//                )
//            ),
            'vip' => array('name' => "V.I.P.", 'type' => 'bool')
        );
    }
    
    static function getTypeName($nb = 1) {
        return _n('Number', 'Numbers', $nb, 'linesmanager');
    }
    
    static function canView() {
        return true;
    }
    
    static function canCreate() {
        return false;
    }
    
    static function canDelete() {
        return false;
    }
    
    static function canPurge() {
        return false;
    }
    
    static function getNameField($options = array()) {
        return 'number';
    }
}