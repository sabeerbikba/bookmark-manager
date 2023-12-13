<?php
require_once(__DIR__ . '/includes/bookmark_fns.php');
do_html_header("Reset password");
// creating short variable name
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        session_start();
        //take username from session
        $username = $_SESSION['valid_user'];

        if (!isset($_SESSION['valid_user'])) {
            throw new Exception("User not authenticated.");
        } 
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        echo resetPassword($username, $current_password, $new_password, $confirm_new_password);
        notify_change_password($username);
    } catch (Exception $e) {
        echo 'Your password could not be reset - ' . $e->getMessage();
    }
}

display_user_menu();
// do_html_url('login.php', 'Login');
do_html_footer();
