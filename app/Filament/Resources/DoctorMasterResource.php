<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\DoctorMasterResource\Pages;
use App\Models\DoctorMaster;
use App\Models\MarketingRepresentative;
use Filament\Forms\Components\Select;
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

class DoctorMasterResource extends Resource
{
    protected static ?string $model = DoctorMaster::class;

    protected static ?string $label = 'Doctor Master';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';

    protected static ?string $navigationGroup = 'Master';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('billing_id')
                        ->label('Billing & Chemist')
                        ->relationship('billing', 'billing_name', fn (Builder $query) => $query->where('status', '=', 1))
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->billing_name} , {$record->doctor_name}")
                        ->searchable()
                        ->inlineLabel()
                        ->required(),

                Select::make('marketing_representative_id')
                        ->label('Marketing Representative')
                        ->relationship('marketing_representative', 'name', fn (Builder $query) => $query->where('status', '=', 1))
                        ->searchable()
                        ->inlineLabel()
                        ->required(),

                Toggle::make('status')
                        ->label('Status')
                        ->required()
                        ->default(true)
                        ->inlineLabel()
                        ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('billing.billing_name')
                            ->label('Billing Name')
                            ->searchable()
                            ->sortable(),

                TextColumn::make('billing.doctor_name')
                            ->label('Doctor/Chemist')
                            ->searchable()
                            ->sortable(),

                TextColumn::make('marketing_representative')
                            ->label('Marketing Representative')
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->sortable(),

                BooleanColumn::make('status')
                               ->label('Status')
                               ->sortable(),

                TextColumn::make('created_by')
                               ->label('Created By')
                               ->sortable()
                               ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([
                SelectFilter::make('status')
                              ->options([
                                  1 => 'Active',
                                  0 => 'De-Active',
                              ])
                              ->column('status'),

                SelectFilter::make('marketing_representative')
                    ->options(MarketingRepresentative::all()->pluck('name', 'id'))
                    ->column('marketing_representative.name'),
            ])

            ->actions([
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
            'import' => Pages\ImportDoctorMaster::route('/import'),
            'index' => Pages\ListDoctorMasters::route('/'),
            'create' => Pages\CreateDoctorMaster::route('/create'),
            'view' => Pages\ViewDoctorMaster::route('/{record}'),
            'edit' => Pages\EditDoctorMaster::route('/{record}/edit'),

        ];
    }
}
