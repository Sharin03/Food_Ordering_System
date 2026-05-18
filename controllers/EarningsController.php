<?php
require_once __DIR__ . '/../models/EarningsModel.php';

class EarningsController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function index() {
        $model    = new EarningsModel($this->conn);
        $agent_id = $_SESSION['agent_id'];
        $summary  = $model->getSummary($agent_id);
        $history  = $model->getEarningsHistory($agent_id);
        include __DIR__ . '/../views/earnings.php';
    }
}
