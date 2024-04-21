<?php

namespace App\Models;

use Heloufir\FilamentWorkflowManager\Core\HasWorkflow;
use Heloufir\FilamentWorkflowManager\Core\InteractsWithWorkflows;
use Heloufir\FilamentWorkflowManager\Models\WorkflowHistory;
use Heloufir\FilamentWorkflowManager\Models\WorkflowModelStatus;
use Heloufir\FilamentWorkflowManager\Models\WorkflowStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model implements HasWorkflow
{
    use HasFactory;
    use InteractsWithWorkflows;

    public const INITIATED = 'Initiated';
    public const APPROVED  = 'Approved';
    public const REJECTED  = 'Rejected';
    public const PERMISSION = 'ProductSalesPermission';

    protected $fillable =
        [
            'doctor_master_id',
            'purchase_price',
            'product_id',
            'stockist_id',
            'sales_unit',
            'free_unit',
            'sales_total',
            'free_total',
            'month',
            'status',
        ];

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

        // Updating workflow after creation
        static::created(function ($model){
            $work_flow_status = $model->status ? self::APPROVED : self::INITIATED;

            $work_flow = WorkflowModelStatus::create(
                [
                    'modelable_type' => "App\Models\ProductSale",
                    'modelable_id'   => $model->id,
                    'workflow_status_id' => WorkflowStatus::where('name', '=' , $work_flow_status)->first()->id,
                ]);

            WorkflowHistory::create(
                [

                    'old_status_id' => null,
                    'new_status_id' => $work_flow->workflow_status_id,
                    'user_id'       => auth()->user()->id,
                    'modelable_type' => "App\Models\ProductSale",
                    'modelable_id'   => $model->id,
                    'executed_at' => now()

                ]);


        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (! $model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->email;
            }
        });

        static::updated(function ($model)
        {
            if ($model->status){

                $work_flow_status =  self::APPROVED ;
                $work_flow = WorkflowModelStatus::where('modelable_type' ,'=',  "App\Models\ProductSale")->where('modelable_id'   ,'=', $model->id);
                $work_flow->update([
                    'workflow_status_id' => WorkflowStatus::where('name', '=' , $work_flow_status)->first()->id,
                ]);

            }

            else{

                $work_flow_status =  self::REJECTED ;
                $work_flow = WorkflowModelStatus::where('modelable_type' ,'=',  "App\Models\ProductSale")->where('modelable_id'   ,'=', $model->id);
                $work_flow->update([
                    'workflow_status_id' => WorkflowStatus::where('name', '=' , $work_flow_status)->first()->id,
                ]);

            }

        });

    }




    public function doctor_master(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DoctorMaster::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockist(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Stockist::class);
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

    public static function state_product($stockist_id)
    {
        $products = [];

        if ($stockist_id == null )
        {
            return [];
        }
        $state_id = Stockist::where("id", "=", $stockist_id)->first()->marketing_representative->area_manager->state_id;
        if ($state_id == null || $state_id <1)
        {
             return [];
        }
        $product_ids = ProductMaster::where("state_id", "=", $state_id)->pluck("product_id");
        $products_array = Product::whereIn("id", $product_ids )->get();
        foreach ($products_array as $product) {
            $data =$product->name;
            $products[$product->id] = strtoupper($data);

        }
        asort($products);

        return $products;
    }
}
