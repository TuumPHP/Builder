<?php
namespace tests\Builder;

use Tuum\Builder\AppBuilder;

require_once(dirname(__DIR__) . '/autoloader.php');

class AppBuilderTest extends \PHPUnit_Framework_TestCase
{
    public $app;
    
    function setup()
    {
        $this->app = __DIR__ . '/app/app.php';
    }

    /**
     * @test
     */
    function constructor_sets_directories()
    {
        $builder = new AppBuilder('dir1', 'dir2');
        $this->assertEquals('dir1', $builder->app_dir);
        $this->assertEquals('dir2', $builder->var_dir);
    }

    /**
     * @test
     */
    function forge_construct_builder()
    {
        $builder = AppBuilder::forge('dir1', 'dir2');
        $this->assertEquals('Tuum\Builder\AppBuilder', get_class($builder));
        $this->assertEquals('dir1', $builder->app_dir);
        $this->assertEquals('dir2', $builder->var_dir);
    }

    /**
     * @test
     */
    function set_has_get_sets_values()
    {
        $builder = AppBuilder::forge('dir1');
        $this->assertFalse($builder->has('test'));
        $builder->set('test', 'tested');
        $this->assertTrue($builder->has('test'));
        $this->assertEquals('tested', $builder->get('test'));
    }

    /**
     * @test
     */
    function setup_calls_closure()
    {
        $builder = AppBuilder::forge('dir1', 'dir2');
        $builder->setup(function(AppBuilder $builder) {
            $builder->set('setup_calls_closure', 'tested');
        });
        $this->assertEquals('tested', $builder->get('setup_calls_closure'));
    }

    /**
     * @test
     */
    function sets_environment_for_local_and_tests() 
    {
        $builder = AppBuilder::forge(__DIR__.'/app')
            ->setup(function (AppBuilder $builder) {
                $builder->loadEnvironment($builder->var_dir . '/env-local-tests');
            });
        $env = $builder->environments;
        $this->assertContains('local', $env);
        $this->assertContains('tests', $env);
    }

    /**
     * @test
     */
    function configure_reads_all_environments()
    {
        $builder = AppBuilder::forge(__DIR__ . '/app')
            ->setup(function (AppBuilder $builder) {
                $builder->loadEnvironment($builder->var_dir . '/env-local-tests');
                $builder->configure('config/test');
                $builder->execConfig('config/only');
            });
        $builder->configure('Application/routes');

        $this->assertEquals('tested', $builder->get('test'));
        $this->assertEquals('tested', $builder->get('test-local'));
        $this->assertEquals('tested', $builder->get('test-tests'));
        $this->assertEquals(null,     $builder->get('only'));
        $this->assertEquals('tested', $builder->get('only-local'));
        $this->assertEquals('tested', $builder->get('only-tests'));
        $this->assertEquals('local key', $builder->get('DB-Key'));
    }

    /**
     * @test
     */
    function configure_reads_only_production()
    {
        $builder = AppBuilder::forge(__DIR__ . '/app')
            ->setup(function (AppBuilder $builder) {
                $builder->loadEnvironment($builder->var_dir . '/environments');
                $builder->configure('config/test');
                $builder->execConfig('config/only');
            });
        $builder->configure('Application/routes');

        $this->assertEquals('tested', $builder->get('test'));
        $this->assertEquals(null, $builder->get('test-local'));
        $this->assertEquals(null, $builder->get('test-tests'));
        $this->assertEquals('tested',     $builder->get('only'));
        $this->assertEquals(null, $builder->get('only-local'));
        $this->assertEquals(null, $builder->get('only-tests'));
        $this->assertEquals('tested', $builder->get('routes'));
        $this->assertEquals('secret key', $builder->get('DB-Key'));
    }

    /**
     * @test
     */
    function evaluate_reads_and_returns_value()
    {
        $builder = AppBuilder::forge(__DIR__ . '/app');
        $this->assertEquals('routed', $builder->execute($builder->app_dir.'/Application/routes'));
        $this->assertEquals('tested', $builder->get('routes'));
    }
}
