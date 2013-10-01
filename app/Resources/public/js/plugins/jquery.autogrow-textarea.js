var __ag_focus = false;
(function($) {

    /*
     * Auto-growing textareas; technique ripped from Facebook
     */
    $.fn.autoGrow = function(options) {
        
        this.filter('textarea').each(function() {
            
            var $this       = $(this),
                minHeight   = $this.height(),
                lineHeight  = $this.css('lineHeight');
            
            var shadow = $('<div></div>').css({
                position:   'absolute',
                top:        -10000,
                left:       -10000,
                width:      $(this).width(),
                fontSize:   $this.css('fontSize'),
                fontFamily: $this.css('fontFamily'),
                lineHeight: $this.css('lineHeight'),
                resize:     'none'
            }).appendTo(document.body);
            
            
            var update = function() {
                console.log($(this).width());
                var val = this.value.replace(/</g, '&lt;')
                                    .replace(/>/g, '&gt;')
                                    .replace(/&/g, '&amp;')
                                    .replace(/\n/g, '<br/>');
                
                shadow.html(val);
                $(this).css('height', Math.max(shadow.height(), minHeight, 20));
            };
            
            
            $(this).change(update).keyup(update).keydown(update).click(update).blur(update);
            
            update.apply(this);
            
        });
        
        return this;
        
    };
    
})(jQuery);