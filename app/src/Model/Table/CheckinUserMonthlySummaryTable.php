<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CheckinUserMonthlySummary Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\CheckinUserMonthlySummary newEmptyEntity()
 * @method \App\Model\Entity\CheckinUserMonthlySummary newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary get($primaryKey, $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CheckinUserMonthlySummary[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CheckinUserMonthlySummaryTable extends Table
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

        $this->setTable('checkin_user_monthly_summary');
        $this->setDisplayField('user_ext_id');
        $this->setPrimaryKey(['yyyymm', 'user_id', 'type']);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
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
            ->integer('total_count')
            ->requirePresence('total_count', 'create')
            ->notEmptyString('total_count');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
