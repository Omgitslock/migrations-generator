<?php

namespace Xethron\MigrationsGenerator\Syntax;

/**
 * Class AddToTable
 * @package Xethron\MigrationsGenerator\Syntax
 */
class AddToTable extends Table {

    /**
     * Return string for adding a column
     *
     * @param array $field
     * @return string
     */
    protected function getItem(array $field){
        if(array_key_exists('foreignKey', $field)){
            unset($field['foreignKey']);
            return $this->getForeignKeyItem($field);
        }
        
        return $this->getColumnItem($field);
    }

    protected function getColumnItem(array $field) {
        $property = $field['field'];
        // If the field is an array,
        // make it an array in the Migration
        if (is_array($property)) {
            $property = "['" . implode("','", $property) . "']";
        } else {
            $property = $property ? "'$property'" : null;
        }

        $type = $field['type'];

        $output = sprintf(
                "\$table->%s(%s)", $type, $property
        );

        // If we have args, then it needs
        // to be formatted a bit differently
        if (isset($field['args'])) {
            $output = sprintf(
                    "\$table->%s(%s, %s)", $type, $property, $field['args']
            );
        }
        if (isset($field['decorators'])) {
            $output .= $this->addDecorators($field['decorators']);
        }
        return $output . ';';
    }

    protected function getForeignKeyItem(array $foreignKey) {
        $value = $foreignKey['field'];
        if (!empty($foreignKey['name'])) {
            $value .= "', '" . $foreignKey['name'];
        }
        $output = sprintf(
                "\$table->foreign('%s')->references('%s')->on('%s')", $value, $foreignKey['references'], $foreignKey['on']
        );
        if ($foreignKey['onUpdate']) {
            $output .= sprintf("->onUpdate('%s')", $foreignKey['onUpdate']);
        }
        if ($foreignKey['onDelete']) {
            $output .= sprintf("->onDelete('%s')", $foreignKey['onDelete']);
        }
        if (isset($foreignKey['decorators'])) {
            $output .= $this->addDecorators($foreignKey['decorators']);
        }
        return $output . ';';
    }

}
