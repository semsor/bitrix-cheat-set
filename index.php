Шаблон

Пролог/подключение языковых файлов (header.php/footer.php)
<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeTemplateLangFile(__FILE__);
?>

Язык шаблона
<html lang="<?=LANGUAGE_ID?>">

Заголовки
<title><?$APPLICATION->ShowTitle();?></title>
<h1><?$APPLICATION->ShowTitle(false,false);?></h1>

Мета данные для head
<?$APPLICATION->ShowHead();?>

Панель в <body>
<?$APPLICATION->ShowPanel();?>

Стили
<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/style.css');?>

Скрипты
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/script.js');?>

--
D7
--
<?
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/fix.js");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/styles/fix.css");
Asset::getInstance()->addString("<link href='http://...' rel='stylesheet' type='text/css'>");
?>

Путь до шаблона
<?=SITE_TEMPLATE_PATH?>
_______________________________________________________________

Включаемая область
<?
$APPLICATION->IncludeComponent(
    "bitrix:main.include", 
    "", 
    array(
        "AREA_FILE_SHOW" => "sect", 
        "AREA_FILE_SUFFIX" => "inc", 
        "AREA_FILE_RECURSIVE" => "Y", 
        "EDIT_TEMPLATE" => ""
    ),
    false
);
?>

Из файла
<?
$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "inc",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/include/name.php"
    )
);
?>
_______________________________________________________________

Проверки url

<?
if ($GLOBALS["APPLICATION"]->GetCurPage() == "/") {} // если в корне

if (CSite::InDir('/about/')) {} // раздел и подразделы

if (strstr($GLOBALS["APPLICATION"]->GetCurPage(), "page")) {} // проверка на совпадение
?>
_______________________________________________________________

Свойства разделов/страницы

<?
if ($APPLICATION->GetProperty("FULL_WIDTH") == "Y") {}
?>

<?
$APPLICATION->SetPageProperty("keywords", "...");
?>
_______________________________________________________________

Фильтр для вывода элементов у которых значение свойства равно определенному значению

<?
global $arFilter;

$arFilter = array(
    "IBLOCK_ID" => 9,
    "ACTIVE" => "Y",
    "PROPERTY_NTV_VALUE" => "Да",
);
// в компоненте "FILTER_NAME" => "arFilter",
?>

Для свойств типа "список"
<?
$enum_list = CIBlockPropertyEnum::GetList(
    array("SORT" => "ASC", "NAME" => "ASC"), 
    array("IBLOCK_ID" => 2, "CODE" => "PROPNAME", "VALUE" => "да")
); 
$arEnumIsMain = $enum_list->GetNext(); 

global $arrFilter;
$arrFilter = array(
    "PROPERTY" => array("PROPNAME" => $arEnumIsMain["ID"])
);
?>
_______________________________________________________________

Пример фильтра для CIBlockElement::GetList по списку

<?
CIBlockElement::GetList(
    array(), // arOrder
    array(
        "IBLOCK_ID" => $arResult["IBLOCK_ID"], 
        "ACTIVE" => "Y", 
        "PROPERTY_SHOW_IN_POPULAR_VALUE" => array("Да")
    ), // arFilter
    false, // arGroupBy
    false, // arNavStartParams
    array() // arSelectFields
);
?>
_______________________________________________________________

Редактирование елементов

<?
$this->AddEditAction(
    $ar_result['ID'], 
    $arItem['EDIT_LINK'], 
    CIBlock::GetArrayByID($arParams['IBLOCK_ID'], "ELEMENT_EDIT")
);
$this->AddDeleteAction(
    $ar_result['ID'], 
    $arItem['DELETE_LINK'], 
    CIBlock::GetArrayByID($arParams['IBLOCK_ID'], "ELEMENT_DELETE"), 
    array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
);
?>

<li id="<?=$this->GetEditAreaId($arItem['ID']);?>"></li>
_______________________________________________________________

Буферизация средствами php

<?
ob_start();
// Код компонента
$GLOBALS['NAME'] = ob_get_contents();
ob_end_clean();
// Вывод $GLOBALS['NAME']
?>
_______________________________________________________________

Буферизация средствами Bitrix

<?
$this->SetViewTarget('NAME');
// Код компонента...
$this->EndViewTarget();
// Вывод $APPLICATION->ShowViewContent("NAME");
?>
_______________________________________________________________

Навигация Следующая - Предыдущая статья.

result_modifier.php
<?
    $arSort = array(
        $arParams["SORT_BY1"] => $arParams["SORT_ORDER1"],
        $arParams["SORT_BY2"] => $arParams["SORT_ORDER2"],
    );
    $arSelect = array("ID", "NAME", "DETAIL_PAGE_URL");
    $arFilter = array (
        "IBLOCK_ID" => $arResult["IBLOCK_ID"],
        // Можно ограничить секцией
        // "SECTION_CODE" => $arParams["SECTION_CODE"], или "SECTION_ID" => $arResult["IBLOCK_SECTION_ID"],
        "ACTIVE" => "Y",
        "CHECK_PERMISSIONS" => "Y",
    );
    // выбирать по 1 соседу с каждой стороны от текущего
    $arNavParams = array(
        "nPageSize" => 1,
        "nElementID" => $arResult["ID"],
    );
    $arItems = array();
    $rsElement = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
    $rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);

    while($obElement = $rsElement->GetNextElement())
        $arItems[] = $obElement->GetFields();
        
    if (count($arItems)==3):
        $arResult["TORIGHT"] = array("NAME" => $arItems[0]["NAME"], "URL" => $arItems[0]["DETAIL_PAGE_URL"]);
        $arResult["TOLEFT"] = array("NAME" => $arItems[2]["NAME"], "URL" => $arItems[2]["DETAIL_PAGE_URL"]);
    elseif (count($arItems)==2):
        if ($arItems[0]["ID"] != $arResult["ID"])
            $arResult["TORIGHT"] = Array("NAME" => $arItems[0]["NAME"], "URL" => $arItems[0]["DETAIL_PAGE_URL"]);
        else
            $arResult["TOLEFT"] = Array("NAME" => $arItems[1]["NAME"], "URL" => $arItems[1]["DETAIL_PAGE_URL"]);
    endif;
    // $arResult["TORIGHT"] и $arResult["TOLEFT"] массивы с информацией о соседних элементах
?>

Вывод в компоненте
<?
    if (is_array($arResult["TOLEFT"])) {
?>
        <a id="pre_page" href="<?=$arResult["TOLEFT"]["URL"]?>"> < <?=$arResult["TOLEFT"]["NAME"]?></a>
<?
    }
    if (is_array($arResult["TORIGHT"])) {
?>
        <a id="next_page" href="<?=$arResult["TORIGHT"]["URL"]?>"><?=$arResult["TORIGHT"]["NAME"]?> > </a>
<?
    }
?>
_______________________________________________________________

Путь к картинке по id

<?
$arOnePhoto = CFile::GetFileArray($PHOTO);
// $arOnePhoto['SRC']
?>
_______________________________________________________________

Если пoльзoвaтeль имeeт прaвa aдминистрaтoрa

<?
if ($USER->Isadmin()) {}
?>
_______________________________________________________________

Пoлучeниe кoличeствa тoвaрoв в нaличии

<?
$ar_res = CCatalogProduct::GetByID($arelement['ID']); 
// $ar_res['QUANTITY']
?>
_______________________________________________________________

Вывод пользовательского свойства раздела типа файл

<? 
$db_list = CIBlockSection::GetList(
    array($by => $order), 
    array("IBLOCK_ID" => 8, "ID" => $arSection['ID']), 
    true,
    array("UF_NAME_IMG")
);

while($ar_result = $db_list->GetNext()):   
    foreach($ar_result["UF_NAME_IMG"] as $PHOTO):
        echo CFile::GetPath($PHOTO);
    endforeach; 
endwhile;
?>

Вывод пользовательского свойства раздела типа список

<?
$sectionProps = array();
$db_list = CIBlockSection::GetList(
    array("SORT"=>"ASC"), 
    array("IBLOCK_ID" => $arResult["IBLOCK_ID"], "ID" => $arResult["ID"]), 
    true, 
    array("UF_PROPS")
);

while($ar_result = $db_list->GetNext()):   
    foreach($ar_result["UF_PROPS"] as $UF_ID):
        $rsEnum = CUserFieldEnum::GetList(array(), array("ID" => $UF_ID));
        $sectionProps[] = $rsEnum->GetNext();
    endforeach;
endwhile;
?>
_______________________________________________________________

Свойства элемента по id

<?
$db_props = CIBlockElement::GetProperty(IBLOCK_ID, ELEMENT_ID, "sort", "asc", array());
$PROPS = array();
while($ar_props = $db_props->Fetch())
    $PROPS[$ar_props['CODE']] = $ar_props['VALUE'];
?>
_______________________________________________________________

Замена стандартного выделения слов в поиске

\www\bitrix\modules\search\classes\general\search.php:317

$str_result = str_replace("%/^%", "</b>", str_replace("%^%","<b>", $str_result));
_______________________________________________________________

Получение элементов инфоблока

<?
$arSelect = Array("ID", "NAME", "PREVIEW_PICTURE");
$arFilter = Array("IBLOCK_ID" => 19, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    print_r($arFields);
}
?> 
_______________________________________________________________

AJAX подгрузка компонентов

<?
define('STOP_STATISTICS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$GLOBALS['APPLICATION']->RestartBuffer();

[ajax code]
?>
_______________________________________________________________

Дополнительные пункты меню

.left.menu_ext.php

<?
if (CModule::IncludeModule("iblock")) {
    global $APPLICATION;
    $aMenuLinksExt = $APPLICATION->IncludeComponent(
        "bitrix:menu.sections",
        "",
        Array(
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "A",
            "DEPTH_LEVEL" => "4",
            "DETAIL_PAGE_URL" => "#SECTION_CODE#/#ELEMENT_CODE#",
            "IBLOCK_ID" => "3",
            "IBLOCK_TYPE" => "content",
            "ID" => $_REQUEST["ID"],
            "IS_SEF" => "Y",
            "SECTION_PAGE_URL" => "#SECTION_CODE#/",
            "SECTION_URL" => "",
            "SEF_BASE_URL" => "/services/"
        )
    );
    $aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
}
?>

C элементами

<?
if (CModule::IncludeModule("iblock")) {

    $aMenuLinksNew = Array();
    $IBLOCK_ID = 3;
    $ctlg_sections = CIBlockSection::GetList(
        Array("ID" => "ASC", " ACTIVE" => "Y"),
        Array("IBLOCK_ID" => $IBLOCK_ID),
        true,
        Array('ID', 'NAME', 'CODE', 'DEPTH_LEVEL')
    );

    while ($ar_fields = $ctlg_sections->GetNext()) {

        array_push(
            $aMenuLinksNew, 
            array(
                $ar_fields['NAME'], 
                $ar_fields['CODE']."/", 
                array(), 
                array(
                    "FROM_IBLOCK" => "1", 
                    "IS_PARENT" => $ar_fields['ELEMENT_CNT'], 
                    "DEPTH_LEVEL" => $ar_fields["DEPTH_LEVEL"]
                ), 
                "",
            ) 
        );

        // elements
        $arSelect = array("ID", "NAME", "CODE");
        $arFilter = array(
            "IBLOCK_ID"=> $IBLOCK_ID, 
            "SECTION_ID" => $ar_fields['ID'], 
            "ACTIVE_DATE"=>"Y", 
            "ACTIVE"=>"Y"
        );
        $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 5000), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            array_push(
                $aMenuLinksNew, 
                array(
                    $arFields['NAME'], 
                    $ar_fields['CODE']."/".$arFields['CODE']."/", 
                    array(), 
                    array(
                        "FROM_IBLOCK"=>"1", 
                        "IS_PARENT"=>"", 
                        "DEPTH_LEVEL"=>"2"
                    ), 
                    "",
                )
            );
        }
 
    }

    $aMenuLinks = array_merge($aMenuLinksNew, $aMenuLinks);
}
?>
____________________________________________________________

Image resize

<?
$arFilters = array(
    array(
        "name" => "watermark", 
        "position" => "tr", 
        "size" => "small", 
        "file" => $_SERVER["DOCUMENT_ROOT"]."/waterm.png"
    )
);  //если нужен водяной знак

// НЕ пропорционально
$renderImage = CFile::ResizeImageGet(
    $PHOTO, 
    array("width" => 200, "height" => 200), 
    $resizeType = BX_RESIZE_IMAGE_EXACT, 
    $bInitSizes = false, 
    $arFilters
); 

// пропорционально
$renderImage = CFile::ResizeImageGet(
    $PHOTO, 
    array("width" => 1200, "height" => 1000), 
    $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL, 
    $bInitSizes = false, 
    $arFilters
);

// $renderImage['src']
?>
_______________________________________________________________

Ресайз и изображение по умолчанию

<? if (strlen($arItem["PREVIEW_PICTURE"]["SRC"]) > 0): ?>

<?
$renderImage = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"],
    Array("width" => 800, "height" => 400), BX_RESIZE_IMAGE_EXACT, false);
?>

<img src="<?= $renderImage["src"] ?>"
     alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>"
     title="<?= $arItem["PREVIEW_PICTURE"]["TITLE"] ?>"
     class="card__image-img"
/>

<? else: ?>
<img src="<?php echo SITE_TEMPLATE_PATH ?>/img/demo-card.jpg"
     class="card__image-img"/>
<? endif ?>

 _______________________________________________________________




Пример news.list с вкладками и бесконечной подгрузкой по кнопке

portfolio.php
<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (isset($_GET['idtab'])) $idtab = $_GET['idtab']; // id активного раздела (для фильтра)
?>

<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "portfolio",
    Array(
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "CHECK_DATES" => "Y",
        "COMPONENT_TEMPLATE" => "portfolio",
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "Y", // для генерации PAGER_ 
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => array(0=>"",1=>"",),
        "FILTER_NAME" => "",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "8",
        "IBLOCK_TYPE" => "design",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "INCLUDE_SUBSECTIONS" => "Y",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "6",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Новости",
        "PARENT_SECTION" => $idtab, // фильтр по конкретному разделу
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => array(0=>"",1=>"",),
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_BY2" => "SORT",
        "SORT_ORDER1" => "DESC",
        "SORT_ORDER2" => "ASC"
    )
);?>

template.php

<?
foreach($arResult["ITEMS"] as $arItem):
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>
    <div id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?=$arItem["NAME"]?></div>
<?
endforeach;
?>

index.php с компонентом

<?
CModule::IncludeModule("iblock");
$newsCounter = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 8, "ACTIVE" => "Y"), array());
?>
<div class="ptabs ptabs_active" data-id="" data-count="<?=$newsCounter?>">Все</div>
<?
$ptabs = CIBlockSection::GetList(array($by=>$order), array('IBLOCK_ID' => 8, 'GLOBAL_ACTIVE' => 'Y'), true);
while ($ar_result = $ptabs->GetNext()) {
    $newsCounter = CIBlockElement::GetList(
        array(), array("IBLOCK_ID" => 8, "ACTIVE" => "Y", "SECTION_ID"=>$ar_result['ID']), array()
    );
    ?>
    <div class="ptabs" data-id="<?=$ar_result['ID']?>" data-count="<?=$newsCounter?>">
        <?=$ar_result['NAME']?> (id:<?=$ar_result['ID']?>)
    </div>
    <?
}
?>

<div>
    <div class="potfolio__list"><?$APPLICATION->IncludeFile("/portfolio.php");?></div>  
    <a href="#" class="show-more">Показать еще</a>
</div>

script.js

<script>
$(document).ready(function(){
    function checkCount(cnt){
        if (currentPage * 6 >= cnt) {
            $('.show-more').hide();
        } else {
            $('.show-more').show();
        }
    }
    var currentPage = 1;
    var path = '/portfolio.php?ajax=Y';
    var count = $('.ptabs_active').data('count');
    checkCount(count);
    $('.ptabs').on('click', function() {
        $('.preloader').fadeIn(0);
        $('.ptabs').removeClass('ptabs_active');
        $(this).addClass('ptabs_active');
        var idtab = $(this).data('id');
        $.get(path, {idtab: idtab}, function(data) {
            $('.potfolio__list').html(data);
            count = $('.ptabs_active').data('count');
            currentPage = 1;
            checkCount(count);
            setTimeout(function() {
                $('.preloader').fadeOut(100);
            }, 500);
        });
    });

    $('.show-more').click(function(e) {
        $('.preloader').fadeIn(0);
        var idtab = $('.ptabs_active').data('id');
        $.get(path, {PAGEN_1: ++currentPage, idtab:idtab}, function(data){
            $('.potfolio__list').append(data);
            checkCount(count);
            setTimeout(function(){
                $('.preloader').fadeOut(100);
            }, 500);
        });
        e.preventDefault();
    });
}
</script>

Если нет возможности узнать общее кол-во (например умный фильтр со scu) проще скрывать по условию:

<?
if ($arResult["NAV_RESULT"]->nEndPage > 1 && $arResult["NAV_RESULT"]->NavPageNomer < $arResult["NAV_RESULT"]->nEndPage):
?>
Показать еще
<?endif;?>
_______________________________________________________________

Получить поля SEO для элемента

<?
CModule::IncludeModule("iblock");
$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues(
    $arCurElem["IBLOCK_ID"], // ID инфоблока
    $arCurElem["ID"] // ID элемента
);
$arElMetaProp = $ipropValues->getValues();
echo '<pre>'; print_r ($arElMetaProp); echo '</pre>';
?>

Поля SEO для раздела

<?
CModule::IncludeModule("iblock");
 
$rsSection = CIBlockSection::GetList(
    array(),
    array(
        "IBLOCK_ID"=>$arParams['IBLOCK_ID'],
        "ACTIVE"=>"Y",
        "=CODE"=>$arParams["SECTION_CODE"]
    ),
    false
);
 
if($arSection = $rsSection->GetNext()){
 
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(
        $arSection["IBLOCK_ID"],
        $arSection["ID"]
    );
    $arSection["IPROPERTY_VALUES"] = $ipropValues->getValues();
    echo "<pre>"; print_r($arSection); echo '</pre>';
}
?>
_______________________________________________________________

Получение всего списка highload блоков

<?
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
$MY_HL_BLOCK_ID = 3;
CModule::IncludeModule('highloadblock');
function GetEntityDataClass($HlBlockId) {
    if (empty($HlBlockId) || $HlBlockId < 1) return false;
    $hlblock = HLBT::getById($HlBlockId)->fetch();   
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    return $entity_data_class;
}
$entity_data_class = GetEntityDataClass($MY_HL_BLOCK_ID);
$rsData = $entity_data_class::getList(array(
   'select' => array('*')
));
while ($el = $rsData->fetch()) {
    print_r($el);
}
?>

или

<?
if (CModule::IncludeModule('highloadblock')) {
    $Date = date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
    $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(3)->fetch();
    $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();
    $resData = $strEntityDataClass::getList(array(
        'select' => array('*'),
    ));
    while ($arItem = $resData->Fetch()) {
        echo "<pre>"; print_r($arItem); echo "</pre>";
    }
}
?>
_______________________________________________________________

Добавление номера страницы в title

каталог/section.php
<?
    CModule::IncludeModule("iblock");
    $arSection = array();
    if ($arResult["VARIABLES"]["SECTION_ID"]>0) {
        $arFilter = array('IBLOCK_ID'=>$arParams["IBLOCK_ID"], 'GLOBAL_ACTIVE'=>'Y', "ID" => $arResult["VARIABLES"]["SECTION_ID"]);
    } elseif(strlen(trim($arResult["VARIABLES"]["SECTION_CODE"]))>0) {
      $arFilter = array('IBLOCK_ID'=>$arParams["IBLOCK_ID"], 'GLOBAL_ACTIVE'=>'Y', "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"]);
    }
    $db_list = CIBlockSection::GetList(array(), $arFilter, true, array("ID", "NAME", "IBLOCK_ID", "DEPTH_LEVEL", "IBLOCK_SECTION_ID", "CODE"));

    while($section = $db_list->GetNext())
        $arSection = $section;

    $arSelect = array("ID");

    if ($arParams["LIST_BROWSER_TITLE"]) $arSelect = array_merge($arSelect, (array)$arParams["LIST_BROWSER_TITLE"]);
    if ($arParams["LIST_META_KEYWORDS"]) $arSelect = array_merge($arSelect, (array)$arParams["LIST_META_KEYWORDS"]);
    if ($arParams["LIST_META_DESCRIPTION"]) $arSelect = array_merge($arSelect, (array)$arParams["LIST_META_DESCRIPTION"]);

    $arSectionFull = CIBlockSection::GetList(
        array(), 
        array("ID" => $arSection["ID"], "IBLOCK_ID"=> $arSection["IBLOCK_ID"]), 
        false, 
        $arSelect, 
        false
    )->GetNext();

    if ($_GET["PAGEN_1"]) $page = GetMessage('PAGE').$_GET["PAGEN_1"];

    if ($arParams["LIST_BROWSER_TITLE"] && $arSectionFull[$arParams["LIST_BROWSER_TITLE"]]) $APPLICATION->SetPageProperty("title", $arSectionFull[$arParams["LIST_BROWSER_TITLE"]].$page);

    // new title
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(
        $arSection["IBLOCK_ID"],
        $arSection["ID"]
    );
    
    $arSection["IPROPERTY_VALUES"] = $ipropValues->getValues();
    $APPLICATION->SetPageProperty("title", $arSection["IPROPERTY_VALUES"]["SECTION_META_TITLE"].$page);
?>

Детальная

<?
if(CModule::IncludeModule("iblock")){
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues(
        $arParams['IBLOCK_ID'],
        $ElementID
    );
    
    $arElMetaProp = $ipropValues->getValues();

    $title = $arElMetaProp["ELEMENT_META_TITLE"] ? $arElMetaProp["ELEMENT_META_TITLE"] : $APPLICATION->GetTitle();
    $description = $arElMetaProp["ELEMENT_META_DESCRIPTION"] ? $arElMetaProp["ELEMENT_META_DESCRIPTION"] : $APPLICATION->GetProperty("description");

    if($_GET["PAGEN_2"]){
        $APPLICATION->SetPageProperty('title', $title.". Страница ".$_GET["PAGEN_2"].".");
        $APPLICATION->SetPageProperty('description', $description.". Страница ".$_GET["PAGEN_2"].".");
    }
}
?>
_______________________________________________________________

HTTPS

dbconn.php
$_SERVER["HTTPS"] = "On";
_______________________________________________________________

CAPTCHA

template.php

<?
$code =  $APPLICATION->CaptchaGetCode();
?>
<img src="/bitrix/tools/captcha.php?captcha_code=<?=$code?>">
<input id="captcha_word" name="captcha_word" type="text" placeholder="Введите слово на картинке">
<input name="captcha_code" value="<?=$code?>" type="hidden">

Проверка

<?
if (!$APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_code"])) {
    // Неправильное значение
} else {
    // Правильное значение
}
?>

Пример обновления

<div class="captcha__update">обновить картинку</div>
<script>
$('.captcha__update').click(function(){
    $.getJSON('<?=$path?>/reload_captcha.php', function(data) {
         $('.captcha__img').attr('src','/bitrix/tools/captcha.php?captcha_sid='+data);
         $('.captcha__validate').val(data);
    });
});
</script>

reload_captcha.php
<?
echo json_encode($APPLICATION->CaptchaGetCode());
?>
_______________________________________________________________

Вывод переменных до их определения

Добавляем ссылку в h1 в шаблоне компонента header.php:
<?$APPLICATION->ShowViewContent('news');?><!-- content -->

Добавляем в шаблон компонента:
<?$this->SetViewTarget('news');?>
    <!-- content -->
<?$this->EndViewTarget();?>
_______________________________________________________________

Генерация url с заменой параметров и сохранением старых

<?=$APPLICATION->GetCurPageParam ('sort=price&method=asc', array('sort', 'method'))?>
_______________________________________________________________

Добавление полей к шаблону письма

<?
AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");

function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
    $arOrder = CSaleOrder::GetByID($orderID);

    $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);

    $city_name = "";  
    $address = "";

    while ($arProps = $order_props->Fetch()){

        if ($arProps["CODE"] == "PHONE") $phone = htmlspecialchars($arProps["VALUE"]);

        if ($arProps["CODE"] == "INDEX") $index = $arProps["VALUE"];   
        if ($arProps["CODE"] == "ADDRESS") $address = $arProps["VALUE"];

        if ($arProps["CODE"] == "LOCATION") {
            $arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
            $country_name =  $arLocs["COUNTRY_NAME_ORIG"];
            $city_name = $arLocs["CITY_NAME_ORIG"];
        }

        /* юр лицо */
        if ($arProps["CODE"] == "COMPANY") $company = $arProps["VALUE"];  
        if ($arProps["CODE"] == "COMPANY_ADR") $company_adr = $arProps["VALUE"];
        if ($arProps["CODE"] == "INN") $inn = $arProps["VALUE"];
        if ($arProps["CODE"] == "KPP") $kpp = $arProps["VALUE"];
        if ($arProps["CODE"] == "CONTACT_PERSON") $contact_person = $arProps["VALUE"];
        if ($arProps["CODE"] == "FAX") $fax = $arProps["VALUE"];
    }

    // служба доставки
    $arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
    if ($arDeliv) $delivery_name = $arDeliv["NAME"];

    // получаем название платежной системы   
    $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
    if ($arPaySystem) $pay_system_name = $arPaySystem["NAME"];

    // добавляем новые поля в массив результатов
    $arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"] ? $arOrder["USER_DESCRIPTION"] : "-";
    $arFields["PHONE"] =  $phone ? $phone : "-";
    $arFields["DELIVERY_NAME"] =  $delivery_name ? $delivery_name : "-";
    $arFields["PAY_SYSTEM_NAME"] =  $pay_system_name ? $pay_system_name : "-";
    $arFields["FULL_ADDRESS"] = $city_name." ".$address;

    $arFields["COMPANY"] = $company ? $company : "-";
    $arFields["COMPANY_ADR"] = $company_adr ? $company_adr : "-";
    $arFields["INN"] = $inn ? $inn : "-";
    $arFields["KPP"] = $kpp ? $kpp : "-";
    $arFields["CONTACT_PERSON"] = $contact_person ? $contact_person : "-";
    $arFields["FAX"] = $fax ? $fax : "-";

}
?>
_______________________________________________________________

Размещаем некэшируемую область в кэшируемом компоненте

result_modifier.php

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->__component->SetResultCacheKeys(array("CACHED_TPL"));?>

component_epilog.php

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
echo preg_replace_callback(
    "/#VOTE_ID_([\d]+)#/is".BX_UTF_PCRE_MODIFIER,
    create_function('$matches', 
        'ob_start();
        /*тут код который не хотим кэшировать*/
        $retrunStr = @ob_get_contents();
        ob_get_clean();
        return $retrunStr;'
    ),
    $arResult["CACHED_TPL"]
);
?>

(в некэшируемом коде вместо обычного $APPLICATION пишем $GLOBALS["APPLICATION"], для видимости объекта внутри временной функции)

template.php

В начало
<?ob_start();?>
В конец
<?
$this->__component->arResult["CACHED_TPL"] = @ob_get_contents();
ob_get_clean();
?>

добавляем маркер
<? echo "#VOTE_ID_1#"; ?>
(регулярка #VOTE_ID_([\d]+)# для примера, удобно использовать $matches[1] для ID и т.п.)
_______________________________________________________________

AJAX сравнение

header.php

<script>
    var SITE_DIR = '<?=SITE_DIR?>';
    var CATALOG_COMPARE_LIST = new Object();
    CATALOG_COMPARE_LIST.defaultText = "Сравнить";
    CATALOG_COMPARE_LIST.activeText = "В сравнении";
</script>
<?// сравнение
foreach ($_SESSION["CATALOG_COMPARE_LIST"][2]["ITEMS"] as $value) { 
    ?>
    <script>CATALOG_COMPARE_LIST[<?=$value["ID"]?>] = true;</script>    
    <?
}
?>

template.php

В начало
<?ob_start();?>
В конец
<?
$this->__component->arResult["CACHED_TPL"] = @ob_get_contents();
ob_get_clean();
?>
Маркер
<? echo "#COMPARE_FAV_ID_".$arItem['ID']."#"; ?>

component_epilog.php 

<?
echo preg_replace_callback(
    "/#COMPARE_FAV_ID_([\d]+)#/is".BX_UTF_PCRE_MODIFIER,
    create_function('$matches', 
        'ob_start();
        ?>
        <div class="btn-c">
            <a href="#" class="add-compare ctrl-radio <?if(!empty($_SESSION["CATALOG_COMPARE_LIST"][2]["ITEMS"][$matches[1]])):?> ctrl-radio_checked<?endif?>" data-id="<?=$matches[1]?>" >
                <i class="ctrl-radio__ico"></i>
                <span class="add-compare__ttl">
                    <?if (!empty($_SESSION["CATALOG_COMPARE_LIST"][2]["ITEMS"][$matches[1]])):?>
                        В сравнении
                    <?else:?>
                        Сравнить
                    <?endif?>
                </span>
            </a>
        </div>
        <?
        $returnStr = @ob_get_contents();
        ob_get_clean();
        return $returnStr;'
    ),
$arResult["CACHED_TPL"]);
?>

result_modifier.php

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->__component->SetResultCacheKeys(array("CACHED_TPL"));?>

script.js 

<script>
    // compare
    $('body').on('click', '.add-compare', function(e){
        e.preventDefault();

        var compareButton = $(this),
            product_id = compareButton.data('id');

        if (CATALOG_COMPARE_LIST[product_id]) {
            compareButton.removeClass('ctrl-radio_checked').find('.add-compare__ttl').text(CATALOG_COMPARE_LIST.defaultText);
            $.ajax({
                url: SITE_DIR + 'catalog/?action=DELETE_FROM_COMPARE_LIST&id=' + product_id,
                type: 'post',
                success: function (data) {
                    $('.bx_catalog-compare-list').replaceWith(data);
                }
            });
            delete CATALOG_COMPARE_LIST[product_id];
        } else {
            compareButton.addClass('ctrl-radio_checked').find('.add-compare__ttl').text(CATALOG_COMPARE_LIST.activeText);
            $.ajax({
                url: SITE_DIR + 'catalog/?action=ADD_TO_COMPARE_LIST&id=' + product_id,
                type: 'post',
                success: function (data) {
                    $('.bx_catalog-compare-list').replaceWith(data);
                }
            });
            CATALOG_COMPARE_LIST[product_id] = true;
        }
    });

    $('body').on('click', '.bx_catalog_compare_form [data-id]', function() {
        var delId = $(this).data('id');
        delete CATALOG_COMPARE_LIST[delId];
        $('.add-compare[data-id='+delId+']').removeClass('ctrl-radio_checked').find('.add-compare__ttl').text(CATALOG_COMPARE_LIST.defaultText);
    });
</script>

sections.php

<?
if ($_REQUEST["action"]=="ADD_TO_COMPARE_LIST" || $_REQUEST["action"]=="DELETE_FROM_COMPARE_LIST") {
    $APPLICATION->RestartBuffer();
    if ($arParams["USE_COMPARE"]=="Y") {
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.compare.list",
            "",
            array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "NAME" => $arParams["COMPARE_NAME"],
                "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                "COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],

            ),
            $component
        );
    }
    die();
}
?>
_______________________________________________________________

AJAX избранное

header.php

<script>
    var SITE_DIR = '<?=SITE_DIR?>';
    var DELAY_LIST = new Object();
</script>

template.php

В начало
<?ob_start();?>
В конец
<?
$this->__component->arResult["CACHED_TPL"] = @ob_get_contents();
ob_get_clean();
?>
Маркер
<? echo "#COMPARE_FAV_ID_".$arItem['ID']."#"; ?>

component_epilog.php

<?
echo preg_replace_callback(
    "/#COMPARE_FAV_ID_([\d]+)#/is".BX_UTF_PCRE_MODIFIER,
    create_function('$matches', 'ob_start();
    ?>
        <? global $favourites; ?>
        <a href="#" class="b-popular-tiles__fav btn-fav add-delay<?
            if (in_array($matches[1], $favourites)):
                ?> btn-fav_active js-check_checked<?
            endif;
            ?>" data-id="<?=$matches[1]?>">
                <i class="icon png-icons btn-fav__ico"></i>
                <span class="add-delay__ttl">
                    <?if (in_array($matches[1], $favourites)):?>
                        В избранном
                    <?else:?>
                        В избранное
                    <?endif?>
                </span>
        </a>
    <?
    $returnStr = @ob_get_contents();
    ob_get_clean();
    return $returnStr;'),
$arResult["CACHED_TPL"]);
?>

result_modifier.php

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->__component->SetResultCacheKeys(array("CACHED_TPL"));?>

script.js

<script>

    $('body').on('click', '.add-delay', function(e){
        e.preventDefault();

        var delayButton = $(this),
            product_id = delayButton.data('id'),
            productPrice = parseFloat(delayButton.parents('.product-item').find('.cur-price').text().replace(/ /g,''));

        if (DELAY_LIST[product_id]) {
            delayButton.removeClass('btn-fav_active js-check_checked').find('.add-delay__ttl').html('В избранное');
            $.post(SITE_DIR + 'ajax/addDelay.php?action=DELAY_DELETE&id=' + product_id);
            delete DELAY_LIST[product_id];
        } else {
            delayButton.addClass('btn-fav_active js-check_checked').find('.add-delay__ttl').html('В избранном');
            $.post(SITE_DIR + 'ajax/addDelay.php?action=DELAY_ADD&id=' + product_id + '&PRICE=' + productPrice);
            DELAY_LIST[product_id] = true;
        }

        $('#head-favourite__info').html(Object.keys(DELAY_LIST).length);
    });

</script>

ajax/addDelay.php

<? 
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule("iblock")) {

    $productID = htmlspecialchars($_REQUEST["id"]);
    $productPrice = htmlspecialchars($_REQUEST["PRICE"]);

    if ($_REQUEST['action'] == 'DELAY_ADD') {
        $res = CIBlockElement::GetList(Array(), array("ID" => $productID), false, Array(), array("NAME"));
        if ($ob = $res->Fetch())
            $name = $ob['NAME'];

        // $ar_res = CPrice::GetBasePrice($productID);

        $arFields = array(
            "PRODUCT_ID" => $productID,
            "PRICE" => $productPrice,
            "CURRENCY" => 'RUB',
            // "PRICE" => $ar_res["PRICE"],            
            // "CURRENCY" => $ar_res["CURRENCY"],
            "QUANTITY" => 1,
            "LID" => LANG,
            "DELAY" => "Y",
            "NAME" => $name,
        );

        CSaleBasket::Add($arFields);
    }

    if ($_REQUEST['action'] == 'DELAY_DELETE') {
        $arFilter = array(
            "FUSER_ID" => CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => "NULL",
            "DELAY" => "Y",
            "PRODUCT_ID" => $productID,
        );

        $dbBasketItems = CSaleBasket::GetList(array(), $arFilter, false, false, array("ID"));
        if ($arItems = $dbBasketItems->Fetch()) {
            CSaleBasket::Delete($arItems['ID']);
        }
    }

    $cntDelay = array();
    $dbBasketItems = CSaleBasket::GetList(array(), array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL", "DELAY" => "Y"), false, false, array("ID"));
    while ($arItems = $dbBasketItems->Fetch()) {
        $cntDelay[] = $arItems;
    }

    echo count($cntDelay);

}
?>
_______________________________________________________________

Добавление доп свойств к заказу

catalog.element script.js
<script>
function getServices(){
    var services = 'получаем свойства';
    return services;
}

window.JCCatalogElement.prototype.SendToBasket = function()
{
    if (!this.canBuy)
        return;

    this.InitBasketUrl();
    this.FillBasketProps();

    /* доп свойства */
    this.basketParams.addServices = getServices();

    BX.ajax.loadJSON(
        this.basketUrl,
        this.basketParams,
        BX.proxy(this.BasketResult, this)
    );

};
</script>

init.php

<?
AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");
function bxModifySaleMails($orderID, &$eventName, &$arFields)
{

    $additional_information = '';

    $cntBasketItems = CSaleBasket::GetList(
        array(
            "NAME" => "ASC",
            "ID" => "ASC"
        ),
        array(
            "ORDER_ID" => $orderID
        ), 
        false,
        false,
        array()
    );

    $itemIds = array();
    $itemNames = array();

    while ($arItems = $cntBasketItems->Fetch()){
        $itemIds[] = $arItems["ID"];
        $itemNames[$arItems["ID"]] = $arItems["NAME"];
    }
    
    if(!empty($itemIds)) $additional_information .= "Дополнительные услуги:<br>";

    $db_res = CSaleBasket::GetPropsList(
        array(),
        array("ORDER_ID" => $orderID, "BASKET_ID"=>$itemIds,"CODE"=>"ST_ADD_SERV")
    );

    while ($ar_res = $db_res->Fetch()) {

        $additional_information .= $itemNames[$ar_res["BASKET_ID"]]." : ".$ar_res["VALUE"]."<br>";

    }

    $arFields["ADDITIONAL_INFORMATION"] = $additional_information;

    $arOrder = CSaleOrder::GetByID($orderID);
    $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
    
    $full_address = "";

    while ($arProps = $order_props->Fetch()){
        if ($arProps["CODE"] == "COMPANY") $company = htmlspecialchars($arProps["VALUE"]);

        if ($arProps["CODE"] == "EMAIL") $email = htmlspecialchars($arProps["VALUE"]);

        if ($arProps["CODE"] == "PHONE") $phone = htmlspecialchars($arProps["VALUE"]);

        if ($arProps["CODE"] == "LOCATION") {
            $arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
            if($arLocs["COUNTRY_NAME_ORIG"]) $full_address .= $arLocs["COUNTRY_NAME_ORIG"];
            if($arLocs["REGION_NAME_ORIG"]) $full_address .= ", ".$arLocs["REGION_NAME_ORIG"];
            //if($arLocs["CITY_NAME_ORIG"]) $full_address .= ", ".$arLocs["CITY_NAME_ORIG"];

            $parameters = array();
            $parameters['filter']['=CODE'] = $arLocs["CODE"];
            $parameters['filter']['NAME.LANGUAGE_ID'] = "ru";
            $parameters['limit'] = 1;
            $parameters['select'] = array('LNAME' => 'NAME.NAME');
            $arVal = Bitrix\Sale\Location\LocationTable::getList( $parameters )->fetch();
            if($arVal[ 'LNAME' ]) $full_address .= ", ".$arVal[ 'LNAME' ];
        }

        if ($arProps["CODE"] == "ADDRESS") $address = $arProps["VALUE"];

        if ($arProps["CODE"] == "REQUISITES") {
            $requisites = "";
            if (!empty($arProps["VALUE"])) {
                $files = explode(", ",$arProps["VALUE"]);
                foreach ($files as $file) {
                    $requisites .= "<a href='".SITE_SERVER_NAME.CFile::GetPath($file)."'>Скачать</a><br>";
                }
            }
        }
    }

    // тип плательщика
    $db_ptype = CSalePersonType::GetList(Array("SORT" => "ASC"), Array("LID"=>SITE_ID));
    while ($ptype = $db_ptype->Fetch())
        if($ptype["ID"]==$arOrder["PERSON_TYPE_ID"]) $personType = $ptype["NAME"];

    // название службы доставки
    $arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
    if ($arDeliv) $delivery_name = $arDeliv["NAME"];

    // название платежной системы   
    $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
    if ($arPaySystem) $pay_system_name = $arPaySystem["NAME"];

    // добавляем новые поля в массив результатов
    $arFields["ORDER_DESCRIPTION"] = "Комментарий: ".$arOrder["USER_DESCRIPTION"]."<br>";
    $arFields["ORDER_P_TYPE"] = "Тип плательщика: ".$personType."<br>";
    $arFields["ORDER_PHONE"] = "Телефон: ".$phone."<br>";
    $arFields["ORDER_EMAIL"] = "E-mail: ".$email."<br>";
    $arFields["DELIVERY_NAME"] = "Доставка: ".$delivery_name."<br>";
    $arFields["PAY_SYSTEM_NAME"] = "Оплата: ".$pay_system_name."<br>";
    $arFields["ADDRESS"] = "Адрес доставки: ".$address."<br>";
    $arFields["FULL_ADDRESS"] = "Местоположение: ".$full_address."<br>";
    $arFields["COMPANY"] = "Компания: ".$company."<br>";
    $arFields["REQUISITES"] = "Реквизиты: ".$requisites."<br>";
}
?>

в старых версиях создаем новый компонент
/bitrix/components/site-hit/catalog.element/component.php

<?
if ($successfulAdd) {
    if ($_REQUEST["addServices"]) {
        $product_properties[] = array(
            "NAME" => "Дополнительные услуги",
            "CODE" => "ST_ADD_SERV",
            "VALUE" => $_REQUEST["addServices"],
            "SORT" => "1000"
        );
    }
    if (!Add2BasketByProductID($productID, $QUANTITY, $arRewriteFields, $product_properties)) {
        if ($ex = $APPLICATION->GetException())
            $strError = $ex->GetString();
        else
            $strError = GetMessage("CATALOG_ERROR2BASKET");
        $successfulAdd = false;
    }
}
?>

в новых версиях создаем новый компонент и наследуем от /bitrix/modules/iblock/lib/component/base.php
class CatalogElementComponent extends Element

<?
    protected function addProductToBasket($productId, $action)
    {
        /** @global \CMain $APPLICATION */
        global $APPLICATION;

        $successfulAdd = true;
        $errorMsg = '';

        $quantity = 0;
        $productProperties = array();
        $iblockId = (int)\CIBlockElement::GetIBlockByID($productId);

        if ($iblockId > 0)
        {
            $productCatalogInfo = \CCatalogSku::GetInfoByIBlock($iblockId);
            if (!empty($productCatalogInfo) && $productCatalogInfo['CATALOG_TYPE'] == \CCatalogSku::TYPE_PRODUCT)
            {
                $productCatalogInfo = false;
            }
            if (!empty($productCatalogInfo))
            {
                if ($this->arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y')
                {
                    $productIblockId = ($productCatalogInfo['CATALOG_TYPE'] == \CCatalogSku::TYPE_CATALOG
                        ? $productCatalogInfo['IBLOCK_ID']
                        : $productCatalogInfo['PRODUCT_IBLOCK_ID']
                    );
                    $iblockParams = $this->storage['IBLOCK_PARAMS'][$productIblockId];
                    if ($productCatalogInfo['CATALOG_TYPE'] !== \CCatalogSku::TYPE_OFFERS)
                    {
                        if (!empty($iblockParams['CART_PROPERTIES']))
                        {
                            $productPropsVar = $this->request->get($this->arParams['PRODUCT_PROPS_VARIABLE']);
                            if (is_array($productPropsVar))
                            {
                                $productProperties = \CIBlockPriceTools::CheckProductProperties(
                                    $productIblockId,
                                    $productId,
                                    $iblockParams['CART_PROPERTIES'],
                                    $productPropsVar,
                                    $this->arParams['PARTIAL_PRODUCT_PROPERTIES'] === 'Y'
                                );
                                if (!is_array($productProperties))
                                {
                                    $errorMsg = Loc::getMessage('CATALOG_PARTIAL_BASKET_PROPERTIES_ERROR');
                                    $successfulAdd = false;
                                }
                            }
                            else
                            {
                                $errorMsg = Loc::getMessage('CATALOG_EMPTY_BASKET_PROPERTIES_ERROR');
                                $successfulAdd = false;
                            }
                        }
                    }
                    else
                    {
                        $skuAddProps = $this->request->get('basket_props') ?: '';
                        if (!empty($iblockParams['OFFERS_CART_PROPERTIES']) || !empty($skuAddProps))
                        {
                            $productProperties = \CIBlockPriceTools::GetOfferProperties(
                                $productId,
                                $productIblockId,
                                $iblockParams['OFFERS_CART_PROPERTIES'],
                                $skuAddProps
                            );
                        }
                    }
                }
            }
            else
            {
                $errorMsg = Loc::getMessage('CATALOG_PRODUCT_NOT_FOUND');
                $successfulAdd = false;
            }

            if ($this->arParams['USE_PRODUCT_QUANTITY'])
            {
                $quantity = (float)$this->request->get($this->arParams['PRODUCT_QUANTITY_VARIABLE']);
            }

            if ($quantity <= 0)
            {
                $ratioIterator = \CCatalogMeasureRatio::getList(
                    array(),
                    array('PRODUCT_ID' => $productId),
                    false,
                    false,
                    array('PRODUCT_ID', 'RATIO')
                );
                if ($ratio = $ratioIterator->Fetch())
                {
                    $intRatio = (int)$ratio['RATIO'];
                    $floatRatio = (float)$ratio['RATIO'];
                    $quantity = $floatRatio > $intRatio ? $floatRatio : $intRatio;
                }
            }

            if ($quantity <= 0)
            {
                $quantity = 1;
            }
        }
        else
        {
            $errorMsg = Loc::getMessage('CATALOG_PRODUCT_NOT_FOUND');
            $successfulAdd = false;
        }

        $rewriteFields = $this->getRewriteFields($action);

        if ($successfulAdd)
        {
            /* */
            if($_REQUEST["optionsprice"]){
                $productProperties[] = array(
                    "NAME" => "Сумма дополнителных опций (за ед.товара)",
                    "CODE" => "PICKED_OPTIONS_PRICE",
                    "VALUE" => $_REQUEST["optionsprice"]." руб.",
                    "SORT" => "1000"
                );
            }
            if($_REQUEST["edge"] && $_REQUEST["edgeprice"]){
                $productProperties[] = array(
                    "NAME" => "Обработка края",
                    "CODE" => "PICKED_EDGE",
                    "VALUE" => $_REQUEST["edge"],
                    "SORT" => "1100"
                );
            }
            if($_REQUEST["edges"] && $_REQUEST["edgesprice"]){
                $productProperties[] = array(
                    "NAME" => "Отстрочка края",
                    "CODE" => "PICKED_EDGES",
                    "VALUE" => $_REQUEST["edges"],
                    "SORT" => "1200"
                );
            }
            if($_REQUEST["bracing"] && $_REQUEST["bracingprice"]){
                $productProperties[] = array(
                    "NAME" => "Крепления",
                    "CODE" => "PICKED_BRACING",
                    "VALUE" => $_REQUEST["bracing"],
                    "SORT" => "1300"
                );
            }
            if($_REQUEST["logo"] && $_REQUEST["logoprice"]){
                $productProperties[] = array(
                    "NAME" => "Логотип",
                    "CODE" => "PICKED_LOGO",
                    "VALUE" => $_REQUEST["logo"],
                    "SORT" => "1400"
                );
            }
            if($_REQUEST["extra"] && $_REQUEST["extraprice"]){
                $productProperties[] = array(
                    "NAME" => "Дополнительно",
                    "CODE" => "PICKED_EXTRA",
                    "VALUE" => $_REQUEST["extra"],
                    "SORT" => "1500"
                );
            }
            /* */

            if (!Add2BasketByProductID($productId, $quantity, $rewriteFields, $productProperties))
            {
                if ($ex = $APPLICATION->GetException())
                {
                    $errorMsg = $ex->GetString();
                }
                else
                {
                    $errorMsg = Loc::getMessage('CATALOG_ERROR2BASKET');
                }

                $successfulAdd = false;
            }
        }

        return array($successfulAdd, $errorMsg);
    }
?>
_______________________________________________________________

Добавление наценки в корзине (пример с добавлением суммы из выбранных свойств товара)

<?
\Bitrix\Main\EventManager::getInstance()->addEventHandler( 
    'sale', 
    'OnBeforeSaleBasketItemSetField', 
    'changePrice' 
);

function changePrice(\Bitrix\Main\Event $event) 
{ 
    $name = $event->getParameter('NAME'); 
    $value = $event->getParameter('VALUE'); 
    $item = $event->getParameter('ENTITY');

    if ($name === 'PRICE') { 
        $basketPropertyCollection = \Bitrix\Sale\BasketPropertiesCollection::load($item);
        $basketId = $basketPropertyCollection->getBasketId();
        $db_res = CSaleBasket::GetPropsList(
            array(
                    "SORT" => "ASC",
                    "NAME" => "ASC"
                ),
            array("BASKET_ID" => $basketId)
        );
        while ($ar_res = $db_res->Fetch()) {
            if ($ar_res["CODE"] == "PICKED_OPTIONS_PRICE") $value += floatval($ar_res["VALUE"]);
        }
        $event->addResult( 
            new \Bitrix\Main\EventResult( 
                \Bitrix\Main\EventResult::SUCCESS, array('VALUE' => $value) 
            ) 
        ); 
    }
}
?>

_______________________________________________________________

Количество показов элемента 

<?php
$res = CIBlockElement::GetByID($arItem["ID"]);
if($ar_res = $res->GetNext())
    echo 'Просмотров: '.$ar_res['SHOW_COUNTER'];
echo '<br>Дата первого показа: '.$ar_res['SHOW_COUNTER_START'];
?>

_______________________________________________________________

PHP 7

1) Меняем версию на хостинге
2) Меняем mod_php5.c в .htaccess
3) /bitrix/php_interface/dbconn.php
define("BX_USE_MYSQLI", true);
4) /bitrix/.settings.php 
className: 'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
...
'handled_errors_types' => 4437,
'exception_errors_types' => 4437,
5) на сервере отключить вывод E_DEPRECATED
_______________________________________________________________

Логи

dbconn.php
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

AddMessage2Log("Сообщение");
_______________________________________________________________

Список сработавших почтовых событий

(Настройки → Инструменты → SQL-запрос)

select * from b_event 
  where event_name like '%form%' 
  order by date_insert desc




________________________________________________________________


Функции


//Текушая реальная директория
function getRealDir(){
	return $curRealDir = substr($_SERVER["REAL_FILE_PATH"], 0, strrpos($_SERVER["REAL_FILE_PATH"], "/")+1);	
}

function isValidPhone($phone) {
    return preg_match("/^(?:\+?[7,8][-. ]?)?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{2})[-. ]?([0-9]{2})$/", $phone);
}

function CheckPhone($phone){	
	if (strlen(($phone) &gt; 0)
	{
		$phone = str_replace(" ", "", $phone);
		$phone = str_replace("(", "", $phone);
		$phone = str_replace(")", "", $phone);
		$phone = str_replace("-", "", $phone);
		$phone = str_replace("_", "", $phone);
		$phone = str_replace("+7", "", $phone);
			
		if (strlen($phone) == 10)
		{
			return true;		
		}else return false
	}	
}

function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &amp;&amp; strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function getNumEnding($number, $endingArray)
{
    $number = $number % 100;
    if ($number&gt;=11 &amp;&amp; $number&lt;=19) {
        $ending=$endingArray[2];
    }
    else {
        $i = $number % 10;
        switch ($i)
        {
	 case (1): $ending = $endingArray[0]; break;
	 case (2):
	 case (3):
	 case (4): $ending = $endingArray[1]; break;
	 default: $ending=$endingArray[2];
        }
    }
    return $ending;
}

function rus_date() {
	// Перевод
	 $translate = array(
	 "am" =&gt; "дп",
	 "pm" =&gt; "пп",
	 "AM" =&gt; "ДП",
	 "PM" =&gt; "ПП",
	 "Monday" =&gt; "Понедельник",
	 "Mon" =&gt; "Пн",
	 "Tuesday" =&gt; "Вторник",
	 "Tue" =&gt; "Вт",
	 "Wednesday" =&gt; "Среда",
	 "Wed" =&gt; "Ср",
	 "Thursday" =&gt; "Четверг",
	 "Thu" =&gt; "Чт",
	 "Friday" =&gt; "Пятница",
	 "Fri" =&gt; "Пт",
	 "Saturday" =&gt; "Суббота",
	 "Sat" =&gt; "Сб",
	 "Sunday" =&gt; "Воскресенье",
	 "Sun" =&gt; "Вс",
	 "January" =&gt; "Января",
	 "Jan" =&gt; "Янв",
	 "February" =&gt; "Февраля",
	 "Feb" =&gt; "Фев",
	 "March" =&gt; "Марта",
	 "Mar" =&gt; "Мар",
	 "April" =&gt; "Апреля",
	 "Apr" =&gt; "Апр",
	 "May" =&gt; "Мая",
	 "May" =&gt; "Мая",
	 "June" =&gt; "Июня",
	 "Jun" =&gt; "Июн",
	 "July" =&gt; "Июля",
	 "Jul" =&gt; "Июл",
	 "August" =&gt; "Августа",
	 "Aug" =&gt; "Авг",
	 "September" =&gt; "Сентября",
	 "Sep" =&gt; "Сен",
	 "October" =&gt; "Октября",
	 "Oct" =&gt; "Окт",
	 "November" =&gt; "Ноября",
	 "Nov" =&gt; "Ноя",
	 "December" =&gt; "Декабря",
	 "Dec" =&gt; "Дек",
	 "st" =&gt; "ое",
	 "nd" =&gt; "ое",
	 "rd" =&gt; "е",
	 "th" =&gt; "ое"
	 );
	// если передали дату, то переводим ее
	if (func_num_args() &gt; 1) {
	$timestamp = func_get_arg(1);
	return strtr(date(func_get_arg(0), $timestamp), $translate);
	} else {
   // иначе текущую дату
	return strtr(date(func_get_arg(0)), $translate);
	}
}
 

// Загружает файлы, вернет массив, имена и реальный путь файлов

function Upload_Files($uploaddir, $filename){

	foreach($_FILES[$filename]['error'] as $k=&gt;$v)
		{
			$uploadfile = $uploaddir. basename($_FILES['FILES']['name'][$k]);				
			if(move_uploaded_file($_FILES[$filename]['tmp_name'][$k], $uploadfile)) 
				{
					$arFiles[]= $_FILES[$filename]['name'][$k];
				}
		}
				
	return $arFiles;

}

function Create_Archive($tmpdir, $uploaddir, $arFiles, $outfilename){

		if(extension_loaded('zip')){

			$zip = new ZipArchive();			
			$zip_name = $uploaddir.$outfilename.".zip";
					
			$zip-&gt;open($zip_name, ZIPARCHIVE::CREATE);
						
			if($zip-&gt;open($zip_name, ZIPARCHIVE::CREATE)!==TRUE){
				$result['errors'] = "Error, ZIP creation failed at this time\n";
			}
					
			foreach($arFiles as $file){
				$zip-&gt;addFile($tmpdir.$file, $file);
			}
						
			$zip-&gt;close();		
						
			if(file_exists($zip_name)){					
				return $zip_name;
			}					
					
		}else echo "You dont have ZIP extension";

}



function grd($name, $type = 'str') { // -- данные из REQUEST --
	global $_REQUEST;
	if (!isset($_REQUEST[$name])) return null;
	$result = null;
	if (is_array($_REQUEST[$name])) $result = grd_array($_REQUEST[$name], $type);
	else {
		$result = strip_tags(trim($_REQUEST[$name]));
		if ($type == 'str') $result = addslashes($result);
		elseif ($type == 'int') $result = intval($result);
	}
	return $result;
}


function gcd($name, $type = 'str', $json_decode = false) { // -- данные из COOKIE --
	global $_COOKIE;
	$data = $json_decode ? json_decode($_COOKIE[$name]) : $_COOKIE[$name];
	if (!isset($data)) return null;
	if (is_array($data)) $result = grd_array($data, $type);
	else if (is_object($data)) $result = grd_object($data, $type);
	else {
		$result = strip_tags(trim($data));
		if ($type == 'str') $result = addslashes($result);
		elseif ($type == 'int') $result = intval($result);
	}
	return $result;
}


function cleanInput($input) { 
  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Удаляем javascript
    '@&lt;;[\/\!]*?[^&lt;&gt;]*?&gt;@si', // Удаляем HTML теги
    '@<style[^>]*?>.*?</style>@siU',    // Удаляем теги style
    '@@'         // Удаляем многострочные комментарии
  );
  $output = preg_replace($search, '', $input);
  return $output;
}


function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=&gt;$val) {
 $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
 $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $output = mysql_real_escape_string($input);
    }
    return $output;
}


function grd_array($var, $type) {
	foreach ($var as $k =&gt; $v) {
		if (is_array($v)) $result[$k] = grd_array($v, $type);
		else if (is_object($v)) $result[$k] = grd_object($v, $type);
		else {
			$result[$k] = strip_tags(trim($v));
			if ($type == 'str') @$result[$k] = addslashes($v);
			elseif ($type == 'int') @$result[$k] = intval($v);
		}
	}
	return $result;
}


function grd_object($var, $type) {
	foreach (get_object_vars($var) as $k =&gt; $v) {
		if (is_array($v)) $var-&gt;$k = grd_array($v, $type);
		else if (is_object($v)) $var-&gt;$k = grd_object($v, $type);
		else {
			$var-&gt;$k = strip_tags(trim($v));
			if ($type == 'str') @$var-&gt;$k = addslashes($v);
			elseif ($type == 'int') @$var-&gt;$k = intval($v);
		}
	}
	return $var;
}


function right_date_format($datestr, $to_mysql = false, $return_delimeter = null) {
	if (!$datestr) return null;
	if (is_int($datestr)) $datestr = date('Y-m-d', $datestr);
	$delimeter = $to_mysql ? '-' : '.';
	$date = explode(($to_mysql ? '.' : '-'), $datestr);
	if ($return_delimeter) $delimeter = $return_delimeter;
	return $date[2].$delimeter.$date[1].$delimeter.$date[0];
}


function right_date($datestr, $time = null, $m_upper = false) {
	if (!$datestr) return null;
	if ($m_upper) $monthes = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
	else $monthes = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	if (is_string($datestr)) {
		if (mb_strlen($datestr) &gt; 10) {
			if ($time) $time = mb_substr($datestr, 11);
			else $datestr = mb_substr($datestr, 0, 10);
		}
		$date = explode('-', $datestr);
	} else {
		$date_string = date('Y-m-d H:i', $datestr);
		if ($time) $time = mb_substr($date_string, 11);
		else $date_string = mb_substr($date_string, 0, 10);
		$date = explode('-', $date_string);
	}
	return $date[2].' '.$monthes[intval($date[1])-1].' '.$date[0].($time ? ' '.$time : null);
}


function format_date($date, $time = null, $type = null) { // -- форматирует дату --
    $out_date = $out_time = null;
    if (strstr($date,':')) $out_time = ' '.substr($date, 11, 10);
    if ((int)substr($date, 8, 2)) $out_date .= substr($date,8,2).".";
    if ((int)substr($date, 5, 2)) $out_date .= substr($date,5,2).".";
    if ((int)substr($date, 0, 4)) $out_date .= substr($date,0,4);
    if ($type) {
        $out_date .= " ".substr($date, 11, 2).":";
        $out_date .= "".substr($date, 14, 2);
    }
    return $out_date.($time ? $out_time : '');
}


function timeformat($time=NULL) 
{
    $labelTime = date('d.m.Y', $time); 
 
    $arrM = array( 
      '01'=&gt;'янв', '02'=&gt;'фев', '03'=&gt;'мар', '04'=&gt;'апр',  
      '05'=&gt;'май', '06'=&gt;'июн', '07'=&gt;'июл', '08'=&gt;'авг',  
      '09'=&gt;'сен', '10'=&gt;'окт', '11'=&gt;'ноя', '12'=&gt;'дек'
    ); 
 
    if ($labelTime == date('d.m.Y')) { 
      return 'Сегодня в '.date('H:i', $time); 
    } elseif ($labelTime == (date('d') - 1).'.'.date('m.Y')) { 
      return 'Вчера в '.date('H:i', $time); 
    } else { 
    return date('d', $time).' '.$arrM[date('m', $time)].' '.date('Y', $time).' в '.date('H:i', $time); 
    } 
}   


function create_slug($string){
   $string = strtolower($string);
   $slug=preg_replace('/[^a-z0-9-]+/', '-', $string);
   return $slug;
}
	

function generate_password($length = 20){
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789``-=~!@#$%^&amp;*()_+,./&lt;&gt;?;:[]{}\|';
	$max = mb_strlen($chars) - 1;
	for ($i = 0; $i &lt; $length; $i++) $str .= $chars[rand(0, $max)];	
	return $str;
}


// Сохранение контента в файл
function Save_File($file, $content)
{
    return (file_put_contents($file, stripslashes($content)));
}

//Дата по русски
//echo rusdate(time(), '%dayweek%, j %month% Y, G:i');
function rusdate($d, $format = 'j %month% Y', $offset = 0){
	$montharray = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
	$dayarray=array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье');
	$d += 3600*$offset;
	$sarray = array('/%month%/i', '/%dayweek%/i');
	$rarray = array($montharray[date("m", $d)-1], $dayarray[date("N", $d)-1]);
	$format = preg_replace($sarray, $rarray, $format);
	return date($format, $d);
}

//Получаем реальный IP
function GetRealIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else $ip = $_SERVER['REMOTE_ADDR'];
    return $ip;
}

//Определения города в PHP
function detect_city($ip) {
        $default = 'UNKNOWN';

        if (!is_string($ip) || strlen($ip) &lt; 1 || $ip == '127.0.0.1' || $ip == 'localhost')
 $ip = '8.8.8.8';

        $curlopt_useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)';

        $url = 'http://ipinfodb.com/ip_locator.php?ip=' . urlencode($ip);
        $ch = curl_init();

        $curl_opt = array(
		 CURLOPT_FOLLOWLOCATION  =&gt; 1,
		 CURLOPT_HEADER      =&gt; 0,
		 CURLOPT_RETURNTRANSFER  =&gt; 1,
		 CURLOPT_USERAGENT   =&gt; $curlopt_useragent,
		 CURLOPT_URL       =&gt; $url,
		 CURLOPT_TIMEOUT         =&gt; 1,
		 CURLOPT_REFERER         =&gt; 'http://' . $_SERVER['HTTP_HOST'],
        );

        curl_setopt_array($ch, $curl_opt);

        $content = curl_exec($ch);

        if (!is_null($curl_info)) {
			$curl_info = curl_getinfo($ch);
        }

        curl_close($ch);

        if ( preg_match('{<br>City : ([^&lt;]*)<br>}i', $content, $regs) )  {
		$city = $regs[1];
        }
        if ( preg_match('{<br>State/Province : ([^&lt;]*)<br>}i', $content, $regs) )  {
		$state = $regs[1];
        }

        if( $city!='' &amp;&amp; $state!='' ){
          $location = $city . ', ' . $state;
          return $location;
        }else{
          return $default;
        }

}

//Whois на PHP
function whois_query($domain) { 
	// исправляем доменное имя:
	 $domain = strtolower(trim($domain));
	 $domain = preg_replace('/^http:\/\//i', '', $domain);
	 $domain = preg_replace('/^www\./i', '', $domain);
	 $domain = explode('/', $domain);
	 $domain = trim($domain[0]);
	 
	$_domain = explode('.', $domain);
	 $lst = count($_domain)-1;
	 $ext = $_domain[$lst];
	 
	$servers = array(
	 "biz" =&gt; "whois.neulevel.biz",
	 "com" =&gt; "whois.internic.net",
	 "us" =&gt; "whois.nic.us",
	 "coop" =&gt; "whois.nic.coop",
	 "info" =&gt; "whois.nic.info",
	 "name" =&gt; "whois.nic.name",
	 "net" =&gt; "whois.internic.net",
	 "gov" =&gt; "whois.nic.gov",
	 "edu" =&gt; "whois.internic.net",
	 "mil" =&gt; "rs.internic.net",
	 "int" =&gt; "whois.iana.org",
	 "ac" =&gt; "whois.nic.ac",
	 "ae" =&gt; "whois.uaenic.ae",
	 "at" =&gt; "whois.ripe.net",
	 "au" =&gt; "whois.aunic.net",
	 "be" =&gt; "whois.dns.be",
	 "bg" =&gt; "whois.ripe.net",
	 "br" =&gt; "whois.registro.br",
	 "bz" =&gt; "whois.belizenic.bz",
	 "ca" =&gt; "whois.cira.ca",
	 "cc" =&gt; "whois.nic.cc",
	 "ch" =&gt; "whois.nic.ch",
	 "cl" =&gt; "whois.nic.cl",
	 "cn" =&gt; "whois.cnnic.net.cn",
	 "cz" =&gt; "whois.nic.cz",
	 "de" =&gt; "whois.nic.de",
	 "fr" =&gt; "whois.nic.fr",
	 "hu" =&gt; "whois.nic.hu",
	 "ie" =&gt; "whois.domainregistry.ie",
	 "il" =&gt; "whois.isoc.org.il",
	 "in" =&gt; "whois.ncst.ernet.in",
	 "ir" =&gt; "whois.nic.ir",
	 "mc" =&gt; "whois.ripe.net",
	 "to" =&gt; "whois.tonic.to",
	 "tv" =&gt; "whois.tv",
	 "ru" =&gt; "whois.ripn.net",
	 "org" =&gt; "whois.pir.org",
	 "aero" =&gt; "whois.information.aero",
	 "nl" =&gt; "whois.domain-registry.nl"
	 );
	 
	if (!isset($servers[$ext])){
	 die('Error: No matching nic server found!');
}
 
$nic_server = $servers[$ext];
 
$output = '';
 
 if ($conn = fsockopen ($nic_server, 43)) {
	 fputs($conn, $domain."\r\n");
	 while(!feof($conn)) {
		$output .= fgets($conn,128);
	 }
	 fclose($conn);
 }
 else { die('Error: Could not connect to ' . $nic_server . '!'); }
 
return $output;
}

//Получаем файл
function Get_File($file)
{
    return file_get_contents($file);
}

//Сохраняем файл
function Save_File($file, $content)
{
    return (file_put_contents($file, stripslashes($content)));
}

function strtoSafe(){
	 $result = stripslashes($result); // удаляем слэши
	 $result = str_replace('#39;', '', $result); // удаляем одинарные кавычки
	 $result = str_replace('"', '', $result); // удаляем двойные кавычки
	 $result = str_replace('&amp;', '', $result); // удаляем амперсанд
	 $result = preg_replace('/([?!:^~|@№$–=+*&amp;%.,;\[\]&lt;&gt;()_—«»#\/]+)/', '', $result); // удаляем недоспустимые символы
	 $result = trim($result); // удаляем пробелы по бокам
	 $result = preg_replace('/ +/', '-', $result); // пробелы заменяем на минусы
	 $result = preg_replace('/-+/', '-', $result); // удаляем лишние минусы
	 $result = preg_replace('/([-]*)(.+)([-]*)/', '\\2', $result); // удаляем лишние минусы
}

//Проверка Email на правильность на PHP
function emailValid($string){ 
    if (preg_match ("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+\.[A-Za-z]{2,6}$/", $string)) 
    return true; 
}

//Обрезаем текст правильно
function cropStr($str, $size){ 
  return mb_substr($str,0,mb_strrpos(mb_substr($str,0,$size,'utf-8'),' ',utf-8),'utf-8');
}


function getUserIp() {
  if ( isset($_SERVER['HTTP_X_REAL_IP']) )
  {
    $ip = $_SERVER['HTTP_X_REAL_IP'];
  } else $ip = $_SERVER['REMOTE_ADDR'];
 
  return $ip;
}

function getWord($number, $suffix) {
	$keys = array(2, 0, 1, 1, 1, 2);
	$modern = $number%100;
	$suffix_key = ($modern &gt; 7 &amp;&amp; $modern &lt; 20) ?2:
	$keys[min($modern%10, 5)];
	return $suffix[$suffix_key];
}

$arraymin = array("минута", "минуты", "минут");
//создали массив для минут
$arrayhour = array("час", "часа", "часов");
//создали массив для часов
$datemin = date('i');
$datehour = date('H');
//создали переменное время: часы и минуты раздельно, для удобства
$hour = getWord($datehour, $arrayhour);
$min = getWord($datemin, $arraymin);
//ну и, собственно, сам вывод
echo "".$datehour." ".$hour." ".$datemin." ".$min."";
//в результате получаем: 14 часов 16 минут

	
//Нормализуем и делаем текст безопасным для вставки в базу
function ProcessText($text)
{
    $text = trim($text); // удаляем пробелы по бокам
    $text = stripslashes($text); // удаляем слэши
    $text = htmlspecialchars($text); // переводим HTML в текст
    $text = preg_replace("/ +/", " ", $text); // множественные пробелы заменяем на одинарные
    $text = preg_replace("/(\r\n){3,}/", "\r\n\r\n", $text); // убираем лишние переводы строк (больше 1 строки)
    $test = nl2br ($text); // заменяем переводы строк на тег
    $text = preg_replace("/^\"([^\"]+[^=&gt;&lt;])\"/u", "$1«$2»", $text); // ставим людские кавычки
    $text = preg_replace("/(«){2,}/","«",$text); // убираем лишние левые кавычки (больше 1 кавычки)
    $text = preg_replace("/(»){2,}/","»",$text); // убираем лишние правые кавычки (больше 1 кавычки)      
    $text = preg_replace("/(\r\n){2,}/u", "<br><br>", $text); // ставим абзацы
    return $text; //возвращаем переменную
}

function GetBasePath() {
    return substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME']) - strlen(strrchr($_SERVER['SCRIPT_FILENAME'], "\\")));
}


function GetURI(){
  $this_page = basename($_SERVER['REQUEST_URI']);
  if (strpos($this_page, "?") !== false) $this_page = reset(explode("?", $this_page));
  return $this_page;
}

//Удаляем лишние из входных данных
function clearspecchars($str){
  $str = str_replace(',','',$str);
  $str = str_replace('\'','',$str);
  $str = str_replace('\\','',$str);
  $str = str_replace('/','',$str);
  $str = stripslashes(trim($str));
  $str = htmlspecialchars($str);
  return $str;
}

//Проверка номера телефона на PHP
function check_phone(){
    preg_match_all("/\(?  (\d{3})?  \)?  (?(1)  [\-\s] ) \d{3}-\d{4}/x",
     "Call 555-1212 or 1-800-555-1212", $phones);
}

//Проверка номера телефона на PHP
function checkPhone($number)
{
    if(preg_match('^[0-9]{3}+-[0-9]{3}+-[0-9]{4}^', $number)){
        return $number;
    } else {
        $items = Array('/\ /', '/\+/', '/\-/', '/\./', '/\,/', '/\(/', '/\)/', '/[a-zA-Z]/');
        $clean = preg_replace($items, '', $number);
        return substr($clean, 0, 3).'-'.substr($clean, 3, 3).'-'.substr($clean, 6, 4);
    }
}

//Делаем текст безопасным для вставки в базу новости, статьи и т. д.
function filter_secure_text($text){
  // Фильтрация опасных слов
  if (!preg_match("/script|http|&lt;|&gt;|&lt;|&gt;|SELECT|UNION|UPDATE|exe|exec|INSERT|tmp/i",$text))
  {
   die("ne dopustimie slova");
  }
}


function check_host_ip(){
  $ip = "yandex.ru";

  if(preg_match('/(\d+).(\d+).(\d+).(\d+)/',$ip))
    $host = gethostbyaddr($ip);
  else
    $host = gethostbyname($ip);

  echo $host;
}


function link_clikabel(){
 $stringa = " bla bla bla http://www.example.com bla bla http://www.example.net bla bla bla";

 $m = preg_match_all('/http:\/\/[a-z0-9A-Z.]+(?(?=[\/])(.*))/', $stringa, $match);

   if ($m) {
      $links=$match[0];
      for ($j=0;$j&lt;$m;$j++) {
          $stringa=str_replace($links[$j],''.$links[$j].'',$stringa);
      }
   }
}

//Проверка электронного адреса на PHP
function check_mail($email){
  $email = "phil.taylor@a_domain.tv";

    if (preg_match("/^[^@]*@[^@]*\.[^@]*$/", $email)) {
 return "E-mail address";
    }
}


//Проверка электронного адреса на PHP
function valid_email($email){
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "E-mail is not valid";
  } else {
      echo "E-mail is valid";
  }
}


function check_abcnum($text){
  // Проверяем все символы на буквы и цифры
  /// /[^(\w)|(\x7F-\xFF-)|(\s)]/  английские , русские буквы и цифры
  if( !( preg_match("/^([a-z0-9]*)$/i", $text) ) )
     {
     die("veli nepravilnie simvloi");
    }
}


function isfile($file){
    return preg_match('/^[^.^:^?^\-][^:^?]*\.(?i)' . getexts() . '$/',$file);
    //first character cannot be . : ? - subsequent characters can't be a : ?
    //then a . character and must end with one of your extentions
    //getexts() can be replaced with your extentions pattern
}

function getexts(){
    //list acceptable file extensions here
    return '(app|avi|doc|docx|exe|ico|mid|midi|mov|mp3|
      mpg|mpeg|pdf|psd|qt|ra|ram|rm|rtf|txt|wav|word|xls)';
}

//Проверка правильность IP адреса
function valid_ip($ip) {
    return preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
 "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $ip);
}


function fixtags($text){
	$text = htmlspecialchars($text);
	$text = preg_replace("/=/", "=\"\"", $text);
	$text = preg_replace("/"/", ""\"", $text);
	$tags = "/&lt;(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(\")\""\"|)(?|(.*)?"(\")|)([\ ]?)(\/|)&gt;/i";
	$replacement = "&lt;$1$2$3$4$5$6$7$8$9$10&gt;";
	$text = preg_replace($tags, $replacement, $text);
	$text = preg_replace("/=\"\"/", "=", $text);
	return $text;
}

//Очищаем входные данные
function clean_chars($string)
{
	// Remove all remaining other unknown characters
	$string = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $string);
	$string = preg_replace('/^[\-]+/', '', $string);
	$string = preg_replace('/[\-]+$/', '', $string);
	$string = preg_replace('/[\-]{2,}/', ' ', $string);

	return $string;
}

//Транслитерация с Латинского  на Русский
function translitrus($string) {
	$converter = array(
	'а' =&gt; 'a', 'б' =&gt; 'b', 'в' =&gt; 'v', 'г' =&gt; 'g', 'д' =&gt; 'd', 'е' =&gt; 'e', 'ё' =&gt; 'e', 'ж' =&gt; 'zh', 'з' =&gt; 'z', 'и' =&gt; 'i', 'й' =&gt; 'y', 'к' =&gt; 'k', 'л' =&gt; 'l', 'м' =&gt; 'm', 'н' =&gt; 'n', 'о' =&gt; 'o', 'п' =&gt; 'p', 'р' =&gt; 'r', 'с' =&gt; 's', 'т' =&gt; 't', 'у' =&gt; 'u', 'ф' =&gt; 'f', 'х' =&gt; 'h', 'ц' =&gt; 'c', 'ч' =&gt; 'ch', 'ш' =&gt; 'sh', 'щ' =&gt; 'sch', 'ь' =&gt; "'", 'ы' =&gt; 'y', 'ъ' =&gt; "'", 'э' =&gt; 'e', 'ю' =&gt; 'yu', 'я' =&gt; 'ya',
	 'А' =&gt; 'A', 'Б' =&gt; 'B', 'В' =&gt; 'V', 'Г' =&gt; 'G', 'Д' =&gt; 'D', 'Е' =&gt; 'E', 'Ё' =&gt; 'E', 'Ж' =&gt; 'Zh', 'З' =&gt; 'Z', 'И' =&gt; 'I', 'Й' =&gt; 'Y', 'К' =&gt; 'K', 'Л' =&gt; 'L', 'М' =&gt; 'M', 'Н' =&gt; 'N', 'О' =&gt; 'O', 'П' =&gt; 'P', 'Р' =&gt; 'R', 'С' =&gt; 'S', 'Т' =&gt; 'T', 'У' =&gt; 'U', 'Ф' =&gt; 'F', 'Х' =&gt; 'H', 'Ц' =&gt; 'C', 'Ч' =&gt; 'Ch', 'Ш' =&gt; 'Sh', 'Щ' =&gt; 'Sch', 'Ь' =&gt; "'", 'Ы' =&gt; 'Y', 'Ъ' =&gt; "'", 'Э' =&gt; 'E', 'Ю' =&gt; 'Yu', 'Я' =&gt; 'Ya'
	);
	return strtr($string, $converter);
}

//Транслитерация с Латинского  на Русский
function translitToRus($string){
  $table = array(
 'А' =&gt; 'A', 'Б' =&gt; 'B', 'В' =&gt; 'V', 'Г' =&gt; 'G', 'Д' =&gt; 'D', 'Е' =&gt; 'E', 'Ё' =&gt; 'YO', 'Ж' =&gt; 'ZH',
 'З' =&gt; 'Z', 'И' =&gt; 'I', 'Й' =&gt; 'J', 'К' =&gt; 'K', 'Л' =&gt; 'L', 'М' =&gt; 'M', 'Н' =&gt; 'N', 'О' =&gt; 'O', 
 'П' =&gt; 'P', 'Р' =&gt; 'R', 'С' =&gt; 'S', 'Т' =&gt; 'T', 'У' =&gt; 'U', 'Ф' =&gt; 'F', 'Х' =&gt; 'H', 'Ц' =&gt; 'C',
 'Ч' =&gt; 'CH', 'Ш' =&gt; 'SH', 'Щ' =&gt; 'CSH', 'Ь' =&gt; '', 'Ы' =&gt; 'Y', 'Ъ' =&gt; '', 'Э' =&gt; 'E', 'Ю' =&gt; 'YU',
 'Я' =&gt; 'YA', 'а' =&gt; 'a', 'б' =&gt; 'b', 'в' =&gt; 'v', 'г' =&gt; 'g', 'д' =&gt; 'd', 'е' =&gt; 'e', 'ё' =&gt; 'yo',
 'ж' =&gt; 'zh', 'з' =&gt; 'z', 'и' =&gt; 'i', 'й' =&gt; 'j', 'к' =&gt; 'k', 'л' =&gt; 'l', 'м' =&gt; 'm', 'н' =&gt; 'n', 
 'о' =&gt; 'o', 'п' =&gt; 'p', 'р' =&gt; 'r', 'с' =&gt; 's', 'т' =&gt; 't', 'у' =&gt; 'u', 'ф' =&gt; 'f', 'х' =&gt; 'h',
 'ц' =&gt; 'c', 'ч' =&gt; 'ch', 'ш' =&gt; 'sh', 'щ' =&gt; 'csh', 'ь' =&gt; '', 'ы' =&gt; 'y', 'ъ' =&gt; '', 'э' =&gt; 'e',
 'ю' =&gt; 'yu', 'я' =&gt; 'ya',
    );

    $output = str_replace(array_keys($table), array_values($table), $string);
    return $output;
}

//Делаем HTML безопасным для вставки в базу
function htmlspecialcharsEx($str)
{
	static $search =  array("&amp;",     "&lt;",     "&gt;",     """,     """,     """,     "'",     "'",     "&lt;",    "&gt;",    "\"");
	static $replace = array("&amp;amp;", "&amp;lt;", "&amp;gt;", "&amp;quot;", "&amp;#34", "&amp;#x22", "&amp;#39", "&amp;#x27", "&lt;", "&gt;", """);
	return str_replace($search, $replace, $str);
}

//Переобразуем HTML Обратно
function htmlspecialcharsback($str)
{
	static $search =  array("&lt;", "&gt;", """, "'", "&amp;");
	static $replace = array("&lt;",    "&gt;",    "\"",     "'",      "&amp;");
	return str_replace($search, $replace, $str);
}

//Удаление файлов на PHP
function DeleteDirFilesEx($path)
{
	if(strlen($path) == 0 || $path == '/')
		return false;

	$full_path = $_SERVER["DOCUMENT_ROOT"]."/".$path;
	$full_path = preg_replace("#[\\\\\\/]+#", "/", $full_path);

	$f = true;
	if(is_file($full_path) || is_link($full_path))
	{
		if(@unlink($full_path))
			return true;
		return false;
	}
	elseif(is_dir($full_path))
	{
		if($handle = opendir($full_path))
		{
			while(($file = readdir($handle)) !== false)
			{
				if($file == "." || $file == "..")
					continue;

				if(!DeleteDirFilesEx($path."/".$file))
					$f = false;
			}
			closedir($handle);
		}
		if(!@rmdir($full_path))
			return false;
		return $f;
	}
	return false;
}

//Удаление файлов на PHP
function DeleteDirFiles($frDir, $toDir, $arExept = array())
{
	if(is_dir($frDir))
	{
		$d = dir($frDir);
		while ($entry = $d-&gt;read())
		{
			if ($entry=="." || $entry=="..")
				continue;
			if (in_array($entry, $arExept))
				continue;
			@unlink($toDir."/".$entry);
		}
		$d-&gt;close();
	}
}

//Получение тип файла на PHP
function GetFileType($path)
{
	$extension = GetFileExtension(strtolower($path));
	switch ($extension)
	{
		case "jpg": case "jpeg": case "gif": case "bmp": case "png":
			$type = "IMAGE";
			break;
		case "swf":
			$type = "FLASH";
			break;
		case "html": case "htm": case "asp": case "aspx":
		case "phtml": case "php": case "php3": case "php4": case "php5": case "php6":
		case "shtml": case "sql": case "txt": case "inc": case "js": case "vbs":
		case "tpl": case "css": case "shtm":
			$type = "SOURCE";
			break;
		default:
			$type = "UNKNOWN";
	}
	return $type;
}

function GetDirectoryIndex($path, $strDirIndex=false)
{
	return GetDirIndex($path, $strDirIndex);
}


function GetDirPath($sPath)
{
	if(strlen($sPath))
	{
		$p = strrpos($sPath, "/");
		if($p === false)
			return '/';
		else
			return substr($sPath, 0, $p+1);
	}
	else
	{
		return '/';
	}
}


function NormalizePhone($number, $minLength = 10)
{
	$minLength = intval($minLength);
	if ($minLength &lt;= 0 || strlen($number) &lt; $minLength)
	{
		return false;
	}

	if (strlen($number) &gt;= 10 &amp;&amp; substr($number, 0, 2) == '+8')
	{
		$number = '00'.substr($number, 1);
	}

	$number = preg_replace("/[^0-9\#\*]/i", "", $number);
	if (strlen($number) &gt;= 10)
	{
		if (substr($number, 0, 2) == '80' || substr($number, 0, 2) == '81' || substr($number, 0, 2) == '82')
		{
		}
		else if (substr($number, 0, 2) == '00')
		{
			$number = substr($number, 2);
		}
		else if (substr($number, 0, 3) == '011')
		{
			$number = substr($number, 3);
		}
		else if (substr($number, 0, 1) == '8')
		{
			$number = '7'.substr($number, 1);
		}
		else if (substr($number, 0, 1) == '0')
		{
			$number = substr($number, 1);
		}
	}

	return $number;
}


function InputType($strType, $strName, $strValue, $strCmp, $strPrintValue=false, $strPrint="", $field1="", $strId="")
{
	$bCheck = false;
	if($strValue &lt;&gt; '')
	{
		if(is_array($strCmp))
			$bCheck = in_array($strValue, $strCmp);
		elseif($strCmp &lt;&gt; '')
			$bCheck = in_array($strValue, explode(",", $strCmp));
	}
	$bLabel = false;
	if ($strType == 'radio')
		$bLabel = true;
	return ($bLabel? '<label>': '').'<input type="'.$strType.'" name="'.$strName.'" id="'.($strId <> ''? $strId : $strName).'" value="'.$strValue.'">'.($strPrintValue? $strValue:$strPrint).($bLabel? '</label>': '');
}


function TruncateText($strText, $intLen)
{
	if(strlen($strText) &gt; $intLen)
		return rtrim(substr($strText, 0, $intLen), ".")."...";
	else
		return $strText;
}


function extract_url($s)
{
	$s2 = '';
	while(strpos(",}])&gt;.", substr($s, -1, 1))!==false)
	{
		$s2 = substr($s, -1, 1);
		$s = substr($s, 0, strlen($s)-1);
	}
	$res = chr(1).$s."/".chr(1).$s2;
	return $res;
}


function DeleteDirFilesEx($path)
{
	if(strlen($path) == 0 || $path == '/')
		return false;

	$full_path = $_SERVER["DOCUMENT_ROOT"]."/".$path;
	$full_path = preg_replace("#[\\\\\\/]+#", "/", $full_path);

	$f = true;
	if(is_file($full_path) || is_link($full_path))
	{
		if(@unlink($full_path))
			return true;
		return false;
	}
	elseif(is_dir($full_path))
	{
		if($handle = opendir($full_path))
		{
			while(($file = readdir($handle)) !== false)
			{
				if($file == "." || $file == "..")
					continue;

				if(!DeleteDirFilesEx($path."/".$file))
					$f = false;
			}
			closedir($handle);
		}
		if(!@rmdir($full_path))
			return false;
		return $f;
	}
	return false;
}

function DeleteDirFiles($frDir, $toDir, $arExept = array())
{
	if(is_dir($frDir))
	{
		$d = dir($frDir);
		while ($entry = $d-&gt;read())
		{
			if ($entry=="." || $entry=="..")
				continue;
			if (in_array($entry, $arExept))
				continue;
			@unlink($toDir."/".$entry);
		}
		$d-&gt;close();
	}
}


function GetFileName($path)
{
	$path = TrimUnsafe($path);
	$path = str_replace("\\", "/", $path);
	$path = rtrim($path, "/");

	$p = bxstrrpos($path, "/");
	if($p !== false)
		return substr($path, $p+1);

	return $path;
}



function GetDirectoryIndex($path, $strDirIndex=false)
{
	return GetDirIndex($path, $strDirIndex);
}


function GetDirPath($sPath)
{
	if(strlen($sPath))
	{
		$p = strrpos($sPath, "/");
		if($p === false)
			return '/';
		else
			return substr($sPath, 0, $p+1);
	}
	else
	{
		return '/';
	}
}


//Парсим URL
function ParseURL($url, $arUrlOld = false)
{
		$arUrl = parse_url($url);

		if (is_array($arUrlOld))
		{
			if (!array_key_exists('scheme', $arUrl))
			{
				$arUrl['scheme'] = $arUrlOld['scheme'];
			}

			if (!array_key_exists('host', $arUrl))
			{
				$arUrl['host'] = $arUrlOld['host'];
			}

			if (!array_key_exists('port', $arUrl))
			{
				$arUrl['port'] = $arUrlOld['port'];
			}
		}

		$arUrl['proto'] = '';
		if (array_key_exists('scheme', $arUrl))
		{
			$arUrl['scheme'] = strtolower($arUrl['scheme']);
		}
		else
		{
			$arUrl['scheme'] = 'http';
		}

		if (!array_key_exists('port', $arUrl))
		{
			if ($arUrl['scheme'] == 'https')
			{
				$arUrl['port'] = 443;
			}
			else
			{
				$arUrl['port'] = 80;
			}
		}

		if ($arUrl['scheme'] == 'https')
		{
			$arUrl['proto'] = 'ssl://';
		}

		$arUrl['path_query'] = array_key_exists('path', $arUrl) ? $arUrl['path'] : '/';
		if (array_key_exists('query', $arUrl) &amp;&amp; strlen($arUrl['query']) &gt; 0)
		{
			$arUrl['path_query'] .= '?' . $arUrl['query'];
		}

		return $arUrl;
}

	
function NormalizePhone($number, $minLength = 10)
{
	$minLength = intval($minLength);
	if ($minLength &lt;= 0 || strlen($number) &lt; $minLength)
	{
		return false;
	}

	if (strlen($number) &gt;= 10 &amp;&amp; substr($number, 0, 2) == '+8')
	{
		$number = '00'.substr($number, 1);
	}

	$number = preg_replace("/[^0-9\#\*]/i", "", $number);
	if (strlen($number) &gt;= 10)
	{
		if (substr($number, 0, 2) == '80' || substr($number, 0, 2) == '81' || substr($number, 0, 2) == '82')
		{
		}
		else if (substr($number, 0, 2) == '00')
		{
			$number = substr($number, 2);
		}
		else if (substr($number, 0, 3) == '011')
		{
			$number = substr($number, 3);
		}
		else if (substr($number, 0, 1) == '8')
		{
			$number = '7'.substr($number, 1);
		}
		else if (substr($number, 0, 1) == '0')
		{
			$number = substr($number, 1);
		}
	}

	return $number;
}	

function InputType($strType, $strName, $strValue, $strCmp, $strPrintValue=false, $strPrint="", $field1="", $strId="")
{
	$bCheck = false;
	if($strValue &lt;&gt; '')
	{
		if(is_array($strCmp))
			$bCheck = in_array($strValue, $strCmp);
		elseif($strCmp &lt;&gt; '')
			$bCheck = in_array($strValue, explode(",", $strCmp));
	}
	$bLabel = false;
	if ($strType == 'radio')
		$bLabel = true;
	return ($bLabel? '<label>': '').'<input type="'.$strType.'" name="'.$strName.'" id="'.($strId <> ''? $strId : $strName).'" value="'.$strValue.'">'.($strPrintValue? $strValue:$strPrint).($bLabel? '</label>': '');
}

function TruncateText($strText, $intLen)
{
	if(strlen($strText) &gt; $intLen)
		return rtrim(substr($strText, 0, $intLen), ".")."...";
	else
		return $strText;
}


function extract_url($s)
{
	$s2 = '';
	while(strpos(",}])&gt;.", substr($s, -1, 1))!==false)
	{
		$s2 = substr($s, -1, 1);
		$s = substr($s, 0, strlen($s)-1);
	}
	$res = chr(1).$s."/".chr(1).$s2;
	return $res;
}



// Переобразуем HTML в текст, сохраняя переносы и пробелы
function HTMLToTxt($str, $strSiteUrl="", $aDelete=array(), $maxlen=70)
{
	//get rid of whitespace
	$str = preg_replace("/[\\t\\n\\r]/", " ", $str);

	//replace tags with placeholders
	static $search = array(
		"'<script[^>]*?>.*?</script>'si",
		"'<style[^>]*?>.*?</style>'si",
		"']*?&gt;.*?'si",
		"'&amp;(quot|#34);'i",
		"'&amp;(iexcl|#161);'i",
		"'&amp;(cent|#162);'i",
		"'&amp;(pound|#163);'i",
		"'&amp;(copy|#169);'i",
	);

	static $replace = array(
		"",
		"",
		"",
		"\"",
		"\xa1",
		"\xa2",
		"\xa3",
		"\xa9",
	);

	$str = preg_replace($search, $replace, $str);

	$str = preg_replace("#&lt;[/]{0,1}(b|i|u|em|small|strong)&gt;#i", "", $str);
	$str = preg_replace("#&lt;[/]{0,1}(font|div|span)[^&gt;]*&gt;#i", "", $str);

	//ищем списки
	$str = preg_replace("#]*&gt;#i", "\r\n", $str);
	$str = preg_replace("#]*&gt;#i", "\r\n  - ", $str);

	//удалим то что задано
	foreach($aDelete as $del_reg)
		$str = preg_replace($del_reg, "", $str);

	//ищем картинки
	$str = preg_replace("/(|\\s*&gt;)/is", "[".chr(1).$strSiteUrl."\\3".chr(1)."] ", $str);
	$str = preg_replace("/(|\\s*&gt;)/is", "[".chr(1)."\\3".chr(1)."] ", $str);

	//ищем ссылки
	$str = preg_replace("/()(.*?)&lt;\\/a&gt;/is", "\\6 [".chr(1).$strSiteUrl."\\3".chr(1)."] ", $str);
	$str = preg_replace("/()(.*?)&lt;\\/a&gt;/is", "\\6 [".chr(1)."\\3".chr(1)."] ", $str);

	//ищем <br>
	$str = preg_replace("#]*&gt;#i", "\r\n", $str);

	//ищем <br>
	$str = preg_replace("#]*&gt;#i", "\r\n\r\n", $str);

	//ищем <hr>
	$str = preg_replace("#]*&gt;#i", "\r\n----------------------\r\n", $str);

	//ищем таблицы
	$str = preg_replace("#&lt;[/]{0,1}(thead|tbody)[^&gt;]*&gt;#i", "", $str);
	$str = preg_replace("#&lt;([/]{0,1})th[^&gt;]*&gt;#i", "&lt;\\1td&gt;", $str);

	$str = preg_replace("##i", "\t", $str);
	$str = preg_replace("##i", "\r\n", $str);
	$str = preg_replace("#]*&gt;#i", "\r\n", $str);

	$str = preg_replace("#\r\n[ ]+#", "\r\n", $str);

	//мочим вообще все оставшиеся тэги
	$str = preg_replace("#&lt;[/]{0,1}[^&gt;]+&gt;#i", "", $str);

	$str = preg_replace("#[ ]+ #", " ", $str);
	$str = str_replace("\t", "    ", $str);

	//переносим длинные строки
	if($maxlen &gt; 0)
		$str = preg_replace("#([^\\n\\r]{".intval($maxlen)."}[^ \\r\\n]*[\\] ])([^\\r])#", "\\1\r\n\\2", $str);

	$str = str_replace(chr(1), " ",$str);
	return trim($str);
}


/**
 * Функция фильтрует строку и устанавливает формат вывода телефонного номера
 * @param string $phone Строка с телефоном
 * @return string
 */
 
function setFormatPhone($phone)
{
  $phone = preg_replace("/[^0-9]/", "", $phone);

  if(strlen($phone) == 7)
    $phone = preg_replace("/([0-9]{3})([0-9]{2})([0-9]{2})/", "$1-$2-$3", $phone);
  elseif(strlen($phone) == 10)
    $phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1)$2-$3", $phone);
  elseif(strlen($phone) == 11)
  {
    $phone = preg_replace("/([0-9])([0-9]{3})([0-9]{3})([0-9]{4})/", "$1($2)$3-$4", $phone);
    $first = substr($phone, 0, 1);
    if(in_array($first, array(7, 8)))
      $phone = '+7'. substr($phone, 1);
  }

  return $phone;
}
getNameDay($date = false)

/**
 * Получаем название дня недели
 * @param string|int $date
 * @return string
 */
 
function getNameDay($date = false)
{
  if(!$date)
    $date = mktime();

  $date = is_int($date) ? $date : strtotime($date);
  $names = array(
      'Monday'    =&gt; 'Понедельник',
      'Tuesday'   =&gt; 'Вторник',
      'Wednesday' =&gt; 'Среда',
      'Thursday'  =&gt; 'Четверг',
      'Friday'    =&gt; 'Пятница',
      'Saturday'  =&gt; 'Суббота',
      'Sunday'    =&gt; 'Воскресенье'
  );

  return $names[ date("l", $date) ];
 

getDaysFromWeek($week = false, $format = 'd.m.Y');

//Функция возвращает интервал дат по номеру недели:

/**
 * По номеру недели функция возвращает интервал дат от понедельника до воскресенья.
 * @param int $week Порядковый номер недели в году
 * @param type $format Формат выводимой даты, по умолчанию 'd.m.Y'
 * @return array
 */
 
function getDaysFromWeek($week = false, $format = 'd.m.Y')
{
  if(!$week)
  $week = date("W");

  $result['today'] = date($format);
  $result['begin'] = date($format, strtotime(date("W") - date("W") ." week -". (date("w") == 0 ? 6 : date("w") - 1) ." day"));
  $result['end']   = date($format, strtotime($result['begin']) + 60 * 60 * 24 * 6);

  $result[] = $result['begin'];
  for($i = 1; $i &lt; 7; $i++)
    $result[] = date($format, strtotime($result['begin']) + 60 * 60 * 24 * $i);

  return $result;
}


//Функция для генерации паролей и любых строк заданной длины. Второй параметр может быть массивом классов символов. Если он указан, тогда в результирующую строчку войдет минимум один символ из каждого класса. Функция выдернута из CMS-Bitrix
randString($pass_len=10, $pass_chars=false)

function randString($pass_len=10, $pass_chars=false)
{
  static $allchars = "abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789";
  $string = "";
  if(is_array($pass_chars))
  {
    while(strlen($string) &lt; $pass_len)
    {
      if(function_exists('shuffle'))
        shuffle($pass_chars);
      foreach($pass_chars as $chars)
      {
        $n = strlen($chars) - 1;
        $string .= $chars[mt_rand(0, $n)];
      }
    }
    if(strlen($string) &gt; count($pass_chars))
      $string = substr($string, 0, $pass_len);
  }
  else
  {
    if($pass_chars !== false)
    {
      $chars = $pass_chars;
      $n = strlen($pass_chars) - 1;
    }
    else
    {
      $chars = $allchars;
      $n = 61; //strlen($allchars)-1;
    }
    for ($i = 0; $i &lt; $pass_len; $i++)
      $string .= $chars[mt_rand(0, $n)];
  }
  return $string;
}

echo randString(7, array(
  "abcdefghijklnmopqrstuvwxyz",
  "ABCDEFGHIJKLNMOPQRSTUVWX­YZ",
  "0123456789",
  "!@#\$%^&amp;*()",
));
 



/**
* Function of processing of variables for a conclusion in a stream
* Функция обработки переменных для вывода в поток
*/
htmlChars($data);
  function htmlChars($data)
  {
    if(is_array($data))
      $data = array_map("htmlChars", $data);
    else
      $data = htmlspecialchars($data);

    return $data;
  }
/**
* Function of processing of variables for a conclusion in a stream
* Функция обработки переменных для вывода в поток
*/
  function htmlChars_decode($data)
  {
    if(is_array($data))
      $data = array_map("htmlChars_decode", $data);
    else
      $data = htmlspecialchars_decode($data);

    return $data;
  }
 

drawTable($data, $tabs = 0, $columns = 10)

/**
*  Table division
*  Деление таблицы
*/
  function drawTable($data, $tabs = 0, $columns = 10)
  {
    $tbl = null;

    if($tabs === false)
    {
      $tr = $td = null;
    }
    else
    {
      $tr = "\n".str_repeat("\t", $tabs);
      $td = $tr."\t";
    }

    for($i = 0, $n = 1, $d = ceil(count($data) / $columns) * $columns; $i &lt; $d; $i++, $n++)
    {
      if($n == 1)
      $tbl .= $tr."\n";

      $tbl .= $td."\n".(isset($data[$i]) ? $data[$i] : ' ')."\n";

      if($n == $columns)
      {
        $n = 0;
        $tbl .= $tr.'';
      }
    }

    if($tabs !== false)
    $tbl .= "\n";

    return $tbl;
    /*
    $gallery  = "";
    $gallery .= drawTable($rows, IRB_IMAGES_ROWS, IRB_IMAGES_COLUMNS);
    $gallery .= "
"; */ }
 



//Чтобы положить в БД строку, её лучше обработать этой штукой:

/**
* Function of processing of literal constants for SQL
* Функция обработки литеральных констант для SQL
*/

function escapeString($data)
  {

    if(is_array($data))
      $data = array_map("escapeString", $data);
    else
      $data = mysql_real_escape_string($data);

    return $data;
 }


function uploadHandle($file_name, $max_file_size = 100, $extensions = array(), $upload_dir = '.', $out_name = false)
  {

    $error = null;
    $info  = null;
    $max_file_size *= 1024;

    if ($_FILES[$file_name]['error'] === UPLOAD_ERR_OK)
    {
      // проверяем расширение файла
      $file_extension = pathinfo($_FILES[$file_name]['name'], PATHINFO_EXTENSION);
      if (in_array($file_extension, $extensions))
      {
        // проверяем размер файла
        if ($_FILES[$file_name]['size'] &lt; $max_file_size)
        {
	 // новое имя файла
	 if($out_name)
	   $out_name = str_replace('.'.$file_extension, '', $out_name) .'.'. $file_extension;
	 else
	   $out_name = mt_rand(mt_rand(10, 1000), 100000) .'_'. $_FILES[$file_name]['name'];

	 $destination = $upload_dir .'/' . $out_name;

	 if(move_uploaded_file($_FILES[$file_name]['tmp_name'], $destination))
	   $info = LANG_FILE_MESS_OK;
	 else   
	$error = LANG_FILE_MESS_ERR_LOAD;
        }
        else
          $error = LANG_FILE_MESS_MAX_SIZE;
      }
      else
        $error = LANG_FILE_MESS_ERR_EXT;
    }
    else
    {
      // массив ошибок
      $error_values = array(
      UPLOAD_ERR_INI_SIZE   =&gt; LANG_FILE_ERR_INI_SIZE,
      UPLOAD_ERR_FORM_SIZE  =&gt; LANG_FILE_ERR_FORM_SIZE,
      UPLOAD_ERR_PARTIAL    =&gt; LANG_FILE_ERR_PARTIAL,
      UPLOAD_ERR_NO_FILE    =&gt; LANG_FILE_ERR_NO_FILE,
      UPLOAD_ERR_NO_TMP_DIR =&gt; LANG_FILE_ERR_NO_TMP_DIR,
      UPLOAD_ERR_CANT_WRITE =&gt; LANG_FILE_ERR_CANT_WRITE
     );

      $error_code = $_FILES[$file_name]['error'];

      if (!empty($error_values[$error_code]))
        $error = $error_values[$error_code];
      else
        $error = LANG_FILE_MESS_BUG;
    }

    return array('info' =&gt; $info, 'error' =&gt; $error, 'name' =&gt; $out_name);
}
 

translateIt($text, $direct = 'ru_en');

/**
* Transliteration function
* Функция транслитерации текста
* @param string $text
* @param string $direct
* @return string
*/
  function translateIt($text, $direct = 'ru_en')
  {
    $L['ru'] = array(
'Ё', 'Ж', 'Ц', 'Ч', 'Щ', 'Ш', 'Ы',
'Э', 'Ю', 'Я', 'ё', 'ж', 'ц', 'ч',
'ш', 'щ', 'ы', 'э', 'ю', 'я', 'А',
'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И',
'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',
'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ъ',
'Ь', 'а', 'б', 'в', 'г', 'д', 'е',
'з', 'и', 'й', 'к', 'л', 'м', 'н',
'о', 'п', 'р', 'с', 'т', 'у', 'ф',
'х', 'ъ', 'ь'
         );

    $L['en'] = array(
"YO", "ZH",  "CZ", "CH", "SHH","SH", "Y'",
"E'", "YU",  "YA", "yo", "zh", "cz", "ch",
"sh", "shh", "y'", "e'", "yu", "ya", "A",
"B" , "V" ,  "G",  "D",  "E",  "Z",  "I",
"J",  "K",   "L",  "M",  "N",  "O",  "P",
"R",  "S",   "T",  "U",  "F",  "X",  "''",
"'",  "a",   "b",  "v",  "g",  "d",  "e",
"z",  "i",   "j",  "k",  "l",  "m",  "n",
"o",  "p",   "r",  "s",  "t",  "u",  "f",
"x",  "''",  "'"
         );

    // Конвертируем хилый и немощный в великий могучий...
    if($direct == 'en_ru')
    {
      $translated = str_replace($L['en'], $L['ru'], $text);
      // Теперь осталось проверить регистр мягкого и твердого знаков.
      $translated = preg_replace('/(?&lt;=[а-яё])Ь/u', 'ь', $translated);
      $translated = preg_replace('/(?&lt;=[а-яё])Ъ/u', 'ъ', $translated);
    }
    else // И наоборот
      $translated = str_replace($L['ru'], $L['en'], $text);

    // Заменяем пробел на нижнее подчеркивание
    $translated = str_replace(' ', '_', $translated);

    // Возвращаем получателю.
    return $translated;
  }



/*
 * xmlToArray() will convert the given XML text to an array in the XML structure.
 * Link: http://www.bin-co.com/php/scripts/xmlToArray/
 * Arguments : $contents - The XML text
 *     $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 *     $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
 * Examples: $array =  xmlToArray(file_get_contents('feed.xml'));
 *   $array =  xmlToArray(file_get_contents('feed.xml', 1, 'attribute'));
 * xmlToArray($contents, $get_attributes, $priority) 
 */
 
function xmlToArray($contents, $get_attributes = 1, $priority = 'tag')
{
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &amp;$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();

        if(isset($value)) {
 if($priority == 'tag') $result = $value;
 else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) {
 foreach($attributes as $attr =&gt; $val) {
     if($priority == 'tag') $attributes_data[$attr] = $val;
     else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
 }
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag ''
 $parent[$level-1] = &amp;$current;
 if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
     $current[$tag] = $result;
     if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
     $repeated_tag_index[$tag.'_'.$level] = 1;

     $current = &amp;$current[$tag];

 } else { //There was another element with the same tag name

     if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
         $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
         $repeated_tag_index[$tag.'_'.$level]++;
     } else {//This section will make the value an array if multiple tags with the same name appear together
         $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
         $repeated_tag_index[$tag.'_'.$level] = 2;

         if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
  $current[$tag]['0_attr'] = $current[$tag.'_attr'];
  unset($current[$tag.'_attr']);
         }

     }
     $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
     $current = &amp;$current[$tag][$last_item_index];
 }

        } elseif($type == "complete") { //Tags that ends in 1 line ''
 //See if the key is already taken.
 if(!isset($current[$tag])) { //New Key
     $current[$tag] = $result;
     $repeated_tag_index[$tag.'_'.$level] = 1;
     if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

 } else { //If taken, put all things inside a list(array)
     if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

         // ...push the new element into that array.
         $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

         if($priority == 'tag' and $get_attributes and $attributes_data) {
  $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
         }
         $repeated_tag_index[$tag.'_'.$level]++;

     } else { //If it is not an array...
         $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
         $repeated_tag_index[$tag.'_'.$level] = 1;
         if($priority == 'tag' and $get_attributes) {
  if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

      $current[$tag]['0_attr'] = $current[$tag.'_attr'];
      unset($current[$tag.'_attr']);
  }

  if($attributes_data) {
      $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
  }
         }
         $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
     }
 }

        } elseif($type == 'close') { //End of tag ''
 $current = &amp;$parent[$level-1];
        }
    }

    return($xml_array);
}
 

wrapperText($text, $length, $bgn, $end, $encoding)

/**
 * Оборачиваем текст в указанные теги по определённой длине.
 * @param string $text Текст, строки которого нужно обернуть в теги.
 * @param integer $length По умолчанию 100
 * @param string $bgn По умолчанию '
' * @param string $end По умолчанию '

' * @param string $encoding По умолчанию 'UTF-8' * @return string */ function wrapperText($text = '', $length = 100, $bgn = '

', $end = '

', $encoding = 'UTF-8') { if(empty($text) || empty($length)) return ''; $line = ''; $rows = array(); $array = explode(' ', $text); for($i = 0; $i &lt; count($array); $i++) { if(mb_strlen($line .' '. $array[$i], $encoding) &lt; $length) $line .= ' '. $array[$i]; else { $rows[] = trim($line); $line = $array[$i]; } if($i == (count($array) - 1) &amp;&amp; !empty($line)) $rows[] = trim($line); } return $bgn . implode($end . $bgn, $rows) . $end; }
 

cropText($string, $maxlen, $link, $encoding)

/**
 * Функция обрезает текст по окончании слова.
 * Если задан параметр $link, то в конце текста вставляется ссылка с адресом $link.
 * @param string $string Строка которую нужно обрезать.
 * @param integer $maxlen Количество символов до обрезания.
 * @param string $link URL address.
 * @param string $encoding Кодировка текста, по умолчанию "UTF-8".
 * @return string
 */
  function cropText($string = '', $maxlen = 100, $link = false, $encoding = "UTF-8")
  {
    $len = (mb_strlen($string, $encoding) &gt; $maxlen)
          ? mb_strripos(mb_substr($string, 0, $maxlen, $encoding), ' ', 0, $encoding)
          : $maxlen;

    $cutStr = rtrim(mb_substr($string, 0, $len, $encoding), "., |()/");

    $result = (mb_strlen($string, $encoding) &gt; $maxlen) ? $cutStr .'...' : $cutStr;

    if(!$link)
      return trim($result);
    else
    {
      // Цифра 1130 несёт в себе временный костыль, на её месте должно быть
      // число 30. Проблема связана с кодировкой сервера.
      if(strlen($result) &lt; 20)
        return ''. $result .'';

      //$pos = mb_strripos($result, ' ', round(mb_strlen($result, $encoding) / 3), $encoding);
      $pos = mb_strrpos($result, ' ', -round(mb_strlen($result, $encoding) / 3), $encoding);
      $string_bgn = mb_substr($result, 0, $pos, $encoding);
      $string_end = mb_substr($result, $pos, 1000, $encoding);

      return trim($string_bgn .' '. $string_end .'');
    }
  }
clearText($text)

/**
   * Текст на выходе должен содержать HTML-документ.
   * Необходимо удалить все HTML-теги, секции javascript, пробельные символы.
   * Также необходимо заменить некоторые HTML-сущности на их эквивалент.
   * @param string $text Входящий текст
   * @return string
   */
  function clearText($text)
  {
    $search = array ("']*?&gt;.*?'si",  // Вырезает javaScript
          "'&lt;[\/\!]*?[^&lt;&gt;]*?&gt;'si",// Вырезает HTML-теги
          "'([\r\n])[\s]+'",      // Вырезает пробельные символы
          "'&amp;(quot|#34);'i",      // Заменяет HTML-сущности
          "'&amp;(amp|#38);'i",
          "'&amp;(lt|#60);'i",
          "'&amp;(gt|#62);'i",
          "'&amp;(nbsp|#160);'i",
          "'&amp;(iexcl|#161);'i",
          "'&amp;(cent|#162);'i",
          "'&amp;(pound|#163);'i",
          "'&amp;(copy|#169);'i",
          "'&amp;#(\d+);'e");         // интерпретировать как php-код

    $replace = array ("",
	"",
	"\\1",
	"\"",
	"&amp;",
	"&lt;",
	"&gt;",
	" ",
	chr(161),
	chr(162),
	chr(163),
	chr(169),
	"chr(\\1)");

    return preg_replace($search, $replace, $text);
  }
  


function clean_phone_number($phone) {
       if (!empty($phone)) {
    //var_dump($phone);
    preg_match_all('/[0-9\(\)+.\- ]/s', $phone, $cleaned);
    foreach($cleaned[0] as $k=&gt;$v) {
 $ready .= $v;
    }
    var_dump($ready);
    die;
    if (mb_strlen($cleaned) &gt; 4 &amp;&amp; mb_strlen($cleaned) &lt;=25) {
 return $cleaned;
    }
    else {
 return false;
    }
       }
       return false;
}





//$extension = substr($file_name, strrpos($file_name, "."));
//$extension = end(explode(".", $file_name));


function formspecialchars($var)
{
        $pattern = '/&amp;(#)?[a-zA-Z0-9]{0,};/';
        if (is_array($var)) {    // If variable is an array
		 $out = array();      // Set output as an array
		 foreach ($var as $key =&gt; $v) {
			 $out[$key] = formspecialchars($v);         // Run formspecialchars on every element of the array and return the result. Also maintains the keys.
		 }
				} else {
		 $out = $var;
		 while (preg_match($pattern,$out) &gt; 0) {
			 $out = htmlspecialchars_decode($out,ENT_QUOTES);
		 }
		 $out = htmlspecialchars(stripslashes(trim($out)), ENT_QUOTES,'UTF-8',true);     // Trim the variable, strip all slashes, and encode it
        }

        return $out;
}




function paginator($count, $limit, $page, $url = null, $name = 'page', $block = 33) {
	if (!$url) {
		$url = $_SERVER['REQUEST_URI'];
		if (mb_strstr($url, '?'.$name.'=')) $url = preg_replace('/\?'.$name.'=[0-9]*/', '', $url);
	}
	if (!$limit) $limit = 10;
	if ($count &lt;= $limit) return;
	$page_list = null;  
	if (strstr($url, '?')) $qw = '&amp;';
    else $qw = '?';   
    if ($page &gt; $block) {
		$i = floor($page/ $block); 
		$i = $i * $block;
		if ($i == $page) $i--;
		$a_url = $url.$qw.$name.'='.$i;
		$page_list .= '<br>&lt;&lt;<br>';
    } else $i = 0;
    for ($j = 0; $i &lt; ceil($count / $limit), $j &lt; $block; $i++, $j++) {
		if ($i * $limit &gt;= $count) break;
		if ($i == $page-1) {
			$a_url = $url.$qw.$name.'='.$page;
			if ($page != $i + 1) $page_list .= '<br>'.($i + 1).'<br>';
			else $page_list .= '<br>'.($i + 1).'<br>';
		} else {
			$a_url = $url.$qw.$name.'='.($i + 1);
			if ($page != $i + 1) $page_list .= '<br>'.($i + 1).'<br>';
			else $page_list .= '<br>'.($i + 1).'<br>';
		}  
    }
    if ($i &lt; ceil($count / $limit)) {
		$a_url = $url.$qw.$name.'='.($i + 1);
		$page_list .= '<br>&gt;&gt;<br>';
    }  
	return '<br>'.$page_list.'<br>';
}


function pagination($numRows, $rowsPerPage=1){
	$pageParamNm = "page";
	$pages = ceil( $numRows/$rowsPerPage );
	if ( $pages &lt; 2 ) return "";
	$res = "Страница ";
	if ( array_key_exists("page", $_GET)){
		$currentPage = is_numeric($_GET[$pageParamNm]) ? $_GET[$pageParamNm] : 1;
		unset( $_GET[$pageParamNm] );
	} else {
		$currentPage = 1;
	}
	$params = '';
	foreach ( $_GET as $k=&gt;$v){
		$params .= $k . '=' . urlencode($v) . '&amp;';
	}
	$path = explode( "?", $_SERVER['REQUEST_URI'] );
	for ( $i = 1; $i &lt;= $pages; $i++ ){
		$res .= sprintf( 
			"%s",
			$i == $currentPage ? $i : "{$i}"
		);
	}
	return $res;
}


function paginator_extra($count, $limit, $page, $block = 12) {
	if ($count &lt;= $limit) return;
	if ($page &gt; $block) {
		$i = floor($page / $block) * $block;
		if ($i == $page) $i -= $block;
		$pages_list .= '<br>&lt;<br>';
	} else $i = 0;
	for ($j = 0; $i &lt; ceil($count / $limit), $j &lt; $block; $i++, $j++) {
		if ($i * $limit &gt;= $count) break;
		$pages_list .= ''.($i + 1).'';
	}
    if ($i &lt; ceil($count / $limit)) $pages_list .= '<br>&gt;<br>';
	return $pages_list;
}

// Проверка на правильность введёного пароля текущим пользователем
function isUserPassword($password){
    global $USER;
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $userData = $rsUser->Fetch();
    $salt = substr($userData['PASSWORD'], 0, (strlen($userData['PASSWORD']) - 32));
    $realPassword = substr($userData['PASSWORD'], -32);
    $password = md5($salt.$password);
    return ($password == $realPassword);
 }

//  Пагинация

 <?// номер текущей страницы
 $curPage = $arResult["NAV_RESULT"]->NavPageNomer;
 // всего страниц - номер последней страницы
 $totalPages = $arResult["NAV_RESULT"]->NavPageCount;
 // номер постраничной навигации на странице
 $navNum = $arResult["NAV_RESULT"]->NavNum;
 ?>

//  Пагинация
 
<?
// номер текущей страницы
$curPage = $arResult["NAV_RESULT"]->NavPageNomer;
// всего страниц - номер последней страницы
$totalPages = $arResult["NAV_RESULT"]->NavPageCount;
// номер постраничной навигации на странице
$navNum = $arResult["NAV_RESULT"]->NavNum;
//всего новостей
$totalNews = $arResult["NAV_RESULT"]->NavRecordCount;
/*еще осталось новостей*/
$rest = $totalNews - $arResult["NAV_RESULT"]->NavPageSize * $curPage;
//выводить на страницу
$onPage = ($rest < $arResult["NAV_RESULT"]->NavPageSize) ? $rest : $arResult["NAV_RESULT"]->NavPageSize;
?>

  

// Начало  News.list вывод информации о разделе (здесь название и свойство раздела)


//  это result_modifier для news.list

 <?
$arFilter = Array("IBLOCK_ID"=> $arParams["IBLOCK_ID"] );
$db_list = CIBlockSection::GetList(Array("NAME"=>"ASC"), $arFilter, false, Array('UF_*'));
while ($arr = $db_list->GetNext()) {
   $arResult["SECTIONS"][$arr["ID"]]["NAME"] = $arr["NAME"];
   $arResult["SECTIONS"][$arr["ID"]]["UF_SECTION_BGCOLOR"] = $arr['~UF_SECTION_BGCOLOR'];
}
?>

//  это в шаблоне

<?= $arResult["SECTIONS"][$arItem["IBLOCK_SECTION_ID"]]["NAME"]; ?>
<?=$arResult["SECTIONS"][$arItem["IBLOCK_SECTION_ID"]]["DESCRIPTION"];?>


// Конец  News.list вывод информации о разделе



<?
global $USER;
if ($USER->IsAdmin()):?>

    // feedback

<? endif; ?>




<?
// Порядок полей в форме регистрации
// Function for fields order - result_modifier.php  -  main.register - template
function sortArray($arSource, $arOrder, $arUserFields = Array()) {
    $arFirst      = Array();
    $arUserProps   = Array();
    if(count($arUserFields)) {
        foreach($arUserFields as $keyFiels=>$arField) {
            $arUserProps[] = $keyFiels;
        }
    }
    $arUsedFields = array_merge($arSource, $arUserProps);

    foreach($arOrder as $sField) {
        if(in_array($sField, $arUsedFields)) {
            $arFirst[] = $sField;

            foreach($arUsedFields as $keySource=>$sSource) {
                if($sSource == $sField) {
                    unset($arUsedFields[$keySource]);
                }
            }
        }
    }

    $arResult = array_merge($arFirst, $arUsedFields);

    return $arResult;
}

// Set array of order fields
$arOrder = Array(
    "LOGIN",
    "EMAIL",
    "PERSONAL_BIRTHDAY",
    "PERSONAL_COUNTRY",
    "PASSWORD",
    "CONFIRM_PASSWORD",
    "LAST_NAME",
    "PERSONAL_PHONE",
    "PERSONAL_GENDER",
    "PERSONAL_STATE",
);

// Order of array
$arResult["SHOW_FIELDS"] = sortArray($arResult["SHOW_FIELDS"], $arOrder, $arResult["USER_PROPERTIES"]["DATA"]);
?>





<?=$arItem['DISPLAY_PROPERTIES']['ATT_DUTIES']['DISPLAY_VALUE']?>
<?=$arItem['PROPERTIES']['ATT_DUTIES']['VALUE']?>








_____________________________


Если нужно вывести название раздела инфоблока в котором находится конкретный элемент/новость в компоненте "списк новостей" Битрикс, и ссылку на этот раздел на сайте. Можно воспользоваться массивом $arItem.

Прямо в temlate.php шаблона списка новостей вставляем код

< ?
$res = CIBlockSection::GetByID($arItem["IBLOCK_SECTION_ID"]);
if($ar_res = $res->GetNext())
? >
В разделе:
< a href="/content/< ? echo $ar_res['CODE']; ?>/">
< ? echo $ar_res['NAME']; ?>
< /a >
Мы получили ID раздела инфоблока, в котором лежит новость. Зная ID получили и его название и символьный код для подстановки в ссылку, если не используте символьный код в ЧПУ, соответсвенно вставляете $ar_res['ID']



<?/* Если мы находимся на главной */?>
<? if ($APPLICATION->GetCurPage(false) === '/'): ?>
<? endif; ?>

<?/* Если мы НЕ находимся на главной */?>
<? if ($APPLICATION->GetCurPage(false) !== '/'): ?>
<? endif; ?>

/* Проверка работы функции mail */
<?if (mail("sorokinsam@yandex.ru","test subject", "test body","From: from@mail"))
echo "Сообщение передано функции mail, проверьте почту в ящике.";
else
echo "Функция mail не работает, свяжитесь с администрацией хостинга.";
?>

// Условия для вывода шаблона (для поддомена и с параметрами)

strpos($_SERVER["HTTP_HOST"], 'spb.wlgdev.ru')!==false

CSite::InDir('/l/') && $_REQUEST['var']=='l3.2'


// Свободное место
echo intval(@disk_free_space($_SERVER["DOCUMENT_ROOT"])/1024/1024)." Mb"
	
