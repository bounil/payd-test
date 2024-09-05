<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieldResource\Pages;
use App\Filament\Resources\FieldResource\RelationManagers;
use App\Models\Field;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Forms Management';
    protected static ?int $navigationSort = 2;  


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('form_id')
            ->label('Form')
            ->relationship('form', 'name') 
            ->required(),

        Forms\Components\TextInput::make('name')
            ->required()
            ->maxLength(255)
            ->rules(['required', 'string', 'max:255']),

        Forms\Components\Select::make('type')
            ->label('Type')
            ->options([
                'text' => 'Text',
                'number' => 'Number',
                'date' => 'Date',
                'file' => 'File Upload', 
            ])
            ->required(),

        Forms\Components\Toggle::make('is_required')
            ->label('Is Required')
            ->default(false),

        Forms\Components\Select::make('category')
            ->label('Category')
            ->options([
                'general' => 'General',
                'identity' => 'Identity',
                'bank' => 'Bank',
            ])
            ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('form.name')->label('Form ID')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('type')->sortable()->searchable(),
            Tables\Columns\BooleanColumn::make('is_required')->label('Required'),
            Tables\Columns\TextColumn::make('category')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime(),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListFields::route('/'),
            'create' => Pages\CreateField::route('/create'),
            'edit' => Pages\EditField::route('/{record}/edit'),
        ];
    }
}
