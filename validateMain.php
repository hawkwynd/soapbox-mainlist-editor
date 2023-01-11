<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting( E_ALL );

require('adminHelper.php');

$Jarvis  = new JarvisAPI();
$m3uFile = $Jarvis->loadMainList();
$m3uFileName = $Jarvis->getMainList();

echo ($Jarvis->getCount( $m3uFile )-1) . " songs in $m3uFileName <br/>"; 

foreach( $m3uFile as $idx => $filenamepath )
{
   echo "Checking line " . $idx." - $filenamepath";
   
   if (!file_exists(trim($filenamepath))) {
    
    // set in_list in library to 0
      $Jarvis->removeInList( trim($filenamepath) ); 

      echo "line $idx - $filenamepath not found and is being removed because its not there.<br/>";
      unset( $m3uFile[$idx] );
      $idx++;

   }else{
        // set in_list in library to 1
        $Jarvis->putInList( trim($filenamepath) );
        echo " set in_list to 1 <br/>";
   }
  
}

// write new .m3u file 

$newM3uFile = array_values( $m3uFile );

echo "Writing $m3uFileName" . "<br/>";

file_put_contents( $m3uFileName, $newM3uFile );

