<?php
session_start();
// -----------------------------------Logic ------------------------------------------------
require __DIR__ . "/functions/manager.php";

// Récupération de tous les contacts
$contacts = find_all_contacts();

// echo "<pre>";
// var_dump($contacts);
// die();

$_SESSION['delete_contact_csrf_token'] = bin2hex(random_bytes(40));
 
?>
<?php //-------------------------------View ---------------------------------------------
?>
<?php $title = "Liste des contacts"; ?>
<?php $desription = "Hello! Voici notre agenda digital et vous êtes sur la page d'accueil"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

<?php require __DIR__ . "/partials/nav.php"; ?>

<!-- Le contenu spécifique a cette page -->
<main class="container">
    <h1 class="text-center my-3 display-5">Liste des contacts</h1>
    <!-- Alert permettant d'afficher le message flash -->
    <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])) : ?>
        <div class="text-center alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif ?>



    <div class="d-flex justify-content-end align-items-center">
        <a data-aos="flip-right" data-aos-duration="3000" href="create.php" class="btn btn-primary shadow"><i class="fa-solid fa-plus"></i> Nouveau contact</a>
    </div>

    <div class="container">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <?php foreach ($contacts as $contact) : ?>
                <div data-aos="fade-up" data-aos-duration="3000" class="my-card my-3 shadow p-4">
                    <p><strong>Prénom</strong> : <?= htmlspecialchars($contact['first_name']); ?></p>
                    <p><strong>Nom</strong> : <?= htmlspecialchars($contact['last_name']); ?></p>
                    <p><strong>Email</strong> : <?= htmlspecialchars($contact['email']); ?></p>
                    <p><strong>Téléphone</strong> : <?= htmlspecialchars($contact['phone']); ?></p>
                    <hr>
                    <a class="text-dark mx-2" title="Voir les détails" href="#" data-bs-toggle="modal" data-bs-target="#modal_<?= htmlspecialchars($contact['id']) ?>"><i class="fa-solid fa-eye"></i></a>
                    <a class="text-secondary mx-2" title="Modifier ce contact" href="edit.php?contact_id=<?= htmlspecialchars($contact['id']) ?>"><i class="fa-solid fa-pencil"></i></a>
                    <!-- Création du bouton supprimer -->
                    <a class="text-danger" href="#" onclick="event.preventDefault(); confirm('Confirmer la suppression?') && document.querySelector('#delete_' + <?= htmlspecialchars($contact['id']) ?>).submit();" title="Supprimer ce contact"><i class="fa-solid fa-trash-can"></i></a>
                    
                    <form id="delete_<?= htmlspecialchars($contact['id']) ?>" action="delete.php" method="POST">
                        <input type="hidden" name="delete_contact_csrf_token" value="<?= $_SESSION['delete_contact_csrf_token'] ?>">
                        <input type="hidden" name="contact_id" value="<?= htmlspecialchars($contact['id']) ?>">
                        <!-- <input type="submit" class="btn btn-sm btn-danger" value="Supprimer"> -->
                    </form>


                    <!-- Modal  on modifie l'id car il doit etre différent pour chaque modal-->
                    <div class="modal fade" id="modal_<?= htmlspecialchars($contact['id']) ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel"><?= htmlspecialchars($contact['first_name']) ?> <?= htmlspecialchars($contact['last_name']) ?></h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php if (isset($contact['age']) && !empty($contact['age'])) : ?>
                                        <p> <strong>Age</strong> : <?= $contact['age'] ?> </p>
                                    <?php else : ?>
                                        <p> <em>Age non renseigné</em> </p>
                                    <?php endif ?>

                                    <?php if (isset($contact['comment']) && !empty($contact['comment'])) : ?>
                                        <p> <strong>Commentaires</strong> : <?= nl2br($contact['comment']) ?> </p>
                                    <?php else : ?>
                                        <p> <em>Commentaire non renseigné</em> </p>
                                    <?php endif ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

</main>
<?php require  __DIR__ . "/partials/footer.php"; ?>


<?php require __DIR__ . "/partials/foot.php"; ?>