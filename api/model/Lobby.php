<?php
class Lobby{
    public $id;
    public $idApplication;

    static function fromId($id, $idApp){
        $instance = new self();
        $instance->id=isset($id)?trim(strip_tags(htmlspecialchars($id))):false;
        $instance->idApplication=isset($idApp)?trim(strip_tags(htmlspecialchars($idApp))):false;
        $ok=false;

        if($instance->id){
            if($instance->idApplication){
                $ok=true;
            }else{ throw new RequestException('Parameter app not set'); }
        }else{ throw new RequestException('Parameter id not set'); }

        if($ok){
            $conn = DB::getConnection();
            $stmt=$conn->prepare("SELECT * FROM lobbies WHERE id=:id AND id_application=:idApp");
            $stmt->bindParam(":id",$instance->id);
            $stmt->bindParam(":idApp",$instance->idApplication);
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>=1){
                $instance->id=$result[0]["id"];
                $instance->idApplication=$result[0]["id_application"];
            }else{
                throw new NotFoundException("Lobby doesn't exist");
            }
            return $instance;
        }
    }


    function listen($u){
        $timestamp = date_timestamp_get(date_create());
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT b.content, u.id, u.nickname, ROUND(UNIX_TIMESTAMP(b.timestamp)*1000) as timestamp FROM broadcasts b 
        INNER JOIN users u ON u.id=b.id_user
        INNER JOIN members m ON m.id_lobby = b.id_lobby AND m.id_user=:idUser
        WHERE b.timestamp >= m.timestamp AND u.id!=:idUser AND b.id_lobby=:id ORDER BY b.timestamp");
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":idUser", $u->id);
        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $data=array();
        foreach( $result as $r){
            $data[]=array(
                "from"=>array("user_id"=>  HashInt::hash($r["id"], 10), "nickname"=>$r["nickname"]),
                "content"=>json_decode($r["content"]),
                "timestamp"=>$r["timestamp"]
            );
        }
        $last = end($data);
        if($last){
            $stmt=$conn->prepare("UPDATE members SET timestamp = FROM_UNIXTIME(:time) WHERE id_lobby = :id AND id_user = :idUser");
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":idUser", $u->id);
            $stmt->bindParam(":time", $last["timestamp"] );
            $stmt->execute();
            print_r( $stmt->errorInfo());
        }
        return $data;
        
    }

    function send($u){
        $data = file_get_contents("php://input");
        $ok=false;
        
        if(json_decode($data)){
            $ok=true;
        }else{ throw new RequestException("Invalid json format"); }
        
        if($ok){
            $data=json_encode(json_decode($data));
            $conn = DB::getConnection();
            $stmt=$conn->prepare("INSERT INTO broadcasts (id_user, content, id_lobby) VALUES(:idUser, :data, :id);");
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":data", $data);
            $stmt->bindParam(":idUser", $u->id);
            $stmt->execute();
            
            if($stmt->rowCount()==1){
                return "Successfully sent data";
            }else{
                throw new NotFoundException("Lobby doesn't exist");
            }
        }
    }

    function getMembers(){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT users.nickname, members.status, 
        CASE WHEN members.status =1 THEN 'online'ELSE 'offline'END AS verbose_status  FROM members 
        INNER JOIN users ON users.id=members.id_user");
        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
        
    }


    function join($u){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("INSERT INTO members (`id_lobby`, `id_user`, `data`)
        SELECT * FROM (SELECT :idLobby, :idUser, '{}') as tmp
        WHERE NOT EXISTS (SELECT * FROM members WHERE id_lobby = :idLobby AND id_user=:idUser ) LIMIT 1;
        UPDATE members SET status = 1 WHERE WHERE id_lobby = :idLobby AND id_user=:idUser");
        $stmt->bindParam(":idLobby",$this->id);
        $stmt->bindParam(":idUser",$u->id);
        $stmt->execute();

        $stmt=$conn->prepare("UPDATE members SET status = 1, timestamp=CURRENT_TIMESTAMP WHERE  id_lobby = :idLobby AND id_user=:idUser;");
        $stmt->bindParam(":idLobby",$this->id);
        $stmt->bindParam(":idUser",$u->id);
        $stmt->execute();
        if($stmt->rowCount()==1){
            return "Successfully joined lobby";
        }else{
            throw new NotFoundException("Lobby doesn't exist or user hasn't joined yet");
        }
            
    }

    function quit($u){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("UPDATE members SET status = 0 WHERE id_lobby = :idLobby AND id_user=:idUser;");
        $stmt->bindParam(":idLobby",$this->id);
        $stmt->bindParam(":idUser",$u->id);
        $stmt->execute();
        if($stmt->rowCount()==1){
            return "Successfully quitted lobby";
        }else{
            throw new NotFoundException("Lobby doesn't exist or user hasn't joined yet");
        }
            
    }

    function getData(){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT data FROM lobbies WHERE id=:id");
        $stmt->bindParam(":id",$this->id);
        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_decode($result[0]["data"]);
        
    }

    function getUserData($u){
        $conn = DB::getConnection();
        $stmt=$conn->prepare("SELECT data FROM members WHERE id_lobby=:idLobby AND id_user=:idUser");
        $stmt->bindParam(":idLobby",$this->id);
        $stmt->bindParam(":idUser",$u->id);
        $stmt->execute();
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return isset($result[0])?json_decode($result[0]["data"]):null;
        
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
            $stmt=$conn->prepare("UPDATE lobbies SET data = :data WHERE id = :id;");
            $stmt->bindParam(":id",$this->id);
            $stmt->bindParam(":data",$data);
            $stmt->execute();
            
            if($stmt->rowCount()==1){
                return "Successfully updated lobby's data";
            }else{
                throw new NotFoundException("Lobby doesn't exist");
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
            $stmt=$conn->prepare("UPDATE members SET data = :data WHERE id_lobby=:idLobby AND id_user=:idUser");
            $stmt->bindParam(":idLobby",$this->id);
            $stmt->bindParam(":idUser",$u->id);
            $stmt->bindParam(":data",$data);
            $stmt->execute();
            
            if($stmt->rowCount()==1){
                return "Successfully updated user's data";
            }else{
                throw new NotFoundException("Lobby doesn't exist or user hasn't joined yet");
            }
        }
    }
    
}