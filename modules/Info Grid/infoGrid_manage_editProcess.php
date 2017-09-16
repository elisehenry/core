<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include '../../functions.php';
include '../../config.php';

include './moduleFunctions.php';

//New PDO DB connection
try {
    $connection2 = new PDO("mysql:host=$databaseServer;dbname=$databaseName", $databaseUsername, $databasePassword);
    $connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

@session_start();

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]['timezone']);

$infoGridEntryID = $_GET['infoGridEntryID'];
$URL = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_POST['address'])."/infoGrid_manage_edit.php&infoGridEntryID=$infoGridEntryID&search=".$_GET['search'];

if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage_edit.php') == false) {
    //Fail 0
    $URL = $URL.'&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Check if school year specified
    if ($infoGridEntryID == '') {
        //Fail1
        $URL = $URL.'&return=error1';
        header("Location: {$URL}");
    } else {
        try {
            $data = array('infoGridEntryID' => $infoGridEntryID);
            $sql = 'SELECT * FROM infoGridEntry WHERE infoGridEntryID=:infoGridEntryID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            //Fail2
            $URL = $URL.'&deleteReturn=error2';
            header("Location: {$URL}");
            exit();
        }

        if ($result->rowCount() != 1) {
            //Fail 2
            $URL = $URL.'&return=error2';
            header("Location: {$URL}");
        } else {
            $row = $result->fetch();

            //Validate Inputs
            $title = $_POST['title'];
            $staff = $_POST['staff'];
            $student = $_POST['student'];
            $parent = $_POST['parent'];
            $priority = $_POST['priority'];
            $url = $_POST['url'];
            $logoLicense = $_POST['logoLicense'];

            if ($title == '' or $staff == '' or $student == '' or $parent == '' or $priority == '' or $url == '') {
                //Fail 3
                $URL = $URL.'&return=error3';
                header("Location: {$URL}");
            } else {
                $partialFail = false;
                $logo = $row['logo'];

                //Move attached image  file, if there is one
                if (!empty($_FILES['file']['tmp_name'])) {
                    $fileUploader = new Gibbon\FileUploader($pdo, $gibbon->session);
                    $fileUploader->getFileExtensions('Graphics/Design');

                    $file = (isset($_FILES['file']))? $_FILES['file'] : null;

                    // Upload the file, return the /uploads relative path
                    $logo = $fileUploader->uploadFromPost($file, 'infoGrid');

                    if (empty($logo)) {
                        $partialFail = true;
                    }
                }

                //Write to database
                try {
                    $data = array('title' => $title, 'staff'=>$staff, 'student'=>$student, 'parent'=>$parent, 'priority' => $priority, 'url' => $url, 'logo' => $logo, 'logoLicense' => $logoLicense, 'infoGridEntryID' => $infoGridEntryID);
                    $sql = 'UPDATE infoGridEntry SET title=:title, staff=:staff, student=:student, parent=:parent, priority=:priority, url=:url, logo=:logo, logoLicense=:logoLicense WHERE infoGridEntryID=:infoGridEntryID';
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    //Fail 2
                    $URL = $URL.'&return=error2';
                    header("Location: {$URL}");
                    exit();
                }

                if ($partialFail == true) {
                    $URL .= '&return=warning1';
                    header("Location: {$URL}");
                } else {
                    $URL .= "&return=success0";
                    header("Location: {$URL}");
                }
            }
        }
    }
}
