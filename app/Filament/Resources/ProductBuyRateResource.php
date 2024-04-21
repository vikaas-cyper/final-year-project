<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductBuyRateResource\Pages;
use App\Filament\Resources\ProductBuyRateResource\RelationManagers;
use App\Models\ProductBuyRate;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\BooleanColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Svg\Tag\Text;

class ProductBuyRateResource extends Resource
{
    protected static ?string $model = ProductBuyRate::class;

    protected static ?string $label = 'Product Purchase Detail';
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('product', 'name', fn (Builder $query) => $query->where('status', '=', 1))
                    ->searchable()
                    ->inlineLabel()
                    ->required(),

                TextInput::make('price_per_unit')
                        ->label('Prize per unit')
                        ->inlineLabel()
                        ->required()
                        ->numeric(),

                Toggle::make('status')
                        ->label('Status')
                        ->required()
                        ->inlineLabel()
                        ->default(true),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->label('Product Name'),

                TextColumn::make('price_per_unit')->sortable()->label('Rate per unit'),

                BooleanColumn::make('status')
                               ->label('Status')
                               ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListProductBuyRates::route('/'),
            'create' => Pages\CreateProductBuyRate::route('/create'),
            'view' => Pages\ViewProductBuyRate::route('/{record}'),
            'edit' => Pages\EditProductBuyRate::route('/{record}/edit'),
        ];
    }
}
