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
    if( ! $kind) return $fallback;
    if(isset($kind[0]) && strpos($kind[0], 'C') === 0) {
        return Get::category('id:' . substr($kind[0], 1), null, $fallback, $scope);
    }
    foreach($kind as $v) {
        if($v && strpos($v, 'C') === 0) {
            return Get::category('id:' . substr($v, 1), null, $fallback, $scope);
        }
    }
    return $fallback;
}

function do_category_set($results, $FP, $data) {
    $category = isset($data['kind']) ? do_category_search($data['kind'], rtrim($FP, ':')) : false;
    $results['category_raw'] = Filter::colon($FP . 'category_raw', $category, $results);
    $results['category'] = Filter::colon($FP . 'category', $results['category_raw'], $results);
    return $results;
}

foreach(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR) as $v) {
    $v = File::B($v);
    Filter::add($v . ':output', 'do_category_set');
}