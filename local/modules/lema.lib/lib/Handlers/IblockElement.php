<?php

namespace Lema\Handlers;

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Type\DateTime;
use Lema\Common\Dumper;
use Lema\IBlock\Section;
use Lema\Subscribe\AutoFit;

Loc::loadMessages(__FILE__);

/**
 * Class IblockElement
 * @package Lema\Handlers
 */
class IblockElement
{
    const DEFAULT_TASK_ID = 38;

    /**
     * @param array $fields
     */
    public static function beforeAdd(array &$fields)
    {
        static::generateElementNameAndCode($fields);
        static::setElementRights($fields);
        static::setReminders($fields);
        static::setAutoFit($fields);

    }

    /**
     * @param array $fields
     */
    public static function beforeUpdate(array &$fields)
    {
        static::generateElementNameAndCode($fields);
        static::setElementRights($fields);
        static::setReminders($fields);
        static::setAutoFit($fields);
        static::sendRequest($fields);
    }

    /**
     * @param int $id
     */
    public static function beforeDelete($id)
    {

    }

    /**
     * @param array $fields
     */
    public static function afterAdd(array &$fields)
    {
        static::sendRequest($fields);
        static::generateElementNameAndCode($fields);
    }

    /**
     * @param array $fields
     */
    public static function afterUpdate(array &$fields)
    {
        static::sendRequest($fields);
    }

    /**
     * @param array $fields
     */
    public static function afterDelete(array &$fields)
    {

    }

    /**
     * Generates element code from its name, square and element ID. Only for objects iblock
     *
     * @param array $fields
     */
    protected static function generateElementNameAndCode(array &$fields)
    {
        //check iblock number
        if (isset($fields['IBLOCK_ID']) && $fields['IBLOCK_ID'] === \LIblock::getId('objects') && !empty($fields['PROPERTY_VALUES'])) {
            //get square property value
            $square = static::getPropValue($fields, 'SQUARE');
            $sectionId = empty($fields['IBLOCK_SECTION']) ? false : (int)current($fields['IBLOCK_SECTION']);
            //get realty type
            $section = Section::getAllD7($fields['IBLOCK_ID'], array(
                'filter' => array('=ID' => $sectionId),
                'select' => array('ID', 'CODE', 'NAME'),
            ));

            if (!empty($section[$sectionId]['NAME']))
                $fields['NAME'] = $section[$sectionId]['NAME'];

            //generate element code for URL
            $id = empty($fields['ID']) ? time() : $fields['ID'];

            $fields['CODE'] = \CUtil::translit($section[$sectionId]['CODE'] . '_' . $square . '_m_2_' . $id, 'ru');
        }
    }

    /**
     * Set element rights for specified user (or clean it)
     *
     * @param array $fields
     */
    protected static function setElementRights(array &$fields)
    {
        /**
         * Get iblock code by id
         */
        if (!empty($fields['IBLOCK_ID'])) {
            $iblockCode = null;
            $checkIblockCodes = array('objects', 'object_call_wait');
            foreach ($checkIblockCodes as $tmpCode) {
                if ($fields['IBLOCK_ID'] == \LIblock::getId($tmpCode)) {
                    $iblockCode = $tmpCode;
                    break;
                }
            }
        }
        //check iblock number
        if (!empty($iblockCode) && !empty($fields['PROPERTY_VALUES'])) {

            /**
             * Administrator can change rieltor for object
             */
            if (\Lema\Common\User::isAdmin()) {

                //get rieltor property value
                $rieltorId = static::getPropValue($fields, 'RIELTOR', 0, $iblockCode);

                //rieltor not specified, clean rights
                if (empty($rieltorId)) {
                    $fields['RIGHTS'] = array();
                } else {
                    //set rights for specified rieltor
                    $fields['RIGHTS'] = array(
                        'n0' => array(
                            'GROUP_CODE' => 'U' . $rieltorId,
                            'DO_CLEAN' => 'N',
                            'TASK_ID' => static::DEFAULT_TASK_ID
                        )
                    );
                }
            } else {
                /**
                 * New object, DO NOT set special section to it and clear access rights
                 */
                if (empty($fields['ID']) && $fields['IBLOCK_ID'] === \LIblock::getId('objects')) {
                    /* if($fields['IBLOCK_ID'] === \LIblock::getId('objects'))
                         $fields['IBLOCK_SECTION'] = ;
                    $fields['ACTIVE'] = 'Y';*/
                    $fields['ACTIVE'] = 'N';

                    //get the fields of the current section
                    $res = \CIBlockSection::GetByID($fields['IBLOCK_SECTION'][0]);
                    if ($arSectionRes = $res->Fetch()) {
                        $arSection = $arSectionRes;
                    }

                    //generate the name of the section of new objects from the current section
                    if ($arSection['IBLOCK_SECTION_ID'] == '23') {
                        $sectCodeNew = $arSection['CODE'] . "-new";
                    } else {
                        $sectCodeNew = stristr($arSection['CODE'], '-', true) . "-new";
                    }

                    //overwrite the name of the section for this item
                    if (!empty($sectCodeNew)) {
                        $sectIdNew = \Liblock::getSectionInfo('objects', $sectCodeNew, true);
                        if ($sectIdNew) {
                            $fields['IBLOCK_SECTION'][0] = $sectIdNew['ID'];
                        }
                    }
                    $fields['RIGHTS'] = array();
                } else {
                    $fields['RIGHTS'] = array(
                        'n0' => array(
                            'GROUP_CODE' => 'U' . \Lema\Common\User::get()->GetId(),
                            'DO_CLEAN' => 'N',
                            'TASK_ID' => static::DEFAULT_TASK_ID
                        )
                    );
                }
            }
        }
    }

    /**
     * Set auto fit agents
     *
     * @param array $fields
     *
     * @return bool
     */
    protected static function setAutoFit(array $fields)
    {
        if (empty($fields['ID']))
            return false;

        if (isset($fields['IBLOCK_ID']) && $fields['IBLOCK_ID'] === \LIblock::getId('requests') && !empty($fields['PROPERTY_VALUES'])) {
            if (!static::getPropValue($fields, 'AUTOFIT', false, 'requests')) {
                AutoFit::removeAgent($fields['ID']);
                return;
            }

            $frequency = static::getPropValue($fields, 'SEND_FREQUENCY', false, 'requests');
            $dateFrom = static::getPropValue($fields, 'SEND_DATE_FROM', false, 'requests');
            $dateTo = static::getPropValue($fields, 'SEND_DATE_TO', false, 'requests');
            $timeStart = static::getPropValue($fields, 'SEND_TIME_START', false, 'requests');

            if (preg_match('~^\\d+$~', $timeStart))
                $timeStart .= ':00';

            if (!empty($frequency) && !empty($dateFrom) && !empty($dateTo) && !empty($timeStart))
                AutoFit::addOrUpdateAgent($fields['ID'], $frequency, $dateFrom, $dateTo, $timeStart);

        }
    }

    /**
     * @param array $fields
     *
     * @return bool
     */
    protected static function setReminders(array $fields)
    {
        if (empty($fields['ID']))
            return false;

        if (isset($fields['IBLOCK_ID']) && $fields['IBLOCK_ID'] === \LIblock::getId('objects') && !empty($fields['PROPERTY_VALUES'])) {
            //get remind date property value
            $remindDate = static::getPropValue($fields, 'REMINDER_DATE', null);

            if (empty($remindDate))
                return false;

            //get rieltor property value
            $rieltor = static::getPropValue($fields, 'RIELTOR', false);


            $agentName = '\\' . get_class() . '::remind(' . $fields['ID'] . ');';

            $remindTimeStamp = strtotime($remindDate == date('d.m.Y') ? '+1 hour' : $remindDate);

            if ($remindTimeStamp > time())
                return false;

            $remindDate = date('d.m.Y H:i:s', $remindTimeStamp);

            //search existing agent
            $res = \CAgent::GetList(array('ID' => 'DESC'), array('NAME' => $agentName));
            if ($row = $res->Fetch()) {
                \CAgent::Update($row['ID'], array(
                    'NAME' => $agentName,
                    'USER_ID' => $rieltor,
                    'NEXT_EXEC' => $remindDate,
                    'ACTIVE' => 'Y'
                ));
            }

            //Agent is not exists, create it now
            \CAgent::AddAgent(
                $agentName,
                '',
                'N',
                60,
                $remindDate,
                'Y',
                $remindDate,
                30,
                $rieltor
            );
        }
    }

    /**
     * @param $objectId
     *
     * @return string
     */
    public static function remind($objectId)
    {
        $agentName = '\\' . get_class() . '::remind(' . $objectId . ');';

        /**
         * Delete agent
         */
        $res = \CAgent::GetList(array('ID' => 'DESC'), array('NAME' => $agentName));
        if ($row = $res->Fetch())
            \CAgent::Delete($row['ID']);

        if (!\CModule::includeModule('iblock'))
            return $agentName;

        $res = \CIBlockElement::GetList(
            array(),
            array('IBLOCK_ID' => \LIblock::getId('objects'), '=ID' => $objectId),
            false,
            false,
            array('NAME', 'PROPERTY_RIELTOR', 'PROPERTY_REMINDER_TEXT')
        );
        if (!($row = $res->Fetch()))
            return $agentName;

        /**
         * This variables are used in modal template and should be defined
         */
        $objectName = $row['NAME'];
        $text = $row['PROPERTY_REMINDER_TEXT_VALUE'];
        if (!empty($text['TEXT']))
            $text = $text['TEXT'];

        /**
         * include modal template
         */
        require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/modal_agent_template.php';

        exit;
    }

    protected static function getPropValue(array $fields = array(), $propCode, $defValue = 0, $iblockCode = 'objects')
    {
        //get property id
        $propId = \LIblock::getPropId($iblockCode, $propCode);

        //get property value
        if (empty($fields['PROPERTY_VALUES'][$propId])) {
            return $defValue;
        } else {
            $tmp = current($fields['PROPERTY_VALUES'][$propId]);
            return empty($tmp) || empty($tmp['VALUE']) ? $defValue : $tmp['VALUE'];
        }
    }

    /**
     * Creates and sends a request
     *
     * @param array $fields
     */
    protected static function sendRequest(array &$fields)
    {
        $checkMail = array_column($fields['PROPERTY_VALUES'][106], 'VALUE');
        if (isset($fields['IBLOCK_ID']) && $fields['IBLOCK_ID'] === \LIblock::getId('requests') && !empty($checkMail[0])) {

            //Adding the properties of an element to an array $arFieldsTemporary
            foreach ($fields['PROPERTY_VALUES'] as $key => $prop) {
                $arFieldsTemporary[$key] = array_column($prop, 'VALUE');
            }

            //Removes from the array the empty properties
            foreach ($arFieldsTemporary as $key => $fieldTemporary) {
                if (!empty($fieldTemporary[0])) {
                    $emailFields[$key] = array_diff($fieldTemporary, array(''));
                }
            }
            //Selects type of rent
            $rentType = $emailFields[88][0];

            //Selects all values of properties of type "list"
            $resProp = \CIBlockPropertyEnum::GetList(
                false,
                Array("IBLOCK_ID" => $fields['IBLOCK_ID'])
            );
            while ($propFields = $resProp->Fetch()) {
                $properties[$propFields['PROPERTY_ID']][$propFields['ID']] = $propFields['VALUE'];
            }
            //Removes property 89 from property array (property row, multiple)
            unset($properties[89]);

            //Replaced the "property value id" with "value"
            foreach ($arFieldsTemporary as $key => $field) {
                if (array_key_exists($key, $properties)) {
                    foreach ($field as $keyField => $value) {
                        $emailFields[$key][$keyField] = $properties[$key][$value];
                    }
                }
            }

            //Converting subarrays to String
            foreach ($emailFields as $key => $field) {
                $emailFieldsFinish[$key] = implode(",", $field);
            }


            //Change value of the "auto-fit" property in the message fields
            if (!empty($emailFieldsFinish[103])) {
                $emailFieldsFinish[103] = "Да";
            } else {
                $emailFieldsFinish[103] = "Нет";
            }
            //Add the additional information in the message fields
            if (!empty($fields['PREVIEW_TEXT'])) {
                $emailFieldsFinish['ADD_INFO'] = $fields['PREVIEW_TEXT'];
            }

            $sendData = null;
            /*
                #87# - Вид сделки
                #88# - Тип недвижимости
                #89# - Количество комнат
                #90# - Вид планировки
                #91# - Этаж
                #92# - Район
                #93# - Цена, от
                #94# - Цена, до
                #95# - Материал
                #96# - Площадь, от
                #97# - Жилой массив, СНТ
                #98# - Тип собственности
                #99# - Время начала рассылки
                #100# - Частота рассылки
                #101# - Дата рассылки, от
                #102# - Дата рассылки, до
                #103# - Автоподбор
                #104# - Имя клиента
                #105# - Телефон клиента
                #106# - Email клиента
                #ADD_INFO# - Дополнительная информация
            */
            switch ($rentType):
                case 94:
                    //APPARTMENTS
                    $sendData .= sprintf(
                        '<br>Вид сделки: %s<br>Тип недвижимости: %s<br>Количество комнат: %s<br>Вид планировки: %s<br>Этаж: %s<br>Район: %s<br>Цена, от: %s<br>Цена, до: %s<br>Время начала рассылки: %s<br>Частота рассылки: %s<br>Дата рассылки, от: %s<br>Дата рассылки, до: %s<br>Автоподбор: %s<br>Доп.информация:<br> %s',
                        (empty($emailFieldsFinish['87']) ? "-" : $emailFieldsFinish['87']),
                        (empty($emailFieldsFinish['88']) ? "-" : $emailFieldsFinish['88']),
                        (empty($emailFieldsFinish['89']) ? "-" : $emailFieldsFinish['89']),
                        (empty($emailFieldsFinish['90']) ? "-" : $emailFieldsFinish['90']),
                        (empty($emailFieldsFinish['91']) ? "-" : $emailFieldsFinish['91']),
                        (empty($emailFieldsFinish['92']) ? "-" : $emailFieldsFinish['92']),
                        (empty($emailFieldsFinish['93']) ? "-" : $emailFieldsFinish['93']),
                        (empty($emailFieldsFinish['94']) ? "-" : $emailFieldsFinish['94']),
                        (empty($emailFieldsFinish['99']) ? "-" : $emailFieldsFinish['99']),
                        (empty($emailFieldsFinish['100']) ? "-" : $emailFieldsFinish['100']),
                        (empty($emailFieldsFinish['101']) ? "-" : $emailFieldsFinish['101']),
                        (empty($emailFieldsFinish['102']) ? "-" : $emailFieldsFinish['102']),
                        (empty($emailFieldsFinish['103']) ? "-" : $emailFieldsFinish['103']),
                        (empty($emailFieldsFinish['ADD_INFO']) ? "-" : $emailFieldsFinish['ADD_INFO'])
                    );
                    break;
                case 95:
                    //ROOMS
                    $sendData .= sprintf(
                        'Вид сделки: %s<br>Тип недвижимости: %s<br>Вид планировки: %s<br>Этаж: %s<br>Район: %s<br>Цена, от: %s<br>Цена, до: %s<br>Время начала рассылки: %s<br>Частота рассылки: %s<br>Дата рассылки, от: %s<br>Дата рассылки, до: %s<br>Автоподбор: %s<br>Доп.информация:<br> %s',
                        (empty($emailFieldsFinish['87']) ? "-" : $emailFieldsFinish['87']),
                        (empty($emailFieldsFinish['88']) ? "-" : $emailFieldsFinish['88']),
                        (empty($emailFieldsFinish['90']) ? "-" : $emailFieldsFinish['90']),
                        (empty($emailFieldsFinish['91']) ? "-" : $emailFieldsFinish['91']),
                        (empty($emailFieldsFinish['92']) ? "-" : $emailFieldsFinish['92']),
                        (empty($emailFieldsFinish['93']) ? "-" : $emailFieldsFinish['93']),
                        (empty($emailFieldsFinish['94']) ? "-" : $emailFieldsFinish['94']),
                        (empty($emailFieldsFinish['99']) ? "-" : $emailFieldsFinish['99']),
                        (empty($emailFieldsFinish['100']) ? "-" : $emailFieldsFinish['100']),
                        (empty($emailFieldsFinish['101']) ? "-" : $emailFieldsFinish['101']),
                        (empty($emailFieldsFinish['102']) ? "-" : $emailFieldsFinish['102']),
                        (empty($emailFieldsFinish['103']) ? "-" : $emailFieldsFinish['103']),
                        (empty($emailFieldsFinish['ADD_INFO']) ? "-" : $emailFieldsFinish['ADD_INFO'])
                    );
                    break;
                case 96:
                    //HOME
                case 97:
                    //BOWERS
                    $sendData .= sprintf(
                        'Вид сделки: %s<br>Тип недвижимости: %s<br>Количество комнат: %s<br>Материал: %s<br>Площадь, от: %s<br>Жилой массив, СНТ: %s<br>Цена, от: %s<br>Цена, до: %s<br>Время начала рассылки: %s<br>Частота рассылки: %s<br>Дата рассылки, от: %s<br>Дата рассылки, до: %s<br>Автоподбор: %s<br>Доп.информация:<br> %s',
                        (empty($emailFieldsFinish['87']) ? "-" : $emailFieldsFinish['87']),
                        (empty($emailFieldsFinish['88']) ? "-" : $emailFieldsFinish['88']),
                        (empty($emailFieldsFinish['89']) ? "-" : $emailFieldsFinish['89']),
                        (empty($emailFieldsFinish['95']) ? "-" : $emailFieldsFinish['95']),
                        (empty($emailFieldsFinish['96']) ? "-" : $emailFieldsFinish['96']),
                        (empty($emailFieldsFinish['97']) ? "-" : $emailFieldsFinish['97']),
                        (empty($emailFieldsFinish['93']) ? "-" : $emailFieldsFinish['93']),
                        (empty($emailFieldsFinish['94']) ? "-" : $emailFieldsFinish['94']),
                        (empty($emailFieldsFinish['99']) ? "-" : $emailFieldsFinish['99']),
                        (empty($emailFieldsFinish['100']) ? "-" : $emailFieldsFinish['100']),
                        (empty($emailFieldsFinish['101']) ? "-" : $emailFieldsFinish['101']),
                        (empty($emailFieldsFinish['102']) ? "-" : $emailFieldsFinish['102']),
                        (empty($emailFieldsFinish['103']) ? "-" : $emailFieldsFinish['103']),
                        (empty($emailFieldsFinish['ADD_INFO']) ? "-" : $emailFieldsFinish['ADD_INFO'])
                    );
                    break;
                case 98:
                    //LAND
                    $sendData .= sprintf(
                        'Вид сделки: %s<br>Тип недвижимости: %s<br>Площадь, от: %s<br>Жилой массив, СНТ: %s<br>Тип собственности: %s<br>Цена, от: %s<br>Цена, до: %s<br>Время начала рассылки: %s<br>Частота рассылки: %s<br>Дата рассылки, от: %s<br>Дата рассылки, до: %s<br>Автоподбор: %s<br>Доп.информация:<br> %s',
                        (empty($emailFieldsFinish['87']) ? "-" : $emailFieldsFinish['87']),
                        (empty($emailFieldsFinish['88']) ? "-" : $emailFieldsFinish['88']),
                        (empty($emailFieldsFinish['96']) ? "-" : $emailFieldsFinish['96']),
                        (empty($emailFieldsFinish['97']) ? "-" : $emailFieldsFinish['97']),
                        (empty($emailFieldsFinish['98']) ? "-" : $emailFieldsFinish['98']),
                        (empty($emailFieldsFinish['93']) ? "-" : $emailFieldsFinish['93']),
                        (empty($emailFieldsFinish['94']) ? "-" : $emailFieldsFinish['94']),
                        (empty($emailFieldsFinish['99']) ? "-" : $emailFieldsFinish['99']),
                        (empty($emailFieldsFinish['100']) ? "-" : $emailFieldsFinish['100']),
                        (empty($emailFieldsFinish['101']) ? "-" : $emailFieldsFinish['101']),
                        (empty($emailFieldsFinish['102']) ? "-" : $emailFieldsFinish['102']),
                        (empty($emailFieldsFinish['103']) ? "-" : $emailFieldsFinish['103']),
                        (empty($emailFieldsFinish['ADD_INFO']) ? "-" : $emailFieldsFinish['ADD_INFO'])
                    );
                    break;
            endswitch;

            \CEvent::Send(
                'REQUEST_MESSAGE',
                's1',
                array(
                    "OBJECT" => $sendData,
                    "EMAIL_TO" => $emailFieldsFinish[106]
                )
            );
        }
    }
}