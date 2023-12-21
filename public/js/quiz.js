// Scripts jQuery / JavaScript généraux
$(document).ready(function() { // Attend que le document (base.html.twig) soit complètement chargé
    let button = $('#quiz_Valider')

    let existingQuestions = $('.borders'); // pour récupérer toute les question existante pour la création du sommaire

    let nbQuestion = existingQuestions.length + 1 // variable déclarer pour le numéro des question prend la valeur de existingQuestion.length dans le cas ou des question serais déja existante.

    button.attr('disabled', true)// désactive le bouton valider
    let listQuestion = $('#questions-list'); // Récupération de l'élément avec l'id questions-list pour le sommaire de questions
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
       
        // Récupération de la liste des questions existantes dans le sommaire
        
        // Ensuite, ajouter de nouvelles questions (à partir du compteur de questions existantes)
        
        let questionID = 'quiz_questions_' + counter; // Création de l'ID pour le lien a

        let li = $('<li></li>'); // Création d'un élément li
        let a = $('<a></a>'); // Création d'un élément a
        a.text('Question n°' + nbQuestion); // Ajout du texte dans l'élément a
        a.attr('href', '#' + questionID); // Ajout du lien vers la question
        li.appendTo(listQuestion); // Placement du li en tant qu'enfant de la div listQuestion
        a.appendTo(li); // Placement du a en tant qu'enfant du li
        let divID = counter;
        // Incrément du compteur d'éléments et mise à jour de l'attribut correspondant
        counter++;
        nbQuestion++;
        list.data('widget-counter', counter);
        // Création d'un nouvel élément (avec son bouton de suppression), et ajout à la fin de la liste des éléments existants
        let newElem = $(list.attr('data-widget-tags')).html(newWidget);
        
        // Mettre en pause pour assurer que l'élément est créé
        setTimeout(function() {
            $('#quiz_questions_'+divID).addClass('borders');
           
        }, 1000); // Délai d'une seconde 
        
        // addDeleteLink($(newElem).find('div.borders'));
        newElem.appendTo(list); 

    });

    

    function createExistingQuestionsSummary() {
        let questionCounter = 1; // Pour le n° de la question 
        //si des question existe
        if (existingQuestions.length > 0) {
            existingQuestions.each(function(question) {
                let questionID = 'quiz_questions_' + question; //Création de l'ID pour le lien a 
                let li = $('<li></li>'); // Création d'un élément li
                let a = $('<a></a>'); // Création d'un élément a
                a.text('Question n°' + questionCounter); // Ajout du texte dans l'élément a
                a.attr('href', '#' + questionID); // Ajout du lien vers la question
                li.appendTo(listQuestion); // Placement du li en tant qu'enfant de la div listQuestion
                a.appendTo(li); // Placement du a en tant qu'enfant du li
                questionCounter++;// Incrémentation du numéro de la question
            });
        }
    }
   
    // Appel de la fonction pour créer le sommaire des questions existantes
    createExistingQuestionsSummary()

    let answerIndex = 0
    existingQuestions.each(function(question) {
        let questionIndex = question;
        $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"][value="0"]').prop('readonly', true);
       
        let answersContainer = $('#quiz_questions_'+ questionIndex +'_answers')
        
        // let counterAnswer = answersContainer.children().length
        
        //name = "quiz[questions][0][answers][0][isRight]"
        radioAnswer =  $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"]')
        radioAnswer.each(function(answerIndex) {
            let radioAnswer = $(this);
            radioAnswer.on('change', function() {
                // Vérifie si la réponse sélectionnée est une bonne réponse
                if ($(this).val() === '1') {
                    // Si c'est une bonne réponse, coche toutes les autres réponses de la même question sur mauvaise 
                    $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"][value="0"]').prop('checked', true);
                    $(this).prop('checked', true);
                }
            });
        });
        

        answerIndex++
        
    });

    function checkMinimumAnswers() {
        let allQuestionsValid = true; // déclaration de la variable a true par défault
        let totalQuestions = $('.borders').length; // la longueur total des question
    
        $('.borders').each(function() {
            let questionIndex = $(this).attr('id').split('_')[2]; // on extrait l'index des question
            let answersContainer = $('#quiz_questions_' + questionIndex + '_answers'); // on récupère le conteneur des question 
            let counterAnswer = answersContainer.children().length; // on regarde le nombre de réponse dans le conteneur
    
            if (counterAnswer < 2) { // si moi de 2 réponse pour chaque question 
                allQuestionsValid = false; // alors allQuestion est faux
                return false;
            }
        });
    
        // Activer le bouton Valider si  au moins 10 questions ajouté et que toutes les questions ont au moins deux réponses
        if (totalQuestions >= 10 && allQuestionsValid) {
            button.attr('disabled', false); // si les deux sont vrais le bouton est actif
        } else { 
            button.attr('disabled', true);// sinon il est désactiver
        }
    }
    
        // Appel de la fonction pour vérifier au chargement initial de la page
        checkMinimumAnswers();



    // // anonymize-collection-widget.js : fonction permettant de supprimer un bloc "question" existant au sein d'une session
    // $('.remove-collection-widget').find('div.borders').each(function() {
    //     addDeleteLink($(this))
    // })

    // // Fonction pour ajouter un bouton de suppression pour un élément de réponse
    // function addDeleteLink($moduleForm) {
    //     let $removeFormButton = $('<div class="block"><button type="button" class="button">Supprimer cette question</button></div>');
    //     $moduleForm.append($removeFormButton);

    //     // Événement de suppression lié au bouton
    //     $removeFormButton.on('click', function(e) {
    //         $moduleForm.remove(); // Supprime l'élément de réponse
    //     });
    // }

  
  // Déclaration de la variable en dehors de la fonction
    let count = 1;

   
    function addAnswerButton($element) {
    // Création d'un nouveau bouton "Ajouter une réponse"
        let addAnswerButton = $('<button>', {
            'type': 'button',
            'class': 'add-answer-another-collection-widget',
            'text': 'Ajouter une réponse'
        });

        // Ajout du bouton ajouter réponse au container des questions
        $element.append(addAnswerButton);

        //Ajout des écouteur d’évènement sur le bouton ‘Ajouter réponses’
        addAnswerButton.on('click', function() {
            // Récupération de l'élément question associé au bouton cliqué
            let $question = $(this).prev('div[id^="quiz_questions_"]'); // sélectionne tous le container de div#quiz_questions_, qui sont créer a chaque apuis sur le bouton Ajouter réponse. 
            
            let questionIndex = $question.attr('id').split('_')[2]; // récupère l'index de la question pour l'uttiliser dans un replace 
            
           console.log('questionIndex', questionIndex);
            // Récupération du container des réponses associé à la question
            let $answersContainer = $question.find('[id$="_answers"]');
            
            // Comptage du nombre actuel de réponses dans le container   
            let counterAnswer = $answersContainer.children().length;

            // Récupération du prototype des réponses depuis les attributs du container des réponses
            let prototype = $answersContainer.attr('data-prototype');
        
            
            // Vérification si le prototype existe
            if (typeof prototype !== 'undefined') {
                
                // Modification du prototype pour enlever le label
                prototype = prototype.replace('<label class="col-form-label required">'+questionIndex+'label__</label>', '');
                prototype = prototype.replace('<legend class="col-form-label required">'+questionIndex+'label__</legend>', '');
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

                    // $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"][value="0"]').prop('readonly', true);
                    checkMinimumAnswers();
                    // Sélection des boutons radio pour une question spécifique
                    // let $radioAnswers = $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"]');

                    // // Ajout de la classe 'hidden' aux mauvaises réponses lors de la création
                    // $radioAnswers.filter('[value="0"]').prop('hidden', true);

            //ajouter au clic sur le bouton le nombre de réponse shouaiter pour les créer tous d'un coup*********************************************************************

                    $radioAnswer = $('[name^="quiz[questions]['+ questionIndex + '][answers]"][type="radio"]')
                    // name="quiz[questions][0][answers][3][isRight]" 
                    $radioAnswer.on('change', function() {
                        // Vérifie si la réponse sélectionnée est une bonne réponse
                        if ($(this).val() === '1') {
                            // Si c'est une bonne réponse, coche toutes les autres réponses de la même question sur mauvaise 
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
                    count = 0
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

            
            $('#questions-fields-list > div').each(function() {
                addAnswerButton($(this)); // Ajout du bouton "Ajouter une réponse" pour les container de question déja présents
            });
        }
    }

    // Appel de la fonction d'initialisation de MutationObserver au chargement du DOM
    initMutationObserver();


    
  

});
