<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use \Lema\Common\Helper;

//Is POST data sent ?
empty($_POST) && exit;
//set rules & fields for form
$form = new \Lema\Forms\AjaxForm(array(
        array('NAME', 'required', array('message' => 'Имя обязательно к заполнению')),
        array('PATRONUMIC', 'required', array('message' => 'Отчество обязательно к заполнению')),
        array('SURNAME', 'required', array('message' => 'Фамилия обязательна к заполнению')),
        array('date', 'required', array('message' => 'Дата рождения обязательна к заполнению')),
        array('PHONE', 'required', array('message' => 'Телефон обязателен к заполнению')),
        array('PHONE', 'phone', array('message' => 'Телефон должен быть в формате +7 (999) 666-33-11')),
        array('EMAIL', 'required', array('message' => 'Email обязателен к заполнению')),
        array('EMAIL', 'email', array('message' => 'Неверный формат E-mail')),
        array('EDUCATION_LEVEL', 'required', array('message' => 'Уровень образования обязателен к заполнению')),
        array('MARITAL_STATUS', 'required', array('message' => 'Семейное положение обязательно к заполнению')),
        array('MARRIAGE_CONTRACT', 'required', array('message' => 'Брачный договор обязателен к заполнению')),
        //array('QUANTITY_MINOR_CHILDREN', 'required', array('message' => 'Количество детей обязательно к заполнению')),
        //array('QUANTITY_MINOR_CHILDREN', 'numerical', array('message' => 'Количество детей должно быть числом')),
        array('REGION', 'required', array('message' => 'Область/Край обязательны к заполнению')),
        array('CITY', 'required', array('message' => 'Город/Поселок обязательны к заполнению')),
        array('STATUS_HOUSING', 'required', array('message' => 'Статус жилья обязателен к заполнению')),
        array('EMPLOYMENT_TYPE', 'required', array('message' => 'Тип занятости обязателен к заполнению')),
        array('TYPE_LABOR_CONTRACT', 'required', array('message' => 'Тип трудового договора обязателен к заполнению')),
        array('EXPERIENCE_CURRENT_WORK', 'required', array('message' => 'Стаж на текущем месте работы обязателен к заполнению')),
        array('EXPERIENCE_CURRENT_WORK', 'numerical', array('message' => 'Стаж на текущем месте работы должен быть числом')),
        array('EXPERIENCE_TOTAL', 'required', array('message' => 'Общий трудовой стаж обязателен к заполнению')),
        array('EXPERIENCE_TOTAL', 'numerical', array('message' => 'Общий трудовой стаж должен быть числом')),
        array('SALARY_PROJECT_BANK', 'required', array('message' => 'Зарплатный проект банка обязателен к заполнению')),
        array('BASIC_SALARY', 'required', array('message' => 'Основная заработная плата обязательна к заполнению')),
        array('BASIC_SALARY', 'numerical', array('message' => 'Основная заработная плата должна быть числом')),
        array('ADDITIONAL_INCOME', 'required', array('message' => 'Дополнительные доходы обязательны к заполнению')),
        array('ADDITIONAL_INCOME', 'numerical', array('message' => 'Дополнительные доходы должны быть числом')),
        array('FAMILY_INCOME', 'required', array('message' => 'Доход семьи обязателен к заполнению')),
        array('FAMILY_INCOME', 'numerical', array('message' => 'Доход семьи должен быть числом')),
        array('METHOD_INCOME_CONFIRMATION', 'required', array('message' => 'Способ подтверждения дохода обязателен к заполнению')),
        array('PROGRAM_CREDIT', 'required', array('message' => 'Программа кредитования обязательна к заполнению')),
        array('REQUESTED_AMOUNT', 'required', array('message' => 'Запрашиваемая сумма обязательна к заполнению')),
        array('REQUESTED_AMOUNT', 'numerical', array('message' => 'Запрашиваемая сумма должна быть числом')),
        array('CREDIT_TERM', 'required', array('message' => 'Срок кредита обязателен к заполнению')),
        array('CREDIT_TERM', 'numerical', array('message' => 'Срок кредита должен быть числом')),
        array('PRICE_REAL_ESTATE_OBJECT', 'required', array('message' => 'Стоимость объекта недвижимости обязательна к заполнению')),
        array('PRICE_REAL_ESTATE_OBJECT', 'numerical', array('message' => 'Стоимость объекта недвижимости должна быть числом')),
        array('AMOUNT_INITIAL_CONTRIBUTION', 'required', array('message' => 'Сумма первоначального взноса обязательна к заполнению')),
        array('AMOUNT_INITIAL_CONTRIBUTION', 'numerical', array('message' => 'Сумма первоначального взноса должна быть числом')),
        array('agreement', 'required', array('message' => 'Вы не согласились с условиями')),
    ),
    $_POST
);
$file = empty($_FILES['file']['name']) ? array() : $_FILES['file'];

$errors = array();

if(!empty($file['tmp_name']) && is_uploaded_file($file['tmp_name'])){
    if (!preg_match('~\\.(?:docx?|txt|pdf)$~u', $file['name'])) {
        $errors['file'] = 'Неверный формат файла. Допустимые форматы файла: txt,doc,docx,pdf';
    } else {
        if(($fileIdNew = CFile::SaveFile($file)))
            $filePath = CFile::MakeFileArray($fileIdNew);
        else
            $errors['file'] = 'Ошибка при сохранении файла';
    }
}else{
    $filePath="";
    /*$errors['file'] = 'Файл обязателен к заполнению';*/
}


$status = empty($errors);

//check form fields
if ($form->validate())
{
    /**
     * File uploaded, all ok
     */
    if($status)
    {
        $elementId = $form->addRecord(
            \LIblock::getId('hypothec'),
            array(
                'NAME' => $form->getField('SURNAME') . ' ' . $form->getField('NAME') . ' ' . $form->getField('PATRONUMIC'),
                'PROPERTY_VALUES' => array(
                    'USER' => \CUser::GetID(),
                    'SURNAME' => $form->getField('SURNAME'),
                    'NAME' => $form->getField('NAME'),
                    'PATRONUMIC' => $form->getField('PATRONUMIC'),
                    'DATE_BIRTH' => date("d.m.Y", strtotime($form->getField('date'))),
                    'PHONE' => $form->getField('PHONE'),
                    'EMAIL' => $form->getField('EMAIL'),
                    'EDUCATION_LEVEL' => array('VALUE' => $form->getField('EDUCATION_LEVEL')),
                    'MARITAL_STATUS' => array('VALUE' => $form->getField('MARITAL_STATUS')),
                    'MARRIAGE_CONTRACT' => array('VALUE' => $form->getField('MARRIAGE_CONTRACT')),
                    'QUANTITY_MINOR_CHILDREN' => $form->getField('QUANTITY_MINOR_CHILDREN'),
                    'REGION' => $form->getField('REGION'),
                    'CITY' => $form->getField('CITY'),
                    'STATUS_HOUSING' => array('VALUE' => $form->getField('STATUS_HOUSING')),
                    'EMPLOYMENT_TYPE' => array('VALUE' => $form->getField('EMPLOYMENT_TYPE')),
                    'TYPE_LABOR_CONTRACT' => array('VALUE' => $form->getField('TYPE_LABOR_CONTRACT')),
                    'EXPERIENCE_CURRENT_WORK' => $form->getField('EXPERIENCE_CURRENT_WORK'),
                    'EXPERIENCE_TOTAL' => $form->getField('EXPERIENCE_TOTAL'),
                    'SALARY_PROJECT_BANK' => array('VALUE' => $form->getField('SALARY_PROJECT_BANK')),
                    'BASIC_SALARY' => $form->getField('BASIC_SALARY'),
                    'ADDITIONAL_INCOME' => $form->getField('ADDITIONAL_INCOME'),
                    'FAMILY_INCOME' => $form->getField('FAMILY_INCOME'),
                    'METHOD_INCOME_CONFIRMATION' => array('VALUE' => $form->getField('METHOD_INCOME_CONFIRMATION')),
                    'PROGRAM_CREDIT' => array('VALUE' => $form->getField('PROGRAM_CREDIT')),
                    'REQUESTED_AMOUNT' => $form->getField('REQUESTED_AMOUNT'),
                    'CREDIT_TERM' => $form->getField('CREDIT_TERM'),
                    'PRICE_REAL_ESTATE_OBJECT' => $form->getField('PRICE_REAL_ESTATE_OBJECT'),
                    'AMOUNT_INITIAL_CONTRIBUTION' => $form->getField('AMOUNT_INITIAL_CONTRIBUTION'),
                    'DOWNLOAD_DOCUMENT' => $filePath
                ),
                'ACTIVE' => 'N',
            ));
        $status = (bool)$elementId;
        $requestEditLink = null;
    }
    /**
     * Element was added, send email
     */
    if ($status)
    {
        $requestEditLink = Helper::getFullUrl(
            '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='
                . \LIblock::getId('hypothec')
                . '&type=personal_office&ID='
                . $elementId
                . '&lang=ru&find_section_section=-1'
        );
        //send message
        $status = $form->sendMessage('PERSONAL_HYPOTHEC_FORM', array(
            'SURNAME' => $form->getField('SURNAME'),
            'NAME' => $form->getField('NAME'),
            'PATRONUMIC' => $form->getField('PATRONUMIC'),
            'PHONE' => $form->getField('PHONE'),
            'REQUEST_EDIT_LINK' => $requestEditLink,
        ));
    }
    else
    {
        /**
         * We need to remove uploaded file
         */
        if(!empty($fileIdNew))
            \CFile::Delete($fileIdNew);
    }
    echo json_encode($status ? array('success' => true) : array('errors' => array_merge($errors, $form->getErrors())));
} else
    echo json_encode(array('errors' => array_merge($errors, $form->getErrors())));
