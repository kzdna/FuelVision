<?php

namespace App\Support;

/**
 * Centralised role name constants. The exact string values below must
 * match `roles.nama_role` in the database exactly (confirmed by the
 * client) — do not change these values without also updating the seeded
 * data.
 */
final class RoleName
{
    public const ADMIN_FINANCE = 'Admin Finance';
    public const VENDOR = 'Vendor';
    public const VIEW_ONLY = 'View Only';

    public static function all(): array
    {
        return [self::ADMIN_FINANCE, self::VENDOR, self::VIEW_ONLY];
    }
}
