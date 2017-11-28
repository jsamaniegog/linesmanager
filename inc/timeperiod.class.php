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

class PluginLinesmanagerTimeperiod extends PluginLinesmanagerLine {

    /**
     * Belongs to this tables. Needed for search used and delete.
     * @var type 
     */
    static public $belongsTo = array(
        "PluginLinesmanagerTimeslot"
    );
    
    function __construct() {
        parent::__construct();

        $this->attributes = array(
            'id' => array('name' => 'id', 'hidden' => true),
            'description' => array('name' => __("Description", "linesmanager"), 'mandatory' => true),
            'time_start' => array(
                'name' => __("Time start", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'time',
                'timezero' => true,
                'default' => '08:00:00',
                'seconds' => false,
                'minutes' => false,
                
            ),
            'time_end' => array(
                'name' => __("Time end", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'time',
                'default' => '15:00:00',
                'seconds' => false,
                'minutes' => false,
            ),
            'dayofweek_start' => array(
                'name' => __("Day of week start", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'enum',
                'values' => array("NULL" => Dropdown::EMPTY_VALUE, 1 => '1','2','3','4','5','6','7')
            ),
            'dayofweek_end' => array(
                'name' => __("Day of week end", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'enum',
                'values' => array("NULL" => Dropdown::EMPTY_VALUE, 1 => '1','2','3','4','5','6','7')
            ),
            'dayofmonth_start' => array(
                'name' => __("Day of month start", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'enum',
                'values' => array("NULL" => Dropdown::EMPTY_VALUE, 1 => '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31')
            ),
            'dayofmonth_end' => array(
                'name' => __("Day of month end", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'enum',
                'values' => array("NULL" => Dropdown::EMPTY_VALUE, 1 => '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31')
            ),
            'monthofyear_start' => array(
                'name' => __("Month of year start", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'enum',
                'values' => array("NULL" => Dropdown::EMPTY_VALUE, 1 => '1','2','3','4','5','6','7','8','9','10','11','12')
            ),
            'monthofyear_end' => array(
                'name' => __("Month of year end", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'enum',
                'values' => array("NULL" => Dropdown::EMPTY_VALUE, 1 => '1','2','3','4','5','6','7','8','9','10','11','12')
            ),
        );
    }

    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     * */
    static function getTypeName($nb = 1) {
        return _n('Time period', 'Time periods', $nb, 'linesmanager');
    }
    
    static function getNameField($options = array()) {
        return 'description';
    }
}