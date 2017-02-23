<?php
/*******************************************************************************
 * rodzeta.pageoptimizeplus - Additional page optimizations
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Pageoptimizeplus;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new \CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_MAIN_TAB_TITLE_SET"),
  ),
));

?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if ($request->getPost("save") != "" || $request->getPost("restore") != "") {
		$options = Options();
		$options["move_css"] = $request->getPost("move_css");
		$options["js_css"]["src_folders"] = $request->getPost("js_css_src_folders");
		$options["js_css"]["src_files"] = $request->getPost("js_css_src_files");
		$options["images"]["src_folders"] = $request->getPost("images_src_folders");
		$options["images"]["src_files"] = $request->getPost("images_src_files");
		OptionsUpdate($options);
		\CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}
}
$options = Options();

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label><?= Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_MOVE_STYLES") ?></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="move_css" value="Y" type="checkbox"
				<?= $options["move_css"] == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr class="heading">
		<td colspan="2"><b><?= Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_JS_CSS_SECTION") ?></b></td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label><?= Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_JS_CSS_FOLDERS") ?></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<textarea name="js_css_src_folders" cols="60" rows="10"><?= implode("\n", $options["js_css"]["src_folders"]) ?></textarea>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label><?= Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_JS_CSS_FILES") ?></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<textarea name="js_css_src_files" cols="60" rows="10"><?= implode("\n", $options["js_css"]["src_files"]) ?></textarea>
		</td>
	</tr>

	<tr class="heading">
		<td colspan="2"><b><?= Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_IMAGES_SECTION") ?></b></td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label><?= Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_IMAGES_FOLDERS") ?></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<textarea name="images_src_folders" cols="60" rows="10"><?= implode("\n", $options["js_css"]["src_folders"]) ?></textarea>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label><?= Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_IMAGES_FILES") ?></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<textarea name="images_src_files" cols="60" rows="10"><?= implode("\n", $options["js_css"]["src_files"]) ?></textarea>
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save"
  	value="<?= Loc::getMessage("RODZETA_PAGEOPTIMIZEPLUS_SAVE_SETTINGS") ?>">

</form>

<?php

$tabControl->end();
