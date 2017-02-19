<?php
/*******************************************************************************
 * rodzeta.pageoptimizeplus - Additional page optimizations
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Pageoptimizeplus;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\EventManager;

require __DIR__ . "/lib/.init.php";

function init() {
	if (\CSite::InDir("/bitrix/")) {
		return;
	}
	$options = Options();
	if ($options["move_css"] != "Y") {
		return;
	}
	EventManager::getInstance()->addEventHandler("main", "OnEndBufferContent", function (&$content) {
		global $APPLICATION;
		if ($APPLICATION->showPanelWasInvoked) { // ignore for admin panel
			return;
		}
		ctx::$styles = array();
		// process all link tags
		$content = preg_replace_callback(
			'{<link([^>]*)>}is',
			__NAMESPACE__ . "\ReplaceStyles",
			$content
		);

		/* loading css from js - not work for google pagespeed
		// try to move after html
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
		// https://developers.google.com/speed/docs/insights/OptimizeCSSDelivery?hl=ru
		if (!empty(ctx::$styles)) {
			$content = str_replace(
				"</html>",
				"</html>\n" . implode("\n", ctx::$styles),
				$content
			);
		}
	});
}

init();
