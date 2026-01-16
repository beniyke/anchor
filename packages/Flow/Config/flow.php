<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * flow
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Columns
    |--------------------------------------------------------------------------
    |
    | These columns will be automatically created when a new project is created.
    | You can customize the names, types, and initial order here.
    |
    */
    'default_columns' => [
        ['name' => 'To Do', 'type' => 'todo', 'order' => 0],
        ['name' => 'In Progress', 'type' => 'doing', 'order' => 1],
        ['name' => 'Done', 'type' => 'done', 'order' => 2],
    ],

    /*
    |--------------------------------------------------------------------------
    | Task Priorities
    |--------------------------------------------------------------------------
    |
    | Define the priority levels available for tasks.
    |
    */
    'priorities' => ['low', 'medium', 'high', 'urgent'],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | Settings for task attachments and project files.
    |
    */
    'storage' => [
        'disk' => 'local',
        'path' => 'flow/attachments',
    ],
    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Define the base URLs for Flow entities.
    |
    */
    'urls' => [
        'task' => '/flow/tasks',
        'project' => '/flow/projects',
    ],
];
