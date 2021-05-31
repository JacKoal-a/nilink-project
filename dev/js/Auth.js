class Auth{
    static token="";

    static url="https://nilink.cf";
    static endopoints={
        'login': "/api/developer/login",
        'loginGoogle': "/api/developer/login/google",
        'loginToken': "/api/developer/login/token"
    }

    static async login(mail, password){
        const data =  JSON.stringify({
            'mail':mail,
            'password':password
        });
        var result = await DataProvider.post(this.url+this.endopoints['login'],"",data);
        console.log(result);
        if(!result.error){
            
            localStorage.setItem("refresh_token",result.data.refresh_token);
            this.token=result["data"].token;
            Route.home();
        }
    }

    static async loginGoogle(idToken){
        const data = new FormData();
        data.append('idtoken', idToken);
        var result = await DataProvider.post(this.url+this.endopoints['loginGoogle'],"",data);
        if(!result.error){
            localStorage.setItem("refresh_token",result.data.refresh_token);
            this.token=result["data"].token;
            Route.home();
        }
        
    }

    static async tryLogin(){
        const data = new FormData();
        data.append('refresh_token', localStorage.getItem("refresh_token"));
        var result = await DataProvider.post(this.url+this.endopoints['loginToken'],"",data);
        if(!result.error){
            localStorage.setItem("refresh_token",result.data.refresh_token);
            this.token=result["data"].token;
            return true;
        }
        return false;
    }

    static logout(){
        localStorage.removeItem("refresh_token");
        Route.login();
    }
}