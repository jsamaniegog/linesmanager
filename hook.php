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

/**
 * Hook called on profile change
 * Good place to evaluate the user right on this plugin
 * And to save it in the session
 */
function plugin_change_profile_linesmanager() {
    
}

/**
 * Register the webservices methods
 *
 * @global array $WEBSERVICES_METHOD
 */
function plugin_linesmanager_registerMethods() {
    global $WEBSERVICES_METHOD;

    $WEBSERVICES_METHOD['linesmanager.getLines'] = array(
        'PluginLinesmanagerWebservice',
        'methodGetLines'
    );
}

/**
 * Fonction d'installation du plugin
 * @return boolean
 */
function plugin_linesmanager_install() {
    global $DB;

    if (!$DB->tableExists("glpi_plugin_linesmanager_line")) {
        $DB->runFile(GLPI_ROOT . "/plugins/linesmanager/sql/0.1.0.sql");

        // hack v0.85 - v0.90 to include needed classes to add the next rows
        include_once "inc/line.class.php";
        foreach (scandir(GLPI_ROOT . '/plugins/linesmanager/inc') as $fichero) {
            if (strstr($fichero, ".class.php")) {
                include_once "inc/$fichero";
            }
        }

        $category = new PluginLinesmanagerCategory();
        $input = array(
            '0' => __("Only receive calls", "linesmanager"),
            '1' => __("Internal calls within the same center", "linesmanager"),
            '2' => __("Internal calls within the company", "linesmanager"),
            '3' => __("External calls", "linesmanager"),
            '4' => PluginLinesmanagerCategory::getTypeName() . ' 4',
            '5' => PluginLinesmanagerCategory::getTypeName() . ' 5',
            '6' => PluginLinesmanagerCategory::getTypeName() . ' 6',
            '7' => PluginLinesmanagerCategory::getTypeName() . ' 7',
            '8' => PluginLinesmanagerCategory::getTypeName() . ' 8',
            '9' => __("All calls", "linesmanager"),
        );
        foreach ($input as $key => $value) {
            $category->add(
                array('name' => "$key",
                    'description' => $value)
            );
        }

        $timeperiod = new PluginLinesmanagerTimeperiod();
        $timeperiod->add(array(
            'description' => __('Morning schedule', 'linesmanager'),
            'time_start' => '08:00:00',
            'time_end' => '15:00:00',
            'dayofweek_start' => '1',
            'dayofweek_end' => '5'
        ));

        $timeslot = new PluginLinesmanagerTimeslot();
        $timeslot->add(array(
            'description' => __('8h-15h, Mon-Fri, Cat. 6', 'linesmanager'),
            'timeperiod' => $timeperiod->getID(),
            'category' => '7'
        ));
    }
    
    if (!$DB->fieldExists("glpi_plugin_linesmanager_lines", "locations_id")) {
        $DB->runFile(GLPI_ROOT . "/plugins/linesmanager/sql/0.2.0.sql");
    }
    
    if (!$DB->fieldExists("glpi_plugin_linesmanager_lines", "states_id")) {
        $DB->runFile(GLPI_ROOT . "/plugins/linesmanager/sql/0.3.0.sql");
    }
    
    if (!$DB->fieldExists("glpi_plugin_linesmanager_numplans", "vip")) {
        $DB->runFile(GLPI_ROOT . "/plugins/linesmanager/sql/0.4.0.sql");
    }
    
    if ($DB->fieldExists("glpi_plugin_linesmanager_numplans", "vip")) {
        $DB->runFile(GLPI_ROOT . "/plugins/linesmanager/sql/0.5.0.sql");
    }

    // register a cron for task execution
    /* $res = CronTask::Register(
      "PluginLinesmanagerCron", "linesmanager", 86400, array(
      'comment' => __('Lines manager', 'linesmanager'),
      'mode' => CronTask::MODE_EXTERNAL
      )
      ); */
    //CronTask::Unregister("Linesmanager");
    return true;
}

/**
 * Fonction de désinstallation du plugin
 * @return boolean
 */
function plugin_linesmanager_uninstall() {
    global $DB;

    $DB->runFile(GLPI_ROOT . "/plugins/linesmanager/sql/uninstall-0.1.0.sql");

    return true;
}

/**
 * Define Dropdown tables to be manage in GLPI :
 * */
function plugin_linesmanager_getDropdown() {
    return array(
        'PluginLinesmanagerLine' => PluginLinesmanagerLine::getTypeName(Session::getPluralNumber()),
//        'PluginLinesmanagerNumplan' => PluginLinesmanagerNumplan::getTypeName(Session::getPluralNumber()),
        'PluginLinesmanagerLinegroup' => PluginLinesmanagerLinegroup::getTypeName(Session::getPluralNumber()),
        'PluginLinesmanagerPickupgroup' => PluginLinesmanagerPickupgroup::getTypeName(Session::getPluralNumber()),
        'PluginLinesmanagerCategory' => PluginLinesmanagerCategory::getTypeName(Session::getPluralNumber()),
        'PluginLinesmanagerExtensionmobility' => PluginLinesmanagerExtensionmobility::getTypeName(Session::getPluralNumber()),
        'PluginLinesmanagerTimeperiod' => PluginLinesmanagerTimeperiod::getTypeName(Session::getPluralNumber()),
        'PluginLinesmanagerTimeslot' => PluginLinesmanagerTimeslot::getTypeName(Session::getPluralNumber()),
        'PluginLinesmanagerDdi' => PluginLinesmanagerDdi::getTypeName(Session::getPluralNumber()),
        'PluginLinesmanagerForward' => PluginLinesmanagerForward::getTypeName(Session::getPluralNumber()),
    );
}

/**
 * Permite incluir campos adicionales en la búsqueda principal.
 * @global type $LANG
 * @param type $itemtype
 * @return array
 */
function plugin_linesmanager_getAddSearchOptions($itemtype) {
    $sopt = array();

    $reservedTypeIndex = PluginLinesmanagerUtilsetup::RESERVED_TYPE_RANGE_MIN;

    $sopt_final = array();
    
    if (in_array($itemtype, PluginLinesmanagerUtilsetup::getAssets()) and PluginLinesmanagerLine::canView()) {
        $line = new PluginLinesmanagerLine();

        $sopt = $line->rawSearchOptions(1, PluginLinesmanagerLine::getTypeName(Session::getPluralNumber()));
        foreach ($sopt as $key => $value) {
            if (strstr($sopt[$key]['name'], '- ID') or $sopt[$key]['id'] == 'common') {
                continue;
            }
            
            if ($sopt[$key]['getAddSearchOptions'] === false) {
                unset($sopt[$key]);
            }
            
            if (PluginLinesmanagerUtilform::isForeingkey($value)) {
                $sopt[$key]['joinparams'] = array(
                    'beforejoin' => array(
                        'table' => PluginLinesmanagerLine::getTable(),
                        'joinparams' => array('jointype' => 'itemtype_item')
                    )
                );
            } else {
                if ($key != 'common') {
                    $sopt[$key]['joinparams'] = array('jointype' => 'itemtype_item');
                }
            }
            
            $sopt_final[$reservedTypeIndex] = $sopt[$key];
            $reservedTypeIndex++;
        }
    }
    
    return $sopt_final;
}

/**
 * Specific select fields for the plugin.
 *
 * @param $itemtype     item type
 * @param $ID           ID of the item to add
 * @param $num          item num in the reque (default 0)
 *
 * @return select string
 * */
function plugin_linesmanager_addSelect($itemtype, $ID, $num) {

    if (in_array($itemtype, PluginLinesmanagerUtilsetup::getAssets())) {
        $tab = plugin_linesmanager_getAddSearchOptions($itemtype);
    } else {
        $item = new $itemtype();
        $tab = $item->rawSearchOptions();
    }
    $searchopt = $tab[$ID];

    /* $fk = getItemTypeForTable($searchopt['table']);
      $fk = new $fk();
      $attribute = $fk->attributes[$searchopt['field']]; */

    // the alias to concat to the table
    $alias = (isset($searchopt['linkfield'])) ? "_" . $searchopt['linkfield'] : "";

    $concat = "";

    if (PluginLinesmanagerUtilform::isForeingkey($searchopt)) {
        //$alias = $searchopt['linkfield'];
        $searchopt['table'] = $searchopt['foreingkey']['item']::getTable();
        $searchopt['field'] = $searchopt['foreingkey']['field_name'];
        if (is_array($searchopt['foreingkey']['field_name'])) {
            $field_array = $searchopt['foreingkey']['field_name'];
        } else {
            $field_array[] = $searchopt['foreingkey']['field_name'];
        }

        // the fields
        $concat = "concat_ws(' - '";
        for ($i = 0; $i < count($field_array); $i++) {
            $concat .= ",";

            $table_name = $searchopt['table'];
            $field_name = $field_array[$i];

            $fk = new $searchopt['foreingkey']['item']();
            $attribute2 = $fk->attributes[$field_array[$i]];
            if (PluginLinesmanagerUtilform::isForeingkey($attribute2)) {
                $table_name = $attribute2['foreingkey']['item']::getTable();
                $field_name = $attribute2['foreingkey']['field_name'];
            }

            //$concat .= $table_name . "_" . $field_array[$i] . "." . $field_name;
            $concat .= $table_name . $alias . "." . $field_name;
        }
        $concat .= ")";
    }

    // only for itemlink
    if ($searchopt['datatype'] === 'itemlink') {
        return "concat_ws('" . Search::SHORTSEP . "', " . $searchopt['table'] . $alias . "." . $searchopt['field'] . ", " . $searchopt['table'] . ".id) as ITEM_" . $num . ", ";
    }

    if ($concat != "") {
        return $concat . " as ITEM_" . $num . ", ";
    } else {
        //return $searchopt['table'] . $alias . "." . $searchopt['field'] . " as ITEM_" . $num . ", ";
        return $searchopt['table'] . "." . $searchopt['field'] . " as ITEM_" . $num . ", ";
    }
}

/**
 * Specific left join for the plugin.
 * @param string $itemtype
 * @param string $ref_table
 * @param string $new_table
 * @param string $linkfield
 * @param array $already_link_tables
 * @return string The left join
 */
function plugin_linesmanager_addLeftJoin($itemtype, $ref_table, $new_table, $linkfield, array &$already_link_tables) {
    // hack
    if ($linkfield == 'plugin_linesmanager_lines_id')
        return null;
    
    if (in_array($itemtype, PluginLinesmanagerUtilsetup::getAssets())) {
        $itemtype = 'PluginLinesmanagerLine';
        $ref_table = PluginLinesmanagerLine::getTable();
    }

    $AS = $new_table . "_" . $linkfield;

    if (in_array($AS, $already_link_tables)) {
        throw new Exception(__class__ . ": duplicate field in select. Line: " . __LINE__);
    }

    $leftjoin = " LEFT JOIN `$new_table` $AS ON ($AS.`id` = `$ref_table`.`$linkfield`)";

    $already_link_tables[] = $AS;

    // check if is foreingkey the field "field_name", if it is we do a new left join with the second table
    $item = new $itemtype();

    $attribute = $item->attributes[$linkfield];

    if (PluginLinesmanagerUtilform::isForeingkey($attribute)) {

        $fk = new $attribute['foreingkey']['item']();

        $alias_sufix = $linkfield;

        // linkfield for the new left join
        $linkfield = $attribute['foreingkey']['field_name'];
        //$linkfield = (is_array($linkfield)) ? $linkfield[0] : $linkfield;
        if (!is_array($linkfield)) {
            $linkfield_array[] = $linkfield;
        } else {
            $linkfield_array = $linkfield;
        }

        foreach ($linkfield_array as $lf_key => $linkfield) {
            $lf;
            if (PluginLinesmanagerUtilform::isForeingkey($fk->attributes[$linkfield])) {
                // ref_table for the new left join
                $ref_table = $AS;

                // new table for the new left join
                $new_table = $fk->attributes[$linkfield]['foreingkey']['item'];
                $new_table = $new_table::getTable();

                $AS = $new_table . "_" . $alias_sufix;
                if (in_array($AS, $already_link_tables)) {
                    throw new Exception(__class__ . ": duplicate field in select. Line: " . __LINE__);
                }

                // add left join for the next join table
                $leftjoin .= " LEFT JOIN `$new_table` $AS ON ($AS.`id` = `$ref_table`.`$linkfield`)";

                $already_link_tables[] = $AS;

                // for next loop
                $AS = $ref_table;
            }
        }
    }

    return $leftjoin;
}

/**
 * Specific Function to add where to a request
 *
 * @param $link         link string: AND, AND NOT, OR,...
 * @param $nott         is it a negative search (1 = NOT)
 * @param $itemtype     item type
 * @param $ID           ID of the item to search
 * @param $searchtype   searchtype used (equals or contains)
 * @param $val          item num in the request
 * @param $meta         is a meta search (meta=2 in search.class.php) (default 0)
 *
 * @return select string
 * */
function plugin_linesmanager_addWhere($link, $nott, $itemtype, $ID, $val, $searchtype) {
    if (in_array($itemtype, PluginLinesmanagerUtilsetup::getAssets())) {
        $tab = plugin_linesmanager_getAddSearchOptions($itemtype);
    } else {
        $item = new $itemtype();
        $tab = $item->rawSearchOptions();
    }
    
    $searchoptArray[] = $tab[$ID];
    
    // hack to pass several fields to add at where clause
    if (strstr($searchtype, ";")) {
        list($searchtype, $fieldsToAdd) = explode(";", $searchtype);
        $fieldsToAdd = explode(",", $fieldsToAdd);
        foreach ($fieldsToAdd as $fieldToAdd) {
            $searchoptArray[] = $tab[$fieldToAdd];
        }
    }

    $nott = ($nott === 1) ? " NOT" : "";

    if (strtolower($val) === 'null') {
        if ($searchtype == 'notequals') {
            $val = "is not NULL";
        } else {
            $val = "is NULL";
        }
    } else if ($searchtype == 'equals') {
        $val = "= '$val'";
    } else if ($searchtype == 'notequals') {
        $val = "= '$val'";
    } else {
        $val = "like '%$val%'";
    }

    $where = $link . " (";

    foreach ($searchoptArray as $key => $searchopt) {

        $fk = getItemTypeForTable($searchopt['table']);
        $fk = new $fk();

        $table = $fk::getTable();
        $alias = $searchopt['linkfield'];
        $alias = ($alias != "") ? "_" . $alias : $alias;
        
        if ($key != 0) {
            $where .= " OR ";
        }

        $field_name = $searchopt['field'];
        $field_names = array();
        if (!is_array($field_name)) {
            $field_names[] = $field_name;
        } else {
            $field_names = $field_name;
        }
        
        foreach ($field_names as $key_field_name => $field_name) {
            
            if ($key_field_name != 0) {
                $where .= " OR ";
            }
            
            $attribute = $fk->attributes[$field_name];
            $table = $searchopt['table'];

            if (PluginLinesmanagerUtilform::isForeingkey($attribute)) {
                $table = $fk->attributes[$field_name]['foreingkey']['item'];
                $table = $table::getTable();

                $field_name = $attribute['foreingkey']['field_name'];
            }

            $where .= $nott . " " . $table . $alias . "." . $field_name . " $val ";
        }
    }

    return $where . ")";
}

/**
 * Actions done after the ADD of the item in the database
 *
 * @return nothing
 */
function plugin_item_add_linesmanager_PluginSimcardSimcard_Item($data) {
    
    PluginLinesmanagerLine::updateFieldsFromParentItem($data);
    
    $line = new PluginLinesmanagerLine();
    // the id of the simcard
    $line->fields['items_id'] = $data->fields['plugin_simcard_simcards_id'];
    $line->fields['itemtype'] = "PluginSimcardSimcard";
    $line->updateContactInformation();
}

/**
 * Actions done after the DELETE of the item in the database
 *
 * @return nothing
 */
function plugin_item_purge_linesmanager_PluginSimcardSimcard_Item($data) {
    $line = new PluginLinesmanagerLine();
    // the id of the linked asset at the simcard
    $line->fields['items_id'] = $data->fields['items_id'];
    $line->fields['itemtype'] = $data->fields['itemtype'];
    $line->cleanContactInformation();
}

function plugin_post_item_add_linesmanager(CommonDBTM $item) {
    PluginLinesmanagerLine::updateFieldsFromParentItem($item);
}

function plugin_post_item_update_linesmanager(CommonDBTM $item) {
    PluginLinesmanagerLine::updateFieldsFromParentItem($item);
}

/*function plugin_post_item_delete_linesmanager(CommonDBTM $item) {
    PluginLinesmanagerLine::updateFieldsFromParentItem($item);
}*/

function plugin_post_item_purge_linesmanager(CommonDBTM $item) {
    $line = new PluginLinesmanagerLine();
    $line->deleteByCriteria(array(
        'itemtype' => get_class($item),
        'items_id' => $item->getID()
    ));
}