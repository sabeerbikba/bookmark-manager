<?php
require_once('bookmark_fns.php');
session_start();

do_html_header('Deleting bookmarks');
check_valid_user();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form is submitted
    if (isset($_POST['del_me']) && is_array($_POST['del_me'])) {
        $del_me = $_POST['del_me'];
        $valid_user = $_SESSION['valid_user'];

        if (empty($del_me)) {
            echo '<p>You have not chosen any bookmarks to delete.<br>Please try again.</p>';
        } else {
            foreach ($del_me as $url) {
                if (delete_bm($valid_user, $url)) {
                    echo 'Deleted ' . htmlspecialchars($url) . '.<br>';
                } else {
                    echo 'Could not delete ' . htmlspecialchars($url) . '.<br>';
                }
            }
        }
    }
}

// Display the bookmarks and form
// Get the bookmarks this user has saved
if ($url_array = get_user_urls($_SESSION['valid_user'])) {
    display_user_urls($url_array);
}

display_user_menu();
do_html_footer();
?>

