<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Person Information</title>
    <link rel="stylesheet" href="mystyle.css">
</head>
<body>
<form method="post" action="">
    <br>
    <p>PERSONAL INFORMATION</p>
    <br>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="surname">Surname:</label>
    <input type="text" id="surname" name="surname" required><br><br>

    <label for="id_number">ID Number:</label>
    <input type="number" id="id_number" name="id_number" pattern="\d{13}" required><br><br>

    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" required><br><br>

    <input type="submit" name="submit" value="Submit">
    <input type="reset" name="cancel" value="Cancel">
    <br><br>
</form>
</body>
</html>

<?php
require 'vendor/autoload.php'; //for if you're using Composer for MongoDB PHP library

//function called when submit button is pressed on form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    //retrieve and save form data in person variable
    $person = [
        'name' => trim($_POST['name']),
        'surname' => trim($_POST['surname']),
        '_id' => trim($_POST['id_number']),
        'dobDate' => $_POST['dob'],
        'dob' => getValidDOB($_POST['dob'])
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
    //validate ID number
    if(!validIdNumber($person['_id'], $person['dob'])) {
        echo "ID number must be 13 numbers long and must match Date of Birth.";
        populateForm($person);
        return;
    }
    //check for duplicate id number
    if(idExists($person['_id'])) {
        echo "Person with that ID number already exists.";
        populateForm($person);
        return;
    }
    //save to MongoDB
    echo "Information is good. Saving to MongoDB."."<br>";
    saveToMongoDB($person);
    echo "Information saved to MongoDB";
}
//saves the given person to the Mongo database
function saveToMongoDB($person) {
    $personDTO = [
        'name' => $person['name'],
        'surname' => $person['surname'],
        '_id' => $person['_id'],
        'dob' => $person['dob']
    ];
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->local->person;
    $collection->insertOne($personDTO);
}
//check a string is valid - only letters, hyphens and apostrophes
function validName($string):bool {
    if (!preg_match("/^[a-zA-Z'-]+$/", $string)){
        return false;
    }else return true;
}
//check that the id number is valid
function validIdNumber($id_number, $dob):bool {
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
function getValidDOB($dob) {
    return date("d/m/Y", strtotime($dob));
}
//check whether someone with the same id number exists in the mongo database
function idExists($id_number):bool {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->local->person;
    $existingPerson = $collection->findOne(['_id' => $id_number]);
    if ($existingPerson) return true;
    else return false;
}
//populate the HTML form with the previously entered data
function populateForm($person){
    ?>
    <script type="text/javascript">
        document.getElementById('name').value = "<?php echo htmlspecialchars($person['name'], ENT_QUOTES, 'UTF-8'); ?>";
        document.getElementById('surname').value = "<?php echo htmlspecialchars($person['surname'], ENT_QUOTES, 'UTF-8'); ?>";
        document.getElementById('id_number').value = "<?php echo htmlspecialchars($person['_id'], ENT_QUOTES, 'UTF-8'); ?>";
        document.getElementById('dob').value = "<?php echo htmlspecialchars($person['dobDate'], ENT_QUOTES, 'UTF-8'); ?>";
    </script>
    <?php
}
?>