
document.addEventListener("DOMContentLoaded", function () {
    let buttons = document.querySelectorAll('.showButton');
    let lists = document.querySelectorAll('[class^="category"]');

    lists.forEach(function (list) {
        list.style.display = 'none';
    });

    buttons.forEach(function (button, index) {
        let list_quiz = document.querySelector('.category' + (index + 1));
        console.log(list_quiz);
        button.addEventListener('click', function () {
            console.log(button);
            // Cacher toutes les listes sauf celle cliquée
            lists.forEach(function (list) {
                if (list !== list_quiz) {
                    list.style.display = 'none';
                }
            });

            // Basculer l'affichage de la liste cliquée
            list_quiz.style.display = (list_quiz.style.display === 'none') ? 'block' : 'none';
            console.log(list_quiz);
        });
    });
});