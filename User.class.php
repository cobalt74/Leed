<?php

/*
 @nom: User
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des utilisateurs
 */

class User extends MysqlEntity{

    protected $id,$login,$password,$prefixDatabase;
    protected $TABLE_NAME = 'user';
    protected $CLASS_NAME = 'User';
    protected $object_fields = 
    array(
        'id'=>'key',
        'login'=>'string',
        'password'=>'string',
        'prefixDatabase'=>'string',
    );

    function __construct(){
        parent::__construct();
    }

    function setId($id){
        $this->id = $id;
    }

    function exist($login,$password,$salt=''){
        $userManager = new User();
        return $userManager->load(array('login'=>$login,'password'=>User::encrypt($password,$salt)));
    }

    function get($login){
        $userManager = new User();
        return $userManager->load(array('login'=>$login,));
    }

    function getToken() {
        assert('!empty($this->password)');
        assert('!empty($this->login)');
        return sha1($this->password.$this->login);
    }

    static function existAuthToken($auth=null){
        $result = false;
        $userManager = new User();
        $users = $userManager->populate('id');
        if (empty($auth)) $auth = @$_COOKIE['leedStaySignedIn'];
        foreach($users as $user){
            if($user->getToken()==$auth) $result = $user;
        }
        return $result;
    }

    function setStayConnected() {
        ///@TODO: set the current web directory, here and on del
        setcookie('leedStaySignedIn', $this->getToken(), time()+31536000);
    }

    static function delStayConnected() {
        setcookie('leedStaySignedIn', '', -1);
    }

    function getId(){
        return $this->id;
    }

    function getLogin(){
        return $this->login;
    }
    
    function setLogin($login){
        $this->login = $login;
    }

    function getPassword(){
        return $this->password;
    }

    function setPassword($password,$salt=''){
        $this->password = User::encrypt($password,$salt);
    }

    static function encrypt($password, $salt=''){
        return sha1($password.$salt);
    }

    static function generateSalt() {
        return ''.mt_rand().mt_rand();
    }

    function setPrefixDatabase($prefix){
        $this->prefixDatabase = $prefix;
    }

    function getprefixDatabase(){
        return $this->prefixDatabase;
    }

    function getUsersCodeSynchro() {
        $objects = array();
        $userManager = new User();
        $users = $userManager->populate('id');
        $query = '';
        $i=false;
        foreach($users as $user){
            $prefixTable = $user->getprefixDatabase();
            if($i){$query.=' UNION ';}else{$i=true;}
            $query.= 'SELECT '.$user->getId().' as id,\''.$user->getLogin().'\' as login, value, (select count(1) from '.$prefixTable.'feed) as nbfeed, (select count(1) from '.$prefixTable.'event WHERE unread=1) as nbunread, (select count(1) from '.$prefixTable.'event) as nbarticle FROM '.$prefixTable.'configuration WHERE `Key`=\'synchronisationCode\'';
        }
        $result = $this->customQuery($query);
        while ($row = mysql_fetch_assoc($result)) {
            $objects[] = $row;
        }
        return $objects;
    }

    function getUserByCodeSync ($codeSync) {
        if (isset($codeSync)){
            $objects = array();
            $userManager = new User();
            $objects = $userManager->getUsersCodeSynchro();
            $user = false;
            foreach ($objects as $users) {
                if ($users['value']==$codeSync)
                    $user = $userManager->load(array('id'=>$users['id']));
            }
            return $user;
        } else {
            return false;
        }
    }
}

?>
