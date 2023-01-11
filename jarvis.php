<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('adminHelper.php');

$db = new JarvisAPI();


// count list rows
if( isset($_POST) && $_POST['action'] == 'count'):
    $out                        = [];
    $listpathinfo               = pathinfo( JarvisAPI::getMainList() );
    $out['filename']            = $listpathinfo['filename'].'.'.$listpathinfo['extension'];
    $out['dirname']             = $listpathinfo['dirname'];
    $list                       = JarvisAPI::loadMainList();
    $out['count']               = ( count($list) -1 ); 
    $out['lastModified']        = date("m-d-Y H:i:s", filemtime( JarvisAPI::getMainList() ));
    $out['lastModifiedago']     = JarvisAPI::time_elapsed_string( date("Y-m-d H:i:s", filemtime( JarvisAPI::getMainList() )) );

    echo json_encode($out, true);

endif;

if(isset($_POST) && $_POST['action']== 'getGenres'):
    $result = $db->getGenres();

    echo json_encode( $result );
endif;

/** Update library with row of data */
if(isset($_POST) && $_POST['action'] == 'updateKeep'):

    $payload = json_decode( $_POST['payload'] );
    $result  = $db->updateKeep( $payload );

    echo json_encode( $result );

endif;

/** Get ID3 info of file */
if( isset($_POST) && $_POST['action'] == 'getID3'):
    $filename   = $_POST['filename'];
    $results    = $db->getID3( $filename );
     
    echo json_encode( $results );

endif;


/** SEARCH MAIN LIST  */

if(isset($_POST) && $_POST['action'] == 'search'):
    $payload    = [];
   
    if( !empty($_POST['artist']) && !empty($_POST['title'])):
        $titles = $db->fetchArtist( $_POST['title'] );
    endif;
    
    if( !empty($_POST['title']) && empty($_POST['artist']) ):
        $titles = $db->fetchTitle( trim($_POST['title']) );
    endif;

    if( !empty($_POST['album']) && !empty($_POST['title']) ):
        $titles = $db->fetchAlbum( trim($_POST['title']));
    endif;
   


     // genre search with empty title
    if(empty($_POST['title']) && isset( $_POST['genre']) ){
        $titles = $db->fetchGenre($_POST['genre']);
    }
            
    // iterate titles
    foreach( $titles as $title )
    {
        if(is_file($title->filenamepath)){
            $title->size = $db->humanFileSize( filesize( $title->filenamepath), "MB" );
            $title->fileLine = $db->findInMain($title->filenamepath);
            array_push($payload, $title );
        } 
    }

    echo json_encode( $payload );

endif;


/** ADD TO MAIN  */

if(isset($_POST['action']) && $_POST['action'] == 'addToMain' ):
    $filenamepath   = $_POST['filenampath'];
    $result         = $db->addToMain($filenamepath);
    
    $db->putInList( $filenamepath, 1 );

    echo json_encode(
        [
            'status'        => 'OK', 
            'message'       => $result,
            'filenampath'   => $filenamepath,
            'action'        => 'added'
        ] );

endif;

/** DELETE FROM MAIN  */

if(isset($_POST['action']) && $_POST['action'] == 'deleteFromMain'):
    $lineno     = $_POST['lineno'];
    $result     = $db->deleteFromMain( $lineno );

    $s = $db->removeInList( rtrim($result,"\n") );

    $path       = pathinfo($result);
    $filename   = $path['basename'];

    echo json_encode(
        [
            'status'    => 'OK', 
            'lineno'    => $lineno,
            'filename'  => $filename,
            'message'   => $result,
            'action'    => 'deleted',
            's'         => $s
        ] );

endif;

// DELETE FROM LIBRARY -- 
// and unset the file from the directory
// ========================================================
if(isset($_POST['action']) && $_POST['action'] == 'kill' ):

    $filenamepath = $_POST['filenamepath'];
    
    // delete from library db
    $rowDeleted = $db->deleteFromKeep( $filenamepath );
    
    // delete from filesystem
    unlink($filenamepath);

    echo json_encode(
        [
            'status'        => 'OK',
            'filenamepath'  => $filenamepath,
            'rowDeleted'    => $rowDeleted
        ]
    );

endif;

// LIST ONLY MAIN 
if(isset($_POST['action']) && $_POST['action'] == 'list'):

    $inList = $db->getInList(); 


   echo json_encode( $inList, JSON_PARTIAL_OUTPUT_ON_ERROR );


endif;