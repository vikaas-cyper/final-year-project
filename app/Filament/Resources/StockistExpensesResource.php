<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockistExpensesResource\Pages;
use App\Filament\Resources\StockistExpensesResource\RelationManagers;
use App\Models\StockistExpenses;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockistExpensesResource extends Resource
{
    protected static ?string $model = StockistExpenses::class;

    protected static ?string $label = 'Stockist Expenses over Month';
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('stockist_id')
                    ->relationship('stockist', 'name', fn (Builder $query) => $query->where('status', '=', 1))
                    ->searchable()
                    ->inlineLabel()
                    ->required(),

                TextInput::make('price_per_stockist')
                    ->label('Expenses over Stockist')
                    ->inlineLabel()
                    ->required()
                    ->numeric(),

                DatePicker::make('expenses_date')
                    ->inlineLabel()
                    ->label('Expenses Date')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stockist.name')
                            ->searchable()
                            ->sortable()
                            ->label('Stockist Name'),

                TextColumn::make('price_per_stockist')
                            ->sortable()
                            ->formatStateUsing(fn (string $state): string => number_format($state, 2, '.', ' , '))
                            ->label('Expenses over Stockist'),

                TextColumn::make('expenses_date')->dateTime('M d Y')

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
            'index' => Pages\ListStockistExpenses::route('/'),
            'create' => Pages\CreateStockistExpenses::route('/create'),
            'view' => Pages\ViewStockistExpenses::route('/{record}'),
            'edit' => Pages\EditStockistExpenses::route('/{record}/edit'),
        ];
    }
}
