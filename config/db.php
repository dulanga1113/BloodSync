<?php

return [

    // 🔹 Application-level settings
    'app' => [
        'name'    => 'BloodSync',
        'env'     => 'dev', // or 'prod'
        'base_url'=> 'http://localhost/BloodSync', // change if needed
    ],

    // 🔹 Database connection (PostgreSQL)
    'db' => [
        'driver'   => 'pgsql',
        'host'     => 'localhost',
        'port'     => 5432,
        'database' => 'bloodsync',      // matches your DB schema :contentReference[oaicite:0]{index=0}
        'username' => 'postgres',
        'password' => 'UOG0723002',     // DO NOT commit real password in a public repo

        'charset'  => 'utf8',
        'options'  => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],

    // 🔹 JWT configuration
    'jwt' => [
        // CHANGE THIS to a long random secret before going live
        'secret'     => 'CHANGE_THIS_TO_A_LONG_RANDOM_SECRET_KEY',
        'issuer'     => 'BloodSync',        // iss claim
        'expires_in' => 60 * 60 * 4,        // 4 hours in seconds
    ],

    // 🔹 Global error settings
    'error' => [
        // In index.php / bootstrap you will read this and call ini_set()
        'display_errors'  => true,         // set false in production
        'error_reporting' => E_ALL,
    ],
];