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

### Loading PHP File

To load configuration files under the `APP_DIR`:

```php
$builder->load('setup');
$builder->load('closure');
```

In `setup.php` file, set up the application, such as:

```php
/** @var Tuum\Builder\Builder $builder */
$builder->set('some', 'value'); // set value

return ['settings' => 'done'];
```

The $builder remembers whatever returned from a configuration file 
as its load name (i.e. $builder->has('setup');).

### Loading Closure

PHP file can return a callable to load into $builder, such as 

```php
use WScore\Builder\Builder;

return function(Builder Builder) {
    $builder->set('more', 'value');
};
```




### Environment File

Loads `.env` file using [vlucas's dotenv](https://github.com/vlucas/phpdotenv) component.
The default location of the `.env` file is at `VAR_DIR`. 

```php
$builder->loadEnv();
```

### Getting Values

`get` gets environment value, or from `$builder`'s internal value, or default value. 

```php
$builder->get('GET_FROM_ENV'); // from .env file
$builder->get('setup');        // from internal value
$builder->get('bad', 'none');  // from default value

```
