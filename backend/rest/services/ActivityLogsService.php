<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/ActivityLogsDAO.php';

class ActivityLogsService extends BaseService {
    public function __construct() {
        parent::__construct(new ActivityLogsDAO());
    }

    // Validate activity log data
    private function validateLogData($data) {
        if (empty($data['activity_type'])) {
            throw new Exception("Activity type is required.");
        }
        if (empty($data['description'])) {
            throw new Exception("Activity description is required.");
        }
        return true;
    }
    
}
?>