<?php

namespace App\Filament\Resources;

use App\Models\Song;
use App\Filament\Resources\SongResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SongResource extends Resource
{
    protected static ?string $model = Song::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-musical-note';
    
    protected static ?string $navigationGroup = 'ניהול תוכן';
    
    protected static ?int $navigationSort = 6;
    
    public static function getModelLabel(): string
    {
        return 'שיר';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'שירים';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('שם השיר')
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('artist')
                                    ->label('אמן')
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\FileUpload::make('file_path')
                                    ->label('קובץ שמע')
                                    ->required()
                                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3'])
                                    ->maxSize(20480) // 20MB
                                    ->directory('songs')
                                    ->preserveFilenames()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('duration')
                                    ->label('אורך (בשניות)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->maxValue(600) // 10 minutes
                                    ->step(1)
                                    ->suffix('שניות'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('פעיל')
                                    ->default(true),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Section::make('נתונים')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('נוצר ב')
                            ->content(fn (?Song $record): string => $record?->created_at?->format('d/m/Y H:i') ?? '-'),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('עודכן ב')
                            ->content(fn (?Song $record): string => $record?->updated_at?->format('d/m/Y H:i') ?? '-'),

                        Forms\Components\Placeholder::make('plays_count')
                            ->label('מספר השמעות')
                            ->content(fn (?Song $record): string => number_format($record?->plays_count ?? 0)),

                        Forms\Components\Placeholder::make('invitations_count')
                            ->label('הזמנות בשימוש')
                            ->content(fn (?Song $record): string => $record ? $record->invitations()->count() : '0'),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('שם השיר')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('artist')
                    ->label('אמן')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('duration')
                    ->label('אורך')
                    ->formatStateUsing(fn (int $state): string => gmdate('i:s', $state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('plays_count')
                    ->label('השמעות')
                    ->sortable()
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('invitations_count')
                    ->label('הזמנות')
                    ->counts('invitations')
                    ->sortable()
                    ->alignEnd(),
                
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('פעיל'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('עדכון אחרון')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('סטטוס')
                    ->boolean()
                    ->trueLabel('פעיל')
                    ->falseLabel('לא פעיל')
                    ->placeholder('הכל'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('צפייה'),
                    Tables\Actions\EditAction::make()
                        ->label('עריכה'),
                    Tables\Actions\DeleteAction::make()
                        ->label('מחיקה')
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListSongs::route('/'),
            'create' => Pages\CreateSong::route('/create'),
            'view' => Pages\ViewSong::route('/{record}'),
            'edit' => Pages\EditSong::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('invitations');
    }
}