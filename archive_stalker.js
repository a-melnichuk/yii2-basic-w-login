var ArchiveStalker = function(posts,posts_per_page,total_posts,posts_desc,categories,plugin_src_url)
{
    this.self = this;
    this.month = 0;
    this.year = 0;
    this.page_offset = 0;
    this.prev_window = 0;
    this.is_dragging = false;

    this.posts = posts;
    this.posts_per_page = posts_per_page;
    this.total_posts = total_posts;
    this.posts_desc = posts_desc;
    this.categories = categories;
    this.plugin_src_url = plugin_src_url;
    
    this.check_dragging = function(elem)
    {
        //make window above all other windows
        var id = jQuery(elem).attr('id');
        if(this.prev_window !== id)
        {
            jQuery(elem).insertAfter('#'+this.prev_window);
            this.prev_window = id;
        }   
    };


    //update visibility of 'previous' and 'next' buttons
    this.update_button_visibility_status = function()
    {    
        //hide button container if all posts fit same page
        if(this.page_offset == 0 && this.page_offset + this.posts_per_page >= this.total_posts)
        {
            jQuery("#archive-stalker-button-container").hide();
            return;
        }
        jQuery("#archive-stalker-button-container").show();
        
        //hide 'previous' button on first page
        if(this.page_offset == 0)
            jQuery("#archive-stalker-prev").css("visibility", "hidden");
        else
            jQuery("#archive-stalker-prev").css("visibility", "visible");
        
        //hide 'next' button on last page
        if(this.page_offset + this.posts_per_page >= this.total_posts)
           jQuery("#archive-stalker-next").css("visibility", "hidden"); 
        else 
           jQuery("#archive-stalker-next").css("visibility", "visible");        
    };


    this.get_window_html = function(id)
    {
        var post = this.posts[id];
        var post_desc_str = '';
        //extract window html from last ajax call
        for (var key in this.posts_desc) {
            if (this.posts_desc.hasOwnProperty(key)) {
              var desc = this.posts_desc[key];
              var label = desc['label'];
              var html_begin =  desc['html']['begin'];
              var html_end =  desc['html']['end'];

              post_desc_str+= (html_begin + label + post[key] + html_end );
            }
        }
        //return extracted html
        return '<div class = "archive-stalker-window-wrapper" id ="'+id+'-window">'
                    + post_desc_str
                    + '<a href="#" class = "archive-stalker-window-blog-close"><img src ="'+this.plugin_src_url+'/close_window.png" /></a>'
                    +'<div class ="archive-stalker-window-text">'
                        + post['content']
                    +'</div>'
                +'</div>';
    };

    //init and show window on posts icon click
    this.window_init = function()
    {
        var self = this;
        jQuery(".archive-stalker-blog-icon").click( function(e) 
        {
            e.preventDefault();
            var id = jQuery(this).attr('id');
            var offset = jQuery(this).offset();
            self.prev_window = id + '-window';
            jQuery('body').append( self.get_window_html(id) );
            jQuery('#'+self.prev_window).offset({left:offset.left,top:offset.top});
            
            //make click or selected window above all others on drag and click
            jQuery('.archive-stalker-window-wrapper').draggable().click(function(e)
            {
               self.check_dragging(this);
            })       
            .mousemove(function(e)
            {
                if(self.is_dragging)
                    self.check_dragging(this);
            })
            .mousedown(function(e)
            {
                self.is_dragging = true;
            })
            .mouseup(function(e)
            {
                self.is_dragging = false;
            });
            
            //remove window from DOM on window closing
            jQuery('.archive-stalker-window-blog-close').click(function(e)
            {
                e.preventDefault();
                jQuery(this).parent().remove();
            });
        });
    };

    //send ajax call to update posts
    this.update_widget_container = function()
    {
       var self = this;
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : myAjax.ajaxurl,
            data : 
            {
                action: "update_widget_container", 
                offset : self.page_offset,
                month: self.month,
                year:self.year
            },
            success: function(response) {
                //update posts in container
                console.log(response);
                jQuery("#archive-stalker-container-table").html(response.data);
                self.posts = response.posts;
                self.total_posts = response.total_posts;
                self.window_init(self);
                self.update_button_visibility_status();
                jQuery("#archive-stalker-loading-img").hide();
             }
        }); 
    };
    
    //setup listeners for data acquisition
    this.init = function()
    {
        var self = this;
        this.update_button_visibility_status();
        
        this.window_init();
        //display next page of posts
        jQuery("#archive-stalker-next").click( function(e) {

            e.preventDefault();
            console.log(1);
            if(self.page_offset < self.total_posts)
            {
               jQuery("#archive-stalker-loading-img").show();
               self.page_offset+=self.posts_per_page;
               self.update_widget_container();
            } 
        });
        //display prevous page of posts
        jQuery("#archive-stalker-prev").click( function(e) {
            e.preventDefault(); 
             console.log(1);
            if(self.page_offset != 0)
            {
                jQuery("#archive-stalker-loading-img").show();
                self.page_offset-=self.posts_per_page;    
                self.update_widget_container();
                
            }  
        }); 
        // hide/show months on year click
        jQuery(".archive-stalker-year-ul").click( function(e) {

            e.preventDefault();
            jQuery(this).find('.archive-stalker-month-container').toggle();
        });
        
        // get posts from month clicked
        jQuery(".archive-stalker-monthname").click( function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery("#archive-stalker-loading-img").show();
            var dates = jQuery(this).data();
            self.page_offset = 0;
            self.month = dates.month;
            self.year = dates.year;
            self.update_widget_container();

        });
        
        //get all posts
        jQuery("#archive-stalker-calendar-img-container").click( function(e) {
            e.preventDefault();
            jQuery("#archive-stalker-loading-img").show();
            self.page_offset = 0;
            self.month = 0;
            self.year = 0;
            self.update_widget_container();
        });
    };
    this.init();
    
};

jQuery(document).ready( function($) {
    //create object if variable are defined
    if( typeof arst_posts_per_page != 'undefined')
    var archiveStalker = new ArchiveStalker(arst_posts,
                                            arst_posts_per_page,
                                            arst_total_posts,
                                            arst_post_desc,
                                            arst_categories,
                                            arst_plugin_src_url);
});