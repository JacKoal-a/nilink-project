import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:nilink/pages/application/ShowApplication.dart';
import 'package:nilink/util/DataProvider.dart';
import 'package:nilink/util/Define.dart';

class Applications extends StatefulWidget {
  @override
  _ApplicationsState createState() => _ApplicationsState();
}

class _ApplicationsState extends State<Applications> {
  TextEditingController _searchController = new TextEditingController();
  ScrollController _scrollViewController;
  bool _showAppbar = true;
  bool isScrollingDown = false;
  dynamic data;
  String show = "";
  @override
  void initState() {
    super.initState();
    _scrollViewController = new ScrollController();
    _scrollViewController.addListener(() {
      if (_scrollViewController.position.userScrollDirection ==
          ScrollDirection.reverse) {
        if (!isScrollingDown) {
          isScrollingDown = true;
          _showAppbar = false;
          setState(() {});
        }
      }

      if (_scrollViewController.position.userScrollDirection ==
          ScrollDirection.forward) {
        if (isScrollingDown) {
          isScrollingDown = false;
          _showAppbar = true;
          setState(() {});
        }
      }
    });
    DataProvider.getApplications().then((value) {
      if (!value["error"]) {
        setState(() {
          data = value["result"];
        });
      }
    });
  }

  @override
  void dispose() {
    super.dispose();
    _scrollViewController.dispose();
    _scrollViewController.removeListener(() {});
  }

  @override
  Widget build(BuildContext context) {
    return (Column(mainAxisAlignment: MainAxisAlignment.start, children: [
      AnimatedContainer(
        height: _showAppbar ? 80.0 : 0.0,
        duration: Duration(milliseconds: 200),
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
          child: data != null
              ? Container(
                  width: Define.isMobile(context) ? null : 400,
                  child: TextField(
                      controller: _searchController,
                      decoration: InputDecoration(
                          suffixIcon: Icon(Icons.search),
                          contentPadding: EdgeInsets.fromLTRB(10, 6, 10, 6),
                          border: OutlineInputBorder(
                              borderRadius:
                                  BorderRadius.all(Radius.circular(16))),
                          labelText: "Search"),
                      onChanged: (value) {
                        setState(() {
                          show = value;
                        });
                      }),
                )
              : null,
        ),
      ),
      data != null
          ? Expanded(
              child: GestureDetector(
                  onTap: () => FocusScope.of(context).unfocus(),
                  child: Container(
                    color: Theme.of(context).scaffoldBackgroundColor,
                    child: GridView.extent(
                        controller: _scrollViewController,
                        maxCrossAxisExtent: 300.0,
                        crossAxisSpacing: 10.0,
                        mainAxisSpacing: 10.0,
                        children: _generateGrid(context, data["data"])),
                  )),
            )
          : Center(
              child: Padding(
                padding: const EdgeInsets.all(8.0),
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                ),
              ),
            ),
    ]));
  }

  List<Widget> _generateGrid(BuildContext context, List<dynamic> data) {
    List<Widget> widgets = [];
    data.forEach((dat) {
      if(dat["name"].contains(show))
      widgets.add(Padding(
        padding: const EdgeInsets.all(8.0),
        child: Container(
          child: InkWell(
            onTap: () {
              print("id prima:" + dat["id"]);
              Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (context) => ShowApplication(dat["id"])),
              );
            },
            child: Card(
              elevation: 4,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  Flexible(
                      flex: 3,
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(16.0),
                        child: Image.network(
                          DataProvider.url + dat["icon"],
                          loadingBuilder: (context, child, loadingProgress) {
                            if (loadingProgress == null) return child;

                            return Center(
                                child: CircularProgressIndicator(
                              strokeWidth: 2,
                            ));
                          },
                          errorBuilder: (context, error, stackTrace) =>
                              Icon(Icons.error_outline_rounded),
                        ),
                      )),
                  Flexible(
                      flex: 1,
                      child: Text(
                        dat["name"],
                        style: TextStyle(fontSize: 16),
                        textAlign: TextAlign.center,
                      )),
                  Flexible(
                      flex: 1,
                      child: Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Text(
                          dat["developer"],
                          style: TextStyle(
                              fontSize: 12, fontWeight: FontWeight.bold),
                          textAlign: TextAlign.center,
                        ),
                      ))
                ],
              ),
            ),
          ),
        ),
      ));
    });
    return widgets;
  }
}
