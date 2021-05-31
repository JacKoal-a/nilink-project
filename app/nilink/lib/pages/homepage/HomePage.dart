import 'package:flutter/material.dart';
import 'package:nilink/pages/homepage/components/Menu.dart';
import 'package:nilink/pages/homepage/contents/Applications.dart';
import 'package:nilink/pages/homepage/contents/MyApps.dart';
import 'package:nilink/pages/homepage/contents/Profile.dart';
import 'package:nilink/pages/homepage/contents/Settings.dart';
import 'package:nilink/util/Auth.dart';
import 'package:nilink/util/Define.dart';
import 'package:nilink/widgets/CustomAppBar.dart';

class HomePage extends StatefulWidget {
  final int initIndex;

  const HomePage({Key key, this.initIndex}) : super(key: key);
  @override
  _HomePageState createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();
  String title;
  int currentIndex;
  Widget content;

  @override
  void initState() {
    super.initState();
    currentIndex=widget.initIndex==null?2:widget.initIndex;
    content=_getPage(currentIndex);
    title=_getTitle(currentIndex);
  }

  @override
  Widget build(BuildContext context) {
    if(Auth.token==null) Navigator.pushNamedAndRemoveUntil(context, "/login", (route) => false);
    return Scaffold(
        key: _scaffoldKey,
        appBar: CustomAppBar(
            title: title,
            child: Icon(Icons.menu_rounded),
            onPressed: () => _scaffoldKey.currentState.openDrawer()),
        drawer: Define.isMobile(context)
            ? Drawer(
                child: Menu(_scaffoldKey, currentIndex)
              )
            : null,
        body: SafeArea(
            child: Center(
                child: Define.isMobile(context)
                    ? content
                    : Row(children: [
                        Container(width: 250.0, child: Menu(_scaffoldKey, currentIndex)),
                        VerticalDivider(),
                        Container(
                            width: MediaQuery.of(context).size.width - 300.0,
                            child: content)
                      ]))));
  }


  Widget _getPage(int index){
    switch(index){
      case 1: return MyApps();
      case 2: return Applications();
      case 3: return Profile();
      case 4: return Settings();
    }
    return null;
  }

    String _getTitle(int index){
    switch(index){
      case 1: return "My Apps";
      case 2: return "Applications";
      case 3: return "Profile";
      case 4: return "Settings";
    }
    return null;
  }



}