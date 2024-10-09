<?php
require_once('functions.php');

if (isset($_POST['search_value'])) {
    $search_value = $_POST['search_value'];
    if ($search_value) {
        $search_value = "%{$search_value}%";
        $stmt = $conn->prepare("SELECT * FROM students WHERE name LIKE ? LIMIT 10");
        if ($stmt) {
            $stmt->bind_param('s', $search_value);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $student_name = $row['name'];
                    $student_id = $row['id'];
                    echo '<a href="student_profile.php?student=' . $student_id . '" class="dropdown-item search-result-item">' . htmlspecialchars($student_name, ENT_QUOTES, 'UTF-8') . '</a>';
                }
            }
        }
    }
}
