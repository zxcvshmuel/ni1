<?php

namespace App\Filament\Resources;

use App\Models\CreditPackage;
use App\Filament\Resources\CreditPackageResource\Pages;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ActionGroup;

class CreditPackageResource extends Resource
{
    protected static ?string $model = CreditPackage::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationGroup = 'ניהול תוכן';
    
    protected static ?int $navigationSort = 2;
    
    public static function getModelLabel(): string
    {
        return 'חבילת קרדיטים';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'חבילות קרדיטים';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name.he')
                            ->label('שם (עברית)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        TextInput::make('name.en')
                            ->label('שם (אנגלית)')
                            ->maxLength(255),
                            
                        Textarea::make('description.he')
                            ->label('תיאור (עברית)')
                            ->rows(3)
                            ->maxLength(1000),
                            
                        Textarea::make('description.en')
                            ->label('תיאור (אנגלית)')
                            ->rows(3)
                            ->maxLength(1000),
                            
                        TextInput::make('credits')
                            ->label('מספר קרדיטים')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(99999)
                            ->step(1)
                            ->rules(['integer', 'min:1'])
                            ->suffix('קרדיטים'),
                            
                        TextInput::make('price')
                            ->label('מחיר')
                            ->numeric()
                            ->required()
                            ->prefix('₪')
                            ->minValue(0)
                            ->maxValue(99999.99)
                            ->step(0.01)
                            ->rules(['numeric', 'min:0', 'max:99999.99']),
                            
                        Toggle::make('is_active')
                            ->label('פעיל')
                            ->default(true)
                            ->helperText('חבילות לא פעילות לא יוצגו למשתמשים'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name.he')
                    ->label('שם')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(JSON_EXTRACT(name, "$.he")) LIKE ?', ['%' . strtolower($search) . '%'])
                                 ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%' . strtolower($search) . '%']);
                        });
                    })
                    ->sortable(),
                    
                TextColumn::make('credits')
                    ->label('קרדיטים')
                    ->sortable()
                    ->alignEnd(),
                    
                TextColumn::make('price')
                    ->label('מחיר')
                    ->money('ILS')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('orders_count')
                    ->label('הזמנות')
                    ->counts('orders')
                    ->sortable()
                    ->alignEnd(),
                    
                ToggleColumn::make('is_active')
                    ->label('פעיל'),

                TextColumn::make('updated_at')
                    ->label('עדכון אחרון')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('פעיל')
                    ->boolean()
                    ->trueLabel('פעיל')
                    ->falseLabel('לא פעיל')
                    ->placeholder('הכל'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('עריכה'),
                    Tables\Actions\DeleteAction::make()
                        ->label('מחיקה')
                        ->requiresConfirmation(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('מחיקה')
                        ->requiresConfirmation(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditPackages::route('/'),
            'create' => Pages\CreateCreditPackage::route('/create'),
            'edit' => Pages\EditCreditPackage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}