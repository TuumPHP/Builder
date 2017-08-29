Application Builder
===================

A generic builder for application construction based on various server environment. 

### Licence

MIT License

### PSR

PSR-1, PSR-2, and PSR-4.

Basic Usage
-----------

### Directory Structure

This component assumes there are two directories to build an application: 

*	`APP_DIR`: directory for application settings, and 
* 	`VAR_DIR`: directory for files not under version control. 

For instance, 

```
+ config/          // <- APP_DIR
   + setup.php
   + routes.php
+ var/             // <- VAR_DIR
   + .env
   + cache/
```


### Construction

Construct the application builder with two directories:

```php
use WScore\Builder\Builder;

$builder  = new Builder([
    Builder::APP_DIR => __DIR__ . '/config,  // app directory
    Builder::VAR_DIR => __DIR__ . '/var',    // var directory
    Builder::DEBUG   => true                 // debug
]);
```

Or, maybe use `forge` method, 

```php
use WScore\Builder\Builder;

$builder  = Builder::forge(
    __DIR__ . '/config,  // app directory
    __DIR__ . '/var',    // var directory
    true                 // debug
);
```

### Setting Application

```php
$builder->setApp(new MyApp());
$app = $builder->getApp();
```


### Configuration File

To load configuration files under the `APP_DIR`:

```php
$builder->load('setup');
$builder->load('routes');
```

In `setup.php` file, set up the application, such as:

```php
/** @var Tuum\Builder\Builder $builder */
$app->set();
```

```php
use WScore\Builder\Builder;

return function(Builder Builder) {
    $app = $builder->app;
    $app->get('/top', function() { /* do something! */ });
};
```


### Environment File

Loads `.env` file using [vlucas's dotenv](https://github.com/vlucas/phpdotenv) component.
The default location of the `.env` file is at `VAR_DIR`. 

```php
$builder->loadEnv();
```




Environment
-----------

### Loading Environment

Create a php file which returns environment strings under `$builder->var_dir` which returns environment name (or an array of environment names), such as `env.php`. 

```php
# environment file
return ['local', 'test'];
```

and to load the environments, 

```php
$builder->loadEnvironment('env');
```

For __production environment__, return **NOTHING**, or empty array. Or, simply, do not create an env-file at all. 



### `configure` mehtod

To setup for environment other than production, create a directory under `$config_dir` with the environment name, and create a configuration file exactly same name as the production. 

```
+- config/
   +- setup.php
   +- local/
      +- setup.php
   +- test/
      +- setup.php
```

The builder will execute **only** one configuration file for the current environment. 

#### to read production config

If the environment specific configuration file requires other script (such as one for the main production), do something like:

```php
return function(AppBuilder Builder) {
    // load the main production script. 
    $builder->execute(dirname(__DIR__).'/script-name'); 
    // continue configuration.
    $builder->app->getContainer()->set('some', 'value');
};
```


More About AppBuilder
-------------------------

### Simple Construction 

`AppBuilder::forge` provides a simpler construction, 

```php
$builder = AppBuilder::forge(
    $app_dir,
    $var_dir, 
    [
        'debug'    => true,
        'env'      => 'local',
        'env-file' => 'env.php',
    ]
);
```

where you can set an optional array. 

* `debug` for setting debug (i.e. `$builder->debug`),
* `env` for directly specifying environment,
* `env-file` for reading environment from the specified file (if `env` is not set). 

The used option can be retrieved by 

```php
$options = $builder->get('options');
```

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
echo $builder->isProduction(); // bool
echo $builder->isEnv('test'); // bool
```

You can force the environment by 

```php
$builder->loadEnvironment(['local', 'test']);
```