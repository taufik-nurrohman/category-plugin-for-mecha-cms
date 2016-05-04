<?php

if(strpos($config->url_path . '/', $config->manager->slug . '/category/') === 0) {
    require __DIR__ . DS . 'workers' . DS . 'route.category.php';
}

Weapon::add('tab_content_1_before', function($page, $segment) use($config, $speak) {
    if( ! is_array($segment) && Mecha::eat(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR))->has(POST . DS . $segment)) {
        include __DIR__ . DS . 'workers' . DS . 'unit' . DS . 'form' . DS . 'post' . DS . 'category.php';
    }
}, 6.09);

function __do_category_add($G, $P) {
    $r = $P['data'];
    if(isset($r['category'])) {
        $rr = ! empty($r['kind']) ? ',' : "";
        $name = Date::slug($r['date']) . '_' . $r['category'] . $rr . implode(',', $r['kind']) . '_' . $r['slug'] . $r['extension'];
        File::open($r['path'])->renameTo($name);
    }
}

function __do_category_field($data) {
    global $segment;
    if(is_array($segment)) {
        $segment = $segment[0];
    }
    // post(s)
    if(isset($data['pages']) && $data['pages'] !== false) {
        foreach($data['pages'] as &$vv) {
            $vv->category_raw = Filter::colon($segment . ':category_raw', do_category_search($vv->kind, $segment), $vv);
            $vv->category = Filter::colon($segment . ':category', $vv->category_raw, $vv);
        }
    // post
    } else if(isset($data['page']) && $data['page'] !== false) {
        $s = $data['page'];
        $data['page']->category_raw = Filter::colon($segment . ':category_raw', do_category_search($s->kind, $segment), $s);
        $data['page']->category = Filter::colon($segment . ':category', $data['page']->category_raw, $s);
    }
    return $data;
}

Filter::add('shield:lot', '__do_category_field');

foreach(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR) as $v) {
    $v = File::B($v);
    Weapon::add('on_' . $v . '_update', '__do_category_add');
}