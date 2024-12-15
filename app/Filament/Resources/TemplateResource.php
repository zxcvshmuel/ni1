<?php

namespace App\Filament\Resources;

use App\Models\Template;
use App\Filament\Resources\TemplateResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    
    protected static ?string $navigationGroup = 'ניהול תוכן';
    
    protected static ?int $navigationSort = 4;
    
    public static function getModelLabel(): string
    {
        return 'תבנית';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'תבניות';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name.he')
                                    ->label('שם (עברית)')
                                    ->required()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('name.en')
                                    ->label('שם (אנגלית)')
                                    ->maxLength(255),

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
                                    
                                Forms\Components\Select::make('category_id')
                                    ->label('קטגוריה')
                                    ->relationship('category', 'name->he')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name.he')
                                            ->label('שם (עברית)')
                                            ->required(),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique('template_categories', 'slug'),
                                    ])
                                    ->columnSpanFull(),

                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->label('תמונה ראשית')
                                    ->collection('thumbnails')
                                    ->image()
                                    ->imageEditor()
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('פעיל')
                            ->default(true)
                            ->helperText('האם התבנית זמינה למשתמשים'),
                            
                        Forms\Components\Placeholder::make('created_at')
                            ->label('נוצר ב')
                            ->content(fn (?Template $record): string => $record?->created_at?->format('d/m/Y H:i') ?? '-'),
                            
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('עודכן ב')
                            ->content(fn (?Template $record): string => $record?->updated_at?->format('d/m/Y H:i') ?? '-'),
                            
                        Forms\Components\Placeholder::make('invitations_count')
                            ->label('הזמנות בשימוש')
                            ->content(fn (?Template $record): string => $record?->invitations_count ?? '0'),
                    ])
                    ->columnSpan(['lg' => 1]),

                Forms\Components\Section::make('קוד המערכת')
                    ->schema([
                        Forms\Components\Textarea::make('html_structure')
                            ->label('HTML')
                            ->required()
                            ->rows(15)
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('css_styles')
                            ->label('CSS')
                            ->required()
                            ->rows(15)
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('settings')
                            ->label('הגדרות נוספות')
                            ->keyLabel('מפתח')
                            ->valueLabel('ערך')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->label('תמונה')
                    ->collection('thumbnails')
                    ->width(100)
                    ->height(60),
                    
                Tables\Columns\TextColumn::make('name.he')
                    ->label('שם')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(JSON_EXTRACT(name, "$.he")) LIKE ?', ['%' . strtolower($search) . '%'])
                                 ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%' . strtolower($search) . '%']);
                        });
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('category.name.he')
                    ->label('קטגוריה')
                    ->sortable(),
                    
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('קטגוריה')
                    ->relationship('category', 'name->he')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'view' => Pages\ViewTemplate::route('/{record}'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
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