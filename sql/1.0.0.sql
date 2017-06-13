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
/*CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_profiles` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `profiles_id` int(11) NOT NULL default 0, 
    `right` int(11) NOT NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;*/

CREATE TABLE `glpi_plugin_linesmanager_configs` (
        `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `type` varchar(32) NOT NULL default '' UNIQUE,
        `value` varchar(32) NOT NULL default ''
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
INSERT INTO `glpi_plugin_linesmanager_configs`(type, value) VALUES ('automate_description', '1');
INSERT INTO `glpi_plugin_linesmanager_configs`(type, value) VALUES ('automate_user_id',     '1');

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_ranges` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `entities_id` int(11) NOT NULL default 0, 
    `is_recursive` tinyint(1) NOT NULL default 0,
    `name` varchar(100) NOT NULL default '',
    `min_number` int(11) NOT NULL,
    `max_number` int(11) NOT NULL,
    `only_pickup` tinyint(1) default '0'
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `glpi_plugin_linesmanager_ranges` ADD UNIQUE(`min_number`, `max_number`);
ALTER TABLE `glpi_plugin_linesmanager_ranges` ADD UNIQUE(`min_number`);
ALTER TABLE `glpi_plugin_linesmanager_ranges` ADD UNIQUE(`max_number`);

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_numplans` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `number` int(11) NOT NULL UNIQUE default 0,
    `range` integer default NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_lines` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `items_id` int(11) default NULL,
    `itemtype` varchar(100) default NULL,
    `entities_id` int(11) NOT NULL default 0,
    `name` varchar(200) NOT NULL default '',
    `surname` varchar(200) NOT NULL default '',
    `numplan` integer NOT NULL,
    `description` varchar(200) default '',
    `user_id` varchar(50) NOT NULL default '',
    `linegroup` integer default NULL,
    `pickupgroup` integer default NULL,
    `category` integer default NULL,
    `extensionmobility` integer default NULL,
    `loginout` tinyint(1) default '0',
    `autoanswer` tinyint(1) default '0',
    `autoanswerpass` varchar(20) default '0',
    `lockcallin` tinyint(1) default '0',
    `lockcallout` tinyint(1) default '0',
    `forward` integer default NULL,
    `forwardtimeout` time default null,
    `timeslot` integer default null,
    `ddiin` integer default NULL,
    `ddiout` integer default NULL,
    `vip` tinyint(1) default '0'
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_categories` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    /*`name` ENUM('0','1','2','3','4','5','6','7','8','9') UNIQUE NOT NULL, */
    `name` varchar(100) NOT NULL default '',
    `description` VARCHAR(200) NOT NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_extensionmobilities` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `loginduration` time default '08:00:00',  /*at week cisco default value*/
    `description` VARCHAR(200) NOT NULL,
    `category` integer default NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_forwards` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `numplan` integer default NULL,
    `category` integer default NULL,
    `other` VARCHAR(100) default NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_timeperiods` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    `description` VARCHAR(200) NOT NULL,
    `time_start` time default '00:08:00',
    `time_end` time default '00:15:00',
    `dayofweek_start` ENUM('1','2','3','4','5','6','7'),
    `dayofweek_end` ENUM('1','2','3','4','5','6','7'),
    `dayofmonth_start` ENUM('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'),
    `dayofmonth_end` ENUM('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'),
    `monthofyear_start` ENUM('1','2','3','4','5','6','7','8','9','10','11','12'),
    `monthofyear_end` ENUM('1','2','3','4','5','6','7','8','9','10','11','12')
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_timeslots` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `description` VARCHAR(200) NOT NULL,
    `timeperiod` integer default NULL,
    `category` integer default NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_linegroups` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `name` varchar(200) NOT NULL default '',
    `algorithm` integer NOT NULL default 0,
    `numplan` integer NOT NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_algorithms` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `name` varchar(200) NOT NULL,
    `description` varchar(250) NOT NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `glpi_plugin_linesmanager_algorithms`(name, description) VALUES ('Longest Idle Time', '(default value)If you choose this distribution algorithm, Cisco Unified CM only distributes a call to idle members, starting from the longest idle member to the least idle member of a line group.');
INSERT INTO `glpi_plugin_linesmanager_algorithms`(name, description) VALUES ('Top Down', 'If you choose this distribution algorithm, Cisco Unified CM distributes a call to idle or available members starting from the first idle or available member of a line group to the last idle or available member.');
INSERT INTO `glpi_plugin_linesmanager_algorithms`(name, description) VALUES ('Circular', 'If you choose this distribution algorithm, Cisco Unified CM distributes a call to idle or available members starting from the (n+1)th member of a route group, where the nth member is the next sequential member in the list who is either idle or busy but not "down." If the nth member is the last member of a route group, Cisco Unified CM distributes a call starting from the top of the route group.');
INSERT INTO `glpi_plugin_linesmanager_algorithms`(name, description) VALUES ('Broadcast', 'If you choose this distribution algorithm, Cisco Unified CM distributes a call to all idle or available members of a line group simultaneously. See the Note in the description of the Selected DN/Route Partition field for additional limitations in using the Broadcast distribution algorithm.');

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_pickupgroups` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `name` varchar(200) NOT NULL default '',
    `numplan` integer NOT NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_linesmanager_ddis` ( 
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT , 
    `name` varchar(200) NOT NULL default '',
    `description` varchar(200) default '',
    `numplan` integer NOT NULL,
    `other` VARCHAR(100) default NULL
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;