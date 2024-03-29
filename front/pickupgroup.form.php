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

if (!PluginLinesmanagerLine::canUpdate()) {
    Session::addMessageAfterRedirect(__("No permission", "linesmanager"), false, ERROR);
    HTML::back();
}

$_GET["id"] = (!isset($_GET["id"])) ? -1 : $_GET["id"] ;

$pickupgroup = new PluginLinesmanagerPickupgroup();

if (PluginLinesmanagerLine::checkPostArgumentsPermissions()) {

    // check arguments for update and add
    if (isset($_POST['update']) or isset($_POST['add'])) {
        $error = $pickupgroup->checkArguments($_POST);

        if ($error !== true) {
            Session::addMessageAfterRedirect($error, false, ERROR);
            HTML::back();
        }
    }

    $result = false;

    try {
        if (isset($_POST['update'])) {
            $result = $pickupgroup->update($_POST);
        }
        if (isset($_POST['add'])) {
            unset($_POST['id']);
            $result = $pickupgroup->add($_POST);
        }
        if (isset($_POST['purge'])) {
            $result = $pickupgroup->delete($_POST);
            Html::redirect(PluginLinesmanagerPickupgroup::getFormURL());
        }

    } catch (Exception $e) {
        $result = false;
    }

    if (!$result) {
        Session::addMessageAfterRedirect(__("Error on save", "linesmanager"), false, ERROR);
    }

    HTML::back();
}

Html::header(
    PluginLinesmanagerPickupgroup::getTypeName(Session::getPluralNumber()), 
    $_SERVER['PHP_SELF'], 
    'config', 
    'commondropdown',
    'PluginLinesmanagerPickupgroup'
);

$pickupgroup->display(array('id' => $_GET["id"]));

Html::footer();