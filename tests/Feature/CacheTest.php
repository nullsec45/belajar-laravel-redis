<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CacheTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCache(): void
    {
       Cache::put("name", "Fajar", 1);
       Cache::put("country","Indonesia", 1);
       
       $response=Cache::get("name");
       self::assertEquals("Fajar", $response);
       $response=Cache::get("country");
       self::assertEquals("Indonesia", $response);

       sleep(5);
       $response=Cache::get("name");
       self::assertNull($response);
       $response=Cache::get("country");
       self::assertNull($response);
    }
}
