<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Predis\Command\Argument\Geospatial\ByRadius;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Predis\Command\Argument\Geospatial\FromLonLat;

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

    public function testSortedSet(){
        Redis::del("names");

        Redis::zadd("names", 100, "Rama");
        Redis::zadd("names", 95, "Fajar");
        Redis::zadd("names", 85, "Fadhillah");
        
        $response=Redis::zrange("names", 0 , -1);
        self::assertEquals(["Fadhillah","Fajar","Rama"], $response);
    }

    public function testHash(){
        Redis::del("user:1");

        Redis::hset("user:1", "name", "Rama");
        Redis::hset("user:1", "email", "ramafajar805@gmail.com");
        Redis::hset("user:1", "age", 20);

        $response=Redis::hgetall("user:1");
        self::assertEquals([
            "name" => "Rama",
            "email" =>  "ramafajar805@gmail.com",
            "age" => "20"
        ], $response);
    }

    public function testGeoPoint(){
        Redis::del("sellers");

        Redis::geoadd("sellers", 106.820990, -6.174704, "Toko A");
        Redis::geoadd("sellers", 106.822696, -6.176870, "Toko B");

        $result=Redis::geodist("sellers", "Toko A", "Toko B", "km");
        self::assertEquals(0.3061, $result);

        $result = Redis::geosearch("sellers", new FromLonLat(106.821666, -6.175494), new ByRadius(5, "km"));
        dd($result);
        self::assertEquals(["Toko A", "Toko B"], $result);
    }

    public function testHyperLogLog(){
        Redis::pfadd("visitors","rama","fajar","fadhillah");
        Redis::pfadd("visitors","rama","fajar","fadhillah");
        Redis::pfadd("visitors","fajar","zaki","aulia");
        Redis::pfadd("visitors","flora","ella","gracia");
        
        $total=Redis::pfcount("visitors");
        self::assertEquals(8, $total);
    }

    public function testPipeline(){
        Redis::pipeline(function($pipeline){
            $pipeline->setex("name", 2, "Fajar");
            $pipeline->setex("address", 2, "Indonesia");
        });

        $response=Redis::get("name");
        self::assertEquals("Fajar", $response);
        $response=Redis::get("address");
        self::assertEquals("Indonesia", $response);
    }

    public function testTransaction(){
        Redis::transaction(function($transaction){
            $transaction->setex("name", 2, "Rama");
            $transaction->setex("address", 2, "Indonesia");
        });

        $response=Redis::get("name");
        self::assertEquals("Rama", $response);
        $response=Redis::get("address");
        self::assertEquals("Indonesia", $response);
    }
}
