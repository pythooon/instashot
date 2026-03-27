<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Validation;

use App\Shared\Validation\ViolationListPresenter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class ViolationListPresenterTest extends TestCase
{
    public function testEmptyListYieldsEmptyString(): void
    {
        $this->assertSame('', ViolationListPresenter::toPlainText(new ConstraintViolationList()));
    }

    public function testFormatsViolationsWithPropertyPath(): void
    {
        $list = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'too short',
                messageTemplate: 'too short',
                parameters: [],
                root: null,
                propertyPath: 'token',
                invalidValue: '',
            ),
            new ConstraintViolation(
                message: 'bad format',
                messageTemplate: 'bad format',
                parameters: [],
                root: null,
                propertyPath: 'username',
                invalidValue: '',
            ),
        ]);

        $text = ViolationListPresenter::toPlainText($list);

        $this->assertStringContainsString('token: too short', $text);
        $this->assertStringContainsString('username: bad format', $text);
    }

    public function testRootLevelViolationWithoutPath(): void
    {
        $list = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'global error',
                messageTemplate: 'global error',
                parameters: [],
                root: null,
                propertyPath: '',
                invalidValue: null,
            ),
        ]);

        $this->assertSame('global error', ViolationListPresenter::toPlainText($list));
    }
}
