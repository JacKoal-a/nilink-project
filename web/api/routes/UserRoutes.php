<?php

#region [user auth]

Route::add(USER.'/signup', function() {
	$u = new User();
	try{
		$msg=$u->create();
		http_response_code(200);
		return json_encode(array("message"=>$msg));

	}catch(Exception $e){
		return handleException($e);
	}
}, "POST");

Route::add(USER.'/signup/google', function() {
	$u = new User();
	try{
		$msg=$u->createFromGoogle();
		http_response_code(200);
		return json_encode(array("message"=>$msg));

	}catch(Exception $e){
		return handleException($e);
	}
}, "POST");

Route::add(USER.'/login', function() {
	try{
		$u = User::fromCredential();
		$jwt = UserAuth::issueJWT($u);
		$refresh_token = UserAuth::issueRefreshToken($u);
		http_response_code(200);
		return json_encode(array("message"=>"Successfully logged in","token"=>$jwt,"refresh_token"=>$refresh_token));

	}catch(Exception $e){
		return handleException($e);
	}
}, "POST");

Route::add(USER.'/login/google', function() {
	try{
		$u = User::fromGoogle();
		$jwt = UserAuth::issueJWT($u);
		$refresh_token = UserAuth::issueRefreshToken($u);
		http_response_code(200);
		return json_encode(array("message"=>"Successfully logged in","token"=>$jwt,"refresh_token"=>$refresh_token));

	}catch(Exception $e){
		return handleException($e);
	}
}, "POST");

Route::add(USER.'/login/token', function() {
	$refresh_token=isset($_POST["refresh_token"])?$_POST["refresh_token"]:false;
	try{
		$u = UserAuth::verifyRefreshToken($refresh_token);
		$jwt = UserAuth::issueJWT($u);
		$refresh_token = UserAuth::issueRefreshToken($u);
		http_response_code(200);
		return json_encode(array("message"=>"Successfully logged in","token"=>$jwt,"refresh_token"=>$refresh_token));

	}catch(Exception $e){
		return handleException($e);
	}
}, "POST");

#endregion
  
#region [user applications]

//all user application
Route::add(USER.'/myapps', function() {
	try{
		$result = UserAuth::verifyJWT(getToken());
		$u = User::fromID($result->data->id);
		$data=$u->getMyApps();
		return json_encode(array("message"=>"Successfully retrived data","data"=>$data));

	}catch(Exception $e){
		return handleException($e);
	}
}, "GET");

//data of a specific application
Route::add(USER.'/myapp'.ONLY_NUMBERS, function($idApplication) {
	try{
		$result = UserAuth::verifyJWT(getToken());
		$u = User::fromID($result->data->id);
		$data=$u->getMyApp($idApplication);
		return json_encode(array("message"=>"Successfully retrived data","data"=>$data));

	}catch(Exception $e){
		return handleException($e);
	}
}, "GET");

//all user application
Route::add(USER.'/applications', function() {
	try{
		$result = UserAuth::verifyJWT(getToken());
		$u = User::fromID($result->data->id);
		$data=$u->getApplications();
		return json_encode(array("message"=>"Successfully retrived data","data"=>$data));

	}catch(Exception $e){
		return handleException($e);
	}
}, "GET");

//data of a specific application
Route::add(USER.'/application'.ONLY_NUMBERS, function($idApplication) {
	try{
		$result = UserAuth::verifyJWT(getToken());
		$u = User::fromID($result->data->id);
		$data=$u->getApplication($idApplication);
		return json_encode(array("message"=>"Successfully retrived data","data"=>$data));

	}catch(Exception $e){
		return handleException($e);
	}
}, "GET");


//signup in a new application 
Route::add(USER.'/application'.ONLY_NUMBERS.'/signup', function($idApplication) {
	try{
		$result = UserAuth::verifyJWT(getToken());
		$u = User::fromID($result->data->id);
		$msg=$u->signupApplication($idApplication);
		return json_encode(array("message"=>$msg));

	}catch(Exception $e){
		return handleException($e);
	}
}, "POST");


//delete all user's data of an application
Route::add(USER.'/myapp'.ONLY_NUMBERS.'/delete', function($idApplication) {
	try{
		$result = UserAuth::verifyJWT(getToken());
		$u = User::fromID($result->data->id);
		$msg=$u->deleteData($idApplication);
		return json_encode(array("message"=>$msg));

	}catch(Exception $e){
		return handleException($e);
	}
}, "DELETE");
#endregion