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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start();

//Gibbon system-wide includes
include '../../functions.php';
include '../../config.php';

//New PDO DB connection
$pdo = new Gibbon\sqlConnection();
$connection2 = $pdo->getConnection();

//CURL MULTI
require_once './src/Library/RollingCurl/RollingCurl.php';
require_once './src/Library/RollingCurl/Request.php';

$output = '';

$category = getRoleCategory($_SESSION[$guid]['gibbonRoleIDCurrent'], $connection2);

$gibbonPersonID = null ;
if (isset($_GET['gibbonPersonID'])) {
    $gibbonPersonID = $_GET['gibbonPersonID'] ;
}

if (is_null($gibbonPersonID) or $gibbonPersonID=='') {
    echo "<div class='error'>";
    echo __($guid, 'You have not specified one or more required parameters.');
    echo '</div>';
}
else {
    $feeds = array();
    $feedCount = 0;
    $names = array() ;
    $namesCount = 0 ;
    if ($category == "Staff") {
        //My own site
        if ($_SESSION[$guid]['website'] != '') {
            if ($_SESSION[$guid]['website']!='') {
                $feeds[$feedCount] = $_SESSION[$guid]['website'] . '?feed=rss2' ;
                $feedCount ++ ;
                $names[$namesCount][0] = $_SESSION[$guid]['website'] ;
                $names[$namesCount][1] = formatName('', $_SESSION[$guid]['preferredName'], $_SESSION[$guid]['surname'], 'Student', false) ;
                $namesCount ++ ;
            }
        }
        //Student sites from my class(es)
        try {
            $data = array('gibbonPersonIDTutor' => $_SESSION[$guid]['gibbonPersonID'], 'gibbonPersonIDTutor2' => $_SESSION[$guid]['gibbonPersonID'], 'gibbonPersonIDTutor3' => $_SESSION[$guid]['gibbonPersonID']);
            $sql = "SELECT gibbonPerson.website, surname, preferredName
                FROM gibbonPerson
                    JOIN gibbonStudentEnrolment ON (gibbonStudentEnrolment.gibbonPersonID=gibbonPerson.gibbonPersonID AND gibbonStudentEnrolment.gibbonSchoolYearID=(SELECT gibbonSchoolYearID FROM gibbonSchoolYear WHERE status='Current'))
                    JOIN gibbonRollGroup ON (gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID)
                WHERE (gibbonPersonIDTutor=:gibbonPersonIDTutor OR gibbonPersonIDTutor2=:gibbonPersonIDTutor2 OR gibbonPersonIDTutor3=:gibbonPersonIDTutor3)
                    AND gibbonPerson.status='Full'";
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
        }
        while ($row=$result->fetch()) {
            if ($row['website']!='') {
                $feeds[$feedCount] = $row['website'] . '?feed=rss2' ;
                $feedCount ++ ;
                $names[$namesCount][0] = $row['website'] ;
                $names[$namesCount][1] = formatName('', $row['preferredName'], $row['surname'], 'Student', false) ;
                $namesCount ++ ;
            }
        }

        //Class sites from my class(es)
        try {
            $data = array('gibbonPersonIDTutor' => $_SESSION[$guid]['gibbonPersonID'], 'gibbonPersonIDTutor2' => $_SESSION[$guid]['gibbonPersonID'], 'gibbonPersonIDTutor3' => $_SESSION[$guid]['gibbonPersonID']);
            $sql = "SELECT gibbonRollGroup.website, gibbonRollGroup.name
                FROM gibbonRollGroup
                WHERE (gibbonPersonIDTutor=:gibbonPersonIDTutor OR gibbonPersonIDTutor2=:gibbonPersonIDTutor2 OR gibbonPersonIDTutor3=:gibbonPersonIDTutor3)
                    AND gibbonRollGroup.gibbonSchoolYearID=(SELECT gibbonSchoolYearID FROM gibbonSchoolYear WHERE status='Current')
                ";
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) { print $e->getMessage() ; }
        while ($row=$result->fetch()) {
            if ($row['website']!='') {
                $feeds[$feedCount] = $row['website'] . '?feed=rss2' ;
                $feedCount ++ ;
                $names[$namesCount][0] = $row['website'] ;
                $names[$namesCount][1] = $row['name'] ;
                $namesCount ++ ;
            }
        }
    } else if ($category == "Student") {
        //My own site
        if ($_SESSION[$guid]['website'] != '') {
            if ($_SESSION[$guid]['website']!='') {
                $feeds[$feedCount] = $_SESSION[$guid]['website'] . '?feed=rss2' ;
                $feedCount ++ ;
                $names[$namesCount][0] = $_SESSION[$guid]['website'] ;
                $names[$namesCount][1] = formatName('', $_SESSION[$guid]['preferredName'], $_SESSION[$guid]['surname'], 'Student', false) ;
                $namesCount ++ ;
            }
        }
    } else if ($category == "Parent") {
        try {
            $data = array('gibbonPersonIDParent' => $_SESSION[$guid]['gibbonPersonID'], 'gibbonPersonIDChild' => $gibbonPersonID);
            $sql = "SELECT gibbonPerson.website AS websitePersonal, gibbonRollGroup.website AS websiteClass, preferredName, surname
                FROM gibbonFamilyAdult
                    JOIN gibbonFamily ON (gibbonFamilyAdult.gibbonFamilyID=gibbonFamily.gibbonFamilyID)
                    JOIN gibbonFamilyChild ON (gibbonFamilyChild.gibbonFamilyID=gibbonFamily.gibbonFamilyID)
                    JOIN gibbonPerson ON (gibbonFamilyChild.gibbonPersonID=gibbonPerson.gibbonPersonID)
                    LEFT JOIN gibbonStudentEnrolment ON (gibbonStudentEnrolment.gibbonPersonID=gibbonPerson.gibbonPersonID AND gibbonStudentEnrolment.gibbonSchoolYearID=(SELECT gibbonSchoolYearID FROM gibbonSchoolYear WHERE status='Current'))
                    LEFT JOIN gibbonRollGroup ON (gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID)
                WHERE childDataAccess='Y'
                    AND gibbonFamilyAdult.gibbonPersonID=:gibbonPersonIDParent
                    AND gibbonPerson.gibbonPersonID=:gibbonPersonIDChild
                    AND gibbonPerson.status='Full'
                ";
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) { }

        if ($result->rowCount() == 1) {
            $row=$result->fetch() ;
            if ($row['websitePersonal'] != '') {
                if ($row['websitePersonal']!='') {
                    $feeds[$feedCount] = $row['websitePersonal'] . '?feed=rss2' ;
                    $feedCount ++ ;
                }
            }
            if ($row['websiteClass'] != '') {
                if ($row['websiteClass']!='') {
                    $feeds[$feedCount] = $row['websiteClass'] . '?feed=rss2' ;
                    $feedCount ++ ;
                }
            }
            $names[$namesCount][0] = $row['websitePersonal'] ;
            $names[$namesCount][1] = formatName('', $row['preferredName'], $row['surname'], 'Student', false) ;
            $namesCount ++ ;
        }
    }

    if ($feedCount==0) {
        echo "<div class='error'>";
        echo __($guid, 'There are no records to display.');
        echo '</div>';
    }
    else {
        $entries = array();

        //DO THE MULTI STUFF
        $rollingCurl = new \RollingCurl\RollingCurl();
        foreach ($feeds as $feed) {
            $rollingCurl->get($feed);
        }
        $entries = array();
        $start = microtime(true);
        $rollingCurl->setSimultaneousLimit(30)->execute();
        $rollingCurlCount = 0;
        foreach ($rollingCurl->getCompletedRequests() as $request) {
            $rss = new DOMDocument();
            if ($request->getResponseText() != '' && $rss->loadXML($request->getResponseText()) != false ) {
                foreach ($rss->getElementsByTagName('item') as $node) {
                    $entries[$rollingCurlCount]['title'] = $node->getElementsByTagName('title')->item(0)->nodeValue ;
                    $entries[$rollingCurlCount]['pubDate'] = $node->getElementsByTagName('pubDate')->item(0)->nodeValue ;
                    $entries[$rollingCurlCount]['description'] = $node->getElementsByTagName('description')->item(0)->nodeValue ;
                    $entries[$rollingCurlCount]['link'] = $node->getElementsByTagName('link')->item(0)->nodeValue ;
                    $entries[$rollingCurlCount]['comments'] = $node->getElementsByTagName('comments')->item(0)->nodeValue ;

                    $rollingCurlCount ++;
                }
            }
        }

        // Sort feed entries by pubDate (ascending)
        usort($entries, function ($x, $y) {
            return  strtotime($y['pubDate']) - strtotime($x['pubDate']);
        });

        $entries=array_slice($entries, 0, 20) ;

        if (count($entries)<1) {
            echo "<div class='error'>";
            echo __($guid, 'There are no records to display.');
            echo '</div>';
        }
        else {
        $count = 0;
            foreach ($entries as $item) {
                $output .= "<h2 class='bigTop'>" . $item['title'] . "</h2>" ;
                $output .= "<p>" ;
                    $output .= '<span class=\'small emphasis\'>' . substr($item['pubDate'], 0, 17) ;
                    foreach ($names as $studentName) {
                        if (strpos($item['link'], $studentName[0]) !== false) {
                            $output .=  " | " . __($guid, "by") . " " . $studentName[1] ;
                        }
                    }
                    $output .= "</span><br/><br/>" ;
                    $output .= str_replace(' [&#8230;]', '...',strip_tags($item['description'])) . " " . "<a target='_blank' href='" . $item['link'] . "'>" . __($guid, "Read on site") . "</a>" ;
                    if ($item['comments']!='') {
                        $output .= " | <a target='_blank' href='" . $item['comments'] . "'>" . __($guid, "Leave A Comment") . "</a>" ;
                    }
                $output .= "</p>" ;
                $count++ ;
            }
        }
    }
}

echo $output;
