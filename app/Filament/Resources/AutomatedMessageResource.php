<?php

namespace App\Filament\Resources;

use App\Models\AutomatedMessage;
use App\Filament\Resources\AutomatedMessageResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AutomatedMessageResource extends Resource
{
    protected static ?string $model = AutomatedMessage::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationGroup = 'תקשורת';
    
    protected static ?int $navigationSort = 7;
    
    public static function getModelLabel(): string
    {
        return 'תבנית הודעה';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'תבניות הודעות';
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
                                    ->label('סוג הודעה')
                                    ->required()
                                    ->options([
                                        'rsvp_invitation' => 'הזמנה למענה',
                                        'rsvp_reminder' => 'תזכורת למענה',
                                        'event_reminder' => 'תזכורת לאירוע',
                                        'thank_you' => 'תודה על ההשתתפות',
                                        'custom' => 'מותאם אישית',
                                    ])
                                    ->columnSpanFull(),

                                Forms\Components\Tabs::make('Content')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make('Hebrew')
                                            ->label('תוכן בעברית')
                                            ->schema([
                                                Forms\Components\TextInput::make('content.he.subject')
                                                    ->label('נושא')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\RichEditor::make('content.he.body')
                                                    ->label('תוכן ההודעה')
                                                    ->required()
                                                    ->toolbarButtons([
                                                        'bold',
                                                        'italic',
                                                        'link',
                                                        'redo',
                                                        'undo',
                                                        'strike',
                                                    ])
                                                    ->helperText('תגים זמינים: {name}, {date}, {time}, {location}, {rsvp_link}'),
                                            ]),
                                            
                                        Forms\Components\Tabs\Tab::make('English')
                                            ->label('תוכן באנגלית')
                                            ->schema([
                                                Forms\Components\TextInput::make('content.en.subject')
                                                    ->label('Subject')
                                                    ->maxLength(255),
                                                Forms\Components\RichEditor::make('content.en.body')
                                                    ->label('Message Content')
                                                    ->toolbarButtons([
                                                        'bold',
                                                        'italic',
                                                        'link',
                                                        'redo',
                                                        'undo',
                                                        'strike',
                                                    ])
                                                    ->helperText('Available tags: {name}, {date}, {time}, {location}, {rsvp_link}'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                Forms\Components\KeyValue::make('settings')
                                    ->label('הגדרות נוספות')
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
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'rsvp_invitation' => 'הזמנה למענה',
                        'rsvp_reminder' => 'תזכורת למענה',
                        'event_reminder' => 'תזכורת לאירוע',
                        'thank_you' => 'תודה על ההשתתפות',
                        'custom' => 'מותאם אישית',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'rsvp_invitation' => 'success',
                        'rsvp_reminder' => 'warning',
                        'event_reminder' => 'danger',
                        'thank_you' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('message_logs_count')
                    ->label('נשלחו')
                    ->counts('messageLogs')
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
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('סוג')
                    ->options([
                        'rsvp_invitation' => 'הזמנה למענה',
                        'rsvp_reminder' => 'תזכורת למענה',
                        'event_reminder' => 'תזכורת לאירוע',
                        'thank_you' => 'תודה על ההשתתפות',
                        'custom' => 'מותאם אישית',
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
            'index' => Pages\ListAutomatedMessages::route('/'),
            'create' => Pages\CreateAutomatedMessage::route('/create'),
            'view' => Pages\ViewAutomatedMessage::route('/{record}'),
            'edit' => Pages\EditAutomatedMessage::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('messageLogs');
    }
}