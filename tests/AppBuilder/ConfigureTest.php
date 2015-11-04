<?php
namespace tests\AppBuilder;

use Tuum\Builder\AppBuilder;

require_once(dirname(__DIR__) . '/autoloader.php');

class ConfigureTest extends \PHPUnit_Framework_TestCase
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
    function configure_reads_config_files_in_prod()
    {
        $this->builder->configure('config');
        $this->assertEquals('tested', $this->builder->get('test-production'));
        $this->assertEquals(null, $this->builder->get('test-local'));
        $this->assertEquals(null, $this->builder->get('test-test'));
    }

    /**
     * @test
     */
    function configure_reads_all_config_files_in_prod_and_local()
    {
        $this->builder->loadEnvironment('local');
        $this->builder->configure('config');
        $this->assertEquals('tested', $this->builder->get('test-production'));
        $this->assertEquals('tested', $this->builder->get('test-local'));
        $this->assertEquals(null, $this->builder->get('test-test'));
    }
}