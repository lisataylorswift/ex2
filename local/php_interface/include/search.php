<?php

function onBeforeSearchIndex(&$arFields) {
//     file_put_contents(
//     $_SERVER['DOCUMENT_ROOT'] . '/ex2_630.log',
//     print_r($arFields, true) . PHP_EOL,
//     FILE_APPEND
// );
    if ($arFields['MODULE_ID'] !== 'iblock' ||
    (int)$arFields['PARAM2'] !== REVIEWS_IBLOCK_ID ||
    empty($arFields['ITEM_ID'])
    ) {
        return;
    }

    $authorId = CIBlockElement::GetProperty(
        REVIEWS_IBLOCK_ID,
        (int)$arFields['ITEM_ID'],
        [],
        ['CODE' => REVIEWS_AUTHOR_PROPERTY]
    )->Fetch()['VALUE'];

    if(!$authorId) {
        return $arFields;
    }

    $user = CUser::GetById((int)$authorId)->Fetch();

    if(!$user || empty($user[AUTHOR_CLASS_FIELD])) {
        return $arFields;
    }

    $enum = new CUserFieldEnum();

    $userField = CUserTypeEntity::GetList(
        [],
        [
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => AUTHOR_CLASS_FIELD,
        ]
    )->Fetch();

    if(!$userField) {
        return $arFields;
    }

    $result = $enum->GetList(
        [],
        [
            'USER_FIELD_ID' => $userField['ID'],
            'ID' => (int)$user[AUTHOR_CLASS_FIELD],
        ]
    );

    $class = $result->Fetch();

    if($class) {
        $arFields['TITLE'] .= ' ' . $class['VALUE'];
    }

    return $arFields;
}



//это если выводить в заголовку логин автора

// function onBeforeSearchIndex(&$arFields)
// {
//     if (
//         $arFields['MODULE_ID'] !== 'iblock' ||
//         (int)$arFields['PARAM2'] !== REVIEWS_IBLOCK_ID ||
//         empty($arFields['ITEM_ID'])
//     ) {
//         return $arFields;
//     }

//     $authorId = CIBlockElement::GetProperty(
//         REVIEWS_IBLOCK_ID,
//         (int)$arFields['ITEM_ID'],
//         [],
//         ['CODE' => REVIEWS_AUTHOR_PROPERTY]
//     )->Fetch()['VALUE'];

//     if (!$authorId) {
//         return $arFields;
//     }

//     $user = CUser::GetByID((int)$authorId)->Fetch();

//     if (!$user || empty($user['LOGIN'])) {
//         return $arFields;
//     }

//     $arFields['TITLE'] .= ' ' . $user['LOGIN'];

//     return $arFields;
// }