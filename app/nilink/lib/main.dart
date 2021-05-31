import 'package:flutter/material.dart';
import 'package:nilink/pages/homepage/HomePage.dart';
import 'package:nilink/pages/loading/Loading.dart';
import 'package:nilink/pages/login/Login.dart';
import 'package:nilink/pages/signup/Signup.dart';

void main(){
  runApp(MyApp());
}

final GlobalKey<NavigatorState> navigatorKey = new GlobalKey<NavigatorState>();


class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Nilink',
      theme: ThemeData(
        primarySwatch: Colors.teal,
        visualDensity: VisualDensity.standard,
        errorColor: Colors.deepOrange
      ),
      debugShowCheckedModeBanner: false,
      initialRoute: '/loading',
      navigatorKey: key,

      routes: {
        '/login': (context) => Login(),
        '/signup': (context) => Signup(),
        '/loading': (context) => Loading(),

        '/home': (context) => HomePage(),
        '/home/myApps': (context) => HomePage(initIndex: 1,),
        '/home/applications': (context) => HomePage(initIndex: 2,),
        '/home/profile': (context) => HomePage(initIndex: 3,),
        '/home/settings': (context) => HomePage(initIndex: 4,),
      },
    );
  }
}