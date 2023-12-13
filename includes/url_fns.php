<?php

function get_user_urls($username)
{
    // Extract from the database all the URLs this user has stored
    $conn = db_connect();
    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT bm_URL FROM bookmark WHERE username = ?");
    $stmt->bind_param("s", $username);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result = $stmt->get_result();

    if (!$result) {
        return false;
    } else {
        // Create an array of the URLs
        $url_array = array();
        while ($row = $result->fetch_row()) {
            $url_array[] = $row[0];
        }
        // Close the statement
        $stmt->close();
        return $url_array;
    }
}


function add_bm($new_url)
{
    // Add new bookmark to the database
    echo "Attempting to add " . htmlspecialchars($new_url) . "<br />";
    $valid_user = $_SESSION['valid_user'];
    $conn = db_connect();
    // Check if the bookmark already exists
    $stmt = $conn->prepare("SELECT * FROM bookmark WHERE username=? AND bm_URL=?");
    $stmt->bind_param("ss", $valid_user, $new_url);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && ($result->num_rows > 0)) {
        $stmt->close();
        throw new Exception('Bookmark already exists.');
    }
    // Insert the new bookmark
    $stmt = $conn->prepare("INSERT INTO bookmark (username, bm_URL) VALUES (?, ?)");
    $stmt->bind_param("ss", $valid_user, $new_url);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception('Bookmark could not be inserted.');
    }
    $stmt->close();
    return true;
}


function delete_bm($user, $url)
{
    // Delete one URL from the database
    $conn = db_connect();

    // Delete the bookmark using a prepared statement
    $stmt = $conn->prepare("DELETE FROM bookmark WHERE username=? AND bm_url=?");
    $stmt->bind_param("ss", $user, $url);

    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception('Bookmark could not be deleted');
    }

    $stmt->close();
    return true;
}



/**
 * advance 
 * not recommende for now 
 */
function recommend_urls($valid_user, $popularity = 1)
{
    // We will provide semi intelligent recommendations to people
    // If they have an URL in common with other users, they may like
    // other URLs that these people like
    $conn = db_connect();
    // find other matching users
    // with an url the same as you
    // as a simple way of excluding people's private pages, and
    // increasing the chance of recommending appealing URLs, we
    // specify a minimum popularity level
    // if $popularity = 1, then more than one person must have
    // an URL before we will recommend it
    $query = "SELECT bm_URL
        FROM bookmark
        WHERE username IN
        (SELECT DISTINCT(b2.username)
        FROM bookmark b1, bookmark b2
        WHERE b1.username='" . $valid_user . "'
        AND b1.username != b2.username
        AND b1.bm_URL = b2.bm_URL)
        AND bm_URL not in
        (SELECT bm_URL
        FROM bookmark
        WHERE username='" . $valid_user . "')
        GROUP BY bm_url
        HAVING COUNT(bm_url)>" . $popularity;
    if (!($result = $conn->query($query))) {
        throw new Exception('Could not find any bookmarks to recommend.');
    }
    if ($result->num_rows == 0) {
        throw new Exception('Could not find any bookmarks to recommend.');
    }
    $urls = array();
    // build an array of the relevant urls
    for ($count = 0; $row = $result->fetch_object(); $count++) {
        $urls[$count] = $row->bm_URL;
    }
    return $urls;
}
