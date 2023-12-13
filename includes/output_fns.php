<?php
function do_html_header($title)
{
  // print an HTML header
?>
  <!doctype html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
      body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px
      }

      li,
      td {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px
      }

      .td {
        width: 200px;
      }

      tr:nth-child(even) {
        background-color: #cad3d2;
      }

      hr {
        color: #3333cc;
      }

      a {
        color: #000
      }

      div.formblock {
        background: #ccc;
        width: 300px;
        padding: 6px;
        border: 1px solid #000;
      }
    </style>
  </head>

  <body>
    <div>
      <img src="images/bookmark.gif" alt="PHPbookmark logo" height="55" width="57" style="float: left; padding-right: 6px;" />
      <h1>PHPbookmark</h1>
    </div>
    <hr />
    <?php
    if ($title) {
      do_html_heading($title);
    }
  }

  //print an HTML header
  function do_html_footer()
  {
    echo "</body>\n</html>";
  }

  // print an HTML title if exist
  function do_html_heading($title)
  {
    echo "<h2>$title</h2>";
  }


  // print an HTML registration form 
  function display_registration_form()
  {
    ?>
    <div class="formblock">
      <form action="register_new" method="post">
        <h2>Register Now</h2>
        <label for="email">Email Address:</label><br>
        <input type="email" name="email" required><br><br>
        <label for="username">Preferred Username<br>(max 16 chars):</label><br>
        <input type="text" name="username" maxlength="16" required><br><br>
        <label for="passwd">Password<br>(between 6 to 16 chars):</label><br>
        <input type="password" name="passwd" id="passwd" minlength="6" maxlength="16" required><br><br>
        <label for="passwd2">Confirm Password:</label><br>
        <input type="password" name="passwd2" id="passwd2" minlength="6" maxlength="16" required><br><br>
        <input type="checkbox" onclick="togglePassword()">Show Password<br><br>
        <button>Register</button><br><br>
        <script>
          function togglePassword() {
            var passwd = document.getElementById("passwd");
            var passwd2 = document.getElementById("passwd2");

            if (passwd.type === "password") {
              passwd.type = "text";
            } else {
              passwd.type = "password";
            }

            if (passwd2.type === "password") {
              passwd2.type = "text";
            } else {
              passwd2.type = "password";
            }
          }
        </script>
      </form>
    </div>

  <?php
  }

  function display_site_info()
  {
  ?>
    <ul>
      <li>Store your bookmarks online with us!</li>
      <li>See other users use!</li>
      <li>Share your favorite link with others!</li>
    </ul>
    <a href="register_form">Not a member?</a>
  <?php
  }

  function display_login_form()
  {
  ?>
    <br><br>
    <div class="formblock">
      <form action="/projects/" method="post">
        <h2>Members Log In Here</h2>
        <label for="username">Username:</label><br>
        <input type="text" name="username" maxlength="16" required><br>
        <label for="passwd">Password:</label><br>
        <input type="password" name="passwd" id="myInput" minlength="6" maxlength="16" required><br><br>
        <input type="checkbox" onclick="myFunction()">Show Password<br><br>
        <button>Log In</button><br><br>
        <a href="#">Forgot your password?</a><br><br>
        <script>
          function myFunction() {
            var x = document.getElementById("myInput");
            if (x.type === "password") {
              x.type = "text";
            } else {
              x.type = "password";
            }
          }
        </script>
      </form>
    </div>
  <?php
  }


  function display_resetPassword_form()
  {
  ?>
    <br><br>
    <div class="formblock">
      <form action="forgot_passwd" method="post">
        <h2>Change Password</h2>
        <label for="oldPassword">Old Password:</label><br>
        <input type="password" name="current_password" id="oldPassword" minlength="6" maxlength="16" required><br><br>
        <label for="newPassword">New Password:</label><br>
        <input type="password" name="new_password" id="newPassword" minlength="6" maxlength="16" required><br><br>
        <label for="repeatPassword">Repeat New Password:</label><br>
        <input type="password" name="confirm_new_password" id="repeatPassword" minlength="6" maxlength="16" required><br><br>
        <input type="checkbox" onclick="myFunction()"> Show Password<br><br>
        <button>Change Password</button><br><br>
        <script>
          function myFunction() {
            var oldPassword = document.getElementById("oldPassword");
            var newPassword = document.getElementById("newPassword");
            var repeatPassword = document.getElementById("repeatPassword");
            togglePassword(oldPassword);
            togglePassword(newPassword);
            togglePassword(repeatPassword);
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
    </div>


  <?php
  }

  function display_addBms_form()
  {
  ?>
    <div class="formblock">
      <form action="add_bms" method="post">
        <h2>New Bookmark</h2>
        <input type="text" value="http://" name="new_url">
        <button>Add Bookmark</button>
      </form>
    </div>

    <?php
  }




  function display_user_urls($url_array)
  {
    if (!empty($url_array)) {
    ?>
      <form method="post" action="delete_bms.php">
        <table border="0" cellspacing="0" cellpadding="5">
          <tr>
            <th>URL</th>
            <th>Delete</th>
          </tr>
          <?php
          foreach ($url_array as $url) {
            echo '<tr>';
            echo '<td class="td">' . $url . '</td>';
            echo '<td><input type="checkbox" name="del_me[]" value="' . $url . '"></td>';
            echo '</tr>';
          }
          ?>
        </table>
        <input type="submit" value="Delete Selected Bookmarks">
      </form>
    <?php

    } else {
      echo '<p>No bookmarks to display.</p>';
    }
  }





  function display_user_menu()
  {
    // all in single line 
    ?>
    <hr>

    <a href="/projects/">Home</a> | <a href="add_bms_form">Add BM</a> |
    <a href="change_passwd_form">Change Password</a> | <a href="logout">Logout</a>
    <hr>
  <?php
  }

  function display_error_page()
  {
  ?>
    <p>It seems like you've reached our error page directory. If you are looking for something specific, please go back to the <b><a href="/projects/">home page</a></b>.</p>
    <p>If you need assistance, feel free to <a href="mailto:sabeerbikba02@gmail.com">contact support</a>.</p>
  <?php
  }



  ?>