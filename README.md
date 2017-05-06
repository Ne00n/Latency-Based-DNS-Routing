Howto for Debian/Ubuntu

Beware: This is an experiment!

1. Get latest PowerDNS from: https://repo.powerdns.com/, should be 4.0+ (PowerDNS Authoritative Server - version 4.0.X)

If you want to see things exploding, feel free to use the 3.1 from the debian repos

1.1 You need also these packages: pdns-backend-remote,curl,traceroute

2. Add these lines to your /etc/powerdns/pdns.conf
```
query-cache-ttl=0
cache-ttl=0
log-dns-details=no
launch=remote
remote-connection-string=http:url=http://dns-req.local:80/dns
```
This will redirect all questions to a local running Webserver.

3. Restart pdns

4. Install nginx, php7, mariadb see here: https://www.vultr.com/docs/setup-up-nginx-php-fpm-and-mariadb-on-debian-8

5. Basic nginx config we used:
```
server {
    listen 80;

    root /var/www/html/dns;
    index index.php index.html index.htm;

    server_name dns-req.local;

    location / {
            try_files $uri $uri/ /index.php;
    }

    location ~ \.php$ {
            try_files $uri =404;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
    }
}
```
6. Restart nginx

7. Clone this git repo into /var/www/html/dns/

8. Import the mysql dump, create a user and update config.php

9. Update index.php and class/Tools.php with your IP addresses

10. Move /remote/ping.php to your external pops, make sure its reachable and update these domains in cron/Runner.php

11. Try a test query with dig @yourDNSIP cdn.yourdomain.com

12. Add cron/Destiny to crontab, so it will be executed every 2 mins
