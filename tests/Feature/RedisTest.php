<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedisTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testPing(){
        $response=Redis::command("ping");
        self::assertEquals("PONG", $response);

        Redis::ping();
    }

    public function testString(){
        Redis::setEx("name", 2, "Fajar");
        $response=Redis::get("name");
        self::assertEquals("Fajar", $response);

        sleep(5);

        $response=Redis::get("name");
        self::assertNull($response);
    }

    public function testList(){
        Redis::del("names");

        Redis::rpush("names", "Rama");
        Redis::rpush("names","Fajar");
        Redis::rpush("names","Fadhillah");

        $response=Redis::lrange("names", 0, -1);
        self::assertEquals(["Rama", "Fajar","Fadhillah"], $response);

        self::assertEquals("Rama", Redis::lpop("names"));
        self::assertEquals("Fajar", Redis::lpop("names"));
        self::assertEquals("Fadhillah", Redis::lpop("names"));
    }

    public function testSet(){
        Redis::del("names");

        Redis::sadd("names","Rama");
        Redis::sadd("names","Rama");
        Redis::sadd("names","Fajar");
        Redis::sadd("names","Fajar");
        Redis::sadd("names","Fadhillah");
        Redis::sadd("names","Fadhillah");

        $resposne=Redis::smembers("names");
        self::assertEquals(["Rama","Fajar","Fadhillah"], $resposne);
    }
}
