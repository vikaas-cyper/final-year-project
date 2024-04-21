<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\StockistResource\Pages;
use App\Models\Stockist;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StockistResource extends Resource
{
    protected static ?string $model = Stockist::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Manager';

    protected static ?string $label = 'Stockist';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                           ->label('Name of the Stockist')
                           ->required()
                           ->inlineLabel(),

                Select::make('marketing_representative_id')
                        ->relationship('marketing_representative', 'name', fn (Builder $query) => $query->where('status', '=', 1))
                        ->required()
                        ->inlineLabel()
                        ->searchable(),

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
                TextColumn::make('name')
                            ->label('Name')
                            ->searchable()
                            ->sortable(),

                TextColumn::make('marketing_representative.name')
                            ->label('Marketing Representative')
                            ->sortable(),

                BooleanColumn::make('status')
                               ->label('Status')
                               ->sortable(),

                TextColumn::make('created_by')
                               ->label('Created By')
                               ->sortable()
                               ->searchable()
                               ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('status')
                              ->options(
                                  [
                                      1 => 'Active',
                                      0 => 'De-Active',
                                  ])
                              ->column('status'),

                SelectFilter::make('marketing_representative')
                              ->relationship('marketing_representative', 'name'),
            ])

            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->bulkActions([
                DeleteBulkAction::make(),
                FilamentExportBulkAction::make('export'),
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
            'index' => Pages\ListStockists::route('/'),
            'create' => Pages\CreateStockist::route('/create'),
            'view' => Pages\ViewStockist::route('/{record}'),
            'edit' => Pages\EditStockist::route('/{record}/edit'),
        ];
    }
}
