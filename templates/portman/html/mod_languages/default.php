
<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_languages
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'mod_languages/template.css', array(), true);
?>
<?php 
	$active_lang = '';
	foreach ($list as $language){
		if($language->active==1){
			$active_lang = '<span>'.$language->title.'</span>';
		}
	}
?>
<div class="mod-languages<?php echo $moduleclass_sfx ?>">
	<div id="dd" class="wrapper-dropdown-2" tabindex="1">
		<span><?php echo $active_lang;?></span>
		<ul class="dropdown <?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block';?>">
		<?php foreach ($list as $language) : ?>
			<?php if ($params->get('show_active', 0) || !$language->active):?>
				<li class="<?php echo $language->active ? 'lang-active' : '';?>" dir="<?php echo JLanguage::getInstance($language->lang_code)->isRTL() ? 'rtl' : 'ltr' ?>">
				<a href="<?php echo $language->link;?>">
				<?php if ($params->get('image', 1)):?>
					<?php //echo JHtml::_('image', 'mod_languages/' . $language->image . '.gif', $language->title_native, array('title' => $language->title_native), true);?>
					<span><?php echo $language->title;?></span>
				<?php else : ?>
					<?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);?>
				<?php endif; ?>
				</a>
				</li>
			<?php endif;?>
		<?php endforeach;?>
		
		<li><span>Mandarin</span></li>
		</ul>
	</div>
</div>