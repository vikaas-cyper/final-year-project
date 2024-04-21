<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorTargetResource\Pages;
use App\Filament\Resources\DoctorTargetResource\RelationManagers;


use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Models\DoctorTarget;

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


class DoctorTargetResource extends Resource
{
    protected static ?string $model = DoctorTarget::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Targets';
    
    protected static ?string $label = 'Doctor Targets';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('doctor_master_id')
                    ->label('Distributed To')
                    ->placeholder('Billing | Doctor')
                    ->options(DoctorTarget::doctor_master_search())
                    ->required()
                    ->inlineLabel()
                    ->searchable(),

                TextInput::make('target')
                           ->label('Target Need To Achieve')
                           ->inlineLabel()
                           ->numeric()
                           ->required(),

                DatePicker::make('start_month')
                            ->label('Start Month')
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
                TextColumn::make('billing_name')
                        ->label('Billing Name')
                        ->searchable()
                        ->sortable(),

                TextColumn::make('doctor_name')
                        ->label('Doctor Name')
                        ->searchable()
                        ->sortable(),

                TextColumn::make('target')
                            ->label('Target')
                            ->sortable()
                            ->searchable()
                            ->formatStateUsing(fn (string $state): string => number_format($state, 0,'' , ' , ')),


                TextColumn::make('start_month')
                            ->label('Start Month')
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
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                FilamentExportBulkAction::make('Export'),
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
            'index' => Pages\ListDoctorTargets::route('/'),
            'create' => Pages\CreateDoctorTarget::route('/create'),
            'view' => Pages\ViewDoctorTarget::route('/{record}'),
            'edit' => Pages\EditDoctorTarget::route('/{record}/edit'),
        ];
    }    
}
