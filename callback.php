<?php

$token = isset( $_GET['user'] ) ? $_GET['user'] : '';

if ( $token ) {
    setcookie( 'auth_token', $token, 0, '/gemguide-api-test/' );
    header( 'Location: http://localhost:8888/gemguide-api-test/data.php' );
} else {
    setcookie( 'auth_token', NULL );
    header( 'Location: http://localhost:8888/gemguide-api-test/error.php' );
}

die;