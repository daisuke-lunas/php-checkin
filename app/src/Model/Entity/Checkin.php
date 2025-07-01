<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Checkin Entity
 *
 * @property string $id
 * @property int $user_id
 * @property string $user_ext_id
 * @property string $user_name
 * @property string $type
 * @property \Cake\I18n\FrozenTime $check_in_at
 * @property string|null $item
 * @property string|null $details
 */
class Checkin extends Entity
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
        'user_id' => true,
        'user_ext_id' => true,
        'user_name' => true,
        'type' => true,
        'check_in_at' => true,
        'item' => true,
        'details' => true,
        'checkin_user_monthly_summary' => true,
    ];
}
