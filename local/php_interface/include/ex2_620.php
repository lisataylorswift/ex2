<?php

function onBeforeUserInfoEvent(
    &$event,
    &$lid,
    &$arFields,
    &$messageId,
    &$files,
    &$languageId
){

    if ($event !== 'USER_INFO' || empty($arFields['USER_ID'])) {
        return;
    }

    $user = CUser::GetById((int)$arFields['USER_ID']) -> Fetch();

    if(!$user) {
        return;
    }

    $classId = (int)$user['UF_USER_CLASS'];
    $class ='';
    if ($classId > 0) {
        $enum = new CUserFieldEnum();

        $result = $enum->GetList(
            [],
            [
                'USER_FIELD_NAME' => 'UF_USER_CLASS',
                'ID' => $classId,
            ]
        );
        
        if ($value = $result->Fetch()) {
            $class = $value['VALUE'];
        }

    }
    
    $arFields['CLASS'] = $class;

    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . '/ex2_620.log',
        print_r($arFields, true) . PHP_EOL,
        FILE_APPEND
    );
}
