<?php
namespace Sleek;

/**
 * Use this class for rendering 'view' files and sending the output to the browse
 */
class View {

    /**
     * @static
     * @param string $filename The path to the file in the views folder, minus extension
     * @param array $data Associative array of variables, ran through extract()
     * @param bool $string If true, returns as string, otherwise, renders to browser
     * @return bool|string
     */
    static public function render($filename, $data = array(), $string = FALSE) {
        $view_path = APP_DIR . "view/$filename.php";
        if (file_exists($view_path)) {
            extract($data);
            if ($string) { ob_start(); }
            include($view_path);
            if ($string) { return ob_get_clean(); }
            return TRUE;
        }
        return FALSE;
    }
}
