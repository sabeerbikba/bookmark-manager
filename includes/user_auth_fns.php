<?php
function register($username, $email, $password)
{
    // Register new person with db
    // Return true or error message
    // Connect to db
    $conn = db_connect();

    // Check if username is unique using a prepared statement
    $stmt = $conn->prepare("SELECT * FROM user WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        $stmt->close();
        throw new Exception('Could not execute query');
    }

    if ($result->num_rows > 0) {
        $stmt->close();
        throw new Exception('That username is taken - go back and choose another one.');
    }

    // If okay, put in the db using a prepared statement
    $stmt = $conn->prepare("INSERT INTO user (username, passwd, email) VALUES (?, SHA1(?), ?)");
    $stmt->bind_param("sss", $username, $password, $email);

    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception('Could not register you in the database - please try again later.');
    }

    $stmt->close();
    return true;
}




function login($username, $password)
{
    // Check username and password with db
    // If yes, return true
    // Else throw exception
    // Connect to db
    $conn = db_connect();

    // Check if username and password match using a prepared statement
    $stmt = $conn->prepare("SELECT * FROM user WHERE username=? AND passwd=SHA1(?)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        $stmt->close();
        throw new Exception('Could not log you in.');
    }

    if ($result->num_rows > 0) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        throw new Exception('Could not log you in.');
    }
}


function isUserExistsDB($conn, $username)
{
    $stmt = $conn->prepare("SELECT username FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Fetch the result to check if the user exists
    $result = $stmt->get_result();

    // Check if there is at least one row (user exists)
    return $result->num_rows > 0;
}

function check_valid_user()
{
    // see if somebody is logged in and notify them if not
    if (isset($_SESSION['valid_user'])) {
        echo "Logged in as " . $_SESSION['valid_user'] . ".<br>";
    } else {
        // they are not logged in
        do_html_heading('Problem:');
        echo 'You are not logged in. Please <b><a href="login.php">login</a></b><br>';
        // do_html_url('login.php', 'Login');
        do_html_footer();
        exit;
    }
}

//not using this fucntion becasuse crated own function 
function change_password($username, $old_password, $new_password)
{
    // change password for username/old_password to new_password
    // return true or false
    // if the old password is right
    // change their password to new_password and return true
    // else throw an exception
    login($username, $old_password);
    $conn = db_connect();
    $result = $conn->query("UPDATE user
        SET passwd = sha1('" . $new_password . "')
        where username = '" . $username . "'");
    if (!$result) {
        throw new Exception('Password could not be changed.');
    } else {
        return true;  // changed successfully
    }
}

function resetPassword($username, $current_password, $new_password, $confirm_new_password)
{
    $conn = db_connect();

    // Retrieve the current user's hashed password from the database
    $get_current_password_query = $conn->query("SELECT passwd FROM user WHERE username = '$username'");

    if ($get_current_password_query->num_rows > 0) {
        $row = $get_current_password_query->fetch_assoc();
        $hashed_current_password = $row['passwd'];

        // Verify the current password
        if (sha1($current_password) === $hashed_current_password) {
            // Check if the new passwords match
            if ($new_password === $confirm_new_password) {
                // Update the password in the database
                $stmt = $conn->prepare("UPDATE user SET passwd = SHA1(?) WHERE username = ?");
                $stmt->bind_param("ss", $new_password, $username);
                $stmt->execute();
                $stmt->close();

                return "Password reset successfully.";
            } else {
                throw new Exception("New passwords do not match.");
            }
        } else {
            throw new Exception("Incorrect current password.");
        }
    } else {
        throw new Exception("User not found.");
    }
}

function notify_change_password($username)
{
    // Include PHPMailer library
    include('smtp/PHPMailerAutoload.php');

    function smtp_mailer($to, $subject, $msg)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        //$mail->SMTPDebug = 2; 
        // how to setup phpMailer : https://youtu.be/vswB4BMqqI8?si=wRVg99abPvwNiGEY
        $mail->Username = "sabeerbikba02@gmail.com"; // change gmail address 
        $mail->Password = "lqbdbbuouorlwokj"; // genrate using gmail account | https://myaccount.google.com/apppasswords
        $mail->SetFrom("sabeerbikba02@gmail.com"); // change gmail address 
        $mail->Subject = $subject;
        $mail->Body = $msg;
        $mail->AddAddress($to);
        $mail->SMTPOptions = array('ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false
        ));
        if (!$mail->Send()) {
            echo $mail->ErrorInfo;
        } else {
            return 'Sent';
        }
    }

    // Notify the user that their password has been changed
    $conn = db_connect();

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT email FROM user WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        $stmt->close();
        throw new Exception('Could not find email address.');
    } else if ($result->num_rows == 0) {
        $stmt->close();
        throw new Exception('Could not find email address.');
        // Username not in the database
    } else {
        $row = $result->fetch_object();
        $stmt->close();

        $to = $row->email;
        date_default_timezone_set('Asia/Kolkata');
        // Get the current date and time
        $time = date('d-m-Y- H:i:s A');

        $msg = "Your PHPBookmark password has been changed at $time \r\nPlease notify if not you!";
        $subject = 'PHPBookmark Password Change';

        if (smtp_mailer($to, $subject, $msg)) {
            return true;
        } else {
            throw new Exception('Could not send email.');
        }
    }
}




//corrently this function not using
function reset_password($username)
{
    // set password for username to a random value
    // return the new password or false on failure
    // get a random dictionary word b/w 6 and 13 chars in length
    $new_password = get_random_word(6, 13);
    if ($new_password == false) {
        // give a default password
        $new_password = "changeMe!";
    }
    // add a number between 0 and 999 to it
    // to make it a slightly better password
    $rand_number = rand(0, 999);
    $new_password .= $rand_number;
    // set user's password to this in database or return false
    $conn = db_connect();
    $result = $conn->query("UPDATE user
        SET passwd = sha1('" . $new_password . "')
        where username = '" . $username . "'");
    if (!$result || $new_password == false) { //modified as descriped in book pno. 585
        throw new Exception('Could not change password.');  // not changed
    } else {
        return $new_password;  // changed successfully
    }
}

//corrently this fucntion not using 
function get_random_word($min_length, $max_length)
{
    // grab a random word from dictionary between the two lengths
    // and return it
    // generate a random word
    $word = '';
    // remember to change this path to suit your system
    $dictionary = 'words/words.txt';  // the ispell dictionary
    $fp = @fopen($dictionary, 'r');
    if (!$fp) {
        return false;
    }
    $size = filesize($dictionary);
    // go to a random location in dictionary
    $rand_location = rand(0, $size);
    fseek($fp, $rand_location);
    // get the next whole word of the right length in the file
    while ((strlen($word) < $min_length) || (strlen($word) > $max_length) ||
        (strstr($word, "'"))
    ) {
        if (feof($fp)) {
            fseek($fp, 0); // if at end, go to start
        }
        $word = fgets($fp, 80);  // skip first word as it could be partial
        $word = fgets($fp, 80);  // the potential password
    }
    $word = trim($word); // trim the trailing \n from fgets
    return $word;
}


//corrently this fucntion not using, comment out because causing error to to ther fucntion 
// function notify_password($username, $password)
// {
//     //included PHPmailer library
//     include('smtp/PHPMailerAutoload.php');
//     function smtp_mailer($to, $subject, $msg)
//     {
//         $mail = new PHPMailer();
//         $mail->IsSMTP();
//         $mail->SMTPAuth = true;
//         $mail->SMTPSecure = 'tls';
//         $mail->Host = "smtp.gmail.com";
//         $mail->Port = 587;
//         $mail->IsHTML(true);
//         $mail->CharSet = 'UTF-8';
//         //$mail->SMTPDebug = 2; 
//         $mail->Username = "sabeerbikba02@gmail.com";
//         $mail->Password = "lqbdbbuouorlwokj"; //sensetive 👁
//         $mail->SetFrom("sabeerbikba02@gmail.com");
//         $mail->Subject = $subject;
//         $mail->Body = $msg;
//         $mail->AddAddress($to);
//         $mail->SMTPOptions = array('ssl' => array(
//             'verify_peer' => false,
//             'verify_peer_name' => false,
//             'allow_self_signed' => false
//         ));
//         if (!$mail->Send()) {
//             echo $mail->ErrorInfo;
//         } else {
//             return 'Sent';
//         }
//     }
//     // notify the user that their password has been changed
//     $conn = db_connect();
//     $result = $conn->query("SELECT email FROM user
//         WHERE username='" . $username . "'");
//     if (!$result) {
//         throw new Exception('Could not find email address.');
//     } else if ($result->num_rows == 0) {
//         throw new Exception('Could not find email address.');
//         // username not in db
//     } else {
//         $row = $result->fetch_object();
//         $to = $row->email;
//         $from = "From: support@phpbookmark \r\n";
//         $msg = "Your PHPBookmark password has been changed to " . $password . "\r\n" .
//             "Please change it next time you log in.\r\n";
//         if (smtp_mailer($to, 'PHPBookmark login information', $msg)) {
//             return true;
//         } else {
//             throw new Exception('Could not send email.');
//         }
//     }
// }
