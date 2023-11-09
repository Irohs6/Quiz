<?php
"id" => "quiz_questions___name__"
"name" => "__name__"
"full_name" => "quiz[questions][__name__]"
"disabled" => false
"label" => "__name__label__"
"label_format" => null
"label_html" => false
"multipart" => false
"block_prefixes" => array:4 [▼
  0 => "form"
  1 => "collection_entry"
  2 => "question"
  3 => "_quiz_questions_entry"
]

"id" => "quiz_questions___name___answers"
            "name" => "answers"
            "full_name" => "quiz[questions][__name__][answers]"
            "disabled" => false
            "label" => null
            "label_format" => null
            "label_html" => false
            "multipart" => false
            "block_prefixes" => array:3 [▼
              0 => "form"
              1 => "collection"
              2 => "_quiz_questions_entry_answers"
            ]
            "unique_block_prefix" => "_quiz_questions_entry_answers"

            "id" => "__name__"
            "name" => "__name__"
            "type_class" => "App\Form\QuestionType"
            "synchronized" => true
            "passed_options" => array:3 [▶]
            "resolved_options" => array:49 [▶]
            "default_data" => array:1 [▶]
            "submitted_data" => []
          ]
          "00000000000003660000000000000000" => array:8 [▶]
          "00000000000003f80000000000000000" => array:8 [▼
            "id" => "__name___answers"
            "name" => "answers"
            "type_class" => "Symfony\Component\Form\Extension\Core\Type\CollectionType"
            "synchronized" => true
            "passed_options" => array:5 [▶]
            "resolved_options" => array:58 [▶]
            "default_data" => array:1 [▶]
            "submitted_data" => []
          ]
          "000000000000042f0000000000000000" => array:8 [▼
            "id" => "quiz"
            "name" => "quiz"
            "type_class" => "App\Form\QuizType"
            "synchronized" => true
            "passed_options" => array:1 [▶]
            "resolved_options" => array:50 [▶]
            "default_data" => array:1 [▶]
            "submitted_data" => []


            "id" => "quiz_questions"
            "name" => "questions"
            "type_class" => "Symfony\Component\Form\Extension\Core\Type\CollectionType"
            "synchronized" => true
            "passed_options" => array:5 [▶]
            "resolved_options" => array:58 [▶]
            "default_data" => array:1 [▶]
            "submitted_data" => []
          ]   