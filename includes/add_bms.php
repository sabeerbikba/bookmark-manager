<?php
require_once(__DIR__ . '/bookmark_fns.php');
session_start();
//create short variable name
$new_url = $_POST['new_url'];
do_html_header('Adding bookmarks');
try {
    check_valid_user();
    if (!filled_out($_POST)) {
        throw new Exception('Form not completely filled out.');
    }
    // check URL format
    if (strstr($new_url, 'http://') === false) {
        $new_url = 'http://' . $new_url;
    }
    // check valid email
    if (valid_url($new_url)) { 
        throw new Exception('That is not a valid url address. 
        Please go back and try again.');
    }
    // try to add bm
    add_bm($new_url);
    echo 'Bookmark added.';
    display_addBms_form();


} catch (Exception $e) {
    echo $e->getMessage();
}
display_user_menu();
do_html_footer();
