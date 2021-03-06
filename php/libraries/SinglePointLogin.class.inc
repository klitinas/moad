<?php
/**
 * This class provides a single point to deal with user authentication
 * and validation of login, displaying the login page if necessary.
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

/**
 * SinglePointLogin class
 *
 * This class provides a single-point user login to the NeuroDB GUIs
 * Its public functions can be used to display a login window at the
 * current location while preserving the current state of the GUI.
 *
 * @category Main
 * @package  Main
 * @author   Loris Team <loris.mni@bic.mni.mcgill.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris/
 */
class SinglePointLogin extends PEAR
{
    /**
     * Defines whether a user is currently logged in
     */
    var $_isLoggedIn = false;

    /**
     * Internally held copy of username
     */
    var $_username = null;

    /**
     * Dumps the login form
     *
     * @return void
     * @access public
     */
    function showLoginScreen()
    {
        $tpl_data = array();
        if (isset($_POST['username'])) {
            $tpl_data['username'] = $_POST['username'];
        } else {
            $tpl_data['username'] = '';
        }

        $tpl_data['currentyear'] = date('Y');

        // remove logout from request
        parse_str($_SERVER['QUERY_STRING'], $params);
        unset($params['logout']);

        $config =& NDB_Config::singleton();

        $tpl_data['title']         = $config->getSetting('title');
        $tpl_data['css']           = $config->getSetting('css');
        $tpl_data['action']        = $_SERVER['PHP_SELF']
            . '?' . http_build_query($params);
        $tpl_data['error_message'] = $this->_lastError;
        $study_links = $config->getSetting('Studylinks');// print_r($study_links);
        foreach (Utility::toArray($study_links['link']) AS $link) {
            $LinkArgs = '';
            $BaseURL  = $link['@']['url'];
            if (isset($link['@']['args'])) {
                $LinkArgs = $link_args[$link['@']['args']];
            }
            $LinkLabel  = $link['#'];
            $WindowName = md5($link['@']['url']);
            $tpl_data['studylinks'][] =array(
                                        'url'        => $BaseURL . $LinkArgs,
                                        'label'      => $LinkLabel,
                                        'windowName' => $WindowName,
                                       );
        }
        $study_title = $config->getSetting('title');
        $study_logo  = $config->getSetting('studylogo');
        $tpl_data['study_title'] = $study_title;
        $tpl_data['study_logo']  = $study_logo;
        //Output template using Smarty
        $smarty = new Smarty_neurodb;
        $smarty->assign($tpl_data);
        $smarty->display('login.tpl');
    }


    /**
     * Dumps the password expiry form
     *
     * @return void
     * @access public
     */
    function showPasswordExpiryScreen()
    {
        $tpl_data['username'] = $_POST['username'];

        // remove logout from request
        parse_str($_SERVER['QUERY_STRING'], $params);
        unset($params['logout']);

        $config =& NDB_Config::singleton();

        $tpl_data['title'] = $config->getSetting('title');
        $tpl_data['css']   = $config->getSetting('css');
        $study_title       = $config->getSetting('title');
        $study_logo        = $config->getSetting('studylogo');

        $tpl_data['study_title'] = $study_title;
        $tpl_data['study_logo']  = $study_logo;

        $tpl_data['action'] = $_SERVER['PHP_SELF']
            .'?'
            . http_build_query($params);

        $tpl_data['error_message'] = $this->_lastError;

        //Output template using Smarty
        $smarty = new Smarty_neurodb;
        $smarty->assign($tpl_data);
        $smarty->display('password_expiry.tpl');
        die();
    }


    /**
     * Checks whether anyone is currently logged in
     *
     * @return bool
     * @access public
     */
    function isLoggedIn()
    {
        return $this->_isLoggedIn;
    }


    /**
     * Saves the new password if last was expired
     *
     * @return void
     * @throws PEAR_Error
     * @access public
     */
    function save()
    {
        // get saved data to pre-populate form
        $user =& User::factory($_POST['username']);

        // get user's data
        $data = $user->getData();

        // check password strength
        /* if (!User::isPasswordStrong(
            $_POST['password'],
            array(
             $_POST['confirm'],
             $data['UserID'],
             $data['Email'],
            ),
            array(
             '==',
             '!=',
             '!=',
            )
        )
        ) {
            $this->_lastError = 'The password is weak, or'
               . ' the passwords do not match';
        }
		*/
        // check that password has changed
        /* if (User::MD5Unsalt($_POST['password'], $data['Password_md5'])) {
            $this->_lastError = 'You cannot keep the same password';
        } */

        // if errors
        if (!empty($this->_lastError)) {
            return false;
        }

        // make the set
        $expiry = date(
            'Y-m-d',
            mktime(0, 0, 0, date('n') + 6, date('j'), date('Y'))
        );
        $set    = array(
                   'Password'        => null,
                   'Password_md5'    => User::MD5Salt($_POST['password']),
                   'Password_expiry' => $expiry,
                  );

        // update the user
        $success = $user->update($set);

        // get config options relating to proftpd
        $config      =& NDB_Config::singleton();
        $ftpSettings = $config->getSetting("proftpd");

        // if proftpd stuff is enabled:
        if ($ftpSettings['enabled'] == 'true') {
            // connect to proftpd database
            $ftpdb   = new Database();
            $success = $ftpdb->connect(
                $ftpSettings['database'],
                $ftpSettings['username'],
                $ftpSettings['password'],
                $ftpSettings['host'],
                false
            );

            // check users table to see if we have a valid user
            $query = "SELECT COUNT(*) AS User_count
                          FROM ftpusers
                      WHERE userid = :UID";
            $row   = $ftpdb->pselectRow($query, array('UID' => $_POST['username']));
            // update password
            if ($row['User_count'] == 1) {

                $setArray['passwd'] = crypt($_POST['password']);

                $success = $ftpdb->update(
                    'ftpusers',
                    $setArray,
                    array('userid' => $_POST['username'])
                );
            } else {
                // if user does not exist, insert user data
                $query = "SELECT MAX(uid) as Max_UID FROM ftpusers";

                $maxUID = $ftpdb->pselectOne($query, array());

                $setArray['userid']  = $_POST['username'];
                $setArray['passwd']  = crypt($_POST['password']);
                $setArray['uid']     = $maxUID + 1;
                $setArray['homedir'] = "/";

                // insert into proftpd
                $success = $ftpdb->insert("ftpusers", $setArray);
            }
        }
        return true;
    }

    /**
     * Checks whether a given username and password are valid
     *
     * @return bool
     * @throws PEAR_Error
     * @access public
     */
    function authenticate()
    {
        $this->_lastError = '';

         ///////////////////////////
         ///initialization/////
         //////////////////////////
        $this->_username = isset($_POST['username'])
            ? $_POST['username']
            : 'Unknown';
         $setArray       = array(
                            'userID'          => $this->_username,
                            'Success'         => 'Y',
                            'Failcode'        => null,
                            'Login_timestamp' => date('Y-m-d H:i:s'),
                            'IP_address'      => $_SERVER['REMOTE_ADDR'],
                            'Page_requested'  => $_SERVER['REQUEST_URI'],
                           );

        // save the new password if the last password expired
        if (isset($_POST['expiry'])) {
            $success = $this->save();

            if ($success == false) {
                $this->showPasswordExpiryScreen();
            }
        }

        // don't waste execution time if no form was submitted
        if (empty($_POST['login'])) {
            return false;
        }
        if (empty($_POST['username'])) {
            $this->_lastError = 'Please enter a username';
            $this->insertFailedDetail(
                $this->_lastError,
                $setArray
            );
            return false;
        }
        if (empty($_POST['password'])) {
            $this->_lastError = 'Please enter a password';
            $this->insertFailedDetail(
                $this->_lastError,
                $setArray
            );
            return false;
        }

        // create DB object
        $DB =& Database::singleton();

        // check users table to see if we have a valid user
        $query = "SELECT COUNT(*) AS User_count,
                         Password_md5, Password_expiry, Active, Pending_approval
                    FROM users
                  WHERE UserID = :username
                  GROUP BY UserID";
        $row   = $DB->pselectRow($query, array('username' => $_POST['username']));

        if ($row['User_count'] == 1) {
            // validate the user
            if (User::MD5Unsalt($_POST['password'], $row['Password_md5'])) {
                // check that the user is active
                if ($row['Active'] == 'N') {
                    $this->_lastError = "Your account has been deactivated."
                        . " Please contact your project administrator to"
                        . " reactivate this account.";
                    $this->insertFailedDetail(
                        "user account not active",
                        $setArray
                    );

                    return false;
                }

                if ($row['Pending_approval'] == '1') {
                    $this->_lastError = "Your account has not yet been activated."
                        . " Please contact your project administrator to activate"
                        . " this account.";
                    $this->insertFailedDetail("user account pending", $setArray);
                    return false;
                }

                // check if the password expired
                if (strcmp($row['Password_expiry'], date('Y-m-d', time())) < 1) {
                    // show password expiry screen
                    $this->showPasswordExpiryScreen();
                }

                // user is valid
                $this->_username = $_POST['username'];

                $result =  $DB->insert('user_login_history', $setArray);
				$user = $_POST['username'];
				$password = $_POST['password'];
				$trythis=system("sudo /var/www/loris/additions/mount.sh $user $password");

                return true;
            }
        }

            // bad usename or password
        $this->_lastError = "Incorrect username or password";
        return false;
    }

    /**
     * Sets the session data (State object)
     *
     * @return void
     * @access public
     */
    function setState()
    {
        // someone is logged in
        $this->_isLoggedIn = true;

        // create the session managed State object
        if (!isset($_SESSION['State'])) {
            $_SESSION['State'] =& State::singleton();
        }

        $_SESSION['State']->setUsername($this->_username);
    }

    /**
     * Clears the authentication related session data (State object)
     *
     * @return void
     * @access public
     */
    function clearState()
    {
        // someone is logged in
        $this->_isLoggedIn = false;

        // create the session managed State object
        if (!isset($_SESSION['State'])) {
            $_SESSION['State'] =& State::singleton();
        }

        $_SESSION['State']->setUsername(null);
    }

     /**
     * Inserts the login (or failed-login) detail into the user_login_history
     *
     * @param String $description description for the failed-login
     * @param Array  $setArray    contains data to be inserted
     *
     * @return null
      */
    function insertFailedDetail($description, $setArray)
    {
        // create DB object
        $DB =& Database::singleton();

        $setArray['Success']     = 'N';
        $setArray['Fail_detail'] = $description;

        // in future, add mapping of error message to Failcode field enum
        $result =  $DB->insert('user_login_history', $setArray);
    }

}
?>
