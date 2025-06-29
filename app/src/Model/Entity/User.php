<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $ext_type
 * @property string $ext_id
 * @property string $username
 * @property string $display_name
 * @property string $user_type
 *
 * @property \App\Model\Entity\Checkin[] $checkin
 */
class User extends Entity
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
        'ext_type' => true,
        'ext_id' => true,
        'username' => true,
        'display_name' => true,
        'user_type' => true,
        'checkin' => true,
    ];
}
