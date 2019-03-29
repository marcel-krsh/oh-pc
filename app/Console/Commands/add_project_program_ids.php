<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UnitProgram;
use App\Models\ProjectProgram;

class add_project_program_ids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:unitProgram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing ids on unit programs table for audits run already';

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
        // GET THE UNIT PROGRAMS WITH A NULL FIELD
        $ups = UnitProgram::whereNull('project_program_id')->get();

        forEach($ups as $up){
            //find matching project program record
            $pp = ProjectProgram::where('project_id',$up->project_id)->where('program_id',$up->program_id)->first();

            if($pp){
                $up->update(['project_program_id' => $pp->id, 'project_program_key' => $pp->project_program_key]);
                $this->line(PHP_EOL.'Updated Unit Program ID'.$up->id);
            }else{
                 $this->error(PHP_EOL.'Could Not Find Matching Project Program for Unit Program ID'.$up->id);
            }
        }

    }
}
