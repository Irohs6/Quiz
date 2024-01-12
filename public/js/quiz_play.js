window.addEventListener("load", (event) => {

    // Récupération des données du quiz depuis l'attribut 'data-attribut'
    let quizData = JSON.parse(document.getElementById('quiz').getAttribute('data-attribut'));
    // La méthode JSON.parse() est utilisée pour analyser la chaîne JSON contenue
    // dans l'attribut 'data-attribut' de l'élément avec l'ID 'quiz'.
    // Cela convertit la chaîne JSON en un objet JavaScript pour une utilisation ultérieure dans le script.
    console.log(quizData);
    let quizQuestionSentence = document.getElementById('questions-sentence'); // récupère lae h2 qui servira a l'affichage de la question
    let selectedAnswers = {}; // déclaration d'un tableau vide pour récupérer plus tard les réponse sélectionné a chaque question
    let quizAnswerSentence = document.getElementById('answer-sentence'); // recupère la div qui a l'id answer qui servira de conteneur pour l'affichage des réponses
    let valider = document.getElementById('valider'); // recupère le bouton valider qui permettra de valider les réponse de chaque question et de passer a la suivante 
    let submit = document.getElementById('submit');// recupère le input type submit qui soumettra le formulmaire a la fin du quiz
    submit.style.display = 'none' // cache le bouton submit il apparaitra seullement une fois les 10 question répondu 
    let play = document.getElementById('play'); // récupère le bouton jouer
    let message = document.getElementById('message'); // div id message 
    let numberQuestion = document.getElementById('number_question');// recupere l'élément ave l'id number_question qui servira a l'affichage du numéro de la question
    let sectionInfoQuestion = document.getElementById('info_question')
    let askedQuestions = []; //tableau vide qui stockera les index des question déja sortit
    let min = 0;  // déclaration d'une variable min a 0 qui sera uttilisé pour l'affichage des question au hasard
    let max = quizData.questions.length; // variable max qui est set avec le nombre total de question du quiz
    let currentQuestionIndex = Math.floor(Math.random() * (max - min)) + min; //Créer un index de question aléatoire pour la première question
    let count = 1; // variable déclarer a 1 utilisé pour compter le nombre de question
    numberQuestion.innerHTML = count 
   // Gestion du clic sur le bouton "Play"
    play.addEventListener('click', function () {

        document.getElementById('quiz-container-play').style.display = 'none';// cache le container afficher au début pour afficher les question et réponse après avoir clické sur le bouton jouer

    });


    // Fonction pour obtenir un index unique aléatoire
    function getRandomUniqueIndex(min, max) {

        let newIndex = Math.floor(Math.random() * (max - min));// créer un nouvel index de question 
        // tant que l'index nouvellement créer est déja présent dans le tableau  askedQuestion
        while (askedQuestions.includes(newIndex)) {
            //il va en créer un autre
            newIndex = Math.floor(Math.random() * (max - min));

        }
        //et retournera le nouvelle index 
        return newIndex;
    }

    //function pour activer désactiver le bouton valider en fonction qu'une réponse soit coché ou non
    function validateAnswers() {
        const radios = quizAnswerSentence.querySelectorAll('input[type="radio"]');//
        let atLeastOneChecked = false; // initialise a false pour que le bouton valider ne sois pas cliquable au lancement du quiz

        radios.forEach(function (radio) {
            if (radio.checked) { //si une réponse selectioné
                atLeastOneChecked = true; // le met a true
            }
        });
        
        if (atLeastOneChecked) { // si atLeastOneChecked = true
            valider.disabled = false; // le bouton valider est cliquable
            valider.classList.remove('disabled'); // Supprime la classe "disabled" pour activer visuellement le bouton
            
        } else {
            valider.disabled = true; // sinon il est désactiver jusqu'a qu'on coche une réponse 
            valider.classList.add('disabled'); // Ajoute la classe "disabled" pour désactiver visuellement le bouton

        }
    }
    // Fonction pour afficher une question
    function displayQuestion() {
        valider.disabled = true; // sinon il est désactiver jusqu'a qu'on coche une réponse 
        valider.classList.add('disabled'); // Ajoute la classe "disabled" pour désactiver visuellement le bouton

        let newIndex = getRandomUniqueIndex(min, max);// stock le nouvel index de question unique
        currentQuestionIndex = newIndex; // et remplace celui de currentQuestion pour la question suivante
        askedQuestions.push(newIndex); // Stock les index de question qui ont déja été afficher pour garantir qu'une question ne sera posé qu'une seul fois
        
        let currentQuestion = quizData.questions[currentQuestionIndex]; // stock la question a afiché 
       
        quizQuestionSentence.textContent = currentQuestion.question; // affiche la question
        

        // Efface les réponses précédentes
        quizAnswerSentence.innerHTML = '';

          // Affichage des réponses possibles pour la question

        currentQuestion.reponses.forEach(function (reponse) {
            let labelAnswer = document.createElement('label'); //creer un élément label qui contiendra l'intitule de la reponse
            let inputAnswerRadio= document.createElement('input'); // crer un élément input
            inputAnswerRadio.type = 'radio'; // ajoute un type"radio" sur l'élément input 
            inputAnswerRadio.name = `answer`; // créer un name 'answer pour tous les bouton radio pour limiter le choix de réponse a 1 seul 
            inputAnswerRadio.id =  `${reponse.id}`; // ajoute l'id de la reponse dans l'id du input
            inputAnswerRadio.className = 'answers' // ajoute une class answer
            labelAnswer.className = 'label_answer';
            labelAnswer.appendChild(inputAnswerRadio); // place le input en ellement enfant de l'élément label 
            labelAnswer.appendChild(document.createTextNode(reponse.intitulle)); //affiche les réponses
            quizAnswerSentence.appendChild(labelAnswer);// place les réponse dans la div du conteneur de réponses 
            
            // Ajoutez un écouteur d'événements aux bouton radio si 1 réponse sélectionné apel la fonction validateAnswers()
            inputAnswerRadio.addEventListener('change', validateAnswers);
        });
    }


    //ecouteur d'événement sur le bouton valider
    valider.addEventListener('click', function() {
        validateAnswers();
       
        let checkedAnswers = [];//instancie un tableau pour stocker les réponses séléctioné
        let currentQuestion = quizData.questions[currentQuestionIndex]; //recupère la question affiché
        let radios = quizAnswerSentence.querySelectorAll('input[type="radio"]'); // récupère tous les bouton radio
        radios.forEach(function(radio) {
           // récupère le bouton radio qui est chek donc la réponse sélectionné
            if (radio.checked) {
                let answerId = radio.id; // récupère l'id de la réponses sélectioné
                let answerIntitulle = radio.nextSibling.textContent; // Récupérez l'intitulé de la réponse sélectionné
                let answerIsRight = currentQuestion.reponses.find((reponse) => reponse.id == answerId ).isRight //cherche la propriété isRight de la réponse sélectioné dans le tableau d'après son id 
                checkedAnswers.push({ 
                    questionId: currentQuestion.id, //id de la question
                    questionIntitulle: currentQuestion.question, // intitulé de la question
                    link: currentQuestion.link,
                    answerId: answerId,  // id de la réponse
                    answerIntitulle: answerIntitulle,  //intitulé de la réponse
                    answerIsright: answerIsRight // réponse juste ou fausse
                });//ajoute dans le tableau l'id de la question son intitule , l'id de la réponse selectioné et son intitulé et si c'est une bonne réponse ou non
                console.log('checkedAnswers,', checkedAnswers);
            }
        });
       
        // Stocke les réponses sélectionnées dans le tableau selectedAnswers
        selectedAnswers[currentQuestion.id] = selectedAnswers[currentQuestion.id] || [];
        selectedAnswers[currentQuestion.id].push(...checkedAnswers);
        console.log('curent',selectedAnswers);
        
         // Affichage de la prochaine question tant que count est inférieur a 10
        if (count < 10) {
            
            displayQuestion();         
        } else {
            // Ajoute le recap dans le html pour le récupérer dans le controlleur le stocké en session a l'afficher sur une autre pages
            arrayRecap()
            valider.style.display = 'none' //cache le bouton valider après la validation de la dernière question
            document.getElementById('quiz-container').style.display = 'none';//cache le contenu du quiz-container contenant les question et les réponse
            sectionInfoQuestion.style.display = 'none'
            message.innerHTML = 'Bravo vous avez Terminer ce quiz appuyer sur le bouton pour découvrir votre résultat'; //Affiche un message une fois le quiz terminer
        }
        
        count++
        numberQuestion.innerHTML = count 
    });
   
    //function qui créer un tableau de recap d'un quiz
    function arrayRecap() {

        let scoreTotal = 0
       
        document.getElementById('quiz-container').style.display = 'none';//cache le contenu du quiz-container contenant les question et les réponse
        submit.style.display = 'block';//fait apparaitre le bouton de soumission

        const recapContainer = document.getElementById('recap-container');
        recapContainer.innerHTML = ''; // Efface le contenu précédent
       
        document.getElementById('recap-container').style.display = 'none'; // cache l'élément avec l'id recap-container
        for (let questionId in selectedAnswers) {

            if (selectedAnswers.hasOwnProperty(questionId)) {

                let questionData = selectedAnswers[questionId];

                if (questionData) {
                    questionData.forEach(answerData => {
                    //si la question sélection est vrais on incrémente la variable scoreTotal de 10
                        if (answerData.answerIsright) {  
                            scoreTotal +=  10
                            console.log(scoreTotal);
                        }
                    });               
                    tableScore = []
                    tableScore.push({
                            'score': scoreTotal //score total
                        }
                    );
                    // Création de l'objet combiné avec les réponses et le score total
                    const selectedAnswersScore = {
                        ...selectedAnswers,
                        ...tableScore
                    };
                        
                    const recapDataJSON = JSON.stringify(selectedAnswersScore);// JSON.stringify converti une variable Javascript/ un objet ou un tableau en un string JSON prend en premier paramamètre la value, peux accepter un second paramètre une fonction de remplacement et un 3ème paramètre pour l'indentation
                    const recapDataField = document.createElement('input');//créer un élément input
                    recapDataField.type = 'hidden';// met le input en type hidden(non visible)
                    recapDataField.name = 'recapData'; // lui ajoute le name recapData
                    recapContainer.appendChild(recapDataField);// place le input en enfant de la div recapContainer
                    recapDataField.value = recapDataJSON; // Ajoutez le Json a la value du input pour pouvoir le récupérer dans le controller
                
                }
            }
        } 
    
    }

    // Commencez par afficher la première question au chargement de la page
    displayQuestion();
    validateAnswers();

   
});
