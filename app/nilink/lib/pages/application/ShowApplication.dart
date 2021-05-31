import 'package:flutter/material.dart';
import 'package:nilink/util/DataProvider.dart';
import 'package:nilink/widgets/CustomAppBar.dart';

class ShowApplication extends StatefulWidget {
  final String id;
  ShowApplication(this.id);
  @override
  _ShowApplicationState createState() => _ShowApplicationState();
}

class _ShowApplicationState extends State<ShowApplication> {
  dynamic data;

  @override
  void initState() {
    super.initState();
    print("id: " + widget.id);
    Future.delayed(Duration.zero, () {
      DataProvider.getApplication(widget.id).then((value) {
        if (!value["error"]) {
          setState(() {
            data = value["result"]["data"][0];
            print(data);
          });
        } else {
          Navigator.of(context).pop();
        }
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: CustomAppBar(
        child: Icon(Icons.arrow_back_rounded),
        onPressed: Navigator.of(context).canPop()
            ? () => Navigator.of(context).pop()
            : () => Navigator.of(context).pushNamed(("/home")),
      ),
      body: data != null
          ? SingleChildScrollView(
              child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                  Row(
                    children: [
                      SizedBox(width: 10),
                      Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Container(
                          height: 100,
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(16.0),
                            child: Image.network(
                              DataProvider.url + data["icon"],
                              loadingBuilder:
                                  (context, child, loadingProgress) {
                                if (loadingProgress == null) return child;

                                return Center(
                                    child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                ));
                              },
                              errorBuilder: (context, error, stackTrace) =>
                                  Icon(Icons.error_outline_rounded),
                            ),
                          ),
                        ),
                      ),
                      SizedBox(width: 10),
                      Column(
                        mainAxisAlignment: MainAxisAlignment.start,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            width: MediaQuery.of(context).size.width-200,
                            child: Text(
                              data["name"],
                              style: TextStyle(fontSize: 24),
                            ),
                          ),
                          SizedBox(height: 10),
                          Text(data["developer"])
                        ],
                      )
                    ],
                  ),
                  SizedBox(
                    height: 20,
                  ),
                  Container(
                    height:  data["screenshots"].length>0?300:0,
                    child: Scrollbar(
                      isAlwaysShown: true,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: data["screenshots"].length,
                        itemBuilder: (context, index) {
                          return Padding(
                            padding: const EdgeInsets.all(8.0),
                            child: Container(
                              height: 200,
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(8.0),
                                child: Image.network(
                                  DataProvider.url + data["screenshots"][index],
                                  loadingBuilder:
                                      (context, child, loadingProgress) {
                                    if (loadingProgress == null) return child;
                                    return Center(
                                        child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                    ));
                                  },
                                  errorBuilder: (context, error, stackTrace) =>
                                      Icon(Icons.error_outline_rounded),
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ),
                  SizedBox(height: 10),
                  data["description"].length>0?
                  Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      children: <Widget>[
                        SizedBox(
                          width: double.infinity,
                          child: Container(
                            decoration: BoxDecoration(
                                border: Border.all(
                                  color: Colors.grey,
                                ),
                                borderRadius:
                                    BorderRadius.all(Radius.circular(20))),
                            child: Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Text(
                                data["description"],
                                textAlign: TextAlign.left,
                                style: TextStyle(fontSize: 18),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ):Container(),
                ]))
          : CircularProgressIndicator(strokeWidth: 2),
    );
  }
}
