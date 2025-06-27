<?php
header('Content-Type: application/json');

// Dados simulados do dashboard
$dashboardData = [
    'totalCollaborators' => 150,
    'birthdays' => 5,
    'pendingAlerts' => 12
];

echo json_encode($dashboardData);
