<?php

// Quick page type detection ...
if(strpos($config->url_path . '/', $config->category->slug . '/') === 0) {
    $config->page_type = 'category';
    Config::set('page_type', 'category');
}

// Add `$post->category` field to post(s)
function do_category_field($data) {
    foreach(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR) as $v) {
        $v = File::B($v);
        // post(s)
        if(isset($data[$v . 's']) && $data[$v . 's'] !== false) {
            foreach($data[$v . 's'] as &$vv) {
                $vv->category_raw = Filter::colon($v . ':category_raw', do_category_search($vv->kind, $v), $vv);
                $vv->category = Filter::colon($v . ':category', $vv->category_raw, $vv);
            }
        // post
        } else if(isset($data[$v]) && $data[$v] !== false) {
            $s = $data[$v];
            $data[$v]->category_raw = Filter::colon($v . ':category_raw', do_category_search($s->kind, $v), $s);
            $data[$v]->category = Filter::colon($v . ':category', $data[$v]->category_raw, $s);
        }
    }
    return $data;
}

Filter::add('shield:lot', 'do_category_field');


/**
 * Category Page
 * -------------
 *
 * [1]. category/category-slug
 * [2]. category/category-slug/1
 *
 */

Route::accept(array($config->category->slug . '/(:any)', $config->category->slug . '/(:any)/(:num)'), function($slug = "", $offset = 1) use($config) {
    // Exclude these fields ...
    $excludes = (array) Config::get($config->page_type . '_fields_exclude', array('content'));
    if( ! $category = Get::articleCategory('slug:' . $slug)) {
        Shield::abort('404-tag');
    }
    $s = Get::articles('DESC', 'kind:C' . $category->id);
    if($articles = Mecha::eat($s)->chunk($offset, $config->tag->per_page)->vomit()) {
        $articles = Mecha::walk($articles, function($path) use($excludes) {
            return Get::article($path, $excludes);
        });
    } else {
        Shield::abort('404-tag');
    }
    Filter::add('pager:url', function($url) {
        return Filter::apply('category:url', $url);
    });
    Config::set(array(
        'page_title' => sprintf($config->category->title, $category->name) . $config->title_separator . $config->title,
        'category_query' => $slug,
        'offset' => $offset,
        'articles' => $articles,
        'pagination' => Navigator::extract($s, $offset, $config->category->per_page, $config->category->slug . '/' . $slug)
    ));
    // `meta` description data based on current category description
    if($description = Text::parse($category->description, '->text')) {
        Config::set('category.description', $description);
    }
    Shield::attach('index-category');
}, 50);