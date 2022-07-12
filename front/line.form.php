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
global $DB, $CFG_GLPI;

include ("../../../inc/includes.php");

//$_GET["id"] = (!isset($_GET["id"])) ? -1 : $_GET["id"] ;

$line = new PluginLinesmanagerLine();

if (PluginLinesmanagerLine::checkPostArgumentsPermissions()) {

    // check arguments for update and add
    if (isset($_POST['update']) or isset($_POST['add'])) {
        $error = $line->checkArguments($_POST);

        if ($error !== true) {
            Session::addMessageAfterRedirect($error, false, ERROR);
            HTML::back();
        }
    }

    $result = false;

    try {
        if (isset($_POST['update'])) {
            $result = $line->update($_POST);
        }
        if (isset($_POST['add'])) {
            unset($_POST['id']);
            $_POST['locations_id'] = "0";
            $_POST['states_id'] = "0";
            $result = $line->add($_POST);
        }
        if (isset($_POST['purge'])) {

            if (isset($_POST['line']) and is_array($_POST['line'])) {
                foreach ($_POST['line'] as $line_id) {
                    $result = $line->delete(array('id' => $line_id));
                }
            } else {
                $result = $line->delete($_POST);
            }
        }

        // disconnect line from an item
        if (isset($_POST['remove'])) {
            
        }

        // connect a line to an item
        if (isset($_POST['associate']) and isset($_POST['itemtype']) and isset($_POST['items_id'])) {
            $result = $line->connectItem($_POST['line'], $_POST['items_id'], $_POST['itemtype']);
        }
    } catch (Exception $e) {
        $result = false;
    }

    if (!$result) {
        Session::addMessageAfterRedirect(__("Error on save", "linesmanager"), false, ERROR);
    }

    HTML::back();
}

Session::addMessageAfterRedirect(__("Please, manage the lines from the corresponding assets.", "linesmanager"), false, INFO);
HTML::back();

/*Html::header(
    PluginLinesmanagerLine::getTypeName(Session::getPluralNumber()), 
    $_SERVER['PHP_SELF'], 
    'config', 
    'commondropdown',
    'PluginLinesmanagerLine'
);

$line->display(array('id' => $_GET["id"]));

Html::footer();*/
