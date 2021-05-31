document.getElementById("create-btn").onclick=()=>{
    document.getElementById("app-form").classList.add("is-active");
    document.getElementById("app-form").classList.add("is-clipped");
}

document.getElementById("app-form").onclick=(e)=>{
    if(e.toElement.className=="modal-background"){
        closeModal();
    }
}

document.getElementById("refresh-btn").onclick=load;

document.getElementById("create-app-btn").onclick=()=>{
    var appName = document.getElementById("app-name").value;
    if(appName.length<1){
        document.getElementById("msg").innerHTML='<span class="tag is-danger is-light m-2">Enter a valid application name</span><br>';
        return;
    } 
    var data = JSON.stringify({"name":appName});
    DataProvider.createApplication(data,(result)=>{
        document.getElementById("create-app-btn").classList.remove("is-loading");
        if(result.error){
            document.getElementById("msg").innerHTML='<span class="tag is-danger is-light m-2">'+result.data.error+'</span><br>';
        }else{
            document.getElementById("msg").innerHTML='<span class="tag is-success is-light m-2">'+result.data.message+'</span><br>';
            document.getElementById("app-name").value="";
            load();
        }
    });
}

function closeModal(){
    document.getElementById('app-form').classList.remove('is-active');
    document.getElementById('app-form').classList.remove('is-clipped');
    document.getElementById('msg').innerHTML='';
}

function load(){
    DataProvider.getApplications((result)=>{
        if(result.error)return;
        var body=document.getElementById("table-body");
        body.innerHTML="";
        result.data.data.forEach(element => body.innerHTML+=`<tr onclick="Route.overview(${element.id})" >
            <td class="is-vcentered" style="cursor:pointer">${element.id}</td>
            <td class="is-vcentered" style="cursor:pointer">
                <figure class="image is-48x48">
                    <img class="app-icon" src='${element.icon}'>
                </figure>
            </td>
            <td class="is-vcentered" style="cursor:pointer">${element.name}</td>
            <td class="is-vcentered" style="cursor:pointer">${element.date}</td>
            
        </tr>`);
    });
}
load();