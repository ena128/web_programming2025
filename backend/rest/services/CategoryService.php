<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/CategoryDAO.php';

class CategoryService extends BaseService {
    public function __construct() {
        parent::__construct(new CategoryDAO());
    }


    // Validate category data
    private function validateCategoryData($data) {
        if (empty($data['name'])) {
            throw new Exception("Category name is required.");
        }
        return true;
    }
}
?>