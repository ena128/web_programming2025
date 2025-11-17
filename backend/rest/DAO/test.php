<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/BaseDAO.php';
require_once __DIR__ . '/UserDAO.php';
require_once __DIR__ . '/TaskDAO.php';
require_once __DIR__ . '/CategoryDAO.php';
require_once __DIR__ . '/PriorityDAO.php';
require_once __DIR__ . '/ActivityLogsDAO.php';


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

// Log user creation
$activityLogsDAO->createLog($newUserId, 'Registered a new account');


$newCategoryName = 'Work Tasks';
$newCategoryId = $categoryDAO->createCategory($newCategoryName);


$activityLogsDAO->createLog($newUserId, "Created a new category: $newCategoryName");


$newPriority = [
    'name' => 'High',
    'color' => '#FF0000'
];
$newPriorityId = $priorityDAO->createPriority($newPriority['name'], $newPriority['color']);


$activityLogsDAO->createLog($newUserId, "Added a new priority: {$newPriority['name']}");


$newTask = [
    'user_id' => $newUserId,
    'title' => 'Complete project report',
    'due_date' => '2025-04-10 15:00:00',
    'status' => 'toDo',
    'priority_id' => $newPriorityId,
    'category_id' => $newCategoryId
];
$taskDAO->createTask(
    $newTask['user_id'],
    $newTask['title'],
    $newTask['due_date'],
    $newTask['status'],
    $newTask['priority_id'],
    $newTask['category_id']
);

// Log task creation
$activityLogsDAO->createLog($newUserId, "Created a new task: {$newTask['title']}");


$users = $userDAO->getAll();
$tasks = $taskDAO->getByUserId($newUserId);
$categories = $categoryDAO->getAllCategories();
$priorities = $priorityDAO->getAllPriorities();
$logs = $activityLogsDAO->getAll();

echo "Users:\n";
print_r($users);

echo "\nTasks:\n";
print_r($tasks);

echo "\nCategories:\n";
print_r($categories);

echo "\nPriorities:\n";
print_r($priorities);

echo "\nActivity Logs:\n";
print_r($logs);
?>
