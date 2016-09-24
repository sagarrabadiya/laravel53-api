<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\Company::class, function (Faker\Generator $faker) {
    return [
        'name'  =>  $faker->name,
        'domain' => $faker->domainWord,
        'settings'  =>  [
            'email' =>  $faker->companyEmail,
            'logo'  =>  $faker->imageUrl(200, 100)
        ]
    ];
});

$factory->define(App\Models\Project::class, function (Faker\Generator $faker) {
   return [
       'name'  =>  $faker->name,
       'active' => $faker->boolean,
       'settings'   =>  [
           'logo'   =>  $faker->imageUrl(100, 100)
       ]
   ];
});

$factory->define(App\Models\Role::class, function (Faker\Generator $faker) {
   return [
       'name'    =>    $faker->name,
       'board_read'    =>    $faker->boolean(),
       'board_write'    =>    $faker->boolean(),
       'milestone_read'    =>    $faker->boolean(),
       'milestone_write'    =>    $faker->boolean(),
       'ticket_read'    =>    $faker->boolean(),
       'ticket_write'    =>    $faker->boolean(),
       'note_read'    =>    $faker->boolean(),
       'note_write'    =>    $faker->boolean(),
       'team_read'    =>    $faker->boolean(),
       'team_write'    =>    $faker->boolean()
   ];
});

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
   return [
        'username'  =>  $faker->userName,
        'password'   =>  '123456',
        'designation'  =>  $faker->randomKey(['admin'=>'admin', 'manager'=>'manager', 'developer'=>'developer', 'designer'=>'designer']),
        'firstname' =>  $faker->firstName,
        'lastname'   =>  $faker->lastName,
        'email'  =>  $faker->email,
        'avatar'  =>  $faker->imageUrl(50, 50)
   ];
});


$factory->define(App\Models\BoardItem::class, function (Faker\Generator $faker) {
    return [
        'title'  =>  $faker->sentence(20),
        'description'   =>  $faker->paragraph(20)
    ] ;
});

$factory->define(App\Models\Milestone::class, function (Faker\Generator $faker) {
    return [
        'title'  =>  $faker->sentence(10),
        'description'    =>  $faker->paragraph(5),
        'deadline'   =>  $faker->dateTime->format('Y-m-d H:i:s'),
        'status' =>  $faker->randomElement(['created', 'active', 'archived', 'completed']),
        'type'   =>  $faker->randomElement(['feature', 'bug fixes', 'release'])
    ];
});

$factory->define(App\Models\Note::class, function (Faker\Generator $faker) {
    return [
        'title'    =>  $faker->sentence(5),
        'description'    => $faker->paragraph(5)
    ];
});

$factory->define(App\Models\NotePage::class, function (Faker\Generator $faker) {
    return [
        'title'    =>  $faker->sentence(5),
        'description'    => $faker->paragraph(5)
    ];
});

$factory->define(App\Models\Ticket::class, function (Faker\Generator $faker) {
    return [
        'title' =>  $faker->sentence(5),
        'description'   =>  $faker->paragraph(5),
        'sequence_id'   =>  $faker->numberBetween(0, 100),
        'status' =>  'new',
        'priority'   =>  $faker->randomElement(['highest', 'high', 'normal', 'low', 'lowest'])
    ];
});

$factory->define(App\Models\Comment::class, function (Faker\Generator $faker) {
    return [
        'text'   =>  $faker->paragraph(3)
    ];
});
