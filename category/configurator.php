<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->title; ?></span>
  <span class="grid span-5">
  <?php echo Form::text('category[title]', $config->category->title, null); ?>
  </span>
</label>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->slug; ?></span>
  <span class="grid span-5">
  <?php echo Form::text('category[slug]', $config->category->slug); ?>
  </span>
</label>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->manager->title_per_page; ?></span>
  <span class="grid span-5">
  <?php echo Form::number('category[per_page]', $config->category->per_page, null, array(
      'min' => 1
  )); ?>
  </span>
</label>