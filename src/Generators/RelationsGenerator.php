<?php

namespace Pepijnolivier\EloquentModelGenerator\Generators;

use Nette\PhpGenerator\ClassLike;
use Nette\PhpGenerator\PhpNamespace;
use Pepijnolivier\EloquentModelGenerator\Relations\TableRelations;
use Pepijnolivier\EloquentModelGenerator\Relations\Types\BelongsToManyRelation;
use Pepijnolivier\EloquentModelGenerator\Relations\Types\BelongsToRelation;
use Pepijnolivier\EloquentModelGenerator\Relations\Types\HasManyRelation;
use Pepijnolivier\EloquentModelGenerator\Relations\Types\HasOneRelation;

class RelationsGenerator
{
    public function __construct(
        protected PhpNamespace &$namespace,
        protected ClassLike &$classLike,
        protected TableRelations $relations, // we should really have a Relations class here
        protected string $modelNamespaceString
    ) {
    }

    public function handle()
    {
        $this->addBelongsToMethods();
        $this->addHasOneMethods();

        $this->addHasManyMethods();
        $this->addBelongsToManyMethods();

        // @todo
        // morph methods
        // through methods
    }

    private function addHasOneMethods()
    {
        $hasOneRules = $this->relations->getHasOneRelations();

        /** @var HasOneRelation $relation */
        foreach($hasOneRules as $relation) {

            $body = sprintf(
                'return $this->hasOne(%s::class, \'%s\', \'%s\');',
                $relation->getEntityClass(),
                $relation->getForeignKey(),
                $relation->getLocalKey()
            );

            $this->classLike
                ->addMethod($relation->getFunctionName())
                ->addBody($body);

            $this->namespace->addUse($this->modelNamespaceString . "\\" . $relation->getEntityClass());
        }
    }

    private function addBelongsToManyMethods() {

        $belongsToManyRules = $this->relations->getBelongsToManyRelations();

        /** @var BelongsToManyRelation $relation */
        foreach($belongsToManyRules as $relation) {

            $body = sprintf(
                'return $this->belongsToMany(%s::class, \'%s\', \'%s\', \'%s\');',
                $relation->getEntityClass(),
                $relation->getTable(),
                $relation->getForeignPivotKey(),
                $relation->getRelatedPivotKey(),
            );

            $this->classLike
                ->addMethod($relation->getFunctionName())
                ->addBody($body);

            $this->namespace->addUse($this->modelNamespaceString . "\\" . $relation->getEntityClass());
        }
    }

    private function addHasManyMethods() {

        $hasManyRules = $this->relations->getHasManyRelations();

        /** @var HasManyRelation $relation */
        foreach($hasManyRules as $relation) {

            $body = sprintf(
                'return $this->hasMany(%s::class, \'%s\', \'%s\');',
                $relation->getEntityClass(),
                $relation->getForeignKey(),
                $relation->getLocalKey()
            );

            $this->classLike
                ->addMethod($relation->getFunctionName())
                ->addBody($body);

            $this->namespace->addUse($this->modelNamespaceString . "\\" . $relation->getEntityClass());
        }
    }

    private function addBelongsToMethods()
    {
        $belongsToRules = $this->relations->getBelongsToRelations();

        /** @var BelongsToRelation $relation */
        foreach($belongsToRules as $relation) {

            $body = sprintf(
                'return $this->belongsTo(%s::class, \'%s\', \'%s\');',
                $relation->getEntityClass(),
                $relation->getForeignKey(),
                $relation->getOwnerKey()
            );

            $this->classLike
                ->addMethod($relation->getFunctionName())
                ->addBody($body);

            $this->namespace->addUse($this->modelNamespaceString . "\\" . $relation->getEntityClass());
        }
    }
}
