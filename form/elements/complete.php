<?php defined('isENGINE') or die; ?>

<?= (!empty($labels['complete'])) ? $labels['complete'] : ''; ?>
<?php if (!empty($labels['refresh'])) : ?>
<a href="<?= $uri -> site . $uri -> path -> string; ?>" class="button"><?= $labels['refresh']; ?></a>
<?php endif; ?>