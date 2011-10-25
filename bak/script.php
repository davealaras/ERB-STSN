<?php
$dbhost='localhost';
$dbuser='root';
$dbpass='j3j3m00n';
$dbname='erb_stsn2';
$dir1 = '"D:\\erbbak\\db\\';
$dir2 = 'C:\\Users\\Administrator\\Dropbox\\stsn_backup\\';
$filename = 'BKSTSN'.time() . '.sql';
$backupFile = $dir1.$filename.'"';
$command1 = "C:/xampp/mysql/bin/mysqldump --host=localhost --user=root --password=j3j3m00n $dbname>$backupFile";
$command2 = 'copy '.$backupFile.' '.$dir2.$filename.'"';
echo "Backing up... \n";
system($command1);
echo "Creating duplicate... \n";
system($command2);
echo "Backup complete!";
?>