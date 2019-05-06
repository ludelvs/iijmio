#!/bin//bash

set -eu

cd /var/www/html/vendor

FILE=/root/.miopon-cli
EXPIRE=$(grep expires_at $FILE|cut -d: -f 2)

if [ "$(date +%s)" -gt "$EXPIRE" ]; then
  while :
  do
    echo "get access token"
    URL=$(node run.js)
    echo $URL
    ACCESS_TOKEN=$(echo $URL|grep -oP "access_token=.*?&"|sed 's/&$//'|cut -d= -f 2)
    if [ -n "$ACCESS_TOKEN" ]; then
       echo "get access token succes"
       EXPIRES_AT=$(expr $(date +%s) + 77600)

       echo "access_token:$ACCESS_TOKEN" > $FILE
       echo "expires_at:$EXPIRES_AT" >> $FILE
       echo "dev_id:$CLIENT_ID" >> $FILE
       break
    fi
    echo "get access token failure. please wait 5 minutes..."
    sleep 300
  done
fi

cd /var/www/html

echo "get iijmio log"
php iijmio.php
echo "get iijmio log success"
