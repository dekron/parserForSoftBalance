<?php
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

$siteId = isset($_REQUEST['SITE_ID']) && is_string($_REQUEST['SITE_ID']) ? $_REQUEST['SITE_ID'] : '';
$siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $siteId), 0, 2);
if (!empty($siteId) && is_string($siteId)) {
    define('SITE_ID', $siteId);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

CBitrixComponent::includeComponentClass('dvoronin:parser');

$component = new CDvoroninParser();

switch ($_REQUEST['type']) {
    case 'setUsers':
        $ib_id = $component->getIB('parserib', 'parsertype');
        $data = $_REQUEST['data'];
        foreach ($data as $userType) {
            $new_id = $component->getIB($userType, 'parsertype');
            $component->addIBUserProperty($userType, $ib_id, 'E', '', $new_id);
        }
        break;
    case 'setRests':
        $ib_id = $component->getIB('parserib', 'parsertype');

        $data = $_REQUEST['data'];
        foreach ($data['rests'] as $rest) {
            foreach ($data['users'] as $usertype) {
                $user_id = $component->getIB($usertype, 'parsertype');
                $component->addIBUserProperty($rest['name'], $user_id, 'S', $rest['default']);
            }
        }
        break;
    case 'addElements':
        $ib_id = $component->getIB('parserib', 'parsertype');

        //перебираем и добавляем свойства
        $props = [];
        foreach ($data['props'] as $propUserType) {
            //получаем тип свойства
            $userType = $propUserType['name'];
            $user_id = $component->getIB($userType, 'parsertype');

            $values = [];
            foreach ($propUserType['values'] as $value) {
                $values[$value['name']] = $value['value'];
            }

            $props[$userType] = $component->addElToIB($user_id, $data['id'] . '_' . $userType, $values);
        }
        //добавляем сам элемент с id свойств
        $component->addElToIB($ib_id, $data['id'], $props);
        break;
}

