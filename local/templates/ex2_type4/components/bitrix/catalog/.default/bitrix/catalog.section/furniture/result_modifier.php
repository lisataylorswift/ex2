<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$productIds = [];

foreach ($arResult['ITEMS'] as $key => $arItem)
{
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] = number_format((float)$arItem['PRICES']['PRICE']['PRINT_VALUE'], 0, '.', ' ');
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] .= ' '.$arItem['PROPERTIES']['PRICECURRENCY']['VALUE_ENUM'];

	$arResult['ITEMS'][$key] = $arItem;
	$productIds[] = (int)$arItem['ID'];
}

$arResult['REVIEWS'] = [];

if($productIds)
{
	$authorIds = [];

	$rsUsers = CUser::GetList(
		($by = 'ID'),
		($order = 'ASC'),
		[
			'GROUP_ID' => 6,
			'UF_AUTHOR_STATUS' => 35,
		],
		[
			'FIELDS' => ['ID'],
		]
	);

	while ($user = $rsUsers->Fetch())
	{
		$authorIds[] = (int)$user['ID'];
	}

	if($authorIds)
	{
		$rsReviews = CIBlockElement::GetList(
			['SORT' => 'ASC'],
			[
				'IBLOCK_CODE' => 'reviews',
				'ACTIVE' => 'Y',
				'PROPERTY_AUTHOR' => $authorIds,
				'PROPERTY_PRODUCT' => $productIds,
			],
			false,
			false,
			[
				'ID',
				'NAME',
				'PROPERTY_PRODUCT',
			]
		);

		while ($review = $rsReviews->Fetch())
		{
			$productId = (int)$review['PROPERTY_PRODUCT_VALUE'];

			if($productId)
			{
				$arResult['REVIEWS'][$productId][] = $review['NAME'];
			}
		}
	}
}

$reviewsProductCount = count($arResult['REVIEWS']);

$ex2Meta = $APPLICATION->GetProperty('ex2_meta');

if(strpos($ex2Meta, '#count#') !== false)
{
	$APPLICATION->SetPageProperty(
		'ex2_meta',
		str_replace('#count#', $reviewsProductCount, $ex2Meta)
	);
}

$firstReview = '';

foreach ($arResult['REVIEWS'] as $reviews)
{
	if(!empty($reviews))
	{
		$firstReview = $reviews[0];
		break;
	}
}

if($firstReview)
{
	$APPLICATION->AddViewContent(
		'first_review',
		'<div id="filial-special" class="information-block">
			<div class="top"></div>
			<div class="information-block-inner">
				<h3>'.GetMessage('DOP').'</h3>
				<div class="special-product">
					<div class="special-product-title">
						'.htmlspecialcharsbx($firstReview).'
					</div>
				</div>
			</div>
			<div class="bottom"></div>
		</div>'
	);
}