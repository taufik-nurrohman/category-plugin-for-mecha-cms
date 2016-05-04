<?php


// Get categor(y|ies) ...
$categories = Get::state_category(null, array(), false);


/**
 * Category Manager
 * ----------------
 */

Route::accept($config->manager->slug . '/category', function() use($config, $speak, $categories) {
    Config::set(array(
        'page_title' => $speak->categories . $config->title_separator . $config->manager->title,
        'cargo' => __DIR__ . DS . 'cargo.category.php'
    ));
    Shield::lot(array(
        'segment' => 'category',
        'files' => ! empty($categories) ? Mecha::O($categories) : false,
        '__DIR__' => PLUGIN . DS . 'manager' . DS . 'workers'
    ))->attach('manager');
});


/**
 * Category Repairer/Igniter
 * -------------------------
 */

Route::accept(array($config->manager->slug . '/category/ignite', $config->manager->slug . '/category/repair/id:(:any)'), function($id = false) use($config, $speak, $categories) {
    if($id === false) {
        Weapon::add('SHIPMENT_REGION_BOTTOM', function() {
            echo '<script>(function($){$.slug(\'name\',\'slug\',\'-\')})(DASHBOARD.$);</script>';
        }, 11);
        $data = array(
            'id' => max(array_keys($categories)) + 1,
            'name' => "",
            'slug' => "",
            'description' => "",
            'scope' => "" // no scope
        );
        $title = Config::speak('manager.title_new_', $speak->category) . $config->title_separator . $config->manager->title;
    } else {
        if( ! isset($categories[$id])) {
            Shield::abort(); // Field not found!
        }
        $data = $categories[$id];
        $data['id'] = $id;
        $title = $speak->editing . ': ' . $data['name'] . $config->title_separator . $config->manager->title;
    }
    foreach($data as $k => $v) {
        $data[$k . '_raw'] = $v;
    }
    $G = array('data' => $data);
    Config::set(array(
        'page_title' => $title,
        'cargo' => __DIR__ . DS . 'repair.category.php'
    ));
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        // Limit HTML tag(s) allowed in the name field
        $request['name'] = Text::parse($request['name'], '->text', str_replace('<a>', "", WISE_CELL_I));
        // Empty name field
        if(trim($request['name']) === "") {
            Notify::error(Config::speak('notify_error_empty_field', $speak->name));
        }
        // Empty slug field
        if(trim($request['slug']) === "") {
            $request['slug'] = $request['name'];
        }
        $s = Text::parse($request['slug'], '->slug');
        $rid = $request['id'];
        if($id === false) {
            $slugs = array();
            foreach($categories as $k => $v) {
                $slugs[$v['slug']] = 1;
            }
            // Duplicate slug
            if(isset($slugs[$s])) {
                Notify::error(Config::speak('notify_error_slug_exist', $s));
            }
            unset($slugs);
            // Duplicate ID
            if(isset($categories[$rid])) {
                Notify::error(Config::speak('notify_invalid_duplicate', $speak->id));
            }
        } else {
            unset($categories[$id]);
        }
        $categories[$rid] = array(
            'name' => $request['name'],
            'slug' => $s,
            'description' => $request['description']
        );
        if(isset($request['scope']) && is_array($request['scope'])) {
            $categories[$rid]['scope'] = implode(',', $request['scope']);
        }
        $P = array('data' => $request);
        $P['data']['id'] = $rid;
        if( ! Notify::errors()) {
            ksort($categories);
            File::serialize($categories)->saveTo(STATE . DS . 'category.txt', 0600);
            Notify::success(Config::speak('notify_success_' . ($id === false ? 'created' : 'updated'), $request['name']));
            Session::set('recent_item_update', $rid);
            Weapon::fire(array('on_category_update', 'on_category_' . ($id === false ? 'construct' : 'repair')), array($G, $P));
            Guardian::kick($id !== $rid ? $config->manager->slug . '/category' : $config->manager->slug . '/category/repair/id:' . $id);
        }
    }
    Shield::lot(array(
        'segment' => 'category',
        'id' => $id,
        'file' => Mecha::O($data),
        '__DIR__' => PLUGIN . DS . 'manager' . DS . 'workers'
    ))->attach('manager');
});


/**
 * Category Killer
 * ---------------
 */

Route::accept($config->manager->slug . '/category/kill/id:(:any)', function($id = false) use($config, $speak, $categories) {
    if( ! Guardian::happy(1)) {
        Shield::abort();
    }
    if( ! isset($categories[$id])) {
        Shield::abort(); // Tag not found!
    }
    $title = $categories[$id]['name'];
    Config::set(array(
        'page_title' => $speak->deleting . ': ' . $title . $config->title_separator . $config->manager->title,
        'cargo' => __DIR__ . DS . 'kill.category.php'
    ));
    $G = array('data' => $categories);
    $G['data']['id'] = $id;
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        unset($categories[$id]); // delete ...
        ksort($categories);
        $P = array('data' => $categories);
        $P['data']['id'] = $id;
        File::serialize($categories)->saveTo(STATE . DS . 'category.txt', 0600);
        Notify::success(Config::speak('notify_success_deleted', $title));
        Weapon::fire(array('on_category_update', 'on_category_destruct'), array($G, $P));
        Guardian::kick($config->manager->slug . '/category');
    } else {
        Notify::warning(Config::speak('notify_confirm_delete_', '<strong>' . $title . '</strong>'));
    }
    Shield::lot(array(
        'segment' => 'category',
        'id' => $id,
        'file' => Mecha::O($categories[$id]),
        '__DIR__' => PLUGIN . DS . 'manager' . DS . 'workers'
    ))->attach('manager');
});