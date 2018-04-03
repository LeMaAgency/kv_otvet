<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc,
    Lema\Template\TemplateHelper as TH;

Loc::loadMessages(__FILE__);


$data = new TH($this);

/**
 * Collect address from parts:
 * City, Region, Street, House number and building number
 */
$props = array(
    'CITY' => array(
        'prefix' => Loc::getMessage('LEMA_CITY_PREFIX'),
        'postfix' => Loc::getMessage('LEMA_CITY_POSTFIX')
    ),
    'REGION' => array(
        'prefix' => Loc::getMessage('LEMA_REGION_PREFIX'),
        'postfix' => Loc::getMessage('LEMA_REGION_POSTFIX')
    ),
    'STREET' => array(
        'prefix' => Loc::getMessage('LEMA_STREET_PREFIX'),
        'postfix' => Loc::getMessage('LEMA_STREET_POSTFIX')
    ),
    'HOUSE_NUMBER' => array(
        'prefix' => Loc::getMessage('LEMA_HOUSE_NUMBER_PREFIX'),
        'postfix' => Loc::getMessage('LEMA_HOUSE_NUMBER_POSTFIX')
    ),
);

$splitSymbol = ', ';

/**
 * Set address for items
 */
foreach($data->items() as $k => $item)
{
    $arResult['ITEMS'][$k]['ADDRESS'] = null;
    foreach($props as $propCode => $propData)
    {
        if($item->propFilled($propCode))
        {
            if(!empty($arResult['ITEMS'][$k]['ADDRESS']))
                $arResult['ITEMS'][$k]['ADDRESS'] .= $splitSymbol;
            $arResult['ITEMS'][$k]['ADDRESS'] .= $propData['prefix'] . $item->propVal($propCode) . $propData['postfix'];
        }
    }
    if($item->propFilled('BUILDING_NUMBER'))
    {
        if(0 !== strpos($item->propVal('BUILDING_NUMBER'), '/'))
            $arResult['ITEMS'][$k]['ADDRESS'] .= $splitSymbol;
        $arResult['ITEMS'][$k]['ADDRESS'] .= $item->propVal('BUILDING_NUMBER');
    }
}