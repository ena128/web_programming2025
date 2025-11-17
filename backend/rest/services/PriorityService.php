<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/PriorityDAO.php';

class PriorityService extends BaseService {
    public function __construct() {
        parent::__construct(new PriorityDAO());
    }

    public function validatePriorityData($data) {
        if (empty($data['name'])) {
            throw new Exception("Priority name is required.");
        }
        
        if (empty($data['color'])) {
            $data['color'] = '#FF0000'; 
        }

        return $data;
    }
}
?>
