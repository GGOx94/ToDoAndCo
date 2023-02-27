<?php

namespace App\Tests;

use Exception;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestFixturesProvider extends WebTestCase
{
    private static array $fixtures = [];

    /** @throws Exception */
    public static function getFixturesEntities() : array
    {
        if(!static::$booted) {
            static::createClient();
        }

        if(empty(static::$fixtures))
        {
            $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

            static::$fixtures = $databaseTool->loadAliceFixture([
                dirname(__DIR__).'/fixtures/alice_fixtures.yaml'
            ]);
        }

        return static::$fixtures;
    }
}