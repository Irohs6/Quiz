// Scripts jQuery / JavaScript généraux
$(document).ready(function() { // Attend que le document (base.html.twig) soit complètement chargé
    let button = $('#quiz_Valider')
    button.attr('disabled', true)// désactive le bouton valider
    // Fonction déclenchée lors du clic sur le bouton d'ajout d'un nouveau bloc "question" au sein d'un quiz
    $('.add-another-collection-widget').click(function (e) {
        // Récupération des informations nécessaires
        let list = $($(this).attr('data-list-selector'));
        let counter = list.data('widget-counter') || list.children().length;
        let quiz = list.data('quiz');
        let newWidget = list.attr('data-prototype');
        
        // Remplacement des placeholders "__name__" par le compteur pour créer un nouvel élément de question
        newWidget = newWidget.replace(/__name__/g, counter);
        newWidget = newWidget.replace(/><input type="hidden"/, ' class="borders"><input type="hidden" value="'+quiz+'"');
       
        counter++;
        list.data('widget-counter', counter);
        let newElem = $(list.attr('data-widget-tags')).html(newWidget);
        
        // Ajout d'un bouton de suppression pour ce nouveau bloc de question
        addDeleteLink($(newElem).find('div.borders'));
        newElem.appendTo(list); 
       
        if (counter > 9 ) {
            button.attr('disabled', false)// réactive le bouton quand 10 question au minimum ont été ajouter
        }
    });

    // Fonction pour ajouter un bouton de suppression pour un élément de réponse
    function addDeleteLink($moduleForm) {
        let $removeFormButton = $('<div class="block"><button type="button" class="button">Supprimer cette réponse</button></div>');
        $moduleForm.append($removeFormButton);

        // Événement de suppression lié au bouton
        $removeFormButton.on('click', function(e) {
            $moduleForm.remove(); // Supprime l'élément de réponse
        });
    }

    // Fonction pour gérer la suppression d'un quiz après confirmation
    $('.remove-quiz-confirm').on('click', function(e) {
        e.preventDefault();
        let id=$(this).data('id');
        let href=$(this).attr('href');
        showModalConfirm(id, href, "Confirmation de suppression d'un quiz");
    });
  
  // Déclaration de la variable en dehors de la fonction
  let count = 1;

  function addAnswerButton($element) {
    // Création d'un nouveau bouton "Ajouter une réponse"
    let addAnswerButton = $('<button>', {
        'type': 'button',
        'class': 'add-answer-another-collection-widget',
        'text': 'Ajouter une réponse'
    });

    // Ajout du bouton nouvellement créé à l'élément fourni en paramètre
    $element.append(addAnswerButton);

    // Gestionnaire d'événement au clic sur le bouton "Ajouter une réponse"
    addAnswerButton.on('click', function() {
    // Récupération de l'élément question associé au bouton cliqué
        let $question = $(this).prev('div[id^="quiz_questions_"]');
        let questionIndex = $question.attr('id').split('_')[2];
    // Récupération du conteneur des réponses associé à la question
        let $answersContainer = $question.find('[id$="_answers"]');
    // Comptage du nombre actuel de réponses dans le conteneur    
        let counterAnswer = $answersContainer.children().length;
    // Récupération du prototype des réponses depuis les attributs de l'élément
        let prototype = $answersContainer.attr('data-prototype');
       
    // Modification du prototype pour enlever le label
        prototype = prototype.replace('<label class="required">'+questionIndex+'label__</label>', '');
    // Vérification si le prototype existe
        if (typeof prototype !== 'undefined') {
    
            
    // Vérification du nombre maximum de réponses (limite à 4 réponses)
            if (counterAnswer < 4) {

                // Utilisation de RegExp pour construire dynamiquement l'expression régulière avec la variable questionIndex
                let regex2 = new RegExp('quiz_questions_' + questionIndex + '_answers_' + questionIndex + '', 'g');
                //éléméent a remplacer dans le prototype pour avoir les bon id de réponses a chaque question
                let prefix2 = ('quiz_questions_' + questionIndex + '_answers_' + count);
                //en remplace avec la nouvelle valeurs
                let newForm = prototype.replace(regex2, prefix2) 
                // préfix créer pour remplacer l'id réponse a chaque ajout de réponse
                let prefix1 = 'quiz[questions][' + questionIndex + '][answers][' + count + ']';
                // Remplacement des placeholders dans le prototype pour créer une nouvelle réponse
                newForm = newForm.replace(/quiz\[questions\]\[\d+\]\[answers\]\[\d+\]/g, prefix1);
                // Ajout de la nouvelle réponse au conteneur
                $answersContainer.append(newForm);
                
                let radio = $('#quiz_questions_'+questionIndex+'_answers_'+ count+'_isRight_0')
                // Ajoutez cet événement lorsque vous créez vos boutons radio
            
                console.log('raido',radio);
                console.log(radio.length);
                // Ajoutez cet événement lorsque vous créez vos boutons radio
                radio.on('change', function() {
                   if (radio.prop('checked').length >= 1) {
                        radio.prop('checked', false);
                   }
                      
                });
                // Incrémentation du compteur pour maintenir les identifiants uniques
                count++;
            } else {
                console.log("Limite de 4 réponses atteinte pour cette question.");
            }
        } else {
            console.error("La valeur de prototype est undefined");
        }
    });
}



    // Observer les modifications du DOM pour détecter l'ajout d'éléments dans la div avec l'ID questions-fields-list
    let observer = new MutationObserver(function(mutationsList) {
        mutationsList.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.target.id === 'questions-fields-list') {
                $(mutation.addedNodes).each(function() {
                    let $addedElement = $(this);
                    addAnswerButton($addedElement); // Ajout du bouton "Ajouter une réponse" pour les nouveaux éléments ajoutés
                });
            }
        });
    });

    // Options pour observer les modifications du DOM
    let observerConfig = { childList: true, subtree: true };

    // Commencer à observer les modifications sur la div avec l'ID questions-fields-list
    observer.observe(document.getElementById('questions-fields-list'), observerConfig);

    // Initialisation - Ajouter le bouton "Ajouter une réponse" pour les éléments déjà présents dans la div
    $('#questions-fields-list > div').each(function() {
        addAnswerButton($(this)); // Ajout du bouton "Ajouter une réponse" pour les éléments déjà présents
    });

});
