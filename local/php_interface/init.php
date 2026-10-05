<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/const.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/review.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/author.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/agent_ex_610.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/ex2_620.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/search.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/glob_menu.php';


//review
AddEventHandler(
	'iblock',
	'OnBeforeIBlockElementAdd',
	['ReviewHandler', 'onBeforeAdd']
);

AddEventHandler(
	'iblock',
	'OnBeforeIBlockElementUpdate',
	['ReviewHandler', 'onBeforeUpdate']
);

AddEventHandler(
	'iblock',
	'OnAfterIBlockElementUpdate',
	['ReviewHandler', 'onAfterUpdate']
);

//author
AddEventHandler(
    'main',
    'OnBeforeUserUpdate',
    ['AuthorHandler', 'onBeforeUserUpdate']
);

AddEventHandler(
    'main',
    'OnAfterUserUpdate',
    ['AuthorHandler', 'onAfterUserUpdate']
);

//620 CLASS в почт шаблон
AddEventHandler(
    'main',
    'OnBeforeEventAdd',
    'OnBeforeUserInfoEvent'
);


//индекс поиск
AddEventHandler(
    'search',
    'BeforeIndex',
    'onBeforeSearchIndex'
);


//глоб меню
AddEventHandler(
    'main',
    'OnBuildGlobalMenu',
    'onBuildGlobalMenu'
);
// file_put_contents(
//     $_SERVER['DOCUMENT_ROOT'] . '/ex2_author.log',
//     date('Y-m-d H:i:s') . ' HANDLERS REGISTERED' . PHP_EOL,
//     FILE_APPEND
// );