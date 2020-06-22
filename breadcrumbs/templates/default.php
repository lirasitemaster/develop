<?php defined('isENGINE') or die; ?>
<?php $path = ''; ?>

<div class="
	breadcrumbs
	breadcrumbs_<?= $module -> param; ?>
	<?= (!empty($module -> settings['classes']['body'])) ? $module -> settings['classes']['body'] : ''; ?>
">

<?php if (!empty($module -> settings['title'])) : ?>
	<span class="
		breadcrumbs_title
		breadcrumbs_<?= $module -> param; ?>_title
		<?= (!empty($module -> settings['classes']['title'])) ? $module -> settings['classes']['title'] : ''; ?>
	"><?php
		if ($module -> settings['title'] === true && !empty(lang('title'))) {
			if (is_array(lang('title'))) {
				foreach (lang('title') as $i) {
					echo $i . ' ';
				}
			} else {
				echo lang('title');
			}
		} else {
			echo $module -> settings['title'];
		}
	?></span>
<?php endif; ?>

<ul class="
	breadcrumbs_wrapper
	breadcrumbs_<?= $module -> param; ?>_wrapper
	<?= (!empty($module -> settings['classes']['ul'])) ? $module -> settings['classes']['ul'] : ''; ?>
">
	
	<?php if (!empty($module -> settings['home'])) : ?>
		<li class="
			breadcrumbs_item
			breadcrumbs_<?= $module -> param; ?>_item
			<?= (!empty($module -> settings['classes']['li'])) ? $module -> settings['classes']['li'] : ''; ?>
			breadcrumbs_item__home
			breadcrumbs_<?= $module -> param; ?>_item__home
			<?= (!empty($module -> settings['classes']['home'])) ? $module -> settings['classes']['home'] : ''; ?>
			breadcrumbs_item__link
			breadcrumbs_<?= $module -> param; ?>_item__link
			<?= (!empty($module -> settings['classes']['link'])) ? $module -> settings['classes']['link'] : ''; ?>
		">
			<?php
				$module -> var['link'] = $template -> url;
				if (!empty(lang('menu:home'))) {
					$module -> var['home'] = lang('menu:home');
				} else {
					$module -> var['home'] = $module -> settings['home'];
				}
			?>
			<a href="<?= $module -> var['link']; ?>"><?= $module -> var['home']; ?></a>
			<?php if (!empty($module -> settings['separator'])) echo $module -> settings['separator']; ?>
		</li>
	<?php endif; ?>
	
	<?php if (objectIs(thispage('parents'))) : ?>
		<?php foreach (thispage('parents') as $item) : ?>
			<?php $path .= '/' . $item; ?>
			<?php
				if (
					empty(thispage('type')) ||
					thispage('type') === 'params' ||
					thispage('type') === 'content'
				) :
			?>
			<li class="
				breadcrumbs_item
				breadcrumbs_<?= $module -> param; ?>_item
				<?= (!empty($module -> settings['classes']['li'])) ? $module -> settings['classes']['li'] : ''; ?>
				breadcrumbs_item__folder
				breadcrumbs_<?= $module -> param; ?>_item__folder
				<?= (!empty($module -> settings['classes']['folder'])) ? $module -> settings['classes']['folder'] : ''; ?>
				breadcrumbs_item__link
				breadcrumbs_<?= $module -> param; ?>_item__link
				<?= (!empty($module -> settings['classes']['link'])) ? $module -> settings['classes']['link'] : ''; ?>
			">
				<a data-set="<?= $item; ?>" href="<?= $path; ?>"><?= lang('menu:' . $item); ?></a>
				<?php if (!empty($module -> settings['separator'])) echo $module -> settings['separator']; ?>
			<?php else : ?>
			<li class="
				breadcrumbs_item
				breadcrumbs_<?= $module -> param; ?>_item
				<?= (!empty($module -> settings['classes']['li'])) ? $module -> settings['classes']['li'] : ''; ?>
				breadcrumbs_item__folder
				breadcrumbs_<?= $module -> param; ?>_item__folder
				<?= (!empty($module -> settings['classes']['folder'])) ? $module -> settings['classes']['folder'] : ''; ?>
				breadcrumbs_item__nolink
				breadcrumbs_<?= $module -> param; ?>_item__nolink
				<?= (!empty($module -> settings['classes']['nolink'])) ? $module -> settings['classes']['nolink'] : ''; ?>
			">
				<span><?= lang('menu:' . $item); ?></span>
				<?php if (thispage('is') && !empty($module -> settings['separator'])) echo $module -> settings['separator']; ?>
			<?php endif; ?>
			</li>
		<?php endforeach; ?>
		<?php unset($item); ?>
	<?php endif; ?>
	
	<?php
		if (
			thispage('is') &&
			!empty($module -> settings['page'])
		) :
	?>
		<?php $page = thispage('is'); ?>
		<?php if (!empty(objectGet('content', 'name'))) : ?>
			<?php $article = objectGet('content', 'name'); ?>
			<?php $path .= '/' . $page; ?>
			<li class="
				breadcrumbs_item
				breadcrumbs_<?= $module -> param; ?>_item
				<?= (!empty($module -> settings['classes']['li'])) ? $module -> settings['classes']['li'] : ''; ?>
				breadcrumbs_item__page
				breadcrumbs_<?= $module -> param; ?>_item__folder
				<?= (!empty($module -> settings['classes']['folder'])) ? $module -> settings['classes']['folder'] : ''; ?>
				breadcrumbs_item__nolink
				breadcrumbs_<?= $module -> param; ?>_item__link
				<?= (!empty($module -> settings['classes']['link'])) ? $module -> settings['classes']['link'] : ''; ?>
			">
				<a data-set="<?= $page; ?>" href="<?= $path; ?>"><?= lang('menu:' . $page); ?></a>
				<?php if (!empty($module -> settings['separator'])) echo $module -> settings['separator']; ?>
			</li>
			<li class="
				breadcrumbs_item
				breadcrumbs_<?= $module -> param; ?>_item
				<?= (!empty($module -> settings['classes']['li'])) ? $module -> settings['classes']['li'] : ''; ?>
				breadcrumbs_item__page
				breadcrumbs_<?= $module -> param; ?>_item__page
				<?= (!empty($module -> settings['classes']['page'])) ? $module -> settings['classes']['page'] : ''; ?>
				breadcrumbs_item__nolink
				breadcrumbs_<?= $module -> param; ?>_item__nolink
				<?= (!empty($module -> settings['classes']['nolink'])) ? $module -> settings['classes']['nolink'] : ''; ?>
			">
				<span><?php
					if (lang('menu:' . $article)) {
						echo lang('menu:' . $article);
					} elseif (lang('menu:defaultarticle')) {
						echo lang('menu:defaultarticle');
					} else {
						echo objectGet('content', 'name');
					}
				?></span>
			</li>
		<?php else : ?>
			<li class="
				breadcrumbs_item
				breadcrumbs_<?= $module -> param; ?>_item
				<?= (!empty($module -> settings['classes']['li'])) ? $module -> settings['classes']['li'] : ''; ?>
				breadcrumbs_item__page
				breadcrumbs_<?= $module -> param; ?>_item__page
				<?= (!empty($module -> settings['classes']['page'])) ? $module -> settings['classes']['page'] : ''; ?>
				breadcrumbs_item__nolink
				breadcrumbs_<?= $module -> param; ?>_item__nolink
				<?= (!empty($module -> settings['classes']['nolink'])) ? $module -> settings['classes']['nolink'] : ''; ?>
			">
				<span><?= lang('menu:' . $page); ?></span>
			</li>
		<?php endif; ?>
		<?php unset($page); ?>
	<?php endif; ?>
	
</ul>

</div>
<?php unset($path); ?>