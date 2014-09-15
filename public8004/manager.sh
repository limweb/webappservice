#!/bin/sh
# filename: manager.sh
 # * * * * * sh /home/ebrueggeman/manager.sh
 
PROCESSORS=5;
x=0
 
while [ "$x" -lt "$PROCESSORS" ];
do
        PROCESS_COUNT=`pgrep -f process.php | wc -l`
        if [ $PROCESS_COUNT -ge $PROCESSORS ]; then
                exit 0
        fi
        x=`expr $x + 1`
        php -f /home/ebrueggeman/worker.php &
done
exit 0