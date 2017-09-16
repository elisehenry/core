<?php
//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql = array();
$count = 0;

//v1.0.00
$sql[$count][0] = '1.0.00';
$sql[$count][1] = '-- First version, nothing to update';

//v1.0.01
++$count;
$sql[$count][0] = '1.0.01';
$sql[$count][1] = '';

//v1.0.02
++$count;
$sql[$count][0] = '1.0.02';
$sql[$count][1] = '';

//v1.0.03
++$count;
$sql[$count][0] = '1.0.03';
$sql[$count][1] = '';

//v1.0.04
++$count;
$sql[$count][0] = '1.0.04';
$sql[$count][1] = '';

//v1.0.05
++$count;
$sql[$count][0] = '1.0.05';
$sql[$count][1] = '';

//v2.0.00
++$count;
$sql[$count][0] = '2.0.00';
$sql[$count][1] = "
UPDATE gibbonModule SET name='Info Grid', description='Offers school-defined image-grids of links to useful resources, with access based on role category (staff, student, parent).', entryURL='infoGrid_manage.php' WHERE name='Staff Handbook';end
RENAME TABLE staffHandbookEntry TO infoGridEntry;end
ALTER TABLE `infoGridEntry` CHANGE `staffHandbookEntryID` `infoGridEntryID` INT(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;end
ALTER TABLE `infoGridEntry` ADD `staff` ENUM('N','Y') NOT NULL DEFAULT 'Y' AFTER `title`, ADD `student` ENUM('N','Y') NOT NULL DEFAULT 'Y' AFTER `staff`, ADD `parent` ENUM('N','Y') NOT NULL DEFAULT 'Y' AFTER `student`;end
UPDATE `gibbonAction` SET `category` = 'Info Grid', `URLList` = 'infoGrid_credits.php', `entryURL` = 'infoGrid_credits.php' WHERE `gibbonaction`.`gibbonActionID` = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end
UPDATE `gibbonAction` SET `name` = 'View Info Grid', `category` = 'Info Grid', `description` = 'Allows a user to view the Info Grid within the homepage dashboard.', `URLList` = 'infoGrid_view.php', `entryURL` = 'infoGrid_view.php' WHERE `gibbonaction`.`gibbonActionID` = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end
UPDATE `gibbonAction` SET `name` = 'View Info Grid', `category` = 'Info Grid', `description` = 'Info Grid', `URLList` = 'infoGrid_manage.php, infoGrid_manage_add.php, infoGrid_manage_edit.php, infoGrid_manage_delete.php', `entryURL` = 'infoGrid_manage.php' WHERE `gibbonaction`.`gibbonActionID` = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end
UPDATE gibbonHook SET name='Staff Information', options='a:3:{s:16:\"sourceModuleName\";s:9:\"Info Grid\";s:18:\"sourceModuleAction\";s:14:\"View Info Grid\";s:19:\"sourceModuleInclude\";s:31:\"hook_dashboard_infoGridView.php\";}' WHERE name='Staff Handbook' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end
INSERT INTO `gibbonHook` (`name`, `type`, `options`, `gibbonModuleID`) VALUES ('Student Information', 'Student Dashboard', 'a:3:{s:16:\"sourceModuleName\";s:9:\"Info Grid\";s:18:\"sourceModuleAction\";s:14:\"View Info Grid\";s:19:\"sourceModuleInclude\";s:31:\"hook_dashboard_infoGridView.php\";}', (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid'));end
INSERT INTO `gibbonHook` (`name`, `type`, `options`, `gibbonModuleID`) VALUES ('Parent Information', 'Parental Dashboard', 'a:3:{s:16:\"sourceModuleName\";s:9:\"Info Grid\";s:18:\"sourceModuleAction\";s:14:\"View Info Grid\";s:19:\"sourceModuleInclude\";s:31:\"hook_dashboard_infoGridView.php\";}', (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid'));end
UPDATE `gibbonAction` SET `defaultPermissionStudent` = 'Y', `defaultPermissionParent` = 'Y', `defaultPermissionSupport` = 'Y', `categoryPermissionStudent` = 'Y', `categoryPermissionParent` = 'Y', `categoryPermissionOther` = 'Y' WHERE name='View Info Grid' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '3', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Info Grid' AND gibbonAction.name='View Info Grid'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '4', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Info Grid' AND gibbonAction.name='View Info Grid'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '6', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Info Grid' AND gibbonAction.name='View Info Grid'));end
UPDATE `gibbonAction` SET menuShow = 'N' WHERE name='View Info Grid' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end
UPDATE `gibbonAction` SET menuShow = 'N' WHERE name='Credits & Licenses' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end
UPDATE `gibbonAction` SET name = 'Manage Info Grid', category='Info Grid' WHERE name='Manage Staff Handbook' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end";

//v2.0.01
++$count;
$sql[$count][0] = '2.0.01';
$sql[$count][1] = '';

//v2.0.02
++$count;
$sql[$count][0] = '2.0.02';
$sql[$count][1] = '';

//v2.0.03
++$count;
$sql[$count][0] = '2.0.03';
$sql[$count][1] = '';

//v2.0.04
++$count;
$sql[$count][0] = '2.0.04';
$sql[$count][1] = '';

//v2.0.05
++$count;
$sql[$count][0] = '2.0.05';
$sql[$count][1] = "
UPDATE `gibbonAction` SET menuShow = 'N' WHERE name='View Info Grid' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name='Info Grid');end
";

//v2.0.06
++$count;
$sql[$count][0] = '2.0.06';
$sql[$count][1] = '';
