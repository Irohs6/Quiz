window.addEventListener("load", (event) => {

    // Récupération des données du quiz depuis l'attribut 'data-attribut'
    let quizData = JSON.parse(document.getElementById('quiz').getAttribute('data-attribut'));
    console.log(quizData);
    let quizQuestion = document.getElementById('questions');
    let selectedAnswers = {};
    let quizAnswer = document.getElementById('answer');
    let valider = document.getElementById('valider');
    let show_recap = document.getElementById('show-recap')
    show_recap.style.display = 'none';
    document.getElementById('submit').style.display = 'none';
    let play = document.getElementById('play');
    let askedQuestions = []; //tableau pour afficher les question dans l'ordre de leurs sortit
    let min = 0; 
    let max = quizData.questions.length;
    let currentQuestionIndex = Math.floor(Math.random() * (max - min)) + min;
    let count = 0;
    let askedQuestionsOrder = []; // Tableau pour stocker l'ordre des questions posées

   // Gestion du clic sur le bouton "Play"
    play.addEventListener('click', function () {
    document.getElementById('quiz-container-play').style.display = 'none';
    });


    // Fonction pour obtenir un index unique aléatoire
    function getRandomUniqueIndex(min, max) {
        let newIndex = Math.floor(Math.random() * (max - min));
        while (askedQuestions.includes(newIndex)) {
            newIndex = Math.floor(Math.random() * (max - min));
        }
        return newIndex;
    }

    // Fonction pour afficher une question
    function displayQuestion() {
        let newIndex = getRandomUniqueIndex(0, quizData.questions.length);
        currentQuestionIndex = newIndex;
        askedQuestions.push(newIndex);
        askedQuestionsOrder.push(newIndex); // Ajoute l'index de la question posée à l'ordre des questions posées
        let currentQuestion = quizData.questions[currentQuestionIndex];// affiche la question
       
        quizQuestion.textContent = currentQuestion.question;
        valider.disabled = true;//pour désactiver le bouton a l'affichage de chaque nouvelle question

        // Efface les réponses précédentes
        quizAnswer.innerHTML = '';

          // Affichage des réponses possibles pour la question

        currentQuestion.reponses.forEach(function (reponse) {
            let label = document.createElement('label'); //creer un élément label
            let input = document.createElement('input'); // crer un élément input
            input.type = 'radio'; // met un type checkbox sur l'élément input
            input.name = `answer`; // ajoute l'id de la question et de la reponse dans le name du input
            input.id =  `${reponse.id}`; // ajoute l'id de la question et de la reponse dans le name du input
            input.className = 'answers'
            label.appendChild(input); // place le input en ellement enfant de l'élément label 
            label.appendChild(document.createTextNode(reponse.intitulle)); //
            quizAnswer.appendChild(label);
            
            // Ajoutez un écouteur d'événements pour bouton radio
            input.addEventListener('change', validateAnswers);
        });
    }

    //function pour activer désactiver le bouton valider en fonction qu'une réponse soit coché ou non
    function validateAnswers() {
        const checkboxes = quizAnswer.querySelectorAll('input[type="radio"]');
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
        
        let checkboxes = quizAnswer.querySelectorAll('input[type="radio"]');

        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                let answerId = checkbox.id;
                let answerIntitulle = checkbox.nextSibling.textContent; // Récupérez le texte associé à la case à cocher
                let answerIsRight = currentQuestion.reponses.find((reponse) => reponse.id == answerId ).isRihgt //cherche dans le tableau la réponse d'après son id
                checkedAnswers.push({ 
                    questionId: currentQuestion.id, //id de la question
                    questionIntitulle: currentQuestion.question, // intitulé de la question
                    link: currentQuestion.link,
                    answerId: answerId,  // id de la réponse
                    answerIntitulle: answerIntitulle,  //intitulé de la réponse
                    answerIsright: answerIsRight // réponse juste ou fausse
                });//ajoute dans le tableau l'id de la question son intitule , l'id de la réponse selectioné son intitulé et si c'est la bonne réponse
            }
        
        
        });
         // Si 10 questions ont été posées, affiche le récapitulatif
       
            if (count === 9 ) {
                valider.style.display = 'none' //cache le bouton valider après la validation de la dernière question
                show_recap.style.display = 'block' //affiche le bouton recap après la validation de la dernière question
                document.getElementById('quiz-container').style.display = 'none';//cache le contenu du quiz-container contenant les question et les réponse
            }
       
        // Stocke les réponses sélectionnées dans le tableau selectedAnswers
        selectedAnswers[currentQuestion.id] = selectedAnswers[currentQuestion.id] || [];
        selectedAnswers[currentQuestion.id].push(...checkedAnswers);
        console.log('curent',selectedAnswers);
        // incrément l'index du tableau pour passé a la question suivante
        
        currentQuestionIndex = Math.floor(Math.random() * (max - min)) + min;
        console.log('currentQuestionIndex=',currentQuestionIndex);
        count++
        // Affichage de la prochaine question si elle existe
        if (currentQuestionIndex < quizData.questions.length && count < 10) {
            displayQuestion();
        } else {
            // Affichage du récapitulatif si le maximum de 10 questions est atteint après affichage d'une question
            displayRecap();
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
        document.getElementById('recap-container').style.display = 'block'; // Met visible l'élément avec l'id recap-container

        askedQuestionsOrder.forEach((questionIndex, index) => {
            const questionData = selectedAnswers[quizData.questions[questionIndex].id];
        
            
            if (questionData) {
                const questionElement = document.createElement('div');
                
                questionElement.innerHTML = `<strong>Question ${index + 1}:</strong>  ${questionData[0].questionIntitulle}`;
    
                const answersElement = document.createElement('ul');
                answersElement.innerHTML = '<strong>Réponses sélectionnées:</strong>';
    
                questionData.forEach(answerData => {
                    const answerListItem = document.createElement('li');

                /**************************************************************************************** */
                //si la question sélection est vrais on lui ajoute la class success(couleur vert) sinon error(couleur rouge)
                if (answerData.answerIsright) {
                answerListItem.className = 'success' //ajoute la class success si la réponse est vrais
                const score = 10 ;
                scoreTotal = scoreTotal + score
                // answerScore.innerHTML = `<strong>Score:</strong> ${ score }`;
                }else{
                    answerListItem.className = 'error'// error si la réponse sélectioné est fause
                    
                    // answerScore.innerHTML = `<strong>Score:</strong> ${ score }`;
                }
                    answerListItem.innerHTML = `<strong>Réponse:</strong> ${answerData.answerIntitulle}`;
                    answersElement.appendChild(answerListItem);//place le li enfant du ul
                    const questionLink = document.createElement('li');
                    answersElement.appendChild(questionLink);//place le li enfant du ul
                    questionLink.innerHTML = `<strong>Lien doc:</strong>  <a target="blank" href="${questionData[0].link}">Lien documentation officielle</a>`;
                });
                questionElement.appendChild(answersElement); // place le ul en enfant de la div
                recapContainer.appendChild(questionElement); //et place la div en enfant de la div recap container
          
                recapContainer.insertAdjacentElement('afterend',answerScoreTotal);
                answerScoreTotal.innerHTML = `<strong>Score Total:</strong> ${scoreTotal}`+'%' ;
                // Création de l'objet pour le score total
                tableScore = []
                tableScore.push({
                        'score': scoreTotal //score ttoal
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
                num++// rajoute 1 a chaque tour de la boucle
            }
            
        }) 
    
    }

    show_recap.addEventListener('click', function(){
        displayRecap();  // Affichez le récapitulatif
        show_recap.style.display = 'none'

    });

    // Commencez par afficher la première question au chargement de la page
    displayQuestion();
    validateAnswers();

      
});
