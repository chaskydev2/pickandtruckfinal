#!/usr/bin/php
<?php

/*
 * Process Laravel Queue - Cron Job Script
 * 
 * This script processes queued jobs (emails) in background.
 * Add to crontab to run every minute:
 * 
 * * * * * * cd /home/u556487000/domains/app.pickntruck.com/public_html && php process_queue.php >> /dev/null 2>&1
 * 
 */

chdir(__DIR__);

// Execute queue worker to process one job
passthru('php artisan queue:work --once --stop-when-empty');
