<?php

class User{
    public $id;
    public $firstname;
    public $surname;
    public $mail;
    public $nickname;

    private $table="users";


    function __construct(){}

    
    function deleteData($id){
        $id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;

        if($id){
            $ok=true;
        }else{ throw new RequestException("Parameter id not set"); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("DELETE FROM registrations WHERE id IN
            (SELECT * FROM  (SELECT DISTINCT id FROM registrations WHERE id_user=:idUser AND id_application=:idApp) as tmp);");
            $stmt->bindParam(":idUser",$this->id);
            $stmt->bindParam(":idApp",$id);
            $stmt->execute();

            if($stmt->rowCount()==1){
                return "Successfully deleted data";
            }else{
                throw new NotFoundException("User doesn't have any data");
            }
        }
    }

    function signupApplication($id){
        $id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;

        if($id){
            $ok=true;
        }else{ throw new RequestException("Parameter id not set"); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("INSERT INTO registrations (`id_user`, `id_application`, `data`)
            SELECT * FROM (SELECT :idUser, :idApp, '{}') as tmp
            WHERE NOT EXISTS (
                SELECT * FROM registrations WHERE id_user = :idUser AND id_application = :idApp
            ) LIMIT 1;");
            $stmt->bindParam(":idUser",$this->id);
            $stmt->bindParam(":idApp",$id);
            $stmt->execute();
            if($stmt->rowCount()==1){
                return "Successfully signed up";
            }else{
                throw new ConflictException("User already signed up");
            }
        }
    }


    function getMyApps(){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT a.id, a.name, CONCAT('/api/icon/',a.icon) AS icon, d.develop_name, r.timestamp FROM registrations r 
        INNER JOIN applications a ON a.id=r.id_application
        INNER JOIN developers d ON d.id=a.id_developer
        WHERE r.id_user=:idUser;");
        $stmt->bindParam(":idUser",$this->id);
        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function getMyApp($id){
        $id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;

        if($id){
            $ok=true;
        }else{ throw new RequestException("Parameter id not set"); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("SELECT a.name, CONCAT('/api/icon/',a.icon) AS icon, d.develop_name AS developer, r.timestamp FROM registrations r 
            INNER JOIN applications a ON a.id=r.id_application
            INNER JOIN developers d ON d.id=a.id_developer
            WHERE r.id_user=:idUser AND r.id_application=:idApplication");
            $stmt->bindParam(":idUser",$this->id);
            $stmt->bindParam(":idApplication",$id);

            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($result)){
                return $result;
            }else{
                throw new NotFoundException("User doesn't have any data here");
            }
            
        }
    }    


    function getApplications(){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT a.id, a.name, CONCAT('/api/icon/',a.icon) AS icon, d.develop_name AS developer FROM applications a
        INNER JOIN developers d ON d.id=a.id_developer");
        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    
    }

    function getApplication($id){
        $id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;

        if($id){
            $ok=true;
        }else{ throw new RequestException("Parameter id not set"); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("SELECT a.name, CONCAT('/api/icon/',a.icon) AS icon, a.description, d.develop_name AS developer FROM applications a
            INNER JOIN developers d ON d.id=a.id_developer WHERE a.id=:idApplication" ) ;
            $stmt->bindParam(":idApplication",$id);
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt=$conn->prepare("SELECT s.screenshot FROM screenshots s WHERE s.id_application=:idApp");
            $stmt->bindParam(":idApp",$id);
            $stmt->execute();
            $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
            $screenshots=array();
            foreach($res as $s){
                $screenshots[]="/api/screenshot/".$s['screenshot'];
            }
            if(!empty($result)){
                $result[0]["screenshots"]=$screenshots;
                return $result;
            }else{
                throw new NotFoundException("The resource doesn't exist");
            }
        }
    }    


    static function fromCredential($mail=null, $pass=null){
        $data = json_decode(file_get_contents("php://input"));
        if(isset($mail) && isset($pass)){
            $data =(object) [
                'mail' => $mail,
                'password' => $pass,
              ];
        }else{
            $data = json_decode(file_get_contents("php://input"));
        }
        $instance = new self();
        $instance->mail=isset($data->mail)?trim(strip_tags(htmlspecialchars($data->mail))):false;
        $password=isset($data->password)?trim(strip_tags(htmlspecialchars($data->password))):false;
        $ok=false;

        if($instance->mail){
            if(filter_var($instance->mail, FILTER_VALIDATE_EMAIL)){
                if($password){
                    if(strlen($password)>=8){
                        $ok=true;
                    }else{ throw new RequestException('Parameter password must be at least 8 charachters long'); }
                }else{ throw new RequestException('Parameter password not set'); }
            }else{ throw new RequestException('Parameter mail invalid value'); }
        }else{ throw new RequestException('Parameter mail not set'); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("SELECT * FROM $instance->table WHERE mail=:mail AND password IS NOT NULL");
            $stmt->bindParam(":mail",$instance->mail);
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>=1){
                if(password_verify($password, $result[0]["password"])){
                    $instance->id=$result[0]["id"];
                    $instance->firstname=$result[0]["firstname"];
                    $instance->surname=$result[0]["surname"];
                    $instance->mail=$result[0]["mail"];
                    $instance->nickname=$result[0]["nickname"];

                    return $instance;
                }else{ throw new AuthException('Invalid credential'); }
            }else{ throw new AuthException('User doesn\'t exists'); }
        }
        
        
    }

    static function fromGoogle($idToken=null){
        if(!isset($idToken)){
            $idToken=isset($_POST["idtoken"])?$_POST["idtoken"]:false;
        }
        if(!($idToken)){throw new RequestException('Parameter idtoken not set');}

        $client = new Google_Client(['client_id' => "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA.apps.googleusercontent.com"]); 
        $payload = $client->verifyIdToken($idToken);
        if ($payload) {
            $mail = $payload["email"];
        } else {
            throw new RequestException('invalid google token');
        }

        
        $instance = new self();
        $instance->mail=isset($mail)?trim(strip_tags(htmlspecialchars($mail))):false;
        $ok=false;

        if($instance->mail){
            if(filter_var($instance->mail, FILTER_VALIDATE_EMAIL)){
                $ok=true;
            }else{ throw new RequestException('Parameter mail invalid value'); }
        }else{ throw new RequestException('Parameter mail not set'); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("SELECT * FROM $instance->table WHERE mail=:mail AND password IS NULL AND auth_type='google'");
            $stmt->bindParam(":mail",$instance->mail);
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>=1){
                    $instance->id=$result[0]["id"];
                    $instance->firstname=$result[0]["firstname"];
                    $instance->surname=$result[0]["surname"];
                    $instance->mail=$result[0]["mail"];
                    $instance->nickname=$result[0]["nickname"];

                    return $instance;
               
            }else{ throw new AuthException('User doesn\'t exists'); }
        }
        
        
    }

    static function fromID($id){
        $instance = new self();

        $instance->id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;

        if($instance->id){
            $ok=true;
        }else{ throw new RequestException('Parameter id not set'); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("SELECT * FROM $instance->table WHERE id=:id");
            $stmt->bindParam(":id",$instance->id);
                
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>=1){
                $instance->id=$result[0]["id"];
                $instance->firstname=$result[0]["firstname"];
                $instance->surname=$result[0]["surname"];
                $instance->mail=$result[0]["mail"];
                $instance->nickname=$result[0]["nickname"];
                return $instance;
            }else{
                throw new AuthException("Invalid Token");
            }

        
        }
        
    }
    
    
    function create($userId=null){
        $data = json_decode(file_get_contents("php://input"));

        $this->firstname=isset($data->firstname)?trim(strip_tags(htmlspecialchars($data->firstname))):false;
        $this->surname=isset($data->surname)?trim(strip_tags(htmlspecialchars($data->surname))):false;
        $this->mail=isset($data->mail)?trim(strip_tags(htmlspecialchars($data->mail))):false;
        $password=isset($data->password)?trim(strip_tags(htmlspecialchars($data->password))):false;
        $this->nickname=isset($data->nickname)?trim(strip_tags(htmlspecialchars($data->nickname))):false;

        $ok=false;

        if($this->firstname){
            if($this->surname){
                if($this->mail){
                    if(filter_var($this->mail, FILTER_VALIDATE_EMAIL)){
                        if($password){
                            if(strlen($password)>=8){
                                if($this->nickname){
                                    $ok=true;
                                }else{ throw new RequestException('Parameter nickname not set'); }
                            }else{ throw new RequestException('Parameter password must be at least 8 charachters long'); }
                        }else{ throw new RequestException('Parameter password not set'); }
                    }else{ throw new RequestException('Parameter mail invalid value'); }
                }else{ throw new RequestException('Parameter mail not set'); }
            }else{ throw new RequestException('Parameter surname not set'); }
        }else{ throw new RequestException('Parameter firstname not set'); }

        if($ok){
            $hash=password_hash($password, PASSWORD_BCRYPT);
            $sign = bin2hex(openssl_random_pseudo_bytes(16));

            $conn = DB::getConnection();
            $stmt=$conn->prepare("INSERT INTO `nilink`.`users`
            (`firstname`,`surname`,`mail`,`password`,`nickname`,`token_sign`) VALUES
            (:firstname, :surname, :mail, :password, :nickname, :tokenSign )");
            $stmt->bindParam(":firstname",$this->firstname);
            $stmt->bindParam(":surname",$this->surname);
            $stmt->bindParam(":mail",$this->mail);
            $stmt->bindParam(":password",$hash);
            $stmt->bindParam(":nickname",$this->nickname);
            $stmt->bindParam(":tokenSign",$sign);

            $stmt->execute();
            if($stmt->rowCount()==1){
                return "Successfully signed up";
            }else{
                throw new ConflictException(str_replace("_UNIQUE","",$stmt->errorinfo()[2])); 
            }
            
        }
    }

    function createFromGoogle(){
        $idToken=isset($_POST["idtoken"])?$_POST["idtoken"]:false;
        if(!($idToken)){throw new RequestException('Parameter idtoken not set');}

        $client = new Google_Client(['client_id' => "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA.apps.googleusercontent.com"]); 
        $payload = $client->verifyIdToken($idToken);
        if ($payload) {
            $mail = $payload["email"];
            $name = $payload["given_name"];
            $surname = $payload["family_name"];
            $nickname = $payload["name"];
        } else {
            throw new RequestException('invalid google token');
        }
        $this->firstname=isset($name)?trim(strip_tags(htmlspecialchars($name))):false;
        $this->surname=isset($surname)?trim(strip_tags(htmlspecialchars($surname))):false;
        $this->mail=isset($mail)?trim(strip_tags(htmlspecialchars($mail))):false;
        $this->nickname=isset($nickname)?trim(strip_tags(htmlspecialchars($nickname))):false;

        $ok=false;

        if($this->firstname){
            if($this->surname){
                if($this->mail){
                    if(filter_var($this->mail, FILTER_VALIDATE_EMAIL)){
                        if($this->nickname){
                            $ok=true;
                        }else{ throw new RequestException('Parameter nickname not set'); }
                    }else{ throw new RequestException('Parameter mail invalid value'); }
                }else{ throw new RequestException('Parameter mail not set'); }
            }else{ throw new RequestException('Parameter surname not set'); }
        }else{ throw new RequestException('Parameter firstname not set'); }

        if($ok){
            $sign = bin2hex(openssl_random_pseudo_bytes(16));

            $conn = DB::getConnection();
            $stmt=$conn->prepare("INSERT INTO `nilink`.`users`
            (`firstname`,`surname`,`mail`,`auth_type`,`nickname`, `token_sign`) VALUES
            (:firstname, :surname, :mail, 'google', :nickname, :tokenSign )");
            $stmt->bindParam(":firstname",$this->firstname);
            $stmt->bindParam(":surname",$this->surname);
            $stmt->bindParam(":mail",$this->mail);
            $stmt->bindParam(":nickname",$this->nickname);
            $stmt->bindParam(":tokenSign",$sign);

            $stmt->execute();
            if($stmt->rowCount()==1){
                return "Successfully signed up";
            }else{
                throw new ConflictException(str_replace("_UNIQUE","",$stmt->errorinfo()[2])); 
            }
            
        }
        
    }

    
    function getSign(){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT token_sign FROM Users
        WHERE id=:idUser");
        $stmt->bindParam(":idUser",$this->id);

        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($result)){
            return $result[0]["token_sign"];
        }
        return null;
    }

    function setSign($sign){
        $sign=isset($sign)?trim(strip_tags(htmlspecialchars($sign))):false;
        $ok=false;
        
        if($sign){
            $ok=true;
        }else{ throw new RequestException('Parameter id not set'); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("UPDATE users SET `token_sign` = :tokenSign WHERE `id` = :idUser");
            $stmt->bindParam(":tokenSign",$sign);
            $stmt->bindParam(":idUser",$this->id);
            $stmt->execute();
           
            if($stmt->rowCount()==1){
                return true;
            }
            return false;
        }
    }
    
}