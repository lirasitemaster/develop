<?php defined('isCMS') or die; ?>

<?php if (!empty($module -> settings -> date -> range)) : ?>

<div id="datepicker" class="input-group input-daterange">
От
<input name="date-from"
<?php if (!empty($module -> settings -> date -> alwaysview)) : ?>
type="hidden"
<?php else : ?>
type="text"
<?php endif; ?>
style="/*visibility: hidden; height: 1px;*/" value="<?= $order -> date; ?>">
До
<input name="date-to"
<?php if (!empty($module -> settings -> date -> alwaysview)) : ?>
type="hidden"
<?php else : ?>
type="text"
<?php endif; ?>
style="/*visibility: hidden; height: 1px;*/" value="<?= $order -> date; ?>">

Число дней: <p class="date-summary"></p>
</div>

<input name="date" type="hidden" id="date">
<?php else : ?>

<div id="datepicker"></div>
<input name="date"
<?php if (!empty($module -> settings -> date -> alwaysview)) : ?>
type="hidden"
<?php else : ?>
type="text"
<?php endif; ?>
id="date" style="/*visibility: hidden; height: 1px;*/" value="<?= $order -> date; ?>">

<?php endif; ?>
