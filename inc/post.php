<tr class ="arhive-stalker-post">
    <td class="archive-stalker-main-td">
    <?php foreach($post_desc as $key=>$desc): ?>
        <?= $desc['label'] . $post[$key] ?><br>
    <?php endforeach; ?>
    </td>
    <td class="archive-stalker-blog-icon-container">
        <a href="#" class="archive-stalker-close-window"><img src="<?= ARST_PLUGIN_URL_SRC ?>/close_window.png"/></a>
        <a href="#" id="<?= $post_id ?>" class="archive-stalker-blog-icon"><img  src ="<?= ARST_PLUGIN_URL_SRC ?>/blog-text.png"/></a>
    </td>
</tr>