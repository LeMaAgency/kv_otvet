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

use Lema\Common\Helper as H,
    Lema\Template\TemplateHelper as TH,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$this->setFrameMode(true);

$data = new TH($this);

?>
<? if ($data->itemCount()): ?>
    <div class="col-sm-6">
        <div id="<?= $arParams['ROOM_MAP_ID']; ?>" class="new-flats__map"></div>
    </div>
    <div class="col-sm-6">
        <? foreach ($data->items() as $item): ?>
            <div class="card-flat_min" <?= $item->editId(); ?>>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-flat__content">
                            <div class="card-flat__content__head clearfix">
                                <h3 class="card-flat__content__head__title_min color_002b4e"><?= $item->getName(); ?></h3>
                                <div class="card-flat__content__head__price card-flat__content__head__price_min">
                                    <b><?= H::formatPrice($item->propVal('PRICE'), null); ?></b>
                                    <?= Loc::getMessage('LEMA_ROOMS_RUB'); ?>
                                </div>
                            </div>
                            <div class="offers-item-info offers-item-info_min clearfix">
                                <? if ($item->propFilled('ROOMS_COUNT')): ?>
                                    <div class="item-info item-info_room">
                                        <div class="item-info__inner">
                                            <div class="item-info__inner__img item-info__inner__img_room"></div>
                                            <div class="item-info__inner__content">
                                                <div class="item-info-name"><?= $item->propName('ROOMS_COUNT'); ?></div>
                                                <div class="item-info-value"><?= $item->propVal('ROOMS_COUNT'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <? endif; ?>
                                <? if ($item->propFilled('STAGES_COUNT')): ?>
                                    <div class="item-info item-info_floor">
                                        <div class="item-info__inner">
                                            <div class="item-info__inner__img item-info__inner__img_floor"></div>
                                            <div class="item-info__inner__content">
                                                <? if ($item->get('IS_HOUSE_OR_LOT')): ?>
                                                    <div class="item-info-name">Этажность</div>
                                                    <div class="item-info-value">
                                                        <?= $item->propVal('STAGES_COUNT'); ?>
                                                    </div>
                                                <? else: ?>
                                                    <? if ($item->propFilled('STAGE') && $item->propFilled('STAGES_COUNT')): ?>
                                                        <div class="item-info-name"><?= $item->propName('STAGE'); ?></div>
                                                        <div class="item-info-value">
                                                            <?= $item->propVal('STAGE'); ?>
                                                            /<?= $item->propVal('STAGES_COUNT'); ?>
                                                        </div>
                                                    <? endif; ?>
                                                <? endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <? endif; ?>
                                <div class="item-info item-info_area">
                                    <div class="item-info__inner">
                                        <div class="item-info__inner__img item-info__inner__img_area"></div>
                                        <div class="item-info__inner__content">
                                            <? if ($item->propFilled('SQUARE')): ?>
                                                <div class="item-info-name"><?= $item->propName('SQUARE'); ?></div>
                                                <div class="item-info-value">
                                                    <?= $item->propVal('SQUARE'); ?>
                                                    <?= Loc::getMessage('LEMA_SQUARE_M_SUP'); ?>
                                                </div>
                                            <? endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <? if ($item->get('IS_HOUSE_OR_LOT') && $item->propFilled('SQUARE_LAND')): ?>
                                    <div class="item-info item-info_world">
                                        <div class="item-info__inner">
                                            <div class="item-info__inner__img item-info__inner__img_world"></div>
                                            <div class="item-info__inner__content">
                                                <div class="item-info-name"><?= $item->propName('SQUARE_LAND'); ?></div>
                                                <div class="item-info-value">
                                                    <?= $item->propVal('SQUARE_LAND'); ?>
                                                    <?= Loc::getMessage('LEMA_SQUARE_M_SUP'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <? endif; ?>
                            </div>
                            <? if ($item->get('ADDRESS')): ?>
                                <p class="card-flat__content__address card-flat__content__address_min icon-location">
                                    <?= $item->get('ADDRESS'); ?>
                                </p>
                            <? endif; ?>
                            <a href="<?= $item->detailUrl(); ?>"
                               class="element-detail-link offers-item-more offers-item-more_min">
                                <?= Loc::getMessage('LEMA_ROOMS_MORE_BTN'); ?>
                                <i class="more-icon"></i>
                            </a>
                            <div class="map-coordinates"
                                 style="display: none;"
                                <? if (!empty($item->propVal("MAP"))) {
                                    $arCoordinates = explode(",", $item->propVal("MAP"));
                                    ?>
                                    data-lat="<?= $arCoordinates['0']; ?>"
                                    data-lng="<?= $arCoordinates['1']; ?>"
                                    data-address="<?= $item->get('ADDRESS'); ?>"
                                <? } ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? endforeach; ?>
    </div>
<? else: ?>
    <div class="col-md-12 text-center">
        <?= Loc::getMessage('LEMA_NO_OBJECTS_FOUND'); ?>
    </div>
<? endif; ?>
