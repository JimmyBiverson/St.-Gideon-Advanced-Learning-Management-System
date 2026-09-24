<?php

use App\Models\BaseModel;
use App\Models\Setting;
use Illuminate\Http\Request;

it('normalizes stale media urls to the configured app url', function () {
    $baseUrl = 'https://gilms.ac.ug';

    expect(Setting::normalizeMediaUrl('http://127.0.0.1:8000/storage/5/sample-badge.png', $baseUrl))
        ->toBe('https://gilms.ac.ug/storage/5/sample-badge.png')
        ->and(Setting::normalizeMediaUrl('https://lms-sample.duckdns.org/storage/2/logo.png', $baseUrl))
        ->toBe('https://gilms.ac.ug/storage/2/logo.png')
        ->and(Setting::normalizeMediaUrl('/assets/icons/logo-dark.png', $baseUrl))
        ->toBe('/assets/icons/logo-dark.png')
        ->and(Setting::normalizeMediaUrl('https://gilms.ac.ug/storage/3/logo-light.png', $baseUrl))
        ->toBe('https://gilms.ac.ug/storage/3/logo-light.png');
});

it('prefers the active request host when the stored media url belongs to a stale domain', function () {
    app()->instance('request', Request::create('https://gilms.ac.ug/admin/settings'));

    expect(Setting::normalizeMediaUrl('https://lms-sample.duckdns.org/storage/42/favicon.png', 'https://lms-sample.duckdns.org'))
        ->toBe('https://gilms.ac.ug/storage/42/favicon.png');
});

it('rewrites stale media urls on model attributes to the active site host', function () {
    app()->instance('request', Request::create('https://gilms.ac.ug'));

    $model = new class extends BaseModel
    {
        protected $table = 'courses';
    };

    $model->setRawAttributes(['thumbnail' => 'https://lms-sample.duckdns.org/storage/35/3.png']);

    expect($model->thumbnail)->toBe('https://gilms.ac.ug/storage/35/3.png');
});

it('rewrites stale media urls nested inside array attributes', function () {
    app()->instance('request', Request::create('https://gilms.ac.ug'));

    $model = new class extends BaseModel
    {
        protected $table = 'courses';
    };

    $model->setRawAttributes([
        'images' => [
            'logo' => 'https://lms-sample.duckdns.org/storage/77/logo-light.png',
            'gallery' => [
                'https://lms-sample.duckdns.org/storage/77/cover.jpg',
            ],
        ],
    ]);

    expect($model->images)->toBe([
        'logo' => 'https://gilms.ac.ug/storage/77/logo-light.png',
        'gallery' => ['https://gilms.ac.ug/storage/77/cover.jpg'],
    ]);
});
