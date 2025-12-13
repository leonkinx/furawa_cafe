<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesReportExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $startDate;
    protected $endDate;
    
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    
    public function collection()
    {
        // Get detailed transactions for the main table
        $transactions = Order::with(['orderItems.menu'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $data = collect();
        $no = 1;
        
        foreach ($transactions as $transaction) {
            $statusText = $this->getStatusText($transaction->status);
            
            foreach ($transaction->orderItems as $index => $item) {
                $itemSubtotal = $item->quantity * $item->price;
                
                $data->push([
                    ($index == 0) ? $no : '',
                    ($index == 0) ? $transaction->order_code : '',
                    ($index == 0) ? $transaction->created_at->format('d/m/Y H:i') : '',
                    ($index == 0) ? $transaction->customer_name : '',
                    ($index == 0) ? $transaction->table_id : '',
                    $item->menu->name,
                    $item->quantity,
                    $item->price,
                    $itemSubtotal,
                    ($index == 0) ? ($transaction->ppn_amount ?? 0) : '',
                    ($index == 0) ? ($transaction->service_charge ?? 0) : '',
                    ($index == 0) ? $transaction->total_amount : '',
                    ($index == 0) ? $statusText : '',
                    ($index == 0) ? 'TUNAI' : ''
                ]);
            }
            $no++;
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        return [
            'No',
            'Kode Pesanan',
            'Tanggal & Waktu',
            'Nama Customer',
            'Meja',
            'Menu',
            'Qty',
            'Harga Satuan',
            'Subtotal Item',
            'PPN',
            'Service Charge',
            'Total Pesanan',
            'Status',
            'Payment'
        ];
    }
    
    private function getStatusSummary()
    {
        return Order::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total_amount'))
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->groupBy('status')
            ->orderByRaw("FIELD(status, 'pending', 'paid', 'processing', 'completed', 'cancelled')")
            ->get();
    }
    
    private function getDetailedTransactions()
    {
        return Order::with(['orderItems.menu'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    private function getStatusText($status)
    {
        $statusTexts = [
            'pending' => 'BELUM BAYAR',
            'paid' => 'SUDAH BAYAR',
            'processing' => 'SEDANG DIPROSES',
            'completed' => 'SELESAI',
            'cancelled' => 'DIBATALKAN'
        ];
        
        return $statusTexts[$status] ?? strtoupper($status);
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Header row styling - Blue background with white text
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Data rows styling - Add borders to all cells
        if ($lastRow > 1) {
            $sheet->getStyle('A2:N' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            
            // Number formatting for currency columns (H, I, J, K, L)
            $sheet->getStyle('H2:H' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('I2:I' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('J2:J' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('K2:K' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('L2:L' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        return [];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Kode Pesanan
            'C' => 18,  // Tanggal & Waktu
            'D' => 15,  // Nama Customer
            'E' => 6,   // Meja
            'F' => 20,  // Menu
            'G' => 5,   // Qty
            'H' => 12,  // Harga Satuan
            'I' => 12,  // Subtotal Item
            'J' => 10,  // PPN
            'K' => 12,  // Service Charge
            'L' => 12,  // Total Pesanan
            'M' => 12,  // Status
            'N' => 8,   // Payment
        ];
    }
    
    public function title(): string
    {
        return 'Laporan Transaksi';
    }
}

