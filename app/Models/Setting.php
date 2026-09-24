<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'sub_type',
        'title',
        'fields',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fields' => 'array',
    ];

    /**
     * Scope a query to only include settings of a specific type.
     *
     * @param  Builder  $query
     * @param  string  $type
     * @return Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include settings of a specific sub-type.
     *
     * @param  Builder  $query
     * @param  string  $subType
     * @return Builder
     */
    public function scopeOfSubType($query, $subType)
    {
        return $query->where('sub_type', $subType);
    }

    /**
     * Get a specific field from the fields JSON.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getField($key, $default = null)
    {
        return data_get($this->fields, $key, $default);
    }

    /**
     * Set a specific field in the fields JSON.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setField($key, $value)
    {
        $fields = $this->fields;
        data_set($fields, $key, $value);
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get settings by type and sub_type.
     *
     * @param  string  $type
     * @param  string|null  $subType
     * @return Collection
     */
    public static function getByType($type, $subType = null)
    {
        $query = self::ofType($type);

        if ($subType) {
            $query->ofSubType($subType);
        }

        return $query->get();
    }

    public static function normalizeMediaUrl(?string $value, ?string $baseUrl = null): ?string
    {
        if (blank($value)) {
            return $value;
        }

        $request = app()->bound('request') ? app('request') : null;
        $requestRoot = $request ? rtrim((string) $request->getSchemeAndHttpHost(), '/') : null;
        $baseUrl = rtrim((string) ($baseUrl ?? config('app.url') ?? $requestRoot), '/');

        if ($requestRoot && $requestRoot !== $baseUrl) {
            $baseUrl = $requestRoot;
        }

        if ($baseUrl === '' || ! preg_match('#^https?://#i', $value)) {
            return $value;
        }

        $parts = parse_url($value);

        if (! is_array($parts) || empty($parts['path'])) {
            return $value;
        }

        $normalized = $baseUrl.$parts['path'];

        if (! empty($parts['query'])) {
            $normalized .= '?'.$parts['query'];
        }

        if (! empty($parts['fragment'])) {
            $normalized .= '#'.$parts['fragment'];
        }

        return $normalized;
    }

    public static function normalizeMediaFields(array $fields, ?string $baseUrl = null): array
    {
        foreach ($fields as $key => $value) {
            $fields[$key] = self::normalizeMediaFieldValue($value, $baseUrl);
        }

        return $fields;
    }

    protected static function normalizeMediaFieldValue(mixed $value, ?string $baseUrl = null): mixed
    {
        if (is_string($value)) {
            return self::normalizeMediaUrl($value, $baseUrl);
        }

        if (is_array($value)) {
            foreach ($value as $index => $item) {
                $value[$index] = self::normalizeMediaFieldValue($item, $baseUrl);
            }
        }

        return $value;
    }
}
