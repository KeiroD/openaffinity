<?php

/*
 * This file is distributed under the terms of the GNU General Public License.
 * For complete license information, please see license.txt in the root directory.
 */

/**
 * Main kernel of the site. Handles mostly everything and anything of interest.
 *
 * @author Tanz
 */

// First enable Gziphandler so that our content is properly compressed.
ob_start("ob_gzhandler");

// Ensure that the GET variables actually exist
// To avoid "undefined index" errors
if (!isset($_GET['p'])) { $_GET['p'] = "index"; }
if (!isset($_GET['d'])) { $_GET['d'] = NULL; }

// Include the renderer because it'll be used a lot in the switch statement.
require_once($_SERVER['DOCUMENT_ROOT'].'/sys/Render.php');

switch ($_GET['p']) {
    case "index":
        Renderer::RenderIndexPage();
        break;
    case "view":
        break;
    case "watch":
        break;
    case "journals":
        break;
    case "user":
        break;
    case "gallery":
        break;
    case "scraps":
        break;
    case "favorites":
        break;
    
    default:
        Renderer::Render404Page();
        break;
        
}

/**
 * This is the "soft" error handler as well as a general "Hey Link!" notifier.
 * Will cancel execution, wipe the buffer and display a message instead (Eg. "You have watched Username").
 * 
 * @param string $msg The message to be displayed
 * @param string $return An URL. Page will autoredirect (and offer a clickable link) to the url specified.
 * @return void
 */
function SysMessage($msg, $return) {
    require_once($_SERVER['DOCUMENT_ROOT'].'/sys/Render.php'); // Just incase it hasn't already been included.
    ob_clean(); // Remove whatever output already present, we won't need it.
    echo Renderer::GetSysMessageHTML(); //Ask the renderer to render a complete SysMessage HTML template, and output it.
    exit(); // This should boot us directly to FatalErrorHandler which will output everything.
}
?>
