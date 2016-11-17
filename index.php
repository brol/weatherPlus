<?php 
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Weather+ for Dotclear.
# Copyright (c) 2008 Gonzague Reydet. All rights
# reserved.
#
# Weather for Dotclear is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Weather for Dotclear is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with DotClear; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

if (!defined('DC_CONTEXT_ADMIN')) { exit; }

$page_title = __('Weather');

$default_tab = 'search-tab';

$wp_id	= $core->blog->settings->weatherplus->wp_id;
$wp_key	= $core->blog->settings->weatherplus->wp_key;

if (isset($_POST['wp_id']))
{
	try
	{
		$wp_id = $_POST['wp_id'];
		$wp_key = $_POST['wp_key'];
		
		# Everything's fine, save options
		$core->blog->settings->addNamespace('weatherplus');
		$core->blog->settings->weatherplus->put('wp_id',$wp_id,'string','Partner ID');
		$core->blog->settings->weatherplus->put('wp_key',$wp_key,'string','License Key');
		
		$core->blog->triggerBlog();
		http::redirect($p_url.'&upd=1&tab=config-tab');
	}
	catch (Exception $e)
	{
		$core->error->add($e->getMessage());
	}
}

?>

<html>
<head>
  <title><?php echo $page_title; ?></title>
  <?php echo dcPage::jsPageTabs($default_tab); ?>
</head>
<body>
<?php

	echo dcPage::breadcrumb(
		array(
			html::escapeHTML($core->blog->name) => '',
			'<span class="page-title">'.$page_title.'</span>' => ''
		));

  if (!empty($_GET['upd'])) {
    dcPage::success(__('Setting have been successfully updated.'));
  }
?>

<div class="multi-part" id="config-tab" title="<?php echo __('Configuration'); ?>">
<?php
echo
'<form action="'.$p_url.'" method="post">'.
'<p><label>'.__('Key ID:').' '.
form::field('wp_key',30,512,html::escapeHTML($wp_key),'').'</label></p>'.
'<p><label>'.__('Project Name:').' '.
form::field('wp_id',30,512,html::escapeHTML($wp_id),'').'</label></p>'.
'<p>'.$core->formNonce().'<input type="submit" value="'.__('Save').'" /></p>'.
'</form>';
?>
</div>

<div class="multi-part" id="search-tab" title="<?php echo __('Search a city'); ?>">
	<form action="plugin.php" method="post">
	<p><label class="classic">
		<?php echo __('City').' : '.form::field('city',20,255,'','',2); ?>
	</label>
	<input type="submit" value="<?php echo __('Search'); ?>" />
	<input type="hidden" name="p" value="weatherPlus"/>
	<?php echo $core->formNonce(); ?></p>
	</form>
<?php
	if (!empty($_POST['city'])) {
		$cities = dcWeather::searchCity($_POST['city']);

		if (!empty($cities->loc)) {
			echo __('Results found for').' <strong>'.$_POST['city'].'</strong> :
			<table>
			<thead>
			<tr>
	  			<th>'.__('City').'</th>
	  			<th>'.__('ID City').'</th>
			</tr>
			</thead>
			<tbody>';

			foreach($cities->loc as $loc)
				echo '<tr><td>'.$loc.'</td><td>'.$loc['id'].'</td></tr>';

			echo '</tbody></table>';
		}
		else echo __('No result found for').' '.$_POST['city'];
	}
?>
</div>
<?php dcPage::helpBlock('weatherPlus'); ?>
</body>
</html>
