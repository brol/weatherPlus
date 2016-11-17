<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Weather for Dotclear.
# Copyright (c) 2006 Christophe Meyer and contributors. All rights
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
if (!defined('DC_RC_PATH')) { return; }

require dirname(__FILE__).'/_widgets.php';

$core->addBehavior('publicHeadContent',array('publicWeatherWidget','publicHeadContent'));

class publicWeatherWidget
{
	public static function publicHeadContent($core)
	{
		$url = $core->blog->getQmarkURL().'pf='.basename(dirname(__FILE__));
		echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$url."/css/weatherPlus.css\" />\n";
	}
}

$core->url->register('weather-icons','weather/icons','^weather/icons/([a-zA-Z0-9_-]+/\d{1,2}).png',array('publicWeather','iconURL'));

class publicWeather
{
	public static function WeatherWidget($w)
	{
		global $core;
		
		if ($w->offline)
			return;
			
		if (($w->homeonly == 1 && $core->url->type != 'default') ||
			($w->homeonly == 2 && $core->url->type == 'default'))
			return;
		
		if (count($w->citycodes) == 0) {
			return;
		}
		
		$res =
		($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '').
		'<ul>';
		
		foreach($w->citycodes as $code)
		{
			if ($xml = dcWeather::getData($code) )
			{
				$icon = (string) $xml->cc->icon == '-' ? '44' : (string) $xml->cc->icon;
				$icon = $core->blog->url.$core->url->getBase('weather-icons').'/'.$w->theme.'/'.$icon.'.png';
				$icon = '<img src="'.$icon.'" alt="" style="display:inline; vertical-align:middle;" />';
				
				$city = explode(',',(string) $xml->loc->dnam);
				$city = $city[0];
				$temp = (string) $xml->cc->tmp;
				
				if($w->clock) {
					$clock_id = str_replace('-','_',strtolower($xml->loc->zone));
					
					$cjs = (string)'';
					$cjs .= '<script type="text/javascript">';
					if (!defined('CLOCK_GEBI'))
					{
						$cjs .= 'function gEBI(id) {return document.getElementById(id);}'.
							'function zeros(int) {if (10 > int) {int = \'0\'+int;}return int;}'.
							'var d = new Date();';
						define('CLOCK_GEBI',(bool)true);
					}
					
					$cjs .= 'var diff_'.$clock_id.' = (d.getHours()-'.date('H',time()+$xml->loc->zone*3600).');';
					$cjs .= 'function clock_'.$clock_id.'() {'.
						'var d = new Date();'.
						'var h = zeros(d.getHours()-diff_'.$clock_id.');'.
						'var m = zeros(d.getMinutes());'.
						'var s = zeros(d.getSeconds());'.
						'gEBI(\'hms_'.$clock_id.'\').innerHTML = h+\':\'+m+\':\'+s;'.
						'setTimeout("clock_'.$clock_id.'()",500);'.
						'}'.
						'clock_'.$clock_id.'();'.
						'</script>';
					
					$time = '<span id="hms_'.$clock_id.'"></span>'.$cjs.'';
				}
				else $time = '';
				
				$res .= '<li class="city">'.$city.'</li>'.
					'<li class="data">'.$time.$icon.' ('.$temp.'&deg;C)</li>';
			}
		}
    $res .= '</ul>';
		return $w->renderDiv($w->content_only,'weatherplus '.$w->class,'',$res);
	}
	
	public static function iconURL($arg)
	{
		$file = dirname(__FILE__).'/icons/'.$arg.'.png';
		if (!file_exists($file)) {
			http::head(404,'Not Found');
			exit;
		}
		
		http::cache(array_merge(array($file),get_included_files()));
		
		header('Content-Type: image/png');
		readfile($file);
		exit;
	}
}