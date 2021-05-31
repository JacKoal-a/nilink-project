<?php
#region [oauth]
Route::add(OAUTH.'/token', function() {
    OAuth::issueToken();
}, "POST");

Route::add(OAUTH.'/authorization', function() {
    OAuth::authorize();
}, "*");

#endregion