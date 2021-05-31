import 'dart:convert';
import 'package:nilink/util/DataProvider.dart';
import 'package:shared_preferences/shared_preferences.dart';


class Auth{
  static SharedPreferences storage;
  static String token;
  static Future<bool> init() async {
    storage = await SharedPreferences.getInstance();
    if(storage.getString("refresh_token")==null) return false;
    Map<String,dynamic> r = await refreshToken();
    return !r["error"];
  }

  static Map<String,String> endpoints = <String,String>{
    "login":"/api/user/login",
    "loginGoogle":"/api/user/login/google",
    "loginToken":"/api/user/login/token",
    "signup":"/api/user/signup",
    "signupGoogle":"/api/user/signup/google",
    "devSignup":"/api/developer/signup"
  };

//API
  static Future<Map<String,dynamic>> signup(String firstname, String surname, String mail, String nickname, String password) async {
    String body=jsonEncode(<String, String>{
      "firstname": firstname,
      "surname": surname,
      "mail": mail,
      "password": password,
      "nickname": nickname
    });
    Map<String,dynamic> data = await DataProvider.request(endpoints["signup"], body, null);
    return data;
  }

  static Future<Map<String,dynamic>> login(String mail, String password) async {
    String body=jsonEncode(<String, String>{
      'mail': mail,
      'password': password
    });
    Map<String,dynamic> data = await DataProvider.request(endpoints["login"], body, null);
    
    if(data["status"]==200){
      token= data["result"]["token"];
      storage.setString("token", data["result"]["token"]);
      storage.setString("refresh_token", data["result"]["refresh_token"]);
    }
    return data;
  }


//Google
  static Future<Map<String,dynamic>> signupWithGoogle(String idToken) async {
    var body = {'idtoken': idToken};
    Map<String,dynamic> data = await DataProvider.request(endpoints["signupGoogle"], body, null);
    return data;
  }

  static Future<Map<String,dynamic>> loginWithGoogle(String idToken) async {
    var body = {'idtoken': idToken};
    
    Map<String,dynamic> data = await DataProvider.request(endpoints["loginGoogle"], body, null);
    if(data["status"]==200){
      token= data["result"]["token"];
      storage.setString("token", data["result"]["token"]);
      storage.setString("refresh_token", data["result"]["refresh_token"]);
    }
    return data;
  }


//Refresh
  static Future<Map<String,dynamic>> refreshToken() async {
    String refreshToken = storage.getString("refresh_token");
    var body={"refresh_token": refreshToken};
    Map<String,dynamic> data = await DataProvider.request(endpoints["loginToken"], body, null);
    
    if(data["status"]==200){
      token= data["result"]["token"];
      storage.setString("token", data["result"]["token"]);
      storage.setString("refresh_token", data["result"]["refresh_token"]);
    }
    return data;
  }

  static void deleteToken(){
    storage.remove("token");
    storage.remove("refresh_token");
    token=null;
  }

  static Future<Map<String,dynamic>> becomeDev(String devName) async {
    String body=jsonEncode(<String, String>{
      "developName" : devName
    });
    Map<String,dynamic> data = await DataProvider.request(endpoints["devSignup"], body, storage.getString("token"));
    return data;
  }


}