<?php

Route::add(LOBBY.'/create', function() {
    try{
        $result=OAuth::verify();
        $a=Application::fromClientId($result["client_id"]);
        $msg=$a->createLobby();
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");



Route::add(LOBBY.ONLY_NUMBERS.'/send', function($id) {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $msg=$l->send($u);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "GET");

Route::add(LOBBY.ONLY_NUMBERS.'/listen', function($id) {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $data=$l->listen($u);
        return json_encode(array("messages"=>$data));
    }catch(Exception $e){
        return handleException($e);
    }
}, "GET");



Route::add(LOBBY.ONLY_NUMBERS.'/members', function($id) {
    try{
        $result = OAuth::verify();
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $data=$l->getMembers();
        return json_encode(array("message"=>"Successfully retrived lobby's member","data"=>$data));
    }catch(Exception $e){
        return handleException($e);
    }
}, "GET");

Route::add(LOBBY.ONLY_NUMBERS.'/join', function($id) {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $msg=$l->join($u);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");

Route::add(LOBBY.ONLY_NUMBERS.'/quit', function($id) {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $msg=$l->quit($u);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");




Route::add(LOBBY.ONLY_NUMBERS.'/data', function($id) {
    try{
        $result = OAuth::verify();
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $data=$l->getData();
        return json_encode(array("message"=>"Successfully retrived app data","data"=>$data));
    }catch(Exception $e){
        return handleException($e);
    }
}, "GET");

Route::add(LOBBY.ONLY_NUMBERS.'/userdata', function($id) {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $data=$l->getUserData($u);
		return json_encode(array("message"=>"Successfully retrived user data","data"=>$data));
    }catch(Exception $e){
        return handleException($e);
    }
}, "GET");

Route::add(LOBBY.ONLY_NUMBERS.'/data', function($id) {
    try{
        $result = OAuth::verify();
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $msg=$l->setData();
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");

Route::add(LOBBY.ONLY_NUMBERS.'/userdata', function($id) {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        $l=Lobby::fromId($id, $a->id);
        $msg=$l->setUserData($u);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");