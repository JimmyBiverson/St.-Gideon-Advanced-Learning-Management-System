<?php

use Database\Data\PageData;
use Illuminate\Support\Arr;

it('uses St. Gideon content for seeded public pages', function () {
    $content = Arr::flatten(PageData::getAllPages());
    $strings = collect($content)->filter(fn ($value) => is_string($value));

    expect($strings->implode(' '))->toContain('St. Gideon Learning Management System')
        ->and($strings->implode(' '))->not->toContain('Mentor LMS')
        ->and($strings->implode(' '))->not->toContain('info@example.com')
        ->and($strings->implode(' '))->not->toContain('123 Education Street')
        ->and($strings->implode(' '))->toContain('Namugongo, Kampala, Uganda');
});
