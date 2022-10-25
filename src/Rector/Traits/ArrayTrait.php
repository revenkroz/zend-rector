<?php

namespace Revenkroz\ZendRector\Rector\Traits;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;

trait ArrayTrait
{
    protected function extractArrayItemByKey(?Node $node, string $key): ?ArrayItem
    {
        if (null === $node) {
            return null;
        }

        if (!$node instanceof Array_) {
            return null;
        }

        foreach ($node->items as $item) {
            if (!$item instanceof ArrayItem) {
                continue;
            }

            if (null === $item->key) {
                continue;
            }

            $itemKey = (string) $this->valueResolver->getValue($item->key);
            if ($key === $itemKey) {
                return $item;
            }
        }

        return null;
    }
}
