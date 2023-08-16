jQuery(document).ready(function($) {
    //Function from Bluthemes, lets you add li elemants to affix object without having to alter and data attributes set out by bootstrap
    setTimeout(function(){
        // name your elements here
        var stickyElement   = '.panel-affix',   // the element you want to make sticky
            bottomElement   = '#main-footer'; // the bottom element where you want the sticky element to stop (usually the footer) 

        // make sure the element exists on the page before trying to initalize
        if($( stickyElement ).length){
            $( stickyElement ).each(function(){
                // let's save some messy code in clean variables
                // when should we start affixing? (the amount of pixels to the top from the element)
                var fromTop = $( this ).offset().top, 
                    // where is the bottom of the element?
                    fromBottom = $( document ).height()-($( this ).offset().top + $( this ).outerHeight()),
                    // where should we stop? (the amount of pixels from the top where the bottom element is)
                    // also add the outer height mismatch to the height of the element to account for padding and borders
                    stopOn = $( document ).height()-( $( bottomElement ).offset().top)+($( this ).outerHeight() - $( this ).height()); 

                // if the element doesn't need to get sticky, then skip it so it won't mess up your layout
                if( (fromBottom-stopOn) > 200 ){
                    // let's put a sticky width on the element and assign it to the top
                    $( this ).css('width', $( this ).width()).css('top', 0).css('position', '');
                    // assign the affix to the element
                    $( this ).affix({
                        offset: { 
                            // make it stick where the top pixel of the element is
                            top: fromTop,  
                            // make it stop where the top pixel of the bottom element is
                            bottom: stopOn
                        }
                    // when the affix get's called then make sure the position is the default (fixed) and it's at the top
                    }).on('affix.bs.affix', function(){
                        $( this ).css('top', 0).css('position', '');
                    });
                }
                // trigger the scroll event so it always activates 
                $( window ).trigger('scroll'); 
            }); 
        }
    }, 50);

    //Offset scrollspy height to highlight li elements at good window height
    
    $('body').scrollspy({target: ".nav-scrollpy", offset: 50});

    //Smooth Scrolling For Internal Page Links
    $('.panel-affix a').on('click', function() {
        var href = $(this).attr('href');
        if ( href == '#' ) {
            return false;
        }
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');

            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top
                }, 1000);
                return false;
            }
        }
    });

    $('.show-navigation').on('click', function(e) {
        e.preventDefault();
        $('.navigation-wrapper').toggleClass('active');
    });
    $('.close-nav').on('click', function(e) {
        e.preventDefault();
        $('.navigation-wrapper').removeClass('active');
    });


    $('.header-nav').on('click', function(){
        var parent_con = $(this).closest('.navigation');
        var parent_this = $(this).closest('.nav-scrollpy');
        parent_con.find('.nav.nav-active').removeClass('nav-active').closest('.nav-scrollpy').find('.header-nav span').html('+');
        parent_this.find('.nav').addClass('nav-active').closest('.nav-scrollpy').find('.header-nav span').html('-');
    });
    
});