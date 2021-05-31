class Route{

    static loading(){
      $.get('pages/loading/loading.html', function(data) {
        $('body').html(data);
      }, 'text');
    }

    static login(){
      $.get('pages/login/login.html', function(data) {
        $('body').html(data);
      }, 'text');
    }

    static home(){
      $.get('pages/home/home.html', function(data) {
        $('body').html(data);
      }, 'text');
    }

    static dashboard(){
      $.get('pages/dashboard/dashboard.html', function(data) {
        $('#content').html(data);
        if(burger) document.getElementById("burger").click();
      }, 'text');
    }

    static myapps(){
      $.get('pages/myapps/myapps.html', function(data) {
        $('#content').html(data);
        if(burger) document.getElementById("burger").click();
      }, 'text');
    }

    static edit(idApp){
      $.get('pages/edit/edit.html', function(data) {
        $('#content').html(data);
        load(idApp)
      }, 'text');
    }

    static overview(idApp){
      $.get('pages/overview/overview.html', function(data) {
        $('#content').html(data);
        load(idApp);
      }, 'text');
    }

    static profile(){
      $.get('pages/profile/profile.html', function(data) {
        $('#content').html(data);
        if(burger) document.getElementById("burger").click();
      }, 'text');
    }

    static api_apps(){
      $.get('/doc/api_applications.html', function(data) {
        $('#content').html(data);
        if(burger) document.getElementById("burger").click();
      }, 'text');
    }

    static api_lobby(){
      $.get('/doc/api_lobby.html', function(data) {
        $('#content').html(data);
        if(burger) document.getElementById("burger").click();
      }, 'text');
    }


    static oauth(){
      $.get('/doc/oauth.html', function(data) {
        $('#content').html(data);
        if(burger) document.getElementById("burger").click();
      }, 'text');
    }
}