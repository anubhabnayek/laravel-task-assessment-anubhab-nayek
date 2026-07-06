<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'due_date',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? false, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['from'] ?? false, function ($query, $from) {
                $query->whereDate('due_date', '>=', $from);
            })
            ->when($filters['to'] ?? false, function ($query, $to) {
                $query->whereDate('due_date', '<=', $to);
            });
    }
}
