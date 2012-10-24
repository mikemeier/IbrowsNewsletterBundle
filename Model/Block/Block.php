<?php

namespace Ibrows\Bundle\NewsletterBundle\Model\Block;

use Ibrows\Bundle\NewsletterBundle\Model\Newsletter\NewsletterInterface;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

abstract class Block implements BlockInterface
{
    protected $id;
    
    protected $name;
    protected $content;
    protected $position;
    
    protected $providerName;
    protected $providerOptions = array();
    
    protected $newsletter;
    
    protected $parentBlock;
    protected $blocks;
    
    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param type $name
     * @return Block
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * @param integer $position
     * @return Block
     */
    public function setPosition($position)
    {
        $this->position = (int)$position;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * @param string $content
     * @return Block
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }
    
    /**
     * @param string $providerName
     * @return Block
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getProviderOptions()
    {
        return $this->providerOptions;
    }
    
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getProviderOption($key, $default = null)
    {
        if(!array_key_exists($key, $this->providerOptions)){
            return $default;
        }
        return $this->providerOptions[$key];
    }
    
    /**
     * @param string $key
     * @param mixed $value
     * @return Block
     */
    public function setProviderOption($key, $value)
    {
        $this->providerOptions[$key] = $value;
        return $this;
    }
    
    /**
     * @param array $options
     * @return Block
     */
    public function setProviderOptions(array $options)
    {
        $this->providerOptions = $options;
        return $this;
    }
    
    /**
     * @return NewsletterInterface
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }
    
    /**
     * @param NewsletterInterface $newsletter
     * @return Block
     */
    public function setNewsletter(NewsletterInterface $newsletter = null)
    {
        $this->newsletter = $newsletter;
        return $this;
    }
    
    /**
     * @return Block
     */
    public function addBlocks(array $blocks)
    {
        foreach($blocks as $block){
            $this->addBlock($block);
        }
    }
    
    /**
     * @param BlockInterface $block
     * @return Block
     */
    public function addBlock(BlockInterface $block)
    {
        $block->setParentBlock($this);
        $this->blocks->add($block);
        return $this;
    }
    
    /**
     * @param BlockInterface $block
     * @return Block
     */
    public function removeBlock(BlockInterface $block)
    {
        $block->setParentBlock(null);
        $this->blocks->remove($block);
        return $this;
    }
    
    /**
     * @return Collection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
    
    /**
     * @return Block
     */
    public function getParentBlock()
    {
        return $this->parentBlock;
    }
    
    /**
     * @param BlockInterface $block
     * @return Block
     */
    public function setParentBlock(BlockInterface $block = null)
    {
        $this->parentBlock = $block;
        return $this;
    }
}