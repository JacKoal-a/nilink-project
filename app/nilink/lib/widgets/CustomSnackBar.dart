import 'package:flutter/material.dart';

class CustomSnackBar {
  CustomSnackBar(BuildContext context, Widget content) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: content,
        backgroundColor: Colors.teal[700],
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30.0)),
        elevation: 10,
        behavior: SnackBarBehavior.floating,
      )
    );
  }
}
