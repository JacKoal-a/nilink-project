import 'dart:io' show Platform;

import 'package:flutter/cupertino.dart';

class Define{
  static bool isMobile(BuildContext context){
    try{
        return  Platform.isIOS || Platform.isAndroid || MediaQuery.of(context).size.width < 600 ;
    } catch(e){
        return MediaQuery.of(context).size.width < 600;
    }
  }  
}