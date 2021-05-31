var burger=false;

Route.loading();
async function init(){
    if(localStorage.getItem("refresh_token")!=null){
        Auth.tryLogin().then((result)=>{
            if(!result.error){
                setTimeout(Route.home,500);
                
            }else{
                Route.login();
            }
        })
    }else{
        Route.login();
    }
}
init();