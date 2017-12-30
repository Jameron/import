<?php
// Sample CSV
// Student ID, Test Name, Score, Date Taken
// 123456, Beginning MySQL, 80, 11-12-2017

$imports = [
    'get_import_route' => '/import',
    'post_import_route' => '/import',
    'roles' => [
        'admin' => [
            'enabled' => true
        ],
        'user' => [
            'enabled' => false
        ],
    ],
    /*
     * The import model will be used to match your csv column headers to the table column names. Note, column headers are trimmed of leading and trailing white space, lowercased, spaces converted to underscores, and stripped of any special character.
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
            'relationship' => 'belongsTo'
            'csv_column' => 'student_id',
            'reference_table' => 'users',
            'reference_field' => 'student_identification_number',
            'reference_primary_key' => 'id',
            'foreign_key' => 'student_id'
        ] 
        [
            'relationship' => 'belongsTo'
            'csv_column' => 'test_name',
            'reference_table' => 'tests',
            'reference_field' => 'name', // This assumes that the name field on the tests table has a rule that forces unique
            'reference_primary_key' => 'id', 
            'foreign_key' => 'test_id'
        ] 
    ]

];

return $imports;
