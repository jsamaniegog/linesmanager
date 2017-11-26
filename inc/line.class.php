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
     * @var string 
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
     * @var string 
     */
    protected $condition_to_load_numplan;

    /**
     * Number of columns in PDF export with PDF plugin. Must be >= 1.
     * @var int
     */
    static private $columns_in_pdf = 2;
    
    /**
     * Constructor. Load model attributes.
     */
    function __construct() {
        parent::__construct();

        // only show numbers assigned to active entities
        $this->condition_to_load_numplan = "`range` in (SELECT id "
            . "FROM " . PluginLinesmanagerRange::getTable() . " "
            . "WHERE ( entities_id in (" . implode(",", $_SESSION['glpiactiveentities']) . ") "
            . "OR id in (SELECT r.id "
            . " FROM `glpi_plugin_linesmanager_ranges` r, `glpi_entities` e "
            . " WHERE r.entities_id = e.id "
            . "     AND r.is_recursive = 1 "
            . "     AND (e.sons_cache like '%" . '\"' . $_SESSION['glpiactive_entity'] . '\"' . "%' OR e.entities_id = -1) ) ) "
            . "AND only_pickup=0)";

        // form width 2 columns
        $this->view_options = array(
            'columns' => '2'
        );

        $this->attributes = array(
            'id' => array('name' => 'ID', 'hidden' => true),
            'numplan' => array('name' => __("Number", "linesmanager"),
                'add' => false,
                'mandatory' => true,
                'type' => 'dropdown',
                'displaytype' => 'dropdown',
                'forcegroupby' => true,
                'massiveaction' => false,
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerNumplan',
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
            'user_id' => array('name' => __("User ID", "linesmanager")),
            'category' => array(
                'name' => PluginLinesmanagerCategory::getTypeName(),
                'mandatory' => true,
                //'default' => 4,
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerCategory',
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
                    'item' => 'PluginLinesmanagerLinegroup',
                    'field_id' => 'id',
                    'field_name' => array('numplan', 'name'),
                    'field_tooltip' => array('name', 'algorithm')
                )
            ),
            'pickupgroup' => array(
                'name' => PluginLinesmanagerPickupgroup::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerPickupgroup',
                    'field_id' => 'id',
                    'field_name' => 'numplan',
                    'field_tooltip' => 'name'
                )
            ),
            'extensionmobility' => array(
                'name' => PluginLinesmanagerExtensionmobility::getTypeName(),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerExtensionmobility',
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
                    'item' => 'PluginLinesmanagerForward',
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
                    'item' => 'PluginLinesmanagerTimeslot',
                    'field_id' => 'id',
                    'field_name' => array('timeperiod', 'category'),
                    'field_tooltip' => array('description', 'timeperiod', 'category'),
                    'string_format' => array('category' => PluginLinesmanagerCategory::getTypeName() . ' %s')
                )
            ),
            'vip' => array('name' => "V.I.P.", 'type' => 'bool'),
            'ddiin' => array(
                'name' => __("Input DDI", "linesmanager"),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerDdi',
                    'field_id' => 'id',
                    'field_name' => array('numplan', 'other'),
                    'field_tooltip' => array('name', 'description')
                )
            ),
            'ddiout' => array(
                'name' => __("Output DDI", "linesmanager"),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerDdi',
                    'field_id' => 'id',
                    'field_name' => array('numplan', 'other'),
                    'field_tooltip' => array('name', 'description')
                )
            ),
            'locations_id' => array(
                'name' => Location::getTypeName(1),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerLocation',
                    'field_id' => 'id',
                    'field_name' => 'completename',
                    'field_tooltip' => 'comment'
                ),
                'hidden' => true,
                'getAddSearchOptions' => false
            ),
            'states_id' => array(
                'name' => State::getTypeName(1),
                'type' => 'dropdown',
                'foreingkey' => array(
                    'item' => 'PluginLinesmanagerState',
                    'field_id' => 'id',
                    'field_name' => 'completename',
                    'field_tooltip' => 'comment'
                ),
                'hidden' => true,
                'getAddSearchOptions' => false
            )
        );
        
        /*$tab[$i]['table']          = 'glpi_locations';
        $tab[$i]['field']          = 'name';
        $tab[$i]['name']           = Location::getTypeName(1);
        $tab[$i]['forcegroupby']   = true;
        $tab[$i]['datatype']       = 'itemlink';
        $tab[$i]['massiveaction']  = false;
        $tab[$i]['itemlink_type']  = 'Location';
        $tab[$i]['joinparams'] = array('jointype' => 'itemtype_item');*/
        /*$tab[$i]['joinparams']  = array(
            'beforejoin' => array(
                'table'      => 'glpi_locations',
                'joinparams' => array('jointype' => 'itemtype_item')
            )
        );*/
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
     */
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
    static function getSpecificValueToSelect($field, $name = '', $values = '', array $options = array()) {
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
    function getSearchOptions($i = 1, $prefix = "", $nb = 0) {

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
                $tab[$i]['field'] = $attribute['foreingkey']['field_name'];

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
        
        /*$tab[$i]['table']          = 'glpi_locations';
        $tab[$i]['field']          = 'name';
        $tab[$i]['name']           = Location::getTypeName(1);
        $tab[$i]['forcegroupby']   = true;
        $tab[$i]['datatype']       = 'itemlink';
        $tab[$i]['massiveaction']  = false;
        $tab[$i]['itemlink_type']  = 'Location';
        $tab[$i]['joinparams'] = array('jointype' => 'itemtype_item');*/
        /*$tab[$i]['joinparams']  = array(
            'beforejoin' => array(
                'table'      => 'glpi_locations',
                'joinparams' => array('jointype' => 'itemtype_item')
            )
        );*/

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
                $count = null;
                if ($_SESSION['glpishow_count_on_tabs']) {
                    $count = self::count("itemtype='" . get_class($item) . "' AND items_id=" . $item->getID());
                }
                $array_ret[0] = self::createTabEntry(PluginLinesmanagerLine::getTypeName(Session::getPluralNumber()), $count);
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
    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        $line = new PluginLinesmanagerLine();

        // opening form
        if (self::canUpdate()) {
            PluginLinesmanagerUtilform::showHtmlFormOpen($line);
        }

        $condition = "itemtype = '" . $item->getType() . "' AND items_id = " . $item->getID();

        // show list of associated records
        PluginLinesmanagerUtilform::showHtmlList(
            "table_lines", $line, $condition, array('purge'), array(), "$('#div_history').html('');"
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

        // div to load the history (logs)
        PluginLinesmanagerUtilform::showHtmlDivOpen("div_history", array(), true);

        // history button
        echo sprintf(
            ' <input class="submit" type="button" value="'
            . __("Historical")
            . '" name="%1$s" %2$s>', Html::cleanInputText('new'), Html::parseAttributes(
                array(
                    'name' => 'history',
                    'onClick' => PluginLinesmanagerUtilform::getJsAjaxShowHistory(
                        get_called_class(), "$('#table_lines').attr('selected_id')", "div_history"
                    )
                )
            )
        );

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

        return true;
    }

    /**
     * Show lines form to add or edit.
     * @global type $DB
     * @param type $ID
     * @param array $options
     *  - item: if is set the form contents three hidden variables with id of
     * item and the item type.
     * @return boolean
     */
    function showForm($ID, $options = array()) {
        global $DB;

        if (!self::canUpdate()) {
            return false;
        }

        // get data
        $this->getFromDB($ID);

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

        // todo: solo funciona la primera vez que se carga el formulario, es decir cuando no hay líneas asociadas
        // functionalities only for this screen
        self::showJsInterfaceFunctions($this);

        return true;
    }

    /**
     * Print javascript to do some funcionalities.
     * @param array $options Must recive: "item" and "params" in the array.
     */
    private static function showJsInterfaceFunctions($item) {
        $config_datas = PluginLinesmanagerConfig::getConfigData();

        // autocomplete user_id
        if ($config_datas['automate_user_id']) {
            $js = "$('#form_linesmanager[action*=\"line.form.php\"] [name=\"numplan\"]').change(function() {"
                . "    $('#form_linesmanager input[name=\"user_id\"]').val('" . self::getUserIdPrefix($item->fields['entities_id']) . "' + $(this).select2('data').text);"
                . "});";
        }

        // autocomplete description
        if ($config_datas['automate_description']) {
            $js .= "$('#form_linesmanager[action*=\"line.form.php\"] input[name=\"name\"],#form_linesmanager[action*=\"line.form.php\"] input[name=\"surname\"]').change(function() {"
                . "    $('#form_linesmanager input[name=\"description\"]').val($('#form_linesmanager input[name=\"name\"]').val() + ' ' + $('#form_linesmanager input[name=\"surname\"]').val());"
                . "});";
        }

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

    function delete(array $input, $force = 0, $history = 1) {
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

    private function getStringNameForHistory() {
        return "ID: " . $this->fields["id"] . ", " 
            . __('Number', 'linesmanager') . ": " 
            . PluginLinesmanagerUtilform::getForeingkeyName(
                $this->fields["numplan"], $this->attributes['numplan']
            );
    }

    /**
     * Actions done after the ADD of the item in the database
     *
     * @return nothing
     */
    function post_addItem() {
        $this->updateContactInformation();

        // logs
        $changes[0] = 0;
        $changes[1] = "";
        $changes[2] = $this->getStringNameForHistory();
        Log::history($this->fields["items_id"], $this->fields["itemtype"], $changes, __CLASS__, Log::HISTORY_ADD_SUBITEM);
    }

    /**
     * Actions done after the UPDATE of the item in the database
     *
     * @param $history store changes history ? (default 1)
     *
     * @return nothing
     * */
    function post_updateItem($history = 1) {
        $this->updateContactInformation($history);
    }

    /**
     * Actions done after the DELETE of the item in the database
     *
     * @return nothing
     * */
    function post_deleteFromDB() {
        $this->updateContactInformation();  // necesary for simcards
        
        // logs
        $changes[0] = 0;
        $changes[1] = $this->getStringNameForHistory();
        $changes[2] = "";
        Log::history($this->fields["items_id"], $this->fields["itemtype"], $changes, __CLASS__, Log::HISTORY_DELETE_SUBITEM);
    }

    /**
     * This function update the fields copied from linked item. This is
     * a hack field for searching.
     * @param $item Item modified. Example: Computer, NetworkEquipment...
     */
    static function updateFieldsFromParentItem(CommonDBTM $item) {
        global $DB;
        
        $itemtype = get_class($item);
        
        $query = "UPDATE " . self::getTable();
        
        if ($itemtype == 'PluginSimcardSimcard_Item') {
            $asset = $item->getField("itemtype");
            $asset = new $asset();
            $asset->getFromDB($item->getField("items_id"));
            
            $query .= " SET " . self::getTable() . ".locations_id = " . $asset->getField('locations_id');
            $query .= ", " . self::getTable() . ".states_id = " . $asset->getField('states_id');
            $query .= " WHERE itemtype='PluginSimcardSimcard' AND items_id=" . $item->getField("plugin_simcard_simcards_id");
        }
        
        // add from linesmanager
        if ($itemtype == 'PluginLinesmanagerLine') {
            $item->getFromDB($item->getID());
            $asset = $item->getField("itemtype");
            $asset = new $asset();
            $asset->getFromDB($item->getField("items_id"));

            if ($item->getField("itemtype") == 'PluginSimcardSimcard') {
                $sim = new PluginSimcardSimcard_Item();
                $result = $sim->find("plugin_simcard_simcards_id = " . $asset->getID());
                $result = array_values($result);
                $asset = new $result[0]['itemtype']();
                $asset->getFromDB($result[0]['items_id']);
            }
            
            $query .= " SET " . self::getTable() . ".locations_id = " . $asset->getField('locations_id');
            $query .= ", " . self::getTable() . ".states_id = " . $asset->getField('states_id');
            $query .= " WHERE id=" . $item->getID();
        }
        
        // update from an asset
        if (in_array($itemtype, PluginLinesmanagerUtilsetup::getAssets())) {
        
            if ($itemtype == 'PluginSimcardSimcard') {
                $sim = new PluginSimcardSimcard_Item();
                $result = $sim->find("plugin_simcard_simcards_id = " . $item->getID());
                $result = array_values($result);
                $asset = new $result[0]['itemtype']();
                $asset->getFromDB($result[0]['items_id']);
                
                $query .= " SET " . self::getTable() . ".locations_id = " . $asset->getField('locations_id');
                $query .= ", " . self::getTable() . ".states_id = " . $asset->getField('states_id');
                $query .= " WHERE itemtype='PluginSimcardSimcard' AND items_id=" . $item->getID();
                
            } else {    
                $query2 = "UPDATE `glpi_plugin_linesmanager_lines` l ";
                $query2 .= " INNER JOIN `glpi_plugin_simcard_simcards` s ON l.itemtype = 'PluginSimcardSimcard' and l.items_id = s.id";
                $query2 .= " INNER JOIN `glpi_plugin_simcard_simcards_items` si ON s.id = si.plugin_simcard_simcards_id";
                $query2 .= " INNER JOIN `glpi_" . strtolower($itemtype) . "s` c ON si.itemtype = '$itemtype' and si.items_id = c.id and c.id = " . $item->getID();
                $query2 .= " SET l.`locations_id` = c.locations_id, l.`states_id` = c.states_id;";
                $DB->query($query2);
                
                $query .= " SET " . self::getTable() . ".locations_id = " . $item->getField('locations_id');
                $query .= ", " . self::getTable() . ".states_id = " . $asset->getField('states_id');
                $query .= " WHERE itemtype='$itemtype' AND items_id=" . $item->getID();
            }
        }
        
        $DB->query($query);
    }
    
    /**
     * This function update the contact information of the itemtype liked.
     */
    function updateContactInformation($history = 1) {
        $config_datas = PluginLinesmanagerConfig::getConfigData();
        if ($config_datas['fill_contact_information'] != 1) {
            return;
        }

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

            $contact = "";
            $contact_num = "";
            $rows = $DB->request($query);
            $has_number = false;
            foreach ($rows as $data) {
                if ($has_number === false) {
                    $has_number = true;
                } else {
                    // separator between contacts and contacts numbers
                    $contact .= ", ";
                    $contact_num .= ", ";
                }

                $contact .= $data['description'];
                $contact_num .= $data['number'];
            }

            $has_linegroup = false;
            foreach ($rows as $data) {
                if (is_numeric($data['linegroup']) and ( !isset($previous) or ! in_array($data['linegroup'], $previous))) {
                    if ($has_linegroup === false) {
                        // separator between number and linegroup
                        $contact_num .= " / ";
                        $has_linegroup = true;
                    } else {
                        // separator between linegroups
                        $contact_num .= ", ";
                    }

                    $contact_num .= $data['linegroup'];

                    $previous[] = $data['linegroup'];
                }
            }

            // hack for simcard plugin
            if ($itemtype == 'PluginSimcardSimcard') {
                $sc = new PluginSimcardSimcard_Item();
                if ($sc->getFromDBByQuery("WHERE plugin_simcard_simcards_id = " . $this->fields['items_id'])) {
                    $itemtype = $sc->fields['itemtype'];
                    $this->fields['items_id'] = $sc->fields['items_id'];
                } else {
                    $contact = "";
                    $contact_num = "";
                }
            }

            if ($contact != "" and $contact_num != "") {
                // for logs
                if ($history == 1) {
                    $query = "SELECT contact, contact_num ";
                    $query .= "FROM " . $itemtype::getTable() . " ";
                    $query .= "WHERE id=" . $this->fields['items_id'];

                    if ($result = $DB->query($query)) {
                        $data = $result->fetch_assoc();
                    }
                }

                $query = "UPDATE " . $itemtype::getTable() . " ";
                $query .= "SET contact='$contact', contact_num='$contact_num' ";
                $query .= "WHERE id=" . $this->fields['items_id'];

                $DB->query($query);

                // logs
                if ($history == 1 and isset($data)) {
                    if ($data['contact'] != $contact) {
                        $this->logHistory(
                            $itemtype, $this->fields['items_id'], 'contact', $data['contact'], $contact
                        );
                    }

                    if ($data['contact_num'] != $contact_num) {
                        $this->logHistory(
                            $itemtype, $this->fields['items_id'], 'contact_num', $data['contact_num'], $contact_num
                        );
                    }
                }
            }
        }
    }

    /**
     * Log history of a field.
     * @param type $itemtype
     * @param type $items_id
     * @param type $field
     * @param type $old_value
     * @param type $new_value
     */
    private function logHistory($itemtype, $items_id, $field, $old_value, $new_value) {
        $item = new $itemtype();
        $search_option_id = $item->getSearchOptionByField('field', $field)['id'];

        $changes[0] = $search_option_id;
        $changes[1] = $old_value;
        $changes[2] = $new_value;
        Log::history($items_id, $itemtype, $changes);
    }

    /**
     * Clean the contact information of an asset.
     * @global type $DB
     * @param type $history
     */
    function cleanContactInformation($history = 1) {
        $config_datas = PluginLinesmanagerConfig::getConfigData();
        if ($config_datas['fill_contact_information'] != 1) {
            return;
        }

        global $DB;

        if (isset($this->fields['items_id']) and isset($this->fields['itemtype'])) {
            $itemtype = $this->fields['itemtype'];
            $items_id = $this->fields['items_id'];

            // for logs
            if ($history == 1) {
                $query = "SELECT contact, contact_num ";
                $query .= "FROM " . $itemtype::getTable() . " ";
                $query .= "WHERE id=" . $items_id;

                $result = $DB->query($query);
                $data = $result->fetch_assoc();
            }

            $query = "UPDATE " . $itemtype::getTable() . " ";
            $query .= "SET contact='', contact_num='' ";
            $query .= "WHERE id=" . $items_id;

            $DB->query($query);

            // logs
            if ($history == 1) {
                if ($data['contact'] != "") {
                    $this->logHistory(
                        $itemtype, $items_id, 'contact', $data['contact'], ""
                    );
                }

                if ($data['contact_num'] != "") {
                    $this->logHistory(
                        $itemtype, $items_id, 'contact_num', $data['contact_num'], ""
                    );
                }
            }
        }
    }

    static function displayTabContentForPDF(PluginPdfSimplePDF $pdf, CommonGLPI $item, $tab) {
        switch ($tab) {
            case '0' :
                self::pdfLines($pdf, $item);
                break;

            default :
                return false;
        }
        return true;
    }

    static function pdfLines($pdf, $item) {
        $line = new PluginLinesmanagerLine();
        $condition = "itemtype = '" . $item->getType() . "' AND items_id = " . $item->getID();
        $records = $line->find($condition);

        //$line->getFromDBByQuery("WHERE itemtype='Phone' AND items_id=" . $item->fields['id']);

        $pdf->setColumnsSize(100);

        $pdf->displayTitle('<b>' . $line->getTypeName(2) . ' (' . (string) count($records) . ')</b>');

        $pdf->setColumnsSize(50, 50);

        foreach ($records as $id => $record) {

            $string = array();
            $fields_counter = 0;
            
            foreach (PluginLinesmanagerUtilform::getFieldsValue($line, $record) as $dbfield_name => $value) {

                $field_name = $line->attributes[$dbfield_name]['name'];

                $css_style = '';
                if ($dbfield_name == 'numplan' or $dbfield_name == 'linegroup') {
                    $css_style = 'color:#3366ff;';
                }
                
                $string[] =  ''
                    . sprintf(
                        __('<b><i>%1$s:&nbsp;</b></i><span style="' . $css_style . '">%2$s</span>'), $field_name, $value
                );

                $fields_counter++;
                
                if ($fields_counter == self::$columns_in_pdf) {
                    call_user_func_array(array($pdf, 'displayLine'), $string);
                    
                    $string = array();
                    $fields_counter = 0;
                }
            }
            
            $pdf->displaySpace();
        }
    }

}