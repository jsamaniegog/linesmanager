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
//header("Content-Type: text/html; charset=UTF-8");
header("Content-Type: application/json; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

foreach (filter_input_array(INPUT_POST) as $key => $value) {
    $_REQUEST[$key] = (is_array($value)) ? $value : str_replace("\\", "", $value);
}

$datas = PluginLinesmanagerUtilform::getDatasToLoadAjaxDropdown(filter_input_array(INPUT_POST));

$ret['results'] = $datas;
$ret['count'] = count($datas);

echo json_encode($ret);