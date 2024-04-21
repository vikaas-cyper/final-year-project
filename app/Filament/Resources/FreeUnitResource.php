<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\FreeUnitResource\Pages;
use App\Models\FreeUnit;
use App\Models\ProductSale;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class FreeUnitResource extends Resource
{
    protected static ?string $model = FreeUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $label = 'Free Unit Limit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('doctor_master_id')
                    ->label('Billing')
                    ->placeholder('Billing / Doctor')
                    ->options(ProductSale::doctor_master_search())
                    ->required()
                    ->inlineLabel()
                    ->columnSpan(2)
                    ->searchable(),

                Select::make('product_id')
                    ->label('Product')
                    ->placeholder('Product name')
                    ->relationship('product', 'name', fn (Builder $query) => $query->where('status', '=', 1))
                    ->required()
                    ->columnSpan(2)
                    ->inlineLabel()
                    ->searchable(),

                TextInput::make('free_unit')
                    ->numeric()
                    ->label('Free Unit Percentage')->maxValue(100)
                    ->columnSpan(2)
                    ->required()
                    ->inlineLabel(),

                DateTimePicker::make('month')
                    ->label('Month')
                    ->required()
                    ->default(now())
                    ->maxDate(now())
                    ->columnSpan(2)
                    ->withoutTime()
                    ->inlineLabel(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('doctor_master.billing.billing_name')
                    ->label('Billing')
                    ->searchable(),

                TextColumn::make('doctor_master.billing.doctor_name')
                    ->label('Doctor Name')
                    ->searchable(),

                TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('free_unit')
                    ->label('Allotted Percentage')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => number_format($state, 0,'' , ' , ')),


                TextColumn::make('month')
                    ->label('Month')
                    ->date('M Y')
                    ->sortable(),

                TextColumn::make('created_by')
                    ->label('Created By')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                FilamentExportBulkAction::make('Export'),

            ])->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'import' => Pages\ImportFreeUnit::route('/import'),
            'index' => Pages\ListFreeUnits::route('/'),
            'create' => Pages\CreateFreeUnit::route('/create'),
            'view' => Pages\ViewFreeUnit::route('/{record}'),
            'edit' => Pages\EditFreeUnit::route('/{record}/edit'),
        ];
    }
}
