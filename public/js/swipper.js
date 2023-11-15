window.addEventListener("load", (event) => {

  const swiper = new Swiper('.swiper', {
      // Optional parameters
      direction: 'horizontal',
      loop: true,
    
      // If we need pagination
      pagination: {
        el: '.swiper-pagination',
      },
    
      // Navigation arrows
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    
      // And if we need scrollbar
      scrollbar: {
        el: '.swiper-scrollbar',
      },
    });

    document.addEventListener('DOMContentLoaded', function () {
      const swiperContainers = document.querySelectorAll('.swiper-container');
      swiperContainers.forEach(function (swiperContainer) {
          new Swiper(swiperContainer, {
            
              loop: true,
            
              navigation: {
                  nextEl: '.swiper-button-next',
                  prevEl: '.swiper-button-prev',
              },
              scrollbar: {
                  el: '.swiper-scrollbar',
              },
          });
      });
  });

});