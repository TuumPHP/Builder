<?php
namespace tests\AppBuilder;

use Tuum\Builder\AppBuilder;

require_once(dirname(__DIR__) . '/autoloader.php');

class AppBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AppBuilder
     */
    private $builder;

    function setup()
    {
        $this->builder = AppBuilder::forge(__DIR__ . '/conf', __DIR__ . '/env');
    }

    /**
     * @test
     */
    function setup_uses_closure()
    {
        $this->builder->setup(function(AppBuilder $builder) {
            $builder->set('test', 'tested');
        });
        $this->assertEquals('tested', $this->builder->get('test'));
        $this->assertTrue($this->builder->has('test'));
        $this->assertFalse($this->builder->has('no-such'));

    }
    
    /**
     * @test
     */
    function set_get_has_works()
    {
        $this->builder->set('test', 'tested');
        $this->assertEquals('tested', $this->builder->get('test'));
        $this->assertTrue($this->builder->has('test'));
        $this->assertFalse($this->builder->has('no-such'));
    }

    /**
     * @test
     */
    function default_env_is_production_and_reads_only_the_production_config()
    {
        $this->builder->configure('config');
        $this->assertEquals('tested', $this->builder->get('test-production'));
        $this->assertEquals(null, $this->builder->get('test-local'));
        $this->assertEquals(null, $this->builder->get('test-test'));
    }

    /**
     * @test
     */
    function setting_local_env_reads_only_the_local_config()
    {
        $this->builder->loadEnvironment('local');
        $this->builder->configure('config');
        $this->assertEquals('tested', $this->builder->get('test-local'));
        $this->assertEquals(null, $this->builder->get('test-production'));
        $this->assertEquals(null, $this->builder->get('test-test'));
        $this->assertEquals(null, $this->builder->get('test-production'));
    }

    /**
     * @test
     */
    function configure_reads_config_scripts()
    {
        $this->builder->loadEnvironment('local');
        $this->builder->configure('production');
        $this->assertEquals('done', $this->builder->get('production'));
    }

    /**
     * @test
     */
    function setting_test_env_reads_script_resolving_production_scripts()
    {
        $this->builder->loadEnvironment('test');
        $this->builder->configure('production');
        $this->assertEquals('done', $this->builder->get('production'));
        $this->assertEquals('done', $this->builder->get('tests'));
    }
}
