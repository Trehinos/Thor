<?php

namespace Tests;

use Thor\Http\Web\Form\Form;
use Thor\Http\Web\Form\Field\{SelectField};
use PHPUnit\Framework\TestCase;
use Thor\Http\Web\Form\Field\TextField;
use Thor\Http\Web\Form\Field\PasswordField;

final class FormTest extends TestCase
{

    public const FORM_ACTION = 'https://example.com/action.php';

    public function testCreateForm(): Form
    {
        $form = new class(self::FORM_ACTION) extends Form {
            public static function formDefinition(): array
            {
                return [
                    'test' => new TextField('test', read_only: true, required: true),
                ];
            }
        };

        $this->assertSame(
            '<form action="' . self::FORM_ACTION . '" method="POST"><input name="test" value="" required readonly class="form-control" type="text"></form>',
            $form->getHtml()
        );

        $form->setFields(['test2' => new PasswordField('test2')]);
        return $form;
    }

    /**
     * @depends testCreateForm
     */
    public function testFormData(Form $form): void
    {
        $form->setFieldsData(['test' => 'text', 'test2' => 'password']);
        $this->assertSame('text', $form->getFieldValue('test'));
        $this->assertSame('password', $form->getFieldValue('test2'));
    }

    public function testSelectField(): void
    {
        $select = new SelectField(
                   'test',
                   false,
                   [
                       'option1' => 'value1',
                       'option2' => 'value2',
                   ],
            value: 'value1'
        );
        $this->assertSame(
            '<select name="test" class="form-control"><option value="value1" selected>option1</option><option value="value2">option2</option></select>',
            $select->getHtml()
        );
    }

}
