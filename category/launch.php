<?php


Config::set('category', array(
    'title' => 'Category %s',
	'slug' => 'category',
	'per_page' => 7
));

$config = Config::get();


foreach(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR) as $v) {
	$v = File::B($v);
	Get::plug($v . 'Category', function($filter, $output = null, $fallback = false) use($v) {
		return Get::tag($filter, $output, $fallback, $v);
	});
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
    // `meta` description data based on current tag description
    if($description = Text::parse($category->description, '->text')) {
        Config::set('category.description', $description);
    }
    Shield::attach('index-category');
}, 50);