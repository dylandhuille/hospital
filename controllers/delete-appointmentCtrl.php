<?php
require_once(dirname(__FILE__) . '/../models/Appointment.php');
// *************************************ID RDV**********************************************//
$id = intval(trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT)));


// Appel méthode statique permettant de supprimer le RDV
$response = Appointment::delete($id);

// Si $response appartient à la classe PDOException (Si une exception est retournée),
// on stocke un message d'erreur à afficher dans la vue
if ($response instanceof PDOException) {
    $message = $response->getMessage();
}

/**************VUES**************************/
include(dirname(__FILE__) . '/../views/templates/header.php');
include(dirname(__FILE__) . '/../views/templates/display-message.php');
include(dirname(__FILE__) . '/../views/templates/footer.php');