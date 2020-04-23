<?php

namespace App\Exports;

use \Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
	$sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

Sheet::macro('freezePane', function (Sheet $sheet, $pane) {
	$sheet->getDelegate()->getActiveSheet()->freezePane($pane);
});

class ContactsToProjectExcel implements FromView, WithEvents
{

	protected $cachedAudits;
	protected $projects;

	public function __construct($cachedAudits, $projects)
	{
		$this->cachedAudits = $cachedAudits;
		$this->projects = $projects;
	}

	/**
	 * @return array
	 */
	public function registerEvents(): array
	{
		return [
			AfterSheet::class => function (AfterSheet $event) {
				$highest_row = $event->sheet->getHighestRow();

				// $event->sheet->setCellValue('E' . (2), '=CONCATENATE(COUNTIF(E3:E' . $event->sheet->getHighestRow() . ',"*"), "asa")');

				// $event->sheet->setCellValue('E2'), '=SUM(E3:E' . $event->sheet->getHighestRow() . ') PROJECTS');

				// $event->sheet->styleCells(
				// 	'A1:' . $event->sheet->getDelegate()->getHighestColumn() . '1',
				// 	[
				// 		'font' => [
				// 			'size' => 16,
				// 			'bold' => true,
				// 			'color' => ['rgb' => 'ffffff'],
				// 		],
				// 		'fill' => [
				// 			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				// 			'startColor' => [
				// 				'rgb' => '0f233e',
				// 			],
				// 		],
				// 	],
				// );
				// $event->sheet->styleCells(
				// 	'A2:' . $event->sheet->getDelegate()->getHighestColumn() . '2',
				// 	[
				// 		'font' => [
				// 			'size' => 12,
				// 			'bold' => true,
				// 			'color' => ['rgb' => 'ffffff'],
				// 		],
				// 		'fill' => [
				// 			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				// 			'startColor' => [
				// 				'rgb' => '5273bc',
				// 			],
				// 		],
				// 	]
				// );
				// Apply array of styles to B2:G8 cell range
				$styleArray = [
					'borders' => [
						'outline' => [
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
							'color' => ['argb' => 'A8A8A8'],
						],
					],
				];
				// $event->sheet->getStyle('A1:' . $event->sheet->getDelegate()->getHighestColumn() . '1')->getFill()
				// 	->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				// 	->getStartColor()->setARGB('0f233e');
				$event->sheet->freezePane('A2', 'A2');
				$event->sheet->getDelegate()->getStyle('A2:V1')->applyFromArray($styleArray);
				$event->sheet->styleCells(
					'A1:V1000',
					[
						'alignment' => [
							'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
						],
					]
				);

				// Set first row to height 20
				$event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(30);
				// $event->sheet->setCellValue('N2', '=SUM(N2:N' . $event->sheet->getHighestRow() . ')');
				// $event->sheet->setCellValue('N2', '=CONCAT(SUM(N3:N' . $highest_row . ')," HOURS")');

				// $event->sheet->getColumnDimension('A:V')->setAutoSize(true);
				for ($i = 1; $i <= 9; $i++) {
					$column = Coordinate::stringFromColumnIndex($i);
					$event->sheet->getColumnDimension($column)->setAutoSize(true);
				}

				// Set A1:D4 range to wrap text in cells
				// $event->sheet->getDelegate()->getStyle('A1:V400')->getAlignment()->setWrapText(true);
			},
		];
	}

/**
 * @return \Illuminate\Support\Collection
 */
	public function view(): View
	{
		$cachedAudits = $this->cachedAudits;
		$projects = $this->projects;
		return view('layouts.stats.project_contacts', compact('cachedAudits', 'projects'));
	}
}
