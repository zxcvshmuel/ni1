<?php

namespace App\Filament\Resources;

use App\Models\Effect;
use App\Filament\Resources\EffectResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EffectResource extends Resource
{
    protected static ?string $model = Effect::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    
    protected static ?string $navigationGroup = 'ניהול תוכן';
    
    protected static ?int $navigationSort = 5;
    
    public static function getModelLabel(): string
    {
        return 'אפקט';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'אפקטים';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name.he')
                                    ->label('שם (עברית)')
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('name.en')
                                    ->label('שם (אנגלית)')
                                    ->maxLength(255),
                                
                                Forms\Components\Select::make('type')
                                    ->label('סוג אפקט')
                                    ->required()
                                    ->options([
                                        'animation' => 'אנימציה',
                                        'transition' => 'מעבר',
                                        'particle' => 'חלקיקים',
                                        'background' => 'רקע',
                                        'interactive' => 'אינטראקטיבי',
                                    ])
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('description.he')
                                    ->label('תיאור (עברית)')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('description.en')
                                    ->label('תיאור (אנגלית)')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->columnSpanFull(),

                                Forms\Components\KeyValue::make('settings')
                                    ->label('הגדרות')
                                    ->keyLabel('מפתח')
                                    ->valueLabel('ערך')
                                    ->addActionLabel('הוספת הגדרה')
                                    ->reorderable()
                                    ->columnSpanFull(),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('פעיל')
                                    ->default(true)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.he')
                    ->label('שם')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(JSON_EXTRACT(name, "$.he")) LIKE ?', ['%' . strtolower($search) . '%'])
                                 ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%' . strtolower($search) . '%']);
                        });
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('סוג')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'animation' => 'success',
                        'transition' => 'warning',
                        'particle' => 'danger',
                        'background' => 'info',
                        'interactive' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('invitations_count')
                    ->label('הזמנות')
                    ->counts('invitations')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('פעיל'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('עדכון אחרון')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('סוג')
                    ->options([
                        'animation' => 'אנימציה',
                        'transition' => 'מעבר',
                        'particle' => 'חלקיקים',
                        'background' => 'רקע',
                        'interactive' => 'אינטראקטיבי',
                    ]),
                
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
                        ->label('מחיקה'),
                ]),
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
            'index' => Pages\ListEffects::route('/'),
            'create' => Pages\CreateEffect::route('/create'),
            'view' => Pages\ViewEffect::route('/{record}'),
            'edit' => Pages\EditEffect::route('/{record}/edit'),
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