import 'package:flutter/material.dart';

class Or extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Row(children: [
      SizedBox(width: 150, child: Divider(color: Colors.grey)),
      Text(" OR ", style: TextStyle(color: Colors.grey)),
      SizedBox(width: 150, child: Divider(color: Colors.grey))
    ], mainAxisAlignment: MainAxisAlignment.center);
  }
}
