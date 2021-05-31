<?php

class RequestException extends Exception {}
class AuthException extends Exception {}
class ConflictException extends Exception {}
class NotFoundException extends Exception {}
class InternalException extends Exception {}

function handleException($e){
    $type = get_class($e);
    switch($type){
        case RequestException::class:
            $error="Bad Request";
            http_response_code(400);
            break;
        case AuthException::class:
            $error="Unauthorized";
            http_response_code(401);
            break;
        case NotFoundException::class:
            $error="Resource Not Found";
            http_response_code(404);
            break;
        case ConflictException::class:
            $error="Resource Conflict";
            http_response_code(409);
            break;
        case InternalException::class:
            $error="Internal Server Error";
            http_response_code(500);
            break;
    }
    return json_encode(array("error"=>$error,"error_description"=>$e->getMessage()));
}

function getToken() {
  $headers = getAuthorizationHeader();
  // HEADER: Get the access token from the header
  if (!empty($headers)) {
      if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
          return $matches[1];
      }
  }
  throw new AuthException("Invalid Access Token");
}

function getAuthorizationHeader(){
  $headers = null;
  if (isset($_SERVER['Authorization'])) {
      $headers = trim($_SERVER["Authorization"]);
  }
  else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
      $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
  } elseif (function_exists('apache_request_headers')) {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      //print_r($requestHeaders);
      if (isset($requestHeaders['Authorization'])) {
          $headers = trim($requestHeaders['Authorization']);
      }
  }
  return $headers;
}