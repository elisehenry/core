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

function getFeed($connection2, $guid, $gibbonPersonID)
{
    $output = '';

    $category = getRoleCategory($_SESSION[$guid]['gibbonRoleIDCurrent'], $connection2);
    $output .= '<p>';
        if ($category == "Staff") {
            $output .= __($guid, 'Shown below is a list of the most recent 20 posts, drawn from your own website, and that of class and student websites for your form groups.') ;
        } else if ($category == "Student") {
            $output .= __($guid, 'Shown below is a list of the most recent 20 posts, drawn from your own website.') ;
        } else if ($category == "Parent") {
            $output .= __($guid, 'Shown below is a list of the most recent 20 posts, drawn from your child\'s website and their class website.') ;
        }
    $output .= '</p>';

    $output .= '<script type=\'text/javascript\'>
        $(document).ready(function(){
            $(\'#feedOuter-' . $gibbonPersonID . '\').load(\'' . $_SESSION[$guid]['absoluteURL'] . '/modules/Feed/feed_view_ajax.php?gibbonPersonID=' .  $gibbonPersonID . '\')});
    </script>' ;

    $output .= '<div id=\'feedOuter-' . $gibbonPersonID . '\' style=\'width: 100%\'>' ;
        $output .= "<div style='text-align: center; width: 100%; margin-top: 5px'>";
            $output .= "<img style='margin: 10px 0 5px 0' src='".$_SESSION[$guid]['absoluteURL']."/themes/Default/img/loading.gif' alt='".__($guid, 'Loading')."' onclick='return false;' /><br/>";
            $output .= __($guid, 'Loading');
        $output .= '</div>';
    $output .='</div>' ;

    return $output;
}
