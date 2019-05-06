#!/bin/sh

set -su

LOG_STREAM=/tmp/stdout

if ! [ -p $LOG_STREAM ]; then
  if [ -f $LOG_STREAM ]; then rm $LOG_STREAM; fi
  mkfifo $LOG_STREAM
  chmod 666 $LOG_STREAM
fi

while :
do
  LAST_DAY=$(date +'%d' -d "1 days ago `date +%Y%m01 -d '+1 month'`")
  if [ "$(date +%d)" -eq "16" -o "$(date +%d)" -eq "$LAST_DAY" ]; then
    if [ "$(date +%H%M)" -eq "2300" ]; then
      sh iijmio.sh
    fi
  fi
  sleep 60
done
