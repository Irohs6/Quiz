// Scripts jQuery / JavaScript généraux
$(document).ready(function() { // Une fois que le document (base.html.twig) HTML/CSS a bien été complètement chargé...
    // add-collection-widget.js : fonction permettant d'ajouter un nouveau bloc "réponse" au sein d'une question (pour agrandir la collection)
    $('.add-another-collection-widget').click(function (e) {
        var list = $($(this).attr('data-list-selector'))
        // Récupération du nombre actuel d'élément "réponse" dans la collection (à défaut, utilisation de la longueur de la collection)
        var counter = list.data('widget-counter') || list.children().length
        var currentCounter = counter;
        // Récupération de l'identifiant de la question concernée, en cours de création/modification
        var question = list.data('question')
        // Extraction du prototype complet du champ (que l'on va adapter ci-dessous)
        var newWidget = list.attr('data-prototype')
        // Remplacement des séquences génériques "__name__" utilisées dans les parties "id" et "name" du prototype
        // par un numéro unique au sein de la collection de "answers" : ce numéro sera la valeur du compteur
        // courant (équivalent à l'index du prochain champ, en cours d'ajout).
        // Au final, l'attribut ressemblera à "question[answers][n°]"
        newWidget = newWidget.replace(/__name__/g, counter)
        // Ajout également des attributs personnalisés "class" et "value", qui n'apparaissent pas dans le prototype original 
        newWidget = newWidget.replace(/><input type="hidden"/, ' class="borders"><input type="hidden" value="'+question+'"')
        // Incrément du compteur d'éléments et mise à jour de l'attribut correspondant
        console.log(question);
       
       
       console.log(counter);
        counter++
        list.data('widget-counter', counter)
        // Création d'un nouvel élément (avec son bouton de suppression), et ajout à la fin de la liste des éléments existants
        var newElem = $(list.attr('data-widget-tags')).html(newWidget)
        addDeleteLink($(newElem).find('div.borders'))
        newElem.appendTo(list)
        $radioAnswer = $('[name^="question[answers]['+ currentCounter + ']"][type="radio"]');
        console.log('Boutons radio des réponses :', $radioAnswer);

        $('[name^="question[answers]['+ currentCounter + ']"][type="radio"]').on('change', function() {
            // Vérifie si la réponse sélectionnée est une bonne réponse
            console.log(currentCounter);
            if ($(this).val() === '1') {
                // Si c'est une bonne réponse, décoche toutes les autres réponses de la même question
                $('[name^="question[answers]['+ currentCounter + ']"][type="radio"][value="0"]').prop('checked', true);
                $(this).prop('checked', true);
            }
        });
        
       
    })
    // anonymize-collection-widget.js : fonction permettant de supprimer un bloc "réponse" existant au sein d'une session
    $('.remove-collection-widget').find('div.borders').each(function() {
        addDeleteLink($(this))
    })
    // fonction permettant l'ajout d'un bouton "Supprimer cet réponse" dans un bloc "programme", et d'enregistrer l'évenement "click" associé
    function addDeleteLink($moduleForm) {
        var $removeFormButton = $('<div class="block"><button type="button" class="button">Supprimer cet réponse</button></div>');
        $moduleForm.append($removeFormButton)
    
        $removeFormButton.on('click', function(e) {
            $moduleForm.remove()
        })
    }
    
    
})