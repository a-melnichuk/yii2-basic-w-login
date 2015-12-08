<h4>General Settings:</h4>
<p>
    <label>How many posts would you you like to display per page?</label><br>
    <input name="<?= $this->get_field_name('posts_per_page') ?>" type="number" min="1" value="<?= $options['posts_per_page'] ?>" />
</p>
<h4>Label Settings:</h4>
<p>
  <label>'Next' page:</label>
  <input name="<?= $this->get_field_name('next_page_label') ?>" value="<?= $instance['next_page_label'] ?>" />
</p>
<p>
    <label>'Previous' page:</label>
    <input name="<?= $this->get_field_name('prev_page_label') ?>" value="<?= $instance['prev_page_label'] ?>" />
</p>

<?php 
foreach($options['post_desc'] as $name=>$desc){ 
?>
<p>
<label><?= ucfirst($name).':' ?></label>
<input name ="<?= $this->get_field_name($name) ?>" value="<?= $desc['label'] ?>" />
</p>
<?php } ?>
<h4>Categories Displayed:</h4>
<?php foreach($categories as $cat){
   $cat_name = $cat->name; 
   $checked = $instance[$cat_name] ? 'checked="checked"' : ''; 
?>
<p>
<label><?= $cat_name ?></label>
<input type="checkbox" <?= $checked; ?>  name="<?= $this->get_field_name($cat_name); ?>" />
</p>
<?php } ?>
