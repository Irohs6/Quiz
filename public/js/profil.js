window.addEventListener("load", (event) => {
   
    let show_quiz = document.getElementById('show-list-quiz') // bouton voir liste quiz
    let show_score = document.getElementById('show-score') // bouton voir tableau de score 
    let show_table = document.getElementById('show_table') // la div contenant le tableau
    

    let list_quiz = document.getElementById('list-quiz')// ul contenant la liste des quiz créer

    show_table.style.display = 'none'//met la div contenant le tableau en none(caché) 
    list_quiz.style.display = 'none'//met la div contenant la liste des quiz en none(caché)

    //ajout d'un écouteur d'évènement sur le bouton voir la liste quiz 
    show_quiz.addEventListener('click', function(){
        list_quiz.style.display = 'block'//affiche l' ul qui contient la liste des quiz créer
    })
    //ajout d'un ecouteur d'évènement sur le ul 
    list_quiz.addEventListener('click', function(){
        list_quiz.style.display = 'none'//cache le ul et la liste des quiz créer
    })

    // ajout d'un écouteur d'évenement au click sur le bouton voir tableau de score 
    show_score.addEventListener('click', function(){
        show_table.style.display = 'block'//affiche le tableau qui étais en none
    })

    // ajoute d'un ecouteur d'évenément sur la la div contenant le tableau
    show_table.addEventListener('click', function(){ 
        show_table.style.display = 'none' // cache la div du tableau
    }) 

});