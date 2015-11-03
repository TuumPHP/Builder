<?php
namespace tests\AppBuilder;

use Tuum\Builder\AppBuilder;

require_once(dirname(__DIR__) . '/autoloader.php');

class ConfigTest extends \PHPUnit_Framework_TestCase
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
    function set_production_env()
    {
        $this->builder->configure('config');
        $this->assertEquals('tested', $this->builder->get('test-production'));
    }

    /**
     * @test
     */
    function set_local_production_env()
    {
        $this->builder->loadEnvironment('local');
        $this->builder->configure('config');
        $this->assertEquals('tested', $this->builder->get('test-local'));
        $this->assertEquals(null, $this->builder->get('test-production'));
    }

    /**
     * @test
     */
    function configure_reads_config_file_for_production_if_no_other()
    {
        $this->builder->loadEnvironment('local');
        $this->builder->configure('production');
        $this->assertEquals('done', $this->builder->get('production'));
    }
}
