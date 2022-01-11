<?php
    include_once './helpers/session_helper.php';

    include_once './header.php';

    if (!isset($_SESSION['usersId'])) {
        redirect('./index.php');
    }
?>

<main>
<form class="form-newPlant" action="./controllers/Plants.php" method="POST">
    <input type="hidden" name="type" value="registerPlant">
    <?php flash('newPlant'); ?>
    <input type="text" name="name" placeholder="Plant name">

    <select name="plantType">
        <option value="vegetable">Vegetable</option>
        <option value="fruitful">Fruitful</option>
    </select>

    <label for="datePlanted">Planted on:</label>
    <input type="date" name="plantedOn" id="datePlanted" value="20-01-2022">

    <label for="fert">Fertilize every</label>
    <select name="fertFreq">
        <option value="short">15 days</option>
        <option value="long">30 days</option>
    </select>

    <label for="image">Please select a picture</label>
    <input type="file" id="image" name="image" accept="image/png, image/jpeg">

    <button type="submit" name="submit">Add new plant</button>
</form>
</main>

<?php
    include_once 'footer.php';
?>