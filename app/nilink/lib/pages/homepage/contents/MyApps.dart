import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class MyApps extends StatefulWidget {
  @override
  _MyAppsState createState() => _MyAppsState();
}

class _MyAppsState extends State<MyApps> {
  @override
  Widget build(BuildContext context) {
    return Center(
      child: SingleChildScrollView(
              child: Container(
          child:Column(
            mainAxisAlignment: MainAxisAlignment.start,
            children:[
              Text("Work In Progress", textAlign: TextAlign.center,
                style: GoogleFonts.ubuntu(
                  textStyle:TextStyle(fontSize: 32)
                )   ,
              ),
              Image.asset("assets/wip.png"),
              
            ]
          )
          
        ),
      ),
    );
  }
}