<?php

// checks whether the form fields are all filled ou
function filled_out($form_vars)
{
    foreach ($form_vars as $key => $value) {
        if ((!isset($key)) || ($value == '')) {
            return false;
        }
    }
    return true;
}

// validate email address 
// check an email address is possibly valid
function valid_email($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}

function valid_url($url)
{
    $pattern = "/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/";
    if (preg_match($pattern, $url)) {
        return false;
    }
    return true;
}
