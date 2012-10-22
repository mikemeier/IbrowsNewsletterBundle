<?php

namespace Ibrows\Bundle\NewsletterBundle\Model\Block\Composition;

interface BlockCompositionInterface
{
    public function addBlock(BlockInterface $block);
    public function removeBlock(BlockInterface $block);
    public function getBlocks();
    public function render();
}