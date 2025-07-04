<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CheckinUserMonthlySummary Entity
 *
 * @property string $yyyymm
 * @property int $user_id
 * @property string $user_ext_id
 * @property string $user_name
 * @property string $type
 * @property int $total_count
 *
 * @property \App\Model\Entity\User $user
 */
class CheckinUserMonthlySummary extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'user_ext_id' => true,
        'user_name' => true,
        'total_count' => true,
        'user' => true,
    ];
}
