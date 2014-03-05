<?php

/*
 @nom: Event
 @auteur: Idleman (idleman@idleman.fr)
 @description: Classe de gestion des évenements/news liés a chaques flux RSS/ATOM
 */

class Event extends MysqlEntity{

    protected $id,$title,$guid,$content,$description,$pudate,$link,$feed,$category,$creator,$unread,$favorite;
    protected $TABLE_NAME = 'event';
    protected $CLASS_NAME = 'Event';
    protected $object_fields =
    array(
        'id'=>'key',
        'guid'=>'longstring',
        'title'=>'string',
        'creator'=>'string',
        'content'=>'extralongstring',
        'description'=>'longstring',
        'link'=>'longstring',
        'unread'=>'integer',
        'feed'=>'integer',
        'unread'=>'integer',
        'favorite'=>'integer',
        'pubdate'=>'integer',
        'syncId'=>'integer',
    );

    protected $object_fields_index =
    array(
        'feed'=>'index',
        'unread'=>'index',
        'favorite'=>'index'
    );

    function __construct($guid=null,$title=null,$description=null,$content=null,$pubdate=null,$link=null,$category=null,$creator=null){

        $this->guid = $guid;
        $this->title = $title;
        $this->creator = $creator;
        $this->content = $content;
        $this->description = $description;
        $this->pubdate = $pubdate;
        $this->link = $link;
        $this->category = $category;
        parent::__construct();
        $myUser = (isset($_SESSION['currentUser'])?unserialize($_SESSION['currentUser']):false);
        if ($myUser!=false) { $this->setPrefixTable($myUser->getPrefixDatabase()); }
    }

    function getEventCountPerFolder(){
        $events = array();
        $results = $this->customQuery('SELECT COUNT('.$this->getPrefixTable().$this->TABLE_NAME.'.id),'.$this->getPrefixTable().'feed.folder FROM '.$this->getPrefixTable().$this->TABLE_NAME.' INNER JOIN '.$this->getPrefixTable().'feed ON ('.$this->getPrefixTable().'event.feed = '.$this->getPrefixTable().'feed.id) WHERE '.$this->getPrefixTable().$this->TABLE_NAME.'.unread=1 GROUP BY '.$this->getPrefixTable().'feed.folder');
        while($item = mysql_fetch_array($results)){
            $events[$item[1]] = $item[0];
        }

        return $events;
    }

    function getEventCountNotVerboseFeed(){
        $results = $this->customQuery('SELECT COUNT(1) FROM '.$this->getPrefixTable().$this->TABLE_NAME.' INNER JOIN '.$this->getPrefixTable().'feed ON ('.$this->getPrefixTable().'event.feed = '.$this->getPrefixTable().'feed.id) WHERE '.$this->getPrefixTable().$this->TABLE_NAME.'.unread=1 AND '.$this->getPrefixTable().'feed.isverbose=0');
        while($item = mysql_fetch_array($results)){
            $nbitem =  $item[0];
        }

        return $nbitem;
    }

    function getEventsNotVerboseFeed($start=0,$limit=10000,$order,$columns='*'){
        $eventManager = new Event();
        $objects = array();
        $results = $this->customQuery('SELECT '.$columns.' FROM '.$this->getPrefixTable().'event INNER JOIN '.$this->getPrefixTable().'feed ON ('.$this->getPrefixTable().'event.feed = '.$this->getPrefixTable().'feed.id) WHERE '.$this->getPrefixTable().'event.unread=1 AND '.$this->getPrefixTable().'feed.isverbose = 0 ORDER BY '.$order.' LIMIT '.$start.','.$limit);
        if($results!=false){
            while($item = mysql_fetch_array($results)){
                $object = new Event();
                foreach($object->getObject_fields() as $field=>$type){
                    $setter = 'set'.ucFirst($field);
                    if(isset($item[$field])) $object->$setter($item[$field]);
                }
                $objects[] = $object;
                unset($object);
            }
        }
        return $objects;
    }

    function setId($id){
        $this->id = $id;
    }

    function getCreator(){
        return $this->creator;
    }

    function setCreator($creator){
        $this->creator = $creator;
    }

    function getCategory(){
        return $this->category;
    }

    function setCategory($category){
        $this->category = $category;
    }

    function getDescription(){
        return $this->description;
    }

    function setDescription($description,$encoding = true){
        $this->description = $description;
    }

    function getPubdate($format=false){
        if($this->pubdate!=0){
        return ($format!=false?date($format,$this->pubdate):$this->pubdate);
        }else{
            return '';
        }
    }

    function getPubdateWithInstant($instant){
        $alpha = $instant - $this->pubdate;
        if ($alpha < 86400 ){
            $hour = floor($alpha/3600);
            $alpha = ($hour!=0?$alpha-($hour*3600):$alpha);
            $minuts = floor($alpha/60);
            return 'il y a '.($hour!=0?$hour.'h et':'').' '.$minuts.'min';
        }else{
            return 'le '.$this->getPubdate('d/m/Y à H:i:s');
        }
    }

    function setPubdate($pubdate){
        $this->pubdate = (is_numeric($pubdate)?$pubdate:strtotime($pubdate));
    }

    function getLink(){
        return $this->link;
    }

    function setLink($link){
        $this->link = $link;
    }

    function getId(){
        return $this->id;
    }

    function getTitle(){
        return $this->title;
    }

    function setTitle($title){
        $this->title = $title;
    }

    function getContent(){
        return $this->content;
    }

    function setContent($content,$encoding=true){
        $this->content = $content;
    }


    function getGuid(){
        return $this->guid;
    }

    function setGuid($guid){
        $this->guid = $guid;
    }

    function getSyncId(){
        return $this->syncId;
    }

    function setSyncId($syncId){
        $this->syncId = $syncId;
    }

    function getUnread(){
        return $this->unread;
    }

    function setUnread($unread){
        $this->unread = $unread;
    }
    function setFeed($feed){
        $this->feed = $feed;
    }
    function getFeed(){
        return $this->feed;
    }
    function setFavorite($favorite){
        $this->favorite = $favorite;
    }
    function getFavorite(){
        return $this->favorite;
    }

}

?>
