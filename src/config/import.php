<?php
// Sample CSV
// Student ID, Test Name, Score, Student Name, Date Taken
// 123456, Beginning MySQL, 80, Bart Simpson, 11-12-2017

$imports = [
    'get_import_route' => '/import',
    'post_import_route' => '/import',
    'roles_enabled_for' => [
        'admin' 
    ],
    /*
     *  The import model will be used to match your csv column headers to the table column names. 
     *  Note, column headers are trimmed of leading and trailing white space, spaces converted to underscores, stripped of any special character, and lowercased. 
    */
    'csv_headers_cleanup_rules' => ['trim','pound_to_word_number','spaces_to_underscores', 'remove_special_characters','lowercase'],
    /*
     * This mock model QuizScores might point to a table called quiz_scores
     * This quiz_scores table might have the following columns: [id, score, quiz_id, student_id, date_taken]
     * quiz_id and student_id are foreign keys that point to a quizs table and a users table respectively.
     * For data that reference relationals you can define them in the relationships array below.
     */
    'import_model' => App\Models\QuizScores::class,
    /*
     * If you are importing data that has relations you can define them here. 
     * This mock config file is for importing a csv of quiz scores, each score belongs to a student. 
     */
    'relationships' => [
        [
            'create_if_not_found' => true,
            'csv_column' => 'student_id',
            'reference_table' => 'users',
            'reference_field' => 'student_identification_number',
            'reference_primary_key' => 'id',
            'foreign_key' => 'student_id',
            'relationship' => 'belongsTo',
            'model' => App\Models\User::class,
            'validator' => App\Http\Requests\UserRequest::class,
            'extra_columns' => [
                [
                    'column' => 'student_name',
                    'maps_to' => ['first_name','last_name'],
                    'explode_on' => ' '
                ],
                [
                    'column' => 'email',
                    'maps_to' => 'email',
                ]
            ],
            'append_data' => [
                'password' => Hash::make('ChangeIt!')
            ]
        ],
        [
            'csv_column' => 'test_name',
            'reference_table' => 'tests',
            'reference_field' => 'name', // This assumes that the name field on the tests table has a rule that forces unique
            'reference_primary_key' => 'id', 
            'foreign_key' => 'test_id',
            'model' => App\Models\Tests::class,
            'validator' => App\Http\Requests\TestRequest::class,
        ] 
    ]

];

return $imports;
