/* global twentyseventeenScreenReaderText */
(function( $ ) {

	$(window).scroll(function () {
		var scroll = $(window).scrollTop();
		if (scroll >= 50) {
			$(".fusion-header-wrapper").addClass("sticky");
		} else {
			$(".fusion-header-wrapper").removeClass("sticky");
		}
	});

	

	$(document).ready(function(){	
    
    



		$('#allDomes .fusion-row').slick
      ({
          infinite: true,               
          slidesToShow: 5,			
          slidesToScroll: 1,    
          arrows: true,
          //autoplay: true,
          centerMode: true,
          responsive: [
              {
                breakpoint: 1439,
                settings: {
                  slidesToShow: 3,
                  slidesToScroll: 1,                        
                }
              },
              {
                breakpoint: 1023,
                settings: {
                  slidesToShow: 2,
                  slidesToScroll: 1
                }
              },
              {
                breakpoint:639,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
              }
              // You can unslick at a given breakpoint now by adding:
              // settings: "unslick"
              // instead of a settings object
            ]
    });

    $('.navSlider .fusion-column-wrapper').slick({
      //infinite: true,
      slidesToShow: 4,
      slidesToScroll: 1,
      vertical: true,
      asNavFor: '.mainSlider .fusion-column-wrapper',
      verticalSwiping: true,
      autoplay: true,
      dots: false,      
      focusOnSelect: true,
      arrows: false,
      responsive: [
        {
          breakpoint: 1279,
          settings: {            
            verticalSwiping: false,
            vertical: false                        
          }
        },
        {
          breakpoint: 735,
          settings: {            
            verticalSwiping: false,
            vertical: false                        
          }
        }
      ]
    });
    $('.mainSlider .fusion-column-wrapper').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: true,
      arrows: false,      
      asNavFor: '.navSlider .fusion-column-wrapper',
      responsive: [
        {
          breakpoint: 1279,
          settings: {            
            verticalSwiping: false,
            vertical: false,
            dots:true                        
          }        }
      ]
    });
  });
	

  
	

})( jQuery ); 


  		