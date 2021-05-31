<?php

#region [developer auth]
Route::add(DEVELOPER.'/signup', function() {
    try{
        $result = UserAuth::verifyJWT(getToken());
        $u = User::fromID($result->data->id);
        $d = new Developer();
        
        $msg=$d->create($u->id);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");

Route::add(DEVELOPER.'/login', function() {
    try{
        $d = Developer::fromCredential();
        $jwt = DeveloperAuth::issueJWT($d);
        $refresh_token = DeveloperAuth::issueRefreshToken($d);
		http_response_code(200);
		return json_encode(array("message"=>"Successfully logged in","token"=>$jwt,"refresh_token"=>$refresh_token));
  
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");

Route::add(DEVELOPER.'/login/google', function() {
	try{
		$d = Developer::fromGoogle();
		$jwt = DeveloperAuth::issueJWT($d);
		$refresh_token = DeveloperAuth::issueRefreshToken($d);
		http_response_code(200);
		return json_encode(array("message"=>"Successfully logged in","token"=>$jwt,"refresh_token"=>$refresh_token));

	}catch(Exception $e){
		return handleException($e);
	}
}, "POST");

Route::add(DEVELOPER.'/login/token', function() {
	$refresh_token=isset($_POST["refresh_token"])?$_POST["refresh_token"]:false;
	try{
		$d = DeveloperAuth::verifyRefreshToken($refresh_token);
		$jwt = DeveloperAuth::issueJWT($d);
		$refresh_token = DeveloperAuth::issueRefreshToken($d);
		http_response_code(200);
		return json_encode(array("message"=>"Successfully logged in","token"=>$jwt,"refresh_token"=>$refresh_token));

	}catch(Exception $e){
		return handleException($e);
	}
}, "POST");
#endregion

Route::add(DEVELOPER.'/applications', function() {
    try{
        $result = DeveloperAuth::verifyJWT(getToken());
        $d = Developer::fromID($result->data->id);
        $data = $d->getApplications();
        return json_encode(array("message"=>"Successfully retrived data","data"=>$data));
  
    }catch(Exception $e){
        return handleException($e);
    }
    
}, "GET");

Route::add(DEVELOPER.'/application'.ONLY_NUMBERS, function($idApplication) {
    try{
        $result = DeveloperAuth::verifyJWT(getToken());
        $d = Developer::fromID($result->data->id);
        $data = $d->getApplication($idApplication);
        return json_encode(array("message"=>"Successfully retrived data","data"=>$data));
  
    }catch(Exception $e){
        return handleException($e);
    }
    
}, "GET");

Route::add(DEVELOPER.'/application/create', function() {
    try{
        $result = DeveloperAuth::verifyJWT(getToken());
        $d = Developer::fromID($result->data->id);
        $msg = $d->createApplication();
        return json_encode(array("message"=>$msg));
  
    }catch(Exception $e){
        return handleException($e);
    }
    
}, "POST");

Route::add(DEVELOPER.'/application'.ONLY_NUMBERS.'/delete', function($idApplication) {
    try{
        $result = DeveloperAuth::verifyJWT(getToken());
        $d = Developer::fromID($result->data->id);
        $msg = $d->deleteApplication($idApplication);
        return json_encode(array("message"=>$msg));
  
    }catch(Exception $e){
        return handleException($e);
    }
    
}, "DELETE");

Route::add(DEVELOPER.'/application'.ONLY_NUMBERS.'/update', function($idApplication) {
    try{
        $result = DeveloperAuth::verifyJWT(getToken());
        $d = Developer::fromID($result->data->id);
        $msg = $d->updateApplication($idApplication);
        return json_encode(array("message"=>$msg));
  
    }catch(Exception $e){
        return handleException($e);
    }
    
}, "PATCH");

Route::add(DEVELOPER.'/application'.ONLY_NUMBERS.'/upload/screenshot', function($idApplication) {
    try{
        $result = DeveloperAuth::verifyJWT(getToken());
        $d = Developer::fromID($result->data->id);
        $msg = $d->uploadScreenshot($idApplication);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
    
}, "POST");

Route::add(DEVELOPER.'/application'.ONLY_NUMBERS.'/upload/icon', function($idApplication) {
    try{
        $result = DeveloperAuth::verifyJWT(getToken());
        $d = Developer::fromID($result->data->id);
        $msg = $d->uploadIcon($idApplication);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "POST");


Route::add(DEVELOPER.'/application'.ONLY_NUMBERS.'/screenshot'.ALL.'/delete', function($idApplication, $idScreen) {
    try{
        $result = DeveloperAuth::verifyJWT(getToken());
        $d = Developer::fromID($result->data->id);
        $msg = $d->deleteScreen($idApplication, $idScreen);
        return json_encode(array("message"=>$msg));
    }catch(Exception $e){
        return handleException($e);
    }
}, "DELETE");