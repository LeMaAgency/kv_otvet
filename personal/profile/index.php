<? defined('NEED_AUTH') or define('NEED_AUTH', true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Личный профиль');

use \Lema\Common\Helper;

$user = new \UserData();
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
        <form class="filter-form form-admin js-personal-office-form" action="<?= SITE_DIR . 'ajax/personal_office_data_form.php' ?>" method="POST">
            <div class="container-index">
                <div class="section-title form-title"><span>* Персональные данные</span></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-field-title">Имя</div>
                    <div class="filter-price">
                        <input type="text" value="<?= $user->get('NAME'); ?>" name="NAME" class="filter-price-input filter-max-value-input"
                               placeholder="Пользователь">
                    </div>
                    <div class="filter-field-title">Отчество</div>
                    <div class="filter-price">
                        <input type="text" value="<?= $user->get('SECOND_NAME'); ?>" name="SECOND_NAME"
                               class="filter-price-input filter-max-value-input" placeholder="Ваше отчество">
                    </div>
                    <div class="filter-field-title">Фамилия</div>
                    <div class="filter-price">
                        <input type="text" value="<?= $user->get('LAST_NAME'); ?>" name="LAST_NAME" class="filter-price-input filter-max-value-input"
                               placeholder="Введите свою фамилию">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-field-title">Пол</div>
                    <div class="filter-select">
                        <a href="#" class="filter-select-link filter-border-color">
                            <? if (empty($user->get('PERSONAL_GENDER'))) { ?>
                                Выбрать
                            <? } elseif ($user->get('PERSONAL_GENDER') == "M") { ?>
                                Мужской
                            <? } else { ?>
                                Женский
                            <? } ?>
                        </a>
                        <ul class="filter-select-drop">
                            <li data-value="">Не определен</li>
                            <li data-value="F">Женский</li>
                            <li data-value="M">Мужской</li>
                        </ul>
                        <input type="hidden" name="PERSONAL_GENDER" value="<?= $user->get('PERSONAL_GENDER'); ?>">
                    </div>

                    <div class="filter-field-title">Дата рождения</div>
                    <div class="filter-price">
                        <input type="text"
                               value="<? if (!empty($user->get('PERSONAL_BIRTHDAY'))){
                                   echo date("Y-m-d", strtotime($user->get('PERSONAL_BIRTHDAY')));
                               }?>"
                               name="PERSONAL_BIRTHDAY"
                               id="PERSONAL_BIRTHDAY"
                               class="filter-price-input filter-max-value-input js-datepicker"
                               placeholder="Дата рождения">
                    </div>
                    <div class="filter-field-title ">Город</div>
                    <div class="filter-select">
                        <a href="#" class="filter-select-link filter-border-color">
                            <? if (empty($user->get('PERSONAL_GENDER'))) { ?>
                                Выбрать
                            <? } else { ?>
                                <?= $user->get('WORK_CITY'); ?>
                            <? } ?>
                        </a>
                        <ul class="filter-select-drop">
                            <li data-value="">Выбрать</li>
                            <? foreach (\LIblock::getPropEnumValues(\LIblock::getPropId('objects', 'CITY')) as $data): ?>
                                <li data-value="<?= htmlspecialcharsbx($data['VALUE']); ?>">
                                    <?= htmlspecialcharsbx($data['VALUE']); ?>
                                </li>
                            <? endforeach; ?>
                        </ul>
                        <input type="hidden" name="WORK_CITY" value="<?= $user->get('WORK_CITY'); ?>">

                    </div>
                </div>
            </div>

            <div class="text-center margin30">
                <input type="hidden" name="FORM_DATA">
                <button type="reset" name="" value="" class="filter-submit-btn margin30 btn-reset">Очистить</button>
                <input type="submit" value="Сохранить" class="filter-submit-btn margin30 btn-save">
            </div>
        </form>

        <div class="filter-form form-admin">

            <div class="container-index">
                <div class="section-title  form-title"><span>* Контактная информация</span></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <form action="<?= SITE_DIR . 'ajax/personal_office_data_form.php' ?>" method="POST"
                          class="filter-form form-admin js-personal-office-form">
                        <div class="filter-field-title">№ телефона</div>
                        <div class="filter-price it-block">
                            <input type="text" value="<?= $user->get('WORK_PHONE'); ?>" name="WORK_PHONE"
                                   class="filter-price-input filter-max-value-input btn-accept" placeholder="№ телефона">
                            <input type="submit" value="Сохранить" class="filter-submit-btn-admin btn-accept">
                            <input type="hidden" name="FORM_PHONE">
                            <div class="it-error"></div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="<?= SITE_DIR . 'ajax/personal_office_data_form.php' ?>" method="POST"
                          class="filter-form form-admin js-personal-office-form">
                        <div class="filter-field-title">Email</div>
                        <div class="filter-price it-block">
                            <input type="text" value="<?= $user->get('EMAIL'); ?>" name="EMAIL" class="filter-price-input filter-max-value-input"
                                   placeholder="Email">
                            <input type="submit" value="Сохранить" class="filter-submit-btn-admin btn-accept">
                            <input type="hidden" name="FORM_EMAIL">
                            <div class="it-error"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <form action="<?= SITE_DIR . 'ajax/personal_office_data_form.php' ?>" method="POST" class="filter-form form-admin js-personal-office-form">
            <div class="container-index">
                <div class="section-title margin30 form-title"><span>* Сменить пароль</span></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-field-title">Старый пароль</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="OLD_PASSWORD" class="filter-price-input filter-max-value-input" placeholder="Старый пароль">
                        <div class="it-error"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="filter-field-title">Новый пароль</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="PASSWORD" class="filter-price-input filter-max-value-input" placeholder="Новый пароль">
                        <div class="it-error"></div>
                    </div>
                    <div class="filter-field-title">Повторите пароль</div>
                    <div class="filter-price it-block">
                        <input type="text" value="" name="CONFIRM_PASSWORD" class="filter-price-input filter-max-value-input"
                               placeholder="Повторите пароль">
                        <div class="it-error"></div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="FORM_PASS">
            <input type="submit" value="Сменить пароль" class="filter-submit-btn">
            <div class="clb margin30"></div>
        </form>

    </div>
<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>