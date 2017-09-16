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

if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage_add.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/infoGrid_manage.php'>".__($guid, 'Manage Info Grid')."</a> > </div><div class='trailEnd'>".__($guid, 'Add Info Grid Entry').'</div>';
    echo '</div>';

    $returns = array();
    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Info Grid/infoGrid_manage_edit.php&infoGridEntryID='.$_GET['editID'].'&search='.$_GET['search'];
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, $returns);
    }

    if ($_GET['search'] != '') { echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Info Grid/infoGrid_manage.php&search='.$_GET['search']."'>Back to Search Results</a>";
        echo '</div>';
    }

    ?>
	<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/Info Grid/infoGrid_manage_addProcess.php?search='.$_GET['search'] ?>" enctype="multipart/form-data">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr>
				<td>
					<b>Title *</b><br/>
				</td>
				<td class="right">
					<input name="title" id="title" maxlength=100 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var title=new LiveValidation('title');
						title.add(Validate.Presence);
					</script>
				</td>
			</tr>
            <tr>
				<td>
					<b><?php echo __($guid, "Viewable To Staff?") ?> *</b><br/>
				</td>
				<td class="right">
					<select name="staff" id="staff" class="standardWidth">
						<option value="Y"><?php echo ynExpander($guid, 'Y') ?></option>
						<option value="N"><?php echo ynExpander($guid, 'N') ?></option>
					</select>
				</td>
			</tr>
            <tr>
				<td>
					<b><?php echo __($guid, "Viewable To Students?") ?> *</b><br/>
				</td>
				<td class="right">
					<select name="student" id="student" class="standardWidth">
						<option value="Y"><?php echo ynExpander($guid, 'Y') ?></option>
						<option value="N"><?php echo ynExpander($guid, 'N') ?></option>
					</select>
				</td>
			</tr>
            <tr>
				<td>
					<b><?php echo __($guid, "Viewable To Parents?") ?> *</b><br/>
				</td>
				<td class="right">
					<select name="parent" id="parent" class="standardWidth">
						<option value="Y"><?php echo ynExpander($guid, 'Y') ?></option>
						<option value="N"><?php echo ynExpander($guid, 'N') ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Priority') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'Higher priorities are displayed first.') ?></i></span>
				</td>
				<td class="right">
					<input name="priority" id="priority" maxlength=2 value="0" type="text" style="width: 300px">
					<script type="text/javascript">
						var priority=new LiveValidation('priority');
						priority.add(Validate.Presence);
						priority.add(Validate.Numericality);
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Link') ?> *</b><br/>
				</td>
				<td class='right'>
					<input name='url' id='url' maxlength=255 value='' type='text' style='width: 300px'>
					<script type='text/javascript'>
						url=new LiveValidation('url');
						url.add(Validate.Presence);
						url.add( Validate.Format, { pattern: /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/, failureMessage: 'Must start with http://' } );
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b>Logo</b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, '335px x 140px') ?></i></span>
				</td>
				<td class="right">
					<input type="file" name="file" id="file">
					<script type="text/javascript">
						var file=new LiveValidation('file');
						file.add( Validate.Inclusion, { within: ['gif','jpg','jpeg','png'], failureMessage: "Illegal file type!", partialMatch: true, caseSensitive: false } );
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b>Logo License/Credits</b><br/>
				</td>
				<td class="right">
					<textarea name='logoLicense' id='logoLicense' rows=5 style='width: 300px'></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<span style="font-size: 90%"><i>* denotes a required field</i></span>
				</td>
				<td class="right">
					<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
					<input type="submit" value="Submit">
				</td>
			</tr>
		</table>
	</form>
	<?php

}
?>
