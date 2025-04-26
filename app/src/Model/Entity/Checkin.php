<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Checkin Entity
 *
 * @property string $id
 * @property int $customer_id
 * @property string $customer_name
 * @property \Cake\I18n\FrozenTime|null $created_at
 * @property string $type
 * @property string|null $item
 * @property string|null $details
 * @property \Cake\I18n\FrozenTime $check_in_at
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
        'customer_id' => true,
        'customer_name' => true,
        'created_at' => true,
        'type' => true,
        'item' => true,
        'details' => true,
        'check_in_at' => true,
    ];
}
