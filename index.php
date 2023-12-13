<?php
// include function files for this application
require_once(__DIR__ . '/includes/bookmark_fns.php');
session_start();
//create short variable names

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //if not username empty and request method is post then set empty value
    if ($_POST['username']) {
        $username = $_POST['username'];
    } else {
        $_POST['username'] = " ";
    }

    //if not password empty and request method is post then set empty value
    if ($_POST['passwd']) {
        $passwd = $_POST['passwd'];
    } else {
        $_POST['passwd'] = " ";
    }

    if ($username && $passwd) {
        // they have just tried logging in
        try {
            login($username, $passwd);
            // if they are in the database register the user id
            $_SESSION['valid_user'] = $username;
        } catch (Exception $e) {
            // unsuccessful login
            do_html_header('Problem:');
            echo '<b style="color:red;">Wrong Username & Password please try again</b>';
            display_login_form();
            // do_html_url('login.php', 'Login');
            do_html_footer();
            exit;
        }
    }
}
do_html_header('Home');
check_valid_user(); //user_auth_fns.php
// get the bookmarks this user has saved
if ($url_array = get_user_urls($_SESSION['valid_user'])) { //url_fns.php
    display_user_urls($url_array); //output_fns.php
}

// give menu of options
display_user_menu();
do_html_footer();
