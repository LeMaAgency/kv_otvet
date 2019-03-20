<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$yml = new \DomSakhExport(\LIblock::getId('objects'), array(
    'storageDir' => '/bitrix/catalog_export/dom_sakh',
    //'additionalData' => array('siteTitle' => 'KvOtvet'),
));

$rentAndRealtyTypes = array();
$tmp = LIblock::getPropEnumValues(LIblock::getPropId('objects', 'RENT_TYPE'));
foreach ($tmp as $code => $data)
    $rentAndRealtyTypes[$data['ID']] = $code;
unset($tmp);

$sections = \LIblock::getSectionsByIblockCode('objects', false);

$yml->loadData(array(
    'arSelect' => array(
        '*',
        'DETAIL_PAGE_URL',
        'PROPERTY_PRICE',
        'PROPERTY_RIELTOR',
        'PROPERTY_MORE_PHOTO',
        'PROPERTY_STAGE',
        'PROPERTY_STAGES_COUNT',
        'PROPERTY_CITY',
        'PROPERTY_REGION',
        'PROPERTY_STREET',
        'PROPERTY_HOUSE_NUMBER',
        'PROPERTY_BUILDING_NUMBER',
        'PROPERTY_CADASTRAL_NUMBER',
        'PROPERTY_PROPOSED_ROOMS_COUNT',
        'PROPERTY_RENT_TYPE',
        'PROPERTY_MATERIAL',
        'PROPERTY_YEAR',
        'PROPERTY_LIFT',
        'PROPERTY_SEP_ENTRANCE',
        'PROPERTY_OWNER',
        'PROPERTY_LAYOUT_TYPE',
        'PROPERTY_BATHROOM',
        'PROPERTY_ROOMS_COUNT',
        'PROPERTY_OFFERED_ROOMS_COUNT',
        'PROPERTY_BATHROOM_COUNT',
        'PROPERTY_SQUARE',
        'PROPERTY_SQUARE_RESIDENT',
        'PROPERTY_SQUARE_KITCHEN',
        'PROPERTY_SQUARE_LAND',
        'PROPERTY_LAND_STATUS',
        'PROPERTY_BALCONIES_COUNT',
        'PROPERTY_LOGGIAS_COUNT',
        'PROPERTY_REPAIR_TYPE',
        'PROPERTY_SIDE',
        'PROPERTY_CLASS_TYPE',
        'PROPERTY_PARKING',
        'PROPERTY_SECURITY_CONCIERGE',
        'PROPERTY_PHONE',
        'PROPERTY_INTERNET',
        'PROPERTY_WATER_SUPPLY',
        'PROPERTY_HEATING',
        'PROPERTY_COLD_WATER',
        'PROPERTY_HOT_WATER',
        'PROPERTY_SEWERAGE',
        'PROPERTY_ELECTRIC',
        'PROPERTY_GAZ',
        'PROPERTY_SAUNA',
        'PROPERTY_GARAGE',
        'PROPERTY_CLOSED_TERRITORY',
        'PROPERTY_SECURITY_ALARM',
        'PROPERTY_FIRE_ALARM',
        'PROPERTY_FIRE_EXT_SYSTEM',
        'PROPERTY_SECURITY_VIDEO',
        'PROPERTY_HAVINGS_TYPE',
        'PROPERTY_USER_NAME',
        'PROPERTY_USER_PHONE',
        'PROPERTY_USER_EMAIL',
        'PROPERTY_TV',
        'PROPERTY_HYPOTHEC',
        'PROPERTY_LOT_CATEGORIES',
        'PROPERTY_LOT_HAVINGS_TYPE',
    ),
    'filter' => array('ACTIVE' => 'Y', 'SECTION_CODE' => 'active', 'INCLUDE_SUBSECTIONS' => 'Y', 'PROPERTY_ADD_OBJECT_TO_EXPORT_VALUE' => 'Y'),
    'callback' => function ($data) use ($rentAndRealtyTypes, $sections) {
        foreach ($data as $k => $v) {
            if ($k[0] === '~' || false !== strpos($k, '_VALUE_ID'))
                unset($data[$k]);
        }
        if (!empty($data['PROPERTY_RIELTOR_VALUE'])) {
            $rsUsers = \CUser::GetList(
                $by = array("UF_SORT"=>"asc","NAME"=>"desc"),
                $order = "desc",
                Array("GROUPS_ID" => Array(5), 'ACTIVE' => 'Y', 'ID' => $data['PROPERTY_RIELTOR_VALUE']),
                Array(
                    "FIELDS" => array(
                        'ID',
                        'WORK_PHONE',
                        'EMAIL',
                        'NAME',
                    )
                )
            );
            if ($arUser = $rsUsers->Fetch()) {
                $data['work_phone'] = $arUser['WORK_PHONE'];
                $data['name'] = $arUser['NAME']; //$arUser['WORK_PHONE'];
                $data['email'] = $arUser['EMAIL'];
            }
        }

        $types = array(
            'куплю' => 'покупка',
            'продам' => 'продажа',
            'сдам' => 'сдача',
            'сниму' => 'съем',
        );
        $type = mb_strtolower($data['PROPERTY_RENT_TYPE_VALUE'], 'UTF-8');
        $data['type'] = isset($types[$type]) ? $types[$type] : null;

        $categories = array(
            'квартиры' => 'квартира',
            'дома' => 'дом',
            'дачи' => 'дом',
            'комнаты' => 'комната',
            'земельный участок' => 'земля',
            'офисы' => 'бизнес',
            'торговые площади' => 'бизнес',
            'здания' => 'бизнес',
        );


        if (isset($sections[$data['IBLOCK_SECTION_ID']])) {
            $category = mb_strtolower($sections[$data['IBLOCK_SECTION_ID']]['NAME'], 'UTF-8');
            $data['category'] = isset($categories[$category]) ? $categories[$category] : null;
        } else {
            unset($data);
            return;
        }

        //Get rent type
        $rentType = null;
        if (empty($rentAndRealtyTypes[$data['PROPERTY_RENT_TYPE_ENUM_ID']]))
            $rentType = \CUtil::translit($data['PROPERTY_RENT_TYPE_VALUE'], 'ru');
        else
            $rentType = $rentAndRealtyTypes[$data['PROPERTY_RENT_TYPE_ENUM_ID']];

        $data['url'] = '/catalog/' . $sections[$data['IBLOCK_SECTION_ID']]['CODE'] . '/' . $rentType . '/' . $data['CODE'];
        $data['creation-date'] = date('c', $data['DATE_CREATE_UNIX']);
        $data['last-update-date'] = date('c', $data['TIMESTAMP_X_UNIX']);
        $data['country'] = 'Россия';
        $data['locality-name'] = $data['PROPERTY_CITY_VALUE'];
        $data['sub-locality-name'] = $data['PROPERTY_REGION_VALUE'];

        $arTempStreetVal = [
            'проспект' => 'пр-т',
            'проезд' => 'пр.',
            'переулок' => 'пер.',
            'набережная' => 'наб.',
            'площадь' => 'пл.',
            'бульвар' => 'б-р',
            'линия' => 'линия',
            'шоссе' => 'ш.',
        ];
        $arStreetVal = explode(' ',trim($data['PROPERTY_STREET_VALUE']));

        $strStreetDefinition = mb_strtolower($arStreetVal[0]);
        unset($arStreetVal[0]);

        $strStreetName = implode(' ', $arStreetVal);

        $strStreetVal =  'ул. ' . $data['PROPERTY_STREET_VALUE'];

        if(isset($arTempStreetVal[$strStreetDefinition])){
            $strStreetVal = $arTempStreetVal[$strStreetDefinition].' '.$strStreetName;
        }

        $data['address'] = trim($strStreetVal . ', ' . $data['PROPERTY_HOUSE_NUMBER_VALUE']);

        $data['images'] = array();
        if (!empty($data['DETAIL_PICTURE']))
            $data['images'][] = \CFile::GetPath($data['DETAIL_PICTURE']);
        if (!empty($data['PROPERTY_MORE_PHOTO_VALUE'])) {
            foreach ($data['PROPERTY_MORE_PHOTO_VALUE'] as $imgId)
                $data['images'][] = \CFile::GetPath($imgId);
        }
        unset($data['DETAIL_PICTURE'], $data['PROPERTY_MORE_PHOTO_VALUE']);

        $landTypes = array(
            'ижс' => 'ИЖС',
            'снт' => 'сельское хозяйство',
            'садоводство' => 'сельское хозяйство',
            'коммерческая застройка' => 'коммерческая застройка',
            'сельское хозяйство' => 'сельское хозяйство',
            'дачное' => 'дачное',
            'подсобное хозяйство' => 'подсобное хозяйство',
        );
        $landType = mb_strtolower($data['PROPERTY_LOT_CATEGORIES_VALUE'], 'UTF-8');
        $data['land-type'] = isset($landTypes[$landType]) ? $landTypes[$landType] : null;

        $propertyTypes = array(
            'собственность' => 'в собственности',
            'аренда' => 'в аренде',
        );
        $propertyType = mb_strtolower($data['PROPERTY_LOT_HAVINGS_TYPE_VALUE'], 'UTF-8');
        $data['property-type'] = isset($propertyTypes[$propertyType]) ? $propertyTypes[$propertyType] : null;

        if ($data['PROPERTY_BALCONIES_COUNT_VALUE'] == 1)
            $data['balcony'] = 'один';
        elseif ($data['PROPERTY_BALCONIES_COUNT_VALUE'] > 1)
            $data['balcony'] = 'больше одного';
        else
            $data['balcony'] = $data['PROPERTY_LOGGIAS_COUNT_VALUE'] > 0 ? 'лоджия' : null;


        $data['alarm'] = $data['PROPERTY_SECURITY_CONCIERGE_VALUE'] == 'Y' || $data['PROPERTY_SECURITY_ALARM_VALUE'] == 'Y';

        return $data;
    },
));

$yml->showData(array(
    'sendHeader' => true,
    'fields' => array(
        'type' => 'type',
        'category' => 'category',
        'property-type' => 'property-type',
        'creation-date' => 'creation-date',
        'last-update-date' => 'last-update-date',
        'living-space' => 'PROPERTY_SQUARE_RESIDENT_VALUE',
        'kitchen-space' => 'PROPERTY_SQUARE_KITCHEN_VALUE',
        //'land-space' => 'PROPERTY_SQUARE_LAND_VALUE',
        'built-year' => 'PROPERTY_YEAR_VALUE',
        'land-type' => 'land-type',
        'CadastralNumber' => 'PROPERTY_CADASTRAL_NUMBER_VALUE',
        'renovation' => 'PROPERTY_REPAIR_TYPE_VALUE',
        'rooms' => 'PROPERTY_ROOMS_COUNT_VALUE',
        'rooms-offered' => 'PROPERTY_OFFERED_ROOMS_COUNT_VALUE',
        'floor' => 'PROPERTY_STAGE_VALUE',
        'floors-total' => 'PROPERTY_STAGES_COUNT_VALUE',
        'bathroom-unit' => 'PROPERTY_BATHROOM_VALUE',
        'building-series' => 'PROPERTY_LAYOUT_TYPE_VALUE',
        'building-type' => 'PROPERTY_MATERIAL_VALUE',
        'balcony' => 'balcony',
        'news' => 'PROPERTY_SIDE_VALUE',
        'heating' => 'PROPERTY_HEATING_VALUE',
        'water' => 'PROPERTY_WATER_SUPPLY_VALUE',
        'sewerage' => 'PROPERTY_SEWERAGE_VALUE',
        'electricity' => 'PROPERTY_ELECTRIC_VALUE',
    ),
    'boolListFields' => array(
        'phone' => 'PROPERTY_PHONE_VALUE',
        'internet' => 'PROPERTY_INTERNET_VALUE',
        'television' => 'PROPERTY_TV_VALUE',
        'alarm' => 'alarm',
        'gas' => 'PROPERTY_GAZ_VALUE',
        'mortgage' => 'PROPERTY_HYPOTHEC_VALUE',
    ),
));