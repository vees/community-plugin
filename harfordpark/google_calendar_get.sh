START=`date --utc "+%Y-%m-%dT%H:%M:%SZ"`
END=`date -d "1 year" --utc "+%Y-%m-%dT%H:%M:%S"`
KEY=`cat /home/hpca/projects/community-plugin/harfordpark/KEY`
URL="https://www.googleapis.com/calendar/v3/calendars/dgif6f88nm2oas7mp4s6mugvlc%40group.calendar.google.com/events?timeMin="$START"&key="$KEY
CACHEFILE="/home/hpca/cache/46c68ed4180acf6a33ecbb829bd7cddd.html"
echo $URL
if /usr/bin/curl -s $URL > $CACHEFILE.tmp ; then cp $CACHEFILE.tmp $CACHEFILE; fi 
exit 0
