window.addEventListener("load", (event) => {
  
  


  // const swiper = new Swiper('.swiper', {
  //     effect: "cards",
  //     grabCursor:true,
  //     initialSlide:2,
  //     loop:true,
  //     rotate:true,
      
     
  //   });

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
          const activeSlideIndex = this.activeIndex;
          const currentSlide = this.slides[activeSlideIndex];
  
          const infoPlayElement = currentSlide.querySelector('.info-play');
          const InfoElements = document.querySelectorAll('.info');
          console.log(InfoElements);
          const showInfoElements = document.querySelectorAll('.show_info');
        showInfoElements.forEach(showInfoElement =>{
          if (infoPlayElement && showInfoElement) {
            showInfoElement.innerHTML = ''; // Efface le contenu actuel de show_info
            showInfoElement.appendChild(infoPlayElement.cloneNode(true)); // Déplace info-play sous show_info
          }})
        }
      }
    });
  });

});
// on: {
//   slideChange: function() {
//     // Récupérer l'index de la diapositive active
//     let activeSlideIndex = this.activeIndex;
//     console.log('activeSlideIndex',activeSlideIndex);
//     // Récupérer tous les éléments .info-play et .show_info de la diapositive active
//     let infoPlayElements = element.querySelectorAll('.info-play');
//     let showInfoElements = document.querySelectorAll('.show_info');
//     console.log('showInfoElements',showInfoElements);
//     // Déplacer chaque élément .info-play sous .show_info à l'intérieur de chaque instance de Swiper
//     infoPlayElements.forEach(infoPlay => {
//       console.log(infoPlay);
//       showInfoElements.forEach(showInfo => {
//         console.log('showInfo',showInfo);
//           showInfo.appendChild(infoPlay.cloneNode(true));
//       });
//     }); 
//   }
// }
