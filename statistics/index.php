<?
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

$APPLICATION->SetTitle('Статистика');
?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?\Lema\Components\Breadcrumbs::inc('breadcrumbs');?>
            </div>
        </div>
    </div>
    <div class="content-page_color">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <h1><?$APPLICATION->ShowTitle(false);?></h1>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <?$APPLICATION->IncludeComponent('lema:statistics', '', array(
                        'IBLOCK_ID' => LIblock::getId('objects'),
                    ));?>
                </div>
            </div>
        </div>
    </div>
<?
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
?>