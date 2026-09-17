<?php

namespace Sprint\Migration;


class Callbackform20260917214712 extends Version
{
    protected $author = "admin";

    protected $description = "Форма обратного звонка";

    protected $moduleVersion = "5.15.1";

    public function up()
    {
        $helper = $this->getHelperManager();

        $formId = $helper->Form()->saveForm([
            'NAME' => 'Обратный звонок',
            'SID' => 'CALLBACK_FORM',
            'C_SORT' => 100,
            'BUTTON' => 'Отправить',
            'DESCRIPTION' => 'Форма обратного звонка',
            'STAT_EVENT1' => 'form',
            'STAT_EVENT2' => 'callback',
        ]);

        $fields = [
            [
                'FIELD_TYPE' => 'text',
                'SID' => 'NAME',
                'TITLE' => 'Ваше имя',
                'REQUIRED' => 'Y',
                'C_SORT' => 100,
            ],
            [
                'FIELD_TYPE' => 'text',
                'SID' => 'PHONE',
                'TITLE' => 'Телефон',
                'REQUIRED' => 'Y',
                'C_SORT' => 200,
                'FIELD_PARAM' => 'placeholder="+7 (___) ___-__-__"',
            ],
            [
                'FIELD_TYPE' => 'textarea',
                'SID' => 'COMMENT',
                'TITLE' => 'Комментарий',
                'REQUIRED' => 'N',
                'C_SORT' => 300,
            ],
            [
                'FIELD_TYPE' => 'checkbox',
                'SID' => 'AGREE',
                'TITLE' => 'Согласен на обработку персональных данных',
                'REQUIRED' => 'Y',
                'C_SORT' => 400,
            ],
        ];

        foreach ($fields as $field) {
            $helper->Form()->saveField($formId, $field);
        }

        $helper->Form()->saveStatus($formId, [
            'TITLE' => 'По умолчанию',
            'SID' => 'DEFAULT',
            'C_SORT' => 100,
            'DEFAULT' => 'Y',
        ]);

        $this->outSuccess('Форма обратного звонка создана');
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        
        $formId = $helper->Form()->getFormIdBySid('CALLBACK_FORM');
        if ($formId) {
            $helper->Form()->deleteForm($formId);
            $this->outSuccess('Форма удалена');
        }
    }
}
