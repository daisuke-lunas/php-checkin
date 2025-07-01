<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Checkins Model
 *
 * @method \App\Model\Entity\Checkin newEmptyEntity()
 * @method \App\Model\Entity\Checkin newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Checkin[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Checkin get($primaryKey, $options = [])
 * @method \App\Model\Entity\Checkin findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Checkin patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Checkin[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Checkin|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Checkin saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Checkin[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Checkin[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Checkin[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Checkin[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CheckinsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('checkins');
        $this->setDisplayField('customer_name');
        $this->setPrimaryKey('id');
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        // CheckinUserMonthlySummary とのアソシエーション（1ユーザー複数月のサマリ）
        $this->hasMany('CheckinUserMonthlySummary', [
            'className' => 'CheckinUserMonthlySummary',
            'foreignKey' => 'user_id',
            'bindingKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');
        $validator
            ->scalar('user_ext_id')
            ->maxLength('user_ext_id', 255)
            ->requirePresence('user_ext_id', 'create')
            ->notEmptyString('user_ext_id');
        $validator
            ->scalar('user_name')
            ->maxLength('user_name', 255)
            ->requirePresence('user_name', 'create')
            ->notEmptyString('user_name');
        $validator
            ->scalar('type')
            ->requirePresence('type', 'create')
            ->notEmptyString('type');
        $validator
            ->dateTime('check_in_at')
            ->requirePresence('check_in_at', 'create')
            ->notEmptyDateTime('check_in_at');
        $validator
            ->scalar('item')
            ->maxLength('item', 255)
            ->allowEmptyString('item');
        $validator
            ->scalar('details')
            ->allowEmptyString('details');
        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);
        return $rules;
    }
}
