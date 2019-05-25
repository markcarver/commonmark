<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Node;

use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testInsertBeforeElementWhichDoesNotHaveAPreviousOne()
    {
        $root = new SimpleNode();
        $root->appendChild($targetNode = new SimpleNode());

        $newNode = new SimpleNode();
        $targetNode->insertBefore($newNode);

        $this->assertNull($newNode->previous());
        $this->assertSame($targetNode, $newNode->next());
        $this->assertSame($newNode, $targetNode->previous());
    }

    public function testInsertBeforeElementWhichAlreadyHasPrevious()
    {
        $root = new SimpleNode();
        $root->appendChild($firstNode = new SimpleNode());
        $root->appendChild($targetNode = new SimpleNode());

        $newNode = new SimpleNode();
        $targetNode->insertBefore($newNode);

        $this->assertSame($firstNode, $newNode->previous());
        $this->assertSame($targetNode, $newNode->next());
        $this->assertSame($newNode, $targetNode->previous());
    }

    public function testPrependChildToChildlessParent()
    {
        $root = new SimpleNode();

        $root->prependChild($newNode = new SimpleNode());

        $this->assertSame($newNode, $root->firstChild());
        $this->assertSame($root, $newNode->parent());
        $this->assertNull($newNode->previous());
    }

    public function testPrependChildToParentWhichAlreadyHasChildren()
    {
        $root = new SimpleNode();
        $root->prependChild($existingChild = new SimpleNode());

        $this->assertSame($existingChild, $root->firstChild());

        $newNode = new SimpleNode();
        $root->prependChild($newNode);

        $this->assertCount(2, $root->children());
        $this->assertSame($newNode, $root->firstChild());
        $this->assertSame($root, $newNode->parent());
        $this->assertNull($newNode->previous());
        $this->assertSame($existingChild, $newNode->next());
        $this->assertSame($newNode, $existingChild->previous());
    }

    public function testDetachChildren()
    {
        $root = new SimpleNode();
        $root->appendChild($child1 = new SimpleNode());
        $root->appendChild($child2 = new SimpleNode());

        $root->detachChildren();

        $this->assertCount(0, $root->children());
        $this->assertNull($root->firstChild());
        $this->assertNull($root->lastChild());

        $this->assertNull($child1->parent());
        $this->assertNull($child2->parent());

        $this->assertSame($child2, $child1->next());
        $this->assertSame($child1, $child2->previous());
    }

    public function testReplaceChildren()
    {
        $root = new SimpleNode();
        $root->appendChild($oldChild = new SimpleNode());

        $newChildren = [
            $newChild1 = new SimpleNode(),
            $newChild2 = new SimpleNode(),
        ];

        $root->replaceChildren($newChildren);

        $this->assertCount(2, $root->children());
        $this->assertSame($newChild1, $root->firstChild());
        $this->assertSame($newChild2, $root->lastChild());
        $this->assertSame($root, $newChild1->parent());
        $this->assertSame($root, $newChild2->parent());
        $this->assertNull($oldChild->parent());
    }

    public function testInsertAfterWithParent()
    {
        $root = new SimpleNode();
        $root->appendChild($child1 = new SimpleNode());
        $root->appendChild($child2 = new SimpleNode());

        $otherRoot = new SimpleNode();
        $otherRoot->appendChild($child3 = new SimpleNode());
        $otherRoot->appendChild($child4 = new SimpleNode());

        $child1->insertAfter($child3);

        $this->assertCount(3, $root->children());
        $this->assertCount(1, $otherRoot->children());
        $this->assertSame($child1, $root->firstChild());
        $this->assertSame($child2, $root->lastChild());
        $this->assertSame($root, $child2->parent());
    }

    public function testInsertAfterWithoutParent()
    {
        $node1 = new SimpleNode();
        $node2 = new SimpleNode();

        $node1->insertAfter($node2);

        $this->assertSame($node2, $node1->next());
        $this->assertSame($node1, $node2->previous());
    }

    public function testInsertBeforeWithParent()
    {
        $root = new SimpleNode();
        $root->appendChild($child1 = new SimpleNode());
        $root->appendChild($child2 = new SimpleNode());
        $root->appendChild($child3 = new SimpleNode());

        $otherRoot = new SimpleNode();
        $otherRoot->appendChild($child4 = new SimpleNode());
        $otherRoot->appendChild($child5 = new SimpleNode());

        $child2->insertBefore($child4);

        $this->assertCount(4, $root->children());
        $this->assertCount(1, $otherRoot->children());
        $this->assertSame($child1, $root->children()[0]);
        $this->assertSame($child4, $root->children()[1]);
        $this->assertSame($child2, $root->children()[2]);
        $this->assertSame($child3, $root->children()[3]);
        $this->assertSame($root, $child4->parent());
    }

    public function testInsertBeforeWithoutParent()
    {
        $node1 = new SimpleNode();
        $node2 = new SimpleNode();

        $node1->insertBefore($node2);

        $this->assertSame($node1, $node2->next());
        $this->assertSame($node2, $node1->previous());
    }
}
