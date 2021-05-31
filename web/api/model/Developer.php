<?php

class Developer extends User{

    public $developName;

    function __construct(){}



    function createApplication(){
        $data = json_decode(file_get_contents("php://input"));

        $appname=isset($data->name)?trim(strip_tags(htmlspecialchars($data->name))):false;

        $ok=false;

        if($appname){
            $ok=true;
        }else{ throw new RequestException('Parameter name not set'); }

        if($ok){
            $clientId=hash("SHA256", $this->developName.$appname.microtime());
            $conn = DB::getConnection();
            $stmt=$conn->prepare("INSERT INTO applications (`name`, `date`, `id_developer`, `client_id`, `data`)
            VALUES(:name, CURRENT_DATE, :idDev, :clientId, '{}');");
            $stmt->bindParam(":name",$appname);
            $stmt->bindParam(":idDev",$this->id);
            $stmt->bindParam(":clientId",$clientId);
            $stmt->execute();

            if($stmt->rowCount()==1){
                $clientSecret=hash("SHA256", $this->id.$this->developName.$appname.microtime());
                $conn = DB::getConnection();
                $stmt=$conn->prepare("INSERT INTO oauth_clients (`client_id`, `client_secret`)
                VALUES(:clientId, :clientSecret);");
                $stmt->bindParam(":clientId",$clientId);
                $stmt->bindParam(":clientSecret",$clientSecret);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    return "Application have been created successfully";
                }else{
                    throw new InternalException("Error while enabling oauth");
                }
            }else{
                throw new ConflictException(str_replace("_UNIQUE","",$stmt->errorinfo()[2]));
            }
        }
    }

    function deleteApplication($id){
        $id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;

        if($id){
            $ok=true;
        }else{ throw new RequestException("Parameter id not set"); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("DELETE FROM `nilink`.`oauth_clients`
            WHERE client_id=(SELECT DISTINCT client_id FROM applications WHERE id=:idApp AND id_developer=:idDev) ;
            DELETE FROM applications WHERE id=:idApp AND id_developer=:idDev;");
            $stmt->bindParam(":idApp",$id);
            $stmt->bindParam(":idDev",$this->id);
            $stmt->execute();

            if($stmt->rowCount()==1){
                return "Successfully deleted application";
            }else{
                throw new NotFoundException("Application doesn't exists or you don't own it");
            }
        }
    }

    function updateApplication($id){
        $data = json_decode(file_get_contents("php://input"));
        $id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;
        
        if($id){
            $ok=true;
        }else{ throw new RequestException("Parameter id not set"); }
        
        if($ok){
            $success=true;
            $conn = DB::getConnection();
            //description
            $description=isset($data->description)?trim(strip_tags(htmlspecialchars($data->description))):false;
            if($description){
                $stmt=$conn->prepare("UPDATE applications SET description = :desc WHERE id = :idApp;");
                $stmt->bindParam(":idApp",$id);
                $stmt->bindParam(":desc",$description);
                $stmt->execute();
                $success=$success && $stmt->rowCount()==1;
            }
            //name
            $name=isset($data->name)?trim(strip_tags(htmlspecialchars($data->name))):false;
            if($name){
                $stmt=$conn->prepare("UPDATE applications SET name = :name WHERE id = :idApp;");
                $stmt->bindParam(":idApp",$id);
                $stmt->bindParam(":name",$name);
                $stmt->execute();
                $success=$success && $stmt->rowCount()==1;
            }
            if($success){
                return "Successfully updated application's data";
            }else{
                throw new NotFoundException("Application doesn't exists or you don't own it");
            }
        }
    }



    function uploadScreenshot($id){
        $uploadPath="uploads/screenshots/";
        $id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;
        if($id){
            $ok=true;
        }else{ throw new RequestException("Parameter id not set"); }

        if (!isset($_FILES['image']['error']) ||is_array($_FILES['image']['error'])) {
            throw new RequestException('Invalid parameters');
        }
        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_OK:
              break;
            case UPLOAD_ERR_NO_FILE:
              throw new RequestException('No file sent');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
              throw new RequestException('Exceeded filesize limit');
            default:
              throw new InternalException('Unknown error');
        }

        if ($_FILES['image']['size'] > 1000000) {
            throw new RequestException('Exceeded filesize limit');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
          $finfo->file($_FILES['image']['tmp_name']),
          array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png'
          ),
          true
        )) {
          throw new RequestException('Invalid file format.');
        }
        $fname=sha1_file($_FILES['image']['tmp_name']);
        
        if (!move_uploaded_file( $_FILES['image']['tmp_name'], 'uploads/'.$fname.".". $ext)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }
        $png=hash('sha256',$fname.$_FILES['image']['tmp_name']);
        $resize = new ResizeImage('uploads/'.$fname.".". $ext);
        $resize->resizeTo(-1, -1, 'exact');
        $out=$uploadPath.$png.".png";
        $resize->saveImage($out);
        unlink('uploads/'.$fname.".". $ext);
        
        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("INSERT INTO screenshots (id_application, screenshot)
            SELECT * FROM (SELECT :idApp, :image) AS tmp
            WHERE EXISTS (
                SELECT id FROM applications WHERE id_developer = :idDev
            ) LIMIT 1;");
            $stmt->bindParam(":idApp",$id);
            $stmt->bindParam(":idDev",$this->id);
            $stmt->bindParam(":image",$png);
            $stmt->execute();

            if($stmt->rowCount()==1){
                return "Successfully uploaded application's screenshot";
            }else{
                throw new NotFoundException("Application doesn't exists or you don't own it");
            }
        }
    }

    function uploadIcon($id){
        $uploadPath="uploads/icons/";
        $id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;
        if($id){
            $ok=true;
        }else{ throw new RequestException("Parameter id not set"); }

        if (!isset($_FILES['image']['error']) ||is_array($_FILES['image']['error'])) {
            throw new RequestException('Invalid parameters');
        }
        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_OK:
              break;
            case UPLOAD_ERR_NO_FILE:
              throw new RequestException('No file sent');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
              throw new RequestException('Exceeded filesize limit');
            default:
              throw new InternalException('Unknown error');
        }

        if ($_FILES['image']['size'] > 1000000) {
            throw new RequestException('Exceeded filesize limit');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
          $finfo->file($_FILES['image']['tmp_name']),
          array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png'
          ),
          true
        )) {
          throw new RequestException('Invalid file format.');
        }
        $fname=sha1_file($_FILES['image']['tmp_name']);
        
        if (!move_uploaded_file( $_FILES['image']['tmp_name'], 'uploads/'.$fname.".". $ext)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }
        $png=hash('sha256',$fname.$_FILES['image']['tmp_name']);
        $resize = new ResizeImage('uploads/'.$fname.".". $ext);
        $resize->resizeTo(256, 256, 'exact');
        $out=$uploadPath.$png.".png";
        $resize->saveImage($out);

        unlink('uploads/'.$fname.".". $ext);
        
        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("SELECT icon FROM applications WHERE id=:idApp");
            $stmt->bindParam(":idApp",$id);
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($result) && $result[0]['icon']!="0"){
                unlink("uploads/icons/".$result[0]['icon'].".png");
            }
            $stmt=$conn->prepare("UPDATE applications SET icon = :image WHERE id = :idApp");
            $stmt->bindParam(":idApp",$id);
            $stmt->bindParam(":image",$png);
            $stmt->execute();

            if($stmt->rowCount()==1){
                return "Successfully uploaded application's icon";
            }else{
                throw new NotFoundException("Application doesn't exists or you don't own it");
            }
        }
    }

    function deleteScreen($idApp, $idScreen){
        $idApp=isset($idApp)?trim(strip_tags(htmlspecialchars($idApp))):false;
        $idScreen=isset($idScreen)?trim(strip_tags(htmlspecialchars($idScreen))):false;
        $ok=false;

        if($idApp){
            if($idScreen){
                $ok=true;
            }else{ throw new RequestException("Parameter id screen not set"); }
        }else{ throw new RequestException("Parameter id application not set"); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("DELETE FROM screenshots WHERE id_application=:idApp AND screenshot=:idScreen");
            $stmt->bindParam(":idApp",$idApp);
            $stmt->bindParam(":idScreen",$idScreen);
            $stmt->execute();

            if($stmt->rowCount()==1){
                if(unlink("uploads/screenshots/".$idScreen.".png")){
                    return "Successfully deleted screenshot";
                }else{
                    throw new NotFoundException("Application or screenshot doesn't exists or you don't own it");
                }
            }else{
                throw new NotFoundException("Application or screenshot doesn't exists or you don't own it");
            }
        }
    }

    function getApplications(){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT a.id, a.name, CONCAT('/api/icon/',a.icon) AS icon, a.date FROM applications a 
        INNER JOIN developers d ON d.id=a.id_developer
        WHERE d.id=:idDev;");
        $stmt->bindParam(":idDev",$this->id);
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
            $stmt=$conn->prepare("SELECT a.id, a.name, CONCAT('/api/icon/',a.icon) AS icon, a.description, a.date, d.develop_name AS developer, a.client_id, oc.client_secret FROM applications a 
            INNER JOIN developers d ON d.id=a.id_developer
            INNER JOIN oauth_clients oc ON a.client_id=oc.client_id
            WHERE d.id=:idDev AND a.id=:idApp;");
            $stmt->bindParam(":idDev",$this->id);
            $stmt->bindParam(":idApp",$id);
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
                $result[0]['screenshots']=$screenshots;
                return $result;
            }else{
                throw new NotFoundException("Application not found or you don't own the resource");
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
            $stmt=$conn->prepare("SELECT u.*, d.develop_name FROM developers d INNER JOIN users u ON u.id = d.id WHERE mail=:mail");
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
                    $instance->developName=$result[0]["develop_name"];

                    return $instance;
                }else{ throw new AuthException('Invalid credential'); }
            }else{ throw new AuthException('User doesn\'t exists'); }
        }
        
        
    }

    static function fromGoogle($idToken = NULL){
        $idToken=isset($_POST["idtoken"])?$_POST["idtoken"]:false;
        if(!($idToken)){throw new RequestException('Parameter idtoken not set');}

        $client = new Google_Client(['client_id' => "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA.apps.googleusercontent.com"]); 
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
            $stmt=$conn->prepare("SELECT u.*, d.develop_name FROM developers d INNER JOIN users u ON u.id = d.id WHERE mail=:mail AND password IS NULL AND auth_type='google'");
            $stmt->bindParam(":mail",$instance->mail);
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>=1){
                $instance->id=$result[0]["id"];
                $instance->firstname=$result[0]["firstname"];
                $instance->surname=$result[0]["surname"];
                $instance->mail=$result[0]["mail"];
                $instance->nickname=$result[0]["nickname"];
                $instance->developName=$result[0]["develop_name"];

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
            $stmt=$conn->prepare("SELECT u.*, d.develop_name FROM developers d INNER JOIN users u ON u.id = d.id WHERE d.id=:id");
            $stmt->bindParam(":id",$instance->id);
                
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>=1){
                $instance->id=$result[0]["id"];
                $instance->firstname=$result[0]["firstname"];
                $instance->surname=$result[0]["surname"];
                $instance->mail=$result[0]["mail"];
                $instance->nickname=$result[0]["nickname"];
                $instance->developName=$result[0]["develop_name"];
                return $instance;
            }else{
                throw new AuthException("Invalid Token");
            }
            
        }
        
    }
    


    function create($userId=null){
        $data = json_decode(file_get_contents("php://input"));

        $this->developName=isset($data->developName)?trim(strip_tags(htmlspecialchars($data->developName))):false;
        $ok=false;

        if($this->developName){
            $ok=true;                    
        }else{ throw new RequestException('Parameter developName not set'); }

        if($ok){
            $conn = DB::getConnection();
            
            $stmt=$conn->prepare("INSERT INTO developers (`id`, `develop_name`)
            SELECT * FROM (SELECT :id, :developname) as tmp
            WHERE NOT EXISTS (
                SELECT id FROM developers WHERE id = :id
            ) LIMIT 1;");
            $stmt->bindParam(":id",$userId);
            $stmt->bindParam(":developname",$this->developName);
            $stmt->execute();
            if($stmt->rowCount()==1){
                return "Successfully signed up";
            }else{
                throw new ConflictException("User already signed up as a developer"); 
            }
            
        }
    }
    
}