// Scripts jQuery / JavaScript généraux
$(document).ready(function() { // Attend que le document (base.html.twig) soit complètement chargé

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
  let count = 0;

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
        
        // Récupération du conteneur des réponses associé à la question
        let $answersContainer = $question.find('[id$="_answers"]');
        
        // Récupération du prototype des réponses depuis les attributs de l'élément
        let prototype = $answersContainer.attr('data-prototype');
        console.log(prototype);
        // Modification du prototype pour enlever le label
        prototype = prototype.replace('<label class="required">0label__</label>', '');
        // Vérification si le prototype existe
        if (typeof prototype !== 'undefined') {
            // Comptage du nombre actuel de réponses dans le conteneur
            let counterAnswer = $answersContainer.children().length;

            // Vérification du nombre maximum de réponses (dans cet exemple, limite à 4 réponses)
            if (counterAnswer < 4) {
                // Récupération de l'index de la question actuelle
                let questionIndex = $question.attr('id').split('_')[2];

                // Création d'un préfixe pour l'identifiant unique de la nouvelle réponse
                let prefix = 'quiz[questions][' + questionIndex + '][answers][' + count + ']';

                // Remplacement des placeholders dans le prototype pour créer une nouvelle réponse
                let newForm = prototype.replace(/quiz\[questions\]\[\d+\]\[answers\]\[\d+\]/g, prefix);

                // Création d'un identifiant unique pour la nouvelle réponse
                let uniqueId = 'quiz_questions_' + questionIndex + '_answers_' + count;

                // Attribution de l'identifiant unique à la nouvelle réponse
                newForm = $(newForm).attr('id', uniqueId);
                // Ajout de la nouvelle réponse au conteneur
                $answersContainer.append(newForm);

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
