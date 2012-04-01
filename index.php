<?php

/*
 * This file is distributed under the terms of the GNU General Public License.
 * For complete license information, please see license.txt in the root directory.
 */

/**
 * This is the micro-kernel which handles the initiation systems such as hard bans and error handling.
 * [NOTE]: Remember that *_once() is faster than regular include/require.
 *
 * @author Tanz
 */

// TODO: Implement hard ban system for DDoS and similar purposes.

// Activate the fatalErrorHandler and custom error handler to ensure errors are handled properly.
require_once($_SERVER['DOCUMENT_ROOT'].'/sys/error/errorHandler.php');
register_shutdown_function('fatalErrorHandler');
set_error_handler('errorHandler');

// Now move onto the actual kernel.
require_once($_SERVER['DOCUMENT_ROOT'].'/sys/Kernel.php');

?>
