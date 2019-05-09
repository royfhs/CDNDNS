#!/bin/bash
/usr/bin/php /root/cdndns/confgen.php > /var/www/html/unbound_cdndns.conf
/usr/sbin/unbound-control reload
