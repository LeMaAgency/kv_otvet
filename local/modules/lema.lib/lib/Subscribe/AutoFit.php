<?php

namespace Lema\Subscribe;
use Lema\Common\Helper;
use Lema\IBlock\Element;

/**
 * Class AutoFit
 * @package Lema\Subscribe
 */
class AutoFit
{

    /**
     * @param $requestId
     * @param $frequency
     * @param $dateFrom
     * @param $dateTo
     * @param $timeStart
     *
     * @return string
     */
    public static function addOrUpdateAgent($requestId, $frequency, $dateFrom, $dateTo, $timeStart)
    {
        /**
         * Check how often we need to send autofit
         */
        $interval = 60 * 60 * 24;
        switch($frequency)
        {
            //day_1
            case 106:
                $interval *= 1;
            break;
            //day_2
            case 107:
                $interval *= 2;
            break;
            //day_3
            case 108:
                $interval *= 3;
            break;
            //day_7
            case 109:
                $interval *= 7;
            break;
            //day_14
            case 110:
                $interval *= 14;
            break;
            //day_30
            case 111:
                $interval *= 30;
            break;
            //day_1
            default:
                $interval *= 1;
            break;
        }

        /**
         * Get start timestamp
         */
        $startDateTime = \DateTime::createFromFormat('d.m.Y H:i', $dateFrom . ' ' . $timeStart);
        if($startDateTime->getTimestamp() < (new \DateTime('now'))->getTimestamp())
            $startDateTime->modify('+1 day');
        /**
         * Get end timestamp
         */
        $endDateTime = \DateTime::createFromFormat('d.m.Y H:i', $dateTo . ' ' . $timeStart);

        $agentName = sprintf(
            '\\%s::start(%d, "%s", "%s");',
            get_class(),
            $requestId,
            $startDateTime->getTimestamp(),
            $endDateTime->getTimestamp()
        );

        if($endDateTime->getTimestamp() < $startDateTime->getTimestamp())
        {
            static::removeAgent($requestId);
            return false;
        }

        //search existing agent
        $res = \CAgent::GetList(array('ID' => 'DESC'), array('NAME' => '\\' . get_class() . '::start(' . $requestId . ', %'));
        if($row = $res->Fetch())
        {
            if($row['NAME'] != $agentName)
            {
                \CAgent::Update($row['ID'], array(
                    'NAME' => $agentName,
                    'USER_ID' => 1,
                    'NEXT_EXEC' => $startDateTime->format('d.m.Y H:i:s'),
                    'ACTIVE' => 'Y'
                ));
            }
        }
        else
        {
            //Agent is not exists, create it now
            \CAgent::AddAgent(
                $agentName,
                '',
                'Y',
                $interval,
                '',
                'Y',
                $startDateTime->format('d.m.Y H:i:s'),
                30,
                1,
                false
            );
        }

        return $agentName;
    }

    /**
     * @param $requestId
     */
    public static function removeAgent($requestId)
    {
        //search existing agent
        $res = \CAgent::GetList(array('ID' => 'DESC'), array('NAME' => '\\' . get_class() . '::start(' . $requestId . ', %'));
        if($row = $res->Fetch())
        {
            \CAgent::Delete($row['ID']);
        }
    }

    /**
     * @param $requestId
     * @param $startDateTime
     * @param $endDateTime
     *
     * @return string|void
     */
    public static function start($requestId, $startDateTime, $endDateTime)
    {

        /**
         * Remove old agents
         */
        if($startDateTime < time() && $endDateTime < time())
        {
            static::removeAgent($requestId);
            return ;
        }

        \Bitrix\Main\Loader::includeModule('iblock');

        $arSelect = array(
            'ID', 'NAME', 'DETAIL_PAGE_URL',
            'PROPERTY_REALTY_TYPE', 'PROPERTY_RENT_TYPE', 'PROPERTY_ROOMS_COUNT', 'PROPERTY_LAYOUT_TYPE',
            'PROPERTY_STAGE', 'PROPERTY_STAGES_COUNT', 'PROPERTY_REGION', 'PROPERTY_PRICE_FROM', 'PROPERTY_PRICE_TO',
            'PROPERTY_MATERIAL', 'PROPERTY_SQUARE_FROM', 'PROPERTY_LIFE_MASSIV_SNT', 'PROPERTY_HAVINGS_TYPE',
            'PROPERTY_SEND_TIME_START', 'PROPERTY_SEND_FREQUENCY', 'PROPERTY_SEND_DATE_FROM', 'PROPERTY_SEND_DATE_TO',
            'PROPERTY_AUTOFIT', 'PROPERTY_CLIENT_NAME', 'PROPERTY_CLIENT_EMAIL', 'PROPERTY_CLIENT_PHONE',
        );
        $request = Element::getById(\LIblock::getId('requests'), $requestId, array(
            'arSelect' => $arSelect
        ));

        if($request['PROPERTY_AUTOFIT_VALUE'] != 'Y')
            return ;

        $filter = array (
            'PROPERTY_REALTY_TYPE_VALUE' => array_values($request['PROPERTY_REALTY_TYPE_VALUE']),
            'PROPERTY_RENT_TYPE_VALUE' => array_values($request['PROPERTY_RENT_TYPE_VALUE']),
            'PROPERTY_ROOMS_COUNT' => $request['PROPERTY_ROOMS_COUNT_VALUE'],
            'PROPERTY_LAYOUT_TYPE_VALUE' => array_values($request['PROPERTY_LAYOUT_TYPE_VALUE']),
            'PROPERTY_REGION_VALUE' => array_values($request['PROPERTY_REGION_VALUE']),
            'PROPERTY_PRICE_FROM' => $request['PROPERTY_PRICE_FROM_VALUE'],
            'PROPERTY_PRICE_TO' => $request['PROPERTY_PRICE_TO_VALUE'],
            'PROPERTY_MATERIAL_VALUE' => array_values($request['PROPERTY_MATERIAL_ENUM_ID']),
            'PROPERTY_SQUARE_FROM' => $request['PROPERTY_SQUARE_FROM_VALUE'],
            'PROPERTY_LIFE_MASSIV_SNT' => $request['PROPERTY_LIFE_MASSIV_SNT_VALUE'],
            'PROPERTY_HAVINGS_TYPE_VALUE' => array_values($request['PROPERTY_HAVINGS_TYPE_ENUM_ID']),
        );

        foreach($filter as $k => $value)
        {
            if(empty($value))
            {
                unset($filter[$k]);
            }
        }
        if(!empty($request['PROPERTY_PRICE_FROM_VALUE']))
            $filter['>=PROPERTY_PRICE'] = $request['PROPERTY_PRICE_FROM_VALUE'];
        if(!empty($request['PROPERTY_PRICE_TO_VALUE']))
            $filter['<=PROPERTY_PRICE'] = $request['PROPERTY_PRICE_TO_VALUE'];

        $realtyTypes = $rentTypes = array();
        foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('objects', 'REALTY_TYPE')) as $k => $v)
            $realtyTypes[$v['VALUE']] = $k;
        foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('objects', 'RENT_TYPE')) as $k => $v)
            $rentTypes[$v['VALUE']] = $k;

        $splitLine = '<br>' . str_repeat('-', 125) . '<br>';
        $sendData = null;
        foreach(Element::getAll(\LIblock::getId('objects'), array('filter' => $filter, 'arSelect' => $arSelect)) as $item)
        {

            switch($request['PROPERTY_STAGE_VALUE'])
            {
                case 'not_last':
                case 'not_last_2':
                    if($item['PROPERTY_STAGE_VALUE'] == $item['PROPERTY_STAGES_COUNT_VALUE'])
                        continue;
                break;
                case 'not_first':
                    if($item['PROPERTY_STAGE_VALUE'] == 1)
                        continue;
                break;
                case 'first':
                    if($item['PROPERTY_STAGE_VALUE'] != 1)
                        continue;
                break;
                case 'last':
                    if($item['PROPERTY_STAGE_VALUE'] != $item['PROPERTY_STAGES_COUNT_VALUE'])
                        continue;
                break;
            }

            $url = Helper::getFullUrl(strtr($item['DETAIL_PAGE_URL'], array(
                '#RENT_TYPE#' => $rentTypes[$item['PROPERTY_RENT_TYPE_VALUE']],
                '#REALTY_TYPE#' => $realtyTypes[$item['PROPERTY_REALTY_TYPE_VALUE']],
            )));

            $sendData .= sprintf(
                'Название: %s<br> Ссылка: <a href="%s">%s</a><br> Тип объекта: %s<br> Тип сделки: %s<br> Количество комнат: %s<br> Вид планировки: %s<br>' .
                'Этаж: %s<br> Регион: %s<br> Материал: %s',
                $item['NAME'],
                $url,
                $url,
                (is_array($item['PROPERTY_REALTY_TYPE_VALUE'])  ? join(', ', $item['PROPERTY_REALTY_TYPE_VALUE'])   : $item['PROPERTY_REALTY_TYPE_VALUE']),
                (is_array($item['PROPERTY_RENT_TYPE_VALUE'])    ? join(', ', $item['PROPERTY_RENT_TYPE_VALUE'])     : $item['PROPERTY_RENT_TYPE_VALUE']),
                (is_array($item['PROPERTY_ROOMS_COUNT_VALUE'])  ? join(', ', $item['PROPERTY_ROOMS_COUNT_VALUE'])   : $item['PROPERTY_ROOMS_COUNT_VALUE']),
                (is_array($item['PROPERTY_LAYOUT_TYPE_VALUE'])  ? join(', ', $item['PROPERTY_LAYOUT_TYPE_VALUE'])   : $item['PROPERTY_LAYOUT_TYPE_VALUE']),
                (is_array($item['PROPERTY_STAGE_VALUE'])        ? join(', ', $item['PROPERTY_STAGE_VALUE'])         : $item['PROPERTY_STAGE_VALUE']),
                (is_array($item['PROPERTY_REGION_VALUE'])       ? join(', ', $item['PROPERTY_REGION_VALUE'])        : $item['PROPERTY_REGION_VALUE']),
                (is_array($item['PROPERTY_MATERIAL_VALUE'])     ? join(', ', $item['PROPERTY_MATERIAL_VALUE'])      : $item['PROPERTY_MATERIAL_VALUE'])
            );
            if(!empty($item['PROPERTY_LIFE_MASSIV_SNT_VALUE']))
                $sendData .= ';Жилой массив, СНТ: ' . $item['PROPERTY_LIFE_MASSIV_SNT_VALUE'];

            $sendData .= $splitLine;
        }

        \CEvent::Send('AUTO_FIT', 's1', array(
            'DATE' => date('d.m.Y'),
            'OBJECTS' => $sendData,
            'EMAIL_TO' => $request['PROPERTY_CLIENT_EMAIL_VALUE'],
        ));

        return sprintf(
            '\\%s::start(%d, "%s", "%s");',
            get_class(),
            $requestId,
            $startDateTime,
            $endDateTime
        );
    }
}