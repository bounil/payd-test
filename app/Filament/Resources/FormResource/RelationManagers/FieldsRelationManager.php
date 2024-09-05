<?php

namespace App\Filament\Resources\FormResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->rules(['required', 'string', 'max:255']),

                Forms\Components\Select::make('type')
                ->options([
                    'text' => 'Text',
                    'number' => 'Number',
                    'date' => 'Date',
                    'file' => 'File Upload',
                ])
                ->required()
                ->rules(['required', 'in:text,number,date,dropdown,file']),

            Forms\Components\Toggle::make('is_required')
                ->label('Is Required')
                ->default(false),

            Forms\Components\Select::make('category')
                ->options([
                    'general' => 'General',
                    'identity' => 'Identity',
                    'bank' => 'Bank',
                ])
                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('type')
                ->sortable()
                ->searchable(),

            Tables\Columns\BooleanColumn::make('is_required')
                ->label('Required'),

            Tables\Columns\TextColumn::make('category')
                ->sortable()
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
