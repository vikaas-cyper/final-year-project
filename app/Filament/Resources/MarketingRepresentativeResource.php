<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\MarketingRepresentativeResource\Pages;
use App\Filament\Resources\MarketingRepresentativeResource\RelationManagers;
use App\Models\MarketingRepresentative;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketingRepresentativeResource extends Resource
{
    protected static ?string $model = MarketingRepresentative::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Manager';

    protected static ?int $navigationSort = 2;



    protected static ?string $label = "Marketing Representative";
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->inlineLabel(),

                TextInput::make('email')
                    ->email()
                    ->label('Email')
                    ->required()
                    ->inlineLabel(),

                Select::make('area_manager_id')
                    ->label('Area Manager')
                    ->relationship('area_manager', 'name', fn (Builder $query) => $query->where('status', '=', 1))
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

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
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

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMarketingRepresentatives::route('/'),
            'create' => Pages\CreateMarketingRepresentative::route('/create'),
            'view' => Pages\ViewMarketingRepresentative::route('/{record}'),
            'edit' => Pages\EditMarketingRepresentative::route('/{record}/edit'),
        ];
    }
}
