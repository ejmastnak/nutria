<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BodyWeightRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'unit_id',
        'kg',
        'lb',
        'date_time_utc',
        'description',
        'user_id',
    ];

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value + 0,
        );
    }

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with('unit:id,name')
            ->orderBy('date_time_utc', 'desc')
            ->get([
                'id',
                'amount',
                'unit_id',
                'date_time_utc',
                'description'
            ]);
    }
    
    public static function getForUserPaginated(?int $userId, Request $request) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with('unit:id,name')
            ->filter($request->only(['body_weight_records_from_date_time_utc', 'body_weight_records_to_date_time_utc']))
            ->orderBy('date_time_utc', 'desc')
            ->paginate(config('pagination.body_weight_records'))
            ->withQueryString()
            ->through(fn ($record) => [
                'id' => $record->id,
                'amount' => $record->amount,
                'unit_id' => $record->unit_id,
                'unit' => $record->unit,
                'date_time_utc' => $record->date_time_utc,
                'description' => $record->description,
            ]);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['body_weight_records_from_date_time_utc'] ?? null, function ($query, $fromDateTimeUtc) {
            $query->where('date_time_utc', '>=', $fromDateTimeUtc);
        })->when($filters['body_weight_records_to_date_time_utc'] ?? null, function ($query, $toDateTimeUtc) {
            $query->where('date_time_utc', '<=', $toDateTimeUtc);
        });
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
