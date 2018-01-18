<?php
// Sample CSV
// Student ID, Test Name, Score, Student Name, Date Taken
// 123456, Beginning MySQL, 80, Bart Simpson, 11-12-2017

$imports = [
    'roles_enabled_for' => [
        'admin' 
    ],
    /*
     *  The import model will be used to match your csv column headers to the table column names. 
     *  Note, column headers are trimmed of leading and trailing white space, spaces converted to underscores, stripped of any special character, and lowercased. 
    */
    /*
     * This mock model QuizScores might point to a table called quiz_scores
     * This quiz_scores table might have the following columns: [id, score, quiz_id, student_id, date_taken]
     * quiz_id and student_id are foreign keys that point to a quizs table and a users table respectively.
     * For data that reference relationals you can define them in the relationships array below.
     */
    /*
     * If you are importing data that has relations you can define them here. 
     * This mock config file is for importing a csv of quiz scores, each score belongs to a student. 
     */

];

return $imports;
