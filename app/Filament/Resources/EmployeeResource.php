<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'employee management';

    protected static ?int $navigationSort = 1;

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

    public static function getNavigationBadgeTooltip(): ?string
    {
        $employees = static::getModel()::with('department')
            ->get()
            ->map(function ($employee) {
                $departmentName = $employee->department ? $employee->department->name : 'No Department';
                return "{$employee->first_name} {$employee->last_name} : {$departmentName},";
            })
            ->implode("\n");
    
        return $employees;
    }
    
    protected function onValidationError(ValidationException $exception): void
    {
        try {
            parent::onValidationError($exception);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                $this->notify('error', 'An employee with this email already exists.');
            } else {
                throw $e;
            }
        }
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(table: Employee::class, column: 'email', ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->required()
                        ->maxLength(255),
                ])->columns(2),
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('country_id')
                        ->relationship(name: 'country', titleAttribute: 'name')
                        ->placeholder('Select Country')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function (Set $set) {
                            $set('state_id', null);
                            $set('city_id', null);
                        })
                        ->required(),
                
                Forms\Components\Select::make('state_id')
                    ->label('State')
                    ->placeholder('Select State')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->options(function (Get $get) {
                        $countryId = $get('country_id');
                        if (!$countryId) {
                            return [];
                        }
                        return State::where('country_id', $countryId)
                            ->pluck('name', 'id');
                    })
                    ->getSearchResultsUsing(function (string $search, Get $get) {
                        $countryId = $get('country_id');
                        if (!$countryId) {
                            return [];
                        }
                        return State::where('country_id', $countryId)
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id');
                    })
                    ->getOptionLabelUsing(fn ($value): ?string => State::find($value)?->name)
                    ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                    ->required(),
                
                Forms\Components\Select::make('city_id')
                    ->label('City')
                    ->placeholder('Select City')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->options(function (Get $get) {
                        $stateId = $get('state_id');
                        if (!$stateId) {
                            return [];
                        }
                        return City::where('state_id', $stateId)
                            ->pluck('name', 'id');
                    })
                    ->getSearchResultsUsing(function (string $search, Get $get) {
                        $stateId = $get('state_id');
                        if (!$stateId) {
                            return [];
                        }
                        return City::where('state_id', $stateId)
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id');
                    })
                    ->getOptionLabelUsing(fn ($value): ?string => City::find($value)?->name)
                    ->required(),
                    Forms\Components\TextInput::make('zip_code')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('address')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columns(2),
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->required(),
                        Forms\Components\Select::make('employment_type_id')
                            ->relationship('employmentType', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('current_package')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->suffix('Yearly'),
                    ])->columns(3),
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('github')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('linkedin')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('facebook')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('twitter')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('instagram')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),
                Section::make()
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->disk('public')
                            ->directory('employees')
                            ->imageEditor()
                            ->imageEditorMode(2)
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->downloadable(),
                        Forms\Components\FileUpload::make('cv')
                            ->required()
                            ->downloadable()
                            ->previewable(true)
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
                    ])->columns(2),
                Section::make()
                    ->schema([
                        Forms\Components\TagsInput::make('skills')
                            ->required(),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->required(),
                        Forms\Components\DatePicker::make('date_of_hired')
                            ->required(),
                    ])->columns(3),
                Section::make()
                ->schema([
                    Forms\Components\Toggle::make('is_terminated')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $set('is_active', false);
                            }
                        }),
                    Forms\Components\DatePicker::make('date_of_termination')
                        ->visible(fn (callable $get) => $get('is_terminated')),
                    Forms\Components\Toggle::make('is_active')
                        ->disabled(fn (callable $get) => $get('is_terminated')),
                ])->columns(3),
            Forms\Components\Repeater::make('experiences')
                ->relationship('experiences')
                ->schema([
                    Forms\Components\TextInput::make('employer')->required()->label('e.g. Rama Technologies'),
                    Section::make()
                        ->schema([
                            Forms\Components\DatePicker::make('from')->required(),
                            Forms\Components\DatePicker::make('to')->required(),
                        ])->columns(2),
                    Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('employer_email')->email()->required(),
                            Forms\Components\TextInput::make('employee_id_at_employer')->required()->label('prev employee id'),
                        ])->columns(2),
                    Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('job_role')->required(),
                            Forms\Components\TextInput::make('job_location')->required(),
                        ])->columns(2),
                ]),
            Forms\Components\Repeater::make('qualifications')
                ->relationship('qualifications')
                ->schema([
                    Forms\Components\TextInput::make('college')->required(),
                    Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('course')->required(),
                            Forms\Components\TextInput::make('branch')->required(),
                        ])->columns(2),
                    Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('from_year')->numeric()->required()->label('Starting year'),
                            Forms\Components\TextInput::make('passing_year')->numeric()->required(),
                            Forms\Components\TextInput::make('percentage')->numeric()->required(),
                        ])->columns(3),
                    Forms\Components\FileUpload::make('certificate')
                        ->required()
                        ->acceptedFileTypes(['application/pdf', 'image/*']),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->first_name . ' ' . $record->last_name;
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employmentType.name'),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\ToggleColumn::make('is_terminated'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // SelectFilter::make('country_id')
                //     ->label('Country')
                //     ->relationship('country', 'name')
                //     ->searchable()
                //     ->preload(),

                // Filter::make('state')
                //     ->form([
                //         Forms\Components\Select::make('state_id')
                //             ->label('State')
                //             ->options(function (callable $get) {
                //                 $countryId = $get('country_id');
                //                 if (!$countryId) {
                //                     return State::pluck('name', 'id');
                //                 }
                //                 return State::where('country_id', $countryId)->pluck('name', 'id');
                //             })
                //             ->searchable()
                //             ->preload()
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query->when(
                //             $data['state_id'],
                //             fn (Builder $query, $stateId): Builder => $query->where('state_id', $stateId)
                //         );
                //     }),

                // Filter::make('city')
                //     ->form([
                //         Forms\Components\Select::make('city_id')
                //             ->label('City')
                //             ->options(function (callable $get) {
                //                 $stateId = $get('state_id');
                //                 if (!$stateId) {
                //                     return City::pluck('name', 'id');
                //                 }
                //                 return City::where('state_id', $stateId)->pluck('name', 'id');
                //             })
                //             ->searchable()
                //             ->preload()
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query->when(
                //             $data['city_id'],
                //             fn (Builder $query, $cityId): Builder => $query->where('city_id', $cityId)
                //         );
                //     }),

                Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->placeholder('Enter name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['name'],
                                fn (Builder $query, $name): Builder => $query
                                    ->where('first_name', 'like', $name . '%')
                                    ->orWhere('last_name', 'like', $name . '%')
                            );
                    }),

                SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('qualification')
                    ->form([
                        Forms\Components\TextInput::make('qualification')
                            ->label('Qualification')
                            ->placeholder('Enter qualification'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['qualification'],
                                fn (Builder $query, $qualification): Builder => $query
                                    ->whereHas('qualifications', function ($query) use ($qualification) {
                                        $query->where('course', 'like', '%' . $qualification . '%')
                                            ->orWhere('college', 'like', '%' . $qualification . '%');
                                    })
                            );
                    }),

                Filter::make('experience')
                    ->form([
                        Forms\Components\TextInput::make('experience')
                            ->label('Experience')
                            ->placeholder('Enter job role or company'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['experience'],
                                fn (Builder $query, $experience): Builder => $query
                                    ->whereHas('experiences', function ($query) use ($experience) {
                                        $query->where('job_role', 'like', '%' . $experience . '%')
                                            ->orWhere('employer', 'like', '%' . $experience . '%');
                                    })
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('first_name');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'department.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->first_name . ' ' . $record->last_name,
            'Email' => $record->email,
            'Department' => $record->department->name,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('department');
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}