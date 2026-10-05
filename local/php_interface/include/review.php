<?php

class ReviewHandler
{
	private static $oldAuthors = [];

	public static function onBeforeAdd(&$arFields)
	{
		if((int)$arFields['IBLOCK_ID'] !== REVIEWS_IBLOCK_ID)
		{
			return true;
		}

		$previewText = (string)$arFields['PREVIEW_TEXT'];
		$length = mb_strlen($previewText);

		if($length < 5)
		{
			global $APPLICATION;

			$APPLICATION->ThrowException(
				GetMessage(
					'REVIEW_PREVIEW_TOO_SHORT',
					[
						'#LENGTH#' => $length,
					]
				)
			);

			return false;
		}

		$arFields['PREVIEW_TEXT'] = str_replace('#del#', '', $previewText);

		return true;
	}

	public static function onBeforeUpdate(&$arFields)
	{
		if((int)$arFields['IBLOCK_ID'] !== REVIEWS_IBLOCK_ID)
		{
			return true;
		}

		if(isset($arFields['PREVIEW_TEXT']))
		{
			$previewText = (string)$arFields['PREVIEW_TEXT'];
			$length = mb_strlen($previewText);

			if($length < 5)
			{
				global $APPLICATION;

				$APPLICATION->ThrowException(
					GetMessage(
						'REVIEW_PREVIEW_TOO_SHORT',
						[
							'#LENGTH#' => $length,
						]
					)
				);

				return false;
			}

			$arFields['PREVIEW_TEXT'] = str_replace('#del#', '', $previewText);
		}

		$oldReview = CIBlockElement::GetList(
			[],
			[
				'ID' => (int)$arFields['ID'],
				'IBLOCK_ID' => REVIEWS_IBLOCK_ID,
			],
			false,
			false,
			[
				'ID',
				'PROPERTY_' . REVIEWS_AUTHOR_PROPERTY,
			]
		)->Fetch();

		if($oldReview)
		{
			self::$oldAuthors[(int)$arFields['ID']] = (int)$oldReview['PROPERTY_' . REVIEWS_AUTHOR_PROPERTY . '_VALUE'];
		}

		return true;
	}

	public static function onAfterUpdate(&$arFields)
	{
		if((int)$arFields['IBLOCK_ID'] !== REVIEWS_IBLOCK_ID)
		{
			return;
		}

		$elementId = (int)$arFields['ID'];

		if(!array_key_exists($elementId, self::$oldAuthors))
		{
			return;
		}

		$oldAuthorId = self::$oldAuthors[$elementId];

		$property = CIBlockElement::GetProperty(
			REVIEWS_IBLOCK_ID,
			$elementId,
			[],
			[
				'CODE' => REVIEWS_AUTHOR_PROPERTY,
			]
		)->Fetch();

		$newAuthorId = (int)$property['VALUE'];

		if($oldAuthorId !== $newAuthorId)
		{
			CEventLog::Add([
				'SEVERITY' => 'INFO',
				'AUDIT_TYPE_ID' => AUDIT_TYPE_REVIEWS,
				'MODULE_ID' => 'iblock',
				'ITEM_ID' => (string)$elementId,
				'DESCRIPTION' => GetMessage(
					'REVIEWS_AUTHOR_CHANGED',
					[
						'#ID#' => $elementId,
						'#OLD_AUTHOR#' => $oldAuthorId,
						'#NEW_AUTHOR#' => $newAuthorId,
					]
				),
			]);
		}

		unset(self::$oldAuthors[$elementId]);
	}
}