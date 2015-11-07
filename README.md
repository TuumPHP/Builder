Application Builder
===================

A generic builder for application construction based on various server environment. 

### Lisence

MIT License

### PSR

PSR-1, PSR-2, and PSR-4.

Basic Usage
-----------

### Construction

Construct the application builder with two directories:

*	`$app_dir` for application settings, and 
* 	`$var_dir` for files not under version control. 

```php
use WScore\Builder\AppBuilder;

$app_dir  = __DIR__ . '/config/;
$var_dir  = dirname(__DIR__).'/var';
$builder  = AppBuilder::forge($app_dir, $var_dir);
```

then, set your favorite application such as, 

```php
$builder->app = new MyApp();
```

### Configuration File

Create configuration files under the `$builder->app_dir`. For example, 

```
+ config/
   +- setup.php
```

In the setup.php file, you can access to the builder as `$builder` and the application as `$app`, such as, 

```php
<?php
use Tuum\Builder\AppBuilder;
/** @var AppBuilder $builder */
/** @var MyApp $app */

/**
 * set up your application
 */
```

and load the configuration, 

```php
$builder->setup(function (AppBuilder $builder) {
    $builder->configure('setup');
```


Environment
-----------

### Loading Environment

Create a php file which returns environment strings under `$builder->var_dir` which returns environment name (or an array of environment names). 

```php
# environment file
return ['local', 'test'];
```

and to load the environments, 

```php
$builder->setup(function (AppBuilder $builder) {
    $builder->loadEnvironment('/env');
});
```

For __production environment__, return **NOTHING**, or empty array. 



### `Configure` mehtod

To setup for environment other than production, create a directory under `$config_dir` with the environment name, and create a configuration file exactly same name as the production. 

```
+- config/
   +- setup.php
   +- local/
      +- setup.php
   +- test/
      +- setup.php
```

The builder will invoke all the configuration files starting from the production and the specified environments. 

### `execConfig` method

To execute **only** one configuration file for the current environment, use `execConfig` method. 

For instance, the following code 

```php
$builder->execConfig('setup'); 
```

will read `setup.php` file based on the environment;

*   in production: `config/setup.php`,
*   in local: `config/local/setup.php`,
*   in test+local: `config/test/setup.php`,

warning:

* [ ] the behavior of 'execConfig' is not fixed yet. 



More About AppBuilder
-------------------------

### Builder as Container

The builder has a really simple container functionality:

```php
$builder->set('key', 'secret');
if ($builder->has('key')) {
	$value = $builder->get('key');
}
```

It is possible to use the environment file to set secret keys, such as DB pass, and use it in the database configuration. 


### Finding the Environment

Check for the current environment using `is` and `isProduction` methods;

```php
echo $builder->env->isProduction();        // bool
echo $builder->env->is('test'); // bool
```

You can force the environment by 

```php
$builder->env->setEnvironment(['local', 'test']);
```