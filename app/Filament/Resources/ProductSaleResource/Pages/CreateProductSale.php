<?php

namespace App\Filament\Resources\ProductSaleResource\Pages;

use App\Filament\Resources\ProductSaleResource;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Stockist;
use App\Models\ProductBuyRate;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Heloufir\FilamentWorkflowManager\Core\WorkflowResource;
use Heloufir\FilamentWorkflowManager\Forms\Components\WorkflowStatusInput;
use Illuminate\Database\Eloquent\Builder;
use DB;

class CreateProductSale extends CreateRecord
{
    use WorkflowResource;


    protected static string $resource = ProductSaleResource::class;

    protected function handleCreation(array $data): array
    {

        $create_data= [];
        $create_data['doctor_master_id']    = $data['doctor_master_id'];
        $create_data['month']               = $data['month'];
        $create_data['stockist_id']         = $data['stockist_id'];
        $products                           = [];
        $stockist                           = Stockist::find($data['stockist_id']);
        $state_id                           = $stockist->marketing_representative->area_manager->state->id;

        foreach ($data['products'] as $product)
        {
            $ptr                                    = Product::find($product['product_id'])->product_masters->where('state_id', '=', $state_id)->where('status', '=', true)->first()->ptr;
            $create_data['product_id']              = $product['product_id'];
            $create_data['sales_unit']              = $product['sales_unit'];
            $create_data['free_unit']               = $product['free_unit'];
            $create_data['sales_total']             = $ptr * $product['sales_unit'];
            $create_data['free_total']              = $ptr * $product['free_unit'];
            $purchase_price                         = DB::table('product_buy_rates')
                                                            ->select('price_per_unit')
                                                            ->where('status', true)
                                                            ->where('product_id', Product::find($product['product_id'])->id)
                                                            ->pluck('price_per_unit')
                                                            ->first();

            $create_data['purchase_price']          = $purchase_price * $product['sales_unit'];
            $create_data['status']                  = null;
            $products                               = array_merge([static::getModel()::create($create_data)], $product);

        }

        return $products;
    }

    protected function getFormSchema(): array
    {
        return [

            Select::make('doctor_master_id')
                ->label('Distributed To')
                ->placeholder('Billing | Doctor')
                ->options(ProductSale::doctor_master_search())
                ->required()
                ->columnSpan(2)
                ->inlineLabel()
                ->searchable(),

            Select::make('stockist_id')
                ->label('Distributed By')
                ->relationship('stockist', 'name', fn (Builder $query) => $query->where('status', '=', 1))
                ->inlineLabel()
                ->placeholder('Stockist')
                ->required()
                ->columnSpan(2)
                ->searchable()
                ->reactive()
            ,

            DatePicker::make('month')
                ->label('Distributed Month')
                ->required()
                ->columnSpan(2)
                ->default(now())
                ->maxDate(now())->withoutTime(true)
                ->inlineLabel(),

            Section::make("Products")->schema([
                Repeater::make('products')->schema([
                    Select::make('product_id')
                    ->label('Product')
                    ->options(function (callable $get) {
                        return ProductSale::state_product($get("../../stockist_id"));
                    })
                    ->required()
                    ->inlineLabel()
                    ->searchable(),

                    TextInput::make('sales_unit')
                        ->numeric()
                        ->label('Sales Unit')
                        ->inlineLabel()
                        ->required(),

                    TextInput::make('free_unit')
                        ->numeric()
                        ->label('Free Unit')
                        ->required()
                        ->inlineLabel(),


                ]),
            ]),


            WorkflowStatusInput::make()
                ->label('Workflow Status')
                ->columnSpan(2)->searchable(false)
                ->inlineLabel(),
        ];
    }

    public function create(bool $another = false): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $this->callHook('beforeCreate');

        $records = $this->handleCreation($data);


        foreach ($records as $record){
            $this->form->model($record)->saveRelationships();

        }

        if (filled($this->getCreatedNotificationMessage())) {
            $this->notify(
                'success',
                $this->getCreatedNotificationMessage(),
            );
        }

        if ($another) {

            $this->form->model($this->record[0]::class);
            $this->record = null;

            $this->fillForm();

            return;
        }

        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected function saveFormAction(): Action
    {
        return Action::make('save')
            ->label('Create')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function saveAnother()
    {
        return $this->save(true);
    }

    protected function CreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label(__('filament::resources/pages/create-record.form.actions.create_another.label'))
            ->action('createAnother')
            ->keyBindings(['mod+shift+s'])
            ->color('secondary');
    }
    protected function cancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament::resources/pages/create-record.form.actions.cancel.label'))
            ->url(static::getResource()::getUrl())
            ->color('secondary');
    }
    protected function getFormActions(): array
    {
        return [
            $this->saveFormAction(),
            $this->CreateAnotherFormAction(),
            $this->cancelFormAction(),
        ];
    }
}
