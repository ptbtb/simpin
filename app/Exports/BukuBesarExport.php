<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class BukuBesarExport implements FromView, ShouldAutoSize, WithEvents

{
	protected $data;

	public function __construct($data)
	{
		$this->data = $data;
	}
	public function view(): View
	{
		return view('buku_besar.excel', $this->data);
	}
	// return view('buku_besar.excel', [
	//     'bukuBesars' => $bukuBesars
	// ]);

	public function registerEvents(): array
    {
        return [
			BeforeSheet::class => function (BeforeSheet $event) {

				$event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
			},
        ];
    }
}
