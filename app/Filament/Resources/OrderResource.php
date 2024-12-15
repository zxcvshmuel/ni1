<?php

namespace App\Filament\Resources;

use App\Models\Order;
use App\Filament\Resources\OrderResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'ניהול כספים';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'הזמנת קרדיטים';
    }

    public static function getPluralModelLabel(): string
    {
        return 'הזמנות קרדיטים';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('משתמש')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('credit_package_id')
                            ->label('חבילת קרדיטים')
                            ->relationship('creditPackage', 'name->he')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $package = \App\Models\CreditPackage::find($state);
                                    if ($package) {
                                        $set('credits', $package->credits);
                                        $set('amount', $package->price);
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('amount')
                            ->label('סכום')
                            ->required()
                            ->numeric()
                            ->prefix('₪')
                            ->disabled(),

                        Forms\Components\TextInput::make('credits')
                            ->label('קרדיטים')
                            ->required()
                            ->numeric()
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('סטטוס')
                            ->required()
                            ->options([
                                'pending' => 'ממתין',
                                'completed' => 'הושלם',
                                'failed' => 'נכשל',
                                'refunded' => 'זוכה',
                            ]),

                        Forms\Components\TextInput::make('payment_id')
                            ->label('מזהה תשלום')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('אמצעי תשלום')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('invoice_number')
                            ->label('מספר חשבונית')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('סטטוס')
                    ->options([
                        'pending' => 'ממתין',
                        'completed' => 'הושלם',
                        'failed' => 'נכשל',
                        'refunded' => 'זוכה',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->label('תאריך')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('מתאריך'),
                        Forms\Components\DatePicker::make('until')
                            ->label('עד תאריך'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('צפייה'),
                    Tables\Actions\EditAction::make()
                        ->label('עריכה'),
                    Tables\Actions\Action::make('resend_invoice')
                        ->label('שליחת חשבונית')
                        ->icon('heroicon-o-paper-airplane')
                        ->requiresConfirmation()
                        ->action(fn(Order $record) => $record->sendInvoice()),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('מספר חשבונית')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('לקוח')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('creditPackage.name.he')
                    ->label('חבילה')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('סכום')
                    ->money('ILS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('credits')
                    ->label('קרדיטים')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('סטטוס')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'ממתין',
                        'completed' => 'הושלם',
                        'failed' => 'נכשל',
                        'refunded' => 'זוכה',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'info' => 'refunded',
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('אמצעי תשלום')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('תאריך')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'ממתין',
                        'completed' => 'הושלם',
                        'failed' => 'נכשל',
                        'refunded' => 'זוכה',
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'creditPackage']);
    }
}
