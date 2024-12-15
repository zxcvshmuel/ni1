<?php

namespace App\Filament\Resources;

use App\Models\TemplateCategory;
use App\Filament\Resources\TemplateCategoryResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TemplateCategoryResource extends Resource
{
    protected static ?string $model = TemplateCategory::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'ניהול תוכן';
    
    protected static ?int $navigationSort = 3;
    
    public static function getModelLabel(): string
    {
        return 'קטגוריית תבניות';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'קטגוריות תבניות';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name.he')
                            ->label('שם (עברית)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, Forms\Set $set) {
                                if (! $set->get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                            
                        Forms\Components\TextInput::make('name.en')
                            ->label('שם (אנגלית)')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->rules(['regex:/^[a-z0-9\-]+$/'])
                            ->helperText('משמש לכתובת URL. רק אותיות באנגלית, מספרים ומקפים'),
                        
                        Forms\Components\Select::make('parent_id')
                            ->label('קטגוריית אב')
                            ->relationship(
                                'parent',
                                'name->he',
                                fn (Builder $query, $record) => $query->when(
                                    $record, 
                                    fn($q) => $q->where('id', '!=', $record->id)
                                        ->whereNot('parent_id', $record->id)
                                )
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('בחר קטגוריית אב'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parent.name.he')
                    ->label('קטגוריית אב')
                    ->placeholder('-')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('name.he')
                    ->label('שם')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(JSON_EXTRACT(name, "$.he")) LIKE ?', ['%' . strtolower($search) . '%'])
                                 ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%' . strtolower($search) . '%']);
                        });
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('templates_count')
                    ->label('תבניות')
                    ->counts('templates')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('children_count')
                    ->label('תת-קטגוריות')
                    ->counts('children')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('עדכון אחרון')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('עריכה'),
                    Tables\Actions\DeleteAction::make()
                        ->label('מחיקה')
                        ->requiresConfirmation()
                        ->before(function (TemplateCategory $record) {
                            // Move children to parent if exists
                            if ($record->parent_id) {
                                $record->children()->update(['parent_id' => $record->parent_id]);
                            } else {
                                // Move to root if no parent
                                $record->children()->update(['parent_id' => null]);
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('מחיקה')
                        ->requiresConfirmation()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->parent_id) {
                                    $record->children()->update(['parent_id' => $record->parent_id]);
                                } else {
                                    $record->children()->update(['parent_id' => null]);
                                }
                            }
                        }),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplateCategories::route('/'),
            'create' => Pages\CreateTemplateCategory::route('/create'),
            'edit' => Pages\EditTemplateCategory::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['templates', 'children']);
    }
}