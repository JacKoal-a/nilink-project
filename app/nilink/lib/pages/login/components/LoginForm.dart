import 'package:flutter/material.dart';
import 'package:nilink/util/Auth.dart';
import 'package:nilink/widgets/CustomSnackBar.dart';

class LoginForm extends StatefulWidget {
  @override
  _LoginFormState createState() => _LoginFormState();
}

class _LoginFormState extends State<LoginForm> {
  final _formKey = GlobalKey<FormState>();

  TextEditingController _mailController = new TextEditingController();
  TextEditingController _passController = new TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Form(
        key: _formKey,
        child: Column(children: <Widget>[
          TextFormField(
              controller: _mailController,
              validator: _validateMail,
              keyboardType: TextInputType.emailAddress,
              decoration: InputDecoration(
                  border: OutlineInputBorder(), labelText: "Mail")),
          SizedBox(
            height: 20,
          ),
          TextFormField(
            obscureText: true,
            controller: _passController,
            validator: _validatePassword,
            decoration: InputDecoration(
                border: OutlineInputBorder(), labelText: "Password"),
          ),
          SizedBox(height: 20),
          TextButton(
              onPressed: () {
                if (_formKey.currentState.validate()) {
                  Auth.login(_mailController.value.text,
                          _passController.value.text)
                      .then((result) {
                    if (!result["error"]) {
                      Navigator.pushNamedAndRemoveUntil(
                          context, '/home', (route) => false);
                    } else {
                      if (result["status"] == 401) {
                        CustomSnackBar(
                            _formKey.currentContext,
                            Text(
                                "You are not signed up, please sign up before Log In"));
                      } else {
                        CustomSnackBar(
                            _formKey.currentContext, Text("Server internal error, try later"));
                      }
                    }
                  });
                }
              },
              child: Padding(
                padding: const EdgeInsets.all(4.0),
                child: Text("Login"),
              ),
              style: TextButton.styleFrom(
                shadowColor: Color.fromARGB(0, 0, 0, 0),
                padding: EdgeInsets.all(12),
                primary: Colors.teal,
                side: BorderSide(color: Colors.teal, width: 2),
              ))
        ]));
  }

  String _validateMail(value) {
    if (value == null || value.isEmpty) {
      return 'Please enter some text';
    }

    return null;
  }

  String _validatePassword(value) {
    if (value == null || value.isEmpty) {
      return 'Please enter some text';
    }

    return null;
  }
}
