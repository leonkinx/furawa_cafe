<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SimpleReportExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
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
        $transactions = Order::with(['orderItems.menu'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $data = collect();
        $no = 1;
        
        foreach ($transactions as $transaction) {
            $statusText = $this->getStatusText($transaction->status);
            
            // Check if transaction has order items
            if ($transaction->orderItems->count() > 0) {
                foreach ($transaction->orderItems as $index => $item) {
                    $itemSubtotal = $item->quantity * $item->price;
                    
                    $data->push([
                        ($index == 0) ? $no : '',
                        ($index == 0) ? $transaction->order_code : '',
                        ($index == 0) ? $transaction->created_at->format('d/m/Y H:i') : '',
                        ($index == 0) ? $transaction->customer_name : '',
                        ($index == 0) ? 'Meja ' . $transaction->table_id : '',
                        $item->menu ? $item->menu->name : 'Menu Dihapus',
                        $item->quantity,
                        'Rp ' . number_format($item->price, 0, ',', '.'),
                        'Rp ' . number_format($itemSubtotal, 0, ',', '.'),
                        ($index == 0) ? 'Rp ' . number_format($transaction->ppn_amount ?? 0, 0, ',', '.') : '',
                        ($index == 0) ? 'Rp ' . number_format($transaction->service_charge ?? 0, 0, ',', '.') : '',
                        ($index == 0) ? 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') : '',
                        ($index == 0) ? $statusText : '',
                        ($index == 0) ? 'TUNAI' : ''
                    ]);
                }
            } else {
                // Handle orders without items
                $data->push([
                    $no,
                    $transaction->order_code,
                    $transaction->created_at->format('d/m/Y H:i'),
                    $transaction->customer_name,
                    'Meja ' . $transaction->table_id,
                    'TIDAK ADA ITEM',
                    0,
                    'Rp 0',
                    'Rp 0',
                    'Rp ' . number_format($transaction->ppn_amount ?? 0, 0, ',', '.'),
                    'Rp ' . number_format($transaction->service_charge ?? 0, 0, ',', '.'),
                    'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
                    $statusText,
                    'TUNAI'
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
        
        // Header row - Blue background with white text
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '366092']
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
        
        // Data rows - Add borders and center alignment
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
            
            // Alternate row colors for better readability
            for ($i = 2; $i <= $lastRow; $i++) {
                if ($i % 2 == 0) {
                    $sheet->getStyle('A' . $i . ':N' . $i)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8F9FA']
                        ]
                    ]);
                }
            }
        }
        
        return [];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // Kode Pesanan
            'C' => 20,  // Tanggal & Waktu
            'D' => 18,  // Nama Customer
            'E' => 10,  // Meja
            'F' => 25,  // Menu
            'G' => 6,   // Qty
            'H' => 18,  // Harga Satuan
            'I' => 18,  // Subtotal Item
            'J' => 15,  // PPN
            'K' => 18,  // Service Charge
            'L' => 18,  // Total Pesanan
            'M' => 18,  // Status
            'N' => 12,  // Payment
        ];
    }
    
    public function title(): string
    {
        return 'Laporan Transaksi';
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                // Calculate totals
                $totals = $this->calculateTotals();
                
                // Add empty row for separation
                $summaryStartRow = $lastRow + 2;
                
                // Add summary header
                $sheet->setCellValue('A' . $summaryStartRow, 'RINGKASAN PENDAPATAN');
                $sheet->mergeCells('A' . $summaryStartRow . ':D' . $summaryStartRow);
                
                // Create table header for summary
                $summaryHeaderRow = $summaryStartRow + 2;
                $sheet->setCellValue('A' . $summaryHeaderRow, 'Keterangan');
                $sheet->setCellValue('B' . $summaryHeaderRow, 'Jumlah');
                $sheet->setCellValue('C' . $summaryHeaderRow, 'Nilai (Rp)');
                $sheet->setCellValue('D' . $summaryHeaderRow, 'Persentase');
                
                // Summary data rows
                $summaryRow = $summaryHeaderRow + 1;
                
                // Period info
                $sheet->setCellValue('A' . $summaryRow, 'Periode Laporan');
                $sheet->setCellValue('B' . $summaryRow, $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y'));
                $sheet->setCellValue('C' . $summaryRow, '-');
                $sheet->setCellValue('D' . $summaryRow, '-');
                $summaryRow++;
                
                // Total transactions
                $sheet->setCellValue('A' . $summaryRow, 'Total Transaksi');
                $sheet->setCellValue('B' . $summaryRow, $totals['total_transactions'] . ' transaksi');
                $sheet->setCellValue('C' . $summaryRow, '-');
                $sheet->setCellValue('D' . $summaryRow, '100%');
                $summaryRow++;
                
                // Total items sold
                $sheet->setCellValue('A' . $summaryRow, 'Total Item Terjual');
                $sheet->setCellValue('B' . $summaryRow, $totals['total_items'] . ' item');
                $sheet->setCellValue('C' . $summaryRow, '-');
                $sheet->setCellValue('D' . $summaryRow, '-');
                $summaryRow++;
                
                // Separator row
                $sheet->setCellValue('A' . $summaryRow, '--- BREAKDOWN PENDAPATAN ---');
                $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
                $summaryRow++;
                
                // Subtotal (before tax and service charge)
                $subtotalPercentage = $totals['grand_total'] > 0 ? round(($totals['subtotal'] / $totals['grand_total']) * 100, 1) : 0;
                $sheet->setCellValue('A' . $summaryRow, 'Subtotal');
                $sheet->setCellValue('B' . $summaryRow, '-');
                $sheet->setCellValue('C' . $summaryRow, 'Rp ' . number_format($totals['subtotal'], 0, ',', '.'));
                $sheet->setCellValue('D' . $summaryRow, $subtotalPercentage . '%');
                $summaryRow++;
                
                // Total PPN
                $ppnPercentage = $totals['grand_total'] > 0 ? round(($totals['total_ppn'] / $totals['grand_total']) * 100, 1) : 0;
                $sheet->setCellValue('A' . $summaryRow, 'Total PPN');
                $sheet->setCellValue('B' . $summaryRow, '-');
                $sheet->setCellValue('C' . $summaryRow, 'Rp ' . number_format($totals['total_ppn'], 0, ',', '.'));
                $sheet->setCellValue('D' . $summaryRow, $ppnPercentage . '%');
                $summaryRow++;
                
                // Total Service Charge
                $servicePercentage = $totals['grand_total'] > 0 ? round(($totals['total_service_charge'] / $totals['grand_total']) * 100, 1) : 0;
                $sheet->setCellValue('A' . $summaryRow, 'Total Service Charge');
                $sheet->setCellValue('B' . $summaryRow, '-');
                $sheet->setCellValue('C' . $summaryRow, 'Rp ' . number_format($totals['total_service_charge'], 0, ',', '.'));
                $sheet->setCellValue('D' . $summaryRow, $servicePercentage . '%');
                $summaryRow++;
                
                // Grand Total (highlighted)
                $sheet->setCellValue('A' . $summaryRow, 'TOTAL KESELURUHAN PENDAPATAN');
                $sheet->setCellValue('B' . $summaryRow, $totals['total_transactions'] . ' transaksi');
                $sheet->setCellValue('C' . $summaryRow, 'Rp ' . number_format($totals['grand_total'], 0, ',', '.'));
                $sheet->setCellValue('D' . $summaryRow, '100%');
                $summaryRow++;
                
                // Add note about excluded cancelled orders
                $sheet->setCellValue('A' . $summaryRow, '(Tidak termasuk pesanan dibatalkan)');
                $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
                
                // Style the summary section header
                $sheet->getStyle('A' . $summaryStartRow . ':D' . $summaryStartRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '366092']
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
                
                // Style summary table header
                $sheet->getStyle('A' . $summaryHeaderRow . ':D' . $summaryHeaderRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '5A6C7D']
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
                
                // Style summary data rows
                $summaryDataStart = $summaryHeaderRow + 1;
                $summaryDataEnd = $summaryRow - 1;
                
                $sheet->getStyle('A' . $summaryDataStart . ':D' . $summaryDataEnd)->applyFromArray([
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
                
                // Style separator row
                $separatorRow = $summaryDataStart + 3;
                $sheet->getStyle('A' . $separatorRow . ':D' . $separatorRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'italic' => true
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E9ECEF']
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
                
                // Style grand total row (highlighted)
                $grandTotalRow = $summaryRow - 1;
                $sheet->getStyle('A' . $grandTotalRow . ':D' . $grandTotalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '28A745'] // Green background
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['rgb' => '000000']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Style note row
                $sheet->getStyle('A' . $summaryRow . ':D' . $summaryRow)->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['rgb' => '6C757D']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Auto-size columns for better visibility (especially for summary section)
                foreach (range('A', 'N') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(false);
                }
                
                // Set specific widths for summary columns to prevent truncation
                $sheet->getColumnDimension('A')->setWidth(30); // Keterangan column
                $sheet->getColumnDimension('B')->setWidth(20); // Jumlah column  
                $sheet->getColumnDimension('C')->setWidth(20); // Nilai column
                $sheet->getColumnDimension('D')->setWidth(15); // Persentase column
            }
        ];
    }
    
    private function calculateTotals()
    {
        $transactions = Order::whereBetween('created_at', [$this->startDate, $this->endDate])->get();
        
        // Exclude cancelled transactions from revenue calculations
        $validTransactions = $transactions->whereNotIn('status', ['cancelled']);
        
        $totalTransactions = $validTransactions->count();
        $grandTotal = $validTransactions->sum('total_amount');
        $totalPpn = $validTransactions->sum('ppn_amount');
        $totalServiceCharge = $validTransactions->sum('service_charge');
        $subtotal = $grandTotal - $totalPpn - $totalServiceCharge;
        
        // Calculate total items (only from valid transactions)
        $totalItems = OrderItem::whereHas('order', function($query) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->whereNotIn('status', ['cancelled']);
        })->sum('quantity');
        
        return [
            'total_transactions' => $totalTransactions,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'total_ppn' => $totalPpn,
            'total_service_charge' => $totalServiceCharge,
            'grand_total' => $grandTotal
        ];
    }
}