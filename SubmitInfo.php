<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Person Information</title>
</head>
<body>
<form method="post" action="">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="surname">Surname:</label>
    <input type="text" id="surname" name="surname" required><br><br>

    <label for="id_number">ID Number:</label>
    <input type="text" id="id_number" name="id_number" pattern="\d{13}" required><br><br>

    <label for="dob">Date of Birth (dd/mm/YYYY):</label>
    <input type="text" id="dob" name="dob" pattern="\d{2}/\d{2}/\d{4}" required><br><br>

    <input type="submit" name="submit" value="Submit">
    <input type="reset" name="cancel" value="Cancel">
</form>
</body>
</html>

<?php
require 'vendor/autoload.php'; //for if you're using Composer for MongoDB PHP library

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    //retrieve and save form data in person variable
    $person = [
        'name' => trim($_POST['name']),
        'surname' => trim($_POST['surname']),
        '_id' => trim($_POST['id_number']),
        'dob' => trim($_POST['dob'])
    ];

    //validate name and surname
    if(!validName($person['name'])) {
        echo "Name may contain only letters, hyphens and apostrophes.";
        populateForm($person);
        return;
    }
    if(!validName($person['surname'])) {
        echo "Surname may contain only letters, hyphens and apostrophes.";
        populateForm($person);
        return;
    }
    //validate date of birth
    if(!validDOB($person['dob'])) {
        echo "Date of Birth must be in the format dd/mm/YYYY.";
        populateForm($person);
        return;
    }
    //validate ID number
    if(!validIdNumber($person['id_number'], $person['dob'])) {
        echo "ID number must be 13 numbers long and must match Date of Birth.";
        populateForm($person);
        return;
    }
    //check for duplicate id number
    if(idExists($person['id'])) {
        echo "Person with that ID number already exists.";
        populateForm($person);
        return;
    }
    // Save to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->local->Person;
    $collection->insertOne($person);
    echo "Person information has been saved successfully.";
}
//check the string is valid - only letters, hyphens and apostrophes
function validName($string){
    if (!preg_match("/^[a-zA-Z'-]+$/", $string)){
        return false;
    }else return true;
}
//check that the id number is valid
function validIdNumber($id_number, $dob) {
    //check ID number against Date of Birth
    $birthYear = substr($dob, 6, 4);
    $birthMonth = substr($dob, 3, 2);
    $birthDay = substr($dob, 0, 2);
    if (substr($id_number, 0, 2) !== substr($birthYear, 2) ||
        substr($id_number, 2, 2) !== $birthMonth ||
        substr($id_number, 4, 2) !== $birthDay) {
        return false;
    }else return true;
}
//check that the date of birth is valid
function validDOB($dob) {
    if (!preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $dob)) return false;
    else return true;
}
//check whether someone with the same id number exists in the mongo database
function idExists($id_number) {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->local->Person;
    $existingPerson = $collection->findOne(['_id' => $id_number]);
    if ($existingPerson) return true;
    else return false;
}
//populate the HTML form with the previously entered data
function populateForm($person){

}
?>