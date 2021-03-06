<?php

use Cartalyst\Extensions\ExtensionInterface;
use Illuminate\Foundation\Application;

return [

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| This is your extension name and it is only required for
	| presentational purposes.
	|
	*/

	'name' => 'eChineseLearning',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| This is your extension unique identifier and should not be changed as
	| it will be recognized as a new extension.
	|
	| Ideally, this should match the folder structure within the extensions
	| folder, but this is completely optional.
	|
	*/

	'slug' => 'kitbs/echineselearning',

	/*
	|--------------------------------------------------------------------------
	| Author
	|--------------------------------------------------------------------------
	|
	| Because everybody deserves credit for their work, right?
	|
	*/

	'author' => 'KitBS',

	/*
	|--------------------------------------------------------------------------
	| Description
	|--------------------------------------------------------------------------
	|
	| One or two sentences describing the extension for users to view when
	| they are installing the extension.
	|
	*/

	'description' => 'eChineseLearning Calendar',

	/*
	|--------------------------------------------------------------------------
	| Version
	|--------------------------------------------------------------------------
	|
	| Version should be a string that can be used with version_compare().
	| This is how the extensions versions are compared.
	|
	*/

	'version' => '0.1.7',

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| List here all the extensions that this extension requires to work.
	| This is used in conjunction with composer, so you should put the
	| same extension dependencies on your main composer.json require
	| key, so that they get resolved using composer, however you
	| can use without composer, at which point you'll have to
	| ensure that the required extensions are available.
	|
	*/

	'require' => [
		'platform/admin'
	],

	/*
	|--------------------------------------------------------------------------
	| Autoload Logic
	|--------------------------------------------------------------------------
	|
	| You can define here your extension autoloading logic, it may either
	| be 'composer', 'platform' or a 'Closure'.
	|
	| If composer is defined, your composer.json file specifies the autoloading
	| logic.
	|
	| If platform is defined, your extension receives convetion autoloading
	| based on the Platform standards.
	|
	| If a Closure is defined, it should take two parameters as defined
	| bellow:
	|
	|	object \Composer\Autoload\ClassLoader      $loader
	|	object \Illuminate\Foundation\Application  $app
	|
	| Supported: "composer", "platform", "Closure"
	|
	*/

	'autoload' => 'composer',

	/*
	|--------------------------------------------------------------------------
	| Register Callback
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is registered. This can do
	| all the needed custom logic upon registering.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'register' => function(ExtensionInterface $extension, Application $app)
	{
		$LessonRepository = 'Kitbs\EChineseLearning\Repositories\LessonRepositoryInterface';

		if ( ! $app->bound($LessonRepository))
		{
			$app->bind($LessonRepository, function($app)
			{
				$model = get_class($app['Kitbs\EChineseLearning\Models\Lesson']);

				return new Kitbs\EChineseLearning\Repositories\DbLessonRepository($model, $app['events']);
			});
		}

		$SuspensionRepository = 'Kitbs\EChineseLearning\Repositories\SuspensionRepositoryInterface';

		if ( ! $app->bound($SuspensionRepository))
		{
			$app->bind($SuspensionRepository, function($app)
			{
				$model = get_class($app['Kitbs\EChineseLearning\Models\Suspension']);

				return new Kitbs\EChineseLearning\Repositories\DbSuspensionRepository($model, $app['events']);
			});
		}
	},

	/*
	|--------------------------------------------------------------------------
	| Boot Callback
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is booted. This can do
	| all the needed custom logic upon booting.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'boot' => function(ExtensionInterface $extension, Application $app)
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Routes
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| any custom routing logic here.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'routes' => function(ExtensionInterface $extension, Application $app)
	{
		Route::group(['namespace' => 'Kitbs\EChineseLearning\Controllers'], function()
		{
			Route::group(['prefix' => admin_uri().'/echineselearning/lessons', 'namespace' => 'Admin'], function()
			{
				Route::get('/', 'LessonsController@index');
				Route::post('/', 'LessonsController@executeAction');
				Route::get('grid', 'LessonsController@grid');
				Route::get('create', 'LessonsController@create');
				Route::post('create', 'LessonsController@store');
				Route::get('{id}/edit', 'LessonsController@edit');
				Route::post('{id}/edit', 'LessonsController@update');
				Route::get('{id}/delete', 'LessonsController@delete');
			});

			Route::group(['prefix' => admin_uri().'/echineselearning/suspensions', 'namespace' => 'Admin'], function()
			{
				Route::get('/', 'SuspensionsController@index');
				Route::post('/', 'SuspensionsController@executeAction');
				Route::get('grid', 'SuspensionsController@grid');
				Route::get('create', 'SuspensionsController@create');
				Route::post('create', 'SuspensionsController@store');
				Route::get('{id}/edit', 'SuspensionsController@edit');
				Route::post('{id}/edit', 'SuspensionsController@update');
				Route::get('{id}/delete', 'SuspensionsController@delete');
			});

			Route::group(['prefix' => admin_uri().'/echineselearning/preview', 'namespace' => 'Admin'], function()
			{
				Route::get('/', 'CalendarController@index');
				Route::get('refresh', 'CalendarController@refresh');

			});

			Route::group(['namespace' => 'Frontend'], function()
			{
				Route::post('echineselearning', 'EmailController@index');
				Route::get('echineselearning.ics', 'CalendarController@index');
			});

		});
	},

	/*
	|--------------------------------------------------------------------------
	| Database Seeds
	|--------------------------------------------------------------------------
	|
	| Platform provides a very simple way to seed your database with test
	| data using seed classes. All seed classes should be stored on the
	| `database/seeds` directory within your extension folder.
	|
	| The order you register your seed classes on the array below
	| matters, as they will be ran in the exact same order.
	|
	| The seeds array should follow the following structure:
	|
	|	Vendor\Namespace\Database\Seeds\FooSeeder
	|	Vendor\Namespace\Database\Seeds\BarSeeder
	|
	*/

	'seeds' => [

		'Kitbs\EChineseLearning\Database\Seeds\LessonsTableSeeder',
		'Kitbs\EChineseLearning\Database\Seeds\SuspensionsTableSeeder',

	],

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| List of permissions this extension has. These are shown in the user
	| management area to build a graphical interface where permissions
	| may be selected.
	|
	| The admin controllers state that permissions should follow the following
	| structure:
	|
	|    Vendor\Namespace\Controller@method
	|
	| For example:
	|
	|    Platform\Users\Controllers\Admin\UsersController@index
	|
	| These are automatically generated for controller routes however you are
	| free to add your own permissions and check against them at any time.
	|
	| When writing permissions, if you put a 'key' => 'value' pair, the 'value'
	| will be the label for the permission which is displayed when editing
	| permissions.
	|
	*/

	'permissions' => function()
	{
		return [

		];
	},

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| all your custom widgets here. Of course, Platform will guess the
	| widget class for you, this is just for custom widgets or if you
	| do not wish to make a new class for a very small widget.
	|
	*/

	'widgets' => function()
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	|
	| Register any settings for your extension. You can also configure
	| the namespace and group that a setting belongs to.
	|
	*/

	'settings' => function()
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Menus
	|--------------------------------------------------------------------------
	|
	| You may specify the default various menu hierarchy for your extension.
	| You can provide a recursive array of menu children and their children.
	| These will be created upon installation, synchronized upon upgrading
	| and removed upon uninstallation.
	|
	| Menu children are automatically put at the end of the menu for extensions
	| installed through the Operations extension.
	|
	| The default order (for extensions installed initially) can be
	| found by editing app/config/platform.php.
	|
	*/

	'menus' => [

		'admin' => [
			[
				'slug' => 'admin-kitbs-echineselearning',
				'name' => 'eChineseLearning',
				'class' => 'fa fa-graduation-cap',
				'uri' => 'echineselearning',
				'children' => [
					[
						'slug' => 'admin-kitbs-echineselearning-lesson',
						'name' => 'Lessons',
						'class' => 'fa fa-calendar',
						'uri' => 'echineselearning/lessons',
					],
					[
						'slug' => 'admin-kitbs-echineselearning-suspension',
						'name' => 'Suspensions',
						'class' => 'fa fa-calendar-o',
						'uri' => 'echineselearning/suspensions',
					],
					[
						'slug' => 'admin-kitbs-echineselearning-ical',
						'name' => 'iCalendar Preview',
						'class' => 'fa fa-file-code-o',
						'uri' => 'echineselearning/preview',
					],
				],
			],
		],
		'main' => [
			
		],
	],

];
