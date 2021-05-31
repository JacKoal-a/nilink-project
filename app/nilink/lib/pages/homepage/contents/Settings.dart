import 'package:flutter/material.dart';
import 'package:nilink/util/Auth.dart';
import 'package:nilink/widgets/CustomSnackBar.dart';
import 'package:nilink/widgets/SettingsTitle.dart';

class Settings extends StatefulWidget {
  @override
  _SettingsState createState() => _SettingsState();
}

class _SettingsState extends State<Settings> {
  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.start,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SettingsTitle("Developer"),
        Padding(
          padding: const EdgeInsets.all(16.0),
          child: ElevatedButton(
              onPressed: () {
                TextEditingController _controller = TextEditingController();
                showDialog(
                  context: context,
                  builder: (BuildContext context) {
                    return AlertDialog(
                      title: Text("Enter a develop name"),
                      content: TextFormField(
                        controller: _controller,
                      ),
                      actions: <Widget>[
                        TextButton(
                          child: Text("Cancel"),
                          onPressed: () {
                            Navigator.pop(context, null);
                          },
                        ),
                        TextButton(
                          child: Text("OK"),
                          onPressed: () {
                            _controller.text.length>0?
                            Navigator.pop(context, _controller.text):null;
                          },
                        ),
                      ],
                    );
                  },
                ).then((val) {
                  if (val != null)
                    Auth.becomeDev(val).then((result) {
                      if (result["status"] == 200) {
                        CustomSnackBar(context,
                            Text("Cheers! Now you are a real developer"));
                      } else {
                        CustomSnackBar(
                            context, Text("Already signed up as a developer"));
                      }
                    });
                });
              },
              child: Text("Become developer")),
        ),



            SettingsTitle("Credentials"),
        Padding(
          padding: const EdgeInsets.all(16.0),
          child: ElevatedButton(
              onPressed: () {
                showDialog(
                  context: context,
                  builder: (BuildContext context) {
                    return AlertDialog(
                      title: Text("Do you really want to logout?"),
                     
                      actions: <Widget>[
                        TextButton(
                          child: Text("Cancel", style: TextStyle(color: Colors.grey),),
                          onPressed: () {
                            Navigator.pop(context, null);
                          },
                        ),
                        TextButton(
                          child: Text("OK"),
                          onPressed: () {
                            Navigator.pop(context, "ok");
                          },
                        ),
                      ],
                    );
                  },
                ).then((val) {
                  if (val != null){
                      Auth.deleteToken();
                      Navigator.pushNamed(context, '/login');
                    }
                });
              },
              child: Text("Logout", style: TextStyle(color: Theme.of(context).errorColor)),
              style: ButtonStyle(backgroundColor: MaterialStateProperty.all(Colors.white)),
            ),
        ),
      ],
    );
  }
}
