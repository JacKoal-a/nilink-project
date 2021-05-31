import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class Profile extends StatefulWidget {
  @override
  _ProfileState createState() => _ProfileState();
}

class _ProfileState extends State<Profile> {
  @override
  Widget build(BuildContext context) {
    return  Center(
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