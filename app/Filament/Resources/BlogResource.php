<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Support\Enums\FontWeight;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();
    
        if ($count >= 0 && $count <= 2) {
            return 'danger';
        } elseif ($count >= 3 && $count <= 5) {
            return 'warning';
        } elseif ($count >= 6 && $count <= 10) {
            return 'primary';
        } else {
            return 'success';
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Basic Information')
                        ->icon('heroicon-o-identification')
                        ->completedIcon('heroicon-m-identification')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('subtitle')
                                ->maxLength(255),
                            Forms\Components\Select::make('category')
                                ->searchable()
                                ->label('Category')
                                ->options(BlogCategory::all()->pluck('name', 'name'))
                                ->required(),
                            Forms\Components\FileUpload::make('image')
                                ->image()
                                ->disk('public')
                                ->directory('blogImages')
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                        ->prepend('blog-images-'),
                                )
                                ->columnSpanFull(),
                        ])->columns(2),
    
                    Step::make('Tags and Keywords')
                        ->icon('heroicon-o-tag')
                        ->completedIcon('heroicon-o-tag')
                        ->schema([
                            Forms\Components\TagsInput::make('tags')
                                ->separator(','),
                            Forms\Components\TagsInput::make('keywords')
                                ->separator(','),
                        ])->columns(2),
    
                    Step::make('Location')
                        ->icon('heroicon-o-map-pin')
                        ->completedIcon('heroicon-o-map-pin')
                        ->schema([
                            Select::make('country')
                                ->options(fn () => Country::pluck('name', 'name')->toArray())
                                ->placeholder('Select Country')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set) {
                                    $set('state', null);
                                    $set('city', null);
                                })
                                ->required()
                                ->columnSpanFull(),
                            Select::make('state')
                                ->placeholder('Select State')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->options(function (Get $get) {
                                    $countryName = $get('country');
                                    if (!$countryName) {
                                        return [];
                                    }
                                    return State::whereHas('country', function ($query) use ($countryName) {
                                        $query->where('name', $countryName);
                                    })->pluck('name', 'name')->toArray();
                                })
                                ->afterStateUpdated(fn (Set $set) => $set('city', null))
                                ->required(),
                            Select::make('city')
                                ->placeholder('Select City')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->options(function (Get $get) {
                                    $stateName = $get('state');
                                    if (!$stateName) {
                                        return [];
                                    }
                                    return City::whereHas('state', function ($query) use ($stateName) {
                                        $query->where('name', $stateName);
                                    })->pluck('name', 'name')->toArray();
                                })
                                ->required(),
                        ])->columns(2),
    
                    Step::make('Content')
                        ->icon('heroicon-o-document-text')
                        ->completedIcon('heroicon-m-document-text')
                        ->schema([
                            Forms\Components\RichEditor::make('description')
                                ->required()
                                ->columnSpanFull(),
                        ]),
    
                    Step::make('Publishing Details')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->completedIcon('heroicon-m-clipboard-document-list')
                        ->schema([
                            Forms\Components\TextInput::make('author')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Toggle::make('is_published')
                                ->required(),
                            Forms\Components\DatePicker::make('date')
                                ->required(),
                        ])->columns(3),
                ])
                ->columnSpan('full')
                ->skippable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->columns([
            //     Tables\Columns\TextColumn::make('title'),
            //     Tables\Columns\TextColumn::make('category'),
            //     Tables\Columns\BooleanColumn::make('is_published'),
            //     Tables\Columns\TextColumn::make('date')
            //         ->date(),
            //     Tables\Columns\TextColumn::make('deleted_at')
            //         ->dateTime()
            //         ->sortable()
            //         ->toggleable(isToggledHiddenByDefault: true),
            //     Tables\Columns\TextColumn::make('created_at')
            //         ->dateTime()
            //         ->sortable()
            //         ->toggleable(isToggledHiddenByDefault: true),
            //     Tables\Columns\TextColumn::make('updated_at')
            //         ->dateTime()
            //         ->sortable()
            //         ->toggleable(isToggledHiddenByDefault: true),
            // ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('image')
                        ->height('100%')
                        ->width('100%'),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('title')
                            ->weight(FontWeight::Bold)
                            ->limit(40),
                        Tables\Columns\TextColumn::make('subtitle')
                            ->color('gray')
                            ->limit(60),
                    ]),
                ])->space(3),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\ColorColumn::make('color')
                            ->grow(false),
                        Tables\Columns\TextColumn::make('description')
                            ->color('gray'),
                    ]),
                ])->collapsible(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 2,
            ])
            ->paginated([
                18,
                36,
                72,
                'all',
            ])
            ->actions([
                Tables\Actions\Action::make('visit')
                    ->label('Visit link')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Blog $record): string => 'https://127.0.0.1:8000/public/blogs/' . urlencode($record->slug) . '.in'),
                    // ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'view' => Pages\ViewBlog::route('/{record}'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}