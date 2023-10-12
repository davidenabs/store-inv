<?php
    session_start();
    // if the user's browser send a cookie for the session
    if ( isset( $_COOKIE[ session_name() ] ) ) {
        // empty the cookie
        setcookie( session_name(), '', time()-86400, '/' );
    }

    // clear all session variables
    session_unset();

    // destory the session
    session_destroy();

    // redirect
    header('location: index.php')
?>