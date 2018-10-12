<?
global $USER;
$rsUsers = CUser::GetList(
    $by = array("UF_SORT"=>"asc","NAME"=>"desc"),
    $order = "desc",
    Array("GROUPS_ID" => Array(5), 'ACTIVE' => 'Y','!ID'=>'1'),
    Array("FIELDS" => array(
        'ID',
        'NAME',
        'SECOND_NAME',
        'LAST_NAME',
        'PERSONAL_PHOTO',
        'PERSONAL_PHONE',
        'WORK_PHONE',
    )
    )
);
/**
 * Bitrix can not search by array of ID.. We need to make it "by-hand"
 */
while ($arUser = $rsUsers->Fetch()) {
    if($arUser['ID'] != 8)
        $arSpecUser[] = $arUser;
}
$arResult['ITEMS'] = $arSpecUser;

$arResult['OBJECTS_COUNT'] = 0;

$users = array();
foreach($arResult['ITEMS'] as $arItem)
    $users[$arItem['ID']] = array();

$section = \LIblock::getSectionInfo('objects', 'active');

$sectionId = empty($section['ID']) ? -1 : (int) $section['ID'];

$res = \CIBlockElement::GetList(
    array(),
    array(
        'SECTION_ID' => $sectionId,
        'IBLOCK_ID' => \LIblock::getId('objects'),
        'PROPERTY_RIELTOR' => array_keys($users),
        'ACTIVE' => 'Y',
        'INCLUDE_SUBSECTIONS' => 'Y'
    ),
    false,
    false,
    array('ID', 'PROPERTY_RIELTOR')
);

while($row = $res->Fetch())
{
    if(!empty($row['PROPERTY_RIELTOR_VALUE']))
        $users[$row['PROPERTY_RIELTOR_VALUE']][] = $row['ID'];
}

foreach($users as $k => $v)
    $users[$k] = count($v);

foreach($arResult['ITEMS'] as $k => $v)
{
    $arResult['ITEMS'][$k]['OBJECTS_COUNT'] = isset($users[$v['ID']]) ? (int) $users[$v['ID']] : 0;
}