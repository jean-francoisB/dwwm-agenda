<?php $title = "Liste des contacts"; ?>
<?php $desription = "Hello! Voici notre agenda digital et vous êtes sur la page d'acceuil"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

<?php require __DIR__ . "/partials/nav.php"; ?>

<!-- Le contenu spécifique a cette page -->
<main class="container">
    <h1 class="text-center my-3 display-5">Liste des contacts</h1>

    <div class="d-flex justify-content-end align-items-center">
        <a href="create.php" class="btn btn-primary shadow"><i class="fa-solid fa-plus"></i> Nouveau contact</a>
    </div>

</main>
<?php require  __DIR__ . "/partials/footer.php"; ?>


<?php require __DIR__ . "/partials/foot.php"; ?>