<?php
use Tuum\Builder\AppBuilder;

/**
 * production for test environment.
 *
 * @param AppBuilder $builder
 */
return function(AppBuilder $builder) {

    /** @var AppBuilder $builder */

    $builder->execute(dirname(__DIR__).'/production');
    $builder->set('tests', 'done');

};
