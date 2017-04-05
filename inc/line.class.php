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

class PluginLinesmanagerLine extends CommonDropdown {

    /**
     * If do history.
     * @var bool 
     */
    public $dohistory = true;

    /**
     * Name for profile rights.
     * @var type 
     */
    static $rightname = 'plugin_linesmanager_line';

    /**
     * model attributes 
     * @var array
     */
    public $attributes = array();

    /**
     * Visualization options.
     * @var array 
     */
    public $view_options = array();

    /**
     * Contains a SQL condition to load numplan combobox.
     * @var type 
     */
    protected $condition_to_load_numplan;

    /**
     * Overwrite parent function.
     * @param type $options
     */
    /* static function dropdown($options=array()) {
      $class = get_called_class();
      $options['name'] = $class::$fieldDefaultToShowInDropdown;
      //parent::dropdown($options);
      PluginLinesmanagerUtilform::showHtmlInputFieldAccordingType($attribute, $values);
      } */

    /**
     * Constructor. Load model attributes.
     */
    function __construct() {
        parent::__construct();

        // only show numbers assigned to active entities
        $this->condition_to_load_numplan = "`range` in (SELECT id "
            . "FROM " . PluginLinesmanagerRange::getTable() . " "
            . "WHERE entities_id in (" . implode(",", $_SESSION['glpiactiveentities']) . "))";

        // form width 2 columns
        $this->view_options = array(
            'columns' => '2'
        );

        $this->attributes = array(
            'id' => array('name' => 'ID', 'hidden' => true),
            'numplan' => array('name' => __("Number", "linesmanager"),
                'add' => false,
                'mandatory' => true,
                /** possible types:
                  case 'count' :
                  case 'number' :
                  if (!isset($searchopt[$field_num]['min'])
                  && !isset($searchopt[$field_num]['max'])) {
                  unset($opt['equals']);
                  unset($opt['notequals']);
                  }
                  case 'bool' :
                  case 'right' :
                  case 'itemtypename' :
                  case 'date' :
                  case 'datetime' :
                  case 'date_delay' :
                 */
                'type' => 'dropdown',
                //'checktype'       => 'text';
                'displaytype' => 'dropdown',
                'forcegroupby' => true,
                'massiveaction' => false,
                'foreingkey' => array(
                    'item' => PluginLinesmanagerNumplan,
                    'condition' => $this->condition_to_load_numplan,
                    'filterUsedValues' => true,
                    'showFilterUsedValuesCheckbox' => true,
                    'field_id' => 'id',
                    'field_name' => 'number',
                    'field_tooltip' => 'number'
                )
            ),
            'name' => array('name' => __("Name", "linesmanager"), 'mandatory' => true),
            'surname' => array('name' => __("Surname", "linesmanager"), 'mandatory' => true),
            'description' => array('name' => __("Description", "linesmanager"), 'mandatory' => true),
            'user_id' => array('name' => __("User ID", "linesmanager"), 'mandatory' => true),
            'category' => array(
                'name' => PluginLinesmanagerCategory::getTypeName(),
                'mandatory' => true,
                'default' => 4,
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerCategory,
                    'field_id' => 'id',
                    'field_name' => array('name', 'description'),
                    'field_tooltip' => 'description',
                //'string_format_name' => PluginLinesmanagerCategory::getTypeName() . ' %d',
                //'string_format_tooltip' => PluginLinesmanagerCategory::getTypeName() . ' %d',
                )
            ),
            'loginout' => array('name' => __("Login/Logout", "linesmanager"), 'type' => 'bool'),
            'autoanswer' => array('name' => __("Autoanswer", "linesmanager"), 'type' => 'bool'),
            'autoanswerpass' => array('name' => __("Autoanswer Password", "linesmanager"), 'type' => 'numeric'),
            'lockcallin' => array('name' => __("Lock callin", "linesmanager"), 'type' => 'bool'),
            'lockcallout' => array('name' => __("Lock callout", "linesmanager"), 'type' => 'bool'),
            'linegroup' => array(
                'name' => PluginLinesmanagerLinegroup::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerLinegroup,
                    'field_id' => 'id',
                    'field_name' => 'numplan',
                    'field_tooltip' => array('name', 'algorithm')
                )
            ),
            'pickupgroup' => array(
                'name' => PluginLinesmanagerPickupgroup::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerPickupgroup,
                    'field_id' => 'id',
                    'field_name' => 'numplan',
                    'field_tooltip' => 'name'
                )
            ),
            'extensionmobility' => array(
                'name' => PluginLinesmanagerExtensionmobility::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerExtensionmobility,
                    'field_id' => 'id',
                    'field_name' => array('loginduration', 'category'),
                    'field_tooltip' => array('description', 'loginduration', 'category'),
                    'string_format' => array('category' => PluginLinesmanagerCategory::getTypeName() . ' %d')
                )
            ),
            'forward' => array(
                'name' => PluginLinesmanagerForward::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerForward,
                    'field_id' => 'id',
                    'field_name' => array('numplan', 'category', 'other'),
                    'field_tooltip' => array('numplan', 'category', 'other'),
                    'string_format' => array('category' => PluginLinesmanagerCategory::getTypeName() . ' %s')
                )
            ),
            'forwardtimeout' => array(
                'name' => __("Forward timeout", "linesmanager"),
                'type' => 'specific',
                'specifictype' => 'time',
                'minutes' => false,
                'hours' => false
            ),
            'timeslot' => array(
                'name' => PluginLinesmanagerTimeslot::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerTimeslot,
                    'field_id' => 'id',
                    'field_name' => array('timeperiod', 'category'),
                    'field_tooltip' => array('description', 'timeperiod', 'category'),
                    'string_format' => array('category' => PluginLinesmanagerCategory::getTypeName() . ' %s')
                )
            ),
            'ddiin' => array(
                'name' => __("Input DDI", "linesmanager"),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerDdi,
                    'field_id' => 'id',
                    'field_name' => 'numplan',
                    'field_tooltip' => array('name', 'description')
                )
            ),
            'ddiout' => array(
                'name' => __("Output DDI", "linesmanager"),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => PluginLinesmanagerDdi,
                    'field_id' => 'id',
                    'field_name' => 'numplan',
                    'field_tooltip' => array('name', 'description')
                )
            )
        );
    }

    /**
     * Check $_POST arguments, and if haven't rights show a message after redirect
     * and go back.
     * @return boolean If all it's ok return true.
     */
    static function checkPostArgumentsPermissions() {
        if (isset($_POST['remove'])
            or isset($_POST['associate'])
            or isset($_POST['update'])
            or isset($_POST['add'])
            or isset($_POST['purge'])) {

            if ((!PluginLinesmanagerLine::canUpdate() and ( isset($_POST['remove'])
                or isset($_POST['associate'])
                or isset($_POST['update'])))

                or ( !PluginLinesmanagerLine::canCreate() and isset($_POST['add']))

                or ( !PluginLinesmanagerLine::canPurge() and isset($_POST['purge']))
            ) {
                Session::addMessageAfterRedirect(__("No permission", "linesmanager"), false, ERROR);
                HTML::back();
            }

            return true;
        }

        return false;
    }

    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     * */
    static function getTypeName($nb = 1) {
        return _n('Line', 'Lines', $nb, 'linesmanager');
    }

    /**
     * View parent.
     * @param type $field
     * @param type $values
     * @param array $options
     * @return type
     */
    static function getSpecificValueToSelect($field, $name, $values, array $options = array()) {
        // todo: no funciona para modificación masiva
        switch ($field) {
            case 'forwardtimeout' :
                return PluginLinesmanagerUtilform::dropdownTime('forwardtimeout', '', array('display' => false));
        }

        return parent::getSpecificValueToDisplay($field, $values, $options);
    }

    /**
     * View parent.
     * @return array
     */
    function getSearchOptions($i = 1, $prefix = "") {

        $tab = array();

        $tab['common'] = _n('Line', 'Lines', $nb, 'linesmanager');

        //$i = 1;
        foreach ($this->attributes as $dbfield_name => $attribute) {
            // hack to don't show ID in asset search
            if ($i === PluginLinesmanagerUtilsetup::RESERVED_TYPE_RANGE_MIN) {
                $i++;
                continue;
            }

            // first all values that match with tab array
            $tab[$i] = $attribute;

            if ($prefix != "") {
                $tab[$i]['name'] = $prefix . " - " . $tab[$i]['name'];
            }

            // for dropdowns
            if (PluginLinesmanagerUtilform::isForeingkey($attribute)) {

                // table to get data
                $tab[$i]['table'] = $attribute['foreingkey']['item']::getTable();

                // field we want to show
                if (is_array($attribute['foreingkey']['field_name'])) {
                    $tab[$i]['field'] = $attribute['foreingkey']['field_name'][0];
                } else {
                    $tab[$i]['field'] = $attribute['foreingkey']['field_name'];
                }

                // field that contains the id
                //$tab[$i]['linkfield'] = $attribute['foreingkey']['field_id'];
                $tab[$i]['linkfield'] = $dbfield_name;

                // dropdown conditions
                $tab[$i]['condition'] = (isset($attribute['foreingkey']['condition'])) ?
                    $attribute['foreingkey']['condition'] :
                    "";
            } else {
                // normal fields
                $tab[$i]['table'] = $this->getTable();
                $tab[$i]['field'] = $dbfield_name;
            }

            // field datatype (first one is alwais itemlink)
            $attribute['type'] = (!isset($attribute['type'])) ? 'string' : $attribute['type'];
            $tab[$i]['datatype'] = ($i == 1) ? "itemlink" : $attribute['type'];

            $i++;
        }

        return $tab;
    }

    /**
     * View parent.
     * @param CommonGLPI $item
     * @param type $withtemplate
     * @return type
     */
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        $array_ret = array();
        if ($item->getID() > -1) {
            if (self::canView()) {
                $array_ret[0] = self::createTabEntry(PluginLinesmanagerLine::getTypeName(Session::getPluralNumber()));
            }
        }
        return $array_ret;
    }

    /**
     * View parent.
     * @param CommonGLPI $item
     * @param type $tabnum
     * @param type $withtemplate
     * @return boolean
     */
    static function displayTabContentForItem(CommonDBTM $item, $tabnum = 1, $withtemplate = 0) {
        $line = new PluginLinesmanagerLine();

        // opening form
        if (self::canUpdate()) {
            PluginLinesmanagerUtilform::showHtmlFormOpen($line);
        }

        $condition = "itemtype = '" . $item->getType() . "' AND items_id = " . $item->getID();

        // show list of associated records
        PluginLinesmanagerUtilform::showHtmlList(
            "table_lines", $line, $condition, array('purge')
        );

        echo Html::scriptBlock(
            PluginLinesmanagerUtilform::getJsTableDisplayScroll("table_lines", 1690)
        );

        // closing form
        if (self::canUpdate()) {
            Html::closeForm();
        }

        // if don't find records show the form to add one
        PluginLinesmanagerUtilform::showHtmlDivToLoadFormByAjax("line", "", false);
        if (!$line->find($condition, "", 1)) {
            $options['item'] = $item;
            $line->showForm(-1, $options);
        }
        PluginLinesmanagerUtilform::showHtmlDivClose();

        // boton para mostrar el formulario de añadir elemento
        if (self::canCreate()) {
            echo sprintf(
                ' <input class="submit" type="button" value="'
                . __("New line", "linesmanager")
                . '" name="%1$s" %2$s>', Html::cleanInputText('new'), Html::parseAttributes(
                    array(
                        'name' => 'new',
                        'onClick' => PluginLinesmanagerUtilform::getJsAjaxLoadForm(
                            $line, -1, array(
                            'items_id' => $item->getID(),
                            'itemtype' => $item->getType()
                        ))
                    )
                )
            );
        }

        // todo: solo funciona la primera vez que se carga el formulario, es decir cuando no hay líneas asociadas
        // functionalities only for this screen
        self::showJsInterfaceFunctions($item);

        return true;
    }

    /**
     * Show lines form to add or edit.
     * @global type $DB
     * @param type $id
     * @param array $options
     *  - item: if is set the form contents three hidden variables with id of
     * item and the item type.
     * @return boolean
     */
    function showForm($id, $options = array()) {
        global $DB;

        if (!self::canUpdate()) {
            return false;
        }

        // get data
        $this->getFromDB($id);

        $options['formoptions'] = "id='form_linesmanager'";

        $this->showFormHeader($options);

        if (isset($options['item'])) {
            // hidden fields from item
            // entities_id is prints by showFormHeader
            //echo Html::hidden("entities_id", array('value' => $options['item']->getEntityID()));
            echo Html::hidden("itemtype", array('value' => $options['item']->getType()));
            echo Html::hidden("items_id", array('value' => $options['item']->getID()));
        }

        // show fields by row
        PluginLinesmanagerUtilform::showHtmlRowFormFields($this->attributes, $this->fields, $this->view_options);

        // showFormButtons close the form
        $this->showFormButtons($options);

        return true;
    }

    /**
     * Print javascript to do some funcionalities.
     * @param array $options Must recive: "item" and "params" in the array.
     */
    private static function showJsInterfaceFunctions($item) {
        // autocomplete user_id
        $js = "$('#form_linesmanager [name=\"numplan\"]').change(function() {"
            . "    $('#form_linesmanager input[name=\"user_id\"]').val('" . self::getUserIdPrefix($item->fields['entities_id']) . "' + $(this).select2('data').text);"
            . "});";

        // autocomplete description
        $js .= "$('#form_linesmanager input[name=\"name\"],#form_linesmanager input[name=\"surname\"]').change(function() {"
            . "    $('#form_linesmanager input[name=\"description\"]').val($('#form_linesmanager input[name=\"name\"]').val() + ' ' + $('#form_linesmanager input[name=\"surname\"]').val());"
            . "});";

        echo Html::scriptBlock($js);
    }

    /**
     * Build a prefix with the name of entity.
     * @param integer $entities_id ID of the entity.
     * @return string
     */
    static function getUserIdPrefix($entities_id) {
        $entity = new Entity();
        $entity->getFromDB($entities_id);
        list($user_id_prefix) = explode("(", $entity->fields['name']);
        return substr(str_replace(" ", "", $user_id_prefix), 0, 10);
    }

    /**
     * Check the arguments before update.
     * @param type $args
     * @return bool|string True if all its ok, else the error
     */
    function checkArguments($arguments) {
        foreach ($this->attributes as $dbfield_name => $attribute) {
            if (isset($attribute['readOnly']))
                continue;

            if (isset($attribute['mandatory'])
                and $attribute['mandatory'] === true
                and trim($arguments[$dbfield_name]) == ""
            ) {
                return __('Mandatory field: ', 'linesmanager') . $attribute['name'];
            }

            if (isset($attribute['type'])
                and $attribute['type'] == 'number'
                and ! is_numeric($arguments[$dbfield_name])
            ) {
                return __('Numeric field: ', 'linesmanager') . $attribute['name'];
            }
        }

        return true;
    }

    function delete($input, $force = 0, $history = 1) {
        global $DB;

        // search and update to null the records with the foreing key deleted
        if (isset($this::$belongsTo)) {
            foreach ($this::$belongsTo as $item) {
                $item = new $item();
                $query = "UPDATE " . $item->getTable();
                $query .= " SET " . PluginLinesmanagerUtilform::getItemName($this);
                $query .= " = ";
                $query .= "NULL";
                $query .= " WHERE ";
                $query .= PluginLinesmanagerUtilform::getItemName($this);
                $query .= " = ";
                $query .= "'" . $input[self::getIndexName()] . "'";

                $DB->query($query);
            }
        }

        return parent::delete($input, $force, $history);
    }

    static function count($condition = "") {
        global $DB;
        $query = "SELECT count(*) as count FROM " . self::getTable();

        if ($condition != "") {
            $query .= " WHERE " . $condition;
        }

        $result = $DB->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Actions done after the ADD of the item in the database
     *
     * @return nothing
     * */
    function post_addItem() {
        $this->updateContactInformation();
    }

    /**
     * Actions done after the UPDATE of the item in the database
     *
     * @param $history store changes history ? (default 1)
     *
     * @return nothing
     * */
    function post_updateItem($history = 1) {
        $this->updateContactInformation();
    }

    /**
     * This function update the contact information of the itemtype liked.
     */
    function updateContactInformation() {
        global $DB;
        
        if (isset($this->fields['itemtype'])
            and in_array($this->fields['itemtype'], PluginLinesmanagerUtilsetup::getAssets())
        ) {
            $itemtype = $this->fields['itemtype'];
            
            $query = "SELECT description, n.number, lg_n.number as linegroup "
                . "FROM glpi_plugin_linesmanager_lines l "
                . "LEFT JOIN glpi_plugin_linesmanager_numplans n ON l.numplan = n.id "
                . "LEFT JOIN glpi_plugin_linesmanager_linegroups lg ON l.linegroup = lg.id "
                . "LEFT JOIN glpi_plugin_linesmanager_numplans lg_n ON lg.numplan = lg_n.id "
                . "WHERE itemtype='$itemtype' AND items_id=" . $this->fields['items_id'];
            
            $contact     = "";
            $contact_num = "";
            $rows = $DB->request($query);
            $has_number = false;
            foreach ($rows as $data) {
                if ($has_number === false) {
                    $has_number = true;
                } else {
                    // separator between contacts and contacts numbers
                    $contact     .= ",";
                    $contact_num .= ",";
                }
                
                $contact     .= $data['description'];
                $contact_num .= $data['number'];
            }
            
            $has_linegroup = false;
            foreach ($rows as $data) {
                if (is_numeric($data['linegroup'])) {
                    if ($has_linegroup === false) {
                        // separator between number and linegroup
                        $contact_num .= " / ";
                        $has_linegroup = true;
                    } else {
                        // separator between linegroups
                        $contact_num .= ",";
                    }
                    
                    $contact_num .= $data['linegroup'];
                }
            }
            
            if ($contact != "" and $contact_num != "") {
                $query  = "UPDATE " . $itemtype::getTable() . " ";
                $query .= "SET contact='$contact', contact_num='$contact_num' ";
                $query .= "WHERE id=". $this->fields['items_id'];

                $DB->query($query);
            }
        }
    }

}
