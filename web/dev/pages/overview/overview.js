
function load(idApp){
    DataProvider.getApplication(idApp, (result)=>{
        console.log(result.data.data[0]);
        document.getElementById("edit-btn").onclick=()=>Route.edit(idApp);
        var app = result.data.data[0];

        document.getElementById("app-icon").setAttribute("src",app.icon);
        document.getElementById("app-name").innerHTML= app.name;
        document.getElementById("developer").innerHTML= app.developer;
        
        document.getElementById("client_id").innerHTML= app.client_id;
        document.getElementById("client_secret").innerHTML= app.client_secret.trim();

        document.getElementById("description").innerHTML=app.description;
        
        var images = document.getElementById("images");
        images.innerHTML="";
        app.screenshots.forEach(element => {
            images.innerHTML+=`<img class="m-2" src=${element}>`;
        });

        document.getElementById("delete-btn").onclick=()=>{
            DataProvider.deleteApplication(idApp,(result)=>{
                if(!result.error){
                    Route.myapps();
                }
            });
        };

    });
}

function changeTab(tab){
    for (const li of document.getElementById("tabs").getElementsByTagName("li")) {
        li.classList.remove("is-active");   
    }
    for (const content of document.getElementsByClassName("content")) {
        content.classList.add("is-hidden");   
    }
    document.getElementById("li-"+tab).classList.add("is-active");
    document.getElementById(tab).classList.remove("is-hidden");
}

function copyToClipboard(id){
    window.getSelection().removeAllRanges();
    var copyText = document.getElementById(id);
    var range = document.createRange();
    range.selectNode(copyText);
    window.getSelection().addRange(range);
    try {
        document.execCommand('copy');
    } catch (err) {
        console.log('Oops, unable to copy');
    }
}