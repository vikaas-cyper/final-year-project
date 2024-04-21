<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorTarget extends Model
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
        'doctor_master_id',
        'target',
        'start_month',
        'status',

    ];

    public function doctor_master(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DoctorMaster::class);
    }

    // This function is created to search DoctorMaster relationships fields.
    public static function doctor_master_search(): array
    {
        $doctor_masters = DoctorMaster::where('status', '=', 1)->get();
        $doctor_options = [];

        foreach ($doctor_masters as $doctor_master) {
            $data =$doctor_master->billing->billing_name.' | '.$doctor_master->billing->doctor_name;
            $doctor_options[$doctor_master->id] = strtoupper($data);

        }

        asort($doctor_options);

        return $doctor_options;
    }
}
