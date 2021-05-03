<?php
class CEcomkassaAtolOnline{

    public function registerMainClass(){
        return new \Bitrix\Main\EventResult(Bitrix\Main\EventResult::SUCCESS,
            array(
                '\ThePoint\Ecomkassa\AtolOnline' => '/bitrix/modules/ecomkassa.atol/lib/AtolOnline.php'
            )
        );
    }
}

