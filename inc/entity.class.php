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

class PluginLinesmanagerEntity extends CommonDBTM {

    static $rightname = 'entity';
    public $dohistory = false;

    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     * */
    static function getTypeName($nb = 1) {
        return _n('Entity', 'Entities', $nb);
    }

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        $array_ret = array();
        if ($item->getID() > -1) {
            if (Session::haveRight("entity", READ)) {
                $array_ret[0] = self::createTabEntry(__('Lines Manager', 'linesmanager'));
            }
        }
        return $array_ret;
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        if ($item->getID() > -1) {
            $pmEntity = new PluginLinesmanagerEntity();
            $pmEntity->showForm($item->getID());
        }

        return true;
    }

    /**
     * Display form for service configuration
     *
     * @param $items_id integer ID
     * @param $options array
     *
     * @return bool true if form is ok
     *
     * */
    public function showForm($ID, array $options = []) {
        $range = new PluginLinesmanagerRange();
        
        $range->showFormTab(array('entities_id' => $ID ?? 0));
        
        // boton para mostrar el formulario de añadir elemento
        echo sprintf(
            ' <input class="submit" type="button" value="'
            . __("New range", "linesmanager")
            . '" name="%1$s" %2$s>', Html::cleanInputText('new'), Html::parseAttributes(
                array(
                    'name' => 'new',
                    'onClick' => PluginLinesmanagerUtilform::getJsAjaxLoadForm(
                        $range, -1, array())
                )
            )
        );
        
        return true;
    }
}
