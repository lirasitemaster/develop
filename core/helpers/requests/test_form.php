<!-- отправка этой формы дает HTTP_REFERER -->
<hr>
<form method="get" action="<?= $url; ?>">
<input type="text" name="name" value="value">
<input type="submit">
</form>