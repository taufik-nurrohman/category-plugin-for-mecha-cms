<?php

Widget::plug('category', function($type = 'LIST', $order = 'ASC', $sorter = 'name', $max_level = 6, $folder = ARTICLE) {
    $T1 = TAB;
    $T2 = str_repeat($T1, 2);
    $kin = strtolower($type);
    $p = $folder !== POST ? File::B($folder) : 'post';
    $id = Config::get('widget_category_' . $kin . '_id', 0) + 1;
    $config = Config::get();
    $speak = Config::speak();
    $query = $config->category_query;
    $counters = array();
    $categories = array();
    $html = O_BEGIN . '<div class="widget widget-category widget-category-' . $kin . '" id="widget-category-' . $kin . '-' . $id . '">' . NL;
    if($files = call_user_func('Get::' . $p . 's')) {
        foreach($files as $file) {
            list($_time, $_kind, $_slug) = explode('_', File::N($file), 3);
            foreach(explode(',', $_kind) as $kind) {
                if(strpos($kind, 'C') === 0) {
                    $counters[] = substr($kind, 1);
                    break;
                }
            }
        }
        foreach(array_count_values($counters) as $id => $count) {
            $category = call_user_func('Get::' . $p . 'Category', 'id:' . $id);
            if($category && $id !== 0) {
                $categories[] = array(
                    'id' => $id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'count' => $count
                );
            }
        }
        if( ! empty($categories)) {
            $categories = Mecha::eat($categories)->order($order, $sorter)->vomit();
            if($type === 'LIST') {
                $html .= $T1 . '<ul>' . NL;
                foreach($categories as $category) {
                    $html .= $T2 . '<li' . ($query === $category['slug'] ? ' class="' . Widget::$config['classes']['current'] . '"' : "") . '><a href="' . Filter::colon('category:url', $config->url . '/' . $config->category->slug . '/' . $category['slug']) . '">' . $category['name'] . '</a> <span class="counter">' . $category['count'] . '</span></li>' . NL;
                }
                $html .= $T1 . '</ul>' . NL;
            }
            if($type === 'CLOUD') {
                $categories_counter = array();
                foreach($categories as $category) {
                    $categories_counter[] = $category['count'];
                }
                $highest_count = max($categories_counter);
                $_html = array();
                foreach($categories as $category) {
                    $size = ceil(($category['count'] / $highest_count) * $max_level);
                    $_html[] = '<span class="size size-' . $size . ($query === $category['slug'] ? ' ' . Widget::$config['classes']['current'] : "") . '"><a href="' . Filter::colon('category:url', $config->url . '/' . $config->category->slug . '/' . $category['slug']) . '" rel="tag">' . $category['name'] . '</a> <span class="counter">' . $category['count'] . '</span></span>';
                }
                $html .= $T1 . implode(' ', $_html) . NL;
            }
            if($type === 'DROPDOWN') {
                $html .= $T1 . '<select>' . NL . ($query === "" ? $T2 . '<option disabled selected>' . $speak->select . '&hellip;</option>' . NL : "");
                foreach($categories as $category) {
                    $html .= $T2 . '<option value="' . Filter::colon('category:url', $config->url . '/' . $config->category->slug . '/' . $category['slug']) . '"' . ($query === $category['slug'] ? ' selected' : "") . '>' . $category['name'] . ' (' . $category['count'] . ')</option>' . NL;
                }
                $html .= $T1 . '</select>' . NL;
            }
        } else {
            $html .= $T1 . Config::speak('notify_empty', strtolower($speak->categories)) . NL;
        }
    } else {
        $html .= $T1 . Config::speak('notify_empty', strtolower($speak->{$p . 's'})) . NL;
    }
    $html .= '</div>' . O_END;
    Config::set('widget_category_' . $kin . '_id', $id);
    return Filter::apply(array('widget:category.' . $kin, 'widget:category', 'widget'), $html, $id);
});