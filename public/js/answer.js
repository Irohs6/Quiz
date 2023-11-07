/********************************************************************************************************************************************** */
$(document).ready(function() { // Une fois que le document (base.html.twig) HTML/CSS a bien été complètement chargé...
    // add-collection-widget.js : fonction permettant d'ajouter un nouveau bloc "question" au sein d'un quiz (pour agrandir la collection)
    $('.add-another-collection-answer-widget').click(function (e) {
        var listAnswer = $($(this).attr('data-list-answers-selector'))
        // console.log(listAnswer);
        // Récupération du nombre actuel d'élément "question" dans la collection (à défaut, utilisation de la longueur de la collection)
        var counterAnswer = listAnswer.data('widget-answers-counter') || list.children().length
        // Récupération de l'identifiant de la session concernée, en cours de création/modification
        var question = list.data('quizId')
        // Extraction du prototype complet du champ (que l'on va adapter ci-dessous)
        var newWidgetAnswer = list.attr('data-answers')
        console.log(newWidgetAnswer);
      
        // Remplacement des séquences génériques "__name__" utilisées dans les parties "id" et "name" du prototype
        // par un numéro unique au sein de la collection de "questions" : ce numéro sera la valeur du compteur
        // courant (équivalent à l'index du prochain champ, en cours d'ajout).
        // Au final, l'attribut ressemblera à "quiz[questions][n°]"
        
        newWidgetAnswer = newWidgetAnswer.replace(/__name__/g, counterAnswer)
        console.log(newWidgetAnswer);
        
        // Ajout également des attributs personnalisés "class" et "value", qui n'apparaissent pas dans le prototype original 

        newWidgetAnswer = newWidgetAnswer.replace(/><input type="hidden"/, ' class="border"><input type="hidden" value="'+question+'"')
        // Incrément du compteur d'éléments et mise à jour de l'attribut correspondant
        counterAnswer++
        list.data('widget-counter', counterAnswer)
        listAnswer.data('widget-answer-counter', counterAnswer)
        // Création d'un nouvel élément (avec son bouton de suppression), et ajout à la fin de la liste des éléments existants
        var newElemAnswer = $(listAnswer.attr('data-widget-answer')).html(newWidgetAnswer)
        addDeleteLink($(newElemAnswer).find('div.border'))
        newElemAnswer.appendTo(listAnswer)
    })
    // anonymize-collection-widget.js : fonction permettant de supprimer un bloc "question" existant au sein d'une session
    $('.remove-collection-answer-widget').find('div.border').each(function() {
        addDeleteLink($(this))
    })
   
    // fonction permettant l'ajout d'un bouton "Supprimer cet question" dans un bloc "questions", et d'enregistrer l'évenement "click" associé
    function addDeleteLink($answerForm) {
        var $removeAnswerFormButton = $('<div class="block"><button type="button" class="button">Supprimer cet réponse</button></div>');
        $answerForm.append($removeAnswerFormButton)
    
        $removeAnswerFormButton.on('click', function(e) {
            $answerForm.remove()
        })
    }
    
    
    // Fonction permettant l'affichage de la fenêtre modale de confirmation pour chaque situation
    function showModalConfirm($id, $href, $title) {
        console.log("id   = "+$id)
        console.log("href = "+$href)
        $('#modalPopup .modal-title').html($title)
        $('#modalPopup .modal-body').html("<span class='center'><i class='fas fa-spinner fa-spin fa-4x'></i></span>")
        $.get(
            "confirm", // La route doit toujours être accessible au moyen du chemin "confirm" dans le contrôleur associé à l'entité concernée 
            {
                'id' : $id
            },
            function(resView) {
                $('#modalPopup .modal-body').html(resView)
            }
        )
        $('#modalConfirm').on('click', function(e){
            window.location.href = $href
        })
        $('#modalPopup').modal('show')
    }
    
})