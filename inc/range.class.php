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

class PluginLinesmanagerRange extends CommonDBTM {

    static $rightname = 'entity';
    
    /**
     * model attributes 
     * @var array
     */
    public $attributes = array();

    function __construct() {
        parent::__construct();

        $this->attributes = array(
            'entities_id' => array('name' => 'entities_id', 'hidden' => true, 'type' => 'number'),
            'name' => array('name' => __("Name", "linesmanager"), 'mandatory' => true, 'type' => 'string'),
            'min_number' => array('name' => __("Range from", "linesmanager"), 'mandatory' => true, 'type' => 'number'),
            'max_number' => array('name' => __("To", "linesmanager"), 'mandatory' => true, 'type' => 'number'),
            'only_pickup' => array('name' => __("Only pickup groups", "linesmanager"), 'mandatory' => true, 'type' => 'bool')
        );
    }
    
    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     * */
    static function getTypeName($nb = 1) {
        return _n('Range', 'Ranges', $nb, 'linesmanager');
    }

    public function validateArguments($arguments) {
        if (!is_numeric($arguments['min_number']) or !is_numeric($arguments['max_number'])) {
            return __("The vlaues must be numbers.", "linesmanager");
        }
        if ($arguments['min_number'] > $arguments['max_number']) {
            return __("Number 'from' must be less or equal than 'to'.", "linesmanager");
        }
        if ($arguments['min_number'] < 0 or $arguments['max_number'] < 0) {
            return __("Numbers must be greater than 0.", "linesmanager");
        }

        $condition = "((min_number <= " . $arguments['min_number'] . " "
            . " AND max_number >= " . $arguments['min_number'] . ") "
            . " OR (min_number <= " . $arguments['max_number'] . " "
            . " AND max_number >= " . $arguments['max_number'] . ") "
            . " OR (min_number >= " . $arguments['min_number'] . " "
            . " AND max_number <= " . $arguments['max_number'] . "))";
        
        if (isset($arguments['id'])) {
            $condition = "id != " . $arguments['id'] . " AND " . $condition;
        }
        
        $ranges = $this->find(
            [$condition], "id", "1"
        );
        
        if (count($ranges) != 0) {

            $entity_id = array_values($ranges)[0]['entities_id'];

            $entity = new Entity();
            $entity->getFromDB($entity_id);

            return __("The specified range is already in use by: ", "linesmanager")
                . Html::link(
                    $entity->fields['completename'], Entity::getFormURLWithID($entity_id))
                . ".";
        }


        return true;
    }

    function showForm($ID, $options = array()) {
        global $DB;

        // check rights
        if (!Session::haveRight("entity", READ)) {
            return false;
        }
        
        // get data
        $this->getFromDB($ID);
        
        echo '<div id="div_form_range">';
        
        // hack to load ID -1 without Notice in logs and for hidden id of entity
        $this->fields['id'] = $ID;
        $this->fields['entities_id'] = ($ID == -1) ? $options['entities_id'] : $this->fields['entities_id'] ;
        $this->fields['is_recursive'] = (isset($this->fields['is_recursive'])) ? $this->fields['is_recursive'] : 0;
        
        // show header table and init form
        $this->showFormHeader($options);
        
        // show fields by row
        PluginLinesmanagerUtilform::showHtmlRowFormFields($this->attributes, $this->fields);

        // show form buttons
        $this->showFormButtons($options);
        
        echo '</div>';
        
        // closing tags
        Html::closeForm();
    }
    
    function showFormTab($options = array()) {
        global $CFG_GLPI;
        
        // WARNING message
        echo Html::image($CFG_GLPI["root_doc"] . "/pics/warning.png", array('alt' => __('Warning'), 'style' => 'margin-left: 50px;'));
        echo "<span style='color:orange;font-size:large;vertical-align:super;'> " 
            . __('Be careful, when you save or delete ranges the lines can be deleted.', 'linesmanager') 
            . "</span><br><br>";
        
        $this->showForm(-1, $options);
        
        PluginLinesmanagerUtilform::showHtmlFormOpen($this);
        $this->showRangesList($options);
        Html::closeForm();
    }
    
    function showRangesList($options) {
        $range = new PluginLinesmanagerRange();
        $ranges = $range->find(["entities_id = " . $options['entities_id'], "min_number"]);
        
        $table_id = "table_ranges";
        
        echo "<table id='$table_id' class='tab_cadre_fixehov'>";
        echo "<tr class='noHover'><th colspan=5>";
        echo __("Assigned ranges", 'linesmanager');
        echo "</th></tr>";
        
        echo "<tr>";
        echo "<th>" . __("Name", "linesmanager") . "</th>";
        echo "<th>" . __("From", "linesmanager") . "</th>";
        echo "<th>" . __("To", "linesmanager") . "</th>";
        echo "<th>" . __("Only pickup groups", "linesmanager") . "</th>";
        echo "<th>" . __("Child entities") . "</th>";
        if (Session::haveRight("entity", UPDATE)) {
            echo "<th width='10%'>" . Html::submit(_x('button','Delete permanently'), array('name' => 'purge', 'confirm' => __("Are you sure you want to delete the selected records ?, all lines with these numbers will be deleted.", "linesmanager")));
        }
        echo "</tr>";
        
        foreach ($ranges as $id => $range) {
            $range['only_pickup'] = ($range['only_pickup'] == 1) ? __('Yes') : __('No') ;
            $range['is_recursive'] = ($range['is_recursive'] == 1) ? __('Yes') : __('No') ;
            echo "<tr id='$id'>";
            echo "<td align=center>" . $range['name'] . "</td>";
            echo "<td align=center>" . $range['min_number'] . "</td>";
            echo "<td align=center>" . $range['max_number'] . "</td>";
            echo "<td align=center>" . $range['only_pickup'] . "</td>";
            echo "<td align=center>" . $range['is_recursive'] . "</td>";
            if (Session::haveRight("entity", UPDATE)) {
                echo "<td align=center><input type='checkbox' name='range[]' value='$id'></td></tr>";
            }
        }
        echo "</table>";
        
        // javascript to load form when a row of the table is clicked
        echo Html::scriptBlock(
            PluginLinesmanagerUtilform::getJsAjaxRowClickLoadForm($table_id, $this)
        );
    }
    
    function pre_deleteItem() {
        global $DB;
        
        $query = "SELECT l.id "
            . "FROM " . PluginLinesmanagerLine::getTable() . " l, " . PluginLinesmanagerNumplan::getTable() . " n "
            . "WHERE l.numplan=n.id AND n.range = " . $this->input['id'];
        
        if ($result = $DB->query($query)) {
            if ($DB->numrows($result) != 0) {
                return false;
            }
        }
        
        return true;
    }
}
