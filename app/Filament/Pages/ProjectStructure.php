<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\type;

class ProjectStructure extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static string $view = 'filament.pages.project-structure';

    protected static ?string $navigationLabel = 'Project Structure';

    public static function canAccess(): bool
    {
        return Auth::user()->email === 'prem.shah8120@gmail.com';
    }

    public function getViewData(): array
    {
        return [
            'structure' => $this->getProjectStructure(),
        ];
    }

    private function getProjectStructure(): array
    {
        return [
            [
                'name' => 'app',
                'type' => 'folder',
                'children' => [
                    [
                        'name' => 'Filament',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'Resources',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => 'AttendanceResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateAttendance', 'type' => 'file'],
                                                    ['name' => 'EditAttendance', 'type' => 'file'],
                                                    ['name' => 'ListAttendance', 'type' => 'file'],
                                                    ['name' => 'ViewAttendance', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'BlogCategoryResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateBlogCategory', 'type' => 'file'],
                                                    ['name' => 'EditBlogCategory', 'type' => 'file'],
                                                    ['name' => 'ListBlogCategory', 'type' => 'file'],
                                                    ['name' => 'ViewBlogCategory', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'CityResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateCity', 'type' => 'file'],
                                                    ['name' => 'EditCity', 'type' => 'file'],
                                                    ['name' => 'ListCity', 'type' => 'file'],
                                                    ['name' => 'ViewCity', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'CountryResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateCountry', 'type' => 'file'],
                                                    ['name' => 'EditCountry', 'type' => 'file'],
                                                    ['name' => 'ListCountry', 'type' => 'file'],
                                                    ['name' => 'ViewCountry', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'DepartmentResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateDepartment', 'type' => 'file'],
                                                    ['name' => 'EditDepartment', 'type' => 'file'],
                                                    ['name' => 'ListDepartment', 'type' => 'file'],
                                                    ['name' => 'ViewDepartment', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'EmployeeResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateEmployee', 'type' => 'file'],
                                                    ['name' => 'EditEmployee', 'type' => 'file'],
                                                    ['name' => 'ListEmployee', 'type' => 'file'],
                                                    ['name' => 'ViewEmployee', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'EmploymentTypeResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateEmploymentType', 'type' => 'file'],
                                                    ['name' => 'EditEmploymentType', 'type' => 'file'],
                                                    ['name' => 'ListEmploymentType', 'type' => 'file'],
                                                    ['name' => 'ViewEmploymentType', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'ProjectResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateProject', 'type' => 'file'],
                                                    ['name' => 'EditProject', 'type' => 'file'],
                                                    ['name' => 'ListProject', 'type' => 'file'],
                                                    ['name' => 'ViewProject', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'SalaryResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateSalary', 'type' => 'file'],
                                                    ['name' => 'EditSalary', 'type' => 'file'],
                                                    ['name' => 'ListSalary', 'type' => 'file'],
                                                    ['name' => 'ViewSalary', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'StateResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateState', 'type' => 'file'],
                                                    ['name' => 'EditState', 'type' => 'file'],
                                                    ['name' => 'ListState', 'type' => 'file'],
                                                    ['name' => 'ViewState', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'UserResource',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'Pages',
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'CreateUser', 'type' => 'file'],
                                                    ['name' => 'EditUser', 'type' => 'file'],
                                                    ['name' => 'ListUser', 'type' => 'file'],
                                                    ['name' => 'ViewUser', 'type' => 'file'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'AttendanceResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'BlogCatgoryResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'CityResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'CountryResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'DepartmentResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'EmployeeResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'EmploymentTypeResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'ProjectResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'SalaryResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'StateResource.php',
                                        'type' => 'file',
                                    ],
                                    [
                                        'name' => 'UserResource.php',
                                        'type' => 'file',
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Widgets',
                                'type' => 'folder',
                                'children' => [
                                    ['name' => 'StatsDashboardOverview.php', 'type' => 'file'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Http',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'Controllers',
                                'type' => 'folder',
                                'children' => [
                                    ['name' => 'Controller.php', 'type' => 'file'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Models',
                        'type' => 'folder',
                        'children' => [
                            ['name' => 'Attendance.php', 'type' => 'file'],
                            ['name' => 'BlogCategory.php', 'type' => 'file'],
                            ['name' => 'City.php', 'type' => 'file'],
                            ['name' => 'Country.php', 'type' => 'file'],
                            ['name' => 'Department.php', 'type' => 'file'],
                            ['name' => 'Employee.php', 'type' => 'file'],
                            ['name' => 'EmployeeExperience.php', 'type' => 'file'],
                            ['name' => 'EmployeeQualification.php', 'type' => 'file'],
                            ['name' => 'EmploymentType.php', 'type' => 'file'],
                            ['name' => 'Project.php', 'type' => 'file'],
                            ['name' => 'ProjectExpense.php', 'type' => 'file'],
                            ['name' => 'ProjectStatus.php', 'type' => 'file'],
                            ['name' => 'Salary.php', 'type' => 'file'],
                            ['name' => 'State.php', 'type' => 'file'],
                            ['name' => 'User.php', 'type' => 'file'],
                        ],
                    ],
                    [
                        'name' => 'Providers',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'Filament',
                                'type' => 'folder',
                                'children' => [
                                    ['name' => 'AdminPanelProvider.php', 'type' => 'file'],
                                ],
                            ],
                            [
                                'name' => 'AppServiceProvider.php',
                                'type' => 'file',
                            ]
                        ],
                    ],
                ],
            ],
            [
                'name' => 'bootstrap',
                'type' => 'folder',
                'children' => [
                    [
                        'name' => 'cache',
                        'type' => 'folder',
                        'children' => [
                            ['name' => 'packages.php', 'type' => 'file'],
                            ['name' => 'services.php', 'type' => 'file'],
                        ],
                    ],
                    [
                        'name' => 'app.php', 
                        'type' => 'file',
                    ],
                    [
                        'name' => 'providers.php',
                        'type' => 'file',
                    ],
                ],
            ],
            [
                'name' => 'config',
                'type' => 'folder',
                'children' => [
                    ['name' => 'app.php', 'type' => 'file'],
                    ['name' => 'auth.php', 'type' => 'file'],
                    ['name' => 'cache.php', 'type' => 'file'],
                    ['name' => 'database.php', 'type' => 'file'],
                    ['name' => 'filesystems.php', 'type' => 'file'],
                    ['name' => 'logging.php', 'type' => 'file'],
                    ['name' => 'mail.php', 'type' => 'file'],
                    ['name' => 'queue.php', 'type' => 'file'],
                    ['name' =>'services.php', 'type' => 'file'],
                    ['name' => 'session.php', 'type' => 'file'],
                ],
            ],
            [
                'name' => 'database',
                'type' => 'folder',
                'children' => [
                    [
                        'name' => 'factories', 
                        'type' => 'folder',
                        'children' => [
                            ['name' => 'UserFactory.php', 'type' => 'file'],
                        ],
                    ],
                    [
                        'name' => 'migrations', 
                        'type' => 'folder',
                        'children' => [
                            ['name' => '0001_01_01_000000_create_users_table.php', 'type' => 'file'],
                            ['name' => '0001_01_01_000001_create_cache_table.php', 'type' => 'file'],
                            ['name' => '0001_01_01_000002_create_jobs_table.php', 'type' => 'file'],
                            ['name' => '2024_06_28_101000_create_countries_table.php', 'type' => 'file'],
                            ['name' => '2024_06_28_101023_create_states_table.php', 'type' => 'file'],
                            ['name' => '2024_06_28_101034_create_cities_table.php', 'type' => 'file'],
                            ['name' => '2024_06_28_101244_create_blog_categories_table.php', 'type' => 'file'],
                            ['name' => '2024_06_28_101245_create_departments_table.php', 'type' => 'file'],
                            ['name' => '2024_06_28_101434_create_employment_types_table.php', 'type' => 'file'],
                            ['name' => '2024_06_28_101539_create_employees_table.php', 'type' => 'file'],
                            ['name' => '2024_07_01_073753_create_attendances_table.php', 'type' => 'file'],
                            ['name' => '2024_07_01_114237_create_projects_table.php', 'type' => 'file'],
                            ['name' => '2024_07_02_080408_create_salaries_table.php', 'type' => 'file'],
                            ['name' => '2024_07_02_085551_add_unique_constraint_to_salaries_table.php', 'type' => 'file'],

                        ],
                    ],
                    [
                        'name' => 'seeders', 
                        'type' => 'folder',
                        'children' => [
                            ['name' => 'BlogCategorySeeder.php', 'type' => 'file'],
                            ['name' => 'CitySeeder.php', 'type' => 'file'],
                            ['name' => 'CountrySeeder.php', 'type' => 'file'],
                            ['name' => 'DatabaseSeeder.php', 'type' => 'file'],
                            ['name' => 'DepartmentSeeder.php', 'type' => 'file'],
                            ['name' => 'EmploymentTypeSeeder.php', 'type' => 'file'],
                            ['name' => 'StateSeeder.php', 'type' => 'file'],
                        ],
                    ],
                    ['name' => '.gitignore', 'type' => 'file'],
                    ['name' => 'databsse.sqlite', 'type' => 'file'],
                ],
            ],
            [
                'name' => 'public',
                'type' => 'folder',
                'children' => [
                    [
                        'name' => 'css', 
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'filament',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => 'filament', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'app.css', 
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'forms', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'forms.css', 
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'support', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'support.css', 
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'js', 
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'filament',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => 'filament', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'app.js', 
                                                'type' => 'file',
                                            ],
                                            [
                                                'name' => 'echo.js', 
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'forms', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'components', 
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'color-picker.js', 'type' => 'file',],
                                                    ['name' => 'date-time-picker.js', 'type' => 'file',],
                                                    ['name' => 'file-upload.js', 'type' => 'file',],
                                                    ['name' => 'key-value.js', 'type' => 'file',],
                                                    ['name' => 'markdown-editor.js', 'type' => 'file',],
                                                    ['name' => 'rich-editor.js', 'type' => 'file',],
                                                    ['name' => 'select.js', 'type' => 'file',],
                                                    ['name' => 'tags-input.js', 'type' => 'file',],
                                                    ['name' => 'textarea.js', 'type' => 'file',],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'notifications', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'notifications.js', 
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'support', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'async-alpine.js', 
                                                'type' => 'file',
                                            ],
                                            [
                                                'name' => 'support.js', 
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'tables', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'components', 
                                                'type' => 'folder',
                                                'children' => [
                                                    ['name' => 'table.js', 'type' => 'file',],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'widgets', 
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'components', 
                                                'type' => 'folder',
                                                'children' => [
                                                    [
                                                        'name' => 'stats-overview', 
                                                        'type' => 'folder',
                                                        'children' => [
                                                            [
                                                                'name' => 'stat', 
                                                                'type' => 'folder',
                                                                'children' => [
                                                                    [
                                                                        'name' => 'chart.js', 
                                                                        'type' => 'file',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    ['name' => 'chart.js', 'type' => 'file',],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    ['name' => '.htaccess', 'type' => 'file'],
                    ['name' => 'favicon.ico', 'type' => 'file'],
                    ['name' => 'index.php', 'type' => 'file'],
                    ['name' => 'robots.txt', 'type' => 'file'],
                ],
            ],
            [
                'name' => 'resources',
                'type' => 'folder',
                'children' => [
                    [
                        'name' => 'css',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'app.css',
                                'type' => 'file',
                            ],
                        ],
                    ],
                    [
                        'name' => 'js',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'app.js',
                                'type' => 'file',
                            ],
                            [
                                'name' => 'bootstrap.js',
                                'type' => 'file',
                            ],
                        ],
                    ],
                    [
                        'name' => 'views',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'filament',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => 'pages',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'partials',
                                                'type' => 'folder',
                                                'children' => [
                                                    [
                                                        'name' => 'directory-tree.blade.php',
                                                        'type' => 'file',
                                                    ],
                                                ],
                                            ],
                                            [
                                                'name' => 'project-structure.blade.php',
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => 'layouts',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => 'app.blade.php',
                                        'type' => 'file',
                                    ],
                                ],
                            ],
                            [
                                'name' => 'tables',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => 'columns',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => 'attendance-ratio.blade.php',
                                                'type' => 'file',
                                            ],
                                            [
                                                'name' => 'payable-amount-link.blade.php',
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],
            [
                'name' => 'routes',
                'type' => 'folder',
                'children' => [
                    [
                        'name' => 'console.php',
                        'type' => 'file',
                    ],
                    [
                        'name' => 'web.php',
                        'type' => 'file',
                    ],
                ],
            ],
            [
                'name' => 'storage',
                'type' => 'folder',
                'children' => [
                    [
                        'name' => 'app',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'public',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => '.gitignore',
                                        'type' => 'file',
                                    ],
                                ],
                            ],
                            [
                                'name' => '.gitignore',
                                'type' => 'file',
                            ]
                        ],
                    ],
                    [
                        'name' => 'framework',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'cache',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => 'data',
                                        'type' => 'folder',
                                        'children' => [
                                            [
                                                'name' => '.gitignore',
                                                'type' => 'file',
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => '.gitignore',
                                        'type' => 'file',
                                    ],
                                ],
                            ],
                            [
                                'name' => 'sessions',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => '.gitignore',
                                        'type' => 'file',
                                    ],
                                ],
                            ],
                            [
                                'name' => 'testing',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => '.gitignore',
                                        'type' => 'file',
                                    ],
                                ],
                            ],
                            [
                                'name' => 'views',
                                'type' => 'folder',
                                'children' => [
                                    [
                                        'name' => '.gitignore',
                                        'type' => 'file',
                                    ],
                                ],
                            ],
                            [
                                'name' => '.gitignore',
                                'type' => 'file',
                            ],
                        ],
                    ],
                    [
                        'name' => 'logs',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => '.gitignore',
                                'type' => 'file',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'tests',
                'type' => 'folder',
                'children' => [
                    [
                        'name' => 'Feature',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'ExampleTest.php',
                                'type' => 'file',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Unit',
                        'type' => 'folder',
                        'children' => [
                            [
                                'name' => 'ExampleTest.php',
                                'type' => 'file',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Pest',
                        'type' => 'file',
                    ],
                    [
                        'name' => 'TestCase',
                        'type' => 'file',
                    ],
                ],
            ],
            [
                'name' => 'vendor',
                'type' => 'folder',
            ],
            [
                'name' => '.editorconfig',
                'type' => 'file',
            ],
            [
                'name' => '.env',
                'type' => 'file',
            ],
            [
                'name' => '.env.example',
                'type' => 'file',
            ],
            [
                'name' => '.gitattributes',
                'type' => 'file',
            ],
            [
                'name' => '.gitignore',
                'type' => 'file',
            ],
            [
                'name' => 'artisan',
                'type' => 'file',
            ],
            [
                'name' => 'composer.json',
                'type' => 'file',
            ],
            [
                'name' => 'composer.lock',
                'type' => 'file',
            ],
            [
                'name' => 'package.json',
                'type' => 'file',
            ],
            [
                'name' => 'phpunit.xml',
                'type' => 'file',
            ],
            [
                'name' => 'readme.md',
                'type' => 'file',
            ],
            [
                'name' => 'vite.config.js',
                'type' => 'file',
            ],
        ];
    }

    // private function getResourcesStructure(): array
    // {
    //     $resources = [
    //         'Attendance', 'BlogCatgory', 'City', 'Country', 'State', 'Department',
    //         'Employee', 'EmploymentType', 'Project', 'Salary', 'User'
    //     ];

    //     return array_map(function ($resource) {
    //         return [
    //             'name' => "{$resource}Resource",
    //             'type' => 'folder',
    //             'children' => [
    //                 [
    //                     'name' => 'Pages',
    //                     'type' => 'folder',
    //                     'children' => [
    //                         ['name' => "Create{$resource}Resource.php", 'type' => 'file'],
    //                         ['name' => "Edit{$resource}Resource.php", 'type' => 'file'],
    //                         ['name' => "List{$resource}Resource.php", 'type' => 'file'],
    //                         ['name' => "View{$resource}Resource.php", 'type' => 'file'],
    //                     ],
    //                 ],
    //                 ['name' => "{$resource}Resource.php", 'type' => 'file'],
    //             ],
    //         ];
    //     }, $resources);
    // }

    // private function getModelsStructure(): array
    // {
    //     $models = [
    //         'Attendance', 'BlogCategory', 'City', 'Country', 'State', 'Department',
    //         'EmployeeExperience', 'EmployeeQualification', 'EmploymentType',
    //         'Project', 'ProjectExpense', 'ProjectStatus', 'Salary', 'User'
    //     ];

    //     return array_map(function ($model) {
    //         return ['name' => "{$model}.php", 'type' => 'file'];
    //     }, $models);
    // }
}
