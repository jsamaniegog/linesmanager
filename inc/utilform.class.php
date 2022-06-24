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
 * Description of utilForms
 *
 * @author Javier Samaniego García <jsamaniegog@gmail.com>
 */
class PluginLinesmanagerUtilform {

    static public $plugin_name = "PluginLinesmanager";

    static function isJson($string) {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    static function getLoadingDiv() {
        return "<div style='margin-top: 20px;margin-bottom:20px;' class='loadingindicator'>" . __s('Loading...') . "</div></div>";
    }

    /**
     * Return javascript to load a form, by ajax, in a div name: div_form_<name>.
     * The file called to load by ajax must be named ajax/show<Name>Form.php
     * @param class $item The class who load the form.
     * @param int|javascript $id Id of element to load form.
     * @param array $data Asociative array with data to send by POST including
     * $id.
     * @return string
     */
    static function getJsAjaxLoadForm($item, $id = "$(this).val()", $data = array()) {
        global $CFG_GLPI;
        $data = json_encode($data);

        return '$.ajax({
            url:  "' . $CFG_GLPI["root_doc"] . '/plugins/linesmanager/ajax/show' . ucfirst(self::getItemName($item)) . 'Form.php",
            success: function(html) {
                $("#div_form_' . self::getItemName($item) . '").html(html);
            },
            method: "POST",
            data: {id: ' . $id . ', ' . substr($data, 1, strlen($data) - 2) . '},
            beforeSend: function( xhr ) {
                $("#div_form_' . self::getItemName($item) . '").html("' . self::getLoadingDiv() . '");
            }
        });';
    }

    /**
     * Javascript to show history of an item in a element like a <div>.
     * @global type $CFG_GLPI
     * @param type $data
     * @param type $element
     * @return string JavaScript code.
     */
    static function getJsAjaxShowHistory($item, $id, $element = 'div_history') {
        global $CFG_GLPI;

        $data['item_type'] = $item::getType();
        $data['items_id'] = -1;

        $data = json_encode($data);

        return '$.ajax({
            url: "' . $CFG_GLPI["root_doc"] . '/plugins/linesmanager/ajax/showHistory.php",
            success: function(html) {
                $("#' . $element . '").html(html);
            },
            method: "POST",
            data: {id: ' . $id . ', ' . substr($data, 1, strlen($data) - 2) . '},
            beforeSend: function( xhr ) {
                $("#' . $element . '").html("' . self::getLoadingDiv() . '");
            }
        });';
    }
    
    /**
     * Javascript to show history of an item in a element like a <div>.
     * @global type $CFG_GLPI
     * @param type $data
     * @param type $element
     * @return string JavaScript code.
     */
    static function getJsAjaxShowHtmlList($item, $id, $order, $sort, $element = 'table_lines') {
        global $CFG_GLPI;

        return '$.ajax({
            url: "' . $CFG_GLPI["root_doc"] . '/plugins/linesmanager/ajax/showLineList.php",
            success: function(html) {
                $("#'.$element.'").parent().parent().html(html);
            },
            method: "POST",
            data: {item: "' . get_class($item) . '",id: ' . $id . ', order:"'.$order.'", sort:"'.$sort.'"}
        });';
    }

    /**
     * Prints a <div> tag to load form by ajax. See getJsAjaxRowClickLoadForm 
     * doc.
     * @param string $name The item name in singular. It will be the id of the div.
     * @param HTML $content Content to include in the div.
     */
    static function showHtmlDivToLoadFormByAjax($name, $content = "", $closeDiv = true) {
        self::showHtmlDivOpen("div_form_$name");
        echo $content;

        if ($closeDiv === true) {
            echo self::showHtmlDivClose();
        }
    }

    /**
     * Open <div> tag.
     * 
     * @param string $id ID of the div.
     * @param array $options Html options.
     * @param bool $closeDiv true if you want close the div.
     */
    static function showHtmlDivOpen($id, $options = array(), $closeDiv = false) {
        echo "<div id='$id' " . Html::parseAttributes($options) . ">";

        if ($closeDiv === true) {
            self::showHtmlDivClose();
        }
    }

    /**
     * Close <div> tag.
     */
    static function showHtmlDivClose() {
        echo "</div>";
    }

    /**
     * Return javascript to load a form, by ajax, in a div name: div_form_<name>
     * when a row of the table is clicked.
     * The file called to load by ajax must be named ajax/show<Name>Form.php
     * Each row must have an attribute named <$attr> to load datas correctly. 
     * @param string $table_id The id of the table.
     * @param class $item The class who load the form.
     * @param string $attr The name of attribute associate to <tr> to search
     * the ID of the row.
     * @param bool $last_unbind Indicates if the last <td> element must have
     * click event. It's because the last column is used for delete checkbox
     * in some forms of this Plugin.
     * @return string
     */
    static function getJsAjaxRowClickLoadForm($table_id, $item, $js_onclick_row = "", $attr = "id", $last_unbind = true) {
        $js = "$( '#$table_id > tbody > tr > td' ).click(function() {";
        $js .= self::getJsAjaxLoadForm($item, "$(this).parent().attr('$attr')");
        $js .= $js_onclick_row;
        $js .= "$( '#$table_id').attr('selected_id', $(this).parent().attr('$attr'));";
        $js .= "});";

        if ($last_unbind) {
            $js .= "$( '#$table_id > tbody > tr > td:last-child' ).unbind('click');";
        }

        return $js;
    }

    /**
     * Show HTML of a table rows with the fields of the form. <table> must be
     * opened previously.
     * @param array $attributes Array of field attributes like: type, name,....
     * @param array $values Array of field values (ususally $this->fields)
     */
    static function showHtmlRowFormFields($attributes, $values, $view_options = array()) {
        // default loops (columns)
        $loops = 2;

        if (isset($view_options['columns'])) {
            $loops = $view_options['columns'];
        }

        foreach ($attributes as $dbfield_name => $attribute) {

            if (!isset($values[$dbfield_name])) {
                $values[$dbfield_name] = '';
            }

            // formating value with sprintf
            if (isset($attribute['string_format'])) {
                $values[$dbfield_name] = sprintf($attribute['string_format'], $values[$dbfield_name]);
            }

            // formating value with number_format
            if (isset($attribute['number_format'])) {
                $values[$dbfield_name] = number_format(
                    $values[$dbfield_name], $attribute['number_format'][0], $attribute['number_format'][1], $attribute['number_format'][2]
                );
            }

            // hidden fields
            if (isset($attribute['hidden']) and $attribute['hidden'] === true) {
                echo Html::hidden($dbfield_name, array('value' => $values[$dbfield_name]));
                continue;
            }

            // row open
            if (isset($view_options['columns'])) {
                if ($loops == $view_options['columns']) {
                    self::showHtmlRowOpen();
                }
            } else {
                self::showHtmlRowOpen();
            }
            $loops--;

            // field name: 
            echo "<td>";
            echo $attribute['name'];
            if (self::isMandatory($attribute)) {
                echo "*";
            }
            echo ": ";
            echo "</td><td>";

            if (isset($attribute['readOnly']) and $attribute['readOnly'] == true) {
                echo $values[$dbfield_name];
            } else {
                self::showHtmlInputFieldAccordingType($attribute, $values, $dbfield_name);
            }

            // close the cell
            echo "</td>";

            // close the row
            if (isset($view_options['columns'])) {
                if ($loops == 0) {
                    self::showHtmlRowClose();
                    $loops = $view_options['columns'];
                }
            } else {
                self::showHtmlRowClose();
            }
        }
    }

    /**
     * Show a field to input according to the type.
     * @param array $attribute Attribute from model class.
     * @param array $values Array of values of the record match.
     * @param string $dbfield_name The name of the field in the database.
     */
    static function showHtmlInputFieldAccordingType($attribute, $values, $dbfield_name) {
        // default value
        if ($values["id"] === "" and isset($attribute['default'])) {
            $values[$dbfield_name] = $attribute['default'];
        }

        // if is a readOnly field
        if (isset($attribute['readOnly']) and $attribute['readOnly'] === true) {
            $attribute['style'] = "background-color:#F1F1F1;";
        }
        /* if (isset($attribute['readOnly']) and $attribute['readOnly'] === true) {

          if (self::isForeingkey($attribute)) {
          $values[$dbfield_name] = self::getForeingKeyName(
          $values[$dbfield_name], $attribute
          );
          }

          //echo "<span>" . $values[$dbfield_name] . "</span>";
          } else */if (self::isForeingkey($attribute)) {

            // if is foreing key
            self::showHtmlForeingkeyDropdown($dbfield_name, $values[$dbfield_name], $attribute);
        } elseif (isset($attribute['type'])) {
            // else show by type
            switch ($attribute['type']) {
                case 'bool':
                    Dropdown::showYesNo($dbfield_name, $values[$dbfield_name]);
                    break;

                case 'specific':
                    switch ($attribute['specifictype']) {
                        case 'time':
                            echo self::dropdownTime($dbfield_name, $values[$dbfield_name], $attribute);
                            break;

                        // default array
                        default:
                            Dropdown::showFromArray($dbfield_name, $attribute['values'], array('value' => $values[$dbfield_name]));
                    }
                    break;

                default:
                    echo Html::input($dbfield_name, array('value' => $values[$dbfield_name]));
            }
        } else {
            // else show an input (free text)
            $options = $attribute;
            $options['value'] = $values[$dbfield_name];
            echo Html::input($dbfield_name, $options);
        }
    }

    /**
     * Dropdown for time selection. (Minutes and seconds will increase by 5 
     * seconds).
     *
     * @param string $name select name
     * @param string $value default value (default 0)
     * @param array $options Elements can be:
     *  - null bool (default true) Show an element to null value.
     *  - timezero bool (default true) Show 0 seconds
     *  - seconds bool (default true) Show seconds values
     *  - minutes bool (default true) Show minutes values
     *  - hours bool (default true) Show hours values
     *  - todo: onlyOnthedotTimes bool (default true) Show only exact times for each
     * type of time: second, minute and hour. If it's false will show 
     * intermediate times, for example: 03:45:50.
     * */
    static function dropdownTime($name, $value = '', $options = array()) {

        $tab = array();

        if (!isset($options['null']) or $options['null'] !== false) {
            $tab["0"] = "----";
        }

        if (!isset($options['timezero']) or $options['timezero'] !== false) {
            $tab[sprintf("00:00:%02d", 0)] = sprintf(_n('%d second', '%d seconds', 0), 0);
        }

        // Seconds
        if (!isset($options['seconds']) or $options['seconds'] !== false) {
            for ($i = 5; $i <= 60; $i += 5) {
                $tab[sprintf("00:00:%02d", $i)] = sprintf(_n('%d second', '%d seconds', $i), $i);
            }
        }

        // Minutes
        if (!isset($options['minutes']) or $options['minutes'] !== false) {
            $tab[sprintf("00:%02d:00", $i)] = sprintf(_n('%d minute', '%d minutes', 1), 1);
            for ($i = 5; $i < 60; $i += 5) {
                $tab[sprintf("00:%02d:00", $i)] = sprintf(_n('%d minute', '%d minutes', $i), $i);
            }
        }

        // Hours
        if (!isset($options['hours']) or $options['hours'] !== false) {
            for ($i = 1; $i <= 24; $i++) {
                $tab[sprintf("%02d:00:00", $i)] = sprintf(_n('%d hour', '%d hours', $i), $i);
            }
        }

        $options['value'] = $value;

        if (isset($options['display']) and $options['display'] === false) {
            return Dropdown::showFromArray($name, $tab, $options);
        }

        Dropdown::showFromArray($name, $tab, $options);
    }

    /**
     * Show a HTML table to list the records.
     * @param type $table_id
     * @param type $item
     * @param type $condition
     * @param array $buttons Can be: remove or delete or purge
     * @param array $table_options HTML table options.
     * @param string $js_onclick_row Javascript that will be executed when a row is clicked
     */
    static function showHtmlList($table_id, CommonDBTM $parentItem, PluginLinesmanagerLine $item, $condition = "", $orderby = "numplan ASC", $buttons = array(), $table_options = array(), $js_onclick_row = "") {

        $records = $item->find([$condition], $orderby);

        echo "<div class='asset' style='overflow: auto; margin-bottom: 30px;'>";
        echo "<div class='card-header main-header d-flex flex-wrap mx-n2 mt-n2 align-items-stretch'>";
        echo "<h3 class='card-title d-flex align-items-center ps-4'>".$item::getTypeName(Session::getPluralNumber())."</h3>";
        echo "</div>";

        if (count($records) === 0) {
            return;
        }

        echo "<table id='$table_id' style='font-size: .85em' class='table card-table table-hover table-striped' " . Html::parseAttributes($table_options) . ">";

        // cabecera campos
        echo "<tr>";
        foreach ($item->attributes as $dbfield_name => $attribute) {

            if (isset($attribute['hidden']) and $attribute['hidden'])
                continue;

            $order = $dbfield_name;
            $sort = "ASC";
            
            list($orderfield, $ordertype) = explode(" ", $orderby);
            $class = "";
            
            if ($orderfield === $dbfield_name) {
                $sort = ($ordertype === 'DESC') ? 'ASC' : 'DESC';
                $class = "class='order_".$ordertype."'";
            }
            
            echo "<th>"
                . "<a href='#' $class onclick='".PluginLinesmanagerUtilform::getJsAjaxShowHtmlList($parentItem, $parentItem->getID(), $order, $sort)."'>"
                . $attribute['name'] . "</th>"
                . "</a>";
        }

        $show_col_buttons = false;
        if (in_array('remove', $buttons) and $item->canUpdate()) {
            $show_col_buttons = true;
            echo "<th>" . Html::submit(__('Remove', 'linesmanager'), array('name' => 'remove', 'confirm' => __("Are you sure you want to remove the selected records?.", "linesmanager"), 'class' => 'btn btn-outline-warning me-2'));
        } elseif ((in_array('delete', $buttons) and $item->canDelete()) or ( $item->canPurge() and in_array('purge', $buttons))) {
            $show_col_buttons = true;
            echo "<th>" . Html::submit(__('Delete permanently'), array('name' => 'purge', 'confirm' => __("Are you sure you want to delete the selected records?.", "linesmanager"), 'class' => 'btn btn-outline-warning me-2'));
        }

        echo "</tr>";

        // valores campos
        foreach ($records as $id => $record) {

            $css_row = ($record['vip']) ? "background-color:#fff294; font-weight: bolder; color: #cca300" : "";

            echo "<tr id='" . $id . "' style='$css_row'>";

            foreach (self::getFieldsValue($item, $record) as $value) {
                echo "<td align=center>" . $value . "</td>";
            }

            if ($show_col_buttons) {
                echo "<td align=center><input type='checkbox' name='line[]' value='$id'></td></tr>";
            }

            echo "</tr>";
        }

        // javascript to load form when a row of the table is clicked
        echo Html::scriptBlock(
            PluginLinesmanagerUtilform::getJsAjaxRowClickLoadForm($table_id, $item, $js_onclick_row)
        );
        
        echo "</table>";
        echo "</div>";
    }

    /**
     * Return an array with the values to show of a record. For example if
     * the value of a foreingkey return as the indicated name.
     * @param type $item
     * @param type $record
     * @return array Array of values. The field name is the key.
     */
    static public function getFieldsValue($item, $record) {
        $values = array();

        foreach ($item->attributes as $dbfield_name => $attribute) {

            if (isset($attribute['hidden']) and $attribute['hidden'])
                continue;

            if (isset($attribute['foreingkey'])) {
                $values[$dbfield_name] = self::getForeingkeyName($record[$dbfield_name], $attribute);
            } else if (isset($attribute['type']) and $attribute['type'] == 'bool') {
                $values[$dbfield_name] = ($record[$dbfield_name] == 1) ? __('Yes') : __('No');
            } else {
                $values[$dbfield_name] = $record[$dbfield_name];
            }
        }

        return $values;
    }

    /**
     * Show tag to open a form.
     * @param type $item
     */
    static public function showHtmlFormOpen($item) {
        echo "<form name='form' method='post' action='" . $item->getFormURL() . "'>";
    }

    /**
     * Open table, row and cell.
     * @param type $class
     * @param type $colspan
     */
    static public function showHtmlTableOpen($class = 'tab_cadre_fixe', $colspan = '1') {
        echo '<table class="' . $class . '"><tr><td style="vertical-align:top" colspan="' . $colspan . '">';
    }

    /**
     * Close cell, row and table.
     */
    static public function showHtmlTableClose() {
        echo '</td></tr></table>';
    }

    /**
     * Close the previous cell and open new one.
     * @param type $colspan
     */
    static public function showHtmlTableNewCell($colspan = '1') {
        echo '</td><td style="vertical-align:top" colspan="' . $colspan . '">';
    }

    /**
     * Close the previous row and open new one.
     * @param type $colspan
     */
    static public function showHtmlTableNewRow($colspan = '1') {
        echo '</td></tr><tr><td style="vertical-align:top" colspan="' . $colspan . '">';
    }

    /**
     * Open new row.
     * @param type $view_options
     */
    static public function showHtmlRowOpen() {
        echo "<tr>";
    }

    /**
     * Open new row.
     * @param type $view_options
     */
    static public function showHtmlRowClose() {
        echo "</tr>";
    }

    /**
     * Show in HTML a line jump (<br>).
     */
    static public function showHtmlLineJump() {
        echo '<br>';
    }

    /**
     * Return an instance of foreingkey. Check first with isForeingkey() 
     * function.
     * @param array $attribute
     * @return item
     */
    static public function getForeingkeyInstance($attribute) {
        return new $attribute['foreingkey']['item']();
    }

    /**
     * Return the name value of a field that is a foreingkey.
     * @param type $item_id
     * @param type $attribute
     * @param string|array $key
     * @param object $fk
     * @param array $options Options: 
     *  - style: can be: 
     *              hyphen(default): names separated by hyphen
     *              fieldvalue: return a string with "field: value" format
     * @return string
     */
    static function getForeingkeyName($item_id, $attribute, $fk = null, $key = 'field_name', $options = array()) {
        if ($item_id === null or $item_id == 0) {
            return '';
        }

        $fk = ($fk == null) ? self::getForeingkeyInstance($attribute) : $fk;
        if (empty($fk->fields)) {
            $fk->getFromDB($item_id);
        }

        // the name of the field where we search
        $field_name = $attribute['foreingkey'][$key];

        // si el nombre que queremos mostrar es también foreingkey
        if (!is_array($field_name) and self::isForeingkey($fk->attributes[$field_name])) {
            $name = self::getForeingkeyName(
                    $fk->fields[$field_name] ?? null, $fk->attributes[$field_name] ?? null, null, $key
            );

            // formating value with sprintf
            if (isset($attribute['foreingkey']['string_format'])) {
                return self::getStringFormat(
                        $name, $attribute['foreingkey']['string_format'], $field_name
                );
            } else {
                return $name;
            }
        } else {
            // if the name we are serveral fields
            if (is_array($field_name)) {
                $name = "";
                foreach ($field_name as $key2 => $field) {
                    $attribute2 = $attribute;
                    //$attribute2['foreingkey'][$key] = $field;
                    // in this case we take field_name
                    $attribute2['foreingkey']["field_name"] = $field;
                    $nametoadd = "";
                    //$nametoadd = self::getForeingkeyName($item_id, $attribute2, $fk, $key);
                    $nametoadd = (string) self::getForeingkeyName($item_id, $attribute2, $fk, "field_name");

                    // guión entre nombres
                    if (!isset($options['style'])) {
                        $options['style'] = 'hyphen';
                    }

                    // nombre del campo si precisa
                    switch ($options['style']) {
                        case 'fieldvalue':
                            if ($name != "")
                                $name .= "<br>";
                            $name .= $fk->attributes[$field]['name'];
                            break;

                        default: break;
                    }

                    if ($name != "" and $nametoadd != "") {
                        switch ($options['style']) {
                            case 'fieldvalue':
                                $name .= ": ";
                                break;
                            default:case 'hyphen':
                                $name .= " - ";
                                break;
                        }
                    }

                    // hack for boolean fields
                    if (isset($fk->attributes[$field]['type']) and $fk->attributes[$field]['type'] === 'bool') {
                        $nametoadd = ($nametoadd == '0') ? __("No") : __("Yes") ;
                    }
                    
                    $name .= $nametoadd;
                }

                return $name;
            }

            // formating value with sprintf
            if (isset($attribute['foreingkey']['string_format'])) {
                $fk->fields[$field_name] = self::getStringFormat(
                        $fk->fields[$field_name] ?? null, $attribute['foreingkey']['string_format'], $field_name
                );
            }

            if (!isset($fk->fields[$field_name])) {
                return '';
            }
            return $fk->fields[$field_name];
        }
    }

    /**
     * Format a string.
     * @param type $value Value to format.
     * @param string_format|array $format Can be an array of string formats.
     * @param string $field_name Only if format is array.
     */
    static function getStringFormat($value, $format, $field_name = null) {
        if (isset($value) and $value != "" and isset($format)) {
            if (is_array($format) and isset($format[$field_name])) {
                $value = sprintf($format[$field_name], $value);
            } elseif (!is_array($format)) {
                $value = sprintf($format, $value);
            }
        }

        return $value;
    }

    /**
     * Show select of a foreing key.
     * @param type $name
     * @param type $value
     * @param type $attribute
     */
    static function showHtmlForeingkeyDropdown($name, $value, $attribute) {
        global $CFG_GLPI;

        $attribute_fk = $attribute['foreingkey'];

        $fk = new $attribute_fk['item']();

        $attribute_fk['field_name'] = (is_array($attribute_fk['field_name'])) ?
            implode(",", $attribute_fk['field_name']) :
            $attribute_fk['field_name'];

        // random number
        $rand = mt_rand();

        // set value or default value
        $value = ($value === "" and isset($attribute['default'])) ?
            $attribute['default'] :
            $value;

        $value = empty($value) ? "0" : $value;

        // name to show 
        $name_to_show = PluginLinesmanagerUtilform::getForeingkeyName($value, $attribute, $fk);
        $name_to_show = ($name_to_show === null or $name_to_show === "") ? Dropdown::EMPTY_VALUE : $name_to_show;

        // params for ajaxDropdown
        $param = array_merge(
            array(
            'id' => $value, // need id to filer used values
            'field' => $name, // need the name of the field to filter used values
            'value' => $value, // this is the value to assing to dropdown
            'valuename' => $name_to_show  // this is the value to show in dropdown
            ), $attribute  // params to send ajax load page
        );

        // id of the dropdown
        $dropdown_id = "dropdown_" . $name . $rand;
        // id of the box that stores the tooltip
        $box_tooltip_id = "box_tooltip_$name$rand";

        // onchange event to change tooltip
        $json_attribute = json_encode(array_merge($attribute), JSON_UNESCAPED_UNICODE);
        $param['on_change'] = '$.ajax({
            url:  "' . $CFG_GLPI["root_doc"] . '/plugins/linesmanager/ajax/getTooltip.php",
            success: function(html) {
                $("#' . $box_tooltip_id . '").html(html);
            },
            method: "POST",
            data: {value: $("#' . $dropdown_id . '").val(), ' . substr($json_attribute, 1, strlen($json_attribute) - 2) . '}
        });';
        
        if (isset($attribute_fk['on_change'])) {
            $param['on_change'] .= $attribute_fk['on_change'];
        }

        // print ajax dropdown
        echo Html::jsAjaxDropdown(
            $name, $dropdown_id, $CFG_GLPI["root_doc"] . '/plugins/linesmanager/ajax/getDropdownValues.php', $param
        );

        // showing tooltip
        if ($fk->canView()) {
            $tooltip_to_show = PluginLinesmanagerUtilform::getForeingkeyName($value, $attribute, $fk, 'field_tooltip', array('style' => 'fieldvalue'));

            $options['link'] = ($fk->canEdit($fk->getID())) ? $fk->getFormURLWithID($value) : null;
            echo "<span id='$box_tooltip_id'>";
            echo Html::showToolTip($tooltip_to_show, $options);
            echo "</span>";
        }

        // showing add button
        if ($fk->canCreate()) {
            // $output = "<img alt='' title=\"" . __s('Add') . "\" src='" . $CFG_GLPI["root_doc"]
            //     . "/pics/add_dropdown.png' style='cursor:pointer; margin-left:2px;' "
            //     . "onClick=\"" . Html::jsGetElementbyID('add_dropdown' . $rand) . ".dialog('open');\">";
            // $output .= Ajax::
            //     createIframeModalWindow(
            //         'add_dropdown' . $rand, $fk->getFormURL(), array('display' => false, 'title' => __('New item'))
            // );

            // $output = '<div  class="btn btn-outline-secondary" title="' . __s('Add') . '" data-bs-toggle="modal" data-bs-target="#add_' . $dropdown_id . '">';
            $output = '<div style="display: inline; vertical-align: middle;" title="' . __s('Add') . '" data-bs-toggle="modal" data-bs-target="#add_' . $dropdown_id . '">';
            $output .= Ajax::createIframeModalWindow('add_' . $dropdown_id, $fk->getFormURL(), ['display' => false]);
            $output .= "<span data-bs-toggle='tooltip'><i class='fa-fw ti ti-plus'></i><span class='sr-only'>" . __s('Add') . "</span></span>";
            $output .= '</div>';

            echo $output;
        }
        
        // showing checkbox to filter used values
        if (isset($attribute['foreingkey']['showFilterUsedValuesCheckbox'])
            and $attribute['foreingkey']['showFilterUsedValuesCheckbox'] === true
        ) {
            // inicializate session variable to control filter used values
            $checked = "";
            $_SESSION['glpi_plugin_linesmanager']['filterUsedValues' . $name] = false;

            if (isset($attribute['foreingkey']['filterUsedValues'])
                and $attribute['foreingkey']['filterUsedValues'] === true) {
                $checked = "checked";
                $_SESSION['glpi_plugin_linesmanager']['filterUsedValues' . $name] = true;
            }

            //echo "&nbsp;";
            echo "<div style='margin-top:5px;'>";
            Html::showCheckbox(array(
                "title" => __("This check box filter used values if it is checked.", "linesmanager"),
                "name" => "check_" . $name,
                "id" => "check_" . $name,
                "checked" => $checked
            ));
            echo "<label ";
            echo "title='" . __("This check box filter used values if it is checked.", "linesmanager") . "' ";
            echo "for='check_" . $name . "'>&nbsp;" . __("Filter used values", "linesmanager");
            echo "</label></div>";
            echo Html::scriptBlock('$("#check_' . $name . '").change(function() {
                $.ajax({
                    url:  "' . $CFG_GLPI["root_doc"] . '/plugins/linesmanager/ajax/setFilterUsedValues.php",
                    method: "POST",
                    data: {value: $(this).is(":checked"),field:"' . $name . '"}
                });
            });');
        }
    }

    /**
     * Return array of datas to load ajax dropdown, use this in the file
     * indicates on url param when you call Html::jsAjaxDropdown function.
     * @param type $attribute must contains: condition, field_name and field_id
     * see model params in PluginLinemanagerLine
     */
    static function getDatasToLoadAjaxDropdown($attribute = array()) {
        global $DB;

        if (!isset($attribute['foreingkey'])) {
            throw new Exception("Arguments error");
        }

        $fk = new $attribute['foreingkey']['item']();

        $field_name = $attribute['foreingkey']['field_name'];
        $field_id = $attribute['foreingkey']['field_id'];

        $field_name_q = PluginLinesmanagerUtilform::getFieldsInQuotationMarksForSql($field_name);
        $field_id_q = PluginLinesmanagerUtilform::getFieldsInQuotationMarksForSql($field_id);

        $field_name_qt = PluginLinesmanagerUtilform::getFieldsInQuotationMarksForSql($field_name, $fk::getTable());
        $field_id_qt = PluginLinesmanagerUtilform::getFieldsInQuotationMarksForSql($field_id, $fk::getTable());

        $query = "SELECT $field_id_qt, $field_name_qt ";

        $query .= "FROM " . $fk::getTable() . " ";

        $names = (is_array($attribute['foreingkey']['field_name'])) ?
            $attribute['foreingkey']['field_name'] :
            array($attribute['foreingkey']['field_name']);
        foreach ($names as $name) {
            if (PluginLinesmanagerUtilform::isForeingkey($fk->attributes[$name])) {
                $fk2 = $fk->attributes[$name]['foreingkey']['item'];
                $query .= "LEFT JOIN " . $fk2::getTable() . " ON " . $fk2::getTable() . ".id = " . $fk::getTable() . "." . $name . " ";
            }
        }

        $query .= "WHERE (" . PluginLinesmanagerUtilform::getWhereConcatStringForLike($field_name_q, $attribute['searchText'] ?? '', $fk) . ") ";

        // filter used values: need belongsTo in foreing key item
        // the value asigned is not filtered
        if ((isset($attribute['foreingkey']['filterUsedValues'])
            and $attribute['foreingkey']['filterUsedValues'] !== "false" )
            and ( (isset($attribute['foreingkey']['filterUsedValues'])
            and $attribute['foreingkey']['filterUsedValues'] === "true"
            and ! isset($attribute['foreingkey']['showFilterUsedValuesCheckbox']) )
            or ( isset($_SESSION['glpi_plugin_linesmanager']['filterUsedValues' . $attribute['field']])
            and $_SESSION['glpi_plugin_linesmanager']['filterUsedValues' . $attribute['field']] === true) )
        ) {

            $attribute['foreingkey']['condition'] .= ($attribute['foreingkey']['condition'] != "") ? " AND " : " ";
            $attribute['foreingkey']['condition'] .= "((1=1";

            foreach ($fk::$belongsTo as $item) {
                $attribute['foreingkey']['condition'] .= " AND " . $attribute['foreingkey']['field_id'] . " NOT IN "
                    . "(SELECT " . self::getItemName($fk)
                    . " FROM " . $item::getTable() . ""
                    . " WHERE " . self::getItemName($fk) . " is not null) ";
            }

            $attribute['foreingkey']['condition'] .= " )";

            $attribute['foreingkey']['condition'] .= " OR " . $attribute['foreingkey']['field_id'] . " = '" . $attribute['id'] . "'";

            $attribute['foreingkey']['condition'] .= " )";
        }

        if (isset($attribute['foreingkey']['condition']) and $attribute['foreingkey']['condition'] != "") {
            $query .= "AND " . $attribute['foreingkey']['condition'] . " ";
        }

        if (isset($attribute['foreingkey']['orderby'])) {
            $query .= "ORDER BY " . $attribute['foreingkey']['orderby'] . " ";
        } else {
            $query .= "ORDER BY     $field_name_qt ";
        }

        $query .= "LIMIT " . intval(($attribute['page'] - 1) * $attribute['page_limit']) . "," . $attribute['page_limit'];
        $result = $DB->query($query);

        // value for NULL if it's mandatory field
        $datas = ($attribute['page'] == "1" and ! PluginLinesmanagerUtilform::isMandatory($attribute)) ?
            array(array("id" => "0", "text" => Dropdown::EMPTY_VALUE)) :
            array();

        if ($DB->numrows($result)) {
            while ($record = $DB->fetchAssoc($result)) {
                $fk->fields = $record;
                $name_to_show = PluginLinesmanagerUtilform::getForeingkeyName($record[$field_id], $attribute, $fk);

                array_push(
                    $datas, array('id' => $record[$field_id], 'text' => (string) $name_to_show)
                );
            }
        }

        return $datas;
    }

    /**
     * Return the fields with SQL comas.
     * @param string|array $fields
     * @param string $table_name Table name for prefix
     * @return string
     */
    static function getFieldsInQuotationMarksForSql($fields, $table_name = "") {

        if ($table_name != "") {
            $table_name .= ".";
        }

        $string = "";
        if (is_array($fields)) {
            foreach ($fields as $key => $value) {
                $string .= ($string == "") ? "$table_name`$value`" : ",$table_name`$value`";
            }
        } else {
            $string = "$table_name`$fields`";
        }

        return $string;
    }

    static function getWhereConcatStringForLike($fields_coma_separated, $search_text, $item) {
        $string = "";

        $fields = explode(",", $fields_coma_separated);
        $and = false;
        foreach ($fields as $key => $value) {

            if ($and) {
                $string .= " OR";
            }

            $value_no_quoted = str_replace("`", "", $value);
            $value = $item::getTable() . "." . $value;
            if ($search_text == "") {
                $ornull = "OR ($value is null)";
            }

            if (PluginLinesmanagerUtilform::isForeingkey($item->attributes[$value_no_quoted])) {
                $item2 = $item->attributes[$value_no_quoted]['foreingkey']['item'];
                $value = $item2::getTable() . "." . $item->attributes[$value_no_quoted]['foreingkey']['field_name'];
            }

            $string .= " ($value like '$search_text%') $ornull ";
            $and = true;
        }

        return $string;
    }

    /**
     * Check if the attribute is mandatory.
     * @param array $attribute
     * @return boolean
     */
    static private function isMandatory($attribute) {
        if (isset($attribute['mandatory']) and $attribute['mandatory']) {
            return true;
        }

        return false;
    }

    /**
     * Check if the attribute is foreing key.
     * @param array $attribute
     * @return boolean
     */
    static public function isForeingkey($attribute) {
        if (isset($attribute['foreingkey'])) {
            return true;
        }

        return false;
    }

    /**
     * Return the name of the item without plugin name prefix and in lowercase.
     * @example For "PluginLinemanagerLine" returns "line".
     * @param CommonDBTM $item
     */
    static public function getItemName(CommonDBTM $item) {
        $name = get_class($item);
        $name = str_replace(static::$plugin_name, "", $name);
        return strtolower($name);
    }

    /**
     * Return javascript to display the scroll, on the element searched by id,
     * when is necesary.
     * @param string $id The ID of the table.
     * @param int $pixels Max. pixels to show the scroll. If window size is 
     * larger than this the scroll is hidden.
     * @return string Javascript code.
     */
    static public function getJsTableDisplayScroll($id, $pixels = 1660) {
        return "$( window ).resize(function() {"
            . "if ($(this).width() < $pixels) {"
            . "$('#$id').css('overflow', 'auto').css('display', 'block')"
            . "} else {"
            . "$('#$id').css('overflow', 'hidden').css('display', 'table')"
            . "}"
            . "}).resize();";
    }

}
