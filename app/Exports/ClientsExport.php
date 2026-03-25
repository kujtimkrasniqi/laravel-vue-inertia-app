<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    public function __construct(
        private readonly ?string $filter = null,
    ) {}

    // ── Query ─────────────────────────────────────────────────────────────────

    public function query()
    {
        $today    = Carbon::today();
        $weekEnd  = Carbon::today()->endOfWeek();
        $monthEnd = Carbon::today()->endOfMonth();

        return Client::query()
            ->when($this->filter === 'active',     fn ($q) => $q->where('expiry_date', '>=', $today))
            ->when($this->filter === 'expired',    fn ($q) => $q->where('expiry_date', '<',  $today))
            ->when($this->filter === 'this_week',  fn ($q) => $q->whereBetween('expiry_date', [$today, $weekEnd]))
            ->when($this->filter === 'this_month', fn ($q) => $q->whereBetween('expiry_date', [$today, $monthEnd]))
            ->orderBy('name');
    }

    // ── Column headings ───────────────────────────────────────────────────────

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Phone',
            'Email',
            'Start Date',
            'Expiry Date',
            'Status',
            'Days Remaining',
        ];
    }

    // ── Row mapping ───────────────────────────────────────────────────────────

    public function map($client): array
    {
        return [
            $client->id,
            $client->name,
            $client->phone,
            $client->email ?? '—',
            $client->start_date->toDateString(),
            $client->expiry_date->toDateString(),
            $client->is_active ? 'Active' : 'Expired',
            $client->is_active ? $client->daysRemaining() : 0,
        ];
    }

    // ── Column widths ─────────────────────────────────────────────────────────

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // #
            'B' => 28,  // Name
            'C' => 18,  // Phone
            'D' => 30,  // Email
            'E' => 16,  // Start Date
            'F' => 16,  // Expiry Date
            'G' => 12,  // Status
            'H' => 18,  // Days Remaining
        ];
    }

    // ── Sheet styles ──────────────────────────────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row — dark indigo background, white bold text, centered
            1 => [
                'font' => [
                    'bold'  => true,
                    'size'  => 11,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    // ── Sheet / tab title ─────────────────────────────────────────────────────

    public function title(): string
    {
        return 'Clients';
    }
}
