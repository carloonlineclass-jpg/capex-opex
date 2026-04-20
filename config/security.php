<?php

return [
    'jwt_secret' => env('JWT_SECRET', ''),
    'jwt_ttl' => env('JWT_TTL', 120),
    'bcrypt_rounds' => env('BCRYPT_ROUNDS', 12),
];
