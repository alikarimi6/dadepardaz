<?php

namespace App\Console\Commands\States;

use App\Models\State;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class GenerateStateClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-state-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $states = State::all();
        $namespace = config('model-states.namespace');
        ;
        $path = config('model-states.default_dir');

        if (!File::exists($path)) {
            File::makeDirectory($path);
        }

        foreach ($states as $state) {
            $className = str($state->name)->studly()->ucfirst();
            $filePath = "$path/$className.php";

            if (!File::exists($filePath)) {
                File::put($filePath, $this->generateClassContent($className, $namespace , $state->name));
                $this->info("Created class: $className");
            } else {
                $this->warn("Class already exists: $className");
            }

            // Update database if not already set
            $fullClassName = "$namespace\\$className";
            if ($state->class !== $fullClassName) {
                $state->class = $fullClassName;
                $state->save();
                $this->info("Updated state DB class: $fullClassName");
            }
        }

        $this->info('✅ All state classes generated successfully.');
    }
    protected function generateClassContent($className, $namespace , $name): string
    {
        return <<<PHP
            <?php

                namespace $namespace;
                use $namespace\StateStatus;
                use Spatie\ModelStates\State;

                class $className extends StateStatus
                {
                    public static  \$name = "$name";
                }
            PHP;
    }

}
