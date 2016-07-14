<?php
/***********************************************************************************************
 * rodzeta.pageoptimizeplus - Additional page optimizations
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\String;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_MAIN_TAB_TITLE_SET"),
  ),
));

?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.pageoptimizeplus", "move_css", $request->getPost("move_css"));

		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}
}

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?> type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Перемещать теги стилей вниз</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="move_css" value="Y" type="checkbox"
				<?= Option::get("rodzeta.pageoptimizeplus", "move_css") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<?php

$tabControl->end();
