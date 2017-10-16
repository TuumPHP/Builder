<?php
namespace tests\AppBuilder;

use Tuum\Builder\Builder;

require_once(dirname(__DIR__) . '/autoloader.php');

class BuilderTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
     */
    private $builder;

    function setUp()
    {
        $_ENV = [];
        unset($_SERVER['ENV_TEST']);
        
        parent::setUp();
        $this->builder = Builder::forge(__DIR__ . '/app', __DIR__ . '/var', true);
    }
    
    public function test_debug()
    {
        $this->assertTrue($this->builder->isDebug());
        $this->builder = Builder::forge(__DIR__ . '/app', __DIR__ . '/var', false);
        $this->assertFalse($this->builder->isDebug());
        $this->builder = Builder::forge(__DIR__ . '/app', __DIR__ . '/var');
        $this->assertTrue($this->builder->isDebug());
    }

    public function test_load_php_file()
    {
        $builder = $this->builder;
        $settings = $builder->load('settings');
        $this->assertEquals('tested', $settings['setting-test']);
        $this->assertEquals('tested', $builder->get('settings')['setting-test']);
        $this->assertTrue($builder->has('settings'));
        $this->assertFalse($builder->has('no-such'));
    }
    
    public function test_load_non_existing_file()
    {
        $builder = $this->builder;
        $this->assertFalse($builder->load('no-such'));

    }
    
    public function test_load_closure_file()
    {
        $builder = $this->builder;
        $settings = $builder->load('closure');
        $this->assertEquals('closure-test', $settings($builder));
        $this->assertEquals('tested', $builder->get('load-closure'));
    }
    
    public function test_env()
    {
        $builder = $this->builder;
        $builder->loadEnv();
        $this->assertEquals('tested', $builder->get('ENV_TEST'));
        $this->assertEquals(null, $builder->get('NO_SUCH'));
        $this->assertEquals('default', $builder->get('NO_SUCH', 'default'));
    }
    
    public function test_another_env_file_name()
    {
        $builder = $this->builder;
        $builder->loadEnv('.env.file');
        $this->assertEquals('file-name', $builder->get('ENV_FILE'));
    }
    
    public function test_no_env()
    {
        $builder = Builder::forge(__DIR__ . '/app', __DIR__ . '/empty', true);
        $this->assertFalse($builder->loadEnv());
    }
}