<?php
require_once(__DIR__ . "/cms.php");
if(isset($_GET["url"])){
    $url = preg_replace('/[^a-z0-9\-_]/', '', strtolower(rtrim($_GET['url'] ?? '', '/')));
    if($url == $cms->getUrls()["loginURL"]){
        if(isset($_POST["login"])){
            if(!$cms->login($_POST["login"])){
                $cms->alert("Nesprávné heslo", SCMS::ERROR);
            }
        }
        if($cms->isLoggedIn()){
            header("location: ".$cms->getBaseURL());
            exit;
        }
    }else if($url == $cms->getUrls()["logoutURL"]){
        $cms->logout();
    }else{
        $cms->throw404();
    }
}else{
    $cms->throw404();
}

?>

<!-- Login page -->

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnapCMS - Login</title>
    <?php echo $cms->link();?>
</head>
<body class="snapCMS" id="loginPage">
    <div class="centerBox">
        <div>
            <h1 class="primary">Vítej!</h1>
            <span>Tady se můžeš přihlásit</span>
        </div>
        <form action="<?php $cms->getUrls()["loginURL"] ?>" method="post">
            <input type="password" placeholder="Heslo" name="login" id="login">
            <button type="submit">Přihlásit se</button>
        </form>

    </div>
    <span class="footer">Made by <a href="https://betthy.cz" target="__BLANK">Betthy</a></span>
    <?php $cms->showAlerts(); ?>
</body>
</html>


