<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/TaskDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';
require_once __DIR__ . '/../DAO/CategoryDAO.php';

class TaskService extends BaseService {
    private $taskDAO;
    private $categoryDao; 

    
    public function __construct() {
        $this->taskDAO = new TaskDao();
        $this->categoryDao = new CategoryDao(); 
        
        
        parent::__construct($this->taskDAO);
    }



public function add($data) {
    // 1. Provjeri da li je poslato ime kategorije
    if (isset($data['category_name']) && !empty($data['category_name'])) {
        
        // 2. Pokušaj naći kategoriju u bazi
        $existingCategory = $this->categoryDao->getCategoryByName($data['category_name']);

        if ($existingCategory) {
            // Ako postoji, uzmi njen ID
            $data['category_id'] = $existingCategory['id'];
        } else {
            // Ako NE postoji, kreiraj novu
            $newCategory = $this->categoryDao->add(['name' => $data['category_name']]);
            $data['category_id'] = $newCategory['id'];
        }
    }
    
    // Obriši category_name iz niza jer u tabeli tasks nemamo tu kolonu (imamo samo category_id)
    unset($data['category_name']);

    // 3. Pozovi originalnu add funkciju da spasi task
    return parent::add($data);
}
    

    // Validate the task data
    private function validateTaskData($data) {
        if (empty($data['title'])) {
            throw new Exception("Task title is required.");
        }

        // Validate due date format if provided
        if (isset($data['due_date']) && !strtotime($data['due_date'])) {
            throw new Exception("Due date is invalid.");
        }

        // Validate user_id
        if (!isset($data['user_id']) || !is_numeric($data['user_id'])) {
            throw new Exception("Valid user ID is required.");
        }

        return true;
    }
    public function getAll() {
        return $this->taskDAO->getAll();
    }
    public function getByUserId($user_id) {
    // Proslijedi ID korisnika u DAO
    return $this->taskDAO->getByUserId($user_id);
}
}
?>