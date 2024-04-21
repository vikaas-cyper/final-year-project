<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingRepresentative extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            if (! $model->isDirty('created_by')) {
                $model->created_by = auth()->user()->email;
            }
            if (! $model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->email;
            }
        });
        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (! $model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->email;
            }
        });
    }

    protected $fillable = [
        'name',
        'email',
        'area_manager_id',
        'status',
    ];

    public function head_quarter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HeadQuarter::class, 'head_quarter_id', 'id');
    }

    public function target(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MarketingRepresentativeTarget::class);
    }

    public function stockist(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Stockist::class);
    }

    public function area_manager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AreaManager::class);
    }

    public function doctor_masters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DoctorMaster::class);
    }
}
