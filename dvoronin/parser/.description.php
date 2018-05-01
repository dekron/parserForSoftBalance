<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
$arComponentDescription = array(
    "NAME" => Loc::getMessage('DVORONIN_PARSER_NAME'),
    "DESCRIPTION" => Loc::getMessage('DVORONIN_PARSER_DESC'),
    "ICON" => '/images/icon.gif',
    "SORT" => 10,
    "PATH" => array(
        "ID" => 'dvoronin',
        "NAME" => Loc::getMessage('DVORONIN_PARSER_DESC_GROUP'),
        "SORT" => 10,
        "CHILD" => array(
            "ID" => 'dparsers',
            "NAME" => Loc::getMessage('DVORONIN_PARSER_DESC_DIR'),
            "SORT" => 10
        )
    ),
);
?>