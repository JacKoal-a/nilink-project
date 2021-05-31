<?php
#region [application]
Route::add(APPLICATION, function() {
    OAuth::verify();
}, "GET");

Route::add(APPLICATION.'/userinfo', function() {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        echo json_encode(array("name"=>$u->firstname, "surname"=>$u->surname, "mail"=>$u->mail));
    }catch(Exception $e){
        return handleException($e);
    }
}, "GET");


Route::add(APPLICATION.'/data', function() {
    try{
        $result = OAuth::verify();
        $a=Application::fromClientId($result["client_id"]);
        $data=$a->getData();
        return json_encode(array("message"=>"Successfully retrived app data","data"=>$data));
    }catch(Exception $e){
        return handleException($e);
    }
}, "GET");

Route::add(APPLICATION.'/userdata', function() {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        $data=$a->getUserData($u);
		return json_encode(array("message"=>"Successfully retrived user data","data"=>$data));
    }catch(Exception $e){
        return handleException($e);
    }
}, "GET");



Route::add(APPLICATION.'/data', function() {
    try{
        $result = OAuth::verify();
        $a=Application::fromClientId($result["client_id"]);
        $msg=$a->setData();
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");

Route::add(APPLICATION.'/userdata', function() {
    try{
        $result = OAuth::verify();
        $u=User::fromID($result["user_id"]);
        $a=Application::fromClientId($result["client_id"]);
        $msg=$a->setUserData($u);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");
  
  

#endregion