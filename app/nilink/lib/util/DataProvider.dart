import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/http.dart';
import 'package:nilink/main.dart';
import 'package:nilink/util/Auth.dart';

class DataProvider {
  static String url = "https:/nilink.cf";

  static Map<String,String> endpoints = <String,String>{
    "applications":"/api/user/applications",
    "application":"/api/user/application/{id}",
  };

  static Future<Map<String, dynamic>> request(
    String endpoint, dynamic params, String token) async {
    Response r = await http.post(Uri.parse(url + endpoint), body: params, headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });
    bool error = r.statusCode != 200;
    dynamic result = jsonDecode(r.body);
    int statusCode = r.statusCode;
    if (r.statusCode == 401 && token!=null) {
      dynamic result = await Auth.refreshToken();
      if (!result["error"]) {
        return await request(endpoint, params, Auth.storage.getString("token"));
      } else {
        Auth.deleteToken();
        Navigator.pushNamedAndRemoveUntil(navigatorKey.currentContext, '/login', (route) => false);
      }
    }
    return <String, dynamic>{
      "error": error,
      "result": result,
      "status": statusCode
    };
  }


static Future<Map<String, dynamic>> getRequest(
    String endpoint, String token) async {
    Response r = await http.get(Uri.parse(url + endpoint), headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });
    bool error = r.statusCode != 200;
    dynamic result = jsonDecode(r.body);
    int statusCode = r.statusCode;
    if (r.statusCode == 401) {
      dynamic result = await Auth.refreshToken();
      if (!result["error"]) {
        return await getRequest(endpoint, Auth.storage.getString("token"));
      } else {
        Auth.deleteToken();
        Navigator.pushNamedAndRemoveUntil(navigatorKey.currentContext, '/login', (route) => false);
      }
    }
    return <String, dynamic>{
      "error": error,
      "result": result,
      "status": statusCode
    };
  }

  static Future<Map<String,dynamic>> getApplications() async {
    return await DataProvider.getRequest(endpoints["applications"], Auth.token);
  }

  static Future<Map<String,dynamic>> getApplication(String id) async {
    return await DataProvider.getRequest(endpoints["application"].replaceAll("{id}", id), Auth.token);
  }
}
