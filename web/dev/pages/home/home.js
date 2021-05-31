document.getElementById("burger").onclick=showMenuMobie;

function showMenuMobie(){
    document.getElementById("menu-mobile").classList.remove("is-hidden");
    document.getElementById("burger").onclick=hideMenuMobie;
    burger=true;
}
function hideMenuMobie(){
    document.getElementById("menu-mobile").classList.add("is-hidden");
    document.getElementById("burger").onclick=showMenuMobie;
    burger=false;
}

Auth.tryLogin().then(()=>Route.myapps());
