# Koken backup

Koken store it's picture into lots of subdirectories, without any option to grab all picture from an album in case of you want to backup (or migrate to an other system) them.

This little script allow to make an export of a complete album into one subdirectory.

Example : 
```
php koken_backup.php id=12
```
This will export the album 12 into a "storage/backup/<album_machine_name>/" directory.
