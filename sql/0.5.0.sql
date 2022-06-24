INSERT INTO `glpi_plugin_linesmanager_configs`(type, value) VALUES ('fill_line_information',     '0');

ALTER TABLE `glpi_plugin_linesmanager_forwards`  ADD `typeforward` VARCHAR(20) NOT NULL DEFAULT 'all' COMMENT 'all, busy, no answer'  AFTER `name`;

ALTER TABLE `glpi_plugin_linesmanager_linegroups` ADD `forward` INT NOT NULL AFTER `numplan`, ADD `forwardtimeout` TIME NULL DEFAULT NULL AFTER `forward`, ADD `timeslot` INT NOT NULL AFTER `forwardtimeout`;