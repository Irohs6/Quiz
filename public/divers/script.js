window.addEventListener("load", (event) => {

    // Récupération des données du quiz depuis l'attribut 'data-attribut'
    let quizData = JSON.parse(document.getElementById('quiz').getAttribute('data-attribut'));
    console.log(quizData);
    let quizQuestion = document.getElementById('questions'); // récupère lae h2 qui servira a l'affichage de la question
    let selectedAnswers = {}; // déclaration d'un tableau vide pour récupérer plus tard les réponse sélectionné a chaque question
    let quizAnswer = document.getElementById('answer'); // recupère la div qui a l'id answer qui servira de conteneur pour l'affichage des réponses
    let valider = document.getElementById('valider'); // recupère le bouton valider qui permettra de valider les réponse de chaque question et de passer a la suivante 
  
    let submit = document.getElementById('submit');// recupère le input type submit
    submit.style.display = 'none' // cache le bouton submit il apparaitra seullement une fois les 10 question répondu 
    let play = document.getElementById('play'); // récupère le bouton jouer
    let message = document.getElementById('message'); // div id message 
    let askedQuestions = []; //tableau pour afficher les question dans l'ordre de leurs sortit
    let min = 0;  // déclaration d'une variable min a 0 qui sera uttilisé pour l'affichage des question au hasard
    let max = quizData.questions.length; // variable max qui est set avec le nombre total de question du quiz
    let currentQuestionIndex = Math.floor(Math.random() * (max - min)) + min; //Créer un index de question aléatoire pour la première question
    let count = 0; // varaible déclarer a 0 utilisé pour compter le nombre de question
    let askedQuestionsOrder = []; // Tableau pour stocker l'ordre des questions posées

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

    // Fonction pour afficher une question
    function displayQuestion() {

        let newIndex = getRandomUniqueIndex(0, quizData.questions.length);// stock le nouvel index de question unique
        currentQuestionIndex = newIndex; // et rempalce celui de currentQuestion pour la question suivante
        askedQuestions.push(newIndex); // Stock les index de question qui ont déja été afficher pour garantir qu'une question ne sera posé qu'une seul fois
        askedQuestionsOrder.push(newIndex); // Ajoute l'index de la question posée, pour pouvoir les afficher dans l'ordre de leurs sortit
        let currentQuestion = quizData.questions[currentQuestionIndex]; // stock la question a afiché 
       
        quizQuestion.textContent = currentQuestion.question; // affiche la question
        valider.disabled = true;//pour désactiver le bouton a l'affichage de chaque nouvelle question

        // Efface les réponses précédentes
        quizAnswer.innerHTML = '';

          // Affichage des réponses possibles pour la question

        currentQuestion.reponses.forEach(function (reponse) {
            let label = document.createElement('label'); //creer un élément label
            let input = document.createElement('input'); // crer un élément input
            input.type = 'radio'; // ajoute un type"radio" sur l'élément input 
            input.name = `answer`; // créer un name 'answer pour tous les bouton radio pour limiter le choix de réponse a 1 seul 
            input.id =  `${reponse.id}`; // ajoute l'id de la question et de la reponse dans l'id du input
            input.className = 'answers' // ajoute une class answer
            label.appendChild(input); // place le input en ellement enfant de l'élément label 
            label.appendChild(document.createTextNode(reponse.intitulle)); //affiche les réponses
            quizAnswer.appendChild(label);// place les réponse dans la div du conteneur de réponses 
            
            // Ajoutez un écouteur d'événements aux bouton radio
            input.addEventListener('change', validateAnswers);
        });
    }

    //function pour activer désactiver le bouton valider en fonction qu'une réponse soit coché ou non
    function validateAnswers() {
        const radios = quizAnswer.querySelectorAll('input[type="radio"]');//
        let atLeastOneChecked = false; // initialise a false pour que le bouton valider ne sois pas cliquable au lancement du quiz

        radios.forEach(function (radio) {
            if (radio.checked) { //si une réponse selectioné
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
        let currentQuestion = quizData.questions[currentQuestionIndex]; //la question affiché
        
        let radios = quizAnswer.querySelectorAll('input[type="radio"]'); // récupère tous les bouton radio

        radios.forEach(function(radio) {
           // récupère le bouton radio qui est chek donc la réponse sélectionné
            if (radio.checked) {
                let answerId = radio.id; // récupère l'id de la réponses sélectioné
                let answerIntitulle = radio.nextSibling.textContent; // Récupérez l'intitulé de la réponse sélectionné
                let answerIsRight = currentQuestion.reponses.find((reponse) => reponse.id == answerId ).isRihgt //cherche la propriété isRight de la réponse sélectioné dans le tableau d'après son id 
                checkedAnswers.push({ 
                    questionId: currentQuestion.id, //id de la question
                    questionIntitulle: currentQuestion.question, // intitulé de la question
                    link: currentQuestion.link,
                    answerId: answerId,  // id de la réponse
                    answerIntitulle: answerIntitulle,  //intitulé de la réponse
                    answerIsright: answerIsRight // réponse juste ou fausse
                });//ajoute dans le tableau l'id de la question son intitule , l'id de la réponse selectioné et son intitulé et si c'est une bonne réponse ou non
            }
        
        
        });
         // Si 10 questions ont été posées
       
            if (count === 9 ) {
                valider.style.display = 'none' //cache le bouton valider après la validation de la dernière question
                document.getElementById('quiz-container').style.display = 'none';//cache le contenu du quiz-container contenant les question et les réponse
            }
       
        // Stocke les réponses sélectionnées dans le tableau selectedAnswers
        selectedAnswers[currentQuestion.id] = selectedAnswers[currentQuestion.id] || [];
        selectedAnswers[currentQuestion.id].push(...checkedAnswers);
        console.log('curent',selectedAnswers);
        // incrément l'index du tableau pour passé a la question suivante
        
        if (count < 9) {
            
            displayQuestion();
        } else {
            // Ajoute le recap dans le html pour le récupérer dans le controlleur le stocké en session a l'afficher sur une autre pages
            displayRecap()
            message.innerHTML = 'Bravo vous avez Terminer ce quiz appuyer sur le bouton pour découvrir votre résultat'; //Affiche un message une fois le quiz terminer
        }
        count++
        // Affichage de la prochaine question tant que count est inférieur a 10
    });

    //function pour afficher le recap d'un quiz une fois fini
    function displayRecap() {
        const answerScoreTotal = document.createElement('span');
        let scoreTotal = 0
        let num = 1
    

        document.getElementById('quiz-container').style.display = 'none';//cache le contenu du quiz-container contenant les question et les réponse
        submit.style.display = 'block';//fait apparaitre le bouton de soumission

        const recapContainer = document.getElementById('recap-container');
        recapContainer.innerHTML = ''; // Efface le contenu précédent
       
        document.getElementById('recap-container').style.display = 'none'; // cache l'élément avec l'id recap-container

        askedQuestionsOrder.forEach((questionIndex, index) => {
            const questionData = selectedAnswers[quizData.questions[questionIndex].id];
        
            
            if (questionData) {
                // const questionElement = document.createElement('div');
                
                // questionElement.innerHTML = `<strong>Question ${index + 1}:</strong>  ${questionData[0].questionIntitulle}`;
    
                // const answersElement = document.createElement('ul');
                // answersElement.innerHTML = '<strong>Réponses sélectionnées:</strong>';
    
                questionData.forEach(answerData => {
                    // const answerListItem = document.createElement('li');

                /**************************************************************************************** */
                //si la question sélection est vrais on lui ajoute la class success(couleur vert) sinon error(couleur rouge)
                    if (answerData.answerIsright) {
                    // answerListItem.className = 'success' //ajoute la class success si la réponse est vrais
                        const score = 10 ;
                        scoreTotal = scoreTotal + score
                    // answerScore.innerHTML = `<strong>Score:</strong> ${ score }`;
                    }
                    // }else{
                    //     answerListItem.className = 'error'// error si la réponse sélectioné est fause
                        
                    //     // answerScore.innerHTML = `<strong>Score:</strong> ${ score }`;
                    // }
                    //     answerListItem.innerHTML = `<strong>Réponse:</strong> ${answerData.answerIntitulle}`;
                    //     answersElement.appendChild(answerListItem);//place le li enfant du ul
                    //     const questionLink = document.createElement('li');
                    //     answersElement.appendChild(questionLink);//place le li enfant du ul
                    //     questionLink.innerHTML = `<strong>Lien doc:</strong>  <a target="blank" href="${questionData[0].link}">Lien documentation officielle</a>`;
                });
                // questionElement.appendChild(answersElement); // place le ul en enfant de la div
                // recapContainer.appendChild(questionElement); //et place la div en enfant de la div recap container
          
                // recapContainer.insertAdjacentElement('afterend',answerScoreTotal);
                // answerScoreTotal.innerHTML = `<strong>Score Total:</strong> ${scoreTotal}`+'%' ;
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
