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
 * Description of location
 *
 * @author Javier Samaniego García <jsamaniegog@gmail.com>
 */
class PluginLinesmanagerState extends PluginLinesmanagerLine {
    
    function __construct() {
        parent::__construct();
        
        $this->attributes = array(
            'id' => array('name' => 'ID', 'hidden' => true),
            'completename' => array('name' => self::getTypeName(1)),
            'comment' => array('name' => __("Description", "linesmanager")),
        );
    }
    
    /**
     * Specific getTable for this class.
     * @param type $classname
     * @return string
     */
    static function getTable($classname = null) {
        return 'glpi_states';
    }
    
    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     * */
    static function getTypeName($nb = 1) {
        return State::getTypeName($nb);
    }
    
    function canEdit($ID) {
        return false;
    }
}
