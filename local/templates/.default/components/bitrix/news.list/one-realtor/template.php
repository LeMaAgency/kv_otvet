<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
if (empty($arResult["ITEMS"]))
    return;
$data = new \Lema\Template\TemplateHelper($this);

$checkElem5 = false;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$itemCount = $data->itemCount();

?>
        <? foreach ($data->items() as $item): ?>

            <div class="callback new-rieltor">
                <div class="avatar" style="background-image: url('<?= CFile::getPath($item->get("PERSONAL_PHOTO")); ?>')!important;"></div>
                <h5><?= $item->getName(); ?> <?= $item->get("LAST_NAME"); ?></h5>
                <span>Ваш ипотечный брокер</span>
                <a href="tel:<?=preg_replace('~\\D+~', '', $item->get('PERSONAL_PHONE'));?>">
                    <?=$item->get('PERSONAL_PHONE');?>
                </a>
                <form class="realtor-card__form js-rieltor-form" action="/ajax/rieltor_call.php" method="post">
                    <input type="hidden" name="rieltor_id" value="<?=$item->getId();?>">
                    <div class="it-block">
                        <label>заказать звонок</label>
                        <input class="realtor-card__form__input" type="tel" name="phone" placeholder="Ваш телефон">
                        <div class="it-error"></div>
                    </div>
                    <button class="realtor-card__form__button" type="submit">
                        <?=Loc::getMessage('LEMA_REALTORS_CALL');?>
                    </button>
                </form>
            </div>

<?if(false):?>

            <div class="realtors__carousel__item">
                <div class="realtors__carousel__item__wrap">
                    <div class="realtors__carousel__item__img">
                        <img src="<?= CFile::getPath($item->get("PERSONAL_PHOTO")); ?>" alt="realtor">
                    </div>
                    <div class="realtors__carousel__item__description">
                        <div class="realtors__carousel__item__description__name">
                            <?= $item->getName(); ?> <?= $item->get("LAST_NAME"); ?>
                        </div>
                        <div class="realtors__carousel__item__description__title">
                            <?=\Lema\Common\Helper::pluralizeN($item->get('OBJECTS_COUNT'), array(
                                Loc::getMessage('LEMA_REALTORS_ONE_OBJECT'),
                                Loc::getMessage('LEMA_REALTORS_TWO_OBJECTS'),
                                Loc::getMessage('LEMA_REALTORS_MANY_OBJECTS'),
                            ));?>
                        </div>
                        <div class="realtors__carousel__item__description__tel__text">
                            <?= Loc::getMessage("LEMA_REALTORS_CONTACT"); ?>
                        </div>
                        <div class="realtors__carousel__item__description__tel">
                            <?= $item->get("PERSONAL_PHONE"); ?>
                        </div>
                        <div class="realtors__carousel__item__description__link js-realtors-feedback"
                             data-id="<?= $item->getId(); ?>">
                            <a href="#">
                                <?= Loc::getMessage("LEMA_REALTORS_CALL"); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
<?endif;?>
        <? endforeach; ?>

    <div id="realtors-feedback-form" class="fancybox-feedback" style="display: none;">.
        <div class="top-slider-form">
            <form method="post" class="ajax-form realtor-card__form js-rieltor-form call-order" action="/ajax/realtor_form.php" method="post"
                  enctype="multipart/form-data">
                <div class="call-order-title">
                    Обратный звонок
                </div>
                <div class="it-block">
                    <input type="text" id="form_field_name" name="name" placeholder="Имя" class="call-order-input">
                    <div class="it-error"></div>
                </div>
                <div class="it-block">
                    <input type="tel" id="form_field_phone" name="phone" placeholder="Телефон" class="call-order-input">
                    <div class="it-error"></div>
                </div>
                <div class="it-block">
                    <input type="hidden" id="form_field_realtor_id" name="realtor_id" placeholder=""
                           class="call-order-input" value="">
                    <div class="it-error"></div>
                </div>
                <div class="it-block checkbox" style="border: 1px solid transparent;">
                    <label style="margin:5px 10px;">
                        <input type="checkbox" value="1" name="agreement" class="checkbox-152-fz">
                        Я ознакомлен <a target="_blank" href="/contacts/apply.pdf">c положением об обработке и защите
                            персональных данных.</a> </label>
                    <div class="it-error"></div>
                </div>
                <div class="it-block it-buttons">
                    <input type="submit" value="Отправить" class="green-btn">
                </div>
            </form>
        </div>
    </div>