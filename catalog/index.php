<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Каталог');

$rootDir = SITE_DIR . 'catalog';
$currentDir = \Lema\Common\Request::get()->getRequestedPageDirectory();
$inRootDir = $currentDir == $rootDir;

if (empty($GLOBALS['arrFilter']))
    $GLOBALS['arrFilter'] = array();

/**
 * Ordering sort for filter items
 */
$filterFields = array(
    array('key' => 'ROOMS_COUNT', 'type' => 'property', 'expanded' => false),
    array('key' => 'PRICE', 'type' => 'property', 'expanded' => false),
    array('key' => 'REGION', 'type' => 'property', 'expanded' => false),
    array('key' => 'ID', 'type' => 'field', 'expanded' => false),
    array('key' => 'STAGE', 'type' => 'property', 'expanded' => true),
    array('key' => 'STAGES_COUNT', 'type' => 'property', 'expanded' => true),
);

/**
 * Load rent & realty types from iblock properties
 */
$rentAndRealtyTypes = array();
$tmp = \LIblock::getPropEnumValues(LIblock::getPropId('objects', 'RENT_TYPE'));
foreach ($tmp as $code => $data)
    $rentAndRealtyTypes[$code] = $data['VALUE'];
unset($tmp);

if ($inRootDir) {
    /**
     * We need to check for showing elements of "public" section only
     */
    $sections = LIblock::getSectionsByIblockCode('objects');
    $rootSectionId = isset($sections['active']) ? $sections['active']['ID'] : 0;
    if (!empty($rootSectionId)) {
        $GLOBALS['arrFilter']['SECTION_ID'] = array();
        foreach ($sections as $section) {
            if ($section['IBLOCK_SECTION_ID'] === $rootSectionId)
                $GLOBALS['arrFilter']['SECTION_ID'][] = $section['ID'];
        }
    }
} else {

    /**
     * Add chain items
     */
    $uriParts = explode('/', trim(preg_replace('~^' . $rootDir . '~i', '', $currentDir, 1), '/'));
    $lastUrl = $rootDir;

    if (!empty($uriParts[0]) && in_array($uriParts[0], array('kvartiry-komnaty', 'doma-dachi-zemelnyy_uchastok'))) {
        $currentSectionCode = array_shift($uriParts);
        $GLOBALS['arrFilter']['SECTION_CODE'] = explode('-', trim($currentSectionCode));
    }

    $filterFields = array(
        array('key' => 'ROOMS_COUNT', 'type' => 'property', 'expanded' => false),
        array('key' => 'PRICE', 'type' => 'property', 'expanded' => false),
        array('key' => 'REGION', 'type' => 'property', 'expanded' => false),
        array('key' => 'ID', 'type' => 'field', 'expanded' => false),
        array('key' => 'SQUARE_LAND', 'type' => 'property', 'expanded' => false),
        array('key' => 'SQUARE', 'type' => 'property', 'expanded' => false),
        array('key' => 'STAGE', 'type' => 'property', 'expanded' => true),
        array('key' => 'STAGES_COUNT', 'type' => 'property', 'expanded' => true),
        array('key' => 'LOT_HAVINGS_TYPE', 'type' => 'property', 'expanded' => true),
        array('key' => 'LOT_CATEGORIES', 'type' => 'property', 'expanded' => true),
        array('key' => 'HEATING', 'type' => 'property', 'expanded' => true),
        array('key' => 'WATER_SUPPLY', 'type' => 'property', 'expanded' => true),
        array('key' => 'SEWERAGE', 'type' => 'property', 'expanded' => true),
        array('key' => 'ELECTRIC', 'type' => 'property', 'expanded' => true),
    );

    if (!empty($uriParts)) {
        $currentSectionCode = array_shift($uriParts);

        $section = \LIblock::getSectionInfo('objects', $currentSectionCode);
        if (!empty($section)) {
            $lastUrl .= '/' . $section['CODE'];
            $APPLICATION->AddChainItem($section['NAME'], $lastUrl . '/');
        }
        switch ($currentSectionCode) {
            case 'kvartiry':
                $showFields = array(
                    'LAYOUT_TYPE', 'MATERIAL', 'YEAR', 'SQUARE_RESIDENT', 'SQUARE_KITCHEN', 'SIDE', 'BATHROOM',
                    'BATHROOM_COUNT', 'BALCONIES_COUNT', 'LOGGIAS_COUNT', 'PHONE', 'TV', 'INTERNET', 'LIFT_FLAG',
                    'PARKING', 'SECURITY_CONCIERGE', 'GAZ', 'HYPOTHEC', 'HAVINGS_TYPE',
                );
                /*$filterFields = array(
                    array('key' => 'ROOMS_COUNT', 'type' => 'property', 'expanded' => false),
                    array('key' => 'PRICE', 'type' => 'property', 'expanded' => false),
                    array('key' => 'REGION', 'type' => 'property', 'expanded' => false),
                    array('key' => 'ID', 'type' => 'field', 'expanded' => false),
                    array('key' => 'STAGE', 'type' => 'property', 'expanded' => true),
                    array('key' => 'STAGES_COUNT', 'type' => 'property', 'expanded' => true),
                );*/
                break;
            case 'komnaty':
                $showFields = array(
                    'OFFERED_ROOMS_COUNT', 'LAYOUT_TYPE', 'MATERIAL', 'YEAR', 'SIDE', 'BATHROOM', 'BALCONIES_COUNT',
                    'LOGGIAS_COUNT', 'REPAIR_TYPE', 'PHONE', 'TV', 'INTERNET', 'LIFT_FLAG', 'PARKING', 'GAZ',
                    'SECURITY_CONCIERGE', 'HAVINGS_TYPE', 'HYPOTHEC',
                );
                /*$filterFields = array(
                    array('key' => 'ROOMS_COUNT', 'type' => 'property', 'expanded' => false),
                    array('key' => 'PRICE', 'type' => 'property', 'expanded' => false),
                    array('key' => 'REGION', 'type' => 'property', 'expanded' => false),
                    array('key' => 'ID', 'type' => 'field', 'expanded' => false),
                    array('key' => 'STAGE', 'type' => 'property', 'expanded' => true),
                    array('key' => 'STAGES_COUNT', 'type' => 'property', 'expanded' => true),
                );*/
                break;
            case 'dachi':
                $showFields = array(
                    'LIFE_MASSIV_SNT', 'MATERIAL', 'YEAR', 'SQUARE_RESIDENT', 'SQUARE_KITCHEN', 'BATHROOM',
                    'BATHROOM_COUNT', 'BALCONIES_COUNT', 'LOGGIAS_COUNT', 'PHONE', 'TV', 'INTERNET', 'ELECTRIC',
                    'HEATING', 'WATER_SUPPLY', 'SEWERAGE', 'GAZ', 'SECURITY_CONCIERGE', 'FIRE_ALARM', 'SECURITY_VIDEO',
                    'GARAGE', 'SAUNA', 'HYPOTHEC', 'HAVINGS_TYPE', 'LOT_HAVINGS_TYPE', 'LOT_CATEGORIES',
                );
                /*$filterFields = array(
                    array('key' => 'ROOMS_COUNT', 'type' => 'property', 'expanded' => false),
                    array('key' => 'PRICE', 'type' => 'property', 'expanded' => false),
                    array('key' => 'REGION', 'type' => 'property', 'expanded' => false),
                    array('key' => 'ID', 'type' => 'field', 'expanded' => false),
                    array('key' => 'SQUARE_LAND', 'type' => 'property', 'expanded' => false),
                    array('key' => 'SQUARE', 'type' => 'property', 'expanded' => false),
                );*/
                break;
            case 'doma':
                $showFields = array(
                    'LIFE_MASSIV_SNT', 'MATERIAL', 'YEAR', 'SQUARE_RESIDENT', 'SQUARE_KITCHEN', 'BATHROOM',
                    'BATHROOM_COUNT', 'BALCONIES_COUNT', 'LOGGIAS_COUNT', 'PHONE', 'TV', 'INTERNET', 'ELECTRIC',
                    'HEATING', 'WATER_SUPPLY', 'SEWERAGE', 'GAZ', 'SECURITY_CONCIERGE', 'FIRE_ALARM', 'SECURITY_VIDEO',
                    'GARAGE', 'SAUNA', 'HYPOTHEC', 'HAVINGS_TYPE', 'LOT_HAVINGS_TYPE', 'LOT_CATEGORIES',
                );
                /*$filterFields = array(
                    array('key' => 'ROOMS_COUNT', 'type' => 'property', 'expanded' => false),
                    array('key' => 'PRICE', 'type' => 'property', 'expanded' => false),
                    array('key' => 'REGION', 'type' => 'property', 'expanded' => false),
                    array('key' => 'ID', 'type' => 'field', 'expanded' => false),
                    array('key' => 'SQUARE_LAND', 'type' => 'property', 'expanded' => false),
                    array('key' => 'SQUARE', 'type' => 'property', 'expanded' => false),
                    array('key' => 'LOT_CATEGORIES', 'type' => 'property', 'expanded' => true),
                    array('key' => 'HEATING', 'type' => 'property', 'expanded' => true),
                    array('key' => 'WATER_SUPPLY', 'type' => 'property', 'expanded' => true),
                    array('key' => 'SEWERAGE', 'type' => 'property', 'expanded' => true),
                );*/
                break;
            case 'zemelnyy_uchastok':
                $showFields = array(
                    'LIFE_MASSIV_SNT', 'ELECTRIC', 'SEWERAGE', 'WATER_SUPPLY', 'GAZ', 'LOT_HAVINGS_TYPE',
                    'LOT_CATEGORIES',
                );
                /*$filterFields = array(
                    array('key' => 'PRICE', 'type' => 'property', 'expanded' => false),
                    array('key' => 'REGION', 'type' => 'property', 'expanded' => false),
                    array('key' => 'ID', 'type' => 'field', 'expanded' => false),
                    //array('key' => 'SQUARE_LAND', 'type' => 'property', 'expanded' => false),
                    array('key' => 'SQUARE', 'type' => 'property', 'expanded' => false),
                    array('key' => 'LOT_HAVINGS_TYPE', 'type' => 'property', 'expanded' => true),
                    array('key' => 'LOT_CATEGORIES', 'type' => 'property', 'expanded' => true),
                    array('key' => 'ELECTRIC', 'type' => 'property', 'expanded' => true),
                );*/
                break;
            case 'ofisy':
                $showFields = array(
                    'CLASS_TYPE', 'MATERIAL', 'YEAR', 'SEP_ENTRANCE', 'PHONE', 'TV', 'INTERNET', 'LIFT',
                    'SECURITY_CONCIERGE', 'SECURITY_ALARM', 'FIRE_ALARM', 'FIRE_EXT_SYSTEM', 'SECURITY_VIDEO',
                    'HYPOTHEC',
                );
                break;
            case 'torgovye_ploshchadi':
                $showFields = array(
                    'SHOPPING_CENTER', 'LIFE_MASSIV_SNT', 'MATERIAL', 'YEAR', 'SEP_ENTRANCE', 'PHONE', 'TV', 'INTERNET',
                    'GAZ', 'PARKING', 'SECURITY_ALARM', 'FIRE_ALARM', 'FIRE_EXT_SYSTEM', 'SECURITY_VIDEO', 'HYPOTHEC',
                );
                break;
            case 'zdaniya':
                $showFields = array(
                    'SHOPPING_CENTER', 'LIFE_MASSIV_SNT', 'MATERIAL', 'YEAR', 'PHONE', 'TV', 'INTERNET', 'HEATING',
                    'COLD_WATER', 'HOT_WATER', 'SEWERAGE', 'ELECTRIC', 'GAZ', 'PARKING', 'CLOSED_TERRITORY',
                    'SECURITY_ALARM', 'FIRE_ALARM', 'FIRE_EXT_SYSTEM', 'SECURITY_VIDEO', 'HYPOTHEC',
                );
                break;
        }

        if (!empty($uriParts) && is_array($uriParts)) {
            foreach ($uriParts as $uriPart) {
                if (isset($rentAndRealtyTypes[$uriPart])) {
                    $lastUrl .= '/' . $uriPart;
                    $APPLICATION->AddChainItem($rentAndRealtyTypes[$uriPart], $lastUrl . '/');
                }
            }
        }
    }
}

/**
 * Filter by sell types
 */
$typeFilter = array();
if (!empty($rentAndRealtyTypes['kuplyu']))
    $typeFilter[] = $rentAndRealtyTypes['kuplyu'];
if (!empty($rentAndRealtyTypes['prodam']))
    $typeFilter[] = $rentAndRealtyTypes['prodam'];

$GLOBALS['arrFilter']['PROPERTY_RENT_TYPE_VALUE'] = $typeFilter;

?>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <? \Lema\Components\Breadcrumbs::inc('breadcrumbs'); ?>
            </div>
        </div>
    </div>

<? $APPLICATION->IncludeComponent(
    "lema:news",
    "catalog",
    array(
        "DISPLAY_DATE" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "SEF_MODE" => "Y",
        "AJAX_MODE" => "N",
        "IBLOCK_TYPE" => "realty",
        "IBLOCK_ID" => "2",
        'PARENT_SECTION_CODE' => (isset($currentSectionCode) ? $currentSectionCode : null),
        "NEWS_COUNT" => "6",
        "USE_SEARCH" => "N",
        "USE_RSS" => "N",
        "USE_RATING" => "N",
        "USE_CATEGORIES" => "N",
        "USE_REVIEW" => "N",
        "USE_FILTER" => "Y",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        "SORT_BY2" => "SORT",
        "SORT_ORDER2" => "ASC",
        "CHECK_DATES" => "Y",
        "PREVIEW_TRUNCATE_LEN" => "",
        "LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
        "LIST_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "LIST_PROPERTY_CODE" => array(
            0 => "ROOMS_COUNT",
            1 => "PRICE",
            2 => "ADDRESS",
            3 => "YEAR",
            4 => "MAP",
            5 => "PLACEMENT",
            6 => "LAYOUT",
            7 => "SQUARE",
            8 => "SIDE",
            9 => "REALTY_TYPE",
            10 => "STAGE",
            11 => "STAGES_COUNT",
        ),
        "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
        "DISPLAY_NAME" => "Y",
        "META_KEYWORDS" => "-",
        "META_DESCRIPTION" => "-",
        "BROWSER_TITLE" => "-",
        "DETAIL_SET_CANONICAL_URL" => "Y",
        "DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
        "DETAIL_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "DETAIL_PROPERTY_CODE" => $showFields,
        "DETAIL_DISPLAY_TOP_PAGER" => "Y",
        "DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
        "DETAIL_PAGER_TITLE" => "Страница",
        "DETAIL_PAGER_TEMPLATE" => "",
        "DETAIL_PAGER_SHOW_ALL" => "Y",
        "DETAIL_STRICT_SECTION_CHECK" => "Y",
        "SET_TITLE" => "Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "ADD_ELEMENT_CHAIN" => "Y",
        "SET_LAST_MODIFIED" => "Y",
        "PAGER_BASE_LINK_ENABLE" => "Y",
        "SET_STATUS_404" => "Y",
        "SHOW_404" => "Y",
        "MESSAGE_404" => "",
        "PAGER_BASE_LINK" => "",
        "PAGER_PARAMS_NAME" => "arrPager",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "USE_PERMISSIONS" => "N",
        "GROUP_PERMISSIONS" => array(
            0 => "1",
        ),
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Элементы",
        "PAGER_SHOW_ALWAYS" => "Y",
        "PAGER_TEMPLATE" => "pagination",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "Y",
        "FILTER_NAME" => "arrFilter",
        "FILTER_FIELD_CODE" => array(
            0 => "ID",
            1 => "",
        ),
        "FILTER_PROPERTY_CODE" => array(
            'RENT_TYPE',
            'ROOMS_COUNT',
            'PRICE',
            'REGION',
            'STAGE',
            'STAGES_COUNT',
            'SQUARE_LAND',
            'SQUARE',
            'LOT_CATEGORIES',
            'HEATING',
            'WATER_SUPPLY',
            'SEWERAGE',
            'LOT_HAVINGS_TYPE',
            'ELECTRIC',
        ),
        'FILTER_ORDER' => $filterFields,
        "NUM_NEWS" => "20",
        "NUM_DAYS" => "30",
        "YANDEX" => "Y",
        "MAX_VOTE" => "5",
        "VOTE_NAMES" => array(
            0 => "0",
            1 => "1",
            2 => "2",
            3 => "3",
            4 => "4",
            5 => "",
        ),
        "CATEGORY_IBLOCK" => "",
        "CATEGORY_CODE" => "CATEGORY",
        "CATEGORY_ITEMS_COUNT" => "5",
        "MESSAGES_PER_PAGE" => "10",
        "USE_CAPTCHA" => "Y",
        "REVIEW_AJAX_POST" => "Y",
        "PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
        "FORUM_ID" => "1",
        "URL_TEMPLATES_READ" => "",
        "SHOW_LINK_TO_FORUM" => "Y",
        "POST_FIRST_MESSAGE" => "Y",
        "SEF_FOLDER" => "/catalog/",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "Y",
        "USE_SHARE" => "N",
        "SHARE_HIDE" => "Y",
        "SHARE_TEMPLATE" => "",
        "SHARE_HANDLERS" => array(
            0 => "delicious",
            1 => "facebook",
            2 => "lj",
            3 => "twitter",
        ),
        "SHARE_SHORTEN_URL_LOGIN" => "",
        "SHARE_SHORTEN_URL_KEY" => "",
        "COMPONENT_TEMPLATE" => "catalog",
        "AJAX_OPTION_ADDITIONAL" => "",
        "STRICT_SECTION_CHECK" => "N",
        "DISPLAY_AS_RATING" => "rating",
        "FILE_404" => "",
        "SEF_URL_TEMPLATES" => array(
            "news" => "",
            "kvartiry-komnaty" => "kvartiry-komnaty/",
            "doma-dachi-zemelnyy_uchastok" => "doma-dachi-zemelnyy_uchastok/",
            "section" => "",
            "detail" => "#SECTION_CODE#/#RENT_TYPE#/#ELEMENT_CODE#/",
            "search" => "search/",
            'realty' => '#SECTION_CODE#/',
            'realty_rent' => '#SECTION_CODE#/#RENT_TYPE#/',
        ),
    ),
    false
); ?>

<? if (!empty($currentSectionCode) && empty($GLOBALS['arrFilter']['SECTION_CODE'])): ?>
    <? if (empty($uriParts[1])): ?>
        <? $APPLICATION->IncludeComponent(
            "bitrix:news.detail",
            "hypothec",
            Array(
                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                "ADD_ELEMENT_CHAIN" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "BROWSER_TITLE" => "-",
                "CACHE_GROUPS" => "Y",
                "CACHE_TIME" => "36000000",
                "CACHE_TYPE" => "A",
                "CHECK_DATES" => "Y",
                "DETAIL_URL" => "",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "DISPLAY_DATE" => "Y",
                "DISPLAY_NAME" => "Y",
                "DISPLAY_PICTURE" => "Y",
                "DISPLAY_PREVIEW_TEXT" => "Y",
                "DISPLAY_TOP_PAGER" => "N",
                "ELEMENT_CODE" => "hypothec",
                "ELEMENT_ID" => "",
                "FIELD_CODE" => array("", ""),
                "IBLOCK_ID" => "7",
                "IBLOCK_TYPE" => "content",
                "IBLOCK_URL" => "",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                "MESSAGE_404" => "",
                "META_DESCRIPTION" => "-",
                "META_KEYWORDS" => "-",
                "PAGER_BASE_LINK_ENABLE" => "N",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_TEMPLATE" => ".default",
                "PAGER_TITLE" => "Страница",
                "PROPERTY_CODE" => array("LIST_ELEMENTS", ""),
                "SET_BROWSER_TITLE" => "N",
                "SET_CANONICAL_URL" => "N",
                "SET_LAST_MODIFIED" => "N",
                "SET_META_DESCRIPTION" => "N",
                "SET_META_KEYWORDS" => "N",
                "SET_STATUS_404" => "N",
                "SET_TITLE" => "N",
                "SHOW_404" => "N",
                "STRICT_SECTION_CHECK" => "N",
                "USE_PERMISSIONS" => "N",
                "USE_SHARE" => "N"
            )
        ); ?>
    <? endif; ?>
<? else: ?>
    <? if ($APPLICATION->GetCurDir() != '/catalog/') { ?>
        <? $APPLICATION->IncludeComponent(
            'bitrix:news.list',
            'advantages_apartments',
            array(
                'DISPLAY_DATE' => 'Y',
                'DISPLAY_NAME' => 'Y',
                'DISPLAY_PICTURE' => 'Y',
                'DISPLAY_PREVIEW_TEXT' => 'Y',
                'AJAX_MODE' => 'N',
                'IBLOCK_TYPE' => 'content',
                'IBLOCK_ID' => '3',
                'NEWS_COUNT' => '20',
                'SORT_BY1' => 'ACTIVE_FROM',
                'SORT_ORDER1' => 'DESC',
                'SORT_BY2' => 'SORT',
                'SORT_ORDER2' => 'ASC',
                'FILTER_NAME' => '',
                'FIELD_CODE' => array(),
                'PROPERTY_CODE' => array(),
                'CHECK_DATES' => 'Y',
                'DETAIL_URL' => '',
                'PREVIEW_TRUNCATE_LEN' => '',
                'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                'SET_TITLE' => 'N',
                'SET_BROWSER_TITLE' => 'N',
                'SET_META_KEYWORDS' => 'N',
                'SET_META_DESCRIPTION' => 'N',
                'SET_LAST_MODIFIED' => 'N',
                'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                'ADD_SECTIONS_CHAIN' => 'N',
                'HIDE_LINK_WHEN_NO_DETAIL' => 'Y',
                'PARENT_SECTION' => '',
                'PARENT_SECTION_CODE' => '',
                'INCLUDE_SUBSECTIONS' => 'Y',
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '36000000',
                'CACHE_FILTER' => 'Y',
                'CACHE_GROUPS' => 'N',
                'DISPLAY_TOP_PAGER' => 'Y',
                'DISPLAY_BOTTOM_PAGER' => 'Y',
                'PAGER_TITLE' => 'Элементы',
                'PAGER_SHOW_ALWAYS' => 'N',
                'PAGER_TEMPLATE' => '',
                'PAGER_DESC_NUMBERING' => 'N',
                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                'PAGER_SHOW_ALL' => 'N',
                'PAGER_BASE_LINK_ENABLE' => 'N',
                'SET_STATUS_404' => 'N',
                'SHOW_404' => 'N',
                'MESSAGE_404' => '',
                'PAGER_BASE_LINK' => '',
                'PAGER_PARAMS_NAME' => 'arrPager',
                'AJAX_OPTION_JUMP' => 'N',
                'AJAX_OPTION_STYLE' => 'Y',
                'AJAX_OPTION_HISTORY' => 'N',
                'AJAX_OPTION_ADDITIONAL' => '',
            )); ?>


        <section class="new-flats">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class=" h2 new-flats__h2"><span>НОВЫЕ ПОСТУПЛЕНИЯ</span></h2>
                    </div>
                </div>
            </div>
            <?php
            $parentSections = array('', '', '');
            ?>
            <? if (isset($currentSectionCode) && $currentSectionCode === 'kvartiry-komnaty'): ?>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3">
                            <a class="new-flats__tab-nav" href="#one-room" data-toggle="tab">
                                <i></i>
                                <span>Однокомнатные</span>
                            </a>
                        </div>
                        <div class="col-sm-3">
                            <a class="new-flats__tab-nav active" href="#two-room" data-toggle="tab">
                                <i></i><i></i>
                                <span>двухкомнатные</span>
                            </a>
                        </div>
                        <div class="col-sm-3">
                            <a class="new-flats__tab-nav" href="#three-room" data-toggle="tab">
                                <div class="new-flats__tab-nav__wrap-icon"><i></i><i></i><i></i></div>
                                <span>трехкомнатные</span>
                            </a>
                        </div>
                        <div class="col-sm-3">
                            <a class="new-flats__tab-nav" href="#four-room" data-toggle="tab">
                                <div class="new-flats__tab-nav__wrap-icon"><i></i><i></i><i></i><i></i></div>
                                <span>четырехкомнатные</span>
                            </a>
                        </div>
                    </div>
                </div>
            <? else: ?>
                <?php
                $parentSections = array('doma', 'dachi', 'zemelnyy_uchastok');
                ?>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4">
                            <a class="new-flats__tab-nav" href="#one-room" data-toggle="tab">
                                <span>Дома</span>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a class="new-flats__tab-nav active" href="#two-room" data-toggle="tab">
                                <span>Дачи</span>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a class="new-flats__tab-nav" href="#three-room" data-toggle="tab">
                                <div class="new-flats__tab-nav__wrap-icon"></div>
                                <span>Земельные участки</span>
                            </a>
                        </div>
                    </div>
                </div>
            <? endif; ?>

            <?
            global $roomNewElementFilter;
            $sectionCodes = explode('-', $currentSectionCode);
            ?>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="one-room">
                    <div class="container">
                        <div class="row">
                            <?php
                            $roomNewElementFilter = array(
                                'PROPERTY_RENT_TYPE_VALUE' => $typeFilter,
                                'SECTION_CODE' => $sectionCodes,
                            );

                            if (empty($parentSections[0]))
                                $roomNewElementFilter['=PROPERTY_ROOMS_COUNT'] = 1;
                            ?>
                            <? $APPLICATION->IncludeComponent('bitrix:news.list', 'rooms', array(
                                'DISPLAY_DATE' => 'Y',
                                'DISPLAY_NAME' => 'Y',
                                'DISPLAY_PICTURE' => 'Y',
                                'DISPLAY_PREVIEW_TEXT' => 'Y',
                                'AJAX_MODE' => 'N',
                                'IBLOCK_TYPE' => 'realty',
                                'IBLOCK_ID' => '2',
                                'NEWS_COUNT' => '3',
                                'SORT_BY1' => 'ACTIVE_FROM',
                                'SORT_ORDER1' => 'DESC',
                                'SORT_BY2' => 'SORT',
                                'SORT_ORDER2' => 'ASC',
                                'FILTER_NAME' => 'roomNewElementFilter',
                                'FIELD_CODE' => array(),
                                'PROPERTY_CODE' => array(
                                    0 => "ROOMS_COUNT",
                                    1 => "PRICE",
                                    2 => "ADDRESS",
                                    3 => "YEAR",
                                    4 => "MAP",
                                    5 => "PLACEMENT",
                                    6 => "LAYOUT",
                                    7 => "SQUARE",
                                    8 => "SIDE",
                                    9 => "REALTY_TYPE",
                                    10 => "STAGE",
                                    11 => "STAGES_COUNT",
                                ),
                                'CHECK_DATES' => 'Y',
                                'DETAIL_URL' => '',
                                'PREVIEW_TRUNCATE_LEN' => '',
                                'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                                'SET_TITLE' => 'N',
                                'SET_BROWSER_TITLE' => 'N',
                                'SET_META_KEYWORDS' => 'N',
                                'SET_META_DESCRIPTION' => 'N',
                                'SET_LAST_MODIFIED' => 'N',
                                'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                                'ADD_SECTIONS_CHAIN' => 'N',
                                'HIDE_LINK_WHEN_NO_DETAIL' => 'Y',
                                'PARENT_SECTION' => '',
                                'PARENT_SECTION_CODE' => $parentSections[0],
                                'INCLUDE_SUBSECTIONS' => 'Y',
                                'CACHE_TYPE' => 'A',
                                'CACHE_TIME' => '36000000',
                                'CACHE_FILTER' => 'Y',
                                'CACHE_GROUPS' => 'N',
                                'DISPLAY_TOP_PAGER' => 'Y',
                                'DISPLAY_BOTTOM_PAGER' => 'Y',
                                'PAGER_TITLE' => 'Элементы',
                                'PAGER_SHOW_ALWAYS' => 'N',
                                'PAGER_TEMPLATE' => '',
                                'PAGER_DESC_NUMBERING' => 'N',
                                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                                'PAGER_SHOW_ALL' => 'N',
                                'PAGER_BASE_LINK_ENABLE' => 'N',
                                'SET_STATUS_404' => 'N',
                                'SHOW_404' => 'N',
                                'MESSAGE_404' => '',
                                'PAGER_BASE_LINK' => '',
                                'PAGER_PARAMS_NAME' => 'arrPager',
                                'AJAX_OPTION_JUMP' => 'N',
                                'AJAX_OPTION_STYLE' => 'Y',
                                'AJAX_OPTION_HISTORY' => 'N',
                                'AJAX_OPTION_ADDITIONAL' => '',
                                'ROOM_MAP_ID' => 'map',
                            )); ?>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane active" id="two-room">
                    <div class="container">
                        <div class="row">
                            <?php
                            $roomNewElementFilter = array(
                                'PROPERTY_RENT_TYPE_VALUE' => $typeFilter,
                                'SECTION_CODE' => $sectionCodes,
                            );
                            if (empty($parentSections[0]))
                                $roomNewElementFilter['=PROPERTY_ROOMS_COUNT'] = 2;
                            ?>
                            <? $APPLICATION->IncludeComponent('bitrix:news.list', 'rooms', array(
                                'DISPLAY_DATE' => 'Y',
                                'DISPLAY_NAME' => 'Y',
                                'DISPLAY_PICTURE' => 'Y',
                                'DISPLAY_PREVIEW_TEXT' => 'Y',
                                'AJAX_MODE' => 'N',
                                'IBLOCK_TYPE' => 'realty',
                                'IBLOCK_ID' => '2',
                                'NEWS_COUNT' => '3',
                                'SORT_BY1' => 'ACTIVE_FROM',
                                'SORT_ORDER1' => 'DESC',
                                'SORT_BY2' => 'SORT',
                                'SORT_ORDER2' => 'ASC',
                                'FILTER_NAME' => 'roomNewElementFilter',
                                'FIELD_CODE' => array(),
                                'PROPERTY_CODE' => array(
                                    0 => "ROOMS_COUNT",
                                    1 => "PRICE",
                                    2 => "ADDRESS",
                                    3 => "YEAR",
                                    4 => "MAP",
                                    5 => "PLACEMENT",
                                    6 => "LAYOUT",
                                    7 => "SQUARE",
                                    8 => "SIDE",
                                    9 => "REALTY_TYPE",
                                    10 => "STAGE",
                                    11 => "STAGES_COUNT",
                                ),
                                'CHECK_DATES' => 'Y',
                                'DETAIL_URL' => '',
                                'PREVIEW_TRUNCATE_LEN' => '',
                                'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                                'SET_TITLE' => 'N',
                                'SET_BROWSER_TITLE' => 'N',
                                'SET_META_KEYWORDS' => 'N',
                                'SET_META_DESCRIPTION' => 'N',
                                'SET_LAST_MODIFIED' => 'N',
                                'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                                'ADD_SECTIONS_CHAIN' => 'N',
                                'HIDE_LINK_WHEN_NO_DETAIL' => 'Y',
                                'PARENT_SECTION' => '',
                                'PARENT_SECTION_CODE' => $parentSections[1],
                                'INCLUDE_SUBSECTIONS' => 'Y',
                                'CACHE_TYPE' => 'A',
                                'CACHE_TIME' => '36000000',
                                'CACHE_FILTER' => 'Y',
                                'CACHE_GROUPS' => 'N',
                                'DISPLAY_TOP_PAGER' => 'Y',
                                'DISPLAY_BOTTOM_PAGER' => 'Y',
                                'PAGER_TITLE' => 'Элементы',
                                'PAGER_SHOW_ALWAYS' => 'N',
                                'PAGER_TEMPLATE' => '',
                                'PAGER_DESC_NUMBERING' => 'N',
                                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                                'PAGER_SHOW_ALL' => 'N',
                                'PAGER_BASE_LINK_ENABLE' => 'N',
                                'SET_STATUS_404' => 'N',
                                'SHOW_404' => 'N',
                                'MESSAGE_404' => '',
                                'PAGER_BASE_LINK' => '',
                                'PAGER_PARAMS_NAME' => 'arrPager',
                                'AJAX_OPTION_JUMP' => 'N',
                                'AJAX_OPTION_STYLE' => 'Y',
                                'AJAX_OPTION_HISTORY' => 'N',
                                'AJAX_OPTION_ADDITIONAL' => '',
                                'ROOM_MAP_ID' => 'map-2',
                            )); ?>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="three-room">
                    <div class="container">
                        <div class="row">
                            <?php
                            $roomNewElementFilter = array(
                                'PROPERTY_RENT_TYPE_VALUE' => $typeFilter,
                                'SECTION_CODE' => $sectionCodes,
                            );
                            if (empty($parentSections[0]))
                                $roomNewElementFilter['=PROPERTY_ROOMS_COUNT'] = 3;
                            ?>
                            <? $APPLICATION->IncludeComponent('bitrix:news.list', 'rooms', array(
                                'DISPLAY_DATE' => 'Y',
                                'DISPLAY_NAME' => 'Y',
                                'DISPLAY_PICTURE' => 'Y',
                                'DISPLAY_PREVIEW_TEXT' => 'Y',
                                'AJAX_MODE' => 'N',
                                'IBLOCK_TYPE' => 'realty',
                                'IBLOCK_ID' => '2',
                                'NEWS_COUNT' => '3',
                                'SORT_BY1' => 'ACTIVE_FROM',
                                'SORT_ORDER1' => 'DESC',
                                'SORT_BY2' => 'SORT',
                                'SORT_ORDER2' => 'ASC',
                                'FILTER_NAME' => 'roomNewElementFilter',
                                'FIELD_CODE' => array(),
                                'PROPERTY_CODE' => array(
                                    0 => "ROOMS_COUNT",
                                    1 => "PRICE",
                                    2 => "ADDRESS",
                                    3 => "YEAR",
                                    4 => "MAP",
                                    5 => "PLACEMENT",
                                    6 => "LAYOUT",
                                    7 => "SQUARE",
                                    8 => "SIDE",
                                    9 => "REALTY_TYPE",
                                    10 => "STAGE",
                                    11 => "STAGES_COUNT",
                                ),
                                'CHECK_DATES' => 'Y',
                                'DETAIL_URL' => '',
                                'PREVIEW_TRUNCATE_LEN' => '',
                                'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                                'SET_TITLE' => 'N',
                                'SET_BROWSER_TITLE' => 'N',
                                'SET_META_KEYWORDS' => 'N',
                                'SET_META_DESCRIPTION' => 'N',
                                'SET_LAST_MODIFIED' => 'N',
                                'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                                'ADD_SECTIONS_CHAIN' => 'N',
                                'HIDE_LINK_WHEN_NO_DETAIL' => 'Y',
                                'PARENT_SECTION' => '',
                                'PARENT_SECTION_CODE' => $parentSections[2],
                                'INCLUDE_SUBSECTIONS' => 'Y',
                                'CACHE_TYPE' => 'A',
                                'CACHE_TIME' => '36000000',
                                'CACHE_FILTER' => 'Y',
                                'CACHE_GROUPS' => 'N',
                                'DISPLAY_TOP_PAGER' => 'Y',
                                'DISPLAY_BOTTOM_PAGER' => 'Y',
                                'PAGER_TITLE' => 'Элементы',
                                'PAGER_SHOW_ALWAYS' => 'N',
                                'PAGER_TEMPLATE' => '',
                                'PAGER_DESC_NUMBERING' => 'N',
                                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                                'PAGER_SHOW_ALL' => 'N',
                                'PAGER_BASE_LINK_ENABLE' => 'N',
                                'SET_STATUS_404' => 'N',
                                'SHOW_404' => 'N',
                                'MESSAGE_404' => '',
                                'PAGER_BASE_LINK' => '',
                                'PAGER_PARAMS_NAME' => 'arrPager',
                                'AJAX_OPTION_JUMP' => 'N',
                                'AJAX_OPTION_STYLE' => 'Y',
                                'AJAX_OPTION_HISTORY' => 'N',
                                'AJAX_OPTION_ADDITIONAL' => '',
                                'ROOM_MAP_ID' => 'map-3',
                            )); ?>
                        </div>
                    </div>
                </div>
                <? if (empty($parentSections[0])): ?>
                    <div role="tabpanel" class="tab-pane" id="four-room">
                        <div class="container">
                            <div class="row">
                                <?php
                                $roomNewElementFilter = array(
                                    '>=PROPERTY_ROOMS_COUNT' => 4,
                                    'PROPERTY_RENT_TYPE_VALUE' => $typeFilter,
                                    'SECTION_CODE' => $sectionCodes,
                                );
                                ?>
                                <? $APPLICATION->IncludeComponent('bitrix:news.list', 'rooms', array(
                                    'DISPLAY_DATE' => 'Y',
                                    'DISPLAY_NAME' => 'Y',
                                    'DISPLAY_PICTURE' => 'Y',
                                    'DISPLAY_PREVIEW_TEXT' => 'Y',
                                    'AJAX_MODE' => 'N',
                                    'IBLOCK_TYPE' => 'realty',
                                    'IBLOCK_ID' => '2',
                                    'NEWS_COUNT' => '3',
                                    'SORT_BY1' => 'ACTIVE_FROM',
                                    'SORT_ORDER1' => 'DESC',
                                    'SORT_BY2' => 'SORT',
                                    'SORT_ORDER2' => 'ASC',
                                    'FILTER_NAME' => 'roomNewElementFilter',
                                    'FIELD_CODE' => array(),
                                    'PROPERTY_CODE' => array(
                                        0 => "ROOMS_COUNT",
                                        1 => "PRICE",
                                        2 => "ADDRESS",
                                        3 => "YEAR",
                                        4 => "MAP",
                                        5 => "PLACEMENT",
                                        6 => "LAYOUT",
                                        7 => "SQUARE",
                                        8 => "SIDE",
                                        9 => "REALTY_TYPE",
                                        10 => "STAGE",
                                        11 => "STAGES_COUNT",
                                    ),
                                    'CHECK_DATES' => 'Y',
                                    'DETAIL_URL' => '',
                                    'PREVIEW_TRUNCATE_LEN' => '',
                                    'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                                    'SET_TITLE' => 'N',
                                    'SET_BROWSER_TITLE' => 'N',
                                    'SET_META_KEYWORDS' => 'N',
                                    'SET_META_DESCRIPTION' => 'N',
                                    'SET_LAST_MODIFIED' => 'N',
                                    'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                                    'ADD_SECTIONS_CHAIN' => 'N',
                                    'HIDE_LINK_WHEN_NO_DETAIL' => 'Y',
                                    'PARENT_SECTION' => '',
                                    'PARENT_SECTION_CODE' => '',
                                    'INCLUDE_SUBSECTIONS' => 'Y',
                                    'CACHE_TYPE' => 'A',
                                    'CACHE_TIME' => '36000000',
                                    'CACHE_FILTER' => 'Y',
                                    'CACHE_GROUPS' => 'N',
                                    'DISPLAY_TOP_PAGER' => 'Y',
                                    'DISPLAY_BOTTOM_PAGER' => 'Y',
                                    'PAGER_TITLE' => 'Элементы',
                                    'PAGER_SHOW_ALWAYS' => 'N',
                                    'PAGER_TEMPLATE' => '',
                                    'PAGER_DESC_NUMBERING' => 'N',
                                    'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                                    'PAGER_SHOW_ALL' => 'N',
                                    'PAGER_BASE_LINK_ENABLE' => 'N',
                                    'SET_STATUS_404' => 'N',
                                    'SHOW_404' => 'N',
                                    'MESSAGE_404' => '',
                                    'PAGER_BASE_LINK' => '',
                                    'PAGER_PARAMS_NAME' => 'arrPager',
                                    'AJAX_OPTION_JUMP' => 'N',
                                    'AJAX_OPTION_STYLE' => 'Y',
                                    'AJAX_OPTION_HISTORY' => 'N',
                                    'AJAX_OPTION_ADDITIONAL' => '',
                                    'ROOM_MAP_ID' => 'map-4',
                                )); ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
            </div>
        </section>
    <? } ?>


    <!--
<section class="new-arrivals">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2 class="h2">Новые поступления</h2>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row without-paddings">
            <div class="col-md-6">
                <div class="flat-card flat-card-revers">
                    <a href="#"></a>
                    <img src="/assets/img/smart-plan-22.png" class="img-flat-1" alt="plan-flat-1">
                    <div class="flat-plan">
                        <h3><span>1</span> <br> Комнатные квартиры</h3>
                        <img src="/assets/img/scheme.png" alt="scheme">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="flat-card">
                    <a href="#"></a>
                    <img src="/assets/img/smart-plan-32.png" class="img-flat-2" alt="plan-flat-2">
                    <div class="flat-plan pos-left">
                        <h3><span>2</span> <br> Комнатные квартиры</h3>
                        <img src="/assets/img/scheme.png" alt="scheme">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="flat-card">
                    <a href="#"></a>
                    <img src="/assets/img/planirovka-kvartir-foto-readgy-com-3.png" class="img-flat-3" alt="plan-flat-3">
                    <div class="flat-plan">
                        <h3><span>3</span> <br> Комнатные квартиры</h3>
                        <img src="/assets/img/scheme.png" alt="scheme">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="flat-card flat-card-revers">
                    <a href="#"></a>
                    <img src="/assets/img/plan-4.png" class="img-flat-4" alt="plan-flat-4">
                    <div class="flat-plan pos-left">
                        <h3><span>4</span> <br> Комнатные квартиры</h3>
                        <img src="/assets/img/scheme.png" alt="scheme">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
-->

<? endif; ?>
<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>