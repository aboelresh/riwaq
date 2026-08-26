<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{

    protected function authHeaders($user): array
    {
        $token = JWTAuth::fromUser($user);
        return ['Authorization' => "Bearer {$token}"];
    }
}