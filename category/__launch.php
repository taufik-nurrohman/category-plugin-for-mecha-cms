<?php

if(strpos($config->url_path . '/', $config->manager->slug . '/category/') === 0) {
    require __DIR__ . DS . 'workers' . DS . 'route.category.php';
}

Weapon::add('tab_content_1_before', function($page, $segment) use($config, $speak) {
    if( ! is_array($segment) && Mecha::eat(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR))->has(POST . DS . $segment)) {
        if($segment !== 'page') include __DIR__ . DS . 'workers' . DS . 'unit' . DS . 'form' . DS . 'post' . DS . 'category.php';
    }
}, 6.09);

function __do_category_add($G, $P) {
    $r = $P['data'];
    if(isset($r['category'])) {
        array_unshift($r['kind'], $r['category']);
        $name = Date::slug($r['date']) . '_' . implode(',', $r['kind']) . '_' . $r['slug'] . $r['extension'];
        File::open($r['path'])->renameTo($name);
    }
}

foreach(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR) as $v) {
    $v = File::B($v);
    Weapon::add('on_' . $v . '_update', '__do_category_add', 11);
}