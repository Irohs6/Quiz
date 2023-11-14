
let show_quiz = document.getElementById('show-list-quiz')
let show_score = document.getElementById('show-score')
let show_table = document.getElementById('show_table')
const section_profil = document.getElementById('section_profil')

let list_quiz = document.getElementById('list-quiz')
show_table.style.display = 'none'
list_quiz.style.display = 'none'

show_quiz.addEventListener('click', function(){
    list_quiz.style.display = 'block'
})

list_quiz.addEventListener('click', function(){
    list_quiz.style.display = 'none'
})

show_score.addEventListener('click', function(){
        show_table.style.display = 'block'
})

show_table.addEventListener('click', function(){
        show_table.style.display = 'none' 
})