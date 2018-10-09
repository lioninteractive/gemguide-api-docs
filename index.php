<?php

$token = isset( $_COOKIE['auth_token'] ) ? $_COOKIE['auth_token'] : '';
if ( $token ) {
    header( 'Location: http://localhost:8888/gemguide-api-test/data.php' );
    die;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gemguide Pricing Data API</title>
</head>
<body>
    <a
        href="https://gemguide-dev.herokuapp.com/api-login?client_key=test"
        style="display: inline-block; background:red;color:white;padding:10px 20px;text-decoration:none">
        Sign In
    </a>
</body>
</html>