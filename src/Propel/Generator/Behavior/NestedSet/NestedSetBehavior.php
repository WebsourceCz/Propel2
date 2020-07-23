<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace Propel\Generator\Behavior\NestedSet;

use Propel\Generator\Model\Behavior;

/**
 * Behavior to adds nested set tree structure columns and abilities
 *
 * @author François Zaninotto
 */
class NestedSetBehavior extends Behavior
{
    // default parameters value
    protected $parameters = [
        'left_column' => 'tree_left',
        'right_column' => 'tree_right',
        'level_column' => 'tree_level',
        'use_scope' => 'false',
        'scope_column' => 'tree_scope',
        'method_proxies' => 'false',
    ];

    protected $objectBuilderModifier;

    protected $queryBuilderModifier;

    /**
     * Add the left, right and scope to the current table
     *
     * @return void
     */
    public function modifyTable()
    {
        $table = $this->getTable();

        if (!$table->hasColumn($this->getParameter('left_column'))) {
            $table->addColumn([
                'name' => $this->getParameter('left_column'),
                'type' => 'INTEGER',
            ]);
        }

        if (!$table->hasColumn($this->getParameter('right_column'))) {
            $table->addColumn([
                'name' => $this->getParameter('right_column'),
                'type' => 'INTEGER',
            ]);
        }

        if (!$table->hasColumn($this->getParameter('level_column'))) {
            $table->addColumn([
                'name' => $this->getParameter('level_column'),
                'type' => 'INTEGER',
            ]);
        }

        if ($this->getParameter('use_scope') === 'true' && !$table->hasColumn($this->getParameter('scope_column'))) {
            $table->addColumn([
                'name' => $this->getParameter('scope_column'),
                'type' => 'INTEGER',
            ]);
        }
    }

    /**
     * @return $this|\Propel\Generator\Behavior\NestedSet\NestedSetBehaviorObjectBuilderModifier
     */
    public function getObjectBuilderModifier()
    {
        if ($this->objectBuilderModifier === null) {
            $this->objectBuilderModifier = new NestedSetBehaviorObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }

    /**
     * @return $this|\Propel\Generator\Behavior\NestedSet\NestedSetBehaviorQueryBuilderModifier
     */
    public function getQueryBuilderModifier()
    {
        if ($this->queryBuilderModifier === null) {
            $this->queryBuilderModifier = new NestedSetBehaviorQueryBuilderModifier($this);
        }

        return $this->queryBuilderModifier;
    }

    /**
     * @return bool
     */
    public function useScope()
    {
        return $this->getParameter('use_scope') === 'true';
    }

    /**
     * @param string $columnName
     *
     * @return string
     */
    public function getColumnConstant($columnName)
    {
        return $this->getColumn($columnName)->getName();
    }

    /**
     * @param string $columnName
     *
     * @return \Propel\Generator\Model\Column
     */
    public function getColumn($columnName)
    {
        return $this->getColumnForParameter($columnName);
    }
}
