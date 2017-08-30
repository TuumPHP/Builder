<?php

/** @var \Tuum\Builder\Builder $builder */

$settings = [
    'setting-test' => 'tested',
];
$builder->set('settings', $settings);

return $settings;