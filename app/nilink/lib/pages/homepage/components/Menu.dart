import 'package:flutter/material.dart';
import 'package:nilink/util/Define.dart';

class Menu extends StatefulWidget {
  final GlobalKey<ScaffoldState> scaffoldKey;
  final int index;
  const Menu(this.scaffoldKey,this.index);
  @override
  _MenuState createState() => _MenuState();
}

class _MenuState extends State<Menu> {
  int activeIndex;

  @override
  void initState() {
    super.initState();
    activeIndex=widget.index;
  }

  @override
  Widget build(context) {
    return ListView(
      children: [
        Define.isMobile(context)
            ? DrawerHeader(
                child: Image.asset("assets/nilink-500.png", width: 50),
              )
            : Container(),
        Padding(
          padding: const EdgeInsets.all(8.0),
          child: TextButton.icon(
              style: _buttonStyle(1),
              onPressed: () {
                if(widget.scaffoldKey.currentState.hasDrawer) Navigator.of(context).pop();
                Navigator.pushNamed(context,'/home/myApps');
              },
              icon: Icon(Icons.star_rate_rounded),
              label: Padding(
                padding: const EdgeInsets.all(8.0),
                child: Text("My Apps"),
              )),
        ),
        Padding(
          padding: const EdgeInsets.all(8.0),
          child: TextButton.icon(
              style: _buttonStyle(2),
              onPressed: () {
                if(widget.scaffoldKey.currentState.hasDrawer) Navigator.of(context).pop();
                Navigator.pushNamed(context,'/home/applications');
              },
              icon: Icon(Icons.library_books_rounded),
              label: Padding(
                padding: const EdgeInsets.all(8.0),
                child: Text("Applications"),
              )),
        ),
        Padding(
          padding: const EdgeInsets.all(8.0),
          child: TextButton.icon(
              style: _buttonStyle(3),
              onPressed: () {
                if(widget.scaffoldKey.currentState.hasDrawer) Navigator.of(context).pop();
                Navigator.pushNamed(context,'/home/profile');
              },
              icon: Icon(Icons.person_outline_sharp),
              label: Padding(
                padding: const EdgeInsets.all(8.0),
                child: Text("Profile"),
              )),
        ),
        Padding(
          padding: const EdgeInsets.all(8.0),
          child: TextButton.icon(
              style: _buttonStyle(4),
              onPressed: () {
                if(widget.scaffoldKey.currentState.hasDrawer) Navigator.of(context).pop();
                Navigator.pushNamed(context,'/home/settings');
              },
              icon: Icon(Icons.settings),
              label: Padding(
                padding: const EdgeInsets.all(8.0),
                child: Text("Settings"),
              )),
        ),
      ],
    );
  }


  ButtonStyle _buttonStyle(int index){
    return ButtonStyle(
      alignment: Alignment.centerLeft,
    foregroundColor: MaterialStateProperty.all<Color>(
      index==activeIndex? Colors.teal : Colors.black
    ),
    shape: MaterialStateProperty.all<RoundedRectangleBorder>(
        RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(18.0),
    )));
  }

}