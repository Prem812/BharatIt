<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Resources\ProjectResource\RelationManagers\ExpensesRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\StatusesRelationManager;
use App\Models\Employee;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'employee management';

    protected static ?int $navigationSort = 2;

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
        $projectLeads = static::getModel()::with('projectLead')
            ->get()
            ->map(function ($project) {
                $leadName = $project->projectLead ? 
                    $project->projectLead->first_name . ' ' . $project->projectLead->last_name : 
                    'No Lead';
                return "{$project->name}: {$leadName}";
            })
            ->implode("\n");

        return $projectLeads;
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            DatePicker::make('start_date')
                ->required(),
            DatePicker::make('end_date')
                ->required(),
            TextInput::make('client_name')
                ->required()
                ->maxLength(255),
            TextInput::make('client_email')
                ->email()
                ->required()
                ->maxLength(255),
            Select::make('project_manager_id')
                ->label('Project Manager')
                ->options(Employee::query()->pluck('first_name', 'id'))
                ->required()
                ->searchable(),
            Select::make('employees')
                ->multiple()
                ->label('Team Members')
                ->options(Employee::query()->pluck('first_name', 'id'))
                ->required()
                ->reactive()
                ->searchable(),
            Select::make('project_lead_id')
                ->label('Project Lead')
                ->options(function (Get $get): Collection {
                    $employeeIds = $get('employees') ?? [];
                    return Employee::query()
                        ->whereIn('id', $employeeIds)
                        ->pluck('first_name', 'id')
                        ->filter() // Remove any null values
                        ->map(fn ($name, $id) => $name ?: "Employee ID: $id"); // Use ID if name is empty
                })
                ->required()
                ->searchable(),
            TagsInput::make('technologies')
                ->required(),
            Repeater::make('statuses')
                ->relationship('statuses')
                ->schema([
                    DatePicker::make('date')
                        ->required(),
                    Forms\Components\Textarea::make('status')
                        ->required(),
                ]),
            Repeater::make('expenses')
                ->relationship('expenses')
                ->schema([
                    TextInput::make('title')
                        ->required(),
                    TextInput::make('price')
                        ->numeric()
                        ->required(),
                    TextInput::make('alternative'),
                    Forms\Components\Textarea::make('description'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('end_date')->date()->sortable(),
                TextColumn::make('client_name')->sortable()->searchable(),
                TextColumn::make('projectManager.first_name')
                    ->label('Project Manager')
                    ->formatStateUsing(fn ($record) => $record->projectManager->first_name . ' ' . $record->projectManager->last_name)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('projectLead.first_name')
                    ->label('Project Lead')
                    ->formatStateUsing(fn ($record) => $record->projectLead->first_name . ' ' . $record->projectLead->last_name)
                    ->sortable()
                    ->searchable(),
                TagsColumn::make('technologies'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            StatusesRelationManager::class,
            ExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}