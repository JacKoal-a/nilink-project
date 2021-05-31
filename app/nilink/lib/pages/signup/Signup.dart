import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:nilink/pages/signup/components/SignupForm.dart';
import 'package:nilink/util/Auth.dart';
import 'package:nilink/widgets/CustomSnackBar.dart';
import 'package:nilink/widgets/Or.dart';

class Signup extends StatefulWidget {
  @override
  _SignupState createState() => _SignupState();
}

class _SignupState extends State<Signup> {
  final GoogleSignIn _googleSignIn = GoogleSignIn();
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();

  void initState() {
    super.initState();
  }

  bool loading = false;
  bool hide = false;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      key: _scaffoldKey,
      body:  Center(
          child: Container(
            height: MediaQuery.of(context).size.height > 900
                ? 800
                : MediaQuery.of(context).size.height,
            width: MediaQuery.of(context).size.width > 600
                ? 500
                : MediaQuery.of(context).size.width,
            decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(10),
                boxShadow: [
                  BoxShadow(
                      color: Colors.grey,
                      offset: Offset.fromDirection(1, 2),
                      blurRadius: 10)
                ]),
            child: SingleChildScrollView(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Padding(
                    padding: const EdgeInsets.fromLTRB(16, 32, 16, 16),
                    child: Image.asset("assets/nilink-500.png", width: 100),
                  ),
                  Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: !loading
                          ? SignupForm(() {
                              setState(() {
                                hide = !hide;
                              });
                            })
                          : CircularProgressIndicator(strokeWidth: 2)),
                  TextButton(
                      onPressed: () => Navigator.pushNamed(context, '/login'),
                      child: Text("Already have an Account? Log In",
                          style: TextStyle(color: Colors.grey))),
                  SizedBox(height: 20),
                  !hide ? Or() : Container(),
                  SizedBox(height: 20),
                  !hide
                      ? SizedBox(
                          width: 220,
                          child: OutlinedButton(
                            onPressed: _googleSignup,
                            child: Padding(
                              padding: const EdgeInsets.all(8.0),
                              child: Wrap(
                                  crossAxisAlignment: WrapCrossAlignment.center,
                                  children: [
                                    Image.asset(
                                      "assets/google_logo.png",
                                      width: 32,
                                    ),
                                    SizedBox(
                                      width: 10,
                                    ),
                                    Text("Sign Up with Google")
                                  ]),
                            ),
                          ))
                      : Container(),
                  SizedBox(height: 20),
                ],
              ),
            ),
          ),
        ),
      
    );
  }

  Future _googleSignup() async {
    _startLoading();
    if (await _googleSignIn.isSignedIn()) {
      _googleSignIn.disconnect();
    }
    try {
      _googleSignIn.signIn().then((result) {
        result.authentication.then((googleKey) {
          Auth.signupWithGoogle(googleKey.idToken).then((result) {
            _stopLoading();
            if (!result["error"]) {
              Auth.signupWithGoogle(googleKey.idToken).then((value) => Navigator.pushNamed(_scaffoldKey.currentContext, "/home"));
              ;
            } else {
              _stopLoading();
              if (result["status"] == 409) {
                CustomSnackBar(_scaffoldKey.currentContext,
                    Text("Already signed up with this account, please Log In"));
              } else {
                CustomSnackBar(
                    _scaffoldKey.currentContext, Text("Server internal error, try later"));
              }
              
            }
          }).catchError((onError) {
            _stopLoading();
          });
        }).catchError((onError) {
          _stopLoading();
        });
      }).catchError((onError) {
        _stopLoading();
      });
    } on PlatformException catch (_) { 
      _stopLoading();
    } catch (err) {
      _stopLoading();
    }
  }

  _startLoading() {
    setState(() {
      loading = true;
      hide = true;
    });
  }

  _stopLoading() {
    setState(() {
      loading = false;
      hide = false;
    });
  }
}
