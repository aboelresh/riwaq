<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    const TTL_TRACKS  = 3600; 
    const TTL_COURSES = 3600;  
    const TTL_TOPIC   = 1800; 

    public static function tracksKey(int $page = 1): string
    {
        return "tracks:list:page:{$page}";
    }

    public static function trackKey(int $id): string
    {
        return "tracks:show:{$id}";
    }

    public static function coursesKey(int $page = 1): string
    {
        return "courses:list:page:{$page}";
    }

    public static function courseKey(int $id): string
    {
        return "courses:show:{$id}";
    }

    public static function clearTracks(): void
    {
        Cache::flush(); 
      
    }

    public static function clearCourses(): void
    {
        Cache::flush();
        
    }
}