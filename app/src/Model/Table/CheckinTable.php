<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Checkin Model
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
class CheckinTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('checkin');
        $this->setDisplayField('customer_name');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('customer_id')
            ->requirePresence('customer_id', 'create')
            ->notEmptyString('customer_id');

        $validator
            ->scalar('customer_name')
            ->maxLength('customer_name', 255)
            ->requirePresence('customer_name', 'create')
            ->notEmptyString('customer_name');

        $validator
            ->dateTime('created_at')
            ->allowEmptyDateTime('created_at');

        $validator
            ->scalar('type')
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('item')
            ->maxLength('item', 255)
            ->allowEmptyString('item');

        $validator
            ->scalar('details')
            ->allowEmptyString('details');

        $validator
            ->dateTime('check_in_at')
            ->requirePresence('check_in_at', 'create')
            ->notEmptyDateTime('check_in_at');

        return $validator;
    }
}
