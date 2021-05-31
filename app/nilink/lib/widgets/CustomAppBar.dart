import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:nilink/util/Define.dart';

class CustomAppBar extends StatelessWidget with PreferredSizeWidget {
  final String title;
  final Widget child;
  final Function onPressed;
  final Function onTitleTapped;

  @override
  final Size preferredSize;
  CustomAppBar({this.title,this.child, this.onPressed,this.onTitleTapped}) : preferredSize = Size.fromHeight(60.0);

  final ShapeBorder kBackButtonShape = RoundedRectangleBorder(
  borderRadius: BorderRadius.only(
      bottomRight: Radius.circular(20)
    ),
  );

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: 
          Row(
            mainAxisSize: MainAxisSize.max,
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: Define.isMobile(context)|| title==null ? MainAxisAlignment.spaceBetween : MainAxisAlignment.center,
            children: <Widget>[
              Define.isMobile(context) || title==null?
              Hero(
                tag: 'topBarBtn',
                child: Container(
                  child: Card(
                    margin:EdgeInsets.zero,
                    elevation: 10,
                    shape: kBackButtonShape,
                    child: MaterialButton(
                      height: 50,
                      minWidth: 50,
                      elevation: 10,
                      shape: kBackButtonShape,
                      onPressed: onPressed,
                      child: child,
                    ),
                  ),
                ),
              ):Container(),
              
              title!=null?
              Hero(
                tag: 'title',
                transitionOnUserGestures: true,
                child: Card(
                  margin:EdgeInsets.zero,
                  color: Theme.of(context).primaryColor,
                  elevation: 10,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.only(
                      bottomLeft: Radius.circular(20),
                      bottomRight:  Define.isMobile(context) ? Radius.circular(0): Radius.circular(20)
                    ),
                  ),
                  child: InkWell(
                    onTap: onTitleTapped,
                    child: Container(
                      width: Define.isMobile(context) ? MediaQuery.of(context).size.width / 1.5 : MediaQuery.of(context).size.width / 2,
                      height: 50,
                      child: Align(
                        alignment: Define.isMobile(context) ? Alignment.centerLeft : Alignment.center,
                        child: Padding(
                          padding: Define.isMobile(context) ? EdgeInsets.only(left: 20, ) : EdgeInsets.only( ),
                          child: Text(
                            title,
                            style: GoogleFonts.roboto(
                              color: Colors.white,
                              fontSize: 24,
                            )
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
              ):Container(),
            ],
          ),
        
    );
  }

}
