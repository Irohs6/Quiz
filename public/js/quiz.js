// Scripts jQuery / JavaScript généraux
$(document).ready(function() { // Attend que le document (base.html.twig) soit complètement chargé
    let button = $('#quiz_Valider')
    let nbQuestion = 1
    button.attr('disabled', true)// désactive le bouton valider
    // Fonction déclenchée lors du clic sur le bouton d'ajout d'un nouveau bloc "question" au sein d'un quiz
    $('.add-another-collection-widget').click(function (e) {
        let list = $($(this).attr('data-list-selector'));
        // Récupération du nombre actuel d'élément de l'entity concerné dans la collection (à défaut, utilisation de la longueur de la collection)
        let counter = list.data('widget-counter') || list.children().length;
         // Récupération de l'identifiant du quiz concernée, en cours de création/modification
        let quiz = list.data('quiz');
        // Extraction du prototype complet du champ (que l'on va adapter ci-dessous)
        let newWidget = list.attr('data-prototype');
        // Remplacement des séquences génériques "__name__" utilisées dans les parties "id" et "name" du prototype
        // par un numéro unique au sein de la collection de "question" ou de "answer"  : ce numéro sera la valeur du compteur
        // courant (équivalent à l'index du prochain champ, en cours d'ajout).
        // Au final, l'attribut ressemblera à "quiz[questions][n°]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Ajout également des attributs personnalisés "class" et "value", qui n'apparaissent pas dans le prototype original 
        newWidget = newWidget.replace(/><input type="hidden"/, ' class="borders"><input type="hidden" value="'+quiz+'"');
        
        let listQuestion = $('#questions-list')// récupère l'élément avec l'id question-list pour la création d'un sommaire de question
        let questionID = 'quiz_questions_' + counter; // créer l'id pour le lien a
        
        let li = $('<li></li>');// créer un élément li
        let a = $('<a></a>') // créer un élément a
        a.text('Question n°' + nbQuestion ); // ajoute le text dans l'élément a
        a.attr('href', '#' + questionID); // ajoute le lien vers la question 
        li.appendTo(listQuestion); //place le li en enfant de la div listQuestion
        a.appendTo(li); // place le a en enfant du li
       
        // Incrément du compteur d'éléments et mise à jour de l'attribut correspondant
        counter++;
        nbQuestion++;
        list.data('widget-counter', counter);
        // Création d'un nouvel élément (avec son bouton de suppression), et ajout à la fin de la liste des éléments existants
        let newElem = $(list.attr('data-widget-tags')).html(newWidget);
        addDeleteLink($(newElem).find('div.borders'));
        newElem.appendTo(list); 
       
        
        if (counter > 9 ) {
            button.attr('disabled', false)// réactive le bouton quand 10 question au minimum ont été ajouter
        }
    });

    // anonymize-collection-widget.js : fonction permettant de supprimer un bloc "question" existant au sein d'une session
    $('.remove-collection-widget').find('div.borders').each(function() {
        addDeleteLink($(this))
    })

    // Fonction pour ajouter un bouton de suppression pour un élément de réponse
    function addDeleteLink($moduleForm) {
        let $removeFormButton = $('<div class="block"><button type="button" class="button">Supprimer cette question</button></div>');
        $moduleForm.append($removeFormButton);

        // Événement de suppression lié au bouton
        $removeFormButton.on('click', function(e) {
            $moduleForm.remove(); // Supprime l'élément de réponse
        });
    }

  
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
                    //on remplace avec la nouvelle valeurs
                    let newForm = prototype.replace(regex2, prefix2) 
                    // préfix créer pour remplacer le name de réponse a chaque ajout de réponse
                    let prefix1 = 'quiz[questions][' + questionIndex + '][answers][' + count + ']';
                    // Remplacement des name dans le prototype pour créer une nouvelle réponse
                    newForm = newForm.replace(/quiz\[questions\]\[\d+\]\[answers\]\[\d+\]/g, prefix1);
                    // Ajout de la nouvelle réponse au conteneur
                    $answersContainer.append(newForm);
                    // Sélection des boutons radio pour une question spécifique
                    // let $radioAnswers = $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"]');

                    // // Ajout de la classe 'hidden' aux mauvaises réponses lors de la création
                    // $radioAnswers.filter('[value="0"]').prop('hidden', true);

            //ajouter au clic sur le bouton le nombre de réponse shouaiter pour les créer tous d'un coup*********************************************************************

                    // $radioAnswer = $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"]')
                    // console.log('radio', $radioAnswer);
                    //name="quiz[questions][0][answers][3][isRight]" 
                    $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"]').on('change', function() {
                        // Vérifie si la réponse sélectionnée est une bonne réponse
                        if ($(this).val() === '1') {
                            // Si c'est une bonne réponse, décoche toutes les autres réponses de la même question
                            $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"][value="0"]').prop('checked', true);
                            $(this).prop('checked', true);
                        }
                    });

                
                    // Incrémentation du compteur pour maintenir les identifiants uniques
                    count++;
                } else {
                    let errorMessage = $('<p>', {
                        'text': 'Limite de 4 réponses atteinte pour cette question.',
                        'class': 'error-message'
                    });
        
                    // Ajout du message d'erreur 
                    $element.append(errorMessage);
                }
            } else {
                console.error("La valeur de prototype est undefined");
            }
        });
    }



    function initMutationObserver() {
        const questionsFieldsList = document.getElementById('questions-fields-list');

        if (questionsFieldsList) {
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

            let observerConfig = { childList: true, subtree: true };
            observer.observe(questionsFieldsList, observerConfig);

            // Initialisation - Ajouter le bouton "Ajouter une réponse" pour les éléments déjà présents dans la div
            $('#questions-fields-list > div').each(function() {
                addAnswerButton($(this)); // Ajout du bouton "Ajouter une réponse" pour les éléments déjà présents
            });
        }
    }

    // Appel de la fonction d'initialisation de MutationObserver au chargement du DOM
    initMutationObserver();

});
