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
 * Class PluginAppliancesProfile
 * */
class PluginLinesmanagerProfile extends CommonDBTM {

    static $rightname = "profile";

    /**
     * Get Tab Name used for itemtype
     *
     *  @see CommonGLPI getTabNameForItem()
     * */
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

        if ($item->getType() == 'Profile') {
            return PluginLinesmanagerLine::getTypeName(2);
        }
        return '';
    }

    /**
     * show Tab content
     *
     * @see CommonGLPI::displayTabContentForItem()
     * */
    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        if ($item->getType() == 'Profile') {
            $ID = $item->getID();
            $prof = new self();

            $prof->showForm($ID);
        }

        return true;
    }

    /**
     * Show profile form
     *
     * @param $profiles_id         integer
     * @param $openform            boolean (true by default)
     * @param $closeform           boolean (true by default)
     *
     * */
    function showForm($profiles_id = 0) {
        if (!Session::haveRight("profile", READ)) {
            return false;
        }

        echo "<div class='firstbloc'>";

        $profile = new Profile();
        if (($canedit = Session::haveRightsOr(self::$rightname, array(UPDATE, CREATE, PURGE)))) {
            echo "<form method='post' action='" . $profile->getFormURL() . "'>";
        }

        // get info of load profile
        $profile->getFromDB($profiles_id);

        $rights = array(
            array(
                'itemtype' => 'PluginLinesmanagerLine',
                'label' => __('Manage lines', 'linesmanager'),
                'field' => 'plugin_linesmanager_line'
            )
        );

        if ($profile->getField('interface') == 'central') {
            $profile->displayRightsChoiceMatrix(
                $rights, array(
                'canedit' => $canedit,
                'default_class' => 'tab_bg_2',
                'title' => PluginLinesmanagerLine::getTypeName(2)
                )
            );
        }

        if (Session::haveRight("profile", UPDATE)) {
            echo "<div class='center'>";
            echo Html::hidden('id', array('value' => $profiles_id));
            echo Html::submit(_sx('button', 'Save'), array('name' => 'update'));
            echo "</div>\n";
            Html::closeForm();
        }
    }

}
