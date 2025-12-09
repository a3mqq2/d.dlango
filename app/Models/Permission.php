<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'group',
        'display_name_ar',
        'display_name_en',
    ];

    /**
     * Get users with this permission
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }

    /**
     * Get display name based on locale
     */
    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->display_name_ar : $this->display_name_en;
    }

    /**
     * Get all permissions grouped
     */
    public static function getGrouped()
    {
        return static::all()->groupBy('group');
    }

    /**
     * Get all available permissions with their definitions
     */
    public static function getAvailablePermissions(): array
    {
        return [
            'users' => [
                ['name' => 'users.view', 'ar' => 'عرض المستخدمين', 'en' => 'View Users'],
                ['name' => 'users.create', 'ar' => 'إضافة مستخدم', 'en' => 'Create User'],
                ['name' => 'users.edit', 'ar' => 'تعديل مستخدم', 'en' => 'Edit User'],
                ['name' => 'users.delete', 'ar' => 'حذف مستخدم', 'en' => 'Delete User'],
            ],
            'suppliers' => [
                ['name' => 'suppliers.view', 'ar' => 'عرض الموردين', 'en' => 'View Suppliers'],
                ['name' => 'suppliers.create', 'ar' => 'إضافة مورد', 'en' => 'Create Supplier'],
                ['name' => 'suppliers.edit', 'ar' => 'تعديل مورد', 'en' => 'Edit Supplier'],
                ['name' => 'suppliers.delete', 'ar' => 'حذف مورد', 'en' => 'Delete Supplier'],
                ['name' => 'suppliers.transactions', 'ar' => 'حركات الموردين', 'en' => 'Supplier Transactions'],
            ],
            'customers' => [
                ['name' => 'customers.view', 'ar' => 'عرض الزبائن', 'en' => 'View Customers'],
                ['name' => 'customers.create', 'ar' => 'إضافة زبون', 'en' => 'Create Customer'],
                ['name' => 'customers.edit', 'ar' => 'تعديل زبون', 'en' => 'Edit Customer'],
                ['name' => 'customers.delete', 'ar' => 'حذف زبون', 'en' => 'Delete Customer'],
                ['name' => 'customers.transactions', 'ar' => 'حركات الزبائن', 'en' => 'Customer Transactions'],
            ],
            'purchases' => [
                ['name' => 'purchases.view', 'ar' => 'عرض المشتريات', 'en' => 'View Purchases'],
                ['name' => 'purchases.create', 'ar' => 'إنشاء فاتورة شراء', 'en' => 'Create Purchase'],
                ['name' => 'purchases.edit', 'ar' => 'تعديل فاتورة شراء', 'en' => 'Edit Purchase'],
                ['name' => 'purchases.delete', 'ar' => 'حذف فاتورة شراء', 'en' => 'Delete Purchase'],
                ['name' => 'purchases.receive', 'ar' => 'استلام المشتريات', 'en' => 'Receive Purchases'],
            ],
            'sales' => [
                ['name' => 'sales.view', 'ar' => 'عرض المبيعات', 'en' => 'View Sales'],
                ['name' => 'sales.create', 'ar' => 'إنشاء عملية بيع', 'en' => 'Create Sale'],
                ['name' => 'sales.pos', 'ar' => 'استخدام نقطة البيع', 'en' => 'Use POS'],
            ],
            'returns' => [
                ['name' => 'returns.view', 'ar' => 'عرض المرتجعات', 'en' => 'View Returns'],
                ['name' => 'returns.create', 'ar' => 'إنشاء مرتجع', 'en' => 'Create Return'],
            ],
            'inventory' => [
                ['name' => 'inventory.view', 'ar' => 'عرض المخزون', 'en' => 'View Inventory'],
                ['name' => 'inventory.barcode', 'ar' => 'طباعة الباركود', 'en' => 'Print Barcode'],
            ],
            'finance' => [
                ['name' => 'finance.cashboxes', 'ar' => 'إدارة الخزائن', 'en' => 'Manage Cashboxes'],
                ['name' => 'finance.transactions', 'ar' => 'إدارة الحركات المالية', 'en' => 'Manage Transactions'],
                ['name' => 'finance.categories', 'ar' => 'إدارة تصنيفات الحركات', 'en' => 'Manage Categories'],
                ['name' => 'finance.statement', 'ar' => 'كشف الحساب', 'en' => 'Account Statement'],
            ],
            'coupons' => [
                ['name' => 'coupons.view', 'ar' => 'عرض الكوبونات', 'en' => 'View Coupons'],
                ['name' => 'coupons.create', 'ar' => 'إنشاء كوبون', 'en' => 'Create Coupon'],
                ['name' => 'coupons.edit', 'ar' => 'تعديل كوبون', 'en' => 'Edit Coupon'],
                ['name' => 'coupons.delete', 'ar' => 'حذف كوبون', 'en' => 'Delete Coupon'],
            ],
            'reports' => [
                ['name' => 'reports.view', 'ar' => 'عرض التقارير والإحصائيات', 'en' => 'View Reports & Statistics'],
            ],
        ];
    }

    /**
     * Seed all permissions
     */
    public static function seedPermissions(): void
    {
        $permissions = static::getAvailablePermissions();

        foreach ($permissions as $group => $groupPermissions) {
            foreach ($groupPermissions as $permission) {
                static::updateOrCreate(
                    ['name' => $permission['name']],
                    [
                        'group' => $group,
                        'display_name_ar' => $permission['ar'],
                        'display_name_en' => $permission['en'],
                    ]
                );
            }
        }
    }
}
