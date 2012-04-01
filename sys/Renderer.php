<?php

/*
 * This file is distributed under the terms of the GNU General Public License.
 * For complete license information, please see license.txt in the root directory.
 */

/**
 * Core design and formatting class.
 * Handles rendering of pages.
 *
 * @author Tanz
 */
class Renderer {

    public static function RenderPage() {
        
        // If 
        
        
    }
    
    public static function Header() {
        //$User = new User();
        echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/sys/templates/header.html');
    }
    
    public static function Content($Page = "index", $Data = NULL) {
        echo "Page = ".(string)$Page." && Data = $Data";
        echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/sys/templates/index.html');
    }

    public static function Footer() {
        echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/sys/templates/footer.html');
    }
    
}

?>
