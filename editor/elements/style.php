<?php defined('isCMS') or die; ?>
<?php if (!empty($sets['simple'])) : ?>
<style>
#editModal table {
	margin: 0;
}
#editModal table thead {
	display: none;
}
#editModal table tbody td {
	border: none;
}
#editModal table tbody td > label,
#editModal table tbody td > select.form-control {
	display: none !important;
}
</style>
<?php endif; ?>