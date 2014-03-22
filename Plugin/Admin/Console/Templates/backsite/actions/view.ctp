    public function view($id = null) {
        $this-><?php echo $currentModelName; ?>->id = $id;
        if (!$this-><?php echo $currentModelName; ?>->exists()) {
            throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
        }
        $<?php echo $singularName; ?> = $this-><?php echo $currentModelName; ?>->read(null, $id);
        $this->set('<?php echo $singularName; ?>', $<?php echo $singularName; ?>);
<?php
            if($modelObj->Behaviors->loaded('Tree')) {
                echo "\t\t\$this->{$currentModelName}->recursive = 0;\n";
                echo "\t\t\$this->paginate = array('conditions' => array('{$currentModelName}.parent_id' => \$id), 'limit' => 15);\n";
                echo "\t\t\$this->set('{$pluralName}', \$this->Paginator->paginate('{$currentModelName}'));\n";
                echo "\t\t\$this->set('{$pluralName}TableURL', array('controller' => '{$backendPluginNameUnderscored}_{$this->_controllerPath($pluralName)}', 'action' => 'index', '{$currentModelName}-parent_id' => \$id));\n";
            }

            foreach (array('hasMany') as $assoc):
                foreach ($modelObj->{$assoc} as $associationName => $relation):
                    if (!empty($associationName)):

                        $otherModelName = $this->_modelName($associationName);
                        $otherPluralName = $this->_pluralName($associationName);

                        echo "\t\t//related {$otherPluralName}\n";
                        echo "\t\t\$this->{$currentModelName}->{$otherModelName}->recursive = 0;\n";
                        switch($otherModelName)
                        {
                            default:
                                echo "\t\t\$this->paginate = array('conditions' => array('{$otherModelName}.{$relation['foreignKey']}' => \$id), 'limit' => 15);\n";
                                break;
                        }
                        echo "\t\t\$this->set('{$otherPluralName}', \$this->Paginator->paginate('{$otherModelName}'));\n";
                        echo "\t\t\$this->set('{$otherPluralName}TableURL', array('controller' => '{$backendPluginNameUnderscored}_{$this->_controllerPath($this->_pluralName($relation['className']))}', 'action' => 'index', '{$otherModelName}-{$relation['foreignKey']}' => \$id));\n";
                    endif;
                endforeach;
            endforeach;
?>
    }
    