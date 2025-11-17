<?php
require_once __DIR__ . '/BaseDAO.php';

class ActivityLogsDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("activitylogs", "log_id"); 
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM activitylogs WHERE user_id = :user_id ORDER BY timestamp DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createLog($user_id, $action) {
        return $this->create([
            "user_id" => $user_id,
            "action" => $action,
            "timestamp" => date("Y-m-d H:i:s")
        ]);
    }

    public function updateLog($log_id, $action) {
        return $this->update($log_id, [
            "action" => $action
        ]);
    }

    public function deleteLog($log_id) {
        return $this->delete($log_id);
    }
}
?>
