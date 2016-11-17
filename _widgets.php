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
		
$core->addBehavior('initWidgets',array('WeatherBehavior','initWidgets'));

class WeatherBehavior {
	
	public static function initWidgets($w)
	{
		$w->create('weatherplus',__('Weather'),array('publicWeather','WeatherWidget'),array('WeatherBehavior','appendWidget'),
			__('Show weather'));
		$w->weatherplus->setting('title',__('Title:'), __('Weather'));
		$w->weatherplus->setting('cities',__('City ID or City (separated by commas):'),'');
		$w->weatherplus->setting('theme',__('Icons theme:'),'liquid','combo',
			array('liquid' => 'liquid', 'flat' => 'flat', 'um' => 'um'));
		$w->weatherplus->setting('clock',__('Display local time'),1, 'check');
		$w->weatherplus->setting('citycodes','','','hidden');
		$w->weatherplus->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
    $w->weatherplus->setting('content_only',__('Content only'),0,'check');
    $w->weatherplus->setting('class',__('CSS class:'),'');
		$w->weatherplus->setting('offline',__('Offline'),0,'check');
	}
	
	public static function appendWidget($w)
	{
		$cities = str_replace("\r",'',$w->cities);
		$cities = explode(",",$cities);
		$w->citycodes = new ArrayObject();
		
		foreach ($cities as $c)
		{
			if (preg_match('/^[A-Z]{4}\d{4}$/',$c)) {
				$w->citycodes[] = $c;
			} elseif (($city = dcWeather::searchCity($c)) && !empty($city->loc)) {
					$w->citycodes[] = (string)$city->loc[0]['id'];
			}
		}
	}
}