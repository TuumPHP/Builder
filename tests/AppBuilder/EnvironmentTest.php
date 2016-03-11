<?php
namespace tests\AppBuilder;

use Tuum\Builder\AppBuilder;

require_once(dirname(__DIR__) . '/autoloader.php');

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AppBuilder
     */
    private $builder;
    
    function setup()
    {
        $this->builder = AppBuilder::forge(__DIR__.'/conf', __DIR__.'/env');
    }

    /**
     * @test
     */
    function returning_string_sets_environment()
    {
        $this->builder->loadEnvironment('local');
        $this->assertFalse($this->builder->isProduction());
        $this->assertTrue($this->builder->isEnv('local'));
        $this->assertFalse($this->builder->isEnv('bad'));
    }

    /**
     * @test
     */
    function default_is_production_environment()
    {
        $this->assertTrue($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnv('local'));
    }
    
    /**
     * @test
     */
    function env_no_return_sets_production_environment()
    {
        $this->builder->loadEnvironment('production');
        $this->assertTrue($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnv('local'));
    }

    /**
     * @test
     */
    function env_return_null_sets_production_environment()
    {
        $this->builder->loadEnvironment('prod-return');
        $this->assertTrue($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnv('local'));
    }

    /**
     * @test
     */
    function reading_non_exist_env_sets_production()
    {
        $this->builder->loadEnvironment('no-such-env-file');
        $this->assertTrue($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnv('local'));
    }

    /**
     * @test
     */
    function return_two_env_sets_two_environments()
    {
        $this->builder->loadEnvironment('two_env');
        $this->assertFalse($this->builder->isProduction());
        $this->assertTrue($this->builder->isEnv('local'));
        $this->assertTrue($this->builder->isEnv('test'));
        $this->assertFalse($this->builder->isEnv('bad'));
    }

    /**
     * @test
     */
    function setEnvironment_sets_new_env()
    {
        $this->builder->loadEnvironment('local');
        $this->builder->loadEnvironment('test');
        $this->assertFalse($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnv('local'));
        $this->assertTrue($this->builder->isEnv('test'));
        $this->assertFalse($this->builder->isEnv('bad'));
    }

    /**
     * @test
     */
    function forge_option_env_sets_environment()
    {
        $this->builder = AppBuilder::forge(__DIR__.'/conf', __DIR__.'/env', ['env' => 'test']);
        $this->assertFalse($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnv('local'));
        $this->assertTrue($this->builder->isEnv('test'));
        $this->assertFalse($this->builder->isEnv('bad'));
    }

    /**
     * @test
     */
    function null_varDir_ignores_loadEnvironment()
    {
        $this->builder = AppBuilder::forge(__DIR__.'/conf', null, ['env' => 'test']);
        $this->builder->loadEnvironment('local');
        $this->assertFalse($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnv('local'));
        $this->assertTrue($this->builder->isEnv('test'));
        $this->assertFalse($this->builder->isEnv('bad'));
    }

    /**
     * @test
     */
    function forge_option_debug_sets_debug_property()
    {
        $this->builder = AppBuilder::forge(__DIR__.'/conf', null, ['debug' => 'test']);
        $this->assertEquals('test', $this->builder->debug);
    }

    /**
     * @test
     */
    function forge_option_envFile_loads_environment()
    {
        $this->builder = AppBuilder::forge(__DIR__.'/conf', __DIR__.'/env', ['env-file' => 'local']);
        $this->assertFalse($this->builder->isProduction());
        $this->assertTrue($this->builder->isEnv('local'));
        $this->assertFalse($this->builder->isEnv('bad'));
    }
}
