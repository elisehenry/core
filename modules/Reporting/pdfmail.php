<?php
if (isActionAccessible($guid, $connection2,"/modules/Reporting/pdf.php")==FALSE) {
    //Acess denied
    print "<div class='error'>" ;
            print "You do not have access to this action." ;
    print "</div>" ;
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".__($guid, getModuleName($_GET['q']))."</a> > </div><div class='trailEnd'>".__($guid, 'PDF Mail').'</div>';
    echo '</div>';    
    // proceed
    // include function pages
    $modpath =  "./modules/".$_SESSION[$guid]["module"];
    include $modpath."/pdfmail_function.php" ;
    include $modpath."/function.php";

    setSessionVariables($guid, $connection2);

    $setpdf = new setpdf();
    $setpdf->setpdfInit($guid, $connection2);
    $setpdf->modpath = $modpath;

    // return page for forms
    $thisPage = 'pdfmail';
    $title = "Send PDF report to parents";

    ///////////////////////////////////////////////////////////////////////////////////////////
    // output to screen
    ///////////////////////////////////////////////////////////////////////////////////////////
    echo "<div class='instruct' id='instruct' style='display:none'>";
    echo "<div style='float:left'><strong>Instructions</strong></div>";
    echo "<div style='float:right'>";
    echo "<a href='#' onclick='instructHide()'>Hide</a>";
    echo "</div>";
    echo "<div style=clear:both></div>";
    echo "<ul>";
    echo "<li>Use this page to send PDF report to parent</li>";
    echo "<li>Select a year group from the drop down box on the right</li>";
    echo "<li>Select a class</li>";
    echo "<li>Select a report</li>";
    echo "<li>Tick boxes for students to whom you wish to send reports</li>";
    echo "<li>Click on <em>Send email</em></li>";
    echo "</ul>";
    echo "</div>";
    echo "<div id='instructShow' style='display:block;float:right' class='smalltext'>";
    echo "<a href='#' onclick='instructShow()'>Instructions</a>";
    echo "</div>";
    echo "<div style='clear:both;'></div>";

    echo "<div class = '$setpdf->class' id = 'status'>$setpdf->msg</div>";
    if ($setpdf->rollGroupID > 0) {
        navbar($guid, $connection2, $thisPage, '', $setpdf->reportID, '', $setpdf->rollGroupID, $setpdf->schoolYearID, $setpdf->yearGroupID);
    }
    $_SESSION[$guid]['sidebarExtra'] = "<div>";
    $_SESSION[$guid]['sidebarExtra'] .= chooseSchoolYear($connection2, '', $setpdf->reportID, $setpdf->schoolYearID);
    $_SESSION[$guid]['sidebarExtra'] .= chooseYearGroup($connection2, $setpdf->yearGroupID, $setpdf->schoolYearID);
    if ($setpdf->yearGroupID > 0) {
        $_SESSION[$guid]['sidebarExtra'] .= chooseRollgroup($connection2, $setpdf->rollGroupID, $setpdf->schoolYearID, $setpdf->yearGroupID);
    }
    if ($setpdf->rollGroupID > 0) {
        $_SESSION[$guid]['sidebarExtra'] .= chooseReport($connection2, '', $setpdf->reportID, $setpdf->rollGroupID, $setpdf->schoolYearID, '', $setpdf->yearGroupID);
    }
    $_SESSION[$guid]['sidebarExtra'] .= "</div><div style = 'clear:both;'>&nbsp;</div>";
    if ($setpdf->rollGroupID > 0 && $setpdf->reportID > 0) {
        $_SESSION[$guid]['sidebarExtra'] .= $setpdf->chooseLeft();
    }

    echo "<div>&nbsp</div>";
    echo "<div>";
        $setpdf->mainform($guid, $connection2);
    echo "</div>";
}