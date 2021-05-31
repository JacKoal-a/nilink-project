<?php

include_once 'lib/php-jwt-master/src/BeforeValidException.php';
include_once 'lib/php-jwt-master/src/ExpiredException.php';
include_once 'lib/php-jwt-master/src/SignatureInvalidException.php';
include_once 'lib/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

class DeveloperAuth{

    static $key = "*********";
    static $keyRefreshToken = "*********";
    static $algo = array('HS256');

    public static function verifyJWT($jwt){
        try {
            $decoded = JWT::decode($jwt, DeveloperAuth::$key, array('HS256'));
            return $decoded;
        }catch (Exception $e){
            throw new AuthException($e->getMessage());
        }
    }

    public static function issueJWT($developer){
        $token = array(
            "iat" => time(),
            "exp" => time()+(60*60),
            "iss" => "nin",
            "type" => "developer",
            "data" => array(
                "id"=>$developer->id,            
                "firstname"=>$developer->firstname,
                "surname"=>$developer->surname,
                "mail"=>$developer->mail,
                "nickname"=>$developer->nickname,
                "develop_name"=>$developer->developName
            )
        );
        $jwt = JWT::encode($token, DeveloperAuth::$key);
        return $jwt;
    }

    public static function verifyRefreshToken($token){
        if(!$token) throw new RequestException("Parameter refresh_token not set");
        try {
            $decoded = JWT::decode($token, DeveloperAuth::$keyRefreshToken, array('HS256'));
            $user = Developer::fromID($decoded->data->id);
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
        $jwt = JWT::encode($token, DeveloperAuth::$keyRefreshToken);
        return $jwt;
    }

}