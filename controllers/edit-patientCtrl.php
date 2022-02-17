<?php
require_once(dirname(__FILE__) . '/../config/regexp.php');

require_once(dirname(__FILE__) . '/../models/Patient.php');


//tableau d'erreurs
$errorsArray = array();
// *************************************ID PATIENT**********************************************//
$id = intval(trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT)));


$response = Patient::get($id);
// Si $response appartient à la classe PDOException (Si une exception est retournée),
// on stocke un message d'erreur à afficher dans la vue
if ($response instanceof PDOException) {
    $message = $response->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// *************************************LASTNAME**********************************************//
    $lastname = trim(filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($lastname, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_STR_NO_NUMBER . '/')));

    if (!empty($lastname)) {
        if (!$isOk) {
            $errorsArray['lastname_error'] = 'Merci de choisir un nom valide';
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
// *************************************birthdate**********************************************//
    $birthdate = trim(filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($birthdate, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_DATE . '/')));

    if (!empty($birthdate)) {
        if (!$isOk) {
            $errorsArray['birthdate_error'] = 'Le date n\'est pas valide, le format attendu est JJ/MM/AAAA';
        }
    } else {
        $errorsArray['birthdate_error'] = 'Le champ est obligatoire';
    }

// *************************************PHONE**********************************************//
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    $isOk = filter_var($phone, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_PHONE . '/')));

    if (!empty($phone)) {
        if (!$isOk) {
            $errorsArray['phone_error'] = 'Le numero n\'est pas valide, les séparateur sont - . /';
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

    // Si il n'y a pas d'erreurs, on met à jour le patient.
    if (empty($errorsArray)) {
        $patient = new Patient($lastname, $firstname, $birthdate, $phone, $mail);
        $response = $patient->update($id);
        // Si $response appartient à la classe PDOException (Si une exception est retournée),
        // on stocke un message d'erreur à afficher dans la vue
        if ($response instanceof PDOException) {
            $message = $response->getMessage();
        } else {
            $message = MSG_UPDATE_PATIENT_OK;
        }
    }
}

/**************VUES **************************/
include(dirname(__FILE__) . '/../views/templates/header.php');
include(dirname(__FILE__) . '/../views/patients/form-patient.php');
include(dirname(__FILE__) . '/../views/templates/footer.php');