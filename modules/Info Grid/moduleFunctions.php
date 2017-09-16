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

function getInfoGrid($connection2, $guid)
{
    $output = '';

    //Discover role category
    $category = strtolower(getRoleCategory($_SESSION[$guid]['gibbonRoleIDCurrent'], $connection2));

    try {
        $data = array();
        $sql = 'SELECT infoGridEntry.* FROM infoGridEntry WHERE ' . $category . '=\'Y\' ORDER BY priority DESC, title';
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) {
        $output .= "<div class='error'>".$e->getMessage().'</div>';
    }

    if ($result->rowCount() < 1) {
        $output .= "<div class='error'>";
        $output .= __($guid, 'There are no records to display.');
        $output .= '</div>';
    } else {
        $count = 0;

        if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage.php') == true) {
            $output .= "<div class='linkTop'>";
            $output .= "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Info Grid/infoGrid_manage.php'>".__($guid, 'Edit')."<img style='margin: 0 0 -4px 5px' title='".__($guid, 'Edit')."' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/config.png'/></a> ";
            $output .= '</div>';
        }

        $output .= "<table class='noIntBorder' cellspacing='0' style='width: 100%'>";
        while ($row = $result->fetch()) {
            if ($count % 2 == 0) {
                $output .= '<tr>';
            }

            $output .= "<td style='text-align: center; width: 50%'>";
            $output .= "<div style='margin: 10px 0'>";
            $target = '';
            if (substr($row['url'], 0, strlen($_SESSION[$guid]['absoluteURL'])) != $_SESSION[$guid]['absoluteURL']) {
                $target = "target='_blank'";
            }

            $size = 'width: 335px; height: 140px' ;
            if ($category == 'parent')
                $size = 'width: 239px; height: 100px' ;
            $output .= "<a style='font-size: 130%; font-weight: normal; letter-spacing: -0.3px;' href='".$row['url']."' $target>";
            if ($row['logo'] != '') {
                $output .= "<img class='user' style='margin-bottom: 10px; $size' src='".$_SESSION[$guid]['absoluteURL'].'/'.$row['logo']."'/>";
            } else {
                $output .= "<img class='user' style='margin-bottom: 10px; $size' src='".$_SESSION[$guid]['absoluteURL']."/modules/Info Grid/img/anonymous.jpg'/>";
            }
            $output .= $row['title'];
            $output .= '</a>';
            $output .= '</div>';
            $output .= '</td>';

            if ($count % 2 == 1) {
                $output .= '</tr>';
            }

            ++$count;
        }
        if ($count % 2 == 1) {
            $output .= "<td style='text-align: center; width: 50%'></td></tr>";
        }
        $output .= '</table>';
    }

    $output .= "<div class='linkTop'>";
    $output .= "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Info Grid/infoGrid_credits.php'>".__($guid, 'Credits & Licensing')."</a> ";
    $output .= '</div>';

    return $output;
}
