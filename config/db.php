<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=MySQL-5.7;dbname=weather_data',  // поменять на свои данные
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];