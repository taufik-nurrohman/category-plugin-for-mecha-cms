<?php

// Get stored category data (internal only)
Get::plug('state_category', function($id = null, $fallback = array(), $all = true, $scope = null) {
    $config = Config::get();
    $speak = Config::speak();
    $category = array(
        0 => array(
            'name' => $speak->uncategorized,
            'slug' => Text::parse($speak->uncategorized, '->slug'),
            'description' => ""
        )
    );
    if($file = File::exist(STATE . DS . 'category.txt')) {
        Mecha::extend($category, File::open($file)->unserialize());
    }
    if($all) {
        if($e = File::exist(SHIELD . DS . $config->shield . DS . 'workers' . DS . 'categories.php')) {
            $category_e = include $e;
            Mecha::extend($category, $category_e);
        }
        foreach(glob(PLUGIN . DS . '*' . DS . '{launch,launch__,__launch}.php', GLOB_NOSORT | GLOB_BRACE) as $active) {
            if($e = File::exist(File::D($active) . DS . 'workers' . DS . 'categories.php')) {
                $category_e = include $e;
                Mecha::extend($category, $category_e);
            }
        }
    }
    // Filter output(s) by `scope`
    $category_alt = array();
    if( ! is_null($scope)) {
        foreach($category as $k => $v) {
            foreach(explode(',', $scope) as $s) {
                if( ! isset($v['scope']) || strpos(',' . $v['scope'] . ',', ',' . $s . ',') !== false) {
                    $category_alt[$k] = $v;
                }
            }
        }
        $category = $category_alt;
    }
    unset($category_alt);
    $category = Filter::apply('state:category', Converter::strEval($category));
    // Filter output(s) by `id`
    if( ! is_null($id)) {
        return Mecha::GVR($category, $id, $fallback);
    }
    // No filter
    return $category;
});

// Get categor(y|ies) ...
Get::plug('categories', function($order = 'ASC', $sorter = 'name', $scope = null) {
    $categories = Get::state_category(null, array(), true, $scope);
    $results = array();
    foreach($categories as $k => $v) {
        $results[] = (object) array(
            'id' => Filter::colon('category:id', $k, $categories),
            'name' => Filter::colon('category:name', $v['name'], $categories),
            'slug' => Filter::colon('category:slug', $v['slug'], $categories),
            'description' => Filter::colon('category:description', $v['description'], $categories)
        );
    }
    unset($categories);
    return Mecha::eat($results)->order($order, $sorter)->vomit();
});

// --ibid (read by computer)
Get::plug('categorys', function() {
    return call_user_func_array('Get::categories', func_get_args());
});

// Get category ...
Get::plug('category', function($filter, $output = null, $fallback = false, $scope = null) {
    $categories = Get::categories('ASC', 'name', $scope);
    // alternate 2: `Get::category('id:2', 'slug', false)`
    if(strpos($filter, ':') !== false) {
        list($key, $value) = explode(':', $filter, 2);
        $value = Converter::strEval($value);
        foreach($categories as $k => $v) {
            if(isset($v->{$key}) && $v->{$key} === $value) {
                return is_null($output) ? $v : (isset($v->{$output}) ? $v->{$output} : $fallback);
            }
        }
    // alternate 1: `Get::category(2, 'slug', false)
    } else {
        foreach($categories as $k => $v) {
            if(
                (is_numeric($filter) && (int) $filter === (int) $v->id) || // by ID
                (string) $filter === (string) $v->slug || // by slug
                (string) $filter === (string) $v->name // by name
            ) {
                return is_null($output) ? $v : (isset($v->{$output}) ? $v->{$output} : $fallback);
            }
        }
    }
    return $fallback;
});

// Get category specific to its scope
foreach(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR) as $v) {
    $v = File::B($v);
    Get::plug($v . 'Categories', function($order = 'ASC', $sorter = 'name') use($v) {
        return Get::categories($order, $sorter, $v);
    });
    Get::plug($v . 'Categorys', function($order = 'ASC', $sorter = 'name') use($v) { // --ibid (read by computer)
        return Get::categories($order, $sorter, $v);
    });
    Get::plug($v . 'Category', function($filter, $output = null, $fallback = false) use($v) {
        return Get::category($filter, $output, $fallback, $v);
    });
}