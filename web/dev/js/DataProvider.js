class DataProvider{

    static url="https://nilink.cf";

    static endopoints={
        'applications': "/api/developer/applications",
        'application': "/api/developer/application/{id}",
        'create-application': "/api/developer/application/create",
        'update-application': "/api/developer/application/{id}/update",
        'delete-application' : "/api/developer/application/{id}/delete",
        'upload-icon' : "/api/developer/application/{id}/upload/icon",
        'upload-screen' : "/api/developer/application/{id}/upload/screenshot",
        'delete-screen' : "/api/developer/application/{id}/screenshot/{screen}/delete"
    }


    static async post(url, token='', data = {}, method='POST') {
        const response = await fetch(url, {
          method: method,
          headers: {
            'Authorization': 'Bearer ' + token
          },
          redirect: 'follow', 
          body: data
        });
        var res=await response.json(); 
        if(response.status==401){
          var result = await Auth.tryLogin();
          if(result){
            return await this.post(url,Auth.token,data);
          }else{
            Auth.logout();
          }
        }
        return {
            'data':res,
            'code':response.status,
            'error':response.status!=200
        }
    }

    static async get(url, token='') {
        const response = await fetch(url, {
          method: 'GET',
          headers: {
            'Authorization': 'Bearer ' + token
          },
          redirect: 'follow', 
          referrerPolicy: 'no-referrer', 
        });
        var res=await response.json(); 
        console.log(res);
        if(response.status==401){
          var result = await Auth.tryLogin();
          if(result){
            return await this.get(url,Auth.token);
          }else{
            Auth.logout();
          }
        }
        return {
            'data':res,
            'code':response.status,
            'error':response.status!=200
        }
    }

    static async getApplications(callback){
      this.get(this.endopoints["applications"], Auth.token).then(callback);
    }

    static async getApplication(idApp, callback){
      console.log(idApp);
      this.get(this.endopoints["application"].replace("{id}",idApp), Auth.token).then(callback);
    }

    static async createApplication(data,callback){
      this.post(this.endopoints["create-application"], Auth.token, data).then(callback);
    }

    static async updateApplication(idApp,data,callback){
      this.post(this.endopoints["update-application"].replace("{id}",idApp), Auth.token, data, 'PATCH').then(callback);
    }

    static async deleteApplication(idApp,callback){
      this.post(this.endopoints["delete-application"].replace("{id}",idApp), Auth.token, {}, 'DELETE').then(callback);
    }

    static async uploadIcon(idApp,data,callback){
      this.post(this.endopoints["upload-icon"].replace("{id}",idApp), Auth.token, data, 'POST').then(callback);
    }

    static async uploadScreen(idApp,data,callback){
      this.post(this.endopoints["upload-screen"].replace("{id}",idApp), Auth.token, data, 'POST').then(callback);
    }

    static async deleteScreen(idApp, idScreen,callback){
      this.post(this.endopoints["delete-screen"].replace("{id}",idApp).replace("{screen}",idScreen), Auth.token, {}, 'DELETE').then(callback);
    }
    

}