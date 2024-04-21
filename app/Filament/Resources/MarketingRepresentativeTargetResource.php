<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\MarketingRepresentativeTargetResource\Pages;
use App\Filament\Resources\MarketingRepresentativeTargetResource\RelationManagers;
use App\Models\MarketingRepresentativeTarget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;


class MarketingRepresentativeTargetResource extends Resource
{
    protected static ?string $model = MarketingRepresentativeTarget::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Targets';
    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Marketing Representative Target';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('marketing_representatives_id')
                    ->label('Marketing Representative')
                    ->relationship('marketing_representative', 'name')
                    ->inlineLabel()
                    ->required()
                    ->searchable(),

                TextInput::make('target')
                           ->label('Target Need To Achieve')
                           ->inlineLabel()
                           ->numeric()
                           ->required(),

                DatePicker::make('month')
                            ->label('Month of Achievement')
                            ->inlineLabel()
                            ->required(),

                Toggle::make('status')
                        ->default(true)
                        ->inlineLabel()
                        ->required(),

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('marketing_representative.name')
                            ->label('Marketing Representative')
                            ->searchable()
                            ->sortable(),

                TextColumn::make('target')
                            ->label('Target')
                            ->sortable()
                            ->searchable()
                            ->formatStateUsing(fn (string $state): string => number_format($state, 0,'' , ' , ')),


                TextColumn::make('month')
                            ->label('Month')
                            ->searchable()
                            ->sortable()->date('F o'),

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
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMarketingRepresentativeTargets::route('/'),
            'create' => Pages\CreateMarketingRepresentativeTarget::route('/create'),
            'view' => Pages\ViewMarketingRepresentativeTarget::route('/{record}'),
            'edit' => Pages\EditMarketingRepresentativeTarget::route('/{record}/edit'),
        ];
    }
}
