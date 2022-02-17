<?php
include(dirname(__FILE__) . '/../config/regexp.php');
include(dirname(__FILE__) . '/../models/Patient.php');
include(dirname(__FILE__) . '/../models/Appointment.php');

//tableau d'erreurs
$errorsArray = array();

// Appel à la méthode qui récupérer tous les patients
$allPatients = Patient::getAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // *************************************DATE ET HEURE DE RDV**********************************************//
    // On test et on nettoie
    $dateHour = trim(filter_input(INPUT_POST, 'dateHour', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($dateHour, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_DATE_HOUR . '/')));

    // la date existe
    if (!empty($dateHour)) {
        // On test la valeur
        if (!$isOk) {
            $errorsArray['dateHour_error'] = 'Le date non valide';
        }
    } else {
        $errorsArray['dateHour_error'] = 'Ce champ est obligatoire';
    }

// *************************************ID DU PATIENTS**********************************************//
    $idPatients = intval(trim(filter_input(INPUT_POST, 'idPatients', FILTER_SANITIZE_NUMBER_INT)));
    //On test si le champ n'est pas vide
    if ($idPatients == 0) {
        $errorsArray['dateHour_error'] = 'Le champ est obligatoire';
    }
// *************************************VERIFICATIONS DES ERREURS / ENREGISTREMENT DU RDV**********************************************//
    if (empty($errorsArray)) {
        // On hydrate l'objet appointment
        $appointment = new Appointment($dateHour, $idPatients);
        // on fait appel a la méthode create d'appointment
        $response = $appointment->create();
        if ($response instanceof PDOException) {
            $message = $response->getMessage();
        } else {
            $message = MSG_CREATE_RDV_OK;
        }
    }
}

/**************VUES**************************/

include(dirname(__FILE__) . '/../views/templates/header.php');
include(dirname(__FILE__) . '/../views/appointments/form-appointment.php');
include(dirname(__FILE__) . '/../views/templates/footer.php');