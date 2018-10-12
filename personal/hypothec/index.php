<?

defined('NEED_AUTH') or define('NEED_AUTH', true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Оформить ипотеку');
?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <? \Lema\Components\Breadcrumbs::inc('breadcrumbs'); ?>
            </div>
        </div>
    </div>

    <div class="container">
        <? $APPLICATION->IncludeComponent('bitrix:menu', 'personal_buttons', array(
            'ALLOW_MULTI_SELECT' => 'N',
            'ROOT_MENU_TYPE' => 'personal',
            'CHILD_MENU_TYPE' => 'left',
            'DELAY' => 'N',
            'MAX_LEVEL' => '1',
            'MENU_CACHE_GET_VARS' => array(),
            'MENU_CACHE_TIME' => '3600',
            'MENU_CACHE_TYPE' => 'A',
            'MENU_CACHE_USE_GROUPS' => 'N',
            'USE_EXT' => 'Y',
            'COMPONENT_TEMPLATE' => 'personal'
        )); ?>
        <div class="row new-rieltor personal-hypotec">
            <div class="col-md-9">
                <div class="add-apartment">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="add-apartment__title"><? $APPLICATION->IncludeFile(SITE_DIR . 'include/hypothec/consultation_title.php'); ?></h3>
                            <span><? $APPLICATION->IncludeFile(SITE_DIR . 'include/hypothec/consultation_text.php'); ?></span>

                            <a href="#" class="add-apartment__button js-feedback-form"><? $APPLICATION->IncludeFile(SITE_DIR . 'include/vakancies/consultation_button_text.php'); ?></a>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 calculator__wrap">
                <?
                $res = \CUser::GetByID(8);
                if($row = $res->Fetch())
                {
                    $arUser = array(
                        'ID' =>$row['ID'],
                        'NAME' => htmlspecialcharsbx(trim($row['LAST_NAME'] . ' ' . $row['NAME'] . ' ' . $row['SECOND_NAME'])),
                        'IMG' => (empty($row['PERSONAL_PHOTO']) ? null : \CFile::GetPath($row['PERSONAL_PHOTO'])),
                        'PHONE' => htmlspecialcharsbx($row[empty($row['WORK_PHONE']) ? 'PERSONAL_PHONE' : 'WORK_PHONE']),
                        'EMAIL' => $row['EMAIL'],
                    );
                }
                ?>
                <div class="callback new-rieltor">
                    <div class="avatar" <?if(!empty($arUser['IMG'])){?>style="background-image:url(<?=$arUser['IMG'];?>);"<?}?>></div>
                    <h5><?=$arUser['NAME'];?></h5>
                    <span>Ваш ипотечный брокер</span>
                    <a href="tel:<?=$arUser['PHONE'];?>">
                        <?=$arUser['PHONE'];?>
                    </a>

                    <form class="realtor-card__form js-rieltor-form" action="/ajax/hypothec_form.php" method="post">
                        <input type="hidden" name="email" value="<?=$arUser['EMAIL'];?>">
                        <div class="it-block">
                            <input class="realtor-card__form__input" type="tel" name="phone" placeholder="Ваш телефон">
                            <div class="it-error"></div>
                        </div>
                        <button class="realtor-card__form__button" type="submit">
                            Жду звонка
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <form class="filter-form form-admin anketa js-hypothec-form"
              action="<?=SITE_DIR."ajax/personal_hypothec_form.php";?>"
              method="POST"
              enctype="multipart/form-data">
            <div class="container-index">
                <div class="section-title form-title">
                    <span>
                        Личная информация
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-field-title">Имя</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="NAME" class="filter-price-input filter-max-value-input"
                               placeholder="Ваше имя">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Отчество</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="PATRONUMIC" class="filter-price-input filter-max-value-input"
                               placeholder="Ваше отчество">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Фамилия</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="SURNAME" class="filter-price-input filter-max-value-input"
                               placeholder="Введите свою фамилию">
                        <div class="it-error"></div>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="filter-field-title">Дата рождения</div>
                    <div class="filter-price it-block">
                        <input type="date" value="" name="date" id="date"
                               class="filter-price-input filter-max-value-input" placeholder="Дата рождения">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">№ телефона</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="PHONE" class="filter-price-input filter-max-value-input btn-accept"
                               placeholder="№ телефона">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Email</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="EMAIL" class="filter-price-input filter-max-value-input"
                               placeholder="Email">
                        <div class="it-error"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-field-title">Уровень образования</div>
                    <div class="filter-select it-block">
                        <a href="#" class="filter-select-link filter-border-color">Выбрать</a>
                        <ul class="filter-select-drop education_level">
                            <li data-value="">Выбрать</li>
                            <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'EDUCATION_LEVEL')) as $data):?>
                                <li data-value="<?= (int) $data['ID'];?>">
                                    <?=htmlspecialcharsbx($data['VALUE']);?>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <input type="hidden" name="EDUCATION_LEVEL" value="">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Семейное положение</div>
                    <div class="filter-select it-block">
                        <a href="#" class="filter-select-link filter-border-color">Выбрать</a>
                        <ul class="filter-select-drop">
                            <li data-value="">Выбрать</li>
                            <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'MARITAL_STATUS')) as $data):?>
                                <li data-value="<?= (int) $data['ID'];?>">
                                    <?=htmlspecialcharsbx($data['VALUE']);?>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <input type="hidden" name="MARITAL_STATUS" value="">
                        <div class="it-error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-field-title object-number">Брачный договор</div>
                    <div class="filter-num-rooms it-block">
                        <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'MARRIAGE_CONTRACT')) as $key => $data):?>
                            <input name="MARRIAGE_CONTRACT"
                                   type="radio"
                                   id="n<?= (int) $data['ID'];?>"
                                   value="<?= (int) $data['ID'];?>"
                                   class="filter-input">
                            <label for="n<?= (int) $data['ID'];?>" class="filter-label">
                                <?=htmlspecialcharsbx($data['VALUE']);?>
                            </label>
                        <?endforeach;?>
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Количество детей до 18 лет</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="QUANTITY_MINOR_CHILDREN" class="filter-price-input filter-max-value-input"
                               placeholder="Количество детей до 18 лет">
                        <small>Оставьте поле пустым, если детей до 18 лет нет</small>
                        <div class="it-error"></div>
                    </div>
                </div>
            </div>
            <div class="container-index">
                <div class="section-title form-title">
                    <span>
                        Место проживания
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-field-title">Область/Край</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="REGION" class="filter-price-input filter-max-value-input"
                               placeholder="Область/Край ">
                        <div class="it-error"></div>
                    </div>

                    <div class="filter-field-title">Город/Поселок</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="CITY" class="filter-price-input filter-max-value-input"
                               placeholder="Город/Поселок">
                        <div class="it-error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-field-title">Статус жилья</div>
                    <div class="filter-select it-block">
                        <a href="#" class="filter-select-link filter-border-color">Выбрать</a>
                        <ul class="filter-select-drop">
                            <li data-value="">Выбрать</li>
                            <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'STATUS_HOUSING')) as $data):?>
                                <li data-value="<?= (int) $data['ID'];?>">
                                    <?=htmlspecialcharsbx($data['VALUE']);?>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <input type="hidden" name="STATUS_HOUSING" value="">
                        <div class="it-error"></div>
                    </div>
                </div>
            </div>

            <div class="container-index">
                <div class="section-title form-title">
                    <span>
                        Служебная информация
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-field-title">Тип занятости</div>
                    <div class="filter-select it-block">
                        <a href="#" class="filter-select-link filter-border-color">Выбрать</a>
                        <ul class="filter-select-drop">
                            <li data-value="">Выбрать</li>
                            <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'EMPLOYMENT_TYPE')) as $data):?>
                                <li data-value="<?= (int) $data['ID'];?>">
                                    <?=htmlspecialcharsbx($data['VALUE']);?>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <input type="hidden" name="EMPLOYMENT_TYPE" value="">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Тип трудового договора</div>
                    <div class="filter-select it-block">
                        <a href="#" class="filter-select-link filter-border-color">Выбрать</a>
                        <ul class="filter-select-drop">
                            <li data-value="">Выбрать</li>
                            <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'TYPE_LABOR_CONTRACT')) as $data):?>
                                <li data-value="<?= (int) $data['ID'];?>">
                                    <?=htmlspecialcharsbx($data['VALUE']);?>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <input type="hidden" name="TYPE_LABOR_CONTRACT" value="">
                        <div class="it-error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-field-title">Стаж на текущем месте работы (в месяцах)</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="EXPERIENCE_CURRENT_WORK" class="filter-price-input filter-max-value-input"
                               placeholder="Стаж">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Общий трудовой стаж (месяцах)</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="EXPERIENCE_TOTAL" class="filter-price-input filter-max-value-input"
                               placeholder="Стаж общий">
                        <div class="it-error"></div>
                    </div>
                </div>
            </div>
            <div class="container-index">
                <div class="section-title form-title">
                    <span>
                        Информация о доходах
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-field-title">Зарплатный проект банка</div>
                    <div class="filter-select it-block">
                        <a href="#" class="filter-select-link filter-border-color">Выбрать</a>
                        <ul class="filter-select-drop">
                            <li data-value="">Выбрать</li>
                            <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'SALARY_PROJECT_BANK')) as $data):?>
                                <?if($data['ID'] == 186 || $data['ID'] == 187){
                                    continue;
                                }?>
                                <li data-value="<?= (int) $data['ID'];?>">
                                    <?=htmlspecialcharsbx($data['VALUE']);?>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <input type="hidden" name="SALARY_PROJECT_BANK" value="">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Основная заработная плата</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="BASIC_SALARY" class="filter-price-input filter-max-value-input"
                               placeholder="Основная заработная плата">
                        <div class="it-error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-field-title">Дополнительные доходы</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="ADDITIONAL_INCOME" class="filter-price-input filter-max-value-input"
                               placeholder="Дополнительные доходы">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Доход семьи</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="FAMILY_INCOME" class="filter-price-input filter-max-value-input"
                               placeholder="Доход семьи">
                        <div class="it-error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-field-title">Способ подтверждения дохода</div>
                    <div class="filter-select it-block">
                        <a href="#" class="filter-select-link filter-border-color">Выбрать</a>
                        <ul class="filter-select-drop">
                            <li data-value="">Выбрать</li>
                            <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'METHOD_INCOME_CONFIRMATION')) as $data):?>
                                <li data-value="<?= (int) $data['ID'];?>">
                                    <?=htmlspecialcharsbx($data['VALUE']);?>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <input type="hidden" name="METHOD_INCOME_CONFIRMATION" value="">
                        <div class="it-error"></div>
                    </div>
                </div>
            </div>
            <div class="container-index">
                <div class="section-title form-title">
                    <span>
                        Информация о запрашиваемом ипотечном кредите
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-field-title">Программа кредитования</div>
                    <div class="filter-select it-block">
                        <a href="#" class="filter-select-link filter-border-color">Выбрать</a>
                        <ul class="filter-select-drop">
                            <li data-value="">Выбрать</li>
                            <?foreach(\LIblock::getPropEnumValues(\LIblock::getPropId('hypothec', 'PROGRAM_CREDIT')) as $data):?>
                                <li data-value="<?= (int) $data['ID'];?>">
                                    <?=htmlspecialcharsbx($data['VALUE']);?>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <input type="hidden" name="PROGRAM_CREDIT" value="">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Запрашиваемая сумма</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="REQUESTED_AMOUNT" class="filter-price-input filter-max-value-input"
                               placeholder="Запрашиваемая сумма">
                        <div class="it-error"></div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="filter-field-title">Срок кредита</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="CREDIT_TERM" class="filter-price-input filter-max-value-input"
                               placeholder="Срок кредита">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Стоимость объекта недвижимости</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="PRICE_REAL_ESTATE_OBJECT" class="filter-price-input filter-max-value-input"
                               placeholder="Стоимость объекта недвижимости">
                        <div class="it-error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-field-title">Сумма первоначального взноса</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="AMOUNT_INITIAL_CONTRIBUTION" class="filter-price-input filter-max-value-input"
                               placeholder="Сумма первоначального взноса">
                        <div class="it-error"></div>
                    </div>
                </div>

                <div class="col-md-6 it-block">
                    <div class="filter-field-title">Загрузить документ</div>
                    <div class="file-upload it-block">
                        <label>
                            <input type="file" name="file">
                            <span>Выберите файл</span>
                        </label>
                        <div class="it-error"></div>
                    </div>
                </div>

            </div>
            <div class="it-block checkbox" style="border:1px solid transparent">
                <label style="margin:5px 10px;">
                    <input type="checkbox" value="1" name="agreement" class="checkbox-152-fz">
                    Я ознакомлен <a target="_blank" href="/contacts/apply.pdf">c положением об обработке и защите персональных данных.</a>
                </label>
                <div class="it-error"></div>
            </div>


            <div class="text-center margin30">
                <button type="reset" name="" value="" class="filter-submit-btn margin30 btn-reset">Отменить</button>
                <button type="submit" name="" value="" class="filter-submit-btn margin30 btn-save">Сохранить</button>
            </div>
        </form>
    </div>
<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>