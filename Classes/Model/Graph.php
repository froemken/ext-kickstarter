<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model;

class Graph
{
    public function __construct(
        private readonly \SplObjectStorage $nodes,
        private readonly \SplObjectStorage $links
    )
    {
    }

    public function getNodes(): \SplObjectStorage
    {
        return $this->nodes;
    }

    public function getLinks(): \SplObjectStorage
    {
        return $this->links;
    }

    public function getExtensionNode(): ?Node
    {
        foreach ($this->getNodes() as $node) {
            if ($node->getType() === 'TYPO3/Extension') {
                return $node;
            }
        }

        return null;
    }

    public function getLinkedOutputNodesByName(Node $node, string $name): \SplObjectStorage
    {
        $nodes = new \SplObjectStorage();
        foreach ($node->getOutputs() as $outputNode) {
            if ($outputNode->getName() !== $name) {
                continue;
            }

            foreach ($outputNode->getLinks() as $linkId) {
                $targetNode = $this->getTargetNodeByLinkId($linkId);
                if ($targetNode instanceof Node) {
                    $nodes->attach($targetNode);
                }
            }
        }

        return $nodes;
    }

    public function getTargetNodeByLinkId(int $linkId): ?Node
    {
        $link = $this->getLinkById($linkId);
        if (!$link instanceof Link) {
            return null;
        }

        return $this->getNodeById($link->getTargetNodeId());
    }

    public function getNodeById(int $id): ?Node
    {
        foreach ($this->getNodes() as $node) {
            if ($node->getId() === $id) {
                return $node;
            }
        }

        return null;
    }

    public function getLinkById(int $id): ?Link
    {
        foreach ($this->getLinks() as $link) {
            if ($link->getId() === $id) {
                return $link;
            }
        }

        return null;
    }
}