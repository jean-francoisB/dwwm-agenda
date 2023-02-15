<?php
session_start();

// ------------------------------------------Logic------------------------------------------------

// Si le serveur confirme que les données ont été envoyées via la méthode "POST",
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
         *-------------------------------------------
         * Pensons en premier à la cybersécurité :)
         *-------------------------------------------
        */
    require __DIR__ . "/functions/security.php";


    // Protégeons le serveur contre la faille de type csrf : https://www.vaadata.com/blog/fr/attaques-csrf-principes-impacts-exploitations-bonnes-pratiques-securite/
    // Si le jéton de sécurité provenant du formulaire n'est pas le même que celui généré par le système,
    if (csrf_middleware($_POST['create_form_csrf_token'], $_SESSION['create_form_csrf_token'])) {
        // On redirige automatiquement l'utilisateur vers la page de laquelle proviennent les informations
        // Puis, on arrête l'exécution du script
        return header("Location: " . $_SERVER['HTTP_REFERER']);
        // die();
        // exit();
    }

    unset($_SESSION['create_form_csrf_token']);


    // Protégeons le serveur contre les robots spameurs : https://nordvpn.com/fr/blog/honeypot-informatique/
    // Si le pot de miel a détecté un robot spameur,
    if (honey_pot_middleware($_POST['create_form_honey_pot'])) {
        // On redirige automatiquement l'utilisateur vers la page de laquelle proviennent les informations
        // Puis, on arrête l'exécution du script
        return header("Location: " . $_SERVER['HTTP_REFERER']);
    }


    // Protégeons le serveur contre la faille de type XSS 
    $post_clean = xss_protection($_POST);


    /*
         *----------------------------------------------------------------
         * Pensons en ensuite à la validation des données du formulaires
         *----------------------------------------------------------------
        */
    require __DIR__ . "/functions/validator.php";

    $errors = [];

    // Pour le prénom
    if (isset($post_clean['first_name'])) {
        if (is_blank($post_clean['first_name'])) {
            $errors['first_name'] = "Le prénom est obligatoire.";
        }
        if (length_is_greater_than($post_clean['first_name'], 255)) {
            $errors['first_name'] = "Le prénom ne doit pas dépasser 255 caractères.";
        }
    }

    // Pour le nom
    if (isset($post_clean['last_name'])) {
        if (is_blank($post_clean['last_name'])) {
            $errors['last_name'] = "Le nom est obligatoire.";
        }
        if (length_is_greater_than($post_clean['last_name'], 255)) {
            $errors['last_name'] = "Le nom ne doit pas dépasser 255 caractères.";
        }
    }

    // Pour l'email
    if (isset($post_clean['email'])) {
        if (is_blank($post_clean['email'])) {
            $errors['email'] = "L'email est obligatoire.";
        } else if (length_is_less_than($post_clean['email'], 5)) {
            $errors['email'] = "L'email doit contenir au moins 5 caractères.";
        } else if (length_is_greater_than($post_clean['email'], 255)) {
            $errors['email'] = "L'email ne doit pas dépasser 255 caractères.";
        } else if (is_invalid_email($post_clean['email'])) {
            $errors['email'] = "Veuillez entrer un email valide.";
        } else if (is_already_exists_on_create($post_clean['email'], "contact", "email")) {
            $errors['email'] = "Cet email appartient déjà à l'un de vos contacts.";
        }
    }

    // var_dump($post_clean['age']); die();
    // Pour l'age
    if (isset($post_clean['age'])) {
        if (is_not_blank($post_clean['age'])) {
            if (is_not_a_number($post_clean['age'])) {
                $errors['age'] = "L'âge doit être un nombre.";
            }
            if (is_not_between($post_clean['age'], 3, 300)) {
                $errors['age'] = "L'age doit être compris entre 3 et 300 ans.";
            }
        }
    }

    // Pour le phone

    if (isset($post_clean['phone'])) {
        if (is_blank($post_clean['phone'])) {
            $errors['phone'] = "Le numéro de téléphone est obligatoire.";
        } else if (is_invalid_phone($post_clean['phone'])) // Pause
        {
            $errors['phone'] = "Veuillez entrer un numéro de téléphone valide.";
        } else if (is_already_exists_on_create($post_clean['phone'], "contact", "phone")) {
            $errors['phone'] = "Ce numéro de téléphone appartient déjà à l'un de vos contacts.";
        }
    }

    // Pour le commentaire
    if (isset($post_clean['comment'])) {

        if (is_not_blank($post_clean['comment'])) {

            if (length_is_greater_than($post_clean['comment'], 4000)) {
                $errors['comment'] = "Le commentaire ne doit pas dépasser 4000 caractères.";
            }
        }
    }

    // Si le tableau contient au moins 1 erreur
    if (count($errors) > 0) {
        // Sauvegardons les messages d'erreurs en session.
        $_SESSION['create_form_errors'] = $errors;

        // Sauvegardon les données provenant du formulaire en session
        $_SESSION['create_form_old_values'] = $post_clean;

        // Effectuons une redirection vers la page de laquelle proviennent les informations
        //Puis, arrêtons l'éxécution du script
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }

    // Appelons le manager
    require __DIR__ . "/functions/manager.php";

    // Effectuons la requête d'insertion des données dans la table "contact"
    create_contact([
        "first_name"   => $post_clean['first_name'],
        "last_name"    => $post_clean['last_name'],
        "email"        => $post_clean['email'],
        "age"          => $post_clean['age'],
        "phone"        => $post_clean['phone'],
        "comment"      => $post_clean['comment'],

    ]);

    // Génerer un message a l'utilisateur pour lui dire que tout s'est bien passé


    $_SESSION['success'] = "Le contact a été ajouté à la liste avec succès";

    // Effectuons une redirection vers la page d'accueil
    // Puis, arrêtons l'exécution du script.
    return header("Location: index.php");
}

$_SESSION['create_form_csrf_token'] = bin2hex(random_bytes(40));










?>
<?php // ------------------------------------------View------------------------------------------------ 
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

                <?php if (isset($_SESSION['create_form_errors']) && !empty($_SESSION['create_form_errors'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            <?php foreach ($_SESSION['create_form_errors'] as $errors) : ?>
                                <li><?= $errors ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['create_form_errors']); ?>
                <?php endif ?>
                <form method="POST">

                    <div class="row">
                        <div class="col md-6">
                            <div class="mb-3">
                                <label for="create_form_first_name">Prénom</label>
                                <input type="text" name="first_name" id="create_form_first_name" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['first_name']) ? $_SESSION['create_form_old_values']['first_name'] : '';
                                                                                                                                unset($_SESSION['create_form_old_values']['first_name']); ?>">
                            </div>
                        </div>
                        <div class="col md-6">
                            <div class="mb-3">
                                <label for="create_form_last_name">Nom</label>
                                <input type="text" name="last_name" id="create_form_last_name" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['last_name']) ? $_SESSION['create_form_old_values']['last_name'] : '';
                                                                                                                            unset($_SESSION['create_form_old_values']['last_name']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="create_form_email">Email</label>
                                <input type="email" name="email" id="create_form_email" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['email']) ? $_SESSION['create_form_old_values']['email'] : '';
                                                                                                                    unset($_SESSION['create_form_old_values']['email']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="create_form_age">Age</label>
                                <input type="number" name="age" id="create_form_age" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['age']) ? $_SESSION['create_form_old_values']['age'] : '';
                                                                                                                    unset($_SESSION['create_form_old_values']['age']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_form_phone">Numéro de téléphone</label>
                        <input type="tel" name="phone" id="create_form_phone" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['phone']) ? $_SESSION['create_form_old_values']['phone'] : '';
                                                                                                            unset($_SESSION['create_form_old_values']['phone']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="create_form_comment">Commentaires</label>
                        <textarea name="comment" id="create_form_comment" class="form-control" rows="4"><?= isset($_SESSION['create_form_old_values']['comment']) ? $_SESSION['create_form_old_values']['comment'] : '';
                                                                                                        unset($_SESSION['create_form_old_values']['comment']); ?></textarea>
                    </div>

                    <div class="mb-3 d-none">
                        <input type="hidden" name="create_form_csrf_token" value="<?= $_SESSION['create_form_csrf_token'] ?>">
                    </div>

                    <div class="mb-3 d-none">
                        <input type="hidden" name="create_form_honey_pot" value="">
                    </div>

                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary shadow" value="Ajouter" formnovalidate>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<?php require __DIR__ . "/partials/footer.php"; ?>

<?php require __DIR__ . "/partials/foot.php"; ?>