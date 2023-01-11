<?php

class JarvisAPI {
    
    // private static $mainList = "/home/scott/radio/playlists/mellow-jazz.m3u";

    private static $mainList = "/home/scott/radio/playlists/main.m3u";
    private static $listpath = "/home/scott/radio/playlists/";
    private $db;
    private $id3;

    public function __construct() 
    {

        // Databse setup
        $config = parse_ini_file('conf/config.ini');
        // $this->mainList = $config['mainlist']['mainlist'];
        $this->db = new PDO("mysql:host=" . $config['db_hostname'] . ";dbname=" . $config['db_database'], $config['db_username'],$config['db_password'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true );
        
        // getID3 class loader
        require( dirname(__FILE__).'/../requester/php-getid3/getid3/getid3.php');
        $this->id3 = new getID3;
       
    }


    public function getGenres() {
        $genres = [];
        $stmt = $this->db->query("SELECT DISTINCT genre FROM library where genre IS NOT NULL ORDER BY genre ");
        
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        foreach($res as $genre ){
            array_push($genres, $genre->genre);
        }

        return $genres;
    }


    public static function loadLists()
    {
        $playlists = [];

        foreach (glob( self::$listpath ."*.m3u") as $filename) {
            $parts = pathinfo( $filename );

            array_push($playlists, $parts );

        }

        return $playlists;

    }

    
    public function updateKeep( $data ){

        $stmt = $this->db->prepare("UPDATE library SET artist=?, title=?,album=?,year=?,genre=?,filenamepath=?,filename=?,fileformat=?,track=?
        WHERE filenamepath=?");
        $stmt->execute(
            [
                $data->artist,
                $data->title,
                $data->album,
                $data->year,
                $data->genre,
                $data->filenamepath,
                $data->filename,
                $data->fileformat,
                $data->track,
                $data->filenamepath
            ]
            );
       
            return $stmt->rowCount();


    }


    public function getID3( $filename )
    {
        $payload      = [];
        $tagTypes     = ['vorbiscomment', 'id3v1', 'id3v2','ape'];
        // $tagTypes     = ['vorbiscomment', 'id3v1', 'id3v2'];

        $ThisFileInfo = $this->id3->analyze( $filename );
        
        if( $ThisFileInfo ){
            
            $tags = $ThisFileInfo['tags']; // array of stuff

            foreach( $tagTypes as $type){

                if( array_key_exists($type, $tags )){

                    // $payload['type']    = $type;
                    // $payload['tags']    = json_encode( $tags );
                    $payload['Title']   = isset($tags[$type]['title']) ? $tags[$type]['title']: '';
                    $payload['Artist']  = isset($tags[$type]['artist']) ? $tags[$type]['artist']: '';
                    $payload['Album']   = isset($tags[$type]['album']) ? $tags[$type]['album']: null ;
                    $payload['Year']    = isset($tags[$type]['date']) ? $tags[$type]['date'] : (isset($tags[$type]['year']) ? $tags[$type]['year'] : null); 
                    $payload['Genre']   = isset($tags[$type]['genre']) ? $tags[$type]['genre'] : null;
                    $payload['Comment'] = isset($tags[$type]['comment']) ? $tags[$type]['comment'] : null;
                    
                    if(array_key_exists('track', $tags[$type])){
                        $payload['Track'] = $tags[$type]['track'][0];
                    }
                    if(array_key_exists('track_number', $tags[$type])){
                        $payload['Track'] = $tags[$type]['track_number'][0];
                    }
                    if(array_key_exists('tracknumber', $tags[$type])){
                        $payload['Track'] = $tags[$type]['tracknumber'][0];
                    }

                    // break;
                }
            }
            // Additional file info and stuff
            $payload['filesize']         = @$ThisFileInfo['filesize'];
            $payload['fileformat']       = @$ThisFileInfo['fileformat']; 
            // $payload['bitrate']          = @$ThisFileInfo['bitrate'];
            $payload['Playtime']         = gmdate("i:s", @$ThisFileInfo['playtime_seconds']); // 04:44
            $payload['Samplerate']      = @$ThisFileInfo['audio']['sample_rate'];
            
            // file informaiton
            $path                        = pathinfo( @$ThisFileInfo['filenamepath'] );
            // $payload['Path']             = $path['dirname'];
            $payload['Filename']         = $path['basename'];
            $payload['filenamepath']     = @$ThisFileInfo['filenamepath'];
            // $payload['replay_    gain']      = @$ThisFileInfo['replay_gain']; //array           
            

        }else{
            return "Nothing came back...";
        }

        return $payload;
    }

    public function fetchGenre( $genre )
    {
        $stmt = $this->db->query("SELECT * FROM library WHERE genre IN('$genre') order by artist, album, title");
        $res = $stmt->fetchALL(PDO::FETCH_OBJ);
        return $res;
    }

    public function fetchArtist( $artist ) 
    {
        // $stmt = $this->db->query("SELECT * FROM library WHERE artist LIKE '%$artist%' ORDER BY artist, album, title");
        $stmt = $this->db->prepare("SELECT * FROM library WHERE artist LIKE ?  ORDER BY artist, album, title");
        
        $stmt->execute([ "%".$artist . "%" ]);

        $res = $stmt->fetchAll( PDO::FETCH_OBJ );

        return $res;      
    }
    
    public function fetchAlbum( $title )
    {
        $stmt = $this->db->prepare("SELECT * FROM library WHERE album LIKE ? ORDER BY artist, title");
        $stmt->execute( ["%".$title."%"] ); 
        
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $res;  
    }

    public function fetchTitle( $title )
    {
        // prepare
        $stmt = $this->db->prepare("SELECT * FROM library WHERE title LIKE ? ORDER BY artist, title");
        // $stmt->bindParam(':title', $title );
        // $stmt = $this->db->query("SELECT * FROM library WHERE title LIKE '%$title%' ORDER BY artist, title");
        $stmt->execute( ["%".$title."%"] ); 
        
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $res;  
    }

    public function fetchRelease($release)
    {
        $stmt   = $this->db->query("SELECT * FROM library WHERE album LIKE '%$release%' ORDER BY artist, title");
        $res    = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $res;
    }

    public function deleteFromKeep($filenamepath)
    {
        $stmt   = $this->db->prepare("DELETE from library where filenamepath=:filenamepath");
        $stmt->bindParam(":filenamepath",$filenamepath,PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function humanFileSize($size,$unit="") {
        if( (!$unit && $size >= 1<<30) || $unit == "GB")
          return number_format($size/(1<<30))."GB";
        if( (!$unit && $size >= 1<<20) || $unit == "MB")
          return number_format($size/(1<<20))."MB";
        if( (!$unit && $size >= 1<<10) || $unit == "KB")
          return number_format($size/(1<<10))."KB";
        return number_format($size)." bytes";
      }


    
    public static function preWrap($array){
        echo "<pre>", print_r($array), "</pre>";
    }
    
    
    public static function getMainList()
    {
        return self::$mainList;
    }

    public static function loadMainList()
    {
        return file( self::getMainList() );
        
    }

    public static function getCount( $array )
    {
        return count( $array );
    }

    public static function getFilename($path)
    {
        $pathInfo = pathinfo($path);
        return $pathInfo['filename'];
    }
    
    /**
     * @return int 
     * returns line number of match found of list array
     * Special Thanks: https://stackoverflow.com/users/2943403/mickmackusa
     * 
     */
    public static function findInMain( $filenamepath )
    {
        $mainList = self::getMainList();
        
        // load main playlist into contents
        $needle = preg_replace('~/(?:[^/]*/)*(?=[^/]*/[^/]*$)~', '', $filenamepath);
        
        // or     implode('/', array_slice(explode('/', $searching), -2)) 
        // ...if you don't like regex

        foreach( file($mainList) as $index => $line) {
            if (strpos($line, $needle) !== false) {
                return $index;
            }
        }
    }


    /**
     * Delete a line number from the main list
     * @return string
     * @var lineno int 
     */

    public static function deleteFromMain( $lineno )
    {
        $filename = self::getMainList();
        $mainList = self::loadMainList();
        
        // /home/scott/Music/pathtofile/filename.ext
        $deleted = $mainList[ $lineno ];

        //Delete the recorded line from array
        unset($mainList[$lineno]);
        
        //Save the file
        file_put_contents( $filename, implode("", $mainList ));

        return  $deleted;       
    }


    public static function addToMain( $filenamepath )
    {
        $filename = self::getMainList();
        $mainList = self::loadMainList();

        array_push($mainList, $filenamepath);
        file_put_contents( $filename, implode("", $mainList ) . "\n" );
        
        $message = pathinfo( $filenamepath );
        
        // update library to set file in main list
        return $message['basename'] . " added successfully";

    }

    public function putInList( $filenamepath, $listID = 1 )
    {
        $stmt   = $this->db->prepare("UPDATE library SET in_list = 1 WHERE filenamepath=:filenamepath");
        $stmt->bindParam(":filenamepath", $filenamepath, PDO::PARAM_STR );
        $stmt->execute();
    }

    public function removeInList( $filenamepath )
    {
        
        $stmt = $this->db->prepare("UPDATE library SET in_list = 0 WHERE filenamepath=:filenamepath");
        $stmt->bindParam( ":filenamepath", $filenamepath, PDO::PARAM_STR);
        $stmt->execute();

        return $filenamepath;
    }

    public function getInList( $listID = 1 ) 
    {
        $stmt   = $this->db->query("SELECT * FROM library WHERE in_list = $listID ORDER BY artist, title");
        $res    = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        return $res;

    }


    public static function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

}