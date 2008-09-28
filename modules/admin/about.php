<?php
/*========================================================================
*   Open eClass 2.1
*   E-learning and Course Management System
* ========================================================================
*  Copyright(c) 2003-2008  Greek Universities Network - GUnet
*  A full copyright notice can be read in "/info/copyright.txt".
*
*  Developers Group:	Costas Tsibanis <k.tsibanis@noc.uoa.gr>
*			Yannis Exidaridis <jexi@noc.uoa.gr>
*			Alexandros Diamantidis <adia@noc.uoa.gr>
*			Tilemachos Raptis <traptis@noc.uoa.gr>
*
*  For a full list of contributors, see "credits.txt".
*
*  Open eClass is an open platform distributed in the hope that it will
*  be useful (without any warranty), under the terms of the GNU (General
*  Public License) as published by the Free Software Foundation.
*  The full license can be read in "/info/license/license_gpl.txt".
*
*  Contact address: 	GUnet Asynchronous eLearning Group,
*  			Network Operations Center, University of Athens,
*  			Panepistimiopolis Ilissia, 15784, Athens, Greece
*  			eMail: info@openeclass.org
* =========================================================================*/

/**===========================================================================
    about.php
    @last update: 31-05-2006 by Pitsiougas Vagelis
    @authors list: Karatzidis Stratos <kstratos@uom.gr>
               Pitsiougas Vagelis <vagpits@uom.gr>
==============================================================================
        @Description: About page for the administrator

     This script displays information about GUnet eClass version and about the
     server running (PHP version, Apache version, MySQL version).

     The user can : - See the information
                 - Return to main administrator page

     @Comments: The script is organised in two sections.

     1) Gather the information
  2) Display them on an HTML page

==============================================================================*/

/*****************************************************************************
        DEAL WITH LANGFILES, BASETHEME, OTHER INCLUDES AND NAMETOOLS
******************************************************************************/
// Check if user is administrator and if yes continue
// Othewise exit with appropriate message
$require_admin = TRUE;
// Include baseTheme
include '../../include/baseTheme.php';
// Define $nameTools
$nameTools = $langVersion;
$navigation[] = array("url" => "index.php", "name" => $langAdmin);
// Initialise $tool_content
$tool_content = "";
$totalHits = 0;
/*****************************************************************************
        MAIN BODY
******************************************************************************/

$sql = "SELECT COUNT(*) AS cnt FROM cours";
$result = db_query($sql);
while ($row = mysql_fetch_assoc($result)) {
    $totalCourses = $row['cnt'];
}
mysql_free_result($result);

$sql = "SELECT code FROM cours";
$result = db_query($sql);
while ($row = mysql_fetch_assoc($result)) {
    $course_codes[] = $row['code'];
}
mysql_free_result($result);

$first_date_time = time();
foreach ($course_codes as $course_code) {
    $sql = "SELECT COUNT(*) AS cnt FROM actions";
    $result = db_query($sql, $course_code);
    while ($row = mysql_fetch_assoc($result)) {
        $totalHits += $row['cnt'];
    }
    mysql_free_result($result);

    $sql = "SELECT UNIX_TIMESTAMP(MIN(date_time)) AS first FROM actions";
    $result = db_query($sql, $course_code);
    while ($row = mysql_fetch_assoc($result)) {
        $tmp = $row['first'];
        if ($tmp < $first_date_time) {
            $first_date_time = $tmp;
        }
    }
    mysql_free_result($result);
}
$uptime = date("d-m-Y H:i", $first_date_time);

// Constract a table with all information
$tool_content .= "<table width=\"99%\">
<tbody>
<tr valign=\"top\"><td><br>
<p align=center>".$langAboutText."</p>
<p align=center><b>".$langEclassVersion."</b></p>
<p align=center>".$langHostName."<b>".$SERVER_NAME."</b></p>
<p align=center>".$langWebVersion."<b>".$SERVER_SOFTWARE."</b></p>";

// Check if we have mysql database to display its information
if (extension_loaded('mysql'))
    $tool_content .= "<p align=center>$langMySqlVersion<b>".mysql_get_server_info()."</b></p>";
else // If not display message no MySQL
    $tool_content .= "<p align=center font color=\"red\">".$langNoMysql."</p>";

// Close table correctly
$tool_content .= "<p align=center>".$langTotalCourses.": <b>".$totalCourses."</b></p>
									<p align=center>".$langTotalHits.": <b>".$totalHits."</b></p>
									<p align=center>".$langUptime.": <b>".$uptime."</b></p>";
$tool_content .= "<br></tbody></td></tr></table>";

// Display link back to index.php
$tool_content .= "<br><center><p><a href=\"index.php\">".$langBack."</a></p></center>";

/*****************************************************************************
        DISPLAY HTML
******************************************************************************/
// Call draw function to display the HTML
// $tool_content: the content to display
// 3: display administrator menu
// admin: use tool.css from admin folder
draw($tool_content,3,'admin');
?>
