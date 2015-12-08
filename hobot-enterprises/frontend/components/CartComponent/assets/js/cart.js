var CartPopup = function(widgetDiv,popupContainer,countedCol,counterId,actionUrl){
    if(typeof actionUrl === 'undefined'){
        throw 'Please,enter url of action to show popup';
    }else{
        this.actionUrl = actionUrl;
    }
    this.popupContainer = (typeof popupContainer === 'undefined') ? '#cw_popup' : popupContainer;
    this.countedCol = (typeof countedCol === 'undefined') ? 'false' : countedCol;
    this.counterId = (typeof counterId === 'undefined') ? '#cw_counter' : counterId;
    this.widgetDiv = (typeof widgetDiv === 'undefined') ? '#cw_widget' : widgetDiv;
    this.timeout = 1000;
}

CartPopup.prototype.setRowRemoveListener = function(){
    var self = this;
    $(this.popupContainer+' a').click(function(e){
         e.preventDefault();
         var url = this.href;
         //hide product item from table
         var el = $(self.popupContainer+' tr')[parseInt(this.id)+1];
         el.style.display = 'none';
         var request = $.ajax({
             type:'GET',
             url:url
         });
         //update cart table
         request.done(function (data) {
            $(self.popupContainer).html(data);
            self.setRowRemoveListener();
        });
        //retry ajax request after timeout on request fail
        request.fail(function() { 
            setTimeout(function() {
               self.getCartData();
            }, self.timeout);
        });
    });       
};

CartPopup.prototype.getCartData = function(){
    var self = this;
    var request = $.ajax({
                  type:'GET',
                  url:self.actionUrl
              });
    //update cart table
    request.done(function (data) {
        $(self.popupContainer).html(data);
        self.setRowRemoveListener();

    });
    //retry ajax request after timeout on request fail
    request.fail(function() { 
        setTimeout(function() {
           self.getCartData();
        }, self.timeout);
    });
};

CartPopup.prototype.setCounterUpdateListener = function(){
    var self = this;
    //update counter on ajax events
    $( document ).ajaxSuccess(function(event, xhr, settings ) {
        if(self.countedCol!=='false'){
            var counter = 0;
            $(self.countedCol).each(function(i, obj) {
                counter += parseInt(obj.innerHTML);   
            });
            $(self.counterId).html(counter);
        }
    });
};

CartPopup.prototype.setCartPopupListeners = function(){
    var self = this;
    var hoveredOnCartImage = false;
    var hoveredOnCartPopup = false;   
    //handle cart popups
    $(self.widgetDiv).parent().mouseenter(function() {
       hoveredOnCartImage = true;
       self.getCartData();
        $(self.popupContainer).show();
      })
      .mouseleave(function() {                                  
            setTimeout(function() {
                hoveredOnCartImage = false;
                if(!hoveredOnCartImage && !hoveredOnCartPopup){
                    $(self.popupContainer).hide();
                }
            }, 100);
    }); 

    $(this.popupContainer).mouseenter(function() {      
        hoveredOnCartPopup = true      
    }).mouseleave(function() {
        
        hoveredOnCartPopup = false;
        if(!hoveredOnCartImage && !hoveredOnCartPopup){
            $(this).hide();
        }
    });         
    this.getCartData();
};

CartPopup.prototype.init = function(){
    this.setCounterUpdateListener();
    this.setCartPopupListeners();
};