<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use \ArrayObject;
use Cake\Network\Session;
use Cake\ORM\TableRegistry;

class LogBehavior extends Behavior
{
    private $Session;
    private $primary_key;
    
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->Session = new Session();
        $this->primary_key = $config['primary_key'];
    }
    
        
    /**
     * Método que cria o log no banco de dados
     * 
     * @param int $user_id
     * @param string $event_name
     * @param array $original_data
     * @param array $new_data
     */
    public function log($user_id, $primary_id, $event_name, $original_data, $new_data)
    {
        $Log = TableRegistry::get("Log");
        $log = $Log->newEntity([
            'user_id'       => $user_id,
            'primary_key'   => $primary_id,
            'event'         => $event_name,
            'original_data' => json_encode($original_data),
            'new_data'      => json_encode($new_data)
        ]);
        $Log->save($log);
    }
    
    /**
     * Pega os atributos atualizados
     * 
     * @param type $entity
     * @return array
     */
    public function getChanged($entity)
    {
        $changed = [
            'original'  => [],
            'new'       => []
        ];
        
        $visible_properties = $entity->visibleProperties();
        foreach($visible_properties as $property) {
            if(!is_array($entity->getOriginal($property)) && $entity->getOriginal($property) != $entity->get($property)) {
                $changed['original'][$property] = $entity->getOriginal($property);
                $changed['new'][$property] = $entity->get($property);
            }
        }
        
        return $changed;
    }
    
    /**
     * Inicializa após concluir um insert ou update
     * 
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
    public function afterSaveCommit(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        
        $event_name = $entity->isNew()?"Novo":"Atualização";
        $user_id = $this->Session->read('Auth.User.user_id');
        $changed = $this->getChanged($entity);
        
        if(!empty($changed['new']))
            $this->log($user_id, $entity->get($this->primary_key), $event_name, $changed['original'], $changed['new']);
    }
    
}