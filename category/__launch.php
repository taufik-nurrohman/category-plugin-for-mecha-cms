<?php

Weapon::add('tab_content_1_before', function($page, $segment) use($config, $speak) {
    echo '<label class="grid-group">
<span class="grid span-1 form-label">' . $speak->category . '</span>
<span class="grid span-5">
' . Form::select('category', array(
'C0' => $speak->uncategorised,
'C1' => 'Foo',
'C2' => 'Bar',
'C3' => 'Baz'
), "") . '
</span>
</label>';
}, 6.09);



function do_prepend_category($G, $P) {
	$r = $P['data'];
	sort($r['kind']);
	$name_o = POST . DS . File::B(File::D($r['path'])) . DS . Date::slug($r['date']) . '_' . implode(',', $r['kind']) . '_' . $r['slug'] . $r['extension'];
	$name_n = Date::slug($r['date']) . '_' . $r['category'] . ',' . implode(',', $r['kind']) . '_' . $r['slug'] . $r['extension'];
    File::open($name_o)->renameTo($name_n);
}

Weapon::add('on_article_update', 'do_prepend_category');