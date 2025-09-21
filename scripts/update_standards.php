<?php

// Script to update standards from IFRS and FASB
// Run as cron job: php /path/to/scripts/update_standards.php

require_once '../config/database.php';
require_once '../app/Database.php';
require_once '../app/Models/Standard.php';
require_once '../app/Controllers/AdminController.php';

// Run the update check
AdminController::checkForUpdates();

echo "Standards update check completed.\n";

?>