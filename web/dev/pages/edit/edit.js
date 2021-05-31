var id;
function load(idApp){
  id=idApp;
  DataProvider.getApplication(idApp, (result)=>{
      console.log(result.data.data[0]);
      var app = result.data.data[0];
      document.getElementById("back-btn").onclick=()=>Route.overview(idApp);
      
      document.getElementById("app-icon").setAttribute("src",app.icon);
      document.getElementById("app-name").value = app.name;
      document.getElementById("developer").innerHTML= app.developer;

      var screenshots= document.getElementById("screenshots");
      screenshots.innerHTML="";
      
      if(app.screenshots.length==0){
        document.getElementById("delete-btn").classList.add("is-hidden");
        document.getElementById("screenshots-main").classList.add("is-hidden");
        document.getElementById("no-screen").classList.remove("is-hidden");
      }else{
        document.getElementById("delete-btn").classList.remove("is-hidden");
        document.getElementById("screenshots-main").classList.remove("is-hidden");
        document.getElementById("no-screen").classList.add("is-hidden");
        app.screenshots.forEach(element => {
          screenshots.innerHTML+=`<img class="m-2" onclick="setMainScreenshot('${element}')" src=${element}>`;
        });
        setMainScreenshot(app.screenshots[0]);
      }

      document.getElementById("description").value= app.description;
      countChar(document.getElementById("description"));

      document.getElementById("new-icon").addEventListener("change", () =>{
        const selectedFile = document.getElementById('new-icon').files[0];
        if(selectedFile!=undefined || selectedFile!=null){
          var data = new FormData();
          data.append('image', selectedFile);
          DataProvider.uploadIcon(idApp,data,(result)=>{
            console.log(result);
            if(!result.error){
              load(idApp);
            }
          });
        }
      }, false);

      document.getElementById("new-screen").addEventListener("change", () =>{
        const selectedFile = document.getElementById('new-screen').files[0];
        if(selectedFile!=undefined || selectedFile!=null){
          var data = new FormData();
          data.append('image', selectedFile);
          console.log(selectedFile);
          console.log("upload");
          document.getElementById('new-screen').value="";
          DataProvider.uploadScreen(idApp,data,(result)=>{
            
            console.log(result);
            if(!result.error){
              load(idApp);
            }
          });


        }
      }, false);

  });
}

function countChar(val) {
    var len = val.value.length;
    if (len >= 1000) {
      val.value = val.value.substring(0, 1000);
    } else {
      $('#charNum').text(999 - len);
    }
}

function setMainScreenshot(src){
  document.getElementById("main-screen").setAttribute("src", src);
  document.getElementById("delete-btn").onclick=()=>{
    DataProvider.deleteScreen(id, src.replace("/api/screenshot/","").replace(".png",""),(result)=>{
      console.log(result);
      if(!result.error){
        load(id);
      }
    });
  }
}

document.getElementById("save-description").onclick=()=>{
  var desc=document.getElementById("description").value==""?" ":document.getElementById("description").value;
  DataProvider.updateApplication(id,JSON.stringify({
    "description": desc
  }), (result)=>{load(id);console.log(result);});
};

document.getElementById("save-name").onclick=()=>{
  DataProvider.updateApplication(id,JSON.stringify({
    "name": document.getElementById("app-name").value
  }), (result)=>{load(id);console.log(result);});
};