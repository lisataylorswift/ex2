<?php

function onBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
{
    global $USER;

    if (!in_array(5, $USER->GetUserGroupArray())) {
        return;
    }

    foreach (array_keys($aGlobalMenu) as $menuId) {
        if ($menuId !== 'global_menu_content') {
            unset($aGlobalMenu[$menuId]);
        }
    }

    $contentItems = [];

    foreach ($aModuleMenu as $item) {
        if (($item['parent_menu'] ?? '') === 'global_menu_content') {
            $contentItems[] = $item;
        }
    }

    $aModuleMenu = $contentItems;

    $aGlobalMenu['global_menu_quick'] = [
        'menu_id' => 'quick',
        'text' => 'Быстрый доступ',
        'title' => 'Быстрый доступ',
        'sort' => 200,
    ];

    $aModuleMenu[] = [
        'parent_menu' => 'global_menu_quick',
        'text' => 'Ссылка 1',
        'title' => 'Ссылка 1',
        'url' => 'https://test1',
        'sort' => 100,
    ];

    $aModuleMenu[] = [
        'parent_menu' => 'global_menu_quick',
        'text' => 'Ссылка 2',
        'title' => 'Ссылка 2',
        'url' => 'https://test2',
        'sort' => 200,
    ];
}