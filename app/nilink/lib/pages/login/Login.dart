import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:nilink/pages/login/components/LoginForm.dart';
import 'package:nilink/util/Auth.dart';
import 'package:nilink/widgets/CustomSnackBar.dart';
import 'package:nilink/widgets/Or.dart';

class Login extends StatefulWidget {
  @override
  _LoginState createState() => _LoginState();
}

class _LoginState extends State<Login> {
  final GoogleSignIn _googleSignIn = GoogleSignIn();
  bool loading = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Center(
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
                  child: Image.asset("assets/nilink-500.png", width: 150),
                ),
                SizedBox(height: 20),
                Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: !loading?LoginForm():CircularProgressIndicator(strokeWidth:2 )
                ),
                TextButton(
                    onPressed: () => Navigator.pushNamed(context, '/signup'),
                    child: Text("Don't have an Account? Sign Up",
                        style: TextStyle(color: Colors.grey))),
                SizedBox(height: 20),
                !loading ? Or() : Container(),
                SizedBox(height: 20),
                !loading
                    ? OutlinedButton(
                        onPressed: (){_googleLogin(context);},
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Image.asset(
                            "assets/google_logo.png",
                            width: 32,
                          ),
                        ),
                        style: ButtonStyle(
                            shape: MaterialStateProperty.all(CircleBorder())),
                      )
                    : Container(),
                SizedBox(height: 20),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _googleLogin(BuildContext context) async {
    _startLoading();
    if (await _googleSignIn.isSignedIn()) {
      _googleSignIn.disconnect();
    }
    try {
      _googleSignIn.signIn().then((result) {
        result.authentication.then((googleKey) {
          Auth.loginWithGoogle(googleKey.idToken).then((result) {
            print("Ho fatto cose");
            _stopLoading();
            if (!result["error"]) {
              print("vado alla home");
              Navigator.pushNamedAndRemoveUntil(
                  context, '/home', (route) => false);
            } else {
              print(result);
              if (result["status"] == 401) {
                CustomSnackBar(
                    context,
                    Text(
                        "You are not signed up, please sign up before Log In"));
              } else {
                CustomSnackBar(
                    context, Text("Server internal error, try later"));
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
    });
  }

  _stopLoading() {
    setState(() {
      loading = false;
    });
  }
}
