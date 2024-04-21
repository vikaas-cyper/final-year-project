<?php

namespace App\Filament\Resources\ProductSaleResource\Pages;

use App\Filament\Resources\ProductSaleResource;
use App\Models\Product;
use App\Models\ProductMaster;
use App\Models\ProductSale;
use App\Models\Stockist;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Heloufir\FilamentWorkflowManager\Core\WorkflowResource;
use Heloufir\FilamentWorkflowManager\Forms\Components\WorkflowStatusInput;
use Heloufir\FilamentWorkflowManager\Models\WorkflowModelStatus;
use Heloufir\FilamentWorkflowManager\Models\WorkflowPermission;
use Heloufir\FilamentWorkflowManager\Models\WorkflowStatus;
use Heloufir\FilamentWorkflowManager\Models\WorkflowUserPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EditProductSale extends EditRecord
{
    use WorkflowResource;

    protected static string $resource = ProductSaleResource::class;

    public bool $field_disable = false;

    public array $beforeUpdate = [
        'doctor_master_id' => null,
        'product_id' => null,
        'stockist_id' => null,
        'sales_unit' => null,
        'free_unit' => null,
        'month' => null,
        'workflow_status_id' => null,
    ];

    // Before update check whether Admin or User
    protected function mutateFormDataBeforeFill(array $data): array
    {

        foreach ($this->beforeUpdate  as $attributes => $key) {
            $this->beforeUpdate[$attributes] = $data[$attributes];
        }

        if ($data['workflow_status'] == null) {
            return $data;
        }

        $workflow_status = $data['workflow_status']['status']['name'];

        $created_by = ProductSale::find($data['id'])->created_by;

        if (auth()->user()->email != $created_by || (strtolower($workflow_status) == strtolower('approved'))) {
            $this->field_disable = true;
        }

        // Return true if the workflow status is not equal rejected or user without sufficient rights
        elseif (auth()->user()->email != $created_by || (! (strtolower($workflow_status) == strtolower('rejected')))) {
            return $data;
        }

        // When user have sufficient rights and workflow status is approved update below
        elseif (auth()->user()->email == $created_by && (strtolower($workflow_status) == strtolower('rejected'))) {
            $current_status = WorkflowModelStatus::find($data['workflow_status']['id']);
            $current_status->workflow_status_id = WorkflowStatus::where('name', '=', 'Initiated')->first()->id; // if 1 = Initiated
            $current_status->save();

            $data['workflow_status_id'] = 1;
            $data['workflow_status'] = $current_status;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($this->beforeUpdate != $data) {

            $stockist                           = Stockist::find($data['stockist_id']);
            $state_id                           = $stockist->marketing_representative->area_manager->state->id;
            $ptr                                = Product::find($data['product_id'])->product_masters->where('state_id', '=', $state_id)->where('status', '=', true)->first()->ptr;
            $data['sales_total']                = $ptr * $data['sales_unit'];
            $data['free_total']                 = $ptr * $data['free_unit'];

            if ($this->beforeUpdate['workflow_status_id'] == WorkflowStatus::where('name', '=', 'Approved')->first()->id) {
                $data['status'] = true;
            }
        }

        if ($this->data['workflow_status_id'] == WorkflowStatus::where('name', '=', 'Approved')->first()->id) {
            $data['status'] = true;
        }

        $record->update($data);

        return $record;
    }

    protected function getFormSchema(): array
    {
        return [

            Select::make('doctor_master_id')
                    ->label('Distributed To')
                    ->placeholder('Billing | Doctor')
                    ->disabled($this->field_disable)
                    ->options(ProductSale::doctor_master_search())
                    ->required()
                    ->columnSpan(2)
                    ->inlineLabel()
                    ->searchable(),

            Select::make('stockist_id')
                    ->label('Distributed By')
                    ->relationship('stockist', 'name', fn (Builder $query) => $query->where('status', '=', 1))
                    ->inlineLabel()
                    ->disabled($this->field_disable)
                    ->required()
                    ->columnSpan(2)
                    ->searchable(),

            DatePicker::make('month')
                        ->label('Distributed Month')
                        ->required()
                        ->columnSpan(2)
                        ->disabled($this->field_disable)
                        ->default(now())
                        ->maxDate(now())->withoutTime(true)
                        ->inlineLabel(),

            Select::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->required()->columnSpan(2)
                    ->disabled($this->field_disable)
                    ->inlineLabel()
                    ->searchable(),

            TextInput::make('sales_unit')
                       ->numeric()
                       ->label('Sales Unit')
                       ->default(1)
                       ->inlineLabel()
                       ->required()
                       ->columnSpan(2)
                       ->disabled($this->field_disable),

            TextInput::make('free_unit')
                       ->numeric()
                       ->label('Free Unit')
                       ->default(1)
                       ->required()
                       ->columnSpan(2)
                       ->inlineLabel()
                       ->disabled($this->field_disable),

            WorkflowStatusInput::make()
                                ->label('Verification')
                                ->columnSpan(2)->searchable(false)
                                ->inlineLabel(),
        ];
    }

    public function approve(){

        $this->getRecord()->update(['status' => true]);

        $this->redirect(static::$resource::getUrl('index'));
    }

    public function reject(){

        $this->getRecord()->update(['status' => null]);

        $this->getRecord()->update(['status' => false]);

//        dd($this->getRecord());
        $this->redirect(static::$resource::getUrl('index'));
    }

    protected function formAction()
    {
        $formAction = [];

        $permission = WorkflowUserPermission::where('user_id', '=', auth()->user()->id)->first();


        if ($permission && (!$this->getRecord()->status) )
        {
            $formAction = [
                            Actions\Action::make('approve')
                                ->label('Approve')->action('approve')
                                ->color('success'),

                            Actions\Action::make('reject')
                                ->label('Reject')->action('reject')
                                ->color('danger')
                    ];
        }

        else {
            $formAction = [parent::getSaveFormAction()];

        }

        return $formAction;
    }
    protected function getFormActions(): array
    {
        return array_merge(

            $this->formAction(),

            [
                Actions\Action::make('back')
                ->label('Back')
                ->url(static::$resource::getUrl('index'))->color('secondary')
            ]
        );
    }

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),

        ];
    }
}
