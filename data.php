<?php
$token = isset( $_COOKIE['auth_token'] ) ? $_COOKIE['auth_token'] : '';
if ( ! $token ) {
    setcookie( 'auth_token', NULL );
    header( 'Location: http://localhost:8888/gemguide-api-test/' );
}

$ch  = curl_init();
$url = 'https://gemguide-dev.herokuapp.com/prices-api/diamond?' . http_build_query( array(
    'name'    => 'Emerald',
    'weight'  => 1,
    'color'   => 'G',
    'clarity' => 'IF/FL',
) );

curl_setopt_array( $ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL            => $url,
    CURLOPT_HTTPHEADER     => array(
        "api_key: test",
        "user: $token"
    ),
) );

$info = curl_getinfo( $ch );
$res = curl_exec( $ch );

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gemguide Pricing Data API</title>

    <style>
        th {
            text-align: right;
        }

        th,
        td {
            border: 1px #ccc solid;
            padding: 10px;
        }

        tr:nth-child( 2n ) {
            background: #eee;
        }

    </style>
</head>
<body>
    <table>
        <tr>
            <th>URL:</th>
            <td><?php echo $url; ?></td>
        </tr>
        <tr>
            <th>Token:</th>
            <td><?php echo $token; ?></td>
        </tr>
        <tr>
            <th>Response:</th>
            <td><?php print_r( $res ); ?></td>
        </tr>
    </table>
</body>
</html>