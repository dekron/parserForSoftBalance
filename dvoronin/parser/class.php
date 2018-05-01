<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CDvoroninParser extends CBitrixComponent
{
    public function createIB($iblockcode, $iblocktype)
    {
        if (CModule::IncludeModule('iblock')) {
            $iblocktype = self::getIBType($iblocktype);

            $obIblock = new CIBlock;
            $arFields = Array(
                "NAME" => $iblockcode,
                "CODE" => $iblockcode,
                "ACTIVE" => "Y",
                "IBLOCK_TYPE_ID" => $iblocktype,
                "SITE_ID" => SITE_ID
            );
            return $obIblock->Add($arFields);
        }
    }

    public function getIBType($iblocktype)
    {
        if (CModule::IncludeModule('iblock')) {
            // $iblocktype = "parsertype";

            $db_iblock_type = CIBlockType::GetList(array(), array('=ID' => $iblocktype));
            while ($ar_iblock_type = $db_iblock_type->Fetch()) {
                return $ar_iblock_type['ID'];
            }

            return self::CreateIBType($iblocktype);
        }
    }

    public function CreateIBType($iblocktype)
    {
        if (CModule::IncludeModule('iblock')) {

            $obIBlockType = new CIBlockType;
            $arFields = Array(
                "ID" => $iblocktype,
                "SECTIONS" => "Y",
                "LANG" => Array(
                    "ru" => Array(
                        "NAME" => $iblocktype,
                    )
                )
            );
            return $obIBlockType->Add($arFields);
        }
    }

    public function getIB($iblockcode, $iblocktype)
    {
        if (CModule::IncludeModule('iblock')) {
            $res = CIBlock::GetList(
                Array(),
                Array(
                    'TYPE' => $iblocktype,//'parsertype',
                    'SITE_ID' => SITE_ID,
                    "CODE" => $iblockcode//'parserib'
                ), true
            );
            while ($ar_res = $res->Fetch()) {
                return $ar_res['ID'];
            }

            return self::createIB($iblockcode, $iblocktype);
        }
    }

    public function addIBUserProperty($code, $ib_id, $type, $default_value, $ib_id_link = false, $user_type = false)
    {
        if (CModule::IncludeModule('iblock')) {
            $ibp = new CIBlockProperty;

            //проверяем наличие свойства
            $properties = $ibp::GetList(Array(), Array("IBLOCK_ID" => $ib_id, "CODE" => $code));
            while ($prop_fields = $properties->GetNext()) {
                return true;
            }

            // можно еще добавить выше обновление свойства
            // иначе создаем его
            $arFields = Array(
                "NAME" => $code,
                "ACTIVE" => "Y",
                "SORT" => 1,
                "CODE" => $code,
                "PROPERTY_TYPE" => $type,
                "DEFAULT_VALUE" => $default_value,
                "IBLOCK_ID" => $ib_id
            );
            if ($user_type) {
                $arFields['USER_TYPE'] = $user_type;
            }
            if ($ib_id_link) {
                $arFields['LINK_IBLOCK_ID'] = $ib_id_link;
            }
            $ibp->Add($arFields);
        }
    }

    ////////////////////////
    // РАБОТАЕМ С ЭЛЕМЕНТАМИ
    ////////////////////////
    public function addElToIB($ib_id, $name, $props = array())
    {
        if (CModule::IncludeModule('iblock')) {

            $elem = self::getEl($name);
            if (isset($elem)) {
                foreach ($props as $key => $value) {
                    self::updateElProp($elem, $key, $value);
                }
                return $elem;
            } else {
                $el = new CIBlockElement;

                $arLoadProductArray = Array(
                    "IBLOCK_ID" => $ib_id,
                    "NAME" => $name,
                    "ACTIVE" => "Y"
                );

                if ($props) {
                    $arLoadProductArray['PROPERTY_VALUES'] = $props;
                }

                return $el->Add($arLoadProductArray);
            }

        }
    }

    public function getEl($name)
    {
        if (CModule::IncludeModule('iblock')) {
            $arSelect = Array("ID", "NAME");
            $arFilter = Array("NAME" => $name);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
            while ($ob = $res->GetNextElement()) {
                return $ob->GetFields()['ID'];
            }
        }
    }

    public function updateEl($elem_id, $name, $props = false)
    {
        if (CModule::IncludeModule('iblock')) {
            $el = new CIBlockElement;

            $arLoadProductArray = Array(
                "NAME" => $name,
                "ACTIVE" => "Y"
            );

            if ($props) {
                $arLoadProductArray['PROPERTY_VALUES'] = $props;
            }
            return $el->Update($elem_id, $arLoadProductArray);
        }
    }

    public function updateElProp($elem_id, $code, $value)
    {
        if (CModule::IncludeModule('iblock')) {
            $el = new CIBlockElement;

            $PROPERTY_CODE = $code;  // код свойства
            $PROPERTY_VALUE = $value;  // значение свойства

            // определяем IBLOCK_ID
            $dbr = CIBlockElement::GetList(array(), array("=ID" => $elem_id), false, false, array("ID", "IBLOCK_ID"));
            if ($dbr_arr = $dbr->Fetch()) {
                $IBLOCK_ID = $dbr_arr["IBLOCK_ID"];
                $el::SetPropertyValues($elem_id, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
            }
        }
    }

    //Отрисовка компонтента
    public function executeComponent()
    {
        if ($this->startResultCache()) {
            $this->includeComponentTemplate();
        }
        return $this->arResult["Y"];
    }
} ?>