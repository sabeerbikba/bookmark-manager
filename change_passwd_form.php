<?php
require_once(__DIR__ . '/includes/bookmark_fns.php');
session_start();
do_html_header('Change password');
check_valid_user();
display_resetPassword_form();
display_user_menu();
do_html_footer();

