# sudo rsync --exclude='*.zip' -z -h -u -av --progress bweinraub@www.justculture.org:/var/www/vhost/justculture.org/wp-content/themes/ /Library/WebServer/Documents/justculture/justculture_com_replica/wp-content/themes

rsync -z -h -u -av --progress root@199.36.142.138:/var/www/vhost/justculture.org/ .
