<?php

namespace Morrislaptop\Caster\Commands;

use Illuminate\Console\Command;

class CasterCommand extends Command
{
    public $signature = 'laravel-castable-object';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
