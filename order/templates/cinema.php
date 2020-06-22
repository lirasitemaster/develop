<?php defined('isENGINE') or die; ?>

<?php require_once $module -> elements . 'opening.php'; ?>
<?php require_once $module -> elements . 'hidden.php'; ?>

<label>Ваше имя</label>
<input type="text" name="user" value="<?= $order -> user; ?>">

<label>Email</label>
<input type="text" name="email" value="<?= $order -> email; ?>">

<label>Телефонный номер</label>
<input type="text" name="phone" value="<?= $order -> phone; ?>">

<label>Места в зале</label>
<?php require_once $module -> elements . 'place.php'; ?>

<label>Дата</label>
<?php require_once $module -> elements . 'date.php'; ?>

<label>Время</label>
<?php require_once $module -> elements . 'time.php'; ?>

<button type="submit">Забронировать</button>

<?php require_once $module -> elements . 'ending.php'; ?>
