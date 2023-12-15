window.addEventListener("load", (event) => {

  const swiperElements = document.querySelectorAll('.swiper');

  swiperElements.forEach(element => {
    const swiperInstance = new Swiper(element, {
      effect: "cards",
      grabCursor: true,
      initialSlide: 2,
      loop: true,
      rotate: true,
  
      on: {
        slideChange: function () {
            const activeSlide = this.slides[this.activeIndex];
            const infoPlay = activeSlide.querySelector('.info-play');
            const showInfo = activeSlide.closest('.info').querySelector('.show_info');

            if (infoPlay && showInfo) {
              // showInfo.innerHTML = ''; // Effacer le contenu actuel de show_info

              const infoPlayContent = infoPlay.innerHTML;
              showInfo.innerHTML = infoPlayContent; // Déplacer le contenu de info-play sous show_info

          }
        }
      }
    });
  });
});

