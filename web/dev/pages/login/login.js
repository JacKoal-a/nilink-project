function login(){
    console.log("login");
    Auth.login(
        document.getElementById("mail").value,
        document.getElementById("pass").value,
    );
}

function onSignedIn(googleUser) {
    Auth.loginGoogle(googleUser.getAuthResponse().id_token).then(

    );
}

