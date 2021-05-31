<?php
include_once 'lib/php-jwt-master/src/BeforeValidException.php';
include_once 'lib/php-jwt-master/src/ExpiredException.php';
include_once 'lib/php-jwt-master/src/SignatureInvalidException.php';
include_once 'lib/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

class UserAuth{
    
    static $keyJWT = "*********";
    static $keyRefreshToken = "*********";
    static $algo = array('HS256');

    public static function verifyJWT($jwt){
        try {
            $decoded = JWT::decode($jwt, UserAuth::$keyJWT, array('HS256'));
            return $decoded;
        }catch (Exception $e){
            throw new AuthException($e->getMessage());
        }
    }

    public static function issueJWT($user){
        $token = array(
            "iat" => time(),
            "exp" => time()+(60*60),
            "iss" => "nin",
            "type" => "user",
            "data" => array(
                "id"=>$user->id,            
                "firstname"=>$user->firstname,
                "surname"=>$user->surname,
                "mail"=>$user->mail,
                "nickname"=>$user->nickname
            )
        );
        $jwt = JWT::encode($token, UserAuth::$keyJWT);
        return $jwt;
    }

    public static function verifyRefreshToken($token){
        if(!$token) throw new RequestException("Parameter refresh_token not set");
        try {
            $decoded = JWT::decode($token, UserAuth::$keyRefreshToken, array('HS256'));
            $user = User::fromID($decoded->data->id);
            if(strcmp($decoded->data->sign, $user->getSign()) !== 0){
                throw new AuthException("Expired token");
            }
            return $user;

        }catch (Exception $e){
            throw new AuthException($e->getMessage());
        }
    }

    public static function issueRefreshToken($user){
        $token = array(
            "iat" => time(),
            "exp" => time()+(60*60*24*30),
            "iss" => "nin",
            "type" => "refresh",
            "data" => array(
                "id"=>$user->id,         
                "sign"=>$user->getSign()
            )
        );
        $jwt = JWT::encode($token, UserAuth::$keyRefreshToken);
        return $jwt;
    }
}