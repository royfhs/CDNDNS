#!/bin/bash

killall -9 php

mysql -ucdndns -pcdndns -Dcdndns -e "update targets set ts_lastprobe = 0 where ts_probefinish = 0; update domains set ts_lastdig = 0 where ts_digfinish = 0;"

ulimit -n 100000

for i in {1..50}
do
	 php digger.php >/dev/null 2>&1 &
done

for i in {1..100}
do
	 php prober.php >/dev/null 2>&1 &
done

