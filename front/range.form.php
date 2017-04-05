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

if (!Session::haveRight("entity", UPDATE)) {
    Session::addMessageAfterRedirect(__("No permission", "linesmanager"), false, ERROR);
    HTML::back();
}

if (isset($_POST['update']) or isset($_POST['add']) or isset($_POST['purge'])) {
    try {
        $range = new PluginLinesmanagerRange();
        $numplan = new PluginLinesmanagerNumplan();
        
        if (isset($_POST['update']) or isset($_POST['add'])) {
            //validations
            $error = $range->validateArguments($_POST);
            if ($error !== true) {
                Session::addMessageAfterRedirect($error, false, ERROR);
                HTML::back();
            }
        }
        
        if (isset($_POST['add']) and isset($_POST['entities_id'])) {
            $range->add($_POST);

            // insertamos todas las líneas del rango
            for ($i = $_POST['min_number']; $i <= $_POST['max_number']; $i++) {
                $numplan->add(
                    array(
                        'number' => $i,
                        'range' => $range->getID()
                    )
                );
            }
        }

        if (isset($_POST['update'])) {
            $range->update($_POST);

            // buscamos y eliminamos los números fuera del rango
            $numplans_to_delete = $numplan->find(
                "`range` = " . $range->getID() 
                . " AND (number < " . $_POST['min_number']
                . " OR number > " . $_POST['max_number'] . ")"
            );
            foreach ($numplans_to_delete as $id => $data) {
                $numplan->delete(array('id'  => $id));
            }
            
            // insertamos todas las líneas del rango
            for ($i = $_POST['min_number']; $i <= $_POST['max_number']; $i++) {
                $numplan->add(
                    array(
                        'number' => $i,
                        'range' => $range->getID()
                    )
                );
            }
        }
        
        if (isset($_POST['purge'])) {
            if (isset($_POST['id'])) {
                $numplan->deleteByCriteria(array('range' => $_POST['id']));
                $range->delete($_POST);
            } else {
                // $_POST['range'] must contains array of ids
                foreach ($_POST['range'] as $range_id) {
                    $numplan->deleteByCriteria(array('range' => $range_id));
                    $range->delete(array('id' => $range_id));
                }
            }
        }
    } catch (Exception $e) {
        Session::addMessageAfterRedirect(__("Error on save", "linesmanager"), false, ERROR);
        HTML::back();
    }
}

HTML::back();
