<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\Finding;
use App\Models\Document;
use Illuminate\Console\Command;

class MakeDocumentRelationships extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fix:update_document_related_ids';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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
		$total = Document::get()->count();
		$progressBar = $this->output->createProgressBar($total);
		$progressBar->setProgressCharacter("\xf0\x9f\x8c\x80");
		$chunk = 20;
		$progress = $chunk;
		$communication_documents = Document::with('communication_details')->chunk($chunk, function ($documents) use ($progressBar, $progress) {
			$progressBar->advance($progress);
			foreach ($documents as $key => $doc) {
				$all_findings = [];
				$all_units = [];
				$all_buildings = [];
				$all_sites = [];
				$exist = false;
				if (!is_null($doc->finding_ids)) {
					$exist = true;
					//dd($findingsToInsert, $request->comment, $request->categories, $request->buValue, $request->audit_id);
					if (!is_array($doc->finding_ids)) {
						$findingIds = json_decode($doc->finding_ids, true);
					} else {
						$findingIds = $doc->finding_ids;
					}
					$findingDetails = Finding::whereIn('id', $findingIds)->get();
					// get unit ids from findings
					$unitIds = $findingDetails->pluck('unit_id')->unique()->filter()->toArray();
					// get the building ids of the units
					$unitBuildingIds = Unit::whereIn('id', $unitIds)->pluck('building_id')->unique()->filter();
					// get the building ids from the findings
					$findingBuildingIds = $findingDetails->pluck('building_id')->unique()->filter();
					// merge them togeter merging duplicates
					$buildingIds = $unitBuildingIds->merge($findingBuildingIds)->unique()->toArray();
					// get site ids from findings
					$siteIds = $findingDetails->where('site', 1)->pluck('amenity_id')->unique()->toArray();
					$unitIds = array_values($unitIds);

					if (!empty($findingIds)) {
						$all_findings = array_merge($all_findings, $findingIds);
						// $document->finding_ids = json_encode($findingIds, true);
					}
					if (!empty($siteIds)) {
						$all_sites = array_merge($all_sites, $siteIds);
						// $document->site_ids = json_encode($siteIds, true);
					}
					if (!empty($buildingIds)) {
						$all_buildings = array_merge($all_buildings, $buildingIds);
						// $document->building_ids = json_encode($buildingIds, true);
					}
					if (!empty($unitIds)) {
						$all_units = array_merge($all_units, $unitIds);
						// $document->unit_ids = json_encode($unitIds, true);
					}
				}
				if (count($doc->communication_details) > 0) {
					// $all_findings = !is_null($doc->finding_ids) ? array_merge($all_findings, json_decode($doc->finding_ids)) : $all_findings;
					// $all_units = !is_null($doc->unit_ids) ? array_merge($all_units, $doc->unit_ids) : $all_units;
					// $all_buildings = !is_null($doc->building_ids) ? array_merge($all_buildings, $doc->building_ids) : $all_buildings;
					// $all_sites = !is_null($doc->site_ids) ? array_merge($all_sites, $doc->site_ids) : $all_sites;

					foreach ($doc->communication_details as $cd) {
						if (!is_null($cd->finding_ids)) {
							$exist = true;

							$findingIds = json_decode($cd->finding_ids);
							//dd($findingsToInsert, $request->comment, $request->categories, $request->buValue, $request->audit_id);
							$findingDetails = Finding::whereIn('id', $findingIds)->get();
							// get unit ids from findings
							$unitIds = $findingDetails->pluck('unit_id')->unique()->filter()->toArray();
							// get the building ids of the units
							$unitBuildingIds = Unit::whereIn('id', $unitIds)->pluck('building_id')->unique()->filter();
							// get the building ids from the findings
							$findingBuildingIds = $findingDetails->pluck('building_id')->unique()->filter();
							// merge them togeter merging duplicates
							$buildingIds = $unitBuildingIds->merge($findingBuildingIds)->unique()->toArray();
							// get site ids from findings
							$siteIds = $findingDetails->where('site', 1)->pluck('amenity_id')->unique()->toArray();
							$unitIds = array_values($unitIds);
							if (!empty($findingIds)) {
								$all_findings = array_merge($all_findings, $findingIds);
								// $document->finding_ids = json_encode($findingIds, true);
							}
							if (!empty($siteIds)) {
								$all_sites = array_merge($all_sites, $siteIds);
								// $document->site_ids = json_encode($siteIds, true);
							}
							if (!empty($buildingIds)) {
								$all_buildings = array_merge($all_buildings, $buildingIds);
								// $document->building_ids = json_encode($buildingIds, true);
							}
							if (!empty($unitIds)) {
								$all_units = array_merge($all_units, $unitIds);
								// $document->unit_ids = json_encode($unitIds, true);
							}

							// $all_sites = array_map('strval', $all_sites);
							// $all_buildings = array_map('strval', $all_buildings);
							// $all_units = array_map('strval', $all_units);
						}
					}
				}
				if ($exist) {
					$all_sites = array_map('strval', $all_sites);
					$all_buildings = array_map('strval', $all_buildings);
					$all_units = array_map('strval', $all_units);

					if (!empty($all_findings)) {
						$doc->finding_ids = (array_unique($all_findings));
					}
					if (!empty($all_sites)) {
						$doc->site_ids = (array_unique($all_sites));
					}
					if (!empty($all_buildings)) {
						$doc->building_ids = (array_unique($all_buildings));
					}
					if (!empty($all_units)) {
						$doc->unit_ids = (array_unique($all_units));
					}
					$doc->save();
				}
			}
		});
		$this->line(PHP_EOL . 'Completed' . PHP_EOL);
		//Get all the docs with associated communications and findings
		//Based on the communications get finding_ids and other ids from this finding_ids
		//Update those ids on documents
	}
}
