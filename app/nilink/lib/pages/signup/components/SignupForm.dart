import 'package:flutter/material.dart';
import 'package:nilink/util/Auth.dart';

class SignupForm extends StatefulWidget {
  final Function hide;
  const SignupForm(this.hide);
  @override
  _SignupFormState createState() => _SignupFormState();
}

class _SignupFormState extends State<SignupForm> {
  bool next = false;
  String name = "",
      surname = "",
      mail = "",
      nickname = "",
      password = "",
      confpass = "";
  Key secondKey = ValueKey(2);

  @override
  Widget build(BuildContext context) {
    if (!next) {
      return AnimatedSwitcher(
        duration: Duration(milliseconds: 300),
        transitionBuilder: _slideTransition,
        child: FirstSignupForm((name, surname, mail) {
          setState(() {
            next = true;
            this.name = name;
            this.surname = surname;
            this.mail = mail;
            widget.hide();
          });
        }, name, surname, mail),
      );
    } else {
      return AnimatedSwitcher(
        duration: Duration(milliseconds: 300),
        child: SecondSignupForm(secondKey, (nickname, password, confpass) {
          setState(() {
            next = false;
            this.nickname = nickname;
            this.password = password;
            this.confpass = confpass;
            widget.hide();
          });
        }, name, surname, mail, nickname, password, confpass),
        transitionBuilder: _slideTransition,
      );
    }
  }

  Widget _slideTransition(Widget child, Animation<double> animation) {
    final inAnimation =
        Tween<Offset>(begin: Offset(1.0, 0.0), end: Offset(0.0, 0.0))
            .animate(animation);
    final outAnimation =
        Tween<Offset>(begin: Offset(-1.0, 0.0), end: Offset(0.0, 0.0))
            .animate(animation);

    if (child.key == ValueKey(2)) {
      return ClipRect(
        child: SlideTransition(
          position: inAnimation,
          child: Padding(
            padding: const EdgeInsets.all(8.0),
            child: child,
          ),
        ),
      );
    } else {
      return ClipRect(
        child: SlideTransition(
          position: outAnimation,
          child: Padding(
            padding: const EdgeInsets.all(8.0),
            child: child,
          ),
        ),
      );
    }
  }
}

class FirstSignupForm extends StatefulWidget {
  final Function next;
  final String name, surname, mail;
  FirstSignupForm(this.next, this.name, this.surname, this.mail);
  @override
  _FirstSignupFormState createState() => _FirstSignupFormState();
}

class _FirstSignupFormState extends State<FirstSignupForm> {
  final _formKey = GlobalKey<FormState>();
  TextEditingController _nameController = new TextEditingController();
  TextEditingController _surnameController = new TextEditingController();
  TextEditingController _mailController = new TextEditingController();

  @override
  void initState() {
    super.initState();
    _nameController.text = widget.name;
    _surnameController.text = widget.surname;
    _mailController.text = widget.mail;
  }

  @override
  Widget build(BuildContext context) {
    return Form(
        key: _formKey,
        child: Column(children: <Widget>[
          TextFormField(
              controller: _nameController,
              validator: _validateName,
              decoration: InputDecoration(
                  border: OutlineInputBorder(), labelText: "Firstname")),
          SizedBox(
            height: 20,
          ),
          TextFormField(
              controller: _surnameController,
              validator: _validateSurname,
              decoration: InputDecoration(
                  border: OutlineInputBorder(), labelText: "Surname")),
          SizedBox(
            height: 20,
          ),
          TextFormField(
              controller: _mailController,
              keyboardType: TextInputType.emailAddress,
              validator: _validateMail,
              decoration: InputDecoration(
                  border: OutlineInputBorder(), labelText: "Mail")),
          
          SizedBox(height: 10),
          Text(
            "",
            style: TextStyle(color: Theme.of(context).errorColor),
          ),
          SizedBox(height: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              TextButton(
                  onPressed: () {
                    if (_formKey.currentState.validate()) {
                      widget.next(_nameController.text, _surnameController.text,
                          _mailController.text);
                    }
                  },
                  child: Padding(
                    padding: const EdgeInsets.all(4.0),
                    child: Icon(Icons.arrow_forward_rounded),
                  ),
                  style: TextButton.styleFrom(
                    shadowColor: Color.fromARGB(0, 0, 0, 0),
                    padding: EdgeInsets.all(8),
                    primary: Colors.teal,
                    side: BorderSide(color: Colors.teal, width: 2),
                  ))
            ],
          ),
        ]));
  }

  String _validateName(value) {
    if (value == null || value.isEmpty) {
      return 'Please enter a valid name';
    }

    return null;
  }

  String _validateSurname(value) {
    if (value == null || value.isEmpty) {
      return 'Please enter a valid surname';
    }

    return null;
  }

  String _validateMail(value) {
    if (value == null ||
        value.isEmpty ||
        !RegExp(r"^[a-zA-Z0-9.a-zA-Z0-9.!#$%&'*+-/=?^_`{|}~]+@[a-zA-Z0-9]+\.[a-zA-Z]+")
            .hasMatch(value)) {
      return 'Please enter a valid mail address';
    }

    return null;
  }
}

class SecondSignupForm extends StatefulWidget {
  final String name, surname, mail, nickname, password, confpass;
  final Function back;
  const SecondSignupForm(Key key, this.back, this.name, this.surname, this.mail,
      this.nickname, this.password, this.confpass)
      : super(key: key);

  @override
  _SecondSignupFormState createState() => _SecondSignupFormState();
}

class _SecondSignupFormState extends State<SecondSignupForm> {
  final _formKey = GlobalKey<FormState>();
  TextEditingController _nickController = new TextEditingController();
  TextEditingController _passController = new TextEditingController();
  TextEditingController _confpassController = new TextEditingController();
  String error = "";

  @override
  void initState() {
    super.initState();
    _nickController.text = widget.nickname;
    _passController.text = widget.password;
    _confpassController.text = widget.confpass;
  }

  @override
  Widget build(BuildContext context) {
    return Form(
        key: _formKey,
        child: Column(children: <Widget>[
          TextFormField(
            controller: _nickController,
            validator: _validateNick,
            decoration: InputDecoration(
                border: OutlineInputBorder(), labelText: "Nickname"),
          ),
          SizedBox(height: 20),
          TextFormField(
            obscureText: true,
            controller: _passController,
            validator: _validatePassword,
            decoration: InputDecoration(
                border: OutlineInputBorder(), labelText: "Password"),
          ),
          SizedBox(height: 20),
          TextFormField(
            obscureText: true,
            controller: _confpassController,
            validator: _validateConfPassword,
            decoration: InputDecoration(
                border: OutlineInputBorder(), labelText: "Confirm Password"),
          ),
          SizedBox(height: 10),
          Text(
            error,
            style: TextStyle(color: Theme.of(context).errorColor),
          ),
          SizedBox(height: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              TextButton(
                  onPressed: () => widget.back(_nickController.text,
                      _passController.text, _confpassController.text),
                  child: Padding(
                    padding: const EdgeInsets.all(4.0),
                    child: Icon(Icons.arrow_back_rounded),
                  ),
                  style: TextButton.styleFrom(
                    shadowColor: Color.fromARGB(0, 0, 0, 0),
                    padding: EdgeInsets.all(8),
                    primary: Colors.teal,
                    side: BorderSide(color: Colors.teal, width: 2),
                  )),
              TextButton(
                  onPressed: () {
                    setState(() {
                      error = "";
                    });
                    if (_formKey.currentState.validate()) {
                      Auth.signup(
                        widget.name, widget.surname, widget.mail,
                        _nickController.text, _passController.text
                      ).then((result) {
                        print(result);
                        if (!result["error"]) {
                          Auth.login(widget.mail, _passController.text)
                              .then((value) {
                            if (!value["error"]) {
                              Navigator.pushNamed(context, '/home');
                            }
                          });
                        } else {
                          setState(() {
                            error = result["result"]["error_description"];
                          });
                        }
                      });
                    }
                  },
                  child: Padding(
                    padding: const EdgeInsets.all(4.0),
                    child: Text("Signup"),
                  ),
                  style: TextButton.styleFrom(
                    shadowColor: Color.fromARGB(0, 0, 0, 0),
                    padding: EdgeInsets.all(12),
                    primary: Colors.teal,
                    side: BorderSide(color: Colors.teal, width: 2),
                  )),
            ],
          ),
        ]));
  }

  String _validateNick(value) {
    if (value == null || value.isEmpty) {
      return 'Please enter a valid nickname';
    }

    return null;
  }

  String _validatePassword(value) {
    if (value == null || value.isEmpty) {
      return 'Please enter a valid password';
    }
    if (value.toString().length < 8) {
      return 'Please enter a password at least 8 charachters long';
    }

    return null;
  }

  String _validateConfPassword(value) {
    if (value == null || value.isEmpty) {
      return 'Please enter a valid password';
    }
    if (value.toString().length < 8) {
      return 'Please enter a password at least 8 charachters long';
    }
    if (value.toString().compareTo(this._passController.text) != 0) {
      return 'Password doesn\'t match with the previous';
    }

    return null;
  }
}
