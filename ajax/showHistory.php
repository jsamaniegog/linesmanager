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

$AJAX_INCLUDE = 1;
include ('../../../inc/includes.php');

// Send UTF8 Headers
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

$args = filter_input_array(INPUT_POST);

if (!isset($args['id']) or $args['id'] === -1) {
    echo __("<p>Please select a row and then click the button again to show the log of a line</p>", "linesmanager");
    die();
}

$args["withtemplate"] = (isset($args["withtemplate"])) ? $args["withtemplate"] : 0 ;

$item = new $args['item_type']();
$item->getFromDB($args['id']);

CommonGLPI::displayStandardTab(
    $item, "Log$1&id=".$args['id'], $args["withtemplate"]
);
