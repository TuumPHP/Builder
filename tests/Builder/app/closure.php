<?php

/**
 * @param Builder $builder
 */

use Tuum\Builder\Builder;

return function (Builder $builder) {
    $builder->set('load-closure', 'tested');
    
    return 'closure-test';
};