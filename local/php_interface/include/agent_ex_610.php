<?php
function Agent_ex_610() {
    $lastRun = COption::GetOptionString(
        'main',
        'ex2_610_last_run',
        ''
    );

    $now = ConvertTimeStamp(time(), 'FULL');

    $count = 0;

    if($lastRun !== '') {
        $rsReviews = CIBlockElenent::GetList(
            [],
            [
                'IBLOCK_CODE' => 'reviews',
                'TIMESTAMP_X_1' => $lastRun,
                'TIMESTAMP_X_2' => $now,
            ],
            false,
            false,
            ['ID']
        );

        $count = $rsReviews->SelectRowCount();
    }

    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'ex2_610',
        'MODULE_ID' => 'iblock',
        'ITEM_ID' => '',
        'DESCRIPTION' => 'Запуск агента ex2_610. С' . 
        ($lastRun ?: ' первого запуска') . ' изменилось ' . $count . ' рецензий'
    ]);

    COption::SetOptionString(
        'main',
        'ex2_610_last_run',
        $now
    );

    return 'Agent_ex_610();';
}