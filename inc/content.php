<div id="archive-stalker-wrapper">
    <?php $this->v_dates_html($months) ?>
    <img style="display:none" id = "archive-stalker-loading-img" src ="<?= ARST_PLUGIN_URL_SRC ?>/widget-loading.gif"/>
    <div id="archive-stalker-container">
        <table id ="archive-stalker-container-table">
            <?php $this->v_posts_html($posts,$post_desc) ?>
        </table>
    </div>
    <?php $this->v_pagination_html($prev_page,$next_page); ?>
</div>