<?php

$categories = array();
foreach(call_user_func('Get::' . $segment . 'Categories') as $category) {
    if($category && $category->id !== 0) {
        $categories['C' . $category->id] = $category->name;
    }
}

?>
<div class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->category; ?></span>
  <div class="grid span-5"><?php echo Form::select('category', array('C0' => '&mdash; ' . $speak->none . ' &mdash;') + $categories, Request::get('category', Guardian::wayback('category', $page->category_raw !== false ? 'C' . $page->category_raw->id : 'C0'))); ?></div>
</div>