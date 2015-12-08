<script>
    var arst_posts = <?= json_encode($posts); ?>;
    var arst_total_posts=<?= $total_posts; ?>; 
    var arst_plugin_src_url =  '<?= ARST_PLUGIN_URL_SRC; ?>';
    var arst_post_desc = <?= json_encode($options['post_desc']); ?>;
    var arst_posts_per_page=<?= $options['posts_per_page']; ?>;
    var arst_categories = <?= json_encode($options['categories']); ?>
</script>