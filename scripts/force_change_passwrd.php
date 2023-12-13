<?php
require_once("../bookmark_fns.php");
function display_forcePasswordReset_form()
{
?>
    <form action="force_change_passwrd.php" method="post">
        <label for="usrname">username:</label><br>
        <input type="text" name="username" required><br><br>
        <label for="passwd">passwd:</label><br>
        <input type="password" name="passwd" minlength="6" maxlength="16" id="passwd"><br><br>
        <label for="passwd2">passwd2:</label><br>
        <input type="password" name="passwd2" minlength="6" maxlength="16" id="passwd2"><br><br>
        <input type="checkbox" onclick="myFunction()"> Show Password<br><br>
        <button>change</button><br><br>
        <script>
            function myFunction() {
                var passwd = document.getElementById("passwd");
                var passwd2 = document.getElementById("passwd2");
                togglePassword(passwd);
                togglePassword(passwd2);
            }
            function togglePassword(element) {
                if (element.type === "password") {
                    element.type = "text";
                } else {
                    element.type = "password";
                }
            }
        </script>
    </form>
<?php
}
do_html_header('Force change Password');
display_forcePasswordReset_form();
do_html_footer();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $passwd = $_POST['passwd'];
    $passwd2 = $_POST['passwd2'];

    $conn = db_connect();
    // Check if the user exists
    if (isUserExistsDB($conn, $username)) {
        // Continue with password update logic
        if ($passwd == $passwd2) {
            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("UPDATE user SET passwd = SHA1(?) WHERE username = ?");
            $stmt->bind_param("ss", $passwd, $username);
            $stmt->execute();
            $stmt->close();

            echo '<b style="color: green;">Password updated successfully.</b>';
        } else {
            echo '<b style="color: red;">Passwords do not match.. please try again</b>';
        }
    } else {
        echo '<b style="color: red;">User not found.</b>';
    }
}
?>