<?php

use Bitrix\Main\Mail\Event;

class AuthorHandler
{
    private static array $oldClasses = [];

    public static function onBeforeUserUpdate(&$arFields)
    {
        if (empty($arFields['ID'])) {
            return;
        }

        $userId = (int)$arFields['ID'];

        $user = CUser::GetById($userId)->Fetch();

        if (!$user) {
            return;
        }

        self::$oldClasses[$userId] = (int)$user[AUTHOR_CLASS_FIELD];
    }

    public static function onAfterUserUpdate(&$arFields)
    {
        if (empty($arFields['ID'])) {
            return;
        }

        $userId = (int)$arFields['ID'];

        if (!array_key_exists($userId, self::$oldClasses)) {
            return;
        }

        $oldClassId = self::$oldClasses[$userId];
        $newClassId = (int)$arFields[AUTHOR_CLASS_FIELD];

        if ($oldClassId === $newClassId) {
            unset(self::$oldClasses[$userId]);

            return;
        }

        $oldClassName = self::getClassName($oldClassId);
        $newClassName = self::getClassName($newClassId);

        Event::send([
            'EVENT_NAME' => AUTHOR_INFO_EVENT,
            'LID' => SITE_ID,
            'C_FIELDS' => [
                'USER_ID' => $userId,
                'OLD_USER_CLASS' => $oldClassName,
                'NEW_USER_CLASS' => $newClassName,
            ],
        ]);

        unset(self::$oldClasses[$userId]);
    }

    private static function getClassName(int $classId): string
    {
        if ($classId <= 0) {
            return '';
        }

        $enum = new CUserFieldEnum();

        $enum->SetFilter([
            'USER_FIELD_NAME' => AUTHOR_CLASS_FIELD,
            'ID' => $classId,
        ]);

        $value = $enum->GetList()->Fetch();

        return $value ? (string)$value['VALUE'] : '';
    }
}