<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_DATABASE'] = ':memory:';

        $app = parent::createApplication();

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('mail.default', 'array');
        $app['config']->set('hashing.bcrypt.rounds', 4);

        return $app;
    }

    protected function setUp(): void
    {
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');
        putenv('MAIL_MAILER=array');
        putenv('BCRYPT_ROUNDS=4');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';
        $_ENV['MAIL_MAILER'] = 'array';
        $_ENV['BCRYPT_ROUNDS'] = '4';
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_DATABASE'] = ':memory:';
        $_SERVER['MAIL_MAILER'] = 'array';
        $_SERVER['BCRYPT_ROUNDS'] = '4';

        parent::setUp();

        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
        config(['queue.default' => 'sync']);
        config(['broadcasting.default' => 'log']);
        config(['mail.default' => 'array']);
        config(['hashing.bcrypt.rounds' => 4]);

        if (class_exists(\Database\Factories\UserFactory::class)) {
            $ref = new \ReflectionProperty(\Database\Factories\UserFactory::class, 'password');
            $ref->setAccessible(true);
            $ref->setValue(null, null);
        }
    }
}
