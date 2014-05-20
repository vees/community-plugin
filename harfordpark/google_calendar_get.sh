START=`date --utc "+%Y-%m-%dT%H:%M:%S"`
END=`date -d "1 year" --utc "+%Y-%m-%dT%H:%M:%S"`
echo $START $END
URL="https://www.google.com/calendar/feeds/dgif6f88nm2oas7mp4s6mugvlc%40group.calendar.google.com/public/full?alt=jsonc&start-min="$START"&start-max="$END"&max-results=1000"
echo $URL
if /usr/bin/curl -s 'https://www.google.com/calendar/feeds/dgif6f88nm2oas7mp4s6mugvlc%40group.calendar.google.com/public/full?alt=jsonc&start-min=2014-04-01T00:00:00&start-max=2015-12-31T00:00:00&max-results=1000' > /home/hpca/cache/46c68ed4180acf6a33ecbb829bd7cddd.html.tmp ; then cp /home/hpca/cache/46c68ed4180acf6a33ecbb829bd7cddd.html.tmp /home/hpca/cache/46c68ed4180acf6a33ecbb829bd7cddd.html; fi 
exit 0
