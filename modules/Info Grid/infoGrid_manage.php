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

@session_start();

//Module includes
include './modules/Info Grid/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > </div><div class='trailEnd'>Manage Info Grid</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Set pagination variable
    $page = null;
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    }
    if ((!is_numeric($page)) or $page < 1) {
        $page = 1;
    }

    $search = null;
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }

    echo "<h2 class='top'>";
    echo 'Search';
    echo '</h2>';
    ?>
	<form method="get" action="<?php echo $_SESSION[$guid]['absoluteURL']?>/index.php">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr>
				<td>
					<b>Search For</b><br/>
					<span style="font-size: 90%"><i>Title</i></span>
				</td>
				<td class="right">
					<input name="search" id="search" maxlength=20 value="<?php echo $search ?>" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td colspan=2 class="right">
					<input type="hidden" name="q" value="/modules/<?php echo $_SESSION[$guid]['module'] ?>/infoGrid_manage.php">
					<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
					<?php
                    echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.$_SESSION[$guid]['module']."/infoGrid_manage.php'>Clear Search</a> "; ?>
					<input type="submit" value="Submit">
				</td>
			</tr>
		</table>
	</form>

	<?php
    echo "<h2 class='top'>";
    echo 'View';
    echo '</h2>';

    try {
        $data = array();
        $sql = 'SELECT infoGridEntry.* FROM infoGridEntry ORDER BY priority DESC, title';
        if ($search != '') {
            $data = array('search1' => "%$search%");
            $sql = 'SELECT infoGridEntry.* FROM infoGridEntry WHERE infoGridEntry.title LIKE :search1 ORDER BY priority DESC, title';
        }
        $sqlPage = $sql.' LIMIT '.$_SESSION[$guid]['pagination'].' OFFSET '.(($page - 1) * $_SESSION[$guid]['pagination']);
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { echo "<div class='error'>".$e->getMessage().'</div>';
    }

    echo "<div class='linkTop'>";
    echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Info Grid/infoGrid_manage_add.php&search=$search'>".__($guid, 'Add')."<img style='margin-left: 5px' title='".__($guid, 'Add')."' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/page_new.png'/></a>";
    echo '</div>';

    if ($result->rowCount() < 1) { echo "<div class='error'>";
        echo 'There are no records to display.';
        echo '</div>';
    } else {
        if ($result->rowCount() > $_SESSION[$guid]['pagination']) {
            printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]['pagination'], 'top', "search=$search");
        }

        echo "<table cellspacing='0' style='width: 100%'>";
        echo "<tr class='head'>";
        echo "<th style='width: 180px'>";
        echo __($guid, 'Logo');
        echo '</th>';
        echo '<th>';
        echo 'Name<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Staff<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Student<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Parent<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Priority<br/>';
        echo '</th>';
        echo "<th style='width: 120px'>";
        echo 'Actions';
        echo '</th>';
        echo '</tr>';

        $count = 0;
        $rowNum = 'odd';
        try {
            $resultPage = $connection2->prepare($sqlPage);
            $resultPage->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }
        while ($row = $resultPage->fetch()) {
            if ($count % 2 == 0) {
                $rowNum = 'even';
            } else {
                $rowNum = 'odd';
            }
            ++$count;

			//COLOR ROW BY STATUS!
			echo "<tr class=$rowNum>";
            echo '<td>';
            if ($row['logo'] != '') {
                echo "<img class='user' style='width: 168px; height: 70px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$row['logo']."'/>";
            } else {
                echo "<img class='user' style='width: 168px; height: 70px' src='".$_SESSION[$guid]['absoluteURL']."/modules/Info Grid/img/anonymous.jpg'/>";
            }
            echo '</td>';
            echo '<td>';
            echo "<a href='".$row['url']."'>".$row['title'].'</a>';
            echo '</td>';
            echo '<td>';
            echo ynExpander($guid, $row['staff']);
            echo '</td>';
            echo '<td>';
            echo ynExpander($guid, $row['student']);
            echo '</td>';
            echo '<td>';
            echo ynExpander($guid, $row['parent']);
            echo '</td>';
            echo '<td>';
            echo $row['priority'];
            echo '</td>';
            echo '<td>';
            echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Info Grid/infoGrid_manage_edit.php&infoGridEntryID='.$row['infoGridEntryID']."&search=$search'><img title='Edit' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/config.png'/></a> ";
            echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Info Grid/infoGrid_manage_delete.php&infoGridEntryID='.$row['infoGridEntryID']."&search=$search'><img title='Delete' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/></a> ";
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';

        if ($result->rowCount() > $_SESSION[$guid]['pagination']) {
            printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]['pagination'], 'bottom', "search=$search");
        }
    }
}
?>
