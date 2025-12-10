<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // ✅ TAMBAHKAN INI: Daftar status yang valid
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_FAILED = 'failed';
    
    // ✅ TAMBAHKAN INI: Mapping untuk validasi
    public static $validStatuses = [
        self::STATUS_PENDING,
        self::STATUS_PAID, 
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED
    ];
    
    public static $validPaymentStatuses = [
        self::PAYMENT_STATUS_PENDING,
        self::PAYMENT_STATUS_PAID,
        self::PAYMENT_STATUS_UNPAID,
        self::PAYMENT_STATUS_FAILED
    ];
    
    public static $statusLabels = [
        self::STATUS_PENDING => 'Menunggu Pembayaran',
        self::STATUS_PAID => 'Sudah Dibayar',
        self::STATUS_PROCESSING => 'Sedang Diproses',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan'
    ];
    
    public static $paymentStatusLabels = [
        self::PAYMENT_STATUS_PENDING => 'Menunggu Pembayaran',
        self::PAYMENT_STATUS_PAID => 'Lunas',
        self::PAYMENT_STATUS_UNPAID => 'Belum Bayar',
        self::PAYMENT_STATUS_FAILED => 'Gagal'
    ];

    protected $fillable = [
        'order_code',
        'customer_name',
        'table_id',
        'status',
        'total_amount',
        'subtotal',
        'ppn_amount',
        'service_charge',
        'payment_method',
        'payment_status',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ✅ TAMBAHKAN INI: Event boot untuk validasi
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($order) {
            // Validasi status sebelum save
            if (!in_array($order->status, self::$validStatuses)) {
                throw new \InvalidArgumentException(
                    "Status '{$order->status}' tidak valid. Status yang diperbolehkan: " . 
                    implode(', ', self::$validStatuses)
                );
            }
            
            // Validasi payment_status sebelum save
            if ($order->payment_status && !in_array($order->payment_status, self::$validPaymentStatuses)) {
                throw new \InvalidArgumentException(
                    "Payment status '{$order->payment_status}' tidak valid. Status yang diperbolehkan: " . 
                    implode(', ', self::$validPaymentStatuses)
                );
            }
        });
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PAID => 'bg-blue-100 text-blue-800',
            self::STATUS_PROCESSING => 'bg-purple-100 text-purple-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusText()
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getPaymentStatusText()
    {
        return self::$paymentStatusLabels[$this->payment_status] ?? $this->payment_status;
    }
    
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
    
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->translatedFormat('d M Y H:i');
    }
    
    public function getTableNumberAttribute()
    {
        return $this->table_id;
    }
    
    public function getOrderNumberAttribute()
    {
        return $this->order_code;
    }
    
    // ✅ TAMBAHKAN METHOD INI: Untuk update status dengan validasi
    public function updateStatusSafely($newStatus, $newPaymentStatus = null)
    {
        // Validasi status
        if (!in_array($newStatus, self::$validStatuses)) {
            throw new \InvalidArgumentException(
                "Status '{$newStatus}' tidak valid. Status yang diperbolehkan: " . 
                implode(', ', self::$validStatuses)
            );
        }
        
        // Validasi payment_status jika ada
        if ($newPaymentStatus && !in_array($newPaymentStatus, self::$validPaymentStatuses)) {
            throw new \InvalidArgumentException(
                "Payment status '{$newPaymentStatus}' tidak valid. Status yang diperbolehkan: " . 
                implode(', ', self::$validPaymentStatuses)
            );
        }
        
        // Update status
        $this->status = $newStatus;
        
        // Update payment_status jika ada
        if ($newPaymentStatus) {
            $this->payment_status = $newPaymentStatus;
        }
        
        // Logika otomatis
        if ($newStatus === self::STATUS_COMPLETED && $this->payment_status === self::PAYMENT_STATUS_UNPAID) {
            $this->payment_status = self::PAYMENT_STATUS_PAID;
        }
        
        return $this->save();
    }
}