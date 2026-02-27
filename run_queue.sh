#!/bin/bash
export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export HOME="/home/u556487000"
cd /home/u556487000/domains/app.pickntruck.com/public_html
/usr/bin/php artisan queue:work --stop-when-empty --max-time=55
