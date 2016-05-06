<?php

Config::set(Mecha::A($config->states->{'plugin_' . md5(File::B(__DIR__))}));
Config::set('category_query', "");

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
require __DIR__ . DS . 'workers' . DS . 'engine' . DS . 'plug' . DS . 'widget.php';

Weapon::add('shell_after', function() {
    echo Asset::stylesheet(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'widgets.css', "", 'shell/widget.category.min.css');
}, 10.1);

Weapon::add('SHIPMENT_REGION_BOTTOM', function() {
    echo Asset::javascript(__DIR__ . DS . 'assets' . DS . 'sword' . DS . 'widgets.js', "", 'sword/widget.category.min.js');
}, 10.1);

function do_category_search($kind, $scope = null, $fallback = false) {
    $kind = (array) $kind;
    if(isset($kind[0]) && strpos($kind[0], 'C') === 0) {
        return Get::category('id:' . substr($kind[0], 1), null, $fallback, $scope);
    }
    /*
    foreach($kind as $v) {
        if($v && strpos($v, 'C') === 0) {
            return Get::category('id:' . substr($v, 1), null, $fallback, $scope);
        }
    }
    */
    return $fallback;
}