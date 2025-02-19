define(['jquery', 'theme_klass/slick'], function($, slick) {
    var klassFront = function() {
        this.frontPageAction();
        this.frontPageSliders();
    };

    klassFront.prototype.frontPageAction = function() {
        if ($('body').hasClass('dir-rtl') ) {
            rtl = true;
        } else {
            rtl = false;
        }
        if ($('body').hasClass('dir-rtl')) {
            $('.slidesContainer').css('float', 'left');
        }
        var prow = $(".course-slider").attr("data-crow");
        prow = parseInt(prow);
        if (prow < 2) {
            $("#available-courses .pagenav").hide();
        }
    };

    klassFront.prototype.frontPageSliders = function() {
        $(".course-slider").slick({
            arrows:true ,
            swipe:true,
            /*prevArrow:'#available-courses .pagenav .slick-prev',
            nextArrow: '#available-courses .pagenav .slick-next',*/
            rtl:rtl,
            slidesToShow: 5,
            slidesToScroll: 5,
            responsive: [
                {
                    breakpoint: 991 ,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    }
                }
            ],
        });
    };

    return {
        init: function() {
            new klassFront();
        }
    };
});