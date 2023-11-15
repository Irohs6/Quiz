window.addEventListener("load", (event) => {

    // Récupérez les données du quiz depuis le data  attribut
    let quizData =   JSON.parse(document.getElementById('quiz').getAttribute('data-attribut'));
    console.log(quizData);
    let quizQuestion = document.getElementById('questions')
    // Réinitialise la variable selectedAnswers à un objet vide
    selectedAnswers = {}
    tableScore = {}
    let quizAnswer = document.getElementById('answer')

    let valider = document.getElementById('valider')

    let nextQuestion = document.getElementById('next_question')

    let quizContainer = document.getElementById('quiz-container')
    document.getElementById('show-recap').style.display = 'none'

    document.getElementById('submit').style.display = 'none'
    let play = document.getElementById('play')
    console.log(play);
    // nextQuestion.style.display = 'none'
    let recapDataArray = {}; // Déclaration de la variable pour stocker le récapitulatif
    let currentQuestionIndex = 0 // initialise a 0 utiliser pour parcourir les index du tableau de question
    let count = 0 

    play.addEventListener('click', function() {
        document.getElementById('quiz-container-play').style.display = 'none'
    })

    // Initialisez les réponses actuelles
    function displayQuestion() {
        let currentQuestion = quizData.questions[currentQuestionIndex];// affiche la question
        quizQuestion.textContent = currentQuestion.question;
        valider.disabled = true;//pour désactiver le bouton a l'affichage de chaque nouvelle question

        // Efface les réponses précédentes
        quizAnswer.innerHTML = '';

        // Affiche les réponses

        currentQuestion.reponses.forEach(function (reponse, index) {
            let label = document.createElement('label'); //creer un élément label
            let input = document.createElement('input'); // crer un élément input
            input.type = 'checkbox'; // met un type checkbox sur l'élément input
        
            input.name = `${reponse.id}`; // ajoute l'id de la question et de la reponse dans le name du input
            input.className = 'answers'
            label.appendChild(input); // place le input en ellement enfant de l'élément label 
            label.appendChild(document.createTextNode(reponse.intitulle)); //
            quizAnswer.appendChild(label);
            
            // Ajoutez un écouteur d'événements pour chaque case à cocher
            input.addEventListener('change', validateAnswers);
        });
    }

    //function pour activer désactiver le bouton valider en fonction qu'une réponse soit coché ou non
    function validateAnswers() {
        const checkboxes = quizAnswer.querySelectorAll('input[type="checkbox"]');
        let atLeastOneChecked = false; // initialise a false pour que le bouton valider ne sois pas cliquable au lancement du quiz

        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) { //si une réponse selectioné
                atLeastOneChecked = true; // le met a true
            }
        });

        if (atLeastOneChecked) { // si atLeastOneChecked = true
            valider.disabled = false; // le bouton valider est cliquable
        } else {
            valider.disabled = true; // sinon il est désactiver jusqu'a qu'on coche une réponse  
        }
    }

    //ecouteur d'événement sur le bouton valider
    valider.addEventListener('click', function() {
        validateAnswers();
        let checkedAnswers = [];//instancie un tableau pour stocker les réponses séléctioné
        // document.getElementById('next_question').style.display = 'block'; // affiche le bouton question suivante
        let currentQuestion = quizData.questions[currentQuestionIndex];
        
        let checkboxes = quizAnswer.querySelectorAll('input[type="checkbox"]');
        // valider.style.display = 'none'
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                
                let answerId = checkbox.name;
                let answerIntitulle = checkbox.nextSibling.textContent; // Récupérez le texte associé à la case à cocher
                let answerIsRight = currentQuestion.reponses.find((reponse) => reponse.id == answerId ).isRihgt //cherche dans le tableau la réponse d'après son id
                checkedAnswers.push({ 
                    questionId: currentQuestion.id, //id de la question
                    questionIntitulle: currentQuestion.question, // intitulé de la question
                    answerId: answerId,  // id de la réponse
                    answerIntitulle: answerIntitulle,  //intitulé de la réponse
                    answerIsright: answerIsRight // réponse juste ou fausse
                });//ajoute dans le tableau l'id de la question son intitule , l'id de la réponse selectioné son intitulé et si c'est la bonne réponse
            }

        
        });

        if (count === quizData.questions.length-1 ) {
            valider.style.display = 'none' //cache le bouton valider après la validation de la dernière question
            document.getElementById('show-recap').style.display = 'block' //affiche le bouton recap après la validation de la dernière question
            document.getElementById('quiz-container').style.display = 'none';//cache le contenu du quiz-container contenant les question et les réponse
        }
        
        // Stocke les réponses sélectionnées dans le tableau selectedAnswers
        selectedAnswers[currentQuestion.id] = selectedAnswers[currentQuestion.id] || [];
        selectedAnswers[currentQuestion.id].push(...checkedAnswers);
    
        // incrément l'index du tableau pour passé a la question suivante

        currentQuestionIndex++;
        count++
        // Sinon, affichez la question suivante
        if (currentQuestionIndex < quizData.questions.length) {
            displayQuestion();
        }
    });

    //function pour afficher le recap d'un quiz une fois fini
    function displayRecap() {
        const answerScoreTotal = document.createElement('span');
        let scoreTotal = 0
        let num = 1
    

        document.getElementById('quiz-container').style.display = 'none';//cache le contenu du quiz-container contenant les question et les réponse
        document.getElementById('submit').style.display = 'block';//fait apparaitre le bouton de soumission

        const recapContainer = document.getElementById('recap-container');
        recapContainer.innerHTML = ''; // Efface le contenu précédent
        document.getElementById('recap-container').style.display = 'block'; //met visible l'élément qui a l'id recap-container

        for (const questionId in selectedAnswers) {
            const questionData = selectedAnswers[questionId];
        
            
            if (selectedAnswers.hasOwnProperty(questionId)) {//hasOwnProperty renvoit true ou false dans l'exemple il vérifie si selectedAnswers posséde la propriété questionId si oui il renvoie True
            
                

                const questionElement = document.createElement('div');

                questionElement.innerHTML = `<strong>Question ${ num }:</strong>  ${questionData[0].questionIntitulle}`;

                const answersElement = document.createElement('ul');
                answersElement.innerHTML = '<strong>Réponses sélectionnées:</strong>';

                questionData.forEach(answerData => {
                const answerListItem = document.createElement('li');
                const answerScore = document.createElement('li');
                
                
                /**************************************************************************************** */
                //si la question sélection est vrais on lui ajoute la class success(couleur vert) sinon error(couleur rouge)
                if (answerData.answerIsright) {
                answerListItem.className = 'success' //ajoute la class success si la réponse est vrais
                const score = 10 ;
                scoreTotal = scoreTotal + score
                answerScore.innerHTML = `<strong>Score:</strong> ${ score }`;
                }else{
                    answerListItem.className = 'error'// error si la réponse sélectioné est fause
                    const score = 0 ;
                    answerScore.innerHTML = `<strong>Score:</strong> ${ score }`;
                }
                    answerListItem.innerHTML = `<strong>Réponse:</strong> ${answerData.answerIntitulle}`;
                    answersElement.appendChild(answerListItem);//place le li enfant du ul
                    answerListItem.appendChild(answerScore);//place le li enfant du ul
                    
                });
                recapContainer.insertAdjacentElement('afterend',answerScoreTotal);
                answerScoreTotal.innerHTML = `<strong>Score Total:</strong> ${ scoreTotal }`;
                tableScore = []
                tableScore.push({
                        'score': scoreTotal //id de la question
                    }
                );
                selectedAnswersScore = {
                    ...selectedAnswers,...tableScore   
                }
                
                console.log(selectedAnswersScore);
                questionElement.appendChild(answersElement); // place le ul en enfant de la div
                recapContainer.appendChild(questionElement); //et palce la div en enfant de la div recap container
                const recapDataJSON = JSON.stringify(selectedAnswersScore);// JSON.stringify converti une variable Javascript/ un objet ou un tableau en un string JSON prend en premier paramamètre la value, peux accepter un second paramètre une fonction de remplacement et un 3ème paramètre pour l'indentation
                const recapDataField = document.createElement('input');//créer un élément input
                recapDataField.type = 'hidden';// met le input en type hidden(non visible)
                recapDataField.name = 'recapData'; // lui ajoute le name recapData
                recapContainer.appendChild(recapDataField);// place le input en enfant de la div recapContainer
                recapDataField.value = recapDataJSON; // Ajoutez le Json a la value du input pour pouvoir le récupérer dans le controller
                num++// rajoute 1 a chaque tour de la boucle
            }
            
        } 
    
    }

    document.getElementById('show-recap').addEventListener('click', function(){
        displayRecap();  // Affichez le récapitulatif
        document.getElementById('show-recap').style.display = 'none'

    });

    // Commencez par afficher la première question au chargement de la page
    displayQuestion();
    validateAnswers();

});
