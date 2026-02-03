<?php

echo "===== RUNNING MIGRATIONS =====\n\n";

// Run migration command
passthru('php artisan migrate --force 2>&1');

echo "\n\n===== MIGRATION COMPLETE =====\n";
echo "Now run: php artisan sync:tarif --year=2025\n";
