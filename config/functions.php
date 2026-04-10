<?php
/**
 * Calculate weighted score for leaderboard
 * Formula: (points * 0.60) + (hours * 0.20) + (tasks * 0.20)
 */
function calculateWeightedScore($points, $hours, $tasks) {
    $score = ($points * 0.60) + ($hours * 0.20) + ($tasks * 0.20);
    return round($score, 2);
}

/**
 * Update student's weighted score in database
 */
function updateStudentScore($conn, $id_number) {
    $stmt = mysqli_prepare($conn, "SELECT points, total_hours, tasks_completed FROM students WHERE id_number = ?");
    mysqli_stmt_bind_param($stmt, 's', $id_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);
    
    if ($student) {
        $score = calculateWeightedScore(
            $student['points'],
            $student['total_hours'],
            $student['tasks_completed']
        );
        
        $update = mysqli_prepare($conn, "UPDATE students SET weighted_score = ? WHERE id_number = ?");
        mysqli_stmt_bind_param($update, 'ds', $score, $id_number);
        mysqli_stmt_execute($update);
        return $score;
    }
    return 0;
}
?>