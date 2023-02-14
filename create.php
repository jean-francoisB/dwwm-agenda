<?php
// -----------------------------Logic ----------------------------------
// Si le serveur confirme que les données ont été envoyées via la methodes POST,
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* 
    *------------------------
    * Pensons en premier a la cybersécurité :)
    *------------------------
    */
    require __DIR__ . "/functions/security.php";
    //
    //Protégeons le serveur contre la faille de type csrf :  https://www.vaadata.com/blog/fr/attaques-csrf-principes-impacts-exploitations-bonnes-pratiques-securite/
    // Si le jeton de sécurité provenant du formulaire n'est pas le meme que celui généré par le systeme, 

    if (csrf_middleware($_POST['create_form_csrf_token'], $_SESSION['create_form_csrf_token'])) {

        // die('test');
        //On redirige automatiquement l'utilisateur vers la page de laquelle proviennent les informations
        // puis on arrete l'execution du script
        return header("Location: " . $_SERVER['HTTP_REFERER']);
        // die();
        // exit();
    }
    // Si les tokens sont bons nous n'en avons plus besoin on le supprime
    unset($_SESSION['create_form_csrf_token']);

    // Protégeons le serveur contre les robots spamerurs https://nordvpn.com/fr/blog/honeypot-informatique/
    // Si le pot de miel a détecté un robot

    if (honey_pot_middleware($_POST['create_form_honey_pot'])) {

        //On redirige automatiquement l'utilisateur vers la page de laquelle proviennent les informations
        // puis on arrete l'execution du script
        return header("Location: " . $_SERVER['HTTP_REFERER']);
    }


    // Protégeons le serveur contre la faille XSS

    $post_clean = xss_protection($_POST);

    echo $post_clean['first_name'];
    // var_dump("On peut continuer"); die();

    /*
    *------------------------
    * Pensons ensuite a la validation du formulaire
    *------------------------
*/
    require __DIR__ . "/functions/validator.php";

    $errors = [];
    // Pour le prénom

    if (isset($spot_clean['first_name'])) {
        if (is_blank($spot_clean['first_name'])) {

            $errors['first_name'] = "Le prénom est obligatoire.";
        }
    }

    var_dump($errors);
}


// Genere des bytes aléatoirement 
$_SESSION['create_form_csrf_token'] = bin2hex(random_bytes(40));




?>
<?php  // -----------------------------View ----------------------------------
?>

<?php $title = "Nouveau contact"; ?>
<?php $description = "Hello! Ajoutez un nouveau contact à la liste via le formulaire."; ?>
<?php $keywords = "Agenda, Contacts, php, php8, Projet, DWWM"; ?>
<?php require __DIR__ . "/partials/head.php"; ?>

<?php require __DIR__ . "/partials/nav.php"; ?>

<!-- Le contenu spécifique à cette page -->
<main class="container">
    <h1 class="text-center my-3 display-5">Nouveau contact</h1>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-lg-7 mx-auto p-4 shadow bg-white">
                <form method="POST">

                    <div class="row">
                        <div class="col md-6">
                            <div class="mb-3">
                                <label for="create_form_first_name">Prénom</label>
                                <input type="text" name="first_name" id="create_form_first_name" class="form-control">
                            </div>
                        </div>
                        <div class="col md-6">
                            <div class="mb-3">
                                <label for="create_form_last_name">Nom</label>
                                <input type="text" name="last_name" id="create_form_last_name" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="create_form_email">Email</label>
                                <input type="email" name="email" id="create_form_email" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="create_form_age">Age</label>
                                <input type="number" name="age" id="create_form_age" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_form_phone">Numéro de téléphone</label>
                        <input type="tel" name="phone" id="create_form_phone" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="create_form_comment">Commentaires</label>
                        <textarea name="comment" id="create_form_comment" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="mb-3 d-none">
                        <input type="hidden" name="create_form_csrf_token" value="<?= $_SESSION['create_form_csrf_token']  ?>">
                    </div>

                    <div class="mb-3 d-none">
                        <input type="hidden" name="create_form_honey_pot" value="">
                    </div>

                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary shadow" value="Ajouter">
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<?php require __DIR__ . "/partials/footer.php"; ?>

<?php require __DIR__ . "/partials/foot.php"; ?>