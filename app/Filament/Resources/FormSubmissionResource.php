<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormSubmissionResource\Pages;
use App\Filament\Resources\FormSubmissionResource\RelationManagers;
use App\Models\FormSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Forms Management';
    protected static ?string $label = 'Submitted forms';
    protected static ?int $navigationSort = 3; 



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('form.name')->label('Form Name'), 
                TextColumn::make('form.country_id')->label('Country name')
                ->toggleable(isToggledHiddenByDefault: true), 
                TextColumn::make('created_at')->label('Created At')
                ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Country Info')
                ->schema([          
                    TextEntry::make('id')->label('ID'),
                    TextEntry::make('form.name')->label('Form Name'), 
                    Card::make()
                    ->schema([
                        KeyValueEntry::make('data')
                            ->label('Submitted Data')
                            ->state(function ($record) {
                                $data = json_decode($record->data, true) ?? [];
                                if (isset($data['uploadedFiles'])) {
                                    $data = array_merge($data, $data['uploadedFiles']);
                                    unset($data['uploadedFiles']); 
                                }
                                return $data; 
                            }),
                    ]),
                    Section::make('Uploaded Files')
                    ->schema([
                        
                        Card::make()->schema(function ($record) {
                            $data = json_decode($record->data, true) ?? [];
                            $uploadedFiles = $data['uploadedFiles'] ?? [];
    
                            $fileEntries = [];
                            foreach ($uploadedFiles as $fileKey => $fileValue) {
                                if (is_string($fileValue)) {
                                    if (self::isImage($fileValue)) {
                                        $fileEntries[] = ImageEntry::make($fileKey)
                                            ->label(ucfirst($fileKey))
                                            ->url($fileValue);
                                    } 
                                  
                                }
                            }
                            return $fileEntries; 
                        }),
                    ])->columns(1),
                 
                          
                ])->columns(2)            
            ]);
    } 


protected static function isImage($filePath): bool
{

    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    return in_array(strtolower($extension), $imageExtensions);
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
            'index' => Pages\ListFormSubmissions::route('/'),
            'create' => Pages\CreateFormSubmission::route('/create'),
            'edit' => Pages\EditFormSubmission::route('/{record}/edit'),
        ];
    }
}
