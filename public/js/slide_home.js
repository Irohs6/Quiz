window.addEventListener("load", (event) => {
    // Sélectionnez tous les boutons 'prev' et 'next'
    const prevButtons = document.querySelectorAll('.prev');
    const nextButtons = document.querySelectorAll('.next');

  
    // Initialisation des index pour chaque ensemble de diapositives
    let slideIndex = [];
   
  
    // Recherche de tous les ensembles de diapositives par classe
    let slidesShows = document.querySelectorAll('[class^="slideshow-container"]');

  
    // Parcours des slideshows et des diapositives pour les rendre invisibles sauf la première
    slidesShows.forEach((showSlide, index) => {
        let divMySlides = showSlide.children;
        slideIndex[index] = 1; // Initialisation de l'index à 1 pour chaque groupe
      console.log('slideIndex[index]',slideIndex);
        for (let i = 0; i < divMySlides.length; i++) {
            if (!divMySlides[i].classList.contains('prev') && !divMySlides[i].classList.contains('next')) {
                if (i == 0) {
                    divMySlides[i].style.display = 'block'; // Afficher la première diapositive
                } else {
                    divMySlides[i].style.display = 'none'; // Masquer les autres diapositives
                }
            }
        }
    });
  
    // Fonction pour afficher une diapositive spécifique
    function showSlide(slideIndex, slides) {
        for (let i = 0; i < slides.length; i++) {
            if (i === slideIndex) {
                slides[i].style.display = 'block';
            } else {
                slides[i].style.display = 'none';
            }
        }
    }

    // Parcours des boutons 'prev' et 'next'
    prevButtons.forEach((prevButton, index) => {
        prevButton.addEventListener('click', function() {
            
            slideIndex[index]--;
            if (slideIndex[index] < 0) {
                slideIndex[index] = slidesShows[index].querySelectorAll('.mySlides').length - 1;
            }
            const slides = slidesShows[index].querySelectorAll('.mySlides');
            showSlide(slideIndex[index], slides);
        });
    });

    nextButtons.forEach((nextButton, index) => {
        nextButton.addEventListener('click', function() {
            slideIndex[index]++;
            const slides = slidesShows[index].querySelectorAll('.mySlides');
            console.log(slides);  
            if (slideIndex[index] >= slides.length) {
                slideIndex[index] = 0;
            }

            showSlide(slideIndex[index], slides);
        });
    });
});

  