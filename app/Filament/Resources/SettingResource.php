<?php

namespace App\Filament\Resources;

use App\Models\Setting;
use App\Filament\Resources\SettingResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    
    protected static ?string $navigationGroup = 'הגדרות מערכת';
    
    protected static ?int $navigationSort = 1;
    
    public static function getModelLabel(): string
    {
        return 'הגדרה';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'הגדרות';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('group')
                            ->label('קבוצה')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name')
                            ->label('שם')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Toggle::make('locked')
                            ->label('נעול')
                            ->helperText('האם למנוע שינוי של ההגדרה'),

                        Forms\Components\Select::make('type')
                            ->label('סוג ערך')
                            ->options([
                                'string' => 'טקסט',
                                'number' => 'מספר',
                                'boolean' => 'בוליאני',
                                'array' => 'מערך',
                                'json' => 'JSON',
                            ])
                            ->default('string')
                            ->required()
                            ->live(),

                        Forms\Components\Group::make()
                            ->schema(fn (Forms\Get $get) => match ($get('type')) {
                                'string' => [
                                    Forms\Components\TextInput::make('payload.value')
                                        ->label('ערך')
                                        ->required(),
                                ],
                                'number' => [
                                    Forms\Components\TextInput::make('payload.value')
                                        ->label('ערך')
                                        ->numeric()
                                        ->required(),
                                ],
                                'boolean' => [
                                    Forms\Components\Toggle::make('payload.value')
                                        ->label('ערך')
                                        ->required(),
                                ],
                                'array' => [
                                    Forms\Components\KeyValue::make('payload.value')
                                        ->label('ערך')
                                        ->required(),
                                ],
                                'json' => [
                                    Forms\Components\Textarea::make('payload.value')
                                        ->label('ערך')
                                        ->required()
                                        ->json(),
                                ],
                                default => [],
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('קבוצה')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('שם')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('payload')
                    ->label('ערך')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        }
                        return $state;
                    }),

                Tables\Columns\IconColumn::make('locked')
                    ->label('נעול')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('עדכון אחרון')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('קבוצה')
                    ->options(fn () => Setting::query()
                        ->distinct()
                        ->pluck('group', 'group')
                        ->toArray()
                    ),

                Tables\Filters\TernaryFilter::make('locked')
                    ->label('נעול'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('צפייה'),
                    Tables\Actions\EditAction::make()
                        ->label('עריכה'),
                    Tables\Actions\DeleteAction::make()
                        ->label('מחיקה'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group');
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'view' => Pages\ViewSetting::route('/{record}'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}