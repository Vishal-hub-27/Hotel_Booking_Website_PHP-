<?php
// Database + app settings. Override with environment variables if you like.
return [
    'debug'      => getenv('APP_DEBUG') === '1',   // set APP_DEBUG=1 to see error details
    'db_host'    => getenv('DB_HOST') ?: '127.0.0.1',
    'db_port'    => getenv('DB_PORT') ?: '3306',
    'db_name'    => getenv('DB_NAME') ?: 'hotel_booking',
    'db_user'    => getenv('DB_USER') ?: 'root',
    'db_pass'    => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',   // XAMPP default: empty
    'max_upload' => 5 * 1024 * 1024,               // 5 MB
];
