<?php
/**
 * This file contains the base class for an NDB_Menu. An NDB_Menu is the
 * base class for any menu page for a Loris module.
 *
 * PHP Version 5
 *
 * @category Main
 * @package  Main
 * @author   Loris Team <loris.mni@bic.mni.mcgill.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris/
 */
require_once 'PEAR.php';
require_once 'NDB_Page.class.inc';

/**
 * Base Menu class
 *
 * @category Main
 * @package  Main
 * @author   Loris Team <loris.mni@bic.mni.mcgill.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris/
 */
class NDB_Menu extends NDB_Page
{
    /**
     * The menu's name
     * @var    string
     * @access private
     */
    var $menu;

    /**
     * The menu's mode
     * @var    string
     * @access private
     */
    var $mode;

    /**
     * The template data for Smarty
     * @var    array
     * @access private
     */
    var $tpl_data;


    /**
     * Generates a new menu instance
     *
     * @param string $menu The name of the menu to use
     * @param string $mode The menu's mode
     *
     * @return object The new object of $menu type
     */
    static function &factory($menu, $mode)
    {
        // get the name of the class
        if (class_exists("NDB_Menu_$menu")) {
            $class = "NDB_Menu_$menu";
        } elseif (class_exists("NDB_Menu_Filter_$menu")) {
            $class = "NDB_Menu_Filter_$menu";
        } elseif (class_exists("NDB_Menu_Filter_Form_$menu")) {
            $class = "NDB_Menu_Filter_Form_$menu";
        } else {
            throw new Exception("The menu class ($menu) is not defined.");
        }

        // create a new menu object
        $obj = new $class;

        if (isset($_REQUEST['test_name'])) {
            $obj->Module = $_REQUEST['test_name'];
        } else {
            $obj->Module = $menu;
        }
        // set the local variables
        $obj->menu = $menu;
        $obj->mode = $mode;
        $access    = $obj->_hasAccess();

        // check that user has access
        if ($access == false) {
            throw new Exception("You do not have access to this page.", 403);
        }

        return $obj;
    }


    /**
     * Returns false if user does not have access to page
     *
     * Usage: the hasAccess() function in a child class should contain
     *    something like:
     *
     *     // create user object
     *     $user =& User::singleton();
     *
     *     return $user->hasPermission('superuser');
     *
     * You do not need to overload hasAccess() if there are no access restrictions
     *
     * @note   overloaded function
     * @return bool
     * @access private
     */
    function _hasAccess()
    {
        return true;
    }


    /**
     * Calls other member functions to do all the work necessary to create the menu
     *
     * @note   overloaded function
     * @return void
     * @access public
     */
    function setup()
    {
        return true;
    }

    /**
     * Displays the menu page
     *
     * @return void
     * @access public
     */
    function display()
    {
        // dump the html for the menu
        $smarty = new Smarty_neurodb($this->Module);
        $smarty->assign('mode', $this->mode);
        $smarty->assign($this->tpl_data);
        $html = $smarty->fetch("menu_$this->menu.tpl");
        return $html;
    }
}
?>