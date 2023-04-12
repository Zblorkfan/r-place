
<?php
// Connexion à la base de donnée
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "dbname";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connexion à la base de données impossible : " . mysqli_connect_error());
}

// Vérifier si le formulaire de dessin a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données de dessin depuis le formulaire
    $x = $_POST['x'];
    $y = $_POST['y'];
    $color = $_POST['color'];

    // Insérer le pixel dans la base de données
    $sql = "INSERT INTO canvas (x, y, color) VALUES ('$x', '$y', '$color')";
    if (mysqli_query($conn, $sql)) {
        // Pixel inséré avec succès, rediriger vers la page d'accueil
        header('Location: index.php');
        exit;
    } else {
        echo "Erreur lors de l'insertion du pixel : " . mysqli_error($conn);
    }
}

// Récupérer tous les pixels de la base de données
$sql = "SELECT * FROM canvas";
$result = mysqli_query($conn, $sql);

// Créer un tableau associatif des pixels
$pixels = array();
while ($row = mysqli_fetch_assoc($result)) {
    $pixels[$row['x']][$row['y']] = $row['color'];
}

// Fermer la connexion à la base de données
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>R/PLACE IS BACK !!!!</title>
</head>
<body>
    <h1>R/PLACE IS BACK !!!!</h1>

    <canvas id="canvas" width="1000" height="600" style="border : solid"></canvas>
    <form method="POST">
        <label for="color">Couleur : </label>
        <input type="color" id="color" name="color" value="#000000">


    </form>

    <script>
        var canvas = document.getElementById('canvas');
        var ctx = canvas.getContext('2d');
        

        // Dessiner tous les pixels de la base de données sur la toile virtuelle
        <?php foreach ($pixels as $x => $row) {
            foreach ($row as $y => $color) { ?>
                ctx.fillStyle = "<?php echo $color; ?>";
                ctx.fillRect(<?php echo $x; ?>, <?php echo $y; ?>, 5, 5);
            <?php }
        } ?>

        // Ajouter un gestionnaire d'événements de clic pour dessiner un nouveau pixel sur la toile virtuelle
        canvas.addEventListener('click', function(event) {
            var x = event.offsetX;
            var y = event.offsetY;
            var color = document.getElementById('color').value;

            // Envoyer les données de dessin au serveur via une requête AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                window.location.reload();
            };
            xhr.send('x=' + x + '&y=' + y + '&color=' + encodeURIComponent(color));
        });
    </script>
</
