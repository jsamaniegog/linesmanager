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

/**
 * Description of utilsetup
 *
 * @author Javier Samaniego García <jsamaniegog@gmail.com>
 */
class PluginLinesmanagerUtilsetup {

    // Type reservation : http://forge.indepnet.net/projects/plugins/wiki/PluginTypesReservation
    // Reserved range   : [10126, 10135]
    const RESERVED_TYPE_RANGE_MIN = 10141;
    const RESERVED_TYPE_RANGE_MAX = 10165;

    /**
     * Return an array with the assets where include the plugin.
     * @return array Array of assets (class name).
     */
    static function getAssets() {
        $assets = array('Phone', 'Peripheral', 'NetworkEquipment', 'Software');
        
        $plugin = new Plugin();
        if ($plugin->isActivated('Simcard')) {
            $assets[] = 'PluginSimcardSimcard';
        }
        
        return $assets;
    }

}
