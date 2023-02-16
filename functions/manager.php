<?php

/**
 * --------------------------------------------------------------------------
 * Le manager
 * 
 * Le rôle du manager est d'interagir avec les tables de la base de données
 * --------------------------------------------------------------------------
 */

function create_contact(array $data): void
{
    // Etablissons une connexion aavec la base de données
    require __DIR__ . "/../db/connexion.php";
    // Effectuons la requête d'insertion des données en base
    // On ne met pas l'id
    $req = $db->prepare("INSERT INTO contact (first_name, last_name, email, age, phone, comment, created_at, updated_at) VALUES (:first_name, :last_name, :email, :age, :phone, :comment, now(), now() )");

    // Passons les valeurs attendues
    $req->bindValue(":first_name",      $data['first_name']);
    $req->bindValue(":last_name",       $data['last_name']);
    $req->bindValue(":email",           $data['email']);
    $req->bindValue(":age",             $data['age'] ? $data['age'] : NULL);
    $req->bindValue(":phone",           $data['phone']);
    $req->bindValue(":comment",         $data['comment']);

    // Executons la requête
    $req->execute();

    //Fermons la connexion établie avec la base de données.
    $req->closeCursor();
}


/**
 * Cette fonction permet de récupérer tous les contacts
 *
 * @return array
 */
function find_all_contacts(): array
{
    require __DIR__ . "/../db/connexion.php";
    // Requete SQL
    $req = $db->prepare("SELECT * FROM contact");

    $req->execute();

    $data = $req->fetchAll();
    $req->closeCursor();

    return $data;
}
