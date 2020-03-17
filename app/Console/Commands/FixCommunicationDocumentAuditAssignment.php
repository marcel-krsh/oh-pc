<?php

namespace App\Console\Commands;

use App\Models\DocumentAudit;
use Illuminate\Console\Command;
use App\Models\CommunicationDocument;

class FixCommunicationDocumentAuditAssignment extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fix:document_audit_relation';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Some of the documents uploaded through communications and documents in CRR reports have no audit_id assigned to them. This command would fix that by assigning audit_id to those documents.';

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
		$this->cds = '';
		$total = CommunicationDocument::whereHas('communication', function ($query) {
			$query->whereNotNull('audit_id');
		})->count();
		$progressBar = $this->output->createProgressBar($total);
		$progressBar->setProgressCharacter("\xf0\x9f\x8c\x80");
		$chunk = 50;
		$communication_documents = CommunicationDocument::whereHas('communication', function ($query) {
			$query->whereNotNull('audit_id');
		})->chunk($chunk, function ($cds) use ($progressBar, $chunk) {
			$progressBar->advance($chunk);
			foreach ($cds as $key => $cd) {
				$check_audit = DocumentAudit::where('audit_id', $cd->communication->audit_id)->where('document_id', $cd->document_id)->first();
				if (!$check_audit) {
					$doc_audit = new DocumentAudit;
					$doc_audit->audit_id = $cd->communication->audit_id;
					$doc_audit->document_id = $cd->document_id;
					$doc_audit->save();
				}
			}
		});
		$this->line(PHP_EOL . 'Completed' . PHP_EOL);
	}
}
