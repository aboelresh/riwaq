<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Code Master API',
    version: '1.0.0',
    description: 'Adaptive Learning Platform - Multi-tenant SaaS',
    contact: new OA\Contact(email: 'dev@codemaster.com')
)]
#[OA\Server(url: 'http://127.0.0.1:8001/api/v1', description: 'Local Sandbox')]
#[OA\Server(url: 'https://api.codemaster.com/api/v1', description: 'Production')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\Tag(name: 'Auth',         description: 'Authentication')]
#[OA\Tag(name: 'Tracks',       description: 'Learning tracks')]
#[OA\Tag(name: 'Courses',      description: 'Courses')]
#[OA\Tag(name: 'Topics',       description: 'Topics and video progress')]
#[OA\Tag(name: 'Quizzes',      description: 'Quiz submission and results')]
#[OA\Tag(name: 'Assessment',   description: 'Initial assessment')]
#[OA\Tag(name: 'Progress',     description: 'User progress and analytics')]
#[OA\Tag(name: 'AI',           description: 'AI Chat assistant Rafiq')]
#[OA\Tag(name: 'Teams',        description: 'Team collaboration')]
#[OA\Tag(name: 'Notifications',description: 'In-app notifications')]
#[OA\Tag(name: 'Profile',      description: 'User profile')]
#[OA\Tag(name: 'Organization', description: 'SaaS organization management')]
#[OA\Tag(name: 'Admin',        description: 'Admin-only endpoints')]
#[OA\Tag(name: 'System',       description: 'Health check')]
abstract class Controller {}