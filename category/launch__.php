<?php

// Quick page type detection ...
if(strpos($config->url_path . '/', $config->category->slug . '/') === 0) {
    $config->page_type = 'category';
    $config->is->posts = true;
    $config->is->post = false;
    Config::set(array(
        'page_type' => 'category',
        'is' => array(
            'posts' => true,
            'post' => false
        )
    ));
}

// Add category link to post(s) footer
function do_category_link($post) {
    global $config, $speak;
    if(isset($post->category) && $post->category !== false) {
        echo O_BEGIN . '<div>' . $speak->category . ': <a href="' . Filter::colon('category:url', $config->url . '/' . $config->category->slug . '/' . $post->category->slug) . '">' . $post->category->name . '</a></div>' . O_END;
    }
}

// Apply weapon(s) ...
foreach(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR) as $v) {
    Weapon::add(File::B($v) . '_footer', 'do_category_link');
}


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
        Shield::abort('404-category');
    }
    $s = Get::articles('DESC', 'kind:C' . $category->id);
    if($articles = Mecha::eat($s)->chunk($offset, $config->category->per_page)->vomit()) {
        $articles = Mecha::walk($articles, function($path) use($excludes) {
            return Get::article($path, $excludes);
        });
    } else {
        Shield::abort('404-category');
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