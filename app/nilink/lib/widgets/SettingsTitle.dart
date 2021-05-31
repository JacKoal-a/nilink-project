import 'package:flutter/material.dart';

class SettingsTitle extends StatelessWidget {
  final String title;
  SettingsTitle(this.title);
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(8.0),
      child: Row(
        children: [
          Text(title+" "),
          Expanded(child: Divider(color: Colors.grey))
        ],
      ),
    );
  }
}