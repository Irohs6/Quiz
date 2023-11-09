$(document).ready(function() {
    let questionsList = $('#questions-list');
    let questionTemplate = $('#question-template').html();

    $('#add-question').click(function() {
        questionsList.append(questionTemplate);
    });

    questionsList.on('click', '.remove-question', function() {
        $(this).closest('.question-container').remove();
    });
});