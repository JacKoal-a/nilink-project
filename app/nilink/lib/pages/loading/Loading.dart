import 'package:flutter/material.dart';
import 'package:nilink/util/Auth.dart';

class Loading extends StatefulWidget {
  @override
  _LoadingState createState() => _LoadingState();
}

class _LoadingState extends State<Loading> {
  String msg;

  @override
  void initState() {
    super.initState();
    _load();
  }

  _load()async{
    msg="Starting auth";
    await Future.delayed(Duration(seconds:1));
    Auth.init().then((value) {
      if(value){
        setState(() {
          msg="Session found";
        });
        Future.delayed(Duration(seconds: 1),(){
          Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false);
        });
      }else{
        setState(() {
          msg="Session not found";
        });
        Future.delayed(Duration(seconds: 1),(){
          Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 32, 16, 16),
            child: Image.asset("assets/nilink-500.png", width: 200),
          ),
          CircularProgressIndicator(strokeWidth: 2,),
          SizedBox(height:20),
          Text(msg)
        ],
      ),
    ));
  }
}
