<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model;

use StefanFroemken\ExtKickstarter\Model\Node\Typo3\ExtensionNode;

class Graph
{
    /**
     * @var \SplObjectStorage|AbstractNode[]
     */
    private \SplObjectStorage $nodes;

    /**
     * @var \SplObjectStorage|Link[]
     */
    private \SplObjectStorage $links;

    public function __construct() {
        $this->nodes = new \SplObjectStorage();
        $this->links = new \SplObjectStorage();
    }

    /**
     * @return \SplObjectStorage|AbstractNode[]
     */
    public function getNodes(): \SplObjectStorage
    {
        return $this->nodes;
    }

    public function addNode(AbstractNode $node): void
    {
        $this->nodes->attach($node);
    }

    /**
     * @return \SplObjectStorage|Link[]
     */
    public function getLinks(): \SplObjectStorage
    {
        return $this->links;
    }

    public function addLink(Link $link): void
    {
        $this->links->attach($link);
    }

    public function getExtensionNode(): ?ExtensionNode
    {
        foreach ($this->getNodes() as $node) {
            if ($node instanceof ExtensionNode) {
                return $node;
            }
        }

        return null;
    }

    public function getLinkedOutputNodesByName(AbstractNode $node, string $name, string $targetType = ''): \SplObjectStorage
    {
        $nodes = new \SplObjectStorage();
        foreach ($node->getOutputs() as $outputNode) {
            if ($outputNode->getName() !== $name) {
                continue;
            }

            foreach ($outputNode->getLinks() as $linkId) {
                $targetNode = $this->getTargetNodeByLinkId($linkId);
                if ($targetNode instanceof AbstractNode) {
                    if ($targetType === '' || $targetType === $targetNode->getType()) {
                        $nodes->attach($targetNode);
                    }
                }
            }
        }

        return $nodes;
    }

    public function getTargetNodeByLinkId(int $linkId): ?AbstractNode
    {
        $link = $this->getLinkById($linkId);
        if (!$link instanceof Link) {
            return null;
        }

        return $this->getNodeById($link->getTargetNodeId());
    }

    public function getNodeById(int $id): ?AbstractNode
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

    public function getNodesByType(string $nodeType): \SplObjectStorage
    {
        $nodes = new \SplObjectStorage();

        foreach ($this->getNodes() as $node) {
            if ($node->getType() === $nodeType) {
                $nodes->attach($node);
            }
        }

        return $nodes;
    }
}
