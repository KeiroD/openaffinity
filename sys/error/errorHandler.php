<?php

/*
 * This file is distributed under the terms of the GNU General Public License.
 * For complete license information, please see license.txt in the root directory.
 */

/**
 * This file contains the function which handles all the error management.
 * If a fatal error is encountered, it will flush
 * the buffer and instead display an appropriate error page.
 * If a regular error is encountered it will print a not-so-revealing error message, which later on might be handled by JS.
 * TODO: Write a proper stacktrace debugging system
 *
 * @author Tanz
 */

function errorHandler($error_number, $error_string, $error_file, $error_line) {
    
    $error_message = "";
    switch ($error_number) {
        case 2: // Warning
            $error_message .= '<span style="color:red;font-weight:bold">Warning:</span> ';
            break;
        case 8: // Notice
            $error_message .= '<span style="color:orange;font-weight:bold">Notice:</span> ';
            break;
        default:
            $error_message .= '<span style="color:pink;font-weight:bold">Unknown('.$error_number.'):</span> ';
            break;
    }
    
    $error_message .= '<span style="font-style:italic">&quot;'.$error_string.'&quot;</span> in ';
    $error_message .= '<span style="font-weight:bold">'.basename($error_file).'</span> on line <span style="font-weight:bold">'.$error_line.'</span><br/>';
    
    echo $error_message;
    
}

function fatalErrorHandler() {
    
    // Find out of an error has occurred and if not do not trigger error reporting.
    // Instead flush whatever is in the buffer to the browser and exit the errorhandler.
    $error = error_get_last();
    if (!$error) { ob_end_flush(); exit(); }
    if ($error['type'] != 1 && $error['type'] != 64) { ob_end_flush(); exit(); }
 
    // If we are here then it means we did, in fact, encounter a problem.
    // Clean whatever part of the page is in the buffer since we no longer need it.
    ob_end_clean();
    ob_start("ob_gzhandler"); // Until we can solve whatever's going on here.

    // Load the error template for edit
    $output = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/sys/error/errorTemplate.html');

    // Decide on what to say
    $e_type = 'Server Error';
    $e_msg = 'Internal PHP Error';
    $e_desc = '
    A fatal system error has occurred.<br/>
    The error ocurred in ['.  basename($error['file']).':'.$error['line'].'] with the message:<br/>
    <i>'.$error['message'].'</i>
    ';

    // Replace variables in the HTML template with appropriate text and output to browser
    $output = str_replace('<%ERRORTYPE%>', $e_type, $output);
    $output = str_replace('<%ERRORMSG%>', $e_msg, $output);
    $output = str_replace('<%ERRORDESC%>', $e_desc, $output);
    $output = str_replace('<%STACKTRACE%>', "<b>Stacktrace:</b><br/><i>Stacktrace subroutine currently unavailable.</i>", $output); // Currently not available
    
    echo $output;
}


function generateStacktrace() {
    
    // Currently not used.
    
    /*
     * This function will generate a stack trace based on the php debug_stacktrace().
     * Generated result should look something like:
     * #123:  Class~Object->method(arg,arg) called at [file.php:666]
     * #123:  Class~Object::method(arg,arg) called at [file.php:666]
     * #123:  ~function(arg,arg) called at [file.php:666]
     * 
     */
    
    $dbgTrace = debug_backtrace();
    $dbgMsg = "<b>Stacktrace:</b><br/>";
    
    // Generate array of debuginfo for used with 
    foreach($dbgTrace as $dbgIndex => $dbgInfo) {
        $args = array();
        
        // Neccessary to ensure that long string arguments are encapsulated with " and shortened with an <abbr> tag.
        foreach($dbgInfo['args'] as $arg) {
            if (is_int($arg)) { $args[] = $arg; continue; }
            $arg = preg_replace('/<br(\s+)?\/?>/i', "", $arg);
            $arg = strip_tags($arg);
            // TODO: Encapsulate ALL string arguments with "
            if (strlen($arg) > 6) {
                $arg = substr_replace($arg, "<abbr title='".$arg."'>...</abbr>", 6, -1);
                //$arg = substr($arg, 1,-1); // Not really needed as only ob_start callback contains spaces since it's a HTML page. 
                $arg = substr_replace($arg, '"', 0,0);
                $args[] = $arg.'"';           
            }
        }
        
        // Do a little bugcheck to ensure the file path is an actual file path.
        // If not, return "Unknown" (until we have a bit more information as what it is)
        if (!$dbgInfo['file'] && !$dbgInfo['line']) {
            $dbgInfo['file'] = "Unknown";
        }
    
        $dbgMsg .= sprintf("#%d:  %s~%s%s%s(%s) called at [%s%s]<br/>"
            ,$dbgIndex
            ,$dbgInfo['class']
            ,$dbgInfo['object']
            ,$dbgInfo['type']
            ,$dbgInfo['function']
            ,implode(',', $args)
            ,basename($dbgInfo['file'])
            ,($dbgInfo['line'])? ":".$dbgInfo['line'] : "" // If a line number is present, append : so that it becomes "file.php:123".
        );
    }
    return $dbgMsg;
}