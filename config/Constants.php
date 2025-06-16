<?php

class Constants
{

    public const DB = [
        'HOSTNAME' => 'localhost',
        'USERNAME' => 'root',
        'PASSWORD' => '',
        'DATABASE' => 'bookstore',
        'PORT' => 3306,
    ];

    public const JWT = [
        'TOKEN_AUTHENTICATION' => false, // (boolean) - enable/disable token authentication
        'SECRET_KEY' => '44i67fg16e8rgsdfv516sd5fi1s6df5v16aef5g4164g6e8gaw6df51a21',
        'ALG'=>'HS256',
        'ISSUER' => 'localhost:8080',
        'EXPIRATION' =>'+3 min',
        'TIMEZONE'=>'Africa/Luanda'
    ];

    public const MSG = [
        'LOGIN_ERROR'=>'Invalid credentials',
        'PERMISSION_DENY'=>'Permission denied',
        'RESOURCE_NOT_FOUND'=>'Resource not found',
        'INVALID_TOKEN'=>'Invalid token. Please login'
    ];
}
