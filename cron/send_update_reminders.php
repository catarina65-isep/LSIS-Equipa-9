<?php
/**
 * Cron job script to send update reminders to users
 * Should be set to run daily via server cron
 */

// Set the timezone
date_default_timezone_set('Europe/Lisbon');

// Include required files
require_once __DIR__ . '/../BLL/AlertManager.php';

// Log function for debugging
function logMessage($message) {
    $logFile = __DIR__ . '/update_reminders.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

try {
    // Initialize the alert manager
    $alertManager = new AlertManager();
    
    // Send reminders
    $sentCount = $alertManager->sendReminderEmails();
    
    // Log the result
    logMessage("Sent $sentCount reminder(s) successfully");
    
    echo "Successfully sent $sentCount reminder(s).\n";
    
} catch (Exception $e) {
    $error = "Error sending reminders: " . $e->getMessage();
    logMessage($error);
    die($error . "\n");
}
