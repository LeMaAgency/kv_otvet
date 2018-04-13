<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if(empty($arResult['ITEMS']))
    return ;

Loc::loadMessages(__FILE__);

$this->setFrameMode(true);

$data = new TH($this);

?>


<section class="offers">
    <div class="container-index">
        <div class="section-title"><span>Лучшие предложения</span></div>
    </div>
    <div class="container-index no-pad">
        <div class="offers-filter">
            <div class="offers-filter-btn hvr-shutter-out-vertical" data-realty-type="1">Квартиры</div>
            <div class="offers-filter-btn hvr-shutter-out-vertical" data-realty-type="3">Загородная недвижимость</div>
            <div class="offers-filter-btn hvr-shutter-out-vertical" data-rent-type="29">Аренда</div>
        </div>
        <div class="offers-list">
    <?foreach($data->items() as $item):?>


        <a href="<?=$item->detailUrl();?>"
           data-rent-type="<?=(int) $item->prop('RENT_TYPE', 'VALUE_ENUM_ID');?>"
           data-realty-type="<?=(int) $item->prop('REALTY_TYPE', 'VALUE_ENUM_ID');?>"
           class="offers-item" <?=$item->editId();?>>
            <h3 class="offers-item__h3"><?=$item->getName();?></h3>
            <div class="offers-item-img"><img alt="img" src="<?=$item->previewPicture();?>"></div>
            <div class="offers-item-price">
                <b><?=H::formatPrice($item->propVal('PRICE'), null);?></b>
                <?=Loc::getMessage('LEMA_ROOMS_RUB');?>
            </div>
            <div class="offers-item-info clearfix">
                <div class="item-info item-info_room">
                    <div class="item-info__inner">
                        <div class="item-info__inner__img item-info__inner__img_room"></div>
                        <div class="item-info__inner__content">
                            <?if($item->propFilled('ROOMS_COUNT')):?>
                                <div class="item-info-name"><?=$item->propName('ROOMS_COUNT');?></div>
                                <div class="item-info-value"><?=$item->propVal('ROOMS_COUNT');?></div>
                            <?endif;?>
                        </div>
                    </div>
                </div>
                <div class="item-info item-info_floor">
                    <div class="item-info__inner">
                        <div class="item-info__inner__img item-info__inner__img_floor"></div>
                        <div class="item-info__inner__content">
                            <?if($item->propFilled('STAGE') && $item->propFilled('STAGES_COUNT')):?>
                                <div class="item-info-name"><?=$item->propName('STAGE');?></div>
                                <div class="item-info-value">
                                    <?=$item->propVal('STAGE');?>/<?=$item->propVal('STAGES_COUNT');?>
                                </div>
                            <?endif;?>
                        </div>
                    </div>
                </div>
                <div class="item-info item-info_area">
                    <div class="item-info__inner">
                        <div class="item-info__inner__img item-info__inner__img_area"></div>
                        <div class="item-info__inner__content">
                            <?if($item->propFilled('SQUARE')):?>
                                <div class="item-info-name"><?=$item->propName('SQUARE');?></div>
                                <div class="item-info-value">
                                    <?=$item->propVal('SQUARE');?>
                                    <?=Loc::getMessage('LEMA_SQUARE_M_SUP');?>
                                </div>
                            <?endif;?>
                        </div>
                    </div>
                </div>
            </div>
            <?if($item->get('ADDRESS')):?>
                <div class="offers-item-location"><?=$item->get('ADDRESS');?></div>
            <?endif;?>
            <div class="offers-item-more">
                <?=Loc::getMessage('LEMA_ROOMS_MORE_BTN');?>
                <i class="more-icon"></i>
            </div>
        </a>
    <?endforeach;?>
        </div>
        <a href="#" class="offers-show-more">
            <span class="link-hvr">Показать больше</span>
            <i class="offers-show-more-icon"></i>
        </a>
    </div>
</section>