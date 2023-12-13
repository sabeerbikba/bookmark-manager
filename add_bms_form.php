<?php
require_once(__DIR__ . '/includes/bookmark_fns.php');
do_html_header('Home');
display_addBms_form();
// give menu of options
display_user_menu();
do_html_footer();

?>