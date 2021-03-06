<?php
return [
    'assetsVersion' => 'v1',
    'debug'         => true,
    'dbs'           => [
        'default' => [
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8',
            'host'     => 'localhost',
            //'path' => 'app/base' # only for pdo_sqlite
            //'port' => 43434 # default
            'dbname'   => 'svi',
            'user'     => 'svi',
            'password' => 'DB PASSWORD',
        ]
    ],
    'twig'          => [
        'strict_variables' => false,
    ],
    'mail'          => [
        'transport'         => 'smtp',                    // Available options is mail, smtp
        'host'              => 'smtp.gmail.com',               // Used only for smtp
        'port'              => 465,                            // Used only for smtp
        'encryption'        => 'ssl',                    // Used only for smtp
        'user'              => 'example@example.com',          // Used only for smtp
        'password'          => 'SMTP PASSWORD',        // Used only for smtp
        'spool'             => 'app/cache/mail',              // Dir related to root where spool is stored
        'spoolTimeLimit'    => null,                 // Used only if spool is setted
        'spoolMessageLimit' => 10,                // Used only if spool is setted
    ],
    'secret'        => 'GENERATE THIS KEY',
    'siteurl'       => 'example.com',
];