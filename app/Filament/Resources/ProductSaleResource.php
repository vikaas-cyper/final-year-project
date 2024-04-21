<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\ProductSaleResource\Pages;
use App\Models\AreaManager;
use App\Models\MarketingRepresentative;
use App\Models\ProductSale;
use App\Models\Stockist;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Heloufir\FilamentWorkflowManager\Forms\Components\WorkflowStatusInput;
use Heloufir\FilamentWorkflowManager\Models\WorkflowUserPermission;
use Heloufir\FilamentWorkflowManager\Tables\Columns\WorkflowStatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Filters\Filter;
use Filament\Forms;


function is_area_manager(): array
{

    if(AreaManager::where('email' ,'=' ,auth()->user()->email)->first() != null){
        return [
            TextColumn::make('doctor_master.marketing_representative.name')
                ->label('Marketing Representative')
                ->sortable()
                ->searchable()
        ];
    }

    return [];
}

class ProductSaleResource extends Resource
{
    protected static ?string $model = ProductSale::class;

    protected static ?string $label = 'Product Sales';

    protected static ?string $navigationIcon = 'heroicon-o-currency-rupee';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

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
                    ->required()
                    ->columnSpan(2)
                    ->searchable(),

                DatePicker::make('month')
                    ->label('Distributed Month')
                    ->required()
                    ->columnSpan(2)
                    ->default(now())
                    ->maxDate(now())->withoutTime(true)
                    ->inlineLabel(),

                Select::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->required()
                    ->columnSpan(2)
                    ->inlineLabel()
                    ->searchable(),


                TextInput::make('sales_unit')
                    ->numeric()
                    ->label('Sales Unit')
                    ->default(0)
                    ->columnSpan(2)
                    ->inlineLabel()
                    ->required(),

                TextInput::make('free_unit')
                    ->numeric()
                    ->label('Free Unit')
                    ->default(0)
                    ->columnSpan(2)
                    ->required()
                    ->inlineLabel(),

                WorkflowStatusInput::make()
                    ->label('Workflow Status')
                    ->inlineLabel()->searchable(false)
                    ->columnSpan(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(array_merge(is_area_manager(),[

                TextColumn::make('stockist.name')
                            ->label('Distributed By')
                            ->toggleable()
                            ->searchable()
                            ->sortable(),

                TextColumn::make('doctor_master.billing.billing_name')
                            ->label('Distributed To')
                            ->searchable()
                            ->toggleable()
                            ->sortable(),

                TextColumn::make('doctor_master.billing.doctor_name')
                            ->label('Chemist')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->sortable(),

                TextColumn::make('product.name')
                            ->label('Product')
                            ->searchable()
                            ->sortable(),

                TextColumn::make('sales_total')
                            ->label('Sales Total')
                            ->searchable()
                            ->formatStateUsing(fn (string $state): string => number_format($state, 2, '.', ' , '))
                            ->sortable(),

                TextColumn::make('free_total')
                            ->label('Free total')
                            ->searchable()
                            ->formatStateUsing(fn (string $state): string => number_format($state, 2, '.', ' , '))
                            ->sortable(),
                
                            
                TextColumn::make('month')
                            ->dateTime('M d Y')
                            ->sortable(),

                TextColumn::make('created_at')
                            ->label("Data Imported at")
                            ->dateTime('M d Y')
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->sortable(),


                BadgeColumn::make('name')
                            ->label('Verification')
                            ->colors([
                                'warning' => 'Initiated',
                                'success' => 'Approved',
                                'danger' => 'Rejected',
                            ])

                            ->searchable()
                            ->sortable(),

                TextColumn::make('created_by')
                            ->label('Created By')
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->searchable()
                            ->sortable(),

            ]))
            ->filters([
                SelectFilter::make('status')
                            ->options(
                                [
                                    1 => 'Active',
                                    0 => 'De-Active',
                                ])
                            ->column('status'),

                SelectFilter::make('stockist_id')
                            ->label('Stockist')
                            ->options(self::stockistFilter()),

                SelectFilter::make('name')->label('Verification')
                                ->options(
                                    [
                                        'Initiated' => 'Initiated',
                                        'Approved' => 'Approved',
                                        'Rejected' => 'Rejected',
                                    ]
                                ),
                Filter::make('created_at')
                        ->form([
                            Forms\Components\DatePicker::make('created_from'),
                            Forms\Components\DatePicker::make('created_until'),
                        ])
                        ->query(function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['created_from'],
                                    fn (Builder $query, $date): Builder => $query->whereDate('product_sales.created_at', '>=', $date),
                                )
                                ->when(
                                    $data['created_until'],
                                    fn (Builder $query, $date): Builder => $query->whereDate('product_sales.created_at', '<=', $date),
                                );
                        })
            ])
            ->actions([

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions(

                array_merge([Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('Export')
                ], self::getWorkflowPermission())




            )->defaultSort('product_sales.created_at', 'desc');
    }

    public static function getWorkflowPermission(): array
    {
        $permission = WorkflowUserPermission::where('user_id', '=', auth()->user()->id)->first();


        $formAction = [];
        if ($permission )
        {
            $formAction = [
                BulkAction::make('approve')
                    ->action(fn (Collection $records) => $records->each->update(['status' => true]))
                    ->label('Approve')->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Are you sure to Approve all selected Sales Details')
                    ->modalSubheading('It cannot be changed')
            ];
        }
        return $formAction;
    }


    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function stockistFilter(){
        $user = auth()->user();
        $area_manager = AreaManager::where('email' ,'=' ,$user->email)->first();

        if($area_manager != null){
            $marketing_representative_id = $area_manager->marketing_representatives->pluck('id');
        }

        elseif ($user->hasRole('Super Admin')){
            $marketing_representative_id = MarketingRepresentative::all()->pluck('id');
        }

        else{
            $marketing_representative_id = [MarketingRepresentative::where('email', '=', $user->email)->first()->id];
        }

        return Stockist::whereIn('marketing_representative_id', $marketing_representative_id)->get()->pluck('name', 'id');
    }
    public static function getPages(): array
    {
        return [
            'import' => Pages\ImportProductSale::route('/import'),
            'index' => Pages\ListProductSales::route('/'),
            'create' => Pages\CreateProductSale::route('/create'),
            'view' => Pages\ViewProductSale::route('/{record}'),
            'edit' => Pages\EditProductSale::route('/{record}/edit'),
        ];
    }
}
