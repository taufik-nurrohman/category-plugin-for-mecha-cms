Category Plugin for Mecha CMS
=============================

> Category feature.

Categories are similar with tags. The difference is that you can only determine one category per article.

### Page Types

 - `category` → for category page

~~~ .php
if($config->page_type === 'category') {
    echo 'You are in the category page.';
}
~~~

### Methods

#### Get Categories

~~~ .php
Get::categories($order = 'ASC', $sorter = 'name');
~~~

#### Get Category

Return specific category item filtered by its available data.

~~~ .php
Get::category($filter, $output = null, $fallback = false);
~~~

### Widgets

#### Category

~~~ .php
echo Widget::category();
~~~

~~~ .php
echo Widget::category('LIST');
echo Widget::category('CLOUD');
echo Widget::category('DROPDOWN');
~~~

~~~ .php
echo Shield::chunk('block.widget', array(
    'title' => $speak->widget->categories,
    'content' => Widget::category()
));
~~~