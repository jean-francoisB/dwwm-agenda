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
/**
 * Cette fonction permet de récupérer un contact en particulier de la table "contact"
 *
 * @param integer $id
 * @return array|false
 */
function contact_find_by(int $id): array|false
{
    // Etablissons la connexion avec la base de données
    require __DIR__ . "/../db/connexion.php";
    /** 
     * Préparons la requete de sélection qui dit 
     * Sélectionne toutes les colonnes de la table "contact, là ou l'identifiant 
     *du contact est égal a l'identifiant récupéré depuis la barre d'url

     */
    $req = $db->prepare("SELECT * FROM contact WHERE id=:id LIMIT 1");
    // Remplacons :id par sa vraie valeur
    $req->bindValue(":id", $id);
    //Executons la requete
    $req->execute();
    //Recuperons la requete
    $data = $req->fetch();
    // Fermons le curseur (Non obligatoire)
    $req->closeCursor();
    // recuperons l'enregistrement selectionné
    return $data;
}
/**
 * Cette fonction permet de modifier les informations d'un contact
 *
 * @param array $data
 * @return void
 */
function edit_contact(array $data): void
{
    //Etablissons la connexion avec la base de données
    require __DIR__ . "/../db/connexion.php";
    // Préparaons la requete de modification des données en précisant les colonnes correspondantes
    $req = $db->prepare("UPDATE contact SET first_name=:first_name, last_name=:last_name, email=:email,age=:age, phone=:phone, comment=:comment, updated_at=now() WHERE id=:id");

    // Remplaçons : ...  par leur vraie valeur
    $req->bindValue(":first_name", $data['first_name']);
    $req->bindValue(":last_name",  $data['last_name']);
    $req->bindValue(":email",      $data['email']);
    $req->bindValue(":age",        $data['age']);
    $req->bindValue(":phone",      $data['phone']);
    $req->bindValue(":comment",    $data['comment']);
    $req->bindValue(":id",         $data['id']);

    //Executons la requete
    $req->execute();
    // Refermons le curseur(pas obligatoire)
    $req->closeCursor();
}

// Contacts (0,n)-------------- (1,1) // Historique
//id
//contact_id

function delete_contact(int $id) : void
    {
        require __DIR__ . "/../db/connexion.php";

        $req = $db->prepare("DELETE FROM contact WHERE id=:id");

        $req->bindValue(":id", $id);

        $req->execute();

        $req->closeCursor();
    }