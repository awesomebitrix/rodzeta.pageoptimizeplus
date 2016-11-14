<?php
/*******************************************************************************
 * rodzeta.pageoptimizeplus - Additional page optimizations
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;

EventManager::getInstance()->addEventHandler("main", "OnEndBufferContent", function (&$content) {
	if (CSite::InDir("/bitrix/")) {
		return;
	}
	global $APPLICATION;
	if ($APPLICATION->showPanelWasInvoked) { // ignore for admin panel
		return;
	}

	// move css to bottom page
	if (Option::get("rodzeta.pageoptimizeplus", "move_css") == "Y") {
		$styles = [];
		// process all link tags
		$content = preg_replace_callback(
			'{<link([^>]*)>}is',
			function ($m) use (&$styles) {
				// ignore with attr data-skip-moving
				if (strpos($m[1], 'data-skip-moving="true"') !== false) {
					return $m[0];
				}
				// ignore other types
				if (strpos($m[1], "stylesheet") === false) {
					return $m[0];
				}
				$styles[] = $m[0];
				return "";
			},
			$content
		);

		/*
		$stylesUrls = [];
		foreach ($styles as $styleTag) {
			if (preg_match('{href=([^\s]+)}i', $styleTag, $attrs)) {
				$stylesUrls[] = substr($attrs[1], 1, -1);
			}
		}
		$content = str_replace("</head>", '
			<script data-skip-moving="true">
				(function () {
					var styles = ' . json_encode($stylesUrls) . ';
					for (var i in styles) {
						var css = document.createElement("link");
						css.setAttribute("rel", "stylesheet");
						css.setAttribute("type", "text/css");
						css.setAttribute("href", styles[i]);
						document.getElementsByTagName("head")[0].appendChild(css);
					}
				})();
			</script>
			</head>', $content);
		$content = str_replace("</body>", "
			<noscript>
				" . implode("\n", $styles) . "
			</noscript>
			</body>", $content);
		*/

		// move collected style tags
		$content = str_replace("</body>", implode("\n", $styles) . "\n</body>", $content);
	}

});
