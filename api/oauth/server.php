<?php
require_once('oauth2-server-php/src/OAuth2/Autoloader.php');

class OAuth{
    static $server;

    static function init(){
        $config=$GLOBALS['config'];

        $dsn = "mysql:host={$config["db"]["host"]};dbname={$config["db"]["dbname"]};charset=utf8";
        $username = $config['db']['username'];
        $password = $config['db']['password'];

        OAuth2\Autoloader::register();
        $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
        // Pass a storage object or array of storage objects to the OAuth2self:: server class
        self::$server = new OAuth2\Server($storage, array(
            'always_issue_new_refresh_token' => true,
            'refresh_token_lifetime'         => 2419200,
        ));

        self::$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
        self::$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
        self::$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, array(
            'always_issue_new_refresh_token' => true
        )));

    }

    static function issueToken(){
        self::$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    }

    static function authorize(){
        header("Content-Type: text/html; charset=UTF-8");
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();
        $err="";
        // validate the authorize request
        if (!self::$server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            die;
        }
        if (!empty($_POST)) {
            $is_authorized = ($_POST['authorized'] === 'yes');
            if(!$is_authorized){
                self::$server->handleAuthorizeRequest($request, $response, $is_authorized);
                $response->send();
                exit();
            }
            try{
                $u=null;
                if(isset($_POST['google'])){
                    $u = User::fromGoogle($_POST['idtoken']);
                }else{
                    $u = User::fromCredential($_POST['mail'],$_POST['pass']);
                }

                self::$server->handleAuthorizeRequest($request, $response, $is_authorized,$u->id);
                $response->send();
                if($response->getStatusCode()==302){
                    try{
                        $a = Application::fromClientId($_GET["client_id"]);
                    }catch(Exception $e){
                        return handleException($e);
                    }
                    
                    try{
                        $u->signupApplication($a->id);
                    }catch(Exception $e){}
    
                }
                exit();
            }catch(Exception $e){
                $err= $e->getMessage();
                $is_authorized=false;
                $u=new User();
                $_POST=null;
            }
        }

        if (empty($_POST)) {
            try{
                $a = Application::fromClientId($_GET["client_id"]);
            }catch(Exception $e){
                return handleException($e);
            }
            
            require "oauth/layout/oauth-authorize.php";

            die;
        }
    }

    static function verify(){
        if (!self::$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            self::$server->getResponse()->send();
            die;
        }
        $token = self::$server->getAccessTokenData(OAuth2\Request::createFromGlobals());
        return array("user_id"=>$token["user_id"], "client_id"=>$token["client_id"]);
    }
}

OAuth::init();