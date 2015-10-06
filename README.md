# Koken backup

Disclaimer : I know, core is horrible. It's base upon some existing koken code.

Disclaimer 2: __DO NOT LEAVE THIS SCRIPT ON A PRODUCTION SERVER__, it does not respect any access right, and SQL query are not safely done

Koken store it's picture into lots of subdirectories, without any option to grab all picture from an album in case of you want to backup (or migrate to an other system) them.

This little script allow to make an export of a complete album into one subdirectory.

The koken_backup.php file needs to be in the koken's root directory.

Example : 
```
php koken_backup.php id=12
```
This will export the album 12 into a "storage/backup/<album_machine_name>/" directory.
