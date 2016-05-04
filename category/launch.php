<?php

Config::set(Mecha::A($config->states->{'plugin_' . md5(File::B(__DIR__))}));

Config::merge('manager_menu', array(
    $speak->category => array(
        'icon' => 'list',
        'url' => $config->manager->slug . '/category',
        'stack' => 9.039
    )
));

// refresh ...
$config = Config::get();

require __DIR__ . DS . 'workers' . DS . 'engine' . DS . 'plug' . DS . 'get.php';

function do_category_search($kind, $scope = null, $fallback = false) {
    foreach($kind as $v) {
        if($v && strpos($v, 'C') === 0) {
            return Get::category('id:' . substr($v, 1), null, $fallback, $scope);
        }
    }
    return $fallback;
}