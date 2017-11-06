Application Builder
===================

A generic builder for application construction based on various server environment. 

### Licence

MIT License

### PSR

PSR-1, PSR-2, and PSR-4.

### Installation

```sh
composer require "tuum/builder: ^1.0.0"
```

### Sample Code

```php
use WScore\Builder\Builder;

$builder  = Builder::forge(
    __DIR__ . '/config,  // app directory
    __DIR__ . '/var',    // var directory
    true                 // debug
);
$builder->loadEnv();
$builder->load('setup');
$builder->load('routes');
if ($builder->isEnv('local')) {
    $builder->load('extra.local');
}

$app = $builder->getApp(); // <- must set an app using setApp()!
```

Basic Usage
-----------

### Directory Structure

`Tuum/Builder` assumes there are two directories to build an application: 

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

Or, simply use `forge` method as shown in the __Sample Code Section__. 


### Loading PHP File

To load configuration files under the `APP_DIR`, use `load` method as;

```php
$builder->load('setup');
```

In `setup.php` file, set up the application, such as:

```php
/** @var Tuum\Builder\Builder $builder */
$builder->set(Builder::APP_KEY, 'ENV');  // set value
$builder->setApp(new YourApp()); // set your application

return [
    'db-name' => $builder->get('DB_NAME', 'demo'),
]; // may return some value
```

* The builder has `has`, `get`, and `set` methods as expected.
* There are `setApp()` and `getApp()` methods to store your application. 
* The returned value from the PHP files are stored in the builder 
using its load name, which can be accessed by: `$builder->get('setup');`.


### Getting Values

The `get` method tries to get value from:
 
1. environment value,
2. `$builder`'s internal value
3. default value.

```php
$builder->get('DB_NAME', 'my_db');
```

### Environment File

Loads `.env` file using [vlucas's dotenv](https://github.com/vlucas/phpdotenv) component.
The default location of the `.env` file is at `VAR_DIR`. 

The `.env` file contains `APP_ENV` key to specify the environment as such;

```sh
APP_ENV = local
```

Then, you can access the environment as,

```php
$builder->loadEnv(); // load the .env file

$builder->isEnv('local');
$builder->isEnvProd();
$env = $builder->getEnv();
```

The builder considers the environment as `prod` 
if no environment is set, or no environment file to load. 

To change the key string used to specify the environment, 
set `Builder::APP_KEY` value to the new key name, such as;

```php
$builder->set(Builder::APP_KEY, 'ENV');  // set value
```

