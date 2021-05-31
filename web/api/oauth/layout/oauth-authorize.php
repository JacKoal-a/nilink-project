<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Varela&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="layout/bulma.css">
    <link rel="stylesheet" href="layout/style.css">

</head>

<body>

    <section class="hero has-background-white-ter is-primary is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-6-tablet is-5-desktop is-4-widescreen">
                        <div class="box p-10">

                            <div class="img-container p-2">
                                <a href="/"><img src="layout/nilink.svg" width="100"></a>
                            </div>

                            <div class="has-text-centered p-2">
                                <h1 class="subtitle has-text-black">Nilink OAuth</h1>
                                <?php if(isset($a)){
                                echo "<p style='margin: auto;'><b>$a->name</b> wants to access your data</p>";
                                }?>
                            </div>

                            <form method="POST">
                                <div class="field">
                                    <div class="control has-icons-left">
                                        <div class="inputBox">
                                            <input type="email" name="mail" id="mail" required class="m-0"
                                                onkeyup="this.setAttribute('value', this.value);" value="">
                                            <label>Mail</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="control has-icons-left">
                                        <div class="inputBox ">
                                            <input type="password" name="pass" id="pass" required class="m-0"
                                                onkeyup="this.setAttribute('value', this.value);" value="">
                                            <label>Password</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="is-flex is-justify-content-center ">
                                    
                                    <button type="submit" name="authorized" value="yes"
                                        class="button is-primary p-2">Login</button>
                                </div>

                            </form>
                            <?php if(isset($err) && $err!=""){
                                echo "<div style='margin: auto;'><div class='notification  is-danger is-light'>$err</span></div>";
                            }?>

                            <div class="has-text-grey or"><span>OR</span></div>

                            <meta name="google-signin-client_id"
                                content="201499464145-aiaorqcl12ero5t98s1hdr4536umr96m.apps.googleusercontent.com">
                            <script src="https://apis.google.com/js/platform.js"></script>

                            <div class="has-text-centered">
                                <div class="g-signin2" data-onsuccess="onSignedIn" data-theme="dark"></div>
                            </div>
                           
                            <script>
                                function onSignedIn(data){
                                    console.log(data["qc"]["id_token"]);
                                   

                                    const form = document.createElement('form');
                                    form.method = "POST";
                                    var params = {
                                        authorized : "yes",
                                        google : "yes",
                                        "idtoken" : data["qc"]["id_token"]
                                    };
                                    for (const key in params) {
                                        if (params.hasOwnProperty(key)) {
                                        const hiddenField = document.createElement('input');
                                        hiddenField.type = 'hidden';
                                        hiddenField.name = key;
                                        hiddenField.value = params[key];

                                        form.appendChild(hiddenField);
                                        }
                                    }

                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
<!--
    <div class="container">
        <div class="img-container">
            <img src="./nilink-500.png" width="150" >
        </div>
        
        <form method="post">

            <div class="inputBox">
                <input type="email" name="mail" required onkeyup="this.setAttribute('value', this.value);"  value="">
                <label>Mail</label>
            </div>
            <div class="inputBox">
                <input type="text" name="pass" required onkeyup="this.setAttribute('value', this.value);"  value="">
                <label>Password</label>
            </div>

            <div class="flex">
                <div style="margin-right: auto; padding: 16px;">
                    <input type="submit" style="background-color: #949494;" name="authorized" value="no">
                </div>

                <div style="margin-left: auto; padding: 18px;">
                    <input type="submit" name="authorized"  value="yes">
                </div>
                
            </div>
            
        </form>
    </div>
    -->
</body>

</html>