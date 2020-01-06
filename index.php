<?php

if(isset($_GET['q'])){

    $shortcut = htmlspecialchars($_GET['q']);

    $bdd = new PDO('mysql:host=localhost;dbname=Bitly;charset=utf8','root','root');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');

    $req->execute(array($shortcut));

    while($result = $req->fetch()){

        if($result['x'] != 1){
            header('location: ../?error=true&message=Adresse url non connue');
            exit();
        }
    }
    $req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    while($result = $req->fetch()){
        header('location: '.$result['url']);
        exit();
    }

}

if(isset($_POST['url'])){

    //variable
    $url = $_POST['url'];

    //verification
    if(!filter_var($url, FILTER_VALIDATE_URL)){
        //pas un lien
        header('location: ../?error=true&message=Adresse url non valide');
        exit();
    }
    //raccourci (SHORTCUT)
    $shortcut = crypt($url, rand());

    //vérification : si l'url a déjà été proposé
    $bdd = new PDO('mysql:host=localhost; dbname=Bitly; charset=utf8', 'root', 'root');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
    $req->execute(array($url));

    while($result = $req->fetch()){

        if($result['x'] !=0){
            header('location: ../?error=true&message=Adresse déjà raccourcie');
            exit();
        }
    }
    //verification : Envoie
    $req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES(?, ?)');
    $req->execute(array($url, $shortcut));

    header('location: ../?short=' .$shortcut);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/CSS" href="design/style.css">
    <link rel="icon" type="image/png" href="img/favico.png">
    <title>URL Express</title>

</head>
<body>
    <section id="hello">
        <div class="container">
            <header>
                <img src="img/logo.png" alt="" id="logo">
            </header>
            <h1>Une url longue ? Raccourcissez-là</h1>
            <h2>Largement meilleur et plus court que les autres.</h2>
            <form action="../" method="post">
            <input type="url" name="url" placeholder="Coller un lien">
            <input type="submit"  value="raccourcir">
            </form>

            <?php if(isset($_GET['error']) && isset($_GET['message'])) { ?>
                <div class="center">
                    <div id="result">
                    <b>
                            <?php echo htmlspecialchars($_GET['message']); ?>
                    </b>
                    </div>
                </div>
<?php } else if(isset($_GET['short'])){
    ?>
<div class="center">
                    <div id="result">
                    <b>URL RACCOURCI : </b>
                            http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>       
                    </div>
                </div>

<?php } ?>
        </div>  
    </section> 
    <section id="brands">
        <div class="container">
            <h3>Ces marques nous font confiance</h3>
            <img src="img/1.png" alt="" class="picture">
            <img src="img/2.png" alt="" class="picture">
            <img src="img/3.png" alt="" class="picture">
            <img src="img/4.png" alt="" class="picture"> 
        </div>
    </section>
    <footer>
        <img src="img/logo2.png" alt="logo" id="logo"><br>
        2018 © Bitly <br>
        <a href="#">Contact</a> -
        <a href="#">A propos</a>
    </footer>
</body>
</html>

