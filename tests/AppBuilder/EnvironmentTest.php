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
        $this->assertTrue($this->builder->isEnvironment('local'));
        $this->assertFalse($this->builder->isEnvironment('bad'));
    }

    /**
     * @test
     */
    function default_is_production_environment()
    {
        $this->assertTrue($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnvironment('local'));
    }
    
    /**
     * @test
     */
    function env_no_return_sets_production_environment()
    {
        $this->builder->loadEnvironment('production');
        $this->assertTrue($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnvironment('local'));
    }

    /**
     * @test
     */
    function env_return_null_sets_production_environment()
    {
        $this->builder->loadEnvironment('prod-return');
        $this->assertTrue($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnvironment('local'));
    }

    /**
     * @test
     */
    function reading_non_exist_env_sets_production()
    {
        $this->builder->loadEnvironment('no-such-env-file');
        $this->assertTrue($this->builder->isProduction());
        $this->assertFalse($this->builder->isEnvironment('local'));
    }

    /**
     * @test
     */
    function return_two_env_sets_two_environments()
    {
        $this->builder->loadEnvironment('two_env');
        $this->assertFalse($this->builder->isProduction());
        $this->assertTrue($this->builder->isEnvironment('local'));
        $this->assertTrue($this->builder->isEnvironment('test'));
        $this->assertFalse($this->builder->isEnvironment('bad'));
    }
}
