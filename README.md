Behavior para framework Cakephp 3.x utilizado para salvar logs no sistema.
É utilizado o evento *afterSaveCommit* para salvar o log.

# Utilização

É necessário a utilização do component Auth.
Para utilizar o Behavior:
Copiar a classe LogBehavior para `src/Model/Behavior`
Chamar na classe Table do Model que deseja efetuar log da seguinte forma:

```php
$this->addBehavior('Log',['primary_key'=>'primary_id']);
```

*Substituir o primary_id pela nome da chave primaria da tabela*

