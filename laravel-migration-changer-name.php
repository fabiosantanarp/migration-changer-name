<?php

namespace App\Console\Commands;

use DirectoryIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class replaceNameMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration-changer-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migration changer name';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $path = 'database/migrations';
        $dir = new DirectoryIterator($path);
        $counter = 10;

        foreach ($dir as $item) {
            $files[] = $item->getFilename();
        }
        sort($files);

        foreach ($files as $fileName) {
            if (in_array($fileName, [".", ".."]) ) continue;            
            $customFileName = Str::studly(implode('_', array_slice(explode('_', $fileName), 0)));
            
            $customFileName = str_replace('-', '', $customFileName); 
            $customFileName = str_replace('_', '', $customFileName); 
            $customFileName = preg_replace('/[0-9]/', '', $customFileName);
            $customFileName = str_replace('.php', '', $customFileName); 

            $fileNameWithPath = $path."/".$fileName;
            $fileContent=file_get_contents($fileNameWithPath);

            $str = str_replace(
                "return new class",
                "class " . $customFileName,
                $fileContent
            );

            $str = str_replace(
                "table->id()",
                "table->increments('id')",
                $str
            );

            $str = str_replace( "};", "}", $str);
            
            file_put_contents($fileNameWithPath, $str);
            $counter++;
            rename($fileNameWithPath, $path."/".date("Y_m_d")."_00000".$counter."_".Str::snake($customFileName, "_").".php");

        }
    }
}
