<?php
require_once(dirname(__FILE__) . '/../config/regexp.php');
require_once(dirname(__FILE__) . '/../models/Patient.php');
require_once(dirname(__FILE__) . '/../models/Appointment.php');

//tableau d'erreurs
$errorsArray = array();


//On ne controle que s'il y a des données envoyées 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// *************************************LASTNAME**********************************************//
    $lastname = trim(filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($lastname, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_STR_NO_NUMBER . '/')));

    if (!empty($lastname)) {
        if (!$isOk) {
            $errorsArray['lastname_error'] = 'Merci de choisir un nom';
        }
    } else {
        $errorsArray['lastname_error'] = 'Le champ est obligatoire';
    }

// *************************************FIRSTNAME**********************************************//
    $firstname = trim(filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($firstname, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_STR_NO_NUMBER . '/')));

    if (!empty($firstname)) {
        if (!$isOk) {
            $errorsArray['firstname_error'] = 'Le prénom n\'est pas valide';
        }
    } else {
        $errorsArray['firstname_error'] = 'Le champ est obligatoire';
    }
// *************************************DATE D'ANNIVERSAIRE **********************************************//
    $birthdate = trim(filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($birthdate, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_DATE . '/')));

    if (!empty($birthdate)) {
        if (!$isOk) {
            $errorsArray['birthdate_error'] = 'Le date n\'est pas valide';
        }
    } else {
        $errorsArray['birthdate_error'] = 'Le champ est obligatoire';
    }

    // *************************************TELEPHONE**********************************************//
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($phone, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_PHONE . '/')));

    if (!empty($phone)) {
        if (!$isOk) {
            $errorsArray['phone_error'] = 'Le numero n\'est pas valide';
        }
    }
// *************************************EMAIL**********************************************//

    $mail = trim(filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL));
    $isOk = filter_var($mail, FILTER_VALIDATE_EMAIL);

    if (!empty($mail)) {
        if (!$isOk) {
            $errorsArray['mail_error'] = 'Le mail n\'est pas valide';
        }
    } else {
        $errorsArray['mail_error'] = 'Le champ est obligatoire';
    }
   // *************************************DATE ET HEURE DE RDV**********************************************//
    $dateHour = trim(filter_input(INPUT_POST, 'dateHour', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($dateHour, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_DATE_HOUR . '/')));

    if (!empty($dateHour)) {
        // On test la valeur
        if (!$isOk) {
            $errorsArray['dateHour_error'] = 'Le date n\'est pas valide';
        }
    } else {
        $errorsArray['dateHour_error'] = 'Le champ est obligatoire';
    }

// *************************************VERIFICATIONS DES ERREURS / ENREGISTREMENT DU RDV / ENREGISTREMENT PATIENTS**********************************************//

    if (empty($errorsArray)) {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();
        $patient = new Patient($lastname, $firstname, $birthdate, $phone, $mail);
        $createPatient = $patient->create();
        $idPatients = $pdo->lastInsertId();
        $appointment = new Appointment($dateHour, $idPatients);
        $createAppointment = $appointment->create();

        if ($createPatient === true && $createAppointment === true) {
            $pdo->commit(); // Valide la transaction et exécute toutes les requetes
            $message = MSG_CREATE_PATIENT_APT_OK;
        } else {
            $pdo->rollBack(); // Annulation de toutes les requêtes exécutées avant la levée de l'exception
            $message = ERR_CREATE_PATIENT_APT_NOTOK;
        }
    }
}

/**************VUES**************************/
include(dirname(__FILE__) . '/../views/templates/header.php');
include(dirname(__FILE__) . '/../views/add-patient-appointment.php');
include(dirname(__FILE__) . '/../views/templates/footer.php');
