<?php

include_once 'config/config.php';

include_once 'lib/Route.php';
include_once 'lib/Util.php';
include_once 'lib/HashInt.php';
include_once 'lib/ResizeImage.php';
include_once 'lib/googleapi/vendor/autoload.php';

include_once 'config/endpoints_def.php';

include_once "db/dbConnection.php";

include_once "auth/DeveloperAuth.php";
include_once "auth/UserAuth.php";

include_once "model/User.php";
include_once "model/Developer.php";
include_once "model/Application.php";
include_once "model/Lobby.php";

include_once 'oauth/server.php';