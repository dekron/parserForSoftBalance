<?
$ComponentPath = $this->GetFolder(); //Путь к папке компонента
$APPLICATION->AddHeadScript($ComponentPath . "/lib.js");  //Подключение lib
?>
<div class="parser-form">
  <div class="parser-form__input">
    <div class="parser-form__input-head">Выберите json файл для загрузки данных на сервер</div>
    <input type='file' id='fileinput'>
    <input type='button' id='btnLoad' value='Загрузить' onclick='lParser.loadFile();'>
  </div>
  <div class="parser-form__log-place">
    <div class="parser-form__log-head">Вывод:</div>
    <div id="parser-form__log" class="parser-form__log"></div>
  </div>
</div>


<script>
  var urlAjax = '<?=$this->getComponent()->GetPath()?>/ajax.php';
  var SITE_ID = '<?=SITE_ID?>';
  var s_data = null;
  var s_settings_users = null;
  var s_settings_rests = null;
  var s_settings = null;
  var s_items = null;
</script>