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

*	`$config_dir` for configuration files, and 
* 	`$var_dir` for files not under version control. 

```php
use WScore\Builder\AppBuilder;

$config_dir = __DIR__;
$var_dir    = dirname(__DIR__).'/var';
$builder    = AppBuilder::forge($config_dir, $var_dir);
```

then, set your favorite application such as, 

```php
$builder->setup(function (AppBuilder $builder) {
    $builder->app = new MyApp();
});
```


### Setting Environment

Create a php file which returns environment strings,

```php
# environment file
return ['local', 'test'];
```

and load the environments. 

```php
$builder->setup(function (AppBuilder $builder) {
    $builder->loadEnvironment($builder->var_dir . '/env-local-tests');
});
```

For __production environment__, return **NOTHING**, or empty array. 


* [ ] is it OK to assume that the environment files to be under `$env_dir`?


### Configuration

Create configuration files for production environment under the `$config_dir`. For example, 

```
+ $config_dir
  +- config/
     +- setup.php
```

and load the configuration as, 

```php
$builder->setup(function (AppBuilder $builder) {
    $builder->configure('config/setup');
```

To setup for environment other than production, create a directory under `$config_dir` with the environment name, and create a configuration file exactly same name as the production. 

```
+ $config_dir
  +- config/
     +- setup.php
  +- local/
    +- config/
       +- setup.php
  +- environment2/
    +- config/
       +- setup.php
```

The builder will invoke the configuration files starting from the production and the specified environments. 

* [ ] the directory structure may look cruttering. any better idea?

More About Configurations
-------------------------

### Configuration File

The builder will pass two local variables when invoking configuration files:

*	`$app`: your application, and 
* 	`$builder`: the application builder. 

Configure whatever necessary to configure your application while using the builder as temporary storage. 


### Builder as Container

The builder has a really simple container functionality:

```php
$builder->set('key', 'secret');
if ($builder->has('key')) {
	$key = $builder->get('key');
}
```

It is possible to use the environment file to set secret keys, such as DB pass, and use it in the database configuration. 


### Environment Specific Configuration

Sometimes, it is necessary to invoke a configuration file for only the environment, maybe construction a PDO. To do so, specify `TRUE` to the second argument of configure method. 

```php
$builder->setup(function (AppBuilder $builder) {
    $builder->configure('config/pdo', true);
```

* [ ] any better API?


### Finding the Environment

to-be-implemented. idea is, 

```php
$builder->isProduction();        // bool
$builder->isEnvironment('test'); // bool
```
