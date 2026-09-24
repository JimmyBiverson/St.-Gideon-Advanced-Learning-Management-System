<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * The "booted" method of the model.
     * Apply global scope to order by created_at DESC
     */
    protected static function booted(): void
    {
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        return $this->normalizeAttributeValue($value);
    }

    public function toArray(): array
    {
        return $this->normalizeAttributeValue(parent::toArray());
    }

    protected function normalizeAttributeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            if ($this->isStaleMediaUrl($value)) {
                return Setting::normalizeMediaUrl($value, $this->currentBaseUrl());
            }

            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $index => $item) {
                $value[$index] = $this->normalizeAttributeValue($item);
            }

            return $value;
        }

        return $value;
    }

    protected function currentBaseUrl(): ?string
    {
        $request = app()->bound('request') ? app('request') : null;

        return $request ? $request->getSchemeAndHttpHost() : config('app.url');
    }

    protected function isStaleMediaUrl(string $value): bool
    {
        if (blank($value) || ! preg_match('#^https?://#i', $value)) {
            return false;
        }

        $parts = parse_url($value);

        if (! is_array($parts) || empty($parts['host']) || empty($parts['path'])) {
            return false;
        }

        $baseUrl = $this->currentBaseUrl();
        $currentHost = parse_url((string) rtrim((string) $baseUrl, '/'), PHP_URL_HOST);

        if (! $currentHost) {
            return false;
        }

        return $parts['host'] !== $currentHost && (str_contains($parts['path'], '/storage/') || str_contains($parts['path'], '/uploads/') || preg_match('/\.(png|jpe?g|gif|webp|svg|ico)(\?.*)?$/i', $parts['path']));
    }

    /**
     * Scope to search categories by title
     */
    public function scopeSearch(Builder $query, string $column, string $value): Builder
    {
        return $query->where($column, 'LIKE', '%'.$value.'%');
    }

    /**
     * Scope to search categories by title
     */
    public function scopeSearchWhen(Builder $query, string $column, array $values, string $key): Builder
    {
        return $query->when(array_key_exists($key, $values) && ! empty($values[$key]), function ($query) use ($values, $key, $column) {
            return $query->where($column, 'LIKE', '%'.$values[$key].'%');
        });
    }
}
