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
 * Author:  Javier Samaniego García <jsamaniegog@gmail.com>
 * Created: 17-nov-2017
 */

ALTER TABLE `glpi_plugin_linesmanager_lines` ADD `locations_id` INT(11) NOT NULL DEFAULT '0' AFTER `entities_id`;

UPDATE `glpi_plugin_linesmanager_lines` l INNER JOIN `glpi_computers` c         SET l.`locations_id` = c.locations_id WHERE l.itemtype = 'Computer' and l.items_id = c.id;
UPDATE `glpi_plugin_linesmanager_lines` l INNER JOIN `glpi_phones` c            SET l.`locations_id` = c.locations_id WHERE l.itemtype = 'Phone' and l.items_id = c.id;
UPDATE `glpi_plugin_linesmanager_lines` l INNER JOIN `glpi_peripherals` c       SET l.`locations_id` = c.locations_id WHERE l.itemtype = 'Peripheral' and l.items_id = c.id;
UPDATE `glpi_plugin_linesmanager_lines` l INNER JOIN `glpi_networkequipments` c SET l.`locations_id` = c.locations_id WHERE l.itemtype = 'NetworkEquipment' and l.items_id = c.id;
UPDATE `glpi_plugin_linesmanager_lines` l INNER JOIN `glpi_softwares` c         SET l.`locations_id` = c.locations_id WHERE l.itemtype = 'Software' and l.items_id = c.id;

UPDATE `glpi_plugin_linesmanager_lines` l 
INNER JOIN `glpi_plugin_simcard_simcards` s ON l.itemtype = 'PluginSimcardSimcard' and l.items_id = s.id
INNER JOIN `glpi_plugin_simcard_simcards_items` si ON s.id = si.plugin_simcard_simcards_id
INNER JOIN `glpi_computers` c ON si.itemtype = 'Computer' and si.items_id = c.id
SET l.`locations_id` = c.locations_id;
UPDATE `glpi_plugin_linesmanager_lines` l 
INNER JOIN `glpi_plugin_simcard_simcards` s ON l.itemtype = 'PluginSimcardSimcard' and l.items_id = s.id
INNER JOIN `glpi_plugin_simcard_simcards_items` si ON s.id = si.plugin_simcard_simcards_id
INNER JOIN `glpi_phones` c ON si.itemtype = 'Phone' and si.items_id = c.id
SET l.`locations_id` = c.locations_id;
UPDATE `glpi_plugin_linesmanager_lines` l 
INNER JOIN `glpi_plugin_simcard_simcards` s ON l.itemtype = 'PluginSimcardSimcard' and l.items_id = s.id
INNER JOIN `glpi_plugin_simcard_simcards_items` si ON s.id = si.plugin_simcard_simcards_id
INNER JOIN `glpi_peripherals` c ON si.itemtype = 'Peripheral' and si.items_id = c.id
SET l.`locations_id` = c.locations_id;
UPDATE `glpi_plugin_linesmanager_lines` l 
INNER JOIN `glpi_plugin_simcard_simcards` s ON l.itemtype = 'PluginSimcardSimcard' and l.items_id = s.id
INNER JOIN `glpi_plugin_simcard_simcards_items` si ON s.id = si.plugin_simcard_simcards_id
INNER JOIN `glpi_networkequipments` c ON si.itemtype = 'NetworkEquipment' and si.items_id = c.id
SET l.`locations_id` = c.locations_id;
UPDATE `glpi_plugin_linesmanager_lines` l 
INNER JOIN `glpi_plugin_simcard_simcards` s ON l.itemtype = 'PluginSimcardSimcard' and l.items_id = s.id
INNER JOIN `glpi_plugin_simcard_simcards_items` si ON s.id = si.plugin_simcard_simcards_id
INNER JOIN `glpi_softwares` c ON si.itemtype = 'Software' and si.items_id = c.id
SET l.`locations_id` = c.locations_id;
