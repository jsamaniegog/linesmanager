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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

/**
 * Description of webservice
 *
 * @author Javier Samaniego García <jsamaniegog@gmail.com>
 */
class PluginLinesmanagerWebservice {

    /**
     * 
     * @param type $help_fields
     * @param type $params
     * @return boolean true if all it's ok, else an response (fault) ready to be 
     * encode (see PluginWebservicesMethodCommon::Error)
     */
    static function checkParams($help_fields, $params) {
        foreach ($help_fields as $key => $value) {
            $options_fields = explode(",", $value);

            $function = "is_" . $options_fields[0];

            if (isset($params[$key]) && $function($params[$key])) {
                return PluginWebservicesMethodCommon::Error(
                        $protocol, WEBSERVICES_ERROR_BADPARAMETER, '', $key . ' should be a ' . $options_fields[0]
                );
            }

            if ($options_fields[1] == "mandatory" and ! isset($params[$key])) {
                return PluginWebservicesMethodCommon::Error(
                        $protocol, WEBSERVICES_ERROR_MISSINGPARAMETER, '', $params[$key]
                );
            }
        }

        return true;
    }

    /**
     * Method for import XML by webservice
     *
     * @param array $params array ID of the agent
     * @param string $protocol value the communication protocol used
     * @return array
     *
     * */
    static function methodGetLines($params, $protocol) {
        global $DB;
        
        $help_fields = array(
            'name'        => 'string,optional',
            'surname'     => 'string,optional',
            'description' => 'string,optional',
            'number'      => 'integer,optional',
            'location'    => 'integer,optional',
            'page'        => 'integer,optional'
        );

        if (isset($params['help'])) {
            return array_merge(
                $help_fields, array('help' => 'bool,optional')
            );
        }

        /*if (!Session::getLoginUserID()) {
            return PluginWebservicesMethodCommon::Error($protocol, WEBSERVICES_ERROR_NOTAUTHENTICATED);
        }*/

        if ($check = self::checkParams($help_fields, $params) !== true) {
            return $check;
        }

        // construct where
        $where = "";
        
        if (isset($params['name'])) {
            $where .= " AND l.name like '%" . $params['name'] . "%' ";
        }
        if (isset($params['surname'])) {
            $where .= " AND l.surname like '%" . $params['surname'] . "%' ";
        }
        if (isset($params['description'])) {
            $where .= " AND l.description like '%" . $params['description'] . "%' ";
        }
        if (isset($params['number'])) {
            $where .= " AND (np.number like '" . $params['number'] . "%' OR lg_np.number like '" . $params['number'] . "%')";
        }
        if (isset($params['location'])) {
            $where .= " AND CONCAT(COALESCE(";
            foreach (PluginLinesmanagerUtilsetup::getAssets() as $key => $itemtype) {
                if ($key != 0) {
                    $where .= ",";
                }
                $where .= $itemtype::getTable() . "_location.completename ";
            }
            $where .= ")) like '%" . $params['location'] . "%' ";
        }
        $page = 0;
        if (isset($params['page'])) {
            $page = trim($params['page']);
            $page = ($page < 0) ? 0 : $page ;
            $page = $page * 20;
        }

        // construct the query
        $query = "SELECT l.itemtype, l.name, l.surname, l.description, np.number,  lg_np.number as linegroup ";
        $query .= ", CONCAT(COALESCE(";
        foreach (PluginLinesmanagerUtilsetup::getAssets() as $key => $itemtype) {
            if ($key != 0) {
                $query .= ",";
            }
            $query .= $itemtype::getTable() . "_location.completename ";
        }
        $query .= ")) as location ";
        $query .= "FROM " . PluginLinesmanagerLine::getTable() . " l ";
        foreach (PluginLinesmanagerUtilsetup::getAssets() as $itemtype) {
            $query .= "LEFT JOIN " . $itemtype::getTable() . " ON l.items_id = " . $itemtype::getTable() . ".id "
                . "AND l.itemtype = '" . $itemtype . "' ";
            $query .= "LEFT JOIN " . Location::getTable() 
                . " as " . $itemtype::getTable() . "_location "
                . " ON " . $itemtype::getTable() . ".locations_id = " . $itemtype::getTable() . "_location.id ";
        }
        $query .= "LEFT JOIN " . PluginLinesmanagerNumplan::getTable() . " np ON l.numplan = np.id ";
        $query .= "LEFT JOIN " . PluginLinesmanagerLinegroup::getTable() . " lg ON l.linegroup = lg.id ";
        $query .= "LEFT JOIN " . PluginLinesmanagerNumplan::getTable() . " lg_np ON lg.numplan = lg_np.id ";
        $query .= "WHERE np.number is not null $where ";
        $query .= "LIMIT $page, 20";
        
        //echo $query;

        // result
        $resp = array();
        foreach ($DB->request($query) as $data) {
            $resp[] = $data;
        }

        return $resp;
    }

}
