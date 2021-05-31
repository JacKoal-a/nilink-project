<?php
class Application{
    public $id;
    public $name;
    public $clientId;
    public $idDeveloper;


    static function fromClientId($id){
        $instance = new self();

        $instance->clientId=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $ok=false;

        if($instance->clientId){
            $ok=true;
        }else{ throw new RequestException('Parameter id not set'); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("SELECT * FROM applications WHERE client_id=:clientId");
            $stmt->bindParam(":clientId",$instance->clientId);
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>=1){
                $instance->id=$result[0]["id"];
                $instance->name=$result[0]["name"];
                $instance->clientId=$result[0]["client_id"];
                $instance->idDeveloper=$result[0]["id_developer"];
            }else{
                throw new NotFoundException("Client doesn't exist");
            }
            return $instance;
        }
    }

    function createLobby(){
        $data = json_decode(file_get_contents("php://input"));

        $name=isset($data->name)?trim(strip_tags(htmlspecialchars($data->name))):false;

        $ok=false;

        if($name){
            $ok=true;
        }else{ throw new RequestException('Parameter name not set'); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("INSERT INTO lobbies (`id_application`, `name`, `data`)
            VALUES(:idApp, :name, '{}');");
            $stmt->bindParam(":name",$name);
            $stmt->bindParam(":idApp",$this->id);
            $stmt->execute();

            if($stmt->rowCount()==1){
                return "Lobby have been created successfully";
            }else{
                throw new InternalException("Unable to create lobby");
            }
        }
    }

    function getData(){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT data FROM applications WHERE id=:idApp");
        $stmt->bindParam(":idApp",$this->id);
        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_decode($result[0]["data"]);
        
    }

    function getUserData($u){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT data FROM registrations WHERE id_application=:idApp AND id_user=:idUser ");
        $stmt->bindParam(":idApp",$this->id);
        $stmt->bindParam(":idUser",$u->id);
        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_decode($result[0]["data"]);
    }

    function setData(){
        $data = file_get_contents("php://input");
        $ok=false;
        
        if(json_decode($data)){
            $ok=true;
        }else{ throw new RequestException("Invalid json format"); }
        
        if($ok){
            $data=json_encode(json_decode($data));
            $conn = DB::getConnection();
            
            $stmt=$conn->prepare("UPDATE applications SET data = :data WHERE id = :idApp;");
            $stmt->bindParam(":idApp",$this->id);
            $stmt->bindParam(":data",$data);
            $stmt->execute();
            
            if($stmt->rowCount()==1){
                return "Successfully updated application's data";
            }else{
                throw new NotFoundException("Application doesn't exists or you don't own it");
            }
        }
    }

    function setUserData($u){
        $data = file_get_contents("php://input");
        $ok=false;
        
        if(json_decode($data)){
            $ok=true;
        }else{ throw new RequestException("Invalid json format"); }
        
        if($ok){
            $data=json_encode(json_decode($data));
            $conn = DB::getConnection();
            $stmt=$conn->prepare("UPDATE registrations SET data = :data WHERE id_application=:idApp AND id_user=:idUser");
            $stmt->bindParam(":idApp",$this->id);
            $stmt->bindParam(":idUser",$u->id);
            $stmt->bindParam(":data",$data);
            $stmt->execute();
            
            if($stmt->rowCount()==1){
                return "Successfully updated user's data";
            }else{
                throw new NotFoundException("Application doesn't exists or you don't own it");
            }
        }
    }


}