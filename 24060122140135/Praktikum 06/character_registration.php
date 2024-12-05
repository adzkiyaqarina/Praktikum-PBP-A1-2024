<?php
include('header.html');
require_once 'lib/db_login.php';


/*
  TODO 2 : BUATLAH
  1. server side validation
  2. insert new character
  3. tampilan hasilnya error / berhasil
*/

if (isset($_POST['submit'])) {
    $valid = TRUE;
    $error_messages = [];

    // Player Name Validation
    $player_name = test_input($_POST['player_name']);
    if (empty($player_name) || !preg_match("/^[a-zA-Z ]*$/", $player_name)) {
        $valid = FALSE;
        $error_messages[] = "Player name is required.";
    }

    // Email Validation
    $email = test_input($_POST['email']);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $valid = FALSE;
        $error_messages[] = "Email is required.";
    }

    // Password Validation
    $password = test_input($_POST['password']);
    if (empty($password) || strlen($password) < 8) {
        $valid = FALSE;
        $error_messages[] = "Password is required.";
    }

    // Race Validation
    $race = test_input($_POST['race']);
    if (empty($race)) {
        $valid = FALSE;
        $error_messages[] = "Race is required.";
    }

    // Class Validation
    $class = test_input($_POST['class']);
    if (empty($class)) {
        $valid = FALSE;
        $error_messages[] = "Class is required.";
    }

    // Attributes Validation
    $strength = (int)test_input($_POST['strength']);
    $agility = (int)test_input($_POST['agility']);
    $intelligence = (int)test_input($_POST['intelligence']);
    $total_attributes = $strength + $agility + $intelligence;

    if ($total_attributes !== 100) {
        $valid = FALSE;
        $error_messages[] = "Total attributes must equal 100.";
    }

    // Skills Validation
    $skills = isset($_POST['skills']) ? implode(',', $_POST['skills']) : "";
    if (empty($skills)) {
        $valid = FALSE;
        $error_messages[] = "At least one skill must be selected.";
    }


if (isset($_POST['race']) && isset($_POST['class'])) {
    $race_id = test_input($_POST['race']);
    $class_id = test_input($_POST['class']);
} else {
    $valid = FALSE;
    $error_messages[] = "Race and class must be selected.";
}

// Jika validasi berhasil, insert ke database
if ($valid) {
    $stmt = $conn->prepare("INSERT INTO tb_characters (player_name, email, password, strength, agility, intelligence, race_id, class_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("ssssiiss", $player_name, $email, $hashed_password, $strength, $agility, $intelligence, $race_id, $class_id);
    
    if ($stmt->execute()) {
        echo "<p class='text-success'>Character successfully created!</p>";
    } else {
        echo "<p class='text-danger'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
} else {
    echo "<ul class='text-danger'>";
    foreach ($error_messages as $message) {
        echo "<li>" . $message . "</li>";
    }
    echo "</ul>";
}

}    

?>

<div class="card">
    <div class="card-header text-center">
        <h3>RPG Character Registration</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <!-- Player Name -->
            <div class="form-group">
                <label for="player_name">Player Name</label>
                <input type="text" name="player_name" id="player_name" class="form-control">
                <div class="text-danger">
                    <!-- Error Message -->
                </div>
            </div>
            
            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" onblur="getCharacter()">
                <div class="text-danger" id="error_email">
                    <!-- Error Message -->
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control">
                <div class="text-danger">
                    <!-- Error Message -->
                </div>
            </div>

            <!-- Race and Class -->
            <div class="form-group">
                <label for="race">Race</label>
                <select name="race" id="race" class="form-control" onchange="getClasses(this.value)">
                    <option value="">Select Race</option>
                    <?php include 'get_races.php'; ?> <!-- Get races from DB -->
                </select>
                <div class="text-danger" id="error_race">
                    <!-- Error Message -->
                </div>
            </div>

            <div class="form-group">
                <label for="class">Class</label>
                <select name="class" id="class" class="form-control">
                    <option value="">Select Class</option>
                </select>
                <div class="text-danger" id="error_class">
                    <!-- Error Message -->
                </div>
            </div>

            <!-- Attributes (Strength, Agility, Intelligence) -->
            <div class="form-group">
                <label for="attributes">Character Attributes (Total: 100)</label>
                <div class="d-flex justify-content-between">
                    <div class="p-2 flex-grow-1">
                        <label for="strength">Strength: </label>
                        <input type="number" name="strength" id="strength" class="form-control flex-fill" min="0" max="100">
                    </div>
                    <div class="p-2 flex-grow-1">
                        <label for="agility">Agility: </label>
                        <input type="number" name="agility" id="agility" class="form-control" min="0" max="100">
                    </div>
                    <div class="p-2 flex-grow-1">
                        <label for="intelligence">Intelligence: </label>
                        <input type="number" name="intelligence" id="intelligence" class="form-control" min="0" max="100">
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="form-group">
                <label for="skills">Select Skills (Ctrl + Click for multiple)</label>
                <select name="skills[]" id="skills" class="form-control" multiple>
                    <option value="Swordsmanship">Swordsmanship</option>
                    <option value="Archery">Archery</option>
                    <option value="Magic">Magic</option>
                    <option value="Stealth">Stealth</option>
                </select>
            </div>

            <br>
            <button type="submit" name="submit" class="btn btn-primary btn-block">Create Character</button>
        </form>
    </div>
</div>