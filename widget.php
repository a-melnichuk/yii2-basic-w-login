<?php
define('ARST_PLUGIN_NAME','archive_stalker');
define('ARST_PLUGIN_URL',WP_PLUGIN_URL .'/'. ARST_PLUGIN_NAME);
define('ARST_PLUGIN_URL_SRC',ARST_PLUGIN_URL . '/src');

class Archive_Stalker_Widget extends WP_Widget {
    //widget defaults
    const POSTS_PER_PAGE_DEFAULT = 5;        
    const NEXT_PAGE_LABEL_DEFAULT ='Next Page &#187;';
    const PREV_PAGE_LABEL_DEFAULT ='&#171; Prev Page';
    const TITLE_LABEL_DEFAULT ='title:';
    const AUTHOR_LABEL_DEFAULT ='by ';
    const DATE_LABEL_DEFAULT ='on ';
    const CATEGORY_LABEL_DEFAULT ='category:';
    
    /*
     * Widget Methods
     */
    
    function __construct() {
        parent::__construct( 'archive_stalker_widget', 'Archive Stalker' );
        //add ajax listeners on instantiation
        add_action("wp_ajax_update_widget_container",array($this, "update_widget_container") );
        add_action("wp_ajax_nopriv_update_widget_container", array($this,"update_widget_container") );
    }

    function widget( $args, $instance ) {

        $options = self::get_options();
        //display widget only if some category is selected
        if($options['categories']){
            //get all posts from allowed categories
            $posts_desc = array(
                  'post_type' => 'post',
                  'post_status' => 'publish',
                  'orderby'   => 'date',
                  'order'     => 'DESC',
                  'category__in'=> $options['categories'],
                  'posts_per_page' => $options['posts_per_page'],
            );   
            $wp_q = new WP_Query($posts_desc);
            //get generated posts data array and html
            $result = $this->get_posts_html($wp_q);
            $total_posts = $wp_q->found_posts;
            $this->v_js_variables($result['posts'], $total_posts,$options);
            $this->v_widget_html($options['post_desc'],
                                $result['posts'],
                                $this->get_months(),
                                $instance['prev_page_label'],
                                $instance['next_page_label']);
            
        }
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $options = $this->get_options();
        $options['posts_per_page'] = esc_attr($new_instance['posts_per_page']);
        $instance['prev_page_label'] = esc_attr($new_instance['prev_page_label']);
        $instance['next_page_label'] = esc_attr($new_instance['next_page_label']);
        
        //update label options
        foreach($options['post_desc'] as $key=>$label)
        {
            $options['post_desc'][$key]['label'] = esc_attr($new_instance[$key]);
        }
        //update categories allowed
        $options['categories'] = array();
        $categories = self::get_post_categories();
        foreach($categories as $cat)
        {
            $cat_name = $cat->name;
            if($new_instance[$cat_name]) 
            {
                $options['categories'][] = $cat->cat_ID;
                $instance[$cat_name] = 1;
            } else  $instance[$cat_name] = 0;
        }
        //save changes
        update_option(ARST_PLUGIN_NAME, $options);

        return $instance;
    }

    function form( $instance ) {
        $options = self::get_options();
        $instance['prev_page_label'] = $instance['prev_page_label'] == '' ? self::PREV_PAGE_LABEL_DEFAULT : $instance['prev_page_label'];
        $instance['next_page_label'] = $instance['next_page_label'] == '' ? self::NEXT_PAGE_LABEL_DEFAULT : $instance['next_page_label'];
        $categories = self::get_post_categories();
        require( 'inc/widget_fields.php' );
    } 

    /*
     * Activation methods
     */
    
    //run on plugin activation
    public function activate()
    {
        $options = self::get_options();
        
        //dont reset options if plugin was activated once
        if( !$options['activated']) {
            $data = array();
            
            //set html tags of posts and default label values 
            $name = 'title';
            $data[$name] = self::set_data_description(self::TITLE_LABEL_DEFAULT,
                    '<h1 class ="archive-stalker-window-blog-title">','</h1>');
            $name = 'author';
            $data[$name] =  self::set_data_description( self::AUTHOR_LABEL_DEFAULT,
                    '<p class = "archive-stalker-window-blog-info">','<br>');
            $name = 'category';
            $data[$name] =  self::set_data_description(self::CATEGORY_LABEL_DEFAULT,
                    '','<br>');
            $name = 'date';
            $data[$name] =  self::set_data_description(self::DATE_LABEL_DEFAULT,
                    '','</p>');        
            $options['post_desc'] = $data;
            $options['posts_per_page'] = self::POSTS_PER_PAGE_DEFAULT;

            //set list of non-empty category id's
            $categories = self::get_post_categories();
            $options['categories']= array();      
            foreach ($categories as $cat)
            {
                $options['categories'][] = $cat->cat_ID;
            }
            //plugin was activated once
            $options['activated'] = true;
            update_option(ARST_PLUGIN_NAME,$options);
        }
    }
 
    
    static function get_options()
    {
        return get_option(ARST_PLUGIN_NAME);
    }

    //set html tags and label of default data
    static function set_data_description($label,$html_begin,$html_end)
    {
        return array(
            'html' => array(
                 'begin'=> $html_begin,
                 'end'=> $html_end
             ),
            'label' => $label,
         );
    }
    
    //get non-empty categories of posts
    static function get_post_categories()
    {
        $args = array(
            'type' => 'post',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 1,
        );
        return get_categories( $args );
    }
    
    
    /*
     * Models
     */
    
   function get_months()
    {
        global $wpdb;
        //get allowed categories
        $cats = implode(',',self::get_options()['categories']);
        //query year,month,month name from categories
        return $wpdb->get_results(
            "SELECT DISTINCT
            YEAR({$wpdb->posts}.post_date) as year,
            MONTH({$wpdb->posts}.post_date) as month ,
            MONTHNAME({$wpdb->posts}.post_date) as monthname
            FROM {$wpdb->posts}
            INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)
            INNER JOIN {$wpdb->term_taxonomy} ON ({$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id)
            WHERE ({$wpdb->term_taxonomy}.term_id IN($cats)
            AND {$wpdb->term_taxonomy}.taxonomy = 'category'
            AND {$wpdb->posts}.post_type = 'post'
            AND {$wpdb->posts}.post_status = 'publish')
            ORDER BY {$wpdb->posts}.post_date DESC"
        );
    }
    
    
    //get html content of given post
    function get_post_html()
    {
        return array(
            'id'=> 'archive-stalker-close-window-' . get_the_ID(),
            'title'=> get_the_title(),
            'author'=> get_the_author(),
            'category'=> get_the_category()[0]->name,
            'date'=> get_the_date(),
            'content'=>  get_the_content()
        );
    }
    
    function get_posts_html($wp_query)
    {
        $results = array();
        $results['posts'] = array();
        while ($wp_query->have_posts()) 
        {
            $wp_query->the_post();
            //get html values of post as assoc array
            $post = $this->get_post_html();
            //hold html values of posts as assoc array
            $results['posts'][ $post['id'] ] = $post;
        }
        return $results;
    }
    
    
    /*
     * VIEWS
     */
    
    
    function v_pagination_html($prev_page,$next_page)
    {      
        require_once( 'inc/pagination.php' );
    }     
    
    function v_js_variables($posts,$total_posts,$options)
    {
        require_once( 'inc/js_variables.php' );
    }
    //get html for 'year -> month's' dropdown menu
    function v_dates_html($months)
    {
        require_once( 'inc/dates.php' );
    }
    
    function v_post_html($post_id,$post_desc = array() ,$post = array())
    {   //there are multiple instances of post
        include( 'inc/post.php' );
    }
    
    function v_posts_html($posts,$post_desc)
    {    //prosts are requested multiple times via ajax
        require_once( 'inc/posts.php' );
    }
    
    function v_widget_html($post_desc,$posts,$months,$prev_page,$next_page)
    {
        require_once( 'inc/content.php' );
    }   
    
    
    /*
     * Ajax
     */
  
    //update widget's content via ajax
    function update_widget_container()
    {   //get posts offset
        $offset = $_REQUEST["offset"];
        $options = self::get_options();
        //query posts given offset from allowed categories
        $posts_desc = array(
              'post_type' => 'post',
              'post_status' => 'publish',
              'orderby'   => 'date',
              'order'     => 'DESC',
              'offset' => $offset,
              'category__in'=> $options["categories"],
              'posts_per_page' => $options["posts_per_page"],
        );
        $month = $_REQUEST["month"];
        $year = $_REQUEST["year"];
        
        //if month and year are sent, query posts from month of given year
        if($month && $year)
        {
            $posts_desc['year'] = $year;
            $posts_desc['monthnum'] = $month;
        }

        $wp_q = new WP_Query($posts_desc);
        $result = $this->get_posts_html($wp_q);
        ob_start();
        $this->v_posts_html($result['posts'], $options['post_desc']);
        $result['data'] = ob_get_clean();
        $result['total_posts'] = $wp_q->found_posts; 
        
        echo json_encode($result);
        die();
    }     
}
