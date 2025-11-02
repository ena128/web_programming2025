<?php
require_once __DIR__ . '\BaseDAO.php';
require_once __DIR__ . '\UserDAO.php';
require_once __DIR__ . '\TaskDAO.php';
require_once __DIR__ . '\CategoryDAO.php';
require_once __DIR__ . '\PriorityDAO.php';
require_once __DIR__ . '\ActivityLogsDAO.php'; 


$userDAO = new UserDAO();
$taskDAO = new TaskDAO();
$categoryDAO = new CategoryDAO();
$priorityDAO = new PriorityDAO();
$activityLogsDAO = new ActivityLogsDAO(); 


$newUser = [
    'name' => 'Ena Slipicevic',
    'email' => 'enaSS2251125@example.com',
    'password' => password_hash('securepassword', PASSWORD_BCRYPT),
    'role' => 'user'
];
$newUserId = $userDAO->create($newUser);


$activityLogsDAO->createLog($newUserId, 'Registered a new account');


$newCategory = [
    'name' => 'Work Tasks'
];
$newCategoryId = $categoryDAO->create($newCategory);


$activityLogsDAO->createLog($newUserId, 'Created a new category: Work Tasks');


$newPriority = [
    'name' => 'High',
    'color' => '#FF0000'
];
$newPriorityId = $priorityDAO->create($newPriority);


$activityLogsDAO->createLog($newUserId, 'Added a new priority: High');


$newTask = [
    'user_id' => $newUserId,
    'title' => 'Complete project report',
    'due_date' => '2025-04-10 15:00:00',
    'status' => 'toDo',
    'priority_id' => $newPriorityId,
    'category_id' => $newCategoryId
];
$taskDAO->create($newTask);


$activityLogsDAO->createLog($newUserId, 'Created a new task: Complete project report');

$users = $userDAO->getAll();
echo "Users:\n";
print_r($users);


$tasks = $taskDAO->getAll();
echo "Tasks:\n";
print_r($tasks);


$categories = $categoryDAO->getAll();
echo "Categories:\n";
print_r($categories);


$priorities = $priorityDAO->getAll();
echo "Priorities:\n";
print_r($priorities);


$logs = $activityLogsDAO->getAll();
echo "Activity Logs:\n";
print_r($logs);
?>