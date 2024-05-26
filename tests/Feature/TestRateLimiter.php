<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class TestRateLimiter extends TestCase
{
    /**
     * A basic feature test example.
     */
   
     public function testRateLimiter(){
        $success=RateLimiter::attempt("send-message-1", 5, function(){
            echo "Send Message"; 
        });

        self::assertTrue($success);
     }
}
