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

$ddi = new PluginLinesmanagerDdi();

if (PluginLinesmanagerLine::checkPostArgumentsPermissions()) {

    // check arguments for update and add
    if (isset($_POST['update']) or isset($_POST['add'])) {
        $error = $ddi->checkArguments($_POST);

        if ($error !== true) {
            Session::addMessageAfterRedirect($error, false, ERROR);
            HTML::back();
        }
    }

    $result = false;

    try {
        if (isset($_POST['update'])) {
            $result = $ddi->update($_POST);
        }
        if (isset($_POST['add'])) {
            $result = $ddi->add($_POST);
        }
        if (isset($_POST['purge'])) {
            $result = $ddi->delete($_POST);
            Html::redirect(PluginLinesmanagerDdi::getFormURL());
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
    PluginLinesmanagerDdi::getTypeName(Session::getPluralNumber()), 
    $_SERVER['PHP_SELF'], 
    'config', 
    'commondropdown',
    'PluginLinesmanagerDdi'
);

$ddi->display(array('id' => $_GET["id"]));

Html::footer();